<?php

namespace Modules\HR\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            //  'location_dates' => LocationDateResource::collection($this->locationDates),
            // 'location_dates' => $this->locationDates,

        ];
    }
}
