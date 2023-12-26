<?php

namespace Modules\HMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HMSPrescriptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'doctor_id' => 'required|exists:h_m_s_doctors,id',
            'patient_id' => 'required|exists:h_m_s_patients,id',
            'appointment_id' => 'nullable|exists:h_m_s_appointments,id',
            'diagnosis_test_list' => 'nullable',
            'diagnosis_test_list.*.diagnosis_test_id' => ['nullable','exists:h_m_s_diagnosis_tests,id', ],
            'drug_list' => 'nullable',
            'drug_list.*.drug_id' => ['nullable','exists:h_m_s_drugs,id', ],
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
