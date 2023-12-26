<?php

namespace Modules\HMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HMSSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'interval' =>'required|min:1|:max 60',
            'is_there_insurance' =>'required|boolean',
            'taxes' =>'required|numeric|min:0|:max:100',
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
