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
        if ($this->method() == 'PUT') {
            return [
                'name' => 'required|string|max:255|unique:boards_rent_packages,name,' . $this->id,
                'name_e' => 'required|string|max:255|unique:boards_rent_packages,name_e,' . $this->id,
                'code'  => 'required|string|max:255|unique:boards_rent_packages,code,' . $this->id,
                'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            ];
        }

        return [
            'name'  => 'required|string|max:255|unique:boards_rent_packages,name',
            'name_e' => 'required|string|max:255|unique:boards_rent_packages,name_e',
            'code'  => 'required|string|max:255|unique:boards_rent_packages,code',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
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
