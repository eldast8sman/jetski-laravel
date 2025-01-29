<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWatercraftInformationRequest extends FormRequest
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
            'title' => 'string|nullable',
            'make' => 'string|nullable',
            'model' => 'string|nullable',
            'hin_number' => 'string|nullable',
            'year' => 'date_format:Y|nullable',
            'loa' => 'string|nullable',
            'draft' => 'string|nullable',
            'nwa' => 'string|nullable',
            'nwa_expiry' => 'string|nullable',
            'mmsi' => ''
        ];
    }
}
 