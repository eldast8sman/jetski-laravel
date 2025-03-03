<?php

namespace App\Http\Resources;

use App\Models\EventTicketPricing;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventBookingResource extends JsonResource
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
        return [
            'uuid' => $this->uuid,
            'event' => new JetskiEventResource($this->jetskiEvent),
            'booking_reference' => $this->booking_reference,
            'total_quantity' => $this->total_quantity,
            'total_amount' => $this->total_amount,
            'tickets' => $tickets
        ];
    }
}
