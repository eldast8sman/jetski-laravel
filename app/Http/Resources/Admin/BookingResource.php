<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'photo' => !empty($this->photo()->first()) ? $this->photo()->first(['url'])->url : null,
            'guest_amount' => $this->guest_amount,
            'created_guests' => $this->created_guests,
            'date' => $this->date,
            'user' => [
                'firstname' => $this->user->firstname,
                'lastname' => $this->user->lastname,
                'email' => $this->user->email,
                'photo' => $this->user->photo
            ],
            'invites' => ($this->invites()->count() > 0) ? InviteResource::collection($this->invites) : null
        ];
    }
}
