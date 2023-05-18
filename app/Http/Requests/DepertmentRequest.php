<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepertmentRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('general_departments')->ignore($this->id)],
            'name_e' => ['required', 'string', 'max:255', Rule::unique('general_departments')->ignore($this->id)],
            'supervisors' => ['required', 'array'],
            'attentions' => ['required', 'array'],
            'supervisors.*'
            => ['required', 'exists:general_employees,id'],
            'attentions.*'
            => ['required', 'exists:general_employees,id'],

        ];
    }
}
