<?php

namespace Modules\ClubMembers\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CheckDateMemberTransactionResource extends JsonResource
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
            'id' => $this->id,
            'member' => collect($this->member)->only(['id','first_name','second_name','third_name','last_name','family_name','membership_number','membership_date']),

            'serial_number' => $this->serial_number,

            'date' => $this->date,


            // 'amount' => $this->amount,
            // 'module_type' => $this->module_type,
            // 'invoice_id' => $this->invoice_id,
            // 'break_id' => $this->break_id,
            // 'year_from' => $this->year_from,
            // 'year_to' => $this->year_to,
            // 'type' => $this->type,
            // 'prefix' => $this->prefix,
            // 'number_of_years' => $this->number_of_years,
            // 'created_by' => $this->created_by,
            // 'financial_year_id' => $this->financial_year_id,

            // 'branch' => collect($this->branch)->only(['id','name','name_e']),
            // 'sponsor' => collect($this->sponsor)->only(['id','name','name_e']),
            // 'document' => collect($this->document),
        ];
    }
}
