<?php

namespace Modules\RecievablePayable\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class OpeningBalanceResource extends JsonResource
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
              "id"=>$this->id,
              "date"=>$this->date,
              "currency_id"=>$this->currency_id,
              "customer_id"=>$this->customer_id,
              "debit"=>$this->debit,
              "credit"=>$this->credit,
              "local_debit"=>$this->local_debit,
              "local_credit"=>$this->local_credit,
              "count"=>$this->count,
              "rate"=>$this->rate,
              "net"=>$this->total_local_debit - $this->total_local_credit,
              "total_local_credit"=>$this->total_local_credit,
              "total_local_debit"=>$this->total_local_debit,
          ];
    }
}
