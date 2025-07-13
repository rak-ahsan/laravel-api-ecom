<?php

namespace App\Models;

use App\Classes\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    public function purchaseDetails() : HasMany
    {
        return $this->hasMany(PurchaseDetail::class, "purchase_id", "id");
    }
}
