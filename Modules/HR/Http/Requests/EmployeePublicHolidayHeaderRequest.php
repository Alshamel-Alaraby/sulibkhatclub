<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class EmployeePublicHolidayHeaderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'vdate'=>'required|date',
            'public_holiday_header_id' => ['required',
            'exists:hr_public_holiday_headers,id',
            Rule::unique('hr_employee_public_holiday_headers')->where(function ($query) {
                return $query->where('vdate', request('vdate'));
            }),
            ],
            // 'employees' => 'array',
            // 'employees.*' => 'exists:general_employees,id',
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
