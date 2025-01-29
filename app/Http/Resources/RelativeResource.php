<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RelativeResource extends JsonResource
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
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'dob' => $this->dob,
            'relationship' => ucfirst($this->relationship),
            'gender' => ucfirst($this->gender),
            'marital_status' => ucfirst($this->marital_status),
            'photo' => $this->photo,
            'address' => $this->address,
            'can_use' => $this->can_use,
            'notifications' => $this->notifications
        ];
    }
}
