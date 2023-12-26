<?php

namespace Modules\HMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HMSServiceTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required' , Rule::unique('h_m_s_service_types')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->service_type->id : 0,'id')],
            'name_e' => ['required' , Rule::unique('h_m_s_service_types')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->service_type->id : 0,'id')],
            'price' => 'required|numeric',
            // 'price_after_sale' => 'nullable|numeric|lt:price',
            'specialty_id' => 'required|exists:h_m_s_specialties,id',
            'hours' => 'required|gte:0|lte:100',
            'minutes' => 'required|gte:0|lt:60',
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
