<?php

namespace Modules\HMS\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HMSDoctorsWorkTimeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $branch = $this->room->branch;
        return [
            'id' => $this->id,
            'day_id' => $this->day_id,
            'day' => ['name'=>$this->day->name,'name_e'=>$this->day->name_e],
            'room_id' => $this->room_id,
            'room' => ['name'=>$this->room->name,'name_e'=>$this->room->name_e],
            'branch_id' => $this->room->branch_id,
            'branch' => ['name'=>$branch->name,'name_e'=>$branch->name_e],
            'from' => $this->from,
            'to' => $this->to,
        ];
    }
}
