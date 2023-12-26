<?php

namespace Modules\HMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HMSPatientHealthHistoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required' , ],//
            'title_e' => ['required' , ],//Rule::unique('h_m_s_patient_health_histories')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->patient_health_history->id : 0,'id')
            'patient_id' => 'required|exists:h_m_s_patients,id',
            'note' => 'nullable',
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
