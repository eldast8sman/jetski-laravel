<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
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
            'type' => $this->type,
            'information' => $this->information,
            'notification_type' => $this->notification_type,
            'notification_image_id' => $this->notification_image_id,
            'notification_image_url' => $this->notification_image->photo,
            'photo' => $this->photo()->first()->url
        ];
    }
}
