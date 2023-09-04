<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Depertment\DepertmentResource;
use App\Http\Resources\Depertment\RelationDepertmentResource;
use App\Http\Resources\RestartPeriod\RelationRestartPeriodResource;

class PeriodicMaintenanceResource extends JsonResource
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
            'date' => $this->date,
            "task" => new TaskResource($this->task),
            "department" => new RelationDepertmentResource($this->department),
            "restart_period_id" => $this->restart_period_id,
            "restart_period" => new RelationRestartPeriodResource($this->restartPeriod),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
