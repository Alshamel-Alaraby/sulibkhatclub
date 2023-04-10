<?php

namespace Modules\RealEstate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RlstBuildingResource extends JsonResource
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
             "name" => $this->name,
             "name_e" => $this->name_e,
             "description" => $this->description,
             "description_e" => $this->description_e,
             "land_area" => $this->land_area,
             "building_area" => $this->building_area,
             "construction_year" => $this->construction_year,
             "project_id" => $this->project_id,
             "country_id" => $this->country_id,
             "country" => new \App\Http\Resources\Country\CountryResource($this->country),
             "city_id" => $this->city_id,
             "city" => new \App\Http\Resources\City\CityResource($this->city),
             "avenue_id" => $this->avenue_id,
             "avenue" => new \App\Http\Resources\Avenue\AvenueResource($this->avenue),
             "lng" => $this->lng,
             "lat" => $this->lat,
             "properties" => $this->properties,
             "attachments" => $this->attachments,
             "module" => $this->module,
            "video_link" => $this->video_link,
             "created_at" => $this->created_at,
             "updated_at" => $this->updated_at,
        ];
    }
}
