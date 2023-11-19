<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeLocationDateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'location_date_id' => 'required|exists:hr_location_dates,id',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'required_with:employee_ids|exists:general_employees,id',

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
