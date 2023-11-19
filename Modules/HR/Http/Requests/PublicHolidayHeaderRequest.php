<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublicHolidayHeaderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'name' => 'required|string|max:50',
            'name_e' => 'required|string|max:50',

            'public_holiday_details' => 'required|array',
            'public_holiday_details.*.vdate' => 'required|date|distinct:public_holiday_details.*.vdate',

            // 'public_holiday_details.*.public_holiday_header_id' =>
            // ['exists:hr_public_holiday_headers,id','distinct:'
            // ],

            'public_holiday_details.*.name' => 'required|string|max:50',
            'public_holiday_details.*.name_e' => 'required|string|max:50',
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
