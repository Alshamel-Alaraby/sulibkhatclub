<?php

namespace Modules\BoardsRent\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class LookUpResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [

            "id" => $this->id,
            "name" => $this->name,
            "name_e" => $this->name_e,
            "type" => $this->type,

            "children" => LookUpResource::collection($this->children),
            'is_parent' => $this->is_parent,
            "is_children" => $this->is_children,

            "parent_id" => $this->parent_id,
        ];
    }
}
