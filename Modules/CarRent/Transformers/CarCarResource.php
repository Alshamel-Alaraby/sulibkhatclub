<?php

namespace Modules\CarRent\Transformers;

use App\Http\Resources\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CarCarResource extends JsonResource
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
            'plate_number' => $this->plate_number,
            'brand_id' => $this->brand_id,
            'model_id' => $this->model_id,
            'year_manufacture' => $this->year_manufacture,
            'chassis_number' => $this->chassis_number,
            'transmission_id' => $this->transmission_id,
            'sunroof_id' => $this->sunroof_id,
            'body_type_id' => $this->body_type_id,
            'cylinders' => $this->cylinders,
            'country_id' => $this->country_id,
            'color_exterior_id' => $this->color_exterior_id,
            'fuel_type_id' => $this->fuel_type_id,
            'color_interior_id' => $this->color_interior_id,
            'seats_material_id' => $this->seats_material_id,
            'passengers' => $this->passengers,
            'financial_value' => $this->financial_value,
            'current_odometer' => $this->current_odometer,
            'specifications' => $this->specifications,
            'depreciation_percentage' => $this->depreciation_percentage,
            'barcode' => $this->barcode,
            'jpscode' => $this->jpscode,
            'daily_price' => $this->daily_price,
            'weekly_price' => $this->weekly_price,
            'monthly_price' => $this->monthly_price,
            'brand' => $this->brand,
            'model' => $this->model,
            'transmission' => $this->transmission,
            'sunroof' => $this->sunroof,
            'body_type' => $this->body_type,
            'country' => $this->country,
            'exterior_color' => $this->exterior_color,
            'interior_color' => $this->interior_color,
            'fuel_type' => $this->fuel_type,
            'seats_material' => $this->seats_material,
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,

        ];
    }
}
