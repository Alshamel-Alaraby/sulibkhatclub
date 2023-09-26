<?php

namespace Modules\RealEstate\Transformers;

use App\Http\Resources\FileResource;
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
            "id"                => $this->id,
            "name"              => $this->name,
            "name_e"            => $this->name_e,
            "description"       => $this->description,
            "description_e"     => $this->description_e,
            "land_area"         => $this->land_area,
            "building_area"     => $this->building_area,
            "construction_year" => $this->construction_year,
            "project_id"        => $this->project_id,
            "country_id"        => $this->country_id,
            "street_id"         => $this->street_id,
            "country"           => $this->whenLoaded('country'),
            "city_id"           => $this->city_id,
            "city"              => $this->whenLoaded('city'),
            "avenue_id"         => $this->avenue_id,
            "avenue"            => $this->whenLoaded('avenue'),
            "lng"               => $this->lng,
            "lat"               => $this->lat,
            "properties"        => $this->properties,
            "attachments"       => $this->attachments,
            "module"            => $this->module,
            "video_link"        => $this->video_link,
            "media"             => isset($this->files) ? FileResource::collection($this->files) : null,
        ];
    }
}
