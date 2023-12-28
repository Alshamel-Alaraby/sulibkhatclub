<?php

namespace App\Http\Resources;

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
            'execution_duration' => $this->execution_duration,
            'customer_id' => $this->customer_id,
            'execution_end_date' => $this->execution_end_date,
            'notification_date' => $this->notification_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'department_task_id' => $this->department_task_id,
            'employee_id' => $this->employee_id,
            'customer_id' => $this->customer_id,
            'department_id' => $this->department_id,
            'status_id' => $this->status_id,
            'note' => $this->note,
            // 'location_id' => $this->location_id,
            'priority_id' => $this->priority_id,
            'is_closed' => $this->is_closed,
            'admin_note' => $this->admin_note,
            'task_requirement' => $this->task_requirement,
            'type' => $this->type,
            'equipment_id' => $this->equipment_id,

            'departmentTask' => $this->departmentTask,
            'department' => $this->department,
            'employee' => $this->employee,
            'customer' => $this->customer,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'equipment' => $this->equipment,
            'status' => $this->status,
            // 'location' => $this->location,
            'priority' => $this->priority,

        ];
    }
}
