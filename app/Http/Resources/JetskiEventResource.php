<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JetskiEventResource extends JsonResource
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
            'event_title' => $this->event_title,
            'description' => $this->description,
            'date_time' => json_decode($this->date_time, true),
            'location_type' => $this->location_type,
            'location' => $this->location,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'photo' => $this->photo->url,
            'ticket_pricings' => $this->tickets()
        ];
    }
}
