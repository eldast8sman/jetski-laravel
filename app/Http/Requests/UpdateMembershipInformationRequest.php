<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMembershipInformationRequest extends FormRequest
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
            'membership_id' => 'required|integer|exists:products,id',
            'title' => 'required|string',
            'make' => 'required|string',
            'model' => 'required|string',
            'hin_number' => 'required|string',
            'year' => 'required|string|max:4|min:4',
            'loa' => 'required|string',
            'beam' => 'required|string',
            'draft' => 'required|string',
            'nwa' => 'required|string',
            'nwa_expiry' => 'required|string',
            'mmsi' => 'required|string',
            'call_sign' => 'required|string'
        ];
    }
}
