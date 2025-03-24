<?php

namespace App\Http\Resources\Admin;

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
        $menu = $this->food_menu;
        $photo = $menu->photos()->first()->file_manager()->first(['url'])->url ?? null;
        return [
            'uuid' => $this->uuid,
            'food_menu' => [
                'uuid' => $menu->uuid,
                'name' => $menu->name,
                'photo' => $photo
            ],
            'add_ons' => $this->sorted_add_ons('admin'),
            'modifier' => $this->sort_modifier('admin'),
            'add_on_price' => $this->add_on_price,
            'unit_price' => $this->unit_price,
            'total_unit_price' => $this->total_unit_price,
            'quantity' => $this->quantity,
            'total_price' => $this->total_price,
        ];
    }
}
