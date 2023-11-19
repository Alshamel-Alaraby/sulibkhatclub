<?php

namespace Modules\HR\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeFingerprintDetailResource extends JsonResource
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
            'employee_fingerprint_header_id' => $this->employee_fingerprint_header_id,
            'employee' => $this->employee,
            'EmployeeFingerprintHeader' => $this->EmployeeFingerprintHeader,

        ];
    }
}
