<?php

namespace App\Models;

use Laratrust\Models\Role as RoleModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends RoleModel
{
    public $guarded = [];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
