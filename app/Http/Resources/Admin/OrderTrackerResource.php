<?php

namespace App\Http\Resources\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderTrackerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->agent_type == 'admin'){
            $agent = Admin::where('id', $this->agent_id)->first(['uuid', 'firstname', 'lastname']);
        } else {
            $agent = null;
        }
        return [
            'status' => $this->status,
            'time' => $this->created_at,
            'agent' => $agent
        ];
    }
}
