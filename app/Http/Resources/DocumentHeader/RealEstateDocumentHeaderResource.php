<?php

namespace App\Http\Resources\DocumentHeader;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Document\DocumentResource;
use App\Http\Resources\DocumentHeaderDetail\RealEstateDocumentHeaderDetailResource;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\GeneralCustomer\GeneralCustomerResource;
use App\Http\Resources\PaymentMethod\PaymentMethodResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RealEstateDocumentHeaderResource extends JsonResource
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

            'id'    => $this->id,
            'document_status_id' => $this->document_status_id,
            'reason' => $this->reason,
            'branch_id' => $this->branch_id,
            'date' => $this->date,
            'serial_number' => $this->serial_number,
            'prefix' => $this->prefix,
            'serial_id' => $this->serial_id,
            'document_id' => $this->document_id,
            'related_document_prefix' => $this->related_document_prefix,
            'related_document_id' => $this->related_document_id,
            'related_document_number' => $this->related_document_number,
            'sell_method_id' => $this->sell_method_id,
            'employee_id' => $this->employee_id,
            'customer_id' => $this->customer_id,
            'task_id' => $this->task_id,
            'external_salesmen_id' => $this->external_salesmen_id,
            'total_invoice' => $this->total_invoice,
            'invoice_discount' => $this->invoice_discount,
            'net_invoice' => $this->net_invoice,
            'remaining' => $this->remaining,
            'complete_status' => $this->complete_status,
            'unrelaized_revenue' => $this->unrelaized_revenue,
            'sell_method_discount' => $this->sell_method_discount,
            'external_commission' => $this->external_commission,
            'payment_method_id'        => $this->payment_method_id,
            'company_id'        => $this->company_id,
            'revenue' => $this->revenue,
            'unrealized_commission' => $this->unrealized_commission,
            'commission' => $this->commission,
            'total_depit_note' => $this->total_depit_note,

            'branch' => new BranchResource($this->whenLoaded('branch')),
            'document_number' =>  $this->whenLoaded('documentNumber'),
            'document'                => $this->whenLoaded('document'),

            'customer' => collect($this->whenLoaded('customer'))->only(['id','name','name_e','address','customer_sub_category'])  ,

            'header_details' => RealEstateDocumentHeaderDetailResource::collection($this->whenLoaded('documentHeaderDetails')),

            'payment_method' => $this->whenLoaded('paymentMethod'),
            'employee'   => collect($this->whenLoaded('employee'))->only(['id','name','name_e','customer_handel']),
        ];
    }
}
