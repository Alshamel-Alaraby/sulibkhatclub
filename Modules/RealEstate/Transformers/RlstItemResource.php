<?php

namespace Modules\RealEstate\Transformers;

use App\Http\Resources\Unit\UnitResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\FileResource;


class RlstItemResource extends JsonResource
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
            'code_number' => $this->code_number,
            'type' => $this->type,
            'price' => $this->price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            "unit_id" => $this->unit_id,
            "unit" => $this->units,
            "media" =>  isset($this->files) ? FileResource::collection($this->files) : null,
            "units" => $this->units,
            "categories" => $this->categories
        ];
    }

}
