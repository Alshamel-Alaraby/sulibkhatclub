<?php

namespace App\Http\Requests\WorkNature;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkNatureRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required' , Rule::unique('general_work_natures')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->work_nature->id : 0,'id')],
            'name_e' => ['required' , Rule::unique('general_work_natures')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->work_nature->id : 0,'id')]
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
