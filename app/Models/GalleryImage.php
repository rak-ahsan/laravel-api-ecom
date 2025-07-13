<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class GalleryImage extends Model
{
    use HasFactory;

    protected $uploadPath = 'uploads/products/gallery';

    protected $guarded = ["id"];

    public static  function getUploadPath()
    {
        return (new self)->uploadPath;
    }
}
