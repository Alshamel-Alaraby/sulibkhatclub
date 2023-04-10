<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\RealEstate\Transformers\RlstInvoiceResource;
use Modules\RecievablePayable\Transformers\BreakDownResource;

class TransactionResource extends JsonResource
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
            'date' => $this->date,
            'amount' => $this->amount,
            'module_type' => $this->module_type,
            'invoice_id' => $this->invoice_id,
            'break_id' => $this->break_id,
//            'invoice' =>  new RlstInvoiceResource($this->invoice),
            'break_down' =>  new BreakDownResource($this->breakDown),

        ];
    }
}
