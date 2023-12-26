<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneralInsuranceDocumentResource extends JsonResource
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
            'related_id' => $this->related_id,
            'insurance_type_id' => $this->insurance_type_id,
            'insurance_number' => $this->insurance_number,
            'insurance_company_id' => $this->insurance_company_id,
            'amount' => $this->amount,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'related_field' => $this->related_field,
            'insurance_type' => $this->insuranceType,
            'insurance_company' => $this->insuranceCompany,

        ];
    }
}

