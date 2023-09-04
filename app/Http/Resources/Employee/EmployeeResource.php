<?php

namespace App\Http\Resources\Employee;

use App\Http\Resources\Branch\RelationBranchResource;
use App\Http\Resources\Depertment\RelationDepertmentResource;
use App\Http\Resources\SalesmenPlansSource\RelationSalesmenPlansSourceResource;
use App\Http\Resources\SalesmenType\RelationSalesmenTypeResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HR\Transformers\JobTitle\RelationJobTitleResource;

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
            'manager_id' => $this->manager_id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'is_salesman' => $this->is_salesman,
            'customer_handel' => $this->customer_handel,
            'department_id' => $this->department_id,
            'code_country' => $this->code_country,
            'salesman_type_id' => $this->salesman_type_id,
            'for_all_customer' => $this->for_all_customer,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'whatsapp' => $this->whatsapp,
            'company_id' => $this->company_id,
            'job_id' => $this->job_id,
            'manage_others' => $this->manage_others,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'job_title' => new RelationJobTitleResource($this->jobTitle),
            'branch_id' => $this->branch_id,
            'branch' => new RelationBranchResource($this->branch),
            'manager' => new RelationEmployeeResource($this->manager),
            'salesman_type' => new RelationSalesmenTypeResource($this->salesmanType) ,
            'department' => new RelationDepertmentResource($this->depertment) ,
            'plans' =>new RelationSalesmenPlansSourceResource($this->plans),


        ];
    }
}
