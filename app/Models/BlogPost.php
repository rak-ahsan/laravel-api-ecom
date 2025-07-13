<?php

namespace App\Models;

use App\Classes\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class BlogPost extends BaseModel
{
    public $uploadPath = "uploads/blogPosts";

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }

    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(Tag::class, "blog_post_tag", "blog_post_id", "tag_id")->withTimestamps();
    }
}
