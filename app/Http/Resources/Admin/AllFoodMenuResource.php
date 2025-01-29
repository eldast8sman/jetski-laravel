<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllFoodMenuResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'amount' => $this->amount,
            'category' => new MenuCategoryResource($this->category),
            'photo' => ($this->photos()->count() > 0) ? $this->photos()->first()->file_manager->url : null
        ];
    }
}
