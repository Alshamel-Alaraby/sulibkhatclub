<?php

namespace Modules\ClubMembers\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CmMemberRejectResource extends JsonResource
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
            'note' => $this->note,
            'session_number' => $this->session_number,
            'prefix' => $this->prefix,
            'member' => collect($this->whenLoaded('member'))->only(['id','first_name','second_name','third_name','last_name','family_name']),
            'branch' => collect($this->whenLoaded('branch'))->only(['id','name','name_e']),
            'member_type' => collect($this->whenLoaded('memberType'))->only(['id','name','name_e']),


        ];
    }
}
