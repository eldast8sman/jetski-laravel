<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmploymentInformationRequest extends FormRequest
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
            'employer' => 'string|nullable',
            'position' => 'string|nullable',
            'industry' => 'string|nullable',
            'address' => 'string|nullable',
            'email' => 'string|nullable',
            'phone' => 'string|nullable',
            'pa_name' => 'string|nullable',
            'pa_email' => 'string|nullable',
            'pa_phone' => 'string|nullable'
        ];
    }
}
