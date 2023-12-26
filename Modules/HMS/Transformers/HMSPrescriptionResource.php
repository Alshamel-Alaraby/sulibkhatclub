<?php

namespace Modules\HMS\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HMSPrescriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $doctor = $this->doctor;
        $patient = $this->patient;
        return [
            "id" => $this->id ,
            "patient_id" => $this->patient_id ,
            "doctor_id" => $this->doctor_id ,
            "appointment_id" => $this->appointment_id ,
            "doctor" => [
                "id" => $doctor->id,
                "name" => $doctor->name,
                "name_e" => $doctor->name_e,
            ],
            "patient" => [
                "id" => $patient->id,
                "name" => $patient->name,
                "name_e" => $patient->name_e,
            ],
            "date" => $this->created_at->format('Y-m-d'),
            "drug_list" => $this->drug_list()->select('type','drug_id','strength','dose','duration','drug_advice')->get(),
            "diagnosis_test_list" => $this->diagnosis_list()->select('diagnosis_test_id','description')->get(),
            "content" => [
                'num_of_drugs' => $this->drug_list()->count(),
                'num_of_diagnosis' => $this->diagnosis_list()->count(),
            ]
        ];
    }
}
