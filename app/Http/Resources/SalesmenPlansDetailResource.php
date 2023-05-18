<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesmenPlansDetailResource extends JsonResource
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
            'plan' => new SalesmenPlanResource($this->plan),
            'amount_from' => $this->amount_from,
            'amount_to' => $this->amount_to,
            'commission_percent' => $this->commission_percent,
            'is_default' => $this->is_default,
            'created_at' => $this->created_at,
            'created_at' => $this->updated_at,
        ];
    }
}
