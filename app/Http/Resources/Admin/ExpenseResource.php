<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"           => $this->id,
            "category_id"  => $this->category_id,
            "amount"       => $this->amount,
            "description"  => $this->description,
            "expense_date" => $this->expense_date,
            "category"     => $this->whenLoaded('category'),
            "created_by"   => $this->whenLoaded('createdBy'),
        ];
    }
}
