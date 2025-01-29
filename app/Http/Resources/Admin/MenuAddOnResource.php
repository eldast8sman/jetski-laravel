<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuAddOnResource extends JsonResource
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
            'name' => ucwords(strtolower($this->name)),
            'description' => ucwords(strtolower($this->description)),
            'amount' => $this->amount,
            'availability' => $this->availability,
            'shelf_life_from' => $this->shelf_life_from,
            'shelf_life_to' => $this->shelf_life_to,
            'ingredients' => $this->ingredients,
            'details' => $this->details,
            'is_stand_alone' => $this->is_stand_alone,
            'is_add_on' => $this->is_add_on,
            'photos' => MenuPhotoResource::collection($this->photos)
        ];
    }
}
