<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BranchRequest extends FormRequest
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
            'id' => 'nullable',
            "name" => "nullable|unique:general_branches,name,". ($this->method() == 'PUT' ?  $this->branch : ''),
            "name_e" => "nullable|unique:general_branches,name_e,". ($this->method() == 'PUT' ?  $this->branch : ''),
            "country_id" => "nullable|exists:general_countries,id",
            "is_active"=>"nullable",
            "parent_id"=>"nullable",
        ];
    }

}
