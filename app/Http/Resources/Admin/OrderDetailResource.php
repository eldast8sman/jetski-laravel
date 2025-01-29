<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            'order_number' => $this->g5_order_number,
            'description' => $this->description,
            'type' => $this->type,
            'paid_from' => $this->paid_from,
            'delivery_status' => $this->delivery_status,
            'payment_status' => $this->payment_status,
            'date_ordered' => $this->date_ordered,
            'served_by' => $this->served_by,
            'created_at' => $this->created_at,
            'user' => [
              'id' => $this->user->id,
              // 'uuid' => $this->user->uuid,
              'firstname' => $this->user->firstname,
              'lastname' => $this->user->lastname,
              'phone' => $this->user->phone,
              'photo' => $this->user->photo,
            ],
            'order_item' => $this->order_item->count() > 0 ? OrderItemResource::collection($this->order_item) : null
        ];
    }
}
