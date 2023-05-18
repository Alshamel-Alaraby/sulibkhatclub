<?php

namespace App\Http\Requests\FinancialYear;

use Illuminate\Foundation\Http\FormRequest;

class FinancialYearRequest extends FormRequest
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
            'name' => 'nullable|string|max:255|unique:general_financial_years,name,' . ($this->method() == 'PUT' ? $this->id : ''),
            'name_e' => 'nullable|string|max:255|unique:general_financial_years,name_e,' . ($this->method() == 'PUT' ? $this->id : ''),
            "start_date" => 'nullable|date_format:Y-m-d|after_or_equal:today|unique:general_financial_years,start_date,' . ($this->method() == 'PUT' ? $this->id : ''),
            "end_date" => 'nullable|date_format:Y-m-d|after_or_equal:start_date|unique:general_financial_years,end_date,' . ($this->method() == 'PUT' ? $this->id : ''),
            "is_active" => 'nullable',
        ];
    }

}
