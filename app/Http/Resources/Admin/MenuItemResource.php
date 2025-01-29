<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'available' => $this->available,
            'category' => ucfirst($this->category),
            'amount' => $this->amount,
            'photo' => $this->photo,
            'g5_id' => $this->g5_id,
            'modifier_id' => $this->modifier_id
        ];
    }
}
