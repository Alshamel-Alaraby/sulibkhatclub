<?php

namespace Modules\CarRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarTransmissionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:50|string|unique:car_transmissions,name' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'name_e' => 'required|max:50|string|unique:car_transmissions,name_e' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'company_id' => 'nullable',

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
