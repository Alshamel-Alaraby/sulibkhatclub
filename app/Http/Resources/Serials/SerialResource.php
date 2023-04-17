<?php

namespace App\Http\Resources\Serials;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Document\DocumentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SerialResource extends JsonResource
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
            'name' => $this->name,
            'name_e' => $this->start_no,
            'company_id' => $this->name_e,
            'start_no' => $this->start_no,
            "document" => new DocumentResource($this->document),
            "branch" => new BranchResource($this->branch),
            'perfix' => $this->perfix,
            'suffix' => $this->suffix,
            'restart_period' => $this->restart_period,
            'is_default' => $this->is_default,
            "store" => $this->store,
            "has_child" => $this->has_child,
        ];
    }
}
