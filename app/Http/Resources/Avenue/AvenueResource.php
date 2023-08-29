<?php

namespace App\Http\Resources\Avenue;

use App\Http\Resources\City\CityResource;
use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\Governorate\GovernorateResource;
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
            'city'=>collect($this->whenLoaded('city'))->only(['id', 'name', 'name_e']),
            'governorate'=>collect($this->whenLoaded('governorate'))->only(['id', 'name', 'name_e']),
            'country'=>collect($this->whenLoaded('country'))->only(['id', 'name', 'name_e']),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
