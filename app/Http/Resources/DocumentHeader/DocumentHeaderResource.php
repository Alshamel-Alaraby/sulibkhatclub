<?php

namespace App\Http\Resources\DocumentHeader;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Document\DocumentResource;
use App\Http\Resources\DocumentHeaderDetail\DocumentHeaderDetailResource;
use App\Http\Resources\DocumentStatuse\DocumentStatuseResource;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\ExternalSalesmen\ExternalSalesmenResource;
use App\Http\Resources\GeneralCustomer\GeneralCustomerResource;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BoardsRent\Transformers\SellMethodResource;

class DocumentHeaderResource extends JsonResource
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
            'id'                       => $this->id,
            'document_status_id'       => $this->document_status_id,
            'reason'                   => $this->reason,
            'branch_id'                => $this->branch_id,
            'date'                     => $this->date,
            'serial_number'            => $this->serial_number,
            'prefix'                   => $this->prefix,
            'serial_id'                => $this->serial_id,
            'document_id'              => $this->document_id,
            'related_document_prefix'  => $this->related_document_prefix,
            'related_document_id'      => $this->related_document_id,
            'related_document_number'  => $this->related_document_number,
            'sell_method_id'           => $this->sell_method_id,
            'employee_id'              => $this->employee_id,
            'customer_id'              => $this->customer_id,
            'task_id'                  => $this->task_id,
            'external_salesmen_id'     => $this->external_salesmen_id,
            'total_invoice'            => $this->total_invoice,
            'invoice_discount'         => $this->invoice_discount,
            'net_invoice'              => $this->net_invoice,
            'remaining'                => $this->remaining,
            'complete_status'          => $this->complete_status,

//            'document_status'         => new DocumentStatuseResource($this->documentStatus),
            'branch'                  => new BranchResource($this->branch),
//            'document'                => new DocumentResource($this->document),
//            'related_document'        => new DocumentResource($this->relatedDocument),
//            'sell_method'             => new SellMethodResource($this->sellMethod),
            'employee'                => new EmployeeResource($this->employee),
            'customer'                => new GeneralCustomerResource($this->customer),
//            'task'                    => new TaskResource($this->task),
//            'external_salesmen'       => new ExternalSalesmenResource($this->externalSalesmen),
            'document_header_details'   =>  DocumentHeaderDetailResource::collection($this->documentHeaderDetails),



        ];
    }
}
