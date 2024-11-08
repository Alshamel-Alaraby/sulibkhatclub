<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralInsuranceTypeRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|max:50|string|unique:general_insurance_types,name' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'name_e' => 'required|max:50|string|unique:general_insurance_types,name_e' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'company_id' => 'nullable',
        ];
    }
}
