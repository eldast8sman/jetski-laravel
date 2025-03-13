<?php

namespace App\Http\Resources;

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
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'amount' => $this->amount,
            'photo' => ($this->photos()->count() > 0) ? $this->photos()->first()->file_manager->url : null,
            'type' => $this->type,
        ];
    }
}
