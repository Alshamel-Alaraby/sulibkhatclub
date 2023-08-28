<?php

namespace App\Http\Resources\VoucherHeader;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Document\DocumentResource;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\GeneralCustomer\GeneralCustomerResource;
use App\Http\Resources\PaymentMethod\PaymentMethodResource;
use App\Http\Resources\Serials\SerialResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BoardsRent\Transformers\CustomerResource;

class VoucherHeaderResource extends JsonResource
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
            'document_id'              => $this->document_id,
            'branch_id'                => $this->branch_id,
            'date'                     => $this->date,
            'serial_id'                => $this->serial_id,
            'salesmen_id'              => $this->salesmen_id,
            'customer_id'              => $this->customer_id,
            'payment_method_id'        => $this->payment_method_id,
            'amount'                   => $this->amount,
            'serial_number'            => $this->serial_number,
            'prefix'                   => $this->prefix,
            'branch'                   => new BranchResource($this->branch),
            'document'                 => new DocumentResource($this->document),
            'salesmen'                 => new EmployeeResource($this->salesmen),
            'customer'                 => new GeneralCustomerResource($this->customer),
            'serial'                   => new SerialResource($this->serial),
            'paymentMethod'            => new PaymentMethodResource($this->paymentMethod),

        ];
    }
}
