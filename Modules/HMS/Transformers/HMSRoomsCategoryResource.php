<?php

namespace Modules\HMS\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HMSRoomsCategoryResource extends JsonResource
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
            'name_e' => $this->name_e,
            'parent_id' => $this->parent_id,
            'parent' => [
                'id' =>  $this->parent ? $this->parent->id:'',
                'name' =>  $this->parent ? $this->parent->name:'',
                'name_e' =>  $this->parent ? $this->parent->name_e:'',
                'does_it_have_schedule' =>  $this->parent ? $this->parent->does_it_have_schedule:'',
            ],
            'children' => $this->when(!$this->parent_id,HMSRoomsCategoryResource::collection($this->children)),
            'is_default' =>(int) $this->is_default,
            'does_it_have_schedule' => (int) $this->does_it_have_schedule,
        ];
    }
}
