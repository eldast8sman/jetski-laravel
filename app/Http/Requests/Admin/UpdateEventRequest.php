<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
            'title' => 'required|string',
            'description' => 'required|string|min:10',
            'sponsored_by' => 'required|string|min:10',
            'date' => 'required|date',
            'notification_image_id' => 'required|integer|exists:notification_images,id',
            'photo' => 'file|mimes:png,jpg,jpeg|nullable' 
        ];
    }
}
