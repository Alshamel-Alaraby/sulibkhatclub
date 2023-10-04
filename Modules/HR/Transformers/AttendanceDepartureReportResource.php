<?php

namespace Modules\HR\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceDepartureReportResource extends JsonResource
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
            'att_code' => $this->att_code,
            'department_id' => $this->department_id,
            'branch_id' => $this->branch_id,
            'fingerprints' => $this->fingerprints,
            // 'department' => $this->depertment,
            // 'branch' => $this->branch,
        ];
    }
}
