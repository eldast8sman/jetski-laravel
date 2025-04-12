<?php

namespace App\Http\Resources;

use App\Models\OrderCart;
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
        if($this->reason == "Order"){
            $narration = OrderCart::find($this->reason_id)->order_no;
        } else {
            if($this->type == 'Credit'){
                $narration = "Account Funding";
            } else {
                $narration = "";
            }
        }
        return [
            'type' => $this->type,
            'amount' => $this->amount,
            'reason' => $this->reason,
            'narration' => $narration,
            'created' => $this->created_at
        ];
    }
}
