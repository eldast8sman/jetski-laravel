<?php

namespace App\Http\Requests\Admin;

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
            'membership' => 'string|nullable',
            'amount' => 'numeric|nullable',
            'payment_date' => 'date|nullable',
            'expiry_date' => 'date|nullable',
            'membership_notes' => 'string|nullable',
            'active_diver' => 'string|nullable',
            'padi_level' => 'string|nullable',
            'padi_number' => 'string|nullable',
            'company' => 'string|nullable',
            'department' => 'string|nullable',
            'referee1' => 'string|nullable',
            'referee2' => 'string|nullable',
            'referee3' => 'string|nullable',
            'referee4' => 'string|nullable',
            'status' => 'boolean|nullable'
        ];
    }
}
