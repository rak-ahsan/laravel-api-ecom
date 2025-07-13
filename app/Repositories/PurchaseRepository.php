<?php

namespace App\Repositories;

use Exception;
use App\Models\Product;
use App\Classes\Helper;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class PurchaseRepository
{
    public function __construct(protected Purchase $model){}

    public function index($request)
    {
        try {
            $paginateSize = Helper::checkPaginateSize($request);
            $searchKey    = $request->input("search_key", null);
            $paidStatus   = $request->input("paid_status", null);

            $purchases = $this->model->with("supplier")
            ->when($searchKey, fn($query) => $query->where("purchase_code", "like", "%$searchKey%"))
            ->when($paidStatus, fn($query) => $query->where("paid_status", $paidStatus))
            ->orderBy("created_at", "desc")->paginate($paginateSize);

            return $purchases;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function store($request)
    {
        $totalQty      = 0;
        $totalBuyPrice = 0;
        try {
            DB::beginTransaction();

            $purchase = new $this->model();

            $purchase->supplier_id   = $request->supplier_id;
            $purchase->purchase_code = strtoupper(Helper::generateRandomString(10));
            $purchase->quantity      = 0;
            $purchase->cost          = $request->cost;
            $purchase->paid_amount   = $request->paid_amount;
            $purchase->paid_status   = $request->paid_status;
            $purchase->save();

            $payload = [];
            foreach ($request->items as $item) {
                $itemBuyPrice    = 0;
                $productId       = $item["product_id"];
                $attributeValue1 = $item["attribute_value_id_1"];
                $attributeValue2 = $item["attribute_value_id_2"];
                $attributeValue3 = $item["attribute_value_id_3"];
                $quantity        = $item["quantity"];
                $buyPrice        = $item["buy_price"];

                $itemBuyPrice   = $buyPrice * $quantity;
                $totalBuyPrice += $itemBuyPrice;
                $totalQty      += $quantity;

                $product = Product::with(['variations'])->select("id", "current_stock")->find($productId);
                if ($product) {
                    if (count($product->variations) > 0) {
                        // Update product variation current stock
                        $productVariation = ProductVariation::where('product_id', $productId)
                        ->where('attribute_value_id_1', $attributeValue1)
                        ->where('attribute_value_id_2', $attributeValue2)
                        ->where('attribute_value_id_3', $attributeValue3)
                        ->first();

                        if ($productVariation) {
                            $productVariation->current_stock += + $quantity;
                            $productVariation->save();
                        }else {
                            throw new CustomException("Product variation not found");
                        }
                    } else {
                        // Update product current stock
                        $product->current_stock += $quantity;
                        $product->save();
                    }
                } else {
                    throw new CustomException("Product not found");
                }

                $payload[] = [
                    "purchase_id"          => $purchase->id,
                    "product_id"           => $productId,
                    "attribute_value_id_1" => $attributeValue1,
                    "attribute_value_id_2" => $attributeValue2,
                    "attribute_value_id_3" => $attributeValue3,
                    "buy_price"            => $buyPrice,
                    "quantity"             => $quantity,
                    "total"                => $itemBuyPrice
                ];
            }

            PurchaseDetail::insert($payload);

            $purchase->quantity   = $totalQty;
            $purchase->buy_price  = $totalBuyPrice;
            $purchase->due_amount = $totalBuyPrice - $request->paid_amount;
            $purchase->save();

            DB::commit();

            return $purchase;
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $purchase = $this->model->with(
            [
                "supplier",
                "createdBy:id,username,phone_number",
                "updatedBy:id,username,phone_number",
                "purchaseDetails",
                "purchaseDetails.product:id,name",
                "purchaseDetails.attributeValue1:id,value",
                "purchaseDetails.attributeValue2:id,value",
                "purchaseDetails.attributeValue3:id,value"
            ])->find($id);

            if (!$purchase) {
                throw new CustomException("Purchase not found");
            }

            return $purchase;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function update($request, $id)
    {
        $totalQty      = 0;
        $totalBuyPrice = 0;

        try {
            DB::beginTransaction();

            $purchase = $this->model->find($id);

            $purchase->supplier_id = $request->supplier_id;
            $purchase->cost        = $request->cost;
            $purchase->paid_amount = $request->paid_amount;
            $purchase->paid_status = $request->paid_status;
            $purchase->save();

            $payload = [];
            foreach ($request->items as $item) {
                $itemBuyPrice    = 0;
                $productId       = $item["product_id"];
                $attributeValue1 = $item["attribute_value_id_1"];
                $attributeValue2 = $item["attribute_value_id_2"];
                $attributeValue3 = $item["attribute_value_id_3"];
                $quantity        = $item["quantity"];
                $buyPrice        = $item["buy_price"];

                $itemBuyPrice   = $buyPrice * $quantity;
                $totalBuyPrice += $itemBuyPrice;
                $totalQty      += $quantity;

                $product = Product::with(['variations'])->select("id", "current_stock")->find($productId);
                if ($product) {
                    if (count($product->variations) > 0) {
                        // Update product variation current stock
                        $productVariation = ProductVariation::where('product_id', $productId)
                        ->where('attribute_value_id_1', $attributeValue1)
                        ->where('attribute_value_id_2', $attributeValue2)
                        ->where('attribute_value_id_3', $attributeValue3)
                        ->first();

                        if ($productVariation) {
                            $productVariation->current_stock += $quantity;
                            $productVariation->save();
                        } else {
                            throw new CustomException("Product variation not found");
                        }
                    } else {
                        // Update product current stock
                        $product->current_stock += + $quantity;
                        $product->save();
                    }
                } else {
                    throw new CustomException("Product not found");
                }

                $payload[] = [
                    "purchase_id"          => $purchase->id,
                    "product_id"           => $productId,
                    "attribute_value_id_1" => $attributeValue1,
                    "attribute_value_id_2" => $attributeValue2,
                    "attribute_value_id_3" => $attributeValue3,
                    "buy_price"            => $buyPrice,
                    "quantity"             => $quantity,
                    "total"                => $itemBuyPrice
                ];
            }

            $purchase->purchaseDetails()->delete();
            PurchaseDetail::insert($payload);

            $purchase->quantity   = $totalQty;
            $purchase->buy_price  = $totalBuyPrice;
            $purchase->due_amount = $totalBuyPrice - $request->paid_amount;
            $purchase->save();

            DB::commit();

            return $purchase;
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
