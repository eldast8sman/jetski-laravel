<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->membership_id,
            'name' => !empty($this->membership_id) ? $this->membership()->first()->name : null,
            'watercraft' => !empty($this->membership_information->first()) ? $this->membership_information()->first([
                'uuid',
                'title',
                "title",
                "make",
                "model",
                "hin_number",
                "year",
                "loa",
                "beam",
                "draft",
                "nwa",
                "nwa_expiry",
                "mmsi",
                "call_sign"
            ]) : null
        ];
    }
}
