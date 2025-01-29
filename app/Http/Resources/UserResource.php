<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->lastname,
            'photo' => $this->photo,
            'gender' => $this->gender,
            'dob' => $this->dob,
            'address' => $this->address,
            'marital_status' => $this->marital_status,
            'account_number' => $this->account_number,
            'wallet' => $this->wallet()->first(['uuid', 'balance']),
            'membership' => $this->membership ? $this->membership->name : null,
            'membership_information' => $this->membership_information,
            'employment_details' => $this->employment_detail,
            'watercraft' => $this->watercraft,
            'relationships' => $this->relations(),
            'last_login' => $this->last_login,
            'prev_login' => $this->prev_login
        ];
    }
}
