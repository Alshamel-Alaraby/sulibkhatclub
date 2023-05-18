<?php

namespace App\Http\Resources;

use App\Http\Resources\Depertment\DepertmentResource;
use App\Http\Resources\DepertmentTask\DepertmentTaskResource;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\GeneralCustomer\GeneralCustomerResource;
use App\Http\Resources\Status\StatusResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'title'  => $this->task_title . '|' .  $this->customer->name . '|' .  $this->employee->name . '|' . $this->departmentTask->name,
            'start'  => $this->execution_date,
            'end'   => $this->execution_end_date,
            'className' =>'bg-warning text-white',
//            'className' => $this->status->color,
            'execution_date'     => $this->execution_date,
            'execution_end_date' => $this->execution_end_date,
            "notification_date"  => $this->notification_date,
            "execution_duration" => $this->execution_duration,
            "contact_phone"      => $this->contact_phone,
            "contact_person"     => $this->contact_person,
            "note"               => $this->note,
            "task_title"         => $this->task_title,
            'department_id'      => $this->department_id,
            'employee_id'        => $this->employee_id,
            "customer_id"        => $this->customer_id,
            'department_task_id' => $this->department_task_id,
            "status_id"          => $this->status_id,
            "start_time"         => $this->start_time,
            "end_time"           => $this->end_time,
            "department_task" => new DepertmentTaskResource($this->departmentTask),
            "department" => new DepertmentResource($this->department),
            "employee" => new EmployeeResource($this->employee),
            "customer" => new GeneralCustomerResource($this->customer),
            "status" => new StatusResource($this->status),
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,
            "owners"        => $this->owners,
            "supervisors"   => EmployeeResource::collection($this->supervisors),
            'attentions' => EmployeeResource::collection($this->attentions),
            "notifications" => $this->notifications,
            'location' => new LocationResource($this->location),
            'priority' => new PriorityResource($this->priority),

        ];
    }
}
