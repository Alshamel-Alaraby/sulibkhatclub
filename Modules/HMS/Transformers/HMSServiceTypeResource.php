<?php

namespace Modules\HMS\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HMSServiceTypeResource extends JsonResource
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
            'hours' => $this->hours,
            'minutes' => $this->minutes,
            'specialty_id' => $this->specialty_id,
            'specialty' =>new HMSSpecialtyResource($this->specialty),
            'price' => $this->price,
            // 'price_after_sale' => $this->price_after_sale,
            'note' => $this->note,
        ];
    }
}
