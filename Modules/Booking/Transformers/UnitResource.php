<?php

namespace Modules\Booking\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'description' => $this->description,
            'description_e' => $this->description_e,
            'price' => $this->price,
            'unit_status_id' => $this->unit_status_id,
            "unit_status" => $this->unitStatus,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'document_header_details' => $this->documentHeaderDetails

        ];
    }
}
