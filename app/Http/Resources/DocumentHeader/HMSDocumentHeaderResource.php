<?php

namespace App\Http\Resources\DocumentHeader;

use App\Http\Resources\DocumentHeaderDetail\DocumentHeaderDetailResource;
use App\Models\DocumentHeaderDetail;
use Illuminate\Http\Resources\Json\JsonResource;

class HMSDocumentHeaderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'date' => $this->date,
            'prefix' => $this->prefix,
            'doctor_id' => $this->doctor_id,
            'patient_id' => $this->patient_id,
            'net_invoice' => $this->net_invoice,
            'appointment_id' => $this->appointment_id,
            'from_doctor_id' => $this->from_doctor_id,
            'patient_insurance_number' => $this->patient_insurance_number,
            'total_company_insurance_amount' => $this->total_company_insurance_amount,
            'total_patient_amount' => $this->total_patient_amount,
            'company_insurance_id' => $this->company_insurance_id,
            'company_id' => $this->company_id,
            'branch' => $this->branch ? $this->get_relation_data($this->branch) : [],
            'doctor' => $this->doctor ? $this->get_relation_data($this->doctor) : [],
            'appointment' => $this->appointment ? $this->get_relation_data($this->appointment) : [],
            'from_doctor' => $this->from_doctor ? $this->get_relation_data($this->from_doctor) : [],
            'patient' => $this->patient ? $this->get_relation_data($this->patient) : [],
            'company_insurance' => $this->company_insurance ? $this->get_relation_data($this->company_insurance) : [],
            'header_details' => DocumentHeaderDetailResource::collection(DocumentHeaderDetail::whereDocumentHeaderId($this->id)->get()),

        ];
    }

    protected function get_relation_data($model){

        return [
            'id' => $model->id,
            'name' => $model->name,
            'name_e' => $model->name_e,
        ];
    }
}
