<?php

namespace Modules\HMS\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HMSAppointmentTimesResource extends JsonResource
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
          "start" => $this->start,
          "end" => $this->end,
        ];
    }
}
