<?php

namespace Modules\BoardsRent\Transformers;

use App\Http\Resources\Avenue\AvenueResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\Governorate\GovernorateResource;
use App\Http\Resources\StreetResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Archiving\Transformers\DepartmentResource;
use Modules\BoardsRent\Transformers\UnitStatusResource;

class PanelResource extends JsonResource
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
            "price" => $this->price,
            "code" => $this->code,
            "new_code" => $this->new_code,
            "size" => $this->size,
            "note" => $this->note,
            'face' => $this->face,
            'unit_status_id' => $this->unit_status_id,
            "category_id" => $this->category_id,
            'governorate_id' => $this->governorate_id,
            "city_id" => $this->city_id,
            "country_id" => $this->country_id,
            'avenue_id' => $this->avenue_id,
            "street_id" => $this->street_id,
            'unit_status' => new UnitStatusResource($this->unitStatus),
            'category' => new CategoryResource($this->category),
            "country" => new CountryResource($this->country),
            "governorate" => new GovernorateResource($this->governorate),
            'city' => new CityResource($this->city),
            'avenue' => new AvenueResource($this->avenue),
            'street' => new StreetResource($this->street),
            'description' => $this->description,
            'description_e' => $this->description_e,
            'lat' => $this->lat,
            'lng' => $this->lng,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
            'itemBreakDowns' => $this->itemBreakDowns,


        ];
    }
}
