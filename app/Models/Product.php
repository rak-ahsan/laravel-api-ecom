<?php

namespace App\Models;

use App\Classes\BaseModel;
use App\Models\GalleryImage;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public $uploadPath = "uploads/products";

    protected $casts = [
        "brand_id"        => "integer",
        "category_id"     => "integer",
        "sub_category_id" => "integer",
        "free_shipping"   => "boolean",
    ];

    public function brand() : BelongsTo
    {
        return $this->belongsTo(Brand::class, "brand_id", "id");
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }

    public function subCategory() : BelongsTo
    {
        return $this->belongsTo(SubCategory::class, "sub_category_id", "id");
    }

    public function variations() : HasMany
    {
        return $this->hasMany(ProductVariation::class, "product_id", "id");
    }

    public function attributeValues1()
    {
        return $this->belongsToMany(AttributeValue::class, "product_variations", "product_id", "attribute_value_id_1")
        ->withTimestamps();
    }

    public function attributeValues2()
    {
        return $this->belongsToMany(AttributeValue::class, "product_variations", "product_id", "attribute_value_id_2")
        ->withTimestamps();
    }

    public function attributeValues3()
    {
        return $this->belongsToMany(AttributeValue::class, "product_variations", "product_id", "attribute_value_id_3")
        ->withTimestamps();
    }

    public function images() : HasMany
    {
        return $this->hasMany(GalleryImage::class, "product_id", "id");
    }

    public function upSellProducts()
    {
        return $this->belongsToMany(Product::class, "up_sell_product", "product_id", "up_sell_id")->withTimestamps();
    }
}
