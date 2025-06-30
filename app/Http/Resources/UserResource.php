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
            'email' => $this->email,
            'phone' => $this->phone,
            'photo' => $this->photo,
            'gender' => $this->gender,
            'dob' => $this->dob,
            'address' => $this->address,
            'marital_status' => $this->marital_status,
            'account_number' => $this->account_number,
            'wallet' => new WalletDetailsResource($this),
            'membership' => $this->membership ? $this->membership->name : null,
            'membership_information' => $this->membership_information,
            'employment_details' => $this->employment_detail,
            'watercraft' => $this->watercraft,
            'relationships' => $this->relations(),
            'delivery_addresses' => DeliveryAddressResource::collection($this->delivery_address),
            'last_login' => $this->last_login,
            'prev_login' => $this->prev_login
        ];
    }
}
