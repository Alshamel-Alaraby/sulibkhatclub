<?php

namespace Modules\HR\Transformers;

use App\Http\Resources\AllDropListResource;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeFingerprintHeaderResource extends JsonResource
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
            'vdate' => $this->vdate,
            'has_fingerprint' => $this->has_fingerprint,
            'employees' => AllDropListResource::collection($this->employees),

        ];
    }
}
