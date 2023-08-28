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
            'name' => 'nullable',
            'name_e' => 'nullable',
            'document_detail_type' => "nullable|in:normal,rent_unit,sell_unit,board_rent,document_money,booking",
            'is_default' => "nullable|in:1,0",
            'attributes' => "nullable|array",
            'branche_id' => "nullable",
            'serial_id' => "nullable",
            "contusion" => "nullable|in:1,0",
            "is_copy" => "nullable|in:1,0",
            'company_id' => 'nullable|integer',
            'document_relateds.*' => 'nullable|integer',
            'required' => 'nullable',
            'need_approve' => 'nullable',
        ];
    }
}
