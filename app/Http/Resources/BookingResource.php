<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'photo' => !empty($this->photo()->first()) ? $this->photo()->first(['url'])->url : null,
            'link' => $this->link,
            'invites' => ($this->invites()->count() > 0) ? InviteResource::collection($this->invites) : null,
            'guest_amount' => $this->guest_amount
        ];
    }
}
