<?php

namespace Modules\HR\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeePublicHolidayDetailResource extends JsonResource
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
            'employee_public_holiday_header_id' => $this->employee_public_holiday_header_id,
            'employee_id' => $this->employee_id,
            'employee' => $this->employee,
            'employee_public_holiday_header' => $this->employeePublicHolidayHeader
        ];
    }
}
