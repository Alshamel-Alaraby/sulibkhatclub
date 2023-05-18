<?php

namespace Modules\HR\Transformers;

use App\Http\Resources\Employee\EmployeeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OnlineRequestResource extends JsonResource
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
            'request_type_id' => $this->request_type_id,
            'employee_id' => $this->employee_id,
            'start_from' => $this->start_from,
            'end_date' => $this->end_date,
            'descriptions' => $this->descriptions,
            'amount' => $this->amount,
            'from_hour' => $this->from_hour,
            'to_hour' => $this->to_hour,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'request_type' => new RequestTypeResource($this->requestType),
            'employee' => new EmployeeResource($this->employee),
        ];
    }
}
