<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFoodMenuRequest extends FormRequest
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
            'menu_category' => 'string|exists:menu_categories,uuid|nullable',
            'name' => 'required|string',
            'description' => 'string|nullable',
            'shelf_life_from' => 'date|nullable',
            'shelf_life_to' => 'date|after:shelf_life_from|nullable',
            'ingredients' => 'string|nullable',
            'details' => 'string|nullable',
            'add_ons' => 'array|nullable',
            'add_ons.*' => 'string|exists:food_menus,uuid',
            'photos' => 'array|nullable',
            'photos.*' => 'file|mimes:png,jpg,jpeg|max:2500',
            'is_stand_alone' => 'required|boolean',
            'is_add_on' => 'required|boolean'
        ];
    }
}
