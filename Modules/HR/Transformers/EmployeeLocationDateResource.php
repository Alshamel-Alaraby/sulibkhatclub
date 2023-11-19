<?php

namespace Modules\HR\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeLocationDateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return
            [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'location_date_id' => $this->location_date_id,
            'employee' => $this->employee,
            'locationDate' => $this->locationDate,
        ];
    }
}
