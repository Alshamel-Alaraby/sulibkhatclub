<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstBuildingCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:50|unique:rlst_building_categories,name,'.($this->method() == 'PUT' ? $this->id : ''),
            'name_e' => 'required|string|max:50|unique:rlst_building_categories,name_e,'.($this->method() == 'PUT' ? $this->id : ''),
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
