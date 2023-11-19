<?php

namespace Modules\HR\Transformers;

use App\Http\Resources\AllDropListResource;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeePublicHolidayHeaderResource extends JsonResource
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
            'vdate' => $this->vdate,
            'public_holiday_header_id' => $this->public_holiday_header_id,
            'public_holiday_header' => new PublicHolidayHeaderResource($this->publicHolidayHeader),
            'employees' => AllDropListResource::collection($this->employees),

        ];
    }
}
