<?php

namespace Modules\HMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HMSPatientRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'name_e' => 'required',
            'date_of_birth' => 'nullable|date|before:today',
            'type' => 'required|in:inpatient,outpatient',
            'safe_number' => 'nullable',
            'email' => [request()->type == 'inpatient' ?'required' :
             'nullable','email',Rule::unique('h_m_s_patients','email')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->patient->id : 0,'id'),
             Rule::unique('general_users','email')->whereNull('deleted_at')->ignore(($this->method() == 'PUT' ? ($this->patient->type == 'inpatient' ? $this->patient->user_id : 0) : 0),'id')],
            'mobile' => 'required',
            'whatsapp' => 'nullable',
            'patient_weight' => 'nullable',
            'patient_height' => 'nullable',
            'address' => 'nullable|max:191',
            'image' => 'nullable|mimes:png,jpg,webp,gif,jpeg',
            'gender' => 'nullable|in:Male,Female',
            'blood' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
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
