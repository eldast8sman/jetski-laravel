<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRelativeRequest extends FormRequest
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
            'relationship' => 'required|string',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'phone' => 'required|string',
            'dob' => 'required|date',
            'gender' => 'required|string',
            'marital_status' => 'required|string',
            'address' => 'required|string',
            'photo' => 'required|file|mimes:jpg,jpeg,png',
            'notifications' => 'required|boolean',
            'can_use' => 'required|boolean'
        ];
    }
}
