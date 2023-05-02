<?php

namespace Modules\BoardsRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:boards_rent_packages,name,' .
            ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'name_e' => 'required|string|max:255|unique:boards_rent_packages,name_e,' .
            ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'code' => 'required|string|max:255|unique:boards_rent_packages,code,' .
            ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            "panels" => "required|array",
            "panels.*" => "required_with:panels|exists:boards_rent_panels,id",
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
