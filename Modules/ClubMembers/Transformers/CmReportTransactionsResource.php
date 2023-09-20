<?php

namespace Modules\ClubMembers\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CmReportTransactionsResource extends JsonResource
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

            'serial_id' => $this->serial_id,
            'document_no' => $this->document_no,
            'date' => $this->date,
            'amount' => $this->amount,
            'member_number' => $this->member_number,
            'year' => $this->year,
            'type' => $this->type,

        ];


    }

}
