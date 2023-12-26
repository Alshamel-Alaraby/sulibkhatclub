<?php

namespace Modules\HMS\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HMSPatientHealthHistoryResource extends JsonResource
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
            "id" => $this->id,
            "title" => $this->title,
            "title_e" => $this->title_e,
            "note" => $this->note,
            "patient_id" => $this->patient_id,
            "created_at" => $this->created_at->format('Y-m-d (H:i)'),
        ];
    }
}
