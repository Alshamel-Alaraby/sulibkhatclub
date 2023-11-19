<?php

namespace Modules\HR\Http\Requests;

use App\Rules\UniqueHolidayDetail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeePublicHolidayDetailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
       
        return [
            'employee_public_holiday_header_id' => ['required', 'exists:hr_employee_public_holiday_headers,id'],
            'employee_ids' => 'required|array',
            'employee_ids.*' => [
                'required_with:employee_ids',
                'exists:general_employees,id',
                new UniqueHolidayDetail(request('employee_public_holiday_header_id'), request('employee_ids')),
            ],
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
