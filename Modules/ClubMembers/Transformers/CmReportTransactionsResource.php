<?php

namespace Modules\ClubMembers\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\ClubMembers\Transformers;
use Modules\ClubMembers\Entities\CmMember;
use Illuminate\Support\Facades\DB;


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
            
            "full_name" =>DB::table('cm_members')->select('full_name')->where('membership_number', $this->member_number)->first()->full_name,
            'member_number' => $this->member_number,
            'serial_id' => $this->serial_id,
            'document_no' => $this->document_no,
            'date' => $this->date,
            'amount' => $this->amount,          
            'year' => $this->year,
            'type' => $this->type,

        ];


    }

}
