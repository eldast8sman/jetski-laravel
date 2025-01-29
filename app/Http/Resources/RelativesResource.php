<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RelativesResource extends JsonResource
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
            'relationship' => ucfirst($this->relationship),
            'photo' => $this->photo,
            'gender' => ucfirst($this->gender),
            'marital_status' => ucfirst($this->marital_status),
            'dob' => $this->dob,
            'address' => $this->address,
            'status' => $this->status
        ];
    }
}
