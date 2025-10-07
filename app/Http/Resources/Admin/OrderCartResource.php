<?php

namespace App\Http\Resources\Admin;

use App\Models\User;
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
        $user = $this->user;
        return [
            'uuid' => $this->uuid,
            'user' => [
                'uuid' => $user->uuid,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'phone' => $user->phone,
                'photo' => $user->photo,
                'parent' => !empty($user->parent_id) ? User::find($user->parent_id, ['uuid', 'firstname', 'lastname', 'email', 'phone', 'photo']) : null
            ],
            'order_no' => $this->order_no,
            'order_type' => $this->order_type,
            'delivery_address' => $this->delivery_address,
            'items' => !empty($this->order_cart_items) ? OrderCartItemResource::collection($this->order_cart_items) : null,
            'item_amount' => $this->item_amount,
            'tip_amount' => $this->tip_amount,
            'total_quantity' => $this->total_quantity,
            'total_amount' => $this->total_amount,
            'time_ordered' => $this->time_ordered,
            'status' => $this->status,
            'trackings' => !empty($this->order_trackers) ? OrderTrackerResource::collection($this->order_trackers) : "",
        ];
    }
}
