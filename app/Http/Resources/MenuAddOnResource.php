<?php

namespace App\Http\Resources;

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
            'slug' => $this->slug,
            'name' => ucwords(strtolower($this->name)),
            'description' => ucwords(strtolower($this->description)),
            'amount' => $this->amount,
            'shelf_life_from' => $this->shelf_life_from,
            'shelf_life_to' => $this->shelf_life_to,
            'ingredients' => $this->ingredients,
            'details' => $this->details,
            'photos' => MenuPhotoResource::collection($this->photos)
        ];
    }
}
