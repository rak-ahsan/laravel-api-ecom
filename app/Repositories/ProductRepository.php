<?php

namespace App\Repositories;

use Exception;
use App\Classes\Helper;
use App\Models\Product;
use App\Models\GalleryImage;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductRepository
{
    public function __construct(protected Product $model){}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);
        $status       = $request->input("status", null);
        $categorySlug = $request->input("category_slugs", null);
        $brandId      = $request->input("brand_id", null);
        $categoryId   = $request->input("category_id", null);
        $categoryIds  = $request->input('category_ids', []);
        $brandIds     = $request->input('brand_ids', []);
        $priceRange   = $request->input('price', []);

        try {
            $products = $this->model->select(
                "id", "name", "slug", "brand_id", "category_id", "sub_category_id", "status", "buy_price",
                "mrp", "offer_price", "discount", "sell_price", "offer_percent", "current_stock",
                "minimum_qty", "alert_qty", "type", "sku", "free_shipping", "img_path", "video_url"
            )->with([
                "category:id,name,slug",
                "subCategory:id,name,slug",
                "brand:id,name,slug",
                "variations",
                "variations.attributeValue1:id,value,attribute_id",
                "variations.attributeValue2:id,value,attribute_id",
                "variations.attributeValue3:id,value,attribute_id",
                "variations.attributeValue1.attribute:id,name",
                "variations.attributeValue2.attribute:id,name",
                "variations.attributeValue3.attribute:id,name",
            ])
            ->when($searchKey, function($query) use ($searchKey){
                $query->where("name", "like", "%$searchKey%")
                ->orWhere("sku", "like", "%$searchKey%");
            })
            ->when($status, fn ($query) => $query->where("status", "like", "$status"))
            ->when($categoryId, fn ($query) => $query->where("category_id", "like", "$categoryId"))
            ->when($categorySlug, function ($query) use ($categorySlug){
                $query->whereHas("category", fn ($q) => $q->where('slug', $categorySlug));
            })
            ->when($brandId, fn ($query) => $query->where("brand_id", "like", "$brandId"))
            ->when((count($categoryIds) > 0), fn($query) => $query->whereIn('category_id', $categoryIds))
                ->when((count($brandIds) > 0),    fn($query)    => $query->whereIn('brand_id', $brandIds))
                // ->when($sku, fn($query)           => $query->where('sku', $sku))
                ->when($priceRange && count($priceRange), function ($query) use ($priceRange) {
                        $minPrice = $priceRange[0];
                        $maxPrice = $priceRange[1];
                        return $query->whereBetween('sell_price', [$minPrice, $maxPrice]);
                    })
            ->orderBy("created_at", "desc")
            ->paginate($paginateSize);

            return $products;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $product = new $this->model();

            $discount     = 0;
            $offerPercent = 0;
            if ($request->offer_price > 0 && $request->offer_price < $request->mrp) {
                $discount = $request->mrp - $request->offer_price;
                $offerPercent = ($discount * 100) / $request->mrp;
            }

            $sellPrice = $request->offer_price > 0 ? $request->offer_price : $request->mrp;

            $product->name              = $request->name;
            $product->slug              = $request->name;
            $product->brand_id          = $request->brand_id;
            $product->category_id       = $request->category_id;
            $product->sub_category_id   = $request->sub_category_id;
            $product->buy_price         = $request->buyPrice ?? 0;
            $product->mrp               = $request->mrp ?? 0;
            $product->offer_price       = $request->offer_price ?? 0;
            $product->sell_price        = $sellPrice;
            $product->discount          = $discount;
            $product->offer_percent     = $offerPercent;
            $product->alert_qty         = $request->alert_qty;
            $product->minimum_qty       = $request->minimum_qty;
            $product->status            = $request->status;
            $product->type              = $request->type;
            $product->sku               = $request->sku;
            $product->free_shipping     = $request->free_shipping ?? 0;
            $product->video_url         = $request->video_url;
            $product->description       = $request->description;
            $product->short_description = $request->short_description;
            $product->meta_keywords     = $request->meta_keywords;
            $product->meta_title        = $request->meta_title;
            $product->meta_description  = $request->meta_description;

            // Check product stock maintain with direct product
            if (Helper::getSettingValue("is_stock_maintain_with_direct_product")) {
                $product->current_stock = $request->current_stock;
            }

            $product->save();

            // Attach  and up sell with product
            $product->upSellProducts()->sync($request->up_sell_ids);

            if (is_array($request->variations) && count($request->variations) > 0) {
                foreach ($request->variations as $key => $variation) {
                    // Calculate variation discount and offer percent
                    $variationDiscount     = 0;
                    $variationOfferPercent = 0;
                    $variationSellPrice    = 0;
                    $variationCurrentStock = 0;
                    $variationMrp          = $variation["mrp"];
                    $variationOfferPrice   = $variation["offer_price"];

                    if ($variationOfferPrice > 0 && $variationOfferPrice < $variationMrp) {
                        $variationDiscount     = $variationMrp - $variationOfferPrice;
                        $variationOfferPercent = ($variationDiscount * 100) / $variationMrp;
                        $variationSellPrice    = $variationOfferPrice;
                    } else {
                        $variationSellPrice    = $variationMrp;
                    }

                    // Check product stock maintain with direct product
                    if (Helper::getSettingValue("is_stock_maintain_with_direct_product")) {
                        $variationCurrentStock = @$variation["current_stock"];
                    }

                    // Upload product variation image
                    $variationImageName = null;
                    if ($request->hasFile("variations." . $key . ".image")) {
                        $image              = $request->file("variations." . $key . ".image");
                        $variationImageName = time() . $key . "." . "webp";
                        $variationImagePath = $image->move(public_path("uploads/products/variationImage"), $variationImageName);

                        // create image manager with desired driver
                        $manager = new ImageManager(new Driver());

                        // read image from file system
                        $variationImg = $manager->read($variationImagePath);

                        // resize by width and height
                        $variationImg->resize(450, 450);

                        $variationImg->save($variationImagePath);
                    }

                    $variationImgPath = "uploads/products/variationImage/" . $variationImageName;

                    ProductVariation::create([
                        "product_id"           => $product->id,
                        "attribute_value_id_1" => !empty($variation['attribute_value_id_1']) ? $variation['attribute_value_id_1'] : null,
                        "attribute_value_id_2" => !empty($variation['attribute_value_id_2']) ? $variation['attribute_value_id_2'] : null,
                        "attribute_value_id_3" => !empty($variation['attribute_value_id_3']) ? $variation['attribute_value_id_3'] : null,
                        "current_stock"        => $variationCurrentStock,
                        "is_default"           => $variation["is_default"] ?? 0,
                        "buy_price"            => $variation["buy_price"],
                        "mrp"                  => $variation["mrp"],
                        "offer_price"          => $variation["offer_price"] ?? 0,
                        "discount"             => $variationDiscount,
                        "offer_percent"        => $variationOfferPercent,
                        "sell_price"           => $variationSellPrice,
                        "img_path"             => $variationImgPath
                    ]);
                }
            }

            // Upload product image
            if ($request->hasFile("image")) {
                Helper::uploadFile($product, $request->image, $product->uploadPath);
            }

            // Upload gallery image
            if ($request->hasFile("gallery_images")) {
                $galleryImages = $request->file("gallery_images");
                $active        = "active";

                foreach ($galleryImages as $key => $image) {
                    $galleryImgName   = time() . $key . "." . "webp";
                    $uploadPath       = GalleryImage::getUploadPath();
                    $galleryImagePath = $image->move(public_path($uploadPath), $galleryImgName);

                    // create image manager with desired driver
                    $manager = new ImageManager(new Driver());

                    // read image from file system
                    $galleryImg = $manager->read($galleryImagePath);

                    // resize by width and height
                    $galleryImg->resize(450, 450);

                    $galleryImg->save($galleryImagePath);

                    $galleryImgSavePath = $uploadPath . '/' . $galleryImgName;

                    GalleryImage::create([
                        "is_active"  => $active,
                        "product_id" => $product->id,
                        "img_path"   => $galleryImgSavePath,
                    ]);

                    $active = null;
                }
            }

            DB::commit();

            $product->load(["brand:id,name", "category:id,name", "variations"]);

            return $product;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function show($id, $status = null)
    {
        try {
            $product = $this->model->with([
                "category:id,name,slug",
                "subCategory:id,name,slug",
                "brand:id,name,slug",
                "variations" => function ($query) {
                    $query->with([
                        'attributeValue1:id,value,attribute_id',
                        'attributeValue2:id,value,attribute_id',
                        'attributeValue3:id,value,attribute_id',
                        'attributeValue1.attribute:id,name',
                        'attributeValue2.attribute:id,name',
                        'attributeValue3.attribute:id,name'
                    ]);
                },
                "images",
                "upSellProducts" => function ($query) {
                    $query->with([
                        'category:id,name',
                        'brand:id,name',
                        'variations' => function ($query) {
                            $query->with([
                                'attributeValue1:id,value',
                                'attributeValue2:id,value',
                                'attributeValue3:id,value',
                                'attributeValue1.attribute:id,name',
                                'attributeValue2.attribute:id,name',
                                'attributeValue3.attribute:id,name',
                            ]);
                        }
                    ]);
                },
            ])
            ->where(fn($q) => $q->where("slug", $id)->orWhere("id", $id))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->firstOrFail();

            return $product;
        } catch (Exception $exception) {
            throw new CustomException("Product not found", 0, $exception);
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $product = $this->model->find($id);

            if (!$product) {
                throw new CustomException("Product not found");
            }

            $discount     = 0;
            $offerPercent = 0;
            if ($request->offer_price > 0 && $request->offer_price < $request->mrp) {
                $discount = $request->mrp - $request->offer_price;
                $offerPercent = ($discount * 100) / $request->mrp;
            }

            $sellPrice = $request->offer_price > 0 ? $request->offer_price : $request->mrp;

            $product->name              = $request->name;
            $product->slug              = $request->name;
            $product->brand_id          = $request->brand_id;
            $product->category_id       = $request->category_id;
            $product->sub_category_id   = $request->sub_category_id;
            $product->buy_price         = $request->buy_price ?? 0;
            $product->mrp               = $request->mrp ?? 0;
            $product->offer_price       = $request->offer_price ?? 0;
            $product->sell_price        = $sellPrice;
            $product->offer_percent     = $offerPercent;
            $product->discount          = $discount;
            $product->alert_qty         = $request->alert_qty;
            $product->minimum_qty       = $request->minimum_qty;
            $product->status            = $request->status;
            $product->type              = $request->type;
            $product->sku               = $request->sku;
            $product->free_shipping     = $request->free_shipping ?? 0;
            $product->video_url         = $request->video_url;
            $product->description       = $request->description;
            $product->short_description = $request->short_description;
            $product->meta_keywords     = $request->meta_keywords;
            $product->meta_title        = $request->meta_title;
            $product->meta_description  = $request->meta_description;

            // Check product stock maintain with direct product
            if (Helper::getSettingValue("is_stock_maintain_with_direct_product")) {
                $product->current_stock = $request->current_stock;
            }

            $product->save();

            // Attach up sell with product
            $product->upSellProducts()->detach();
            $product->upSellProducts()->sync($request->up_sell_ids);

            if ($request->variations && count($request->variations) > 0) {
                // Delete old variation image
                if ($request->delete_variation_image_ids && count($request->delete_variation_image_ids) > 0) {
                    $deleteVariationImages = ProductVariation::whereIn('id', $request->delete_variation_image_ids)->get();

                    if ($deleteVariationImages && count($deleteVariationImages) > 0) {
                        foreach ($deleteVariationImages as $deleteVariationImage) {
                            if ($deleteVariationImage->img_path) {
                                Helper::deleteFile($deleteVariationImage->img_path);
                            }
                        }
                    }
                }

                $variationDetails = [];
                foreach ($request->variations as $key => $variation) {
                    // Calculate variation discount and offer percent
                    $variationDiscount     = 0;
                    $variationOfferPercent = 0;
                    $variationSellPrice    = 0;
                    $variationCurrentStock = 0;
                    $variationMrp          = $variation["mrp"];
                    $variationOfferPrice   = $variation["offer_price"];
                    $variationId           = @$variation["id"];

                    // Check product stock maintain with direct product
                    if (Helper::getSettingValue("is_stock_maintain_with_direct_product")) {
                        $variationCurrentStock = @$variation["current_stock"];
                    }

                    if ($variationOfferPrice > 0 && $variationOfferPrice < $variationMrp) {
                        $variationDiscount     = $variationMrp - $variationOfferPrice;
                        $variationOfferPercent = ($variationDiscount * 100) / $variationMrp;
                        $variationSellPrice    = $variationOfferPrice;
                    } else {
                        $variationSellPrice   = $variationMrp;
                    }

                    // Upload product variation image
                    if ($request->hasFile('variations.' . $key . '.image')) {
                        $image              = $request->file('variations.' . $key . '.image');
                        $variationImageName = "uploads/products/variationImage/" . time() . $key . '.' . 'webp';
                        $variationImagePath = $image->move(public_path('uploads/products/variationImage'), $variationImageName);

                        // create image manager with desired driver
                        $manager = new ImageManager(new Driver());

                        // read image from file system
                        $variationImg = $manager->read($variationImagePath);

                        // resize by width and height
                        $variationImg->resize(450, 450);

                        $variationImg->save($variationImagePath);
                    } else {
                        $variationData = ProductVariation::find($variationId);

                        if ($variationData) {
                            $variationImageName = $variationData->img_path;
                        } else {
                            $variationImageName = null;
                        }
                    }

                    $variationDetails[] = [
                        'product_id'           => $product->id,
                        "attribute_value_id_1" => !empty($variation['attribute_value_id_1']) ? $variation['attribute_value_id_1'] : null,
                        "attribute_value_id_2" => !empty($variation['attribute_value_id_2']) ? $variation['attribute_value_id_2'] : null,
                        "attribute_value_id_3" => !empty($variation['attribute_value_id_3']) ? $variation['attribute_value_id_3'] : null,
                        "current_stock"        => $variationCurrentStock,
                        'is_default'           => $variation['is_default'] ?? 0,
                        'buy_price'            => $variation['buy_price'],
                        'mrp'                  => $variation['mrp'],
                        'offer_price'          => $variation['offer_price'] ?? 0,
                        'discount'             => $variationDiscount,
                        'offer_percent'        => $variationOfferPercent,
                        'sell_price'           => $variationSellPrice,
                        'img_path'             => $variationImageName,
                    ];
                }

                // Delete previous variation and insert new variation
                $product->variations()->delete();
                ProductVariation::insert($variationDetails);
            }

            // Upload product image
            if ($request->hasFile("image")) {
                Helper::uploadFile($product, $request->image, $product->uploadPath, $product->img_path);
            }

            // Delete old gallery image
            if ($request->delete_gallery_image_ids && count($request->delete_gallery_image_ids) > 0) {
                // Get old image
                $oldImages = GalleryImage::whereIn("id", $request->delete_gallery_image_ids)->get();

                foreach ($oldImages as $oldImage) {
                    Helper::deleteFile($oldImage->img_path);
                }

                GalleryImage::whereIn("id", $request->delete_gallery_image_ids)->delete();
            }

            // Upload gallery new image
            if ($request->hasFile("gallery_images")) {
                $active = "active";
                $galleryImages = $request->file("gallery_images");

                foreach ($galleryImages as $key => $image) {
                    $galleryImageName = time() . $key . "." . "webp";
                    $uploadPath = GalleryImage::getUploadPath();
                    $galleryImagePath = $image->move(public_path($uploadPath), $galleryImageName);

                    // create image manager with desired driver
                    $manager = new ImageManager(new Driver());

                    // read image from file system
                    $galleryImg = $manager->read($galleryImagePath);

                    // resize by width and height
                    $galleryImg->resize(450, 450);

                    $galleryImg->save($galleryImagePath);

                    $galleryImgSavePath = $uploadPath . '/' . $galleryImageName;

                    GalleryImage::create([
                        "is_active"  => $active,
                        "product_id" => $product->id,
                        "img_path"   => $galleryImgSavePath,
                    ]);

                    $active = null;
                }
            }

            DB::commit();

            $product->load(["brand:id,name", "category:id,name", "variations"]);

            return $product;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function destroy($id)
    {
        try {
            $product = $this->model->find($id);

            if (!$product) {
                throw new CustomException("Product not found");
            }

            //  Delete old image
            if ($product->img_path) {
                Helper::deleteFile($product->img_path);
            }

            foreach ($product->images as $image) {
                Helper::deleteFile($image->img_path);
            }

            foreach ($product->variations as $variation) {
                Helper::deleteFile($variation->img_path);
            }

            $product->images()->delete();

            return $product->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $searchKey    = $request->input("search_key", null);
        $status       = $request->input("status", null);
        $categorySlug = $request->input("category_slugs", null);
        $brandId      = $request->input("brand_id", null);
        $categoryId   = $request->input("category_id", null);

        try {
            $products = $this->model->onlyTrashed()->with(
                [
                    "category:id,name,slug",
                    "subCategory:id,name,slug",
                    "brand:id,name,slug",
                    "variations",
                    "variations.attributeValue1:id,value",
                    "variations.attributeValue2:id,value",
                    "variations.attributeValue3:id,value"
                ])
                ->when($searchKey, function ($query) use ($searchKey) {
                    $query->where("name", "like", "%$searchKey%")
                        ->orWhere("sku", "like", "%$searchKey%");
                })
                ->when($status, fn($query) => $query->where("status", "like", "$status"))
                ->when($categoryId, fn ($query) => $query->where("category_id", "like", "$categoryId"))
                ->when($categorySlug, function ($query) use ($categorySlug) {
                    $query->whereHas("category", fn($q) => $q->where('slug', $categorySlug));
                })
                ->when($brandId, fn($query) => $query->where("brand_id", "like", "$brandId"))
                ->orderBy("created_at", "desc")
                ->paginate($paginateSize);

            return $products;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $product = $this->model->onlyTrashed()->find($id);

            if (!$product) {
                throw new CustomException("Product not found", 404);
            }
            $product->restore();

            return $product;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $product = $this->model->withTrashed()->find($id);

            if (!$product) {
                throw new CustomException("Product not found", 404);
            }

            $product->images()->delete();
            $product->variations()->delete();
            $product->upSellProducts()->delete();

            return $product->forceDelete();

        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function productHistory($request, $id)
    {
        $limit = $request->input("limit", 5);

        try {
            $audits = $this->model->find($id)->audits()->with("user:id,username")->take($limit)->get();

            return $audits;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function updateStatus($request)
    {
        try {
            foreach ($request->product_ids as $id) {
                $product = $this->model->find($id);

                if (!$product) {
                    throw new CustomException("Product not found");
                }

                $product->status = $request->status;
                $product->save();
            }

            return true;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function categoryWiseProduct($request, $slug)
    {
        $paginateSize = Helper::checkPaginateSize($request);

        try {
            $products = $this->model->with(
            [
                "category:id,name",
                "subCategory:id,name",
                "brand:id,name",
                "variations",
                "variations.attributeValue1:id,value",
                "variations.attributeValue2:id,value",
                "variations.attributeValue3:id,value"
            ])
            ->where("status", "active")
            ->whereHas("category", fn($query) => $query->where("slug", $slug))
            ->paginate($paginateSize);

            return $products;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function subCategoryWiseProduct($request, $slug)
    {
        $paginateSize = Helper::checkPaginateSize($request);

        try {
            $products = $this->model->with(
                [
                    "category:id,name",
                    "subCategory:id,name",
                    "brand:id,name",
                    "variations",
                    "variations.attributeValue1:id,value",
                    "variations.attributeValue2:id,value",
                    "variations.attributeValue3:id,value"
                ]
            )
            ->where("status", "active")
            ->whereHas("subCategory", fn ($query) => $query->where("slug", $slug))
            ->paginate($paginateSize);

            return $products;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function productVariation($request)
    {
        $variationData     = null;
        $allAttributeData  = [];
        $attributeId1      = $request->input("attribute_id_1", null);
        $attributeId2      = $request->input("attribute_id_2", null);
        $attributeId3      = $request->input("attribute_id_3", null);
        $attributeValueId1 = $request->input("attribute_value_id_1", null);
        $attributeValueId2 = $request->input("attribute_value_id_2", null);
        $attributeValueId3 = $request->input("attribute_value_id_3", null);

        try {
            $variations = ProductVariation::with([
                "attributeValue1:id,value,attribute_id",
                "attributeValue2:id,value,attribute_id",
                "attributeValue3:id,value,attribute_id",
                "attributeValue1.attribute:id,name",
                "attributeValue2.attribute:id,name",
                "attributeValue3.attribute:id,name",
            ])
            ->where("product_id", $request->product_id)
            ->when($attributeValueId1, fn ($query) => $query->where("attribute_value_id_1", $attributeValueId1))
            ->when($attributeValueId2, fn ($query) => $query->where("attribute_value_id_2", $attributeValueId2))
            ->when($attributeValueId3, fn ($query) => $query->where("attribute_value_id_3", $attributeValueId3))
            ->get();

            // Get variation product price
            if (count($variations) === 1) {
                $variationData = $variations->map(function ($variation) {
                    return [
                        'mrp'           => $variation["mrp"],
                        'offer_price'   => $variation["offer_price"],
                        'discount'      => $variation["discount"],
                        'sell_price'    => $variation["sell_price"],
                        'offer_percent' => $variation["offer_percent"],
                    ];
                });
            }

            $allData = ProductVariation::with([
                "attributeValue1:id,value,attribute_id",
                "attributeValue2:id,value,attribute_id",
                "attributeValue3:id,value,attribute_id",
                "attributeValue1.attribute:id,name",
                "attributeValue2.attribute:id,name",
                "attributeValue3.attribute:id,name",
            ])
            ->where("product_id", $request->product_id)
            ->when($attributeValueId1 && !$attributeId1, fn ($query) => $query->where("attribute_value_id_1", $attributeValueId1))
            ->when($attributeValueId2 && !$attributeId2, fn ($query) => $query->where("attribute_value_id_2", $attributeValueId2))
            ->when($attributeValueId3 && !$attributeId3, fn ($query) => $query->where("attribute_value_id_3", $attributeValueId3))
            ->get();

            $attributes1 = $allData->pluck('attributeValue1')->unique("id")->values();
            $attributes2 = $allData->pluck('attributeValue2')->unique("id")->values();
            $attributes3 = $allData->pluck('attributeValue3')->unique("id")->values();

            if ($attributeId1) {
                $allAttributeData = $this->formatAttributeData($attributes1);

            }

            if ($attributeId2) {
                $allAttributeData = $this->formatAttributeData($attributes2);
            }

            if ($attributeId3) {
                $allAttributeData = $this->formatAttributeData($attributes3);
            }

            $groupAttributes = $this->groupAttributes($variations);

            if (count($allAttributeData) > 0) {
                $groupAttributes = array_merge($groupAttributes, $allAttributeData);
            }

            $data["attributes"]      = $groupAttributes;
            $data["variation_price"] = $variationData;

            return $data;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    private function groupAttributes($variations)
    {
        $attributes = [];

        foreach ($variations as $variation) {
            // Loop through possible attribute values
            for ($i = 1; $i <= 3; $i++) {
                $attributeValue = $variation->{"attributeValue$i"} ?? null;
                if ($attributeValue) {
                    $attributeName = @$attributeValue->attribute->name;

                    // Check if the attribute name exists in the array
                    if (!isset($attributes[$attributeName])) {
                        $attributes[$attributeName] = [];
                    }

                    // Add unique attributes to the array
                    $attributes[$attributeName][$attributeValue->id] = [
                        'attribute_value_id' => $attributeValue->id,
                        'attribute_value'    => $attributeValue->value,
                        'attribute_id'       => $attributeValue->attribute_id
                    ];
                }
            }
        }

        // Flatten the arrays to ensure that each attribute type is properly formatted
        foreach ($attributes as &$attributeGroup) {
            $attributeGroup = array_values($attributeGroup);
        }

        return $attributes;
    }

    private function formatAttributeData($data)
    {
        $attribute = [];

        foreach ($data as $item) {
            $attributeName = @$item->attribute->name;
            if ($attributeName) {
                $attribute[$attributeName][] = [
                    'attribute_value_id' => $item->id,
                    'attribute_value'    => $item->value,
                    'attribute_id'       => $item->attribute_id
                ];
            }
        }

        return $attribute;
    }
}
