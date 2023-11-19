<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstGuardRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:100|unique:rlst_guards,name,'.($this->method() == 'PUT' ? $this->id : ''),
            'name_e' => 'required|string|max:100|unique:rlst_guards,name_e,'.($this->method() == 'PUT' ? $this->id : ''),
            'building_id' => 'required|integer|exists:rlst_buildings,id',
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
