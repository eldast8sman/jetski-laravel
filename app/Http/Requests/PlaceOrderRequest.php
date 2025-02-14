<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
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
            'order_type' => 'required|string|in:Delivery,Take Out,Dining In',
            'delivery_address' => 'required_if:order_type,Delivery|string',
            'longitude' => 'numeric|nullable',
            'latitude' => 'numeric|nullable',
            'delivery_phone' => 'required_if:order_type,Delivery|string',
            'delivery_email' => 'required_if:order_type,Delivery|email',
            'items' => 'required|array',
            'items.*.slug' => 'required|string|exists:food_menus,slug',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.add_ons' => 'nullable|array',
            'items.*.add_ons.*.id' => 'required|string|exists:food_menus,slug',
            'items.*.add_ons.*.quantity' => 'required|integer|min:1',
        ];
    }
}
