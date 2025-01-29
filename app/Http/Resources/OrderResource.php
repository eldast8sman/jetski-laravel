<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'order_number' => $this->g5_order_number,
            'description' => $this->description,
            'type' => $this->type,
            'paid_from' => $this->paid_from,
            'delivery_status' => $this->delivery_status,
            'payment_status' => $this->payment_status,
            'date_ordered' => $this->date_ordered,
            'served_by' => $this->served_by,
            'created_at' => $this->created_at,
            'order_items' => $this->order_item->count() > 0 ? OrderItemResource::collection($this->order_item) : null
        ];
    }
}
