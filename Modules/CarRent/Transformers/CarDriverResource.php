<?php

namespace Modules\CarRent\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CarDriverResource extends JsonResource
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
            'employee_id' => $this->employee_id,
            'driving_license' => $this->driving_license,
            'license_exp_date' => $this->license_exp_date,
            'daily_price' => $this->daily_price,
            'weekly_price' => $this->weekly_price,
            'monthly_price' => $this->monthly_price,
            'employee' => $this->employee
        ];
    }
}
