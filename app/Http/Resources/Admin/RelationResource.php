<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RelationResource extends JsonResource
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
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'other_emails' => $this->other_emails,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'marital_status' => $this->marital_status,
            'address' => $this->address,
            'status' => $this->status,
            'membsership' => $this->membership_information->membership() ?? null
        ];
    }
}
