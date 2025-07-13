<?php

namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\Order;
use App\Classes\Helper;
use App\Models\Product;
use App\Models\Campaign;
use App\Enums\StatusEnum;
use App\Classes\SSLGateway;
use App\Models\OrderDetail;
use App\Models\RawMaterial;
use App\Models\FreeDelivery;
use App\Enums\PaidStatusEnum;
use App\Models\DeliveryGateway;
use App\Models\OrderRawMaterial;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use App\Enums\FreeDeliveryTypeEnum;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Auth;

class OrderRepository
{
    public function __construct(protected Order $model){}

    public function index($request)
    {
        $searchKey       = $request->input('search_key', null);
        $currentStatusId = $request->input('current_status_id', null);
        $paidStatus      = $request->input('paid_status', null);
        $orderFrom       = $request->input('order_from', null);
        $paginateSize    = Helper::checkPaginateSize($request);

        try {
            $orders = $this->model->with([
                'currentStatus', 'paymentGateway:id,name', 'preparedBy:id,username', 'updatedBy:id,username'
            ]);

            $orders = $orders->when($currentStatusId, fn($query) => $query->where("current_status_id", $currentStatusId))
            ->when($paidStatus, fn ($query) => $query->where("paid_status", $paidStatus))
            ->when($orderFrom, fn ($query) => $query->where("order_from", $orderFrom))
            ->when($searchKey, function($query) use ($searchKey) {
                $query->where("phone_number", "like", "%$searchKey%")
                ->orWhere("customer_name", "like", "%$searchKey%")
                ->orWhere("id", "like", "%$searchKey%");
            })
            ->orderBy("created_at", "desc")
            ->paginate($paginateSize);

            return $orders;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $order = new $this->model();

            $order->payment_gateway_id  = $request->payment_gateway_id;
            $order->delivery_gateway_id = $request->delivery_gateway_id;
            $order->current_status_id   = $request->current_status_id;
            $order->coupon_id           = $request->coupon_id;
            $order->paid_status         = $request->paid_status;
            $order->delivery_charge     = $request->delivery_charge ?? 0;
            $order->special_discount    = $request->special_discount ?? 0;
            $order->advance_payment     = $request->advance_payment ?? 0;
            $order->address_details     = $request->address_details;
            $order->district            = $request->district;
            $order->customer_name       = $request->customer_name;
            $order->phone_number        = $request->phone_number;
            $order->order_from          = $request->order_from;
            $order->note                = $request->order_note;
            $order->save();

            $itemDetails = [];
            foreach ($request->items as $item) {
                $productId         = $item["product_id"];
                $attributeValueId1 = $item["attribute_value_id_1"];
                $attributeValueId2 = $item["attribute_value_id_2"];
                $attributeValueId3 = $item["attribute_value_id_3"];
                $quantity          = $item["quantity"];

                $product = Product::select("id", "buy_price", "mrp", "offer_price", "discount", "sell_price", "current_stock")
                ->with("variations")
                ->where("status", "active")
                ->find($productId);

                // Check product exist
                if (!$product) {
                    throw new CustomException("Product not fount");
                }

                // Check product have variation
                if (count($product->variations) > 0) {
                    $productVariation = ProductVariation::where("product_id", $productId)
                    ->where("attribute_value_id_1", $attributeValueId1)
                    ->where("attribute_value_id_2", $attributeValueId2)
                    ->where("attribute_value_id_3", $attributeValueId3)
                    ->first();

                    // Check product current stock
                    if (!$productVariation) {
                        throw new CustomException("Variation $product not found");
                    }

                    // Check is stock maintain
                    if (Helper::getSettingValue("is_stock_maintain")) {
                        $attributeValue1 = @$productVariation->attributeValue1->value ?? null;
                        $attributeValue2 = @$productVariation->attributeValue2->value ?? null;
                        $attributeValue3 = @$productVariation->attributeValue3->value ?? null;

                        // Check product current stock
                        if ($productVariation->current_stock < $quantity) {
                            throw new CustomException("$product->name $attributeValue1 $attributeValue2 $attributeValue3 is out of stock");
                        }

                        // Update variation product current stock
                        $productVariation->current_stock -= $quantity;
                        $productVariation->save();

                        // Check alert quantity
                        if ($productVariation->current_stock <= $product->alert_qty) {
                            info("$product->name $attributeValue1 $attributeValue2 $attributeValue3 quantity is $productVariation->current_stock");
                        }
                    }

                    // Get variation product information
                    $buyPrice  = $productVariation->buy_price;
                    $mrp       = $productVariation->mrp;
                    $discount  = $productVariation->discount;
                    $sellPrice = $productVariation->sell_price;
                } else {
                    // Check is stock maintain
                    if (Helper::getSettingValue("is_stock_maintain")) {
                        // Check product current stock
                        if ($product->current_stock < $quantity) {
                            throw new CustomException("$product is out of stock");
                        }

                        // Update product current stock
                        $product->current_stock -= $quantity;
                        $product->save();

                        // Check alert quantity
                        if ($product->current_stock <= $product->alert_qty) {
                            info("The $product->name quantity is $product->current_stock");
                        }
                    }

                    // Get product information
                    $buyPrice  = $product->buy_price;
                    $mrp       = $product->mrp;
                    $discount  = $product->discount;
                    $sellPrice = $product->sell_price;
                }

                // Prepare payload for order details
                $itemDetails[] = [
                    'order_id'             => $order->id,
                    'product_id'           => $product->id,
                    'attribute_value_id_1' => $attributeValueId1,
                    'attribute_value_id_2' => $attributeValueId2,
                    'attribute_value_id_3' => $attributeValueId3,
                    'quantity'             => $quantity,
                    'buy_price'            => $buyPrice,
                    'mrp'                  => $mrp,
                    'sell_price'           => $sellPrice,
                    'discount'             => $discount,
                    'created_at'           => now()
                ];
            }

            // insert order details
            OrderDetail::insert($itemDetails);

            // Update buy_price, mrp, sell_price, discount, net_value, payable and coupon_value
            $order->updateOrderValue($order);

            // attach order status
            $order->statuses()->attach($request->current_status_id);

            // Start add raw materials
            if ($request->raw_materials && count($request->raw_materials) > 0) {
                $rawMaterials    = [];
                $rawMaterialCost = 0;
                foreach($request->raw_materials as $material) {
                    $rawMaterialId = $material["raw_material_id"];
                    $quantity      = $material["quantity"];

                    $rawMaterial = RawMaterial::find($rawMaterialId);
                    if ($rawMaterial) {
                        // calculate total cost
                        $itemTotal = $rawMaterial->unit_cost * $quantity;
                        $rawMaterialCost += $itemTotal;
                        $rawMaterials[] = [
                            "order_id"        => $order->id,
                            "raw_material_id" => $rawMaterialId,
                            "quantity"        => $quantity,
                            "unit_cost"       => $rawMaterial->unit_cost,
                            "total"           => $itemTotal,
                        ];
                    }

                    $rawMaterial->quantity -= $quantity;
                    $rawMaterial->save();
                }

                OrderRawMaterial::insert($rawMaterials);

                $order->raw_material_cost = $rawMaterialCost;
                $order->save();
            }
            // End add raw materials

            DB::commit();

            return $order;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $order = $this->model->with(
                [
                    "createdBy:id,username",
                    "updatedBy:id,username",
                    "preparedBy:id,username",
                    "deliveryGateway:id,name",
                    "paymentGateway:id,name",
                    "coupon:id,name,code",
                    "currentStatus:id,name,bg_color,text_color",
                    "statuses:id,name,bg_color,text_color",
                    "details",
                    "details.attributeValue1:id,value",
                    "details.attributeValue2:id,value",
                    "details.attributeValue3:id,value",
                    "details.product:id,name,img_path",
                    "details.product.variations",
                    "rawMaterials:id,name",
                ]
            )->find($id);

            if (!$order) {
                throw new CustomException("Order not found");
            }

            return $order;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $order = $this->model->find($id);

            if (!$order) {
                throw new CustomException("Order not found");
            }

            // Check is stock maintain
            if (Helper::getSettingValue("is_stock_maintain")) {
                // Add previous quantity with product current stock
                foreach ($order->details as $item) {
                    $previousProduct = Product::with(["variations"])->find($item->product_id);

                    if ($previousProduct) {
                        if (count($previousProduct->variations) > 0) {
                            $previousVariation = ProductVariation::where("product_id", $item->product_id)
                            ->where("attribute_value_id_1", $item->attribute_value_id_1)
                            ->where("attribute_value_id_2", $item->attribute_value_id_2)
                            ->where("attribute_value_id_3", $item->attribute_value_id_3)
                            ->first();

                            if (!$previousVariation) {
                                throw new CustomException("Variation product not found");
                            }

                            $previousVariation->current_stock += $item->quantity;
                            $previousVariation->save();
                        } else {
                            $previousProduct->current_stock += $item->quantity;
                            $previousProduct->save();
                        }
                    }
                }
            }


            $order = Order::find($id);

            $order->payment_gateway_id  = $request->payment_gateway_id;
            $order->delivery_gateway_id = $request->delivery_gateway_id;
            $order->current_status_id   = $request->current_status_id;
            $order->coupon_id           = $request->coupon_id;
            $order->paid_status         = $request->paid_status;
            $order->delivery_charge     = $request->delivery_charge ?? 0;
            $order->special_discount    = $request->special_discount ?? 0;
            $order->advance_payment     = $request->advance_payment ?? 0;
            $order->address_details     = $request->address_details;
            $order->district            = $request->district;
            $order->customer_name       = $request->customer_name;
            $order->phone_number        = $request->phone_number;
            $order->order_from          = $request->order_from;
            $order->note                = $request->order_note;
            $order->save();

            $itemDetails = [];

            // Check order is cancel or return
            if (!($request->current_status_id == 5 || $request->current_status_id == 9)) {
                foreach ($request->items as $item) {
                    $productId         = $item["product_id"];
                    $attributeValueId1 = @$item["attribute_value_id_1"];
                    $attributeValueId2 = @$item["attribute_value_id_2"];
                    $attributeValueId3 = @$item["attribute_value_id_3"];
                    $quantity          = $item["quantity"];
                    $buyPrice          = $item["buy_price"];
                    $mrp               = $item["mrp"];
                    $sellPrice         = $item["sell_price"];
                    $discount          = $item["discount"];

                    $product = Product::select("id", "buy_price", "mrp", "offer_price", "discount", "sell_price", "current_stock")
                    ->with("variations")
                    ->where("status", "active")
                    ->find($productId);

                    // Check product exist
                    if (!$product) {
                        throw new CustomException("Product not fount");
                    }

                    // Check product have variation
                    if (count($product->variations) > 0) {
                        $variation = ProductVariation::where("product_id", $productId)
                        ->where("attribute_value_id_1", $attributeValueId1)
                        ->where("attribute_value_id_2", $attributeValueId2)
                        ->where("attribute_value_id_3", $attributeValueId3)
                        ->first();

                        if (!$product) {
                            throw new CustomException("Variation product not fount");
                        }

                        // Check is stock maintain
                        if (Helper::getSettingValue("is_stock_maintain")) {
                            $attributeValue1 = @$variation->attributeValue1->value ?? null;
                            $attributeValue2 = @$variation->attributeValue2->value ?? null;
                            $attributeValue3 = @$variation->attributeValue3->value ?? null;

                            // Check variation stock
                            if ($variation && $variation->current_stock < $quantity) {
                                throw new CustomException("Invalid quantity of $product->name $attributeValue1 $attributeValue2 $attributeValue3");
                            }

                            // Update variation product stock
                            $variation->current_stock -= $quantity;
                            $variation->save();

                            // Check alert quantity
                            if ($variation->current_stock <= $product->alert_qty) {
                                info("The $product->name $attributeValue1 $attributeValue2, $attributeValue3 quantity is $variation->current_stock");
                            }
                        }

                    } else {
                        // Check is stock maintain
                        if (Helper::getSettingValue("is_stock_maintain")) {
                            // Check product stock
                            if ($product && $product->current_stock < $quantity) {
                                throw new CustomException("Invalid quantity of $product");
                            }

                            // Update product current stock
                            $product->current_stock -= $quantity;
                            $product->save();

                            // Check alert quantity
                            if ($product->current_stock <= $product->alert_qty) {
                                info("The $product->name quantity is $product->current_stock");
                            }
                        }
                    }

                    // Prepare order details payload
                    $itemDetails[] = [
                        'order_id'             => $order->id,
                        'product_id'           => $product->id,
                        'attribute_value_id_1' => $attributeValueId1,
                        'attribute_value_id_2' => $attributeValueId2,
                        'attribute_value_id_3' => $attributeValueId3,
                        'quantity'             => $quantity,
                        'buy_price'            => $buyPrice,
                        'mrp'                  => $mrp,
                        'sell_price'           => $sellPrice,
                        'discount'             => $discount,
                        'created_at'           => now()
                    ];
                }

                // Delete previous order details
                $order->details()->delete();

                // Insert order details
                OrderDetail::insert($itemDetails);

                // Update buy_price, mrp, sell_price, discount, net_value, payable and coupon_value
                $order->updateOrderValue($order);
            }

            // Attach order status
            $order->statuses()->syncWithoutDetaching([$request->current_status_id]);

            // Start add raw materials

            $order->rawMaterials()->detach();
            if ($request->raw_materials && count($request->raw_materials) > 0) {
                $rawMaterials    = [];
                $rawMaterialCost = 0;
                foreach($request->raw_materials as $material) {
                    $rawMaterialId = $material["raw_material_id"];
                    $quantity      = $material["quantity"];

                    $rawMaterial = RawMaterial::find($rawMaterialId);
                    if ($rawMaterial) {
                        // calculate total cost
                        $itemTotal = $rawMaterial->unit_cost * $quantity;
                        $rawMaterialCost += $itemTotal;
                        $rawMaterials[] = [
                            "order_id"        => $order->id,
                            "raw_material_id" => $rawMaterialId,
                            "quantity"        => $quantity,
                            "unit_cost"       => $rawMaterial->unit_cost,
                            "total"           => $itemTotal,
                        ];
                    }

                    $rawMaterial->quantity -= $quantity;
                    $rawMaterial->save();
                }

                OrderRawMaterial::insert($rawMaterials);

                $order->raw_material_cost = $rawMaterialCost;
                $order->save();
            }
            // End add raw materials

            DB::commit();

            return $order;
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::find($id);
            if (!$order) {
                throw new CustomException("Order not found");
            }

            return $order->delete();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function customerOrder($request)
    {
        try {
            DB::beginTransaction();

            $deliveryCharge  = 0;
            $specialDiscount = 0;

            $deliveryGateway = DeliveryGateway::find($request->delivery_gateway_id);
            if ($deliveryGateway) {
                $deliveryCharge = $deliveryGateway->delivery_fee;
            }

            // Check bonus point is applicable
            if (Auth::check() && $request->is_bonus_point_applied) {
                $user            = Auth::user();
                $bonusPointValue = Helper::getSettingValue("bonus_point_value");

                if ($bonusPointValue && $user->bonus_points && ($user->bonus_points >= $bonusPointValue)) {
                    $specialDiscount = round($user->bonus_points / $bonusPointValue);

                    // Update bonus point
                    $user->bonus_points -= $specialDiscount * $bonusPointValue;
                    $user->save();
                }
            }

            $order = new $this->model();

            $order->payment_gateway_id  = $request->payment_gateway_id;
            $order->delivery_gateway_id = $request->delivery_gateway_id;
            $order->coupon_id           = $request->coupon_id;
            $order->advance_payment     = $request->advance_payment ?? 0;
            $order->special_discount    = $specialDiscount;
            $order->current_status_id   = 1;
            $order->paid_status         = PaidStatusEnum::UNPAID->value;
            $order->address_details     = $request->address_details;
            $order->district            = $request->district;
            $order->customer_name       = $request->customer_name;
            $order->phone_number        = $request->phone_number;
            $order->order_from          = "website";
            $order->note                = $request->order_note;
            $order->save();

            $orderDetails = [];
            foreach ($request->items as $item) {
                $productId         = $item["product_id"];
                $attributeValueId1 = $item["attribute_value_id_1"] ?? null;
                $attributeValueId2 = $item["attribute_value_id_2"] ?? null;
                $attributeValueId3 = $item["attribute_value_id_3"] ?? null;
                $quantity          = $item["quantity"] ?? 1;
                $campaignId        = $item["campaign_id"] ?? null;

                $product = Product::select("id", "name", "buy_price", "mrp", "offer_price", "discount", "sell_price", "current_stock", "alert_qty")
                ->with(["variations"])
                ->where("status", "active")
                ->find($productId);

                // Check product exist
                if (!$product) {
                    throw new CustomException("Product not found");
                }

                // Check product minimum quantity
                if ( $quantity < $product->minimum_qty) {
                    throw new CustomException("$product->name minimum order quantity $product->minimum_qty");
                }

                // Check product have variation
                if (count($product->variations) > 0) {
                    $variation = ProductVariation::where("product_id", $productId)
                    ->where("attribute_value_id_1", $attributeValueId1)
                    ->where("attribute_value_id_2", $attributeValueId2)
                    ->where("attribute_value_id_3", $attributeValueId3)
                    ->first();

                    if (!$variation) {
                        throw new CustomException("Variation product not found");
                    }

                    $buyPrice   = $variation->buy_price;
                    $mrp        = $variation->mrp;
                    $offerPrice = $variation->offer_price;
                    $discount   = $variation->discount;
                    $sellPrice  = $variation->sell_price;

                    // Check is stock maintain
                    if (Helper::getSettingValue("is_stock_maintain")) {
                        $attributeValue1  = @$variation->attributeValue1->value ?? null;
                        $attributeValue2  = @$variation->attributeValue2->value ?? null;
                        $attributeValue3  = @$variation->attributeValue3->value ?? null;

                        // Check variation stock
                        if ($variation->current_stock < $quantity) {
                            throw new CustomException("Invalid quantity of $product->name $attributeValue1 $attributeValue2, $attributeValue3");
                        }

                        // Update variation stock
                        $variation->current_stock -= $quantity;
                        $variation->save();

                        // Check alert quantity
                        if ($variation->current_stock <= $product->alert_qty) {
                            info("The $product->name $attributeValue1 $attributeValue2, $attributeValue3 quantity is $variation->current_stock");
                        }
                    }
                } else {
                    // Check is stock maintain
                    if (Helper::getSettingValue("is_stock_maintain")) {
                        // Check product stock
                        if ($product->current_stock < $quantity) {
                            throw new CustomException("Invalid quantity of $product->name");
                        }

                        // Update product stock
                        $product->current_stock -= $quantity;
                        $product->save();

                        // Check alert quantity
                        if ($product->current_stock <= $product->alert_qty) {
                            info("$product->name quantity is $product->current_stock");
                        }

                        $buyPrice   = $product->buy_price;
                        $mrp        = $product->mrp;
                        $offerPrice = $product->offer_price;
                        $discount   = $product->discount;
                        $sellPrice  = $product->sell_price;
                    }
                }

                $campaign = null;
                // Check campaign
                if ($campaignId) {
                    $campaign = Campaign::with([
                        'campaignProducts' => function ($query) use ($productId) {
                            $query->where('product_id', $productId);
                        },
                        'campaignProducts.campaignProductVariations' => function ($query) use ($attributeValueId1, $attributeValueId2, $attributeValueId3) {
                            $query->where('attribute_value_id_1', $attributeValueId1)
                                ->where('attribute_value_id_2', $attributeValueId2)
                                ->where('attribute_value_id_3', $attributeValueId3);
                        }
                    ])->find($campaignId);

                    if (!$campaign) {
                        throw new CustomException("Invalid campaign");
                    }
                }

                // Check campaign product have variation
                if ($campaign && count($campaign->campaignProducts) > 0) {
                    $campaignProduct = $campaign->campaignProducts[0];
                    if (count($campaignProduct->campaignProductVariations) > 0) {
                        $campaignVariationPrice = $campaignProduct->campaignProductVariations[0];

                        $buyPrice   = $campaignVariationPrice->buy_price;
                        $mrp        = $campaignVariationPrice->mrp;
                        $offerPrice = $campaignVariationPrice->offer_price;
                        $discount   = $campaignVariationPrice->discount;
                        $sellPrice  = $offerPrice;
                    } else {
                        $buyPrice   = $campaignProduct->buy_price;
                        $mrp        = $campaignProduct->mrp;
                        $offerPrice = $campaignProduct->offer_price;
                        $discount   = $campaignProduct->discount_value;
                        $sellPrice  = $offerPrice;
                    }
                }

                // prepare payload for order details
                $orderDetails[] = [
                    "order_id"             => $order->id,
                    "product_id"           => $product->id,
                    'attribute_value_id_1' => $attributeValueId1,
                    'attribute_value_id_2' => $attributeValueId2,
                    'attribute_value_id_3' => $attributeValueId3,
                    "quantity"             => $quantity,
                    "buy_price"            => $buyPrice,
                    "mrp"                  => $mrp,
                    "sell_price"           => $sellPrice,
                    "discount"             => $discount,
                    "created_at"           => now()
                ];

                // Check free shipping exist
                if ($item["free_shipping"]) {
                    $deliveryCharge = 0;
                }
            }

            $order->delivery_charge = $deliveryCharge;
            $order->save();

            // insert order details data
            OrderDetail::insert($orderDetails);

            // Check free delivery applicable
            $freeDelivery = FreeDelivery::where("status", StatusEnum::ACTIVE->value)->first();
            if ($freeDelivery) {
                // For free delivery type quantity
                if ($freeDelivery->type === FreeDeliveryTypeEnum::QUANTITY->value) {
                    $totalOrderQty = $order->getTotalQuantity();
                    if ($totalOrderQty >= $freeDelivery->quantity) {
                        $order->delivery_charge = 0;
                        $order->save();
                    }
                }

                // For free delivery type price
                if ($freeDelivery->type === FreeDeliveryTypeEnum::PRICE->value) {
                    $netOrderPrice = $order->getNetOrderPrice();
                    if ($netOrderPrice >= $freeDelivery->price) {
                        $order->delivery_charge = 0;
                        $order->save();
                    }
                }
            }

            // attach order status id
            $order->statuses()->attach(1);

            // Update buy_price, mrp, sell_price, discount, net_value, payable and coupon_value
            $order->updateOrderValue($order);

            // Add bonus points with customer account
            if (Auth::check() && Helper::getSettingValue("is_bonus_point_add")) {
                $netOrderPrice = $order->getNetOrderPrice();

                $user = Auth::user();
                $user->bonus_points += $netOrderPrice;
                $user->save();
            }

            DB::commit();

            return $order;
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    public function updateStatus($request)
    {
        try {
            DB::beginTransaction();

            foreach ($request->order_ids as $id) {
                $order = $this->model->find($id);
                if (!$order) {
                    throw new CustomException("Order $id not found ");
                }

                $order->current_status_id = $request->current_status_id;
                $order->save();

                // attach order status id
                $order->statuses()->syncWithoutDetaching([$request->current_status_id]);

                if ($request->current_status_id == 5 || $request->current_status_id == 9) {
                    // Add previous quantity with product current stock
                    foreach ($order->details as $item) {
                        $product = Product::with(["variations"])->find($item->product_id);

                        if ($product) {
                            if (count($product->variations) > 0) {
                                $variation = ProductVariation::where("product_id", $item->product_id)
                                ->where("attribute_value_id_1", $item->attribute_value_id_1)
                                ->where("attribute_value_id_2", $item->attribute_value_id_2)
                                ->where("attribute_value_id_3", $item->attribute_value_id_3)
                                ->first();

                                if (!$variation) {
                                    throw new CustomException("Variation product not found ");
                                }

                                $variation->current_stock += $item->quantity;
                                $variation->save();
                            } else {
                                $product->current_stock += $item->quantity;
                                $product->save();
                            }
                        }
                    }
                }
            }

            DB::commit();

            return true;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function updatePaidStatus($request)
    {
        try {
            DB::beginTransaction();

            foreach ($request->order_ids as $id) {
                $order = $this->model->find($id);
                if (!$order) {
                    throw new CustomException("Order $id not found ");
                }

                $order->paid_status = $request->paid_status;
                $order->save();
            }

            DB::commit();

            return true;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function addAdditionCost($request)
    {
        try {
            DB::beginTransaction();

            $averageCost = 0;
            $endDate     = $request->end_date ?? $request->start_date;
            $startDate   = Carbon::parse($request->start_date)->startOfDay();
            $endDate     = Carbon::parse($endDate)->endOfDay();

            $orders = $this->model->whereBetween('created_at', [$startDate, $endDate])->get();

            if (count($orders) > 0) {
                $averageCost = $request->cost / $orders->count();

                foreach ($orders as $order) {
                    $order->additional_cost = $averageCost;
                    $order->save();
                }
            } else {
                throw new CustomException("Order not found");
            }

            DB::commit();

            return true;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function addRawMaterial($request)
    {
        try {
            DB::beginTransaction();

            $orderId = $request->input("order_id");

            $order = $this->model->find($orderId);

            if (!$order) {
                throw new CustomException("Order not found");
            }

            $rawMaterials = [];
            $totalCost    = 0;
            foreach($request->raw_materials as $material) {
                $rawMaterialId = $material["raw_material_id"];
                $quantity      = $material["quantity"];

                $rawMaterial = RawMaterial::find($rawMaterialId);
                if ($rawMaterial) {
                    // calculate total cost
                    $itemTotal = $rawMaterial->unit_cost * $quantity;
                    $totalCost += $itemTotal;
                    $rawMaterials[] = [
                        "order_id"        => $orderId,
                        "raw_material_id" => $rawMaterialId,
                        "quantity"        => $quantity,
                        "unit_cost"       => $rawMaterial->unit_cost,
                        "total"           => $itemTotal,
                    ];
                }

                $rawMaterial->quantity -= $quantity;
                $rawMaterial->save();
            }

            OrderRawMaterial::insert($rawMaterials);

            $order->raw_material_cost = $totalCost;
            $order->save();

            DB::commit();

            return $totalCost;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function multipleInvoice($request)
    {
        $startDate = $request->input('start_date', null);
        $endDate   = $request->input('end_date', null);
        $orderIds  = $request->input('order_ids', []);

        try {
            $orders = $this->model->with([
                "createdBy:id,username",
                "updatedBy:id,username",
                "preparedBy:id,username",
                "deliveryGateway:id,name",
                "paymentGateway:id,name",
                "coupon:id,name,code",
                "currentStatus:id,name,bg_color,text_color",
                "statuses:id,name,bg_color,text_color",
                "details",
                "details.attributeValue1:id,value",
                "details.attributeValue2:id,value",
                "details.attributeValue3:id,value",
                "details.product:id,name",
                "details.product.variations",
            ]);

            if ($startDate && $endDate) {
                $startDate = Carbon::parse($startDate)->startOfDay();
                $endDate   = Carbon::parse($endDate)->endOfDay();
                $orders    = $orders->whereBetween('created_at', [$startDate, $endDate]);
            }

            if ($orderIds) {
                $orders = $orders->whereIn('id', $orderIds);
            }

            if (($startDate && $endDate) || $orderIds) {
                $orders = $orders->get();
            } else {
                $orders = [];
            }

            return $orders;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function orderHistory($request, $id)
    {
        $limit = $request->input("limit", 10);

        try {
            return $this->model->find($id)->audits()->with("user:id,username")->take($limit)->get();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    function orderTeamList($request)
    {
        $searchKey    = $request->input('search_key', null);
        $paginateSize = Helper::checkPaginateSize($request);

        try {

            $orders = $this->model->with(['currentStatus:id,name']);

            $orders = $orders->whereNull('prepared_by')
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where('phone_number', $searchKey)
                ->orWhere('customer_name', $searchKey);
            });

            return $orders->orderBy("created_at", "desc")->paginate($paginateSize);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    function preparedBy($request)
    {
        $orderIds = $request->input('order_ids', []);

        try {
            DB::beginTransaction();

            $orders = $this->model->whereIn('id', $orderIds)
            ->whereNull('prepared_by')
            ->update([
                'prepared_by' => Auth::id(),
                'prepared_at' => now(),
            ]);

            DB::commit();

            return $orders;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    function preparedByRestore($request)
    {
        $orderIds = $request->input('order_ids', []);

        try {
            DB::beginTransaction();

            $orders = $this->model->whereIn('id', $orderIds)
            ->update([
                'prepared_by' => null,
                'prepared_at' => null,
            ]);

            DB::commit();

            return $orders;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function preparedByList($request)
    {
        $searchKey    = $request->input('search_key', null);
        $paginateSize = Helper::checkPaginateSize($request);

        try {

            $orders = $this->model->with(['currentStatus', 'preparedBy:id,username']);

            $orders = $orders->where('prepared_by', Auth::id())
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where('phone_number', $searchKey)
                ->orWhere('customer_name', $searchKey);
            });

            return $orders->orderBy("prepared_at", "desc")->paginate($paginateSize);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function orderLockedStatus($id)
    {
        try {
            $order = $this->model->select("id", "locked_by_id")->with(["lockedBy:id,username"])->find($id);
            if (!$order) {
                throw new CustomException("Order not found");
            }

            if ($order->locked_by_id == Auth::id()) {
                $lockStatus = false;
            } elseif (is_null($order->locked_by_id)) {
                $lockStatus = false;
            } else {
                $lockStatus = true;
            }

            $response = [
                'order_id'     => $order->id,
                'locked_by_id' => $order->locked_by_id,
                'locked_by'    => @$order->lockedBy->username,
                'lockStatus'   => $lockStatus,
            ];

            return $response;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function orderLocked($id)
    {
        try {
            $order = $this->model->select("id", "locked_by_id")->find($id);
            if (!$order) {
                throw new CustomException("Order not found");
            }

            if ($order->locked_by_id) {
                $order->locked_by_id = null;
            } else {
                $order->locked_by_id = Auth::id();
            }

            $order->save();

            $order->load("lockedBy:id,username");

            $response = [
                'order_id'     => $order->id,
                'locked_by_id' => $order->locked_by_id,
                'locked_by'    => @$order->lockedBy->username,
            ];

            return $response;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function trashList($request)
    {
        $searchKey       = $request->input('search_key', null);
        $currentStatusId = $request->input('current_status_id', null);
        $paidStatus      = $request->input('paid_status', null);
        $orderFrom       = $request->input('order_from', null);
        $paginateSize    = Helper::checkPaginateSize($request);

        try {
            $orders = $this->model->onlyTrashed()->with([
                'currentStatus',
                'paymentGateway:id,name',
                'preparedBy:id,username',
                'updatedBy:id,username'
            ]);

            $orders = $orders->when($currentStatusId, fn($query) => $query->where("current_status_id", $currentStatusId))
            ->when($paidStatus, fn($query) => $query->where("paid_status", $paidStatus))
            ->when($orderFrom, fn($query) => $query->where("order_from", $orderFrom))
            ->when($searchKey, function ($query) use ($searchKey) {
                $query->where("phone_number", "like", "%$searchKey%")
                    ->orWhere("customer_name", "like", "%$searchKey%")
                    ->orWhere("id", "like", "%$searchKey%");
            })
            ->orderBy("created_at", "desc")
            ->paginate($paginateSize);

            return $orders;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $order = $this->model->withTrashed()->find($id);
            if (!$order) {
                throw new CustomException("Order not found", 404);
            }

            $order->restore();

            return $order;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $order = $this->model->withTrashed()->find($id);
            if (!$order) {
                throw new CustomException("Order not found");
            }

            $order->details()->delete();
            $order->statuses()->delete();
            $order->rawMaterials()->delete();

            return $order->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    // Execute SSL payment gateway
    public function executeSSLPayment($shippingAddress, $customerName, $phoneNumber, $amount, $trxId, $numOfItems)
    {
        $productCats      = "ProductCategory";
        $productName      = "Productname";
        $productProfile   = "general";
        $customerPhone    = $phoneNumber;
        $customerEmail    = "test@gmail.com";
        $customerAddress  = $shippingAddress;
        $customerCity     = "City";
        $customerPostcode = "0000";
        $customerCountry  = "Bangladesh";
        $multiCardName    = "";

        $sslObj = new SSLGateway();
        $paymentRes = $sslObj->requestSession(
            $amount, $trxId, $productCats, $productName, $productProfile,
            $customerName, $customerEmail, $customerAddress, $customerCity, $customerPostcode, $customerCountry, $customerPhone,
            $numOfItems, $multiCardName
        );

        $paymentResStatus = $paymentRes['status'];

        if ($paymentResStatus === "SUCCESS") {
            $redirectGatewayURL = $paymentRes['redirectGatewayURL'];
            return $redirectGatewayURL;
        } else {
            return false;
        }
    }
}
