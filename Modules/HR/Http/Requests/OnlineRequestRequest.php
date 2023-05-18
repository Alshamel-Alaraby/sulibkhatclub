<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OnlineRequestRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'request_type_id'=> 'nullable|exists:hr_requests_types,id',
            'employee_id'=>'nullable|exists:general_employees,id',
            'start_from'=> 'required|date',
            'end_date'=>'required|date',
            'descriptions'=>'required|string|max:255',
            'amount'=>'required',
            'from_hour'=>'required',
            'to_hour'=>'required',
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
