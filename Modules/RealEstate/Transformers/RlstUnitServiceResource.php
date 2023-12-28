<?php

namespace Modules\RealEstate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RlstUnitServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *RlstUnitServicesCollectionResource
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
//            'unit_id' => $this->unit_id,
            'unit' => $this->whenLoaded('unit'),
            'service' => $this->whenLoaded('service'),
            'price' => $this->default_price,
            'from_date' => $this->from_date,

        ];
    }
}
