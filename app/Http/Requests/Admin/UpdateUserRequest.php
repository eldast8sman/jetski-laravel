<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'username' => 'string|nullable',
            'phone' => 'required|string',
            'dob' => 'required|date',
            'gender' => 'string|in:Male,Female|nullable',
            'marital_status' => 'string|in:Single,Married|nullable',
            'address' => 'string',
            'photo' => 'file|mimes:jpg,jpeg,png,svg,webp,gif|max:2048'
        ];
    }
}
