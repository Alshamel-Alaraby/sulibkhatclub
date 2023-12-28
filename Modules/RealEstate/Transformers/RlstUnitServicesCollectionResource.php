<?php

namespace Modules\RealEstate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RlstUnitServicesCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $services = $this->whenLoaded('services')->unique('id');
        return [
            'id' => $this->id,
            'services' => $services,
            'unit' => [
                'name' => $this->name,
                'name_e' => $this->name_e,
            ],
            'default_price' => $services->sum('pivot.default_price'),
        ];
    }
}
