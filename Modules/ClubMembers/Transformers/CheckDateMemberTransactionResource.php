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
            'date' => $this->date,
            'amount' => $this->amount,
            'module_type' => $this->module_type,
            'invoice_id' => $this->invoice_id,
            'break_id' => $this->break_id,
            'year_from' => $this->year_from,
            'year_to' => $this->year_to,
            'type' => $this->type,
            'prefix' => $this->prefix,
            'serial_number' => $this->serial_number,
            'number_of_years' => $this->number_of_years,
            'created_by' => $this->created_by,
            'financial_year_id' => $this->financial_year_id,

            'branch' => collect($this->whenLoaded('branch'))->only(['id','name','name_e']),
            'sponsor' => collect($this->whenLoaded('sponsor'))->only(['id','name','name_e']),
            'member' => collect($this->whenLoaded('member'))->only(['id','first_name','second_name','third_name','last_name','family_name']),
            'document' => collect($this->whenLoaded('document')),
        ];
    }
}
