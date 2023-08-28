<?php

namespace Modules\ClubMembers\Transformers;

use App\Http\Resources\Branch\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CmTransactionResource extends JsonResource
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
            'branch' => new BranchResource($this->branch),
            'sponsor' => new CmSponserResource($this->sponsor),
            "member" => $this->cm_member_id ? new \Modules\ClubMembers\Transformers\CmMemberResource($this->member) : null,

            'year_from' => $this->year_from,
            'year_to' => $this->year_to,
            'type' => $this->type,
            'prefix' => $this->prefix,
            'serial_number' => $this->serial_number,
            'number_of_years' => $this->number_of_years,
            'created_by' => $this->created_by,
            'financial_year_id' => $this->financial_year_id,


            // year - branch - document - prefix - serial_number
        ];
    }
}
