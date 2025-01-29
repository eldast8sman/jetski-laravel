<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModifierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->SalesItemID) {
            return [
              'id' => $this->SalesItemID,
              'g5_id' => $this->SalesItemID,
              'name' => ucfirst(strtolower($this->Description)),
              'description' => ucfirst(strtolower($this->MenuDescription)),
              'amount' => $this->PriceMode1,
              'available' => $this->Available,
              // 'screen_id' => $this->WSScreenItemID,
              // 'type_id' => $this->ItemTypeID,
              'photo' => $this->Picture,
              // 'parent_id' => $this->ParentID,
              'modifier_id' => $this->Modifier1,
              'group_id' => $this->GroupID
            ];
        }
    }
}
