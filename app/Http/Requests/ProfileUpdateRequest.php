<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
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
            'username' => 'required|string|'.Rule::unique('users', 'username')->ignore(auth('user-api')->user()->id, 'id'),
            'phone' => 'required|string',
            'dob' => 'required|date',
            'gender' => 'string|in:Male,Female|nullable',
            'marital_status' => 'string|in:Single,Married|nullable',
            'address' => 'string'
        ];
    }
}
