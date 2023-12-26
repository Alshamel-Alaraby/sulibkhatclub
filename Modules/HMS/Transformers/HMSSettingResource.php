<?php

namespace Modules\HMS\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HMSSettingResource extends JsonResource
{
    /**HMSPatientAllDataResource
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'interval' => $this->interval,
            'taxes' => $this->taxes,
            'is_there_insurance' => $this->is_there_insurance,
        ];
    }
}
