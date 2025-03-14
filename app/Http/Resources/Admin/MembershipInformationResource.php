<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'membership' => $this->membership->name,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'date_joined' => $this->date_joined,
            'expiry_date' => $this->expiry_date,
            'membership_notes' => $this->membership_notes,
            'active_diver' => $this->active_diver,
            'padi_level' => $this->padi_level,
            'padi_number' => $this->padi_number,
            'company' => $this->company,
            'department' => $this->company,
            'referee1' => $this->referee1,
            'referee2' => $this->referee2,
            'referee3' => $this->referee3,
            'referee4' => $this->referee4
        ];
    }
}
 