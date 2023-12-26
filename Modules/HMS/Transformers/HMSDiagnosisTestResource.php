<?php

namespace Modules\HMS\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HMSDiagnosisTestResource extends JsonResource
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
            'price' => $this->price,
            'comment' => $this->comment,
        ];
    }
}
