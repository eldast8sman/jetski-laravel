<?php

namespace App\Http\Resources\Admin;

use App\Models\EventTicketPricing;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JetskiEventBookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tickets = [];
        foreach(json_decode($this->tickets, true) as $ticket){
            $tickets[] = [
                'ticket' => EventTicketPricing::find($ticket['ticket_id'], ['uuid', 'name']),
                'unit_price' => $ticket['unit_price'],
                'quantity' => $ticket['quantity'],
                'total_price' => $ticket['total_price']
            ];
        }
        $user = $this->user;
        return [
            'uuid' => $this->uuid,
            'booking_reference' => $this->booking_reference,
            'total_amount' => $this->total_amount,
            'total_quantity' => $this->total_quantity,
            'ticket_details' => $tickets,
            'user' => [
                'uuid' => $user->uuid,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'photo' => $user->photo
            ]
        ];
    }
}
