<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
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
            'name'       => 'nullable',
            'name_e'     => 'nullable',
            'is_default' => 'nullable|integer',
            'attributes' => "nullable|array",
            'branche_id' => "nullable",
            'serial_id' => "nullable",
            "contusion" => "nullable|in:true,false",
            'company_id' => 'nullable|integer',
            'document_relateds.*' => 'nullable|integer',
        ];
    }
}
