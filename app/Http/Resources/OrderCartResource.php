<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderCartResource extends JsonResource
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
            'order_no' => $this->order_no,
            'order_type' => $this->order_type,
            'delivery_address' => $this->delivery_address,
            'items' => OrderCartItemResource::collection($this->order_cart_items),
            'item_amount' => $this->item_amount,
            'tip_amount' => $this->tip_amount,
            'total_quantity' => $this->total_quantity,
            'total_amount' => $this->total_amount,
            'time_ordered' => $this->time_ordered,
            'trackings' => OrderTrackerResource::collection($this->order_trackers),
        ];
    }
}
