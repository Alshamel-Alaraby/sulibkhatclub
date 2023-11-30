<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GlChartRequest extends FormRequest
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
            'name' => 'required|string|max:64|unique:gl_chart,name,' . ($this->method() == 'PUT' ?  $this->id : '') ,
            'name_e' => 'required|string|max:64|unique:gl_chart,name_e,' . ($this->method() == 'PUT' ?  $this->id : '') ,
            'account_number' => 'required|string|max:20|unique:gl_chart,account_number,' . ($this->method() == 'PUT' ?  $this->id : '') ,
            'parent_id' => 'sometimes|exists:gl_chart,id',
        ];
    }
}
