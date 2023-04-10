<?php

namespace App\Http\Resources;

use App\Http\Resources\Avenue\AvenueResource;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\Governorate\GovernorateResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BoardsRent\Transformers\CustomerResource;

class CustomerBranchResource extends JsonResource
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
            "customer" => new CustomerResource($this->customer),
            "country" => new CountryResource($this->country),
            "city" => new CityResource($this->city),
            "governorate" => new GovernorateResource($this->governorate),
            "avenue" => new AvenueResource($this->avenue),
            "street" => new StreetResource($this->street),
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
