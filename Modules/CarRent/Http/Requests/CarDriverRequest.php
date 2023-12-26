<?php

namespace Modules\CarRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarDriverRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'employee_id' => 'required|exists:general_employees,id|unique:car_drivers,employee_id' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'driving_license' => 'required|string|max:50|unique:car_drivers,driving_license' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'license_exp_date' => 'required|date',
            'daily_price' => 'required',
            'weekly_price' => 'required',
            'monthly_price' => 'required',
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
