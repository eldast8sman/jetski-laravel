<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdsResource extends JsonResource
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
            'campaign_name' => $this->campaign_name,
            'type' => $this->type,
            'description' => $this->description,
            'image_banner' => $this->banner->url,
            'campaign_start' => $this->campaign_start,
            'campaign_end' => $this->campaign_end,
            'ads_link' => $this->ads_link,
            'clicks' => $this->clicks,
            'impressions' => $this->impressions,
            'conversions' => $this->conversions,
            'status' => $this->status
        ];
    }
}
