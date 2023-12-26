<?php

namespace Modules\HMS\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HMSPatientAllDataResource extends JsonResource
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
            'health_history' => HMSPatientHealthHistoryResource::collection($this->health_history()->latest()->get()),
            'medical_files' => HMSPatientMedicalFileResource::collection($this->medical_files()->latest()->get()),
            'appointments' => HMSAppointmentResource::collection($this->appointments()->latest()->get()),
            'prescriptions' => HMSPrescriptionResource::collection($this->prescriptions()->latest()->get()),
        ];
    }
}
