<?php

namespace Modules\HMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HMSDrugRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'trade_name' => ['required' , Rule::unique('h_m_s_drugs')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->drug->id : 0,'id')],
            'trade_name_e' => ['required' , Rule::unique('h_m_s_drugs')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->drug->id : 0,'id')],
            'generic_name' => ['required' , Rule::unique('h_m_s_drugs')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->drug->id : 0,'id')],
            'generic_name_e' => ['required' , Rule::unique('h_m_s_drugs')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->drug->id : 0,'id')],
            'note' => 'nullable',
            'sell_price' => 'required|numeric|gt:0',
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
