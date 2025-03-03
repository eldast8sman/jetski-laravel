<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJetskiEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'event_title' => 'required|string',
            'description' => 'required|string',
            'audience' => 'required|array',
            'date_time' => 'required|array',
            'date_time.*.date' => 'required|date',
            'date_time.*.time' => 'required|date_format:H:i',
            'location_type' => 'required|string|in:Virtual,Physical',
            'location' => 'required|string',
            'longitude' => 'nullable|string',
            'latitude' => 'nullable|string',
            'photo' => 'file|mimes:jpeg,jpg,png|max:2048|nullable',
            'tickets_pricing' => 'required|array',
            'tickets_pricing.*.uuid' => 'required|string|exists:event_ticket_pricings,uuid',
            'tickets_pricing.*.total_quantity' => 'required|integer|min:1',
            'tickets_pricing.*.available_quantity' => 'required|integer|min:0',
        ];
    }
}
