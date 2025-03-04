<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'photo' => $this->photo,
            'is_parent' => !empty($this->parent_id) ? 0 : 1,
            'parent_id' => $this->parent_id,
            'authorization' => $this->authorization
        ];
    }
}
