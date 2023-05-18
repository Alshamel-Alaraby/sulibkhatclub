<?php

namespace App\Http\Resources\Employee;

use App\Http\Resources\Depertment\DepertmentResource;
use App\Http\Resources\SalesmenPlanResource;
use App\Http\Resources\SalesmenType\SalesmenTypeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'name' => $this->name,
            'name_e' => $this->name_e,
            'salesman_type' => new SalesmenTypeResource($this->salesmanType),
            'department' => new DepertmentResource($this->depertment),
            'is_salesman' => $this->is_salesman,
            'customer_handel' => $this->customer_handel,
            'manager' => new EmployeeResource($this->manager),
            'plans' => SalesmenPlanResource::collection($this->plans),
            'job_title'=>$this->jobTitle,
            'branch_id'=>$this->branch,
            'manage_others'=>$this->manage_others,
            'department_id' =>$this->department_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
