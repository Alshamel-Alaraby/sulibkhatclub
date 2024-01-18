<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralTaskResource extends JsonResource
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
            'id' => $this->id,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'task_title' => $this->task_title,
            'execution_date' => $this->execution_date,
            'actual_execution_date' => $this->actual_execution_date,
            'execution_duration' => $this->execution_duration,
            'customer_id' => $this->customer_id,
            'execution_end_date' => $this->execution_end_date,
            'actual_execution_end_date' => $this->actual_execution_end_date,
            'actual_execution_duration' => $this->actual_execution_duration,
            'notification_date' => $this->notification_date,
            'actual_start_time' => $this->actual_start_time,
            'start_time' => $this->start_time,
            'actual_end_time' => $this->actual_end_time,
            'end_time' => $this->end_time,
            'department_task_id' => $this->department_task_id,
            'employee_id' => $this->employee_id,
            'department_id' => $this->department_id,
            'status_id' => $this->status_id,
            'note' => $this->note,
            'location_id' => $this->location_id,
            'sub_location_id' => $this->sub_location_id,
            'priority_id' => $this->priority_id,
            'is_closed' => $this->is_closed,
            'admin_note' => $this->admin_note,
            'task_requirement' => $this->task_requirement,
            'type' => $this->type,
            'equipment_id' => $this->equipment_id,
            "work_nature_id" => $this->work_nature_id,
            "address" => $this->address,
            "signature" => $this->signature,
            'departmentTask' => $this->departmentTask,
            'department' => $this->department,
            'employee' => $this->employee,
            'customer' => $this->customer,
            'equipment' => $this->equipment,
            'status' => $this->status,
            'location' => $this->location,
            'sub_location' => $this->sub_location,
            'owners' => [$this->created_by ? User::find($this->created_by)->employee_id : null],
            'supervisors' => $this->supervisors->pluck('id')->toArray(),
            'notifications' => $this->attentions->pluck('id')->toArray(),
            'priority' => $this->priority,
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,
            'created_at' => $this->created_at

        ];
    }
}
