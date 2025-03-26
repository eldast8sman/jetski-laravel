<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllUserResource extends JsonResource
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
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'phone' => $this->phone,
            'photo' => $this->photo,
            'membership_id' => $this->membership_id,
            'membership' => !empty($this->membership) ? (!empty($this->membership_information->membership()) ? $this->membership->membership() : null) : null
        ];
    }
}
