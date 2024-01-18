<?php

namespace App\Http\Resources;

use App\Models\SpatieRole;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $role = SpatieRole::query()->findOrFail($this->id);
        return [
            'id' => $this->id,
            "name" => $this->name,
            "name_e" => $this->name_e,
            "permissions" => PermissionsResource::collection($this->permissions),
            "hotkeys" => $role->hotkeys,
        ];
    }
}
