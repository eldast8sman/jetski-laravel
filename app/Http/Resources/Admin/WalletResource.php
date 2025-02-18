<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'bank' => 'Sparkle MFB',
            'account_number' => $this->account_number,
            'wallet_balance' => ($this->wallet->balance >= 0) ? $this->wallet->balance : 0,
            'outstanding_balance' => ($this->wallet->balance <= 0) ? abs($this->wallet->balance) : 0,
        ];
    }
}
