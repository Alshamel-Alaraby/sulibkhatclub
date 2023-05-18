<?php

namespace App\Http\Resources\Document;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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
            'name' => $this->name,
            'name_e' => $this->name_e,
//            'is_default' => $this->getIsDefault(),
            'is_default' => $this->is_default,
            'is_admin' => $this->is_admin,
            'company_id' => $this->company_id,
            'attributes' => $this->attributes,
            'branche_id' => $this->branche_id,
//            "contusion" => $this->getContusion(),
            "contusion" => $this->contusion,
            'serial_id' => $this->serial_id,
            'required' => $this->required,
            'need_approve' => $this->need_approve,
            'payment_plan_installments' => $this->payment_plan_installments,
//            'document_Relateds' => $this->documentRelateds,
//            'required' => $this->getRequired(),
//            'need_approve' => $this->getNeedApprove(),
            'document_Relateds' => $this->documentRelateds,
            'required' => $this->required,
            'need_approve' => $this->need_approve,
            'document_detail_type' => $this->document_detail_type,

            'document_relateds' => $this->documentRelateds,
            'employees'         => $this->employees,
        ];
    }
}
