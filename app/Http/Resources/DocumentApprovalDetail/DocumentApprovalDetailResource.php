<?php

namespace App\Http\Resources\DocumentApprovalDetail;

use App\Http\Resources\Employee\EmployeeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentApprovalDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"                     => $this->id,
            "document_serial_number" => $this->document_serial_number,
            "notes"                  => $this->notes,
            "decision_date"          => $this->decision_date,
            'approval_time'          => $this->approval_time,
            "employee_id"            => $this->employee_id,
            "decision_id"            => $this->decision_id,
            "documentStatuse"        => $this->documentStatuse,
            "employee"               => new EmployeeResource($this->employee)
        ];
    }
}
