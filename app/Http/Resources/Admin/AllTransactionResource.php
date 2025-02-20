<?php

namespace App\Http\Resources\Admin;

use App\Models\OrderCart;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->reason == "Order"){
            $narration = OrderCart::find($this->reason_id)->order_no;
        }
        return [
            'uuid' => $this->uuid,
            'amount' => $this->amount,
            'type' => $this->type,
            'reason' => $this->reason,
            'narration' => $this->narration,
            'user' => $this->wallet->user()->first(['uuid', 'firstname', 'lastname', 'photo']),
            'created' => $this->created_at
        ];
    }
}
