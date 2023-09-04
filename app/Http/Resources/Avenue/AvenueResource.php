<?php

namespace App\Http\Resources\Avenue;

use App\Http\Resources\City\RelationCityResource;
use App\Http\Resources\Country\RelationCountryResource;
use App\Http\Resources\Governorate\RelationGovernorateResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AvenueResource extends JsonResource
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
            'is_active' => $this->is_active,
            'city' => new RelationCityResource($this->city),
            'governorate' => new RelationGovernorateResource($this->governorate),
            'country' =>new RelationCountryResource($this->country),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
