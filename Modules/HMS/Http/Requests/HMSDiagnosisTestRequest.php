<?php

namespace Modules\HMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HMSDiagnosisTestRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required' , Rule::unique('h_m_s_diagnosis_tests')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->diagnosis_test->id : 0,'id')],
            'name_e' => ['required' , Rule::unique('h_m_s_diagnosis_tests')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->diagnosis_test->id : 0,'id')],
            'price' => 'required|numeric',
            'comment' => 'nullable',
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
