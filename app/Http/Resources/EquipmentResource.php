<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'parent' => collect($this->whenLoaded('parent'))->only(['id', 'name', 'name_e']),
            'parent_id' => $this->parent_id,
            "location" => collect($this->whenLoaded('location'))->only(['id', 'name', 'name_e']),
            "location_id" => $this->location_id,
            "periodic_maintenance" =>collect($this->whenLoaded('periodicMaintenance'))->only(['id', 'name', 'name_e']) ,
            "periodic_maintenance_id" => $this->periodic_maintenance_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
