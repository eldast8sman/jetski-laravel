<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddItemToCartRequest extends FormRequest
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
            'slug' => 'required|string|exists:food_menus,slug',
            'quantity' => 'required|integer|min:1',
            'add_ons' => 'nullable|array',
            'add_ons.*.id' => 'required|string|exists:food_menus,slug',
            'add_ons.*.quantity' => 'required|integer|min:1',
        ];
    }
}
