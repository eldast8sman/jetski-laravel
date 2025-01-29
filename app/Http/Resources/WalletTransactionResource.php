<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
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
            'uuid' => $this->uuid,
            'amount' => $this->amount,
            'type' => $this->type,
            'is_user_credited' => $this->is_user_credited,
            'payment_processor' => $this->payment_processor,
            'order' => ($this->order()->count() > 0) ? new OrderResource($this->order) : null
        ];
    }
}
