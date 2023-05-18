<?php

namespace Modules\BoardsRent\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'code' => $this->code,
            'price' => $this->price,
             "panels" => PanelResource::collection($this->panels),
        ];
    }
}
