<?php

namespace App\Repositories;

use Exception;
use App\Models\Product;
use App\Classes\Helper;
use App\Models\Campaign;
use App\Models\CampaignProduct;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Models\CampaignProductVariation;

class CampaignRepository
{
    public function __construct(protected Campaign $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $paginateSize = $request->input("paginate_size", null);
        $now          = $request->input("now", null);
        $status       = $request->input("status", null);

        try {
            $campaign = $this->model->with([
                "campaignProducts",
                "campaignProducts.product:id,name,img_path",
                "campaignProducts.campaignProductVariations",
                "campaignProducts.campaignProductVariations.attributeValue1:id,value",
                "campaignProducts.campaignProductVariations.attributeValue2:id,value",
                "campaignProducts.campaignProductVariations.attributeValue3:id,value"
            ])
            ->when($now, fn ($query) => $query->where("start_date", "<=", $now)->where("end_date", ">=", $now))
            ->when($status, fn ($query) => $query->where("status", $status))
            ->orderBy("created_at", "desc")
            ->paginate($paginateSize);

            return $campaign;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $campaign = new $this->model();

            $campaign->title      = $request->title;
            $campaign->start_date = $request->start_date;
            $campaign->end_date   = $request->end_date;
            $campaign->status     = $request->status ?? "inactive";
            $campaign->save();

            foreach ($request->items as $item) {
                $product = Product::select("id", "buy_price", "mrp", "sell_price", "discount" )
                ->with("variations")
                ->find($item["product_id"]);

                if (!$product) {
                    throw new CustomException("Product not found");
                }

                // For discount type fixed
                if ($item["discount_type"] === "fixed") {
                    // For non variation product
                    $discountValue = 0;
                    $offerPrice    = 0;
                    if ($product->mrp > 0) {
                        $discountValue = $item["discount"];
                        $offerPrice    = $product->mrp - $item["discount"];
                    }

                    $campaignProduct = $campaign->campaignProducts()->create(
                        [
                            "campaign_id"    => $campaign->id,
                            "product_id"     => $item["product_id"],
                            "buy_price"      => $product->buy_price,
                            "mrp"            => $product->mrp,
                            "discount"       => $item["discount"],
                            "discount_type"  => $item["discount_type"],
                            "discount_value" => $discountValue,
                            "offer_price"    => $offerPrice
                        ]
                    );

                    // For variation product
                    if (count($product->variations) > 0) {
                        foreach ($product->variations as $variation) {
                            $variationDiscount     = $item["discount"];
                            $variationOfferPercent = ($variationDiscount * 100) / $variation->mrp;

                            $campaignProduct->campaignProductVariations()->create(
                                [
                                    "campaign_product_id"  => $campaignProduct->id,
                                    "attribute_value_id_1" => $variation->attribute_value_id_1,
                                    "attribute_value_id_2" => $variation->attribute_value_id_2,
                                    "attribute_value_id_3" => $variation->attribute_value_id_3,
                                    "is_default"           => $variation->is_default,
                                    "buy_price"            => $variation->buy_price,
                                    "mrp"                  => $variation->mrp,
                                    "offer_price"          => $variation->mrp - $variationDiscount,
                                    "discount"             => $variationDiscount,
                                    "offer_percent"        => $variationOfferPercent,
                                    "img_path"             => $variation->img_path
                                ]
                            );
                        }
                    }
                } else {
                    // For discount type percentage
                    // For non variation product
                    $discountValue = 0;
                    $offerPrice    = 0;
                    if ($product->mrp > 0) {
                        $discountValue = ($item["discount"] * $product->mrp) / 100;
                        $offerPrice    = $product->mrp - $discountValue;
                    }

                    $campaignProduct = $campaign->campaignProducts()->create(
                        [
                            "campaign_id"    => $campaign->id,
                            "product_id"     => $item["product_id"],
                            "buy_price"      => $product->buy_price,
                            "mrp"            => $product->mrp,
                            "discount"       => $item["discount"],
                            "discount_type"  => $item["discount_type"],
                            "discount_value" => $discountValue,
                            "offer_price"    => $offerPrice
                        ]
                    );

                    // For variation product
                    if (count($product->variations) > 0) {
                        foreach ($product->variations as $variation) {
                            $variationDiscount = ($item["discount"] * $variation->mrp) / 100;

                            $campaignProduct->campaignProductVariations()->create(
                                [
                                    "campaign_product_id"  => $campaignProduct->id,
                                    "attribute_value_id_1" => $variation->attribute_value_id_1,
                                    "attribute_value_id_2" => $variation->attribute_value_id_2,
                                    "attribute_value_id_3" => $variation->attribute_value_id_3,
                                    "is_default"           => $variation->is_default,
                                    "buy_price"            => $variation->buy_price,
                                    "mrp"                  => $variation->mrp,
                                    "offer_price"          => $variation->mrp - $variationDiscount,
                                    "discount"             => $variationDiscount,
                                    "offer_percent"        => $item["discount"],
                                    "img_path"             => $variation->img_path
                                ]
                            );
                        }
                    }
                }
            }

            DB::commit();

            return $campaign;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id, $status = null)
    {
        try {
            $campaign = $this->model->with([
                "campaignProducts",
                "campaignProducts.product:id,name,img_path",
                "campaignProducts.campaignProductVariations",
                "campaignProducts.campaignProductVariations.attributeValue1:id,value",
                "campaignProducts.campaignProductVariations.attributeValue2:id,value",
                "campaignProducts.campaignProductVariations.attributeValue3:id,value"
            ])
            ->when($status, fn ($query) => $query->where("status", $status))
            ->find($id);

            if (!$campaign) {
                throw new CustomException("Campaign not found");
            }

            return $campaign;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $campaign = $this->model->find($id);

            $campaign->title      = $request->title;
            $campaign->start_date = $request->start_date;
            $campaign->end_date   = $request->end_date;
            $campaign->status     = $request->status;
            $campaign->save();

            // Delete campaign product and product variations
            $campaign->campaignProducts()->delete();

            if ($campaign) {
                foreach ($request->items as $item) {
                    $product = Product::select("id", "buy_price", "mrp", "sell_price", "discount" )
                    ->with("variations")
                    ->find($item["product_id"]);

                    if (!$product) {

                        throw new CustomException("Product not found");
                    }

                    // For discount type fixed
                    if ($item["discount_type"] === "fixed") {
                        // For non variation product
                        $discountValue = 0;
                        $offerPrice    = 0;
                        if ($product->mrp > 0) {
                            $discountValue = $item["discount"];
                            $offerPrice    = $product->mrp - $item["discount"];
                        }

                        $campaignProduct = CampaignProduct::create(
                            [
                                "campaign_id"    => $campaign->id,
                                "product_id"     => $item["product_id"],
                                "buy_price"      => $product->buy_price,
                                "mrp"            => $product->mrp,
                                "discount"       => $item["discount"],
                                "discount_type"  => $item["discount_type"],
                                "discount_value" => $discountValue,
                                "offer_price"    => $offerPrice
                            ]
                        );

                        // For variation product
                        if (count($product->variations) > 0) {
                            foreach ($product->variations as $variation) {
                                $variationDiscount     = $item["discount"];
                                $variationOfferPercent = ($variationDiscount * 100) / $variation->mrp;

                                CampaignProductVariation::create(
                                    [
                                    "campaign_product_id"  => $campaignProduct->id,
                                    "attribute_value_id_1" => $variation->attribute_value_id_1,
                                    "attribute_value_id_2" => $variation->attribute_value_id_2,
                                    "attribute_value_id_3" => $variation->attribute_value_id_3,
                                    "is_default"           => $variation->is_default,
                                    "buy_price"            => $variation->buy_price,
                                    "mrp"                  => $variation->mrp,
                                    "offer_price"          => $variation->mrp - $variationDiscount,
                                    "discount"             => $variationDiscount,
                                    "offer_percent"        => $variationOfferPercent,
                                    "image"                => $variation->image
                                    ]
                                );
                            }
                        }
                    } else {
                        // For discount type percentage
                        // For non variation product
                        $discountValue = 0;
                        $offerPrice    = 0;
                        if ($product->mrp > 0) {
                            $discountValue = ($item["discount"] * $product->mrp) / 100;
                            $offerPrice    = $product->mrp - $discountValue;
                        }

                        $campaignProduct = CampaignProduct::create(
                            [
                                "campaign_id"    => $campaign->id,
                                "product_id"     => $item["product_id"],
                                "buy_price"      => $product->buy_price,
                                "mrp"            => $product->mrp,
                                "discount"       => $item["discount"],
                                "discount_type"  => $item["discount_type"],
                                "discount_value" => $discountValue,
                                "offer_price"    => $offerPrice
                            ]
                        );

                        // For variation product
                        if (count($product->variations) > 0) {
                            foreach ($product->variations as $variation) {
                                $variationDiscount = ($item["discount"] * $variation->mrp) / 100;

                                CampaignProductVariation::create(
                                    [
                                    "campaign_product_id"  => $campaignProduct->id,
                                    "attribute_value_id_1" => $variation->attribute_value_id_1,
                                    "attribute_value_id_2" => $variation->attribute_value_id_2,
                                    "attribute_value_id_3" => $variation->attribute_value_id_3,
                                    "is_default"           => $variation->is_default,
                                    "buy_price"            => $variation->buy_price,
                                    "mrp"                  => $variation->mrp,
                                    "offer_price"          => $variation->mrp - $variationDiscount,
                                    "discount"             => $variationDiscount,
                                    "offer_percent"        => $item["discount"]
                                    ]
                                );
                            }
                        }
                    }
                }
            }

            DB::commit();

            return $campaign;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $campaign = $this->model->find($id);
            if (!$campaign) {
                throw new CustomException("Campaign not found");
            }

            // Delete campaign product and product variations
            $campaign->campaignProducts()->delete();

            return $campaign->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function campaignProductPrice($request)
    {
        try {
            return CampaignProductVariation::select("campaign_product_id", "size_id", "color_id", "mrp", "offer_price", "discount", "offer_percent")
            ->where("campaign_product_id", $request->campaign_product_id)
            ->where("attribute_value_id_1", $request->attribute_value_id_1)
            ->where("attribute_value_id_2", $request->attribute_value_id_2)
            ->where("attribute_value_id_3", $request->attribute_value_id_3)
            ->first();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $paginateSize = $request->input("paginate_size", null);

        try {
            $campaign = $this->model->with([
                "campaignProducts",
                "campaignProducts.product:id,name",
                "campaignProducts.campaignProductVariations",
                "campaignProducts.campaignProductVariations.attributeValue1:id,value",
                "campaignProducts.campaignProductVariations.attributeValue2:id,value",
                "campaignProducts.campaignProductVariations.attributeValue3:id,value"
            ])
            ->onlyTrashed()
            ->orderBy("created_at", "desc")
            ->paginate($paginateSize);

            return $campaign;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $campaign = $this->model->onlyTrashed()->find($id);
            if (!$campaign) {
                throw new CustomException("Campaign not found");
            }

            // Delete campaign product and product variations
            $campaign->restore();

            return $campaign;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $campaign = $this->model->withTrashed()->find($id);
            if (!$campaign) {
                throw new CustomException("campaign not found", 404);
            }

            $campaign->campaignProducts()->delete();
            $campaign->campaignProducts->campaignProductVariations()->delete();

            return $campaign->forceDelete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
