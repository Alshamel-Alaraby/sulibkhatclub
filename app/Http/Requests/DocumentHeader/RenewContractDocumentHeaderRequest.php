<?php

namespace App\Http\Requests\DocumentHeader;

use Illuminate\Foundation\Http\FormRequest;

class RenewContractDocumentHeaderRequest extends FormRequest
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
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'ids'   => 'required|array|min:1',
            'ids.*'   => 'exists:general_document_headers,id',

        ];
    }
}
