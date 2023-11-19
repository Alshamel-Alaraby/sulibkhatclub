<?php

namespace Modules\HR\Transformers;

use App\Http\Resources\AllDropListResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationDateResource extends JsonResource
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
            'vdate' => $this->vdate,
            'location_id' => $this->location_id,
            'location' => new LocationResource($this->location),
            'employees' => AllDropListResource::collection($this->employees),
        ];
    }
}
