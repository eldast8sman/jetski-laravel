<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookEventTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'event_id' => 'required|string|exists:jetski_events,uuid',
            'tickets' => 'required|array',
            'tickets.*.ticket_id' => 'required|string|exists:event_ticket_pricings,uuid',
            'tickets.*.quantity' => 'required|integer|min:1'
        ];
    }
}
