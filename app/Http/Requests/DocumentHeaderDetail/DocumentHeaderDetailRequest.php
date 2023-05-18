<?php

namespace App\Http\Requests\DocumentHeaderDetail;

use Illuminate\Foundation\Http\FormRequest;

class DocumentHeaderDetailRequest extends FormRequest
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
            'document_header_id' => 'required|integer|exists:general_document_headers,id',
            'category_id' => 'nullable',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'governorate_id' => 'required|integer|exists:general_governorates,id',
            'quantiy' => 'required|numeric|integer',
            'price_per_uint' => 'required',
            'total' => 'required',
            'unit_type' => 'required|string',
            'is_stripe'  => 'nullable|in:1,0',
        ];

    }
}
