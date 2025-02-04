<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderCartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'food_menu' => $this->food_menu()->first(['slug', 'name']),
            'add_ons' => $this->sorted_add_ons(),
            'add_on_price' => $this->add_on_price,
            'unit_price' => $this->unit_price,
            'total_unit_price' => $this->total_unit_price,
            'quantity' => $this->quantity,
            'total_price' => $this->total_price,
        ];
    }
}
