<?php

namespace App\Http\Resources\DepertmentTask;

use App\Http\Resources\Depertment\RelationDepertmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DepertmentTaskResource extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "name_e" => $this->name_e,
            "description" => $this->description,
            "description_e" => $this->description_e,
            "department_id" => $this->department_id,
            "estimate_task_duration" => $this->estimate_task_duration,
            // "depertment" => $this->depertment,
            'depertment' => new RelationDepertmentResource($this->depertment),
        ];
    }
}
