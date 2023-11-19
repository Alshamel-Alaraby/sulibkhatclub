<?php

namespace Modules\RealEstate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RlstUnitServiceResource extends JsonResource
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
            'unit' => $this->whenLoaded('unit'),
            'service' => $this->whenLoaded('service'),
            'price' => $this->price,
        ];
    }
}
