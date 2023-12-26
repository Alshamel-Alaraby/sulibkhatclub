<?php

namespace App\Http\Requests\InsuranceCompany;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InsuranceCompanyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required' , Rule::unique('general_insurance_companies')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->insurance_company->id : 0,'id')],
            'name_e' => ['required' , Rule::unique('general_insurance_companies')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->insurance_company->id : 0,'id')]
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
