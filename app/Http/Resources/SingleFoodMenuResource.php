<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleFoodMenuResource extends JsonResource
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
            'category' => new MenuCategoryResource($this->category),
            'amount' => $this->amount,
            'availability_time' => !empty($this->availability_time) ? json_decode($this->availability_time, true) : null,
            'shelf_life_from' => $this->shelf_life_from,
            'shelf_life_to' => $this->shelf_life_to,
            'ingredients' => $this->ingredients,
            'details' => $this->details,
            'add_ons' => ($this->user_add_ons() != null) ? (($this->user_add_ons()->count() > 0) ? MenuAddOnResource::collection($this->user_add_ons()->get()) : null) : null,
            'modifiers' => (($this->user_modifiers() !== null) and ($this->user_modifiers()->count() > 0)) ? MenuAddOnResource::collection($this->user_modifiers()->get()) : null,
            'photos' => MenuPhotoResource::collection($this->photos) ?? null
        ];
    }
}
