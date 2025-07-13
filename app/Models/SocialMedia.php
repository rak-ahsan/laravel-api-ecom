<?php

namespace App\Models;

use App\Classes\BaseModel;

class SocialMedia extends BaseModel
{
    protected $table = 'social_medias';

    public $uploadPath = 'uploads/socialMedias';

}
