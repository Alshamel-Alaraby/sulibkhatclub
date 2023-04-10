<?php

namespace Modules\RealEstate\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class RlstBuildingRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'name_e' => 'required|string|max:255',
            'description' => "nullable",
            'description_e' => "nullable",
            'land_area' => "nullable",
            'building_area' => ['lt:land_area'],
            'construction_year' => ['gt:1980'],
            'country_id' => "required|exists:general_countries,id",
            'city_id' => "required|exists:general_cities,id",
            'avenue_id' => "required|exists:general_avenues,id",
            'lng' => "numeric|required_with:lat",
            'lat' => "numeric|required_with:lng",
            'properties' => "nullable|array",
            'attachments' => "nullable|array",
            'module' => "required",
        ];
    }


}
