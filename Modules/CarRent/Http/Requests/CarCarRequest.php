<?php

namespace Modules\CarRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarCarRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'plate_number' => 'nullable|max:50|string|unique:car_cars,plate_number' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'brand_id' => 'nullable|exists:general_brands,id',
            'model_id' => 'nullable|exists:car_models,id',
            'year_manufacture' => 'nullable|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'chassis_number' => 'nullable|max:50|string|unique:car_cars,chassis_number' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'transmission_id' => 'nullable|exists:car_transmissions,id',
            'sunroof_id' => 'nullable|exists:car_sunroofs,id',
            'body_type_id' => 'nullable|exists:car_body_types,id',
            'cylinders' => 'nullable|integer|max_digits:2',
            'country_id' => 'nullable|exists:general_countries,id',
            'color_exterior_id' => 'nullable|exists:general_colors,id',
            'fuel_type_id' => 'nullable|exists:car_fuel_types,id',
            'color_interior_id' => 'nullable|exists:general_colors,id',
            'seats_material_id' => 'nullable|exists:car_seats_materials,id',
            'passengers' => 'nullable|integer|max_digits:1',
            'financial_value' => 'nullable',
            'current_odometer' => 'nullable|integer|max_digits:11',
            'specifications' => 'nullable|array',
            'depreciation_percentage' => 'nullable',
            'barcode' => 'nullable|string|max:50',
            'jpscode' => 'nullable|string|max:50',
            "media" => "nullable|array",
            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],

        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
