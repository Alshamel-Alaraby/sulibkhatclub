<?php

namespace Modules\RealEstate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RlstContractHeaderResource extends JsonResource
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
            'id'                      => $this->id,
            'branch_id'               => $this->branch_id,
            'date'                    => $this->date,
            'serial_id'               => $this->serial_id,
            'related_document_id'     => $this->related_document_id,
            'related_document_number' => $this->related_document_number,
            'related_document_prefix' => $this->related_document_prefix,
            'employee_id'             => $this->employee_id,
            'tenant_id'               => $this->tenant_id,
            'notice_period'           => $this->notice_period,
            'insurance_amount'        => $this->insurance_amount,
            'attendants'              => $this->attendants,
            'automatic_renews'        => $this->automatic_renews,
            'external_salesmen_id'    => $this->external_salesmen_id,
            'commission'              => $this->commission,
            'posted'                  => $this->posted,
            'receipt_print_detail'    => $this->receipt_print_detail,
            'document_id'             => $this->document_id,
            'prefix'                  => $this->prefix,
            'serial_number'           => $this->serial_number,



            'branch'                  => $this->whenLoaded('branch'),
            'serial'                  => $this->whenLoaded('serial'),
            'related_document'        => $this->whenLoaded('relatedDocument'),
            'employee'                => $this->whenLoaded('employee'),
            'tenant'                  => $this->whenLoaded('tenant'),
            'externalSalesmen'        => $this->whenLoaded('externalSalesmen'),
            'document'                => $this->whenLoaded('document'),
            'contractHeaderDetail'    => $this->whenLoaded('contractHeaderDetail'),
            'units'                   => $this->whenLoaded('units'),

        ];
    }
}
