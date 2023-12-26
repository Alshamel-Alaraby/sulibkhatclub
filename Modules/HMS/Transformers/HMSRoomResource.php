<?php

namespace Modules\HMS\Transformers;

use App\Http\Resources\Branch\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;

class HMSRoomResource extends JsonResource
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
            'name' => $this->name,
            'name_e' => $this->name_e,
            'branch_id' => $this->branch_id,
            'note' => $this->note,
            'rooms_category_id' => $this->rooms_category_id,
            'rooms_category' => new HMSRoomsCategoryResource($this->rooms_category),
            'branch' => new BranchResource($this->branch),
            'schedule_times' =>HMSRoomsScheduleResource::collection($this->schedule),
            'has_schedule' => $this->schedule()->count() > 0 ? true :false

        ];
    }
}
