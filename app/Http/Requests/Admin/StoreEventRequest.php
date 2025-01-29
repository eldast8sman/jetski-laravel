<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
            'title'      => ['required', 'string'],
            'description'   => ['required',  'min:10'],
            'sponsored_by'   => ['required',  'min:10'],
            'date' => ['required', 'date'],
            'notification_type' => ['required', 'string'],
            'notification_image_id' => ['required', 'exists:notification_images,id'],
            'photo' => ['required', 'file', 'mimes:jpg,jpeg,png']
        ];
    }
}
