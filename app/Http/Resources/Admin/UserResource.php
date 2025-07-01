<?php

namespace App\Http\Resources\Admin;

use App\Models\User;
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
            'uuid' => $this->uuid,
            'slug' => $this->slug,
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
            'photo' => $this->photo,
            'account_number' => $this->account_number,
            'wallet' => $this->wallet()->first(['uuid', 'balance']),
            'membership_id' => $this->membership_id,
            'relations' => empty($this->parent_id) ? (!empty(User::where('parent_id', $this->id)->get()) ? RelationResource::collection(User::where('parent_id', $this->id)->get()) : null) : null,
            'membership_information' => new MembershipInformationResource($this->membership_information) ?? null,
            'watercraft' => $this->watercraft ?? null,
            'employment_details' => $this->employment_detail ?? null,
            'delivery_addresses' => DeliveryAddressResource::collection($this->delivery_address),
            'status' => $this->can_use
        ];
    }
}
