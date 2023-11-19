<?php

namespace Modules\HR\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceSettingResource extends JsonResource
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
            'pre_in' => $this->pre_in,
            'post_in' => $this->post_in,
            'absent_minutes' => $this->absent_minutes,
            'pre_out' => $this->pre_out,
            'post_out' => $this->post_out,
            'max_out' => $this->max_out,
            'location_fingerprint' => $this->location_fingerprint,
            'mobile_id_fingerprint' => $this->mobile_id_fingerprint,
            'pre_in_fingerprint' => $this->pre_in_fingerprint,
            'day_off_fingerprint' => $this->day_off_fingerprint,
            'public_holiday_fingerprint' => $this->public_holiday_fingerprint,
            'post_out_fingerprint' => $this->post_out_fingerprint,
            'pre_out_fingerprint' => $this->pre_out_fingerprint,
            'holiday_fingerprint' => $this->holiday_fingerprint,
            'Training_course_fingerprint' => $this->Training_course_fingerprint,
        ];
    }
}
