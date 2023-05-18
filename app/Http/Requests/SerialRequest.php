<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SerialRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'company_id' => 'nullable',
            'restart_period_id' => "nullable|exists:general_restart_period,id",
            'is_default' => 'nullable',
            'start_no' => 'nullable',
            'perfix' => 'nullable',
            'suffix' => 'nullable',
            'document_id' => "nullable|exists:general_documents,id",
            'branch_id' => "nullable|exists:general_branches,id",
            "name" => "nullable|max:151|string",
            "name_e" => "nullable|max:151|string",
        ];
    }
}
