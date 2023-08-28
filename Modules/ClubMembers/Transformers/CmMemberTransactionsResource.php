<?php

namespace Modules\ClubMembers\Transformers;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Document\DocumentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CmMemberTransactionsResource extends JsonResource
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
            'branch' => new BranchResource($this->branch),
            'sponsor' => new CmSponserResource($this->sponsor),
            'document' => new DocumentResource($this->document),


            // year - branch - document - prefix - serial_number
        ];
    }
}
