<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstUnitRequest extends FormRequest
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
            'code' => "required|string|max:20",
            'name' => "required|string|max:100",
            'name_e' => "required|string|max:100",
            'description' => "nullable|string",
            'description_e' => "nullable|string",
            'unit_ty' => "nullable|integer",
            'unit_status_id' => "nullable|integer|exists:rlst_unit_statuses,id",
            'unit_area' => "nullable|numeric",
            'unit_net_area' => "nullable|numeric",
            'building_id' => "nullable|integer|exists:rlst_buildings,id",
            'rooms' => "nullable|integer",
            'path' => "nullable|integer",
            'view' => "nullable|integer",
            'floor' => "nullable|integer",
            'finishing' => "nullable|integer",
            'properties' => "required|array",
            'module' => "required|string",
            "media" => "nullable|array",
            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],
            // 'attachments' => "required|array",

        ];
    }


}
