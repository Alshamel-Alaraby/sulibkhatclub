<?php

namespace Modules\HMS\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HMSPatientResource extends JsonResource
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
            'name' => $this->name,
            'name_e' => $this->name_e,
            'date_of_birth' => $this->date_of_birth,
            'safe_number' => $this->safe_number,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'whatsapp' => $this->whatsapp,
            'patient_weight' => $this->patient_weight,
            'patient_height' => $this->patient_height,
            'address' => $this->address,
            'image' => $this->image,
            'gender' => $this->gender,
            'blood' => $this->blood,
            'type' => $this->type,
            'is_active' => $this->user ? $this->user->is_active : 0,
            'code_country' => $this->code_country,


        ];
    }
}
