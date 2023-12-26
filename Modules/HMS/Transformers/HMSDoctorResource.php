<?php

namespace Modules\HMS\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HMSDoctorResource extends JsonResource
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
            'email' => $this->email,
            'mobile' => $this->mobile,
            'whatsapp' => $this->whatsapp,
            'analytic_account' => $this->type == 'indoctor' ? $this->analytic_account  :'-',
            'selling_items_commission' => $this->type == 'indoctor' ? $this->selling_items_commission  :'-',
            'transfer_patient_commission' => $this->type == 'indoctor' ? $this->transfer_patient_commission  :'-',
            'medical_commission' => $this->type == 'indoctor' ? $this->medical_commission  :'-',
            'is_active' => $this->type == 'indoctor' && $this->user? (int) $this->user->is_active  :'-'  ,
            'code_country' => $this->type == 'indoctor' && $this->employee ? $this->employee->code_country   :'-'  ,
            'specialty_id' => $this->specialty_id,
            'type' => $this->type,
            'specialty' => new HMSSpecialtyResource($this->specialty),
            'work_times' =>HMSDoctorsWorkTimeResource::collection($this->work_times),
        ];
    }
}
