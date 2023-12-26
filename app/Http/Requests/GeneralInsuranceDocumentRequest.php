<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralInsuranceDocumentRequest extends FormRequest
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
            'related_id' => 'required|integer',
            'insurance_type_id' => 'required|integer|exists:general_insurance_types,id',
            'insurance_number' => 'required|string|max:50',
            'insurance_company_id' => 'required|integer|exists:general_insurance_companies,id',
            'amount' => 'required',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'related_field' => 'nullable|string|max:50',
        ];
    }
}
