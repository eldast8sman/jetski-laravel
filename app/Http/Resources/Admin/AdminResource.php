<?php

namespace App\Http\Resources\Admin;

use App\Models\FileManager;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'photo' => !empty($this->photo) ? FileManager::where('id', $this->id)->first(['path', 'url', 'size', 'extension', 'filename']) : null,
            'bank_account_details' => $this->account()->first(['bank_name', 'account_number', 'account_name'])
        ];
    }
}
