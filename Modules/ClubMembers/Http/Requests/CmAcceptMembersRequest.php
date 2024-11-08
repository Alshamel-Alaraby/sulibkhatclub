<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CmAcceptMembersRequest extends FormRequest
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
            "accept-members" => "required|array",
            "accept-members.*.id" => 'required',
            "accept-members.*.session_date" => 'required',
            "accept-members.*.session_number" => 'required',
            "accept-members.*.executive_office_date" => 'nullable',
            "accept-members.*.executive_office_number" => 'nullable',
            "accept-members.*.board_of_directors_date" => 'nullable',
            "accept-members.*.board_of_directors_number" => 'nullable',
            "accept-members.*.membership_date" => 'required',
        ];
    }
}


