<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CmMemberRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'second_name' => 'required|string|max:255',
            'third_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'family_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'national_id' => ['required', 'string', new \App\Rules\NationalIdRule()],
            'nationality_class' => 'required|string|max:255',
            'phone_code' => 'required|string|max:255',
            'home_phone' => 'required|string|max:255',
            'work_phone' => 'required|string|max:255',
            'home_address' => 'required|string|max:255',
            'work_address' => 'required|string|max:255',
            'membership_date' => 'nullable|date',
            'membership_number' => 'nullable|string|max:255',
            'job' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'acceptance' => 'nullable|in:0,1,2',
            'session_date' => 'nullable|date',
            'session_number' => 'required|string|max:255',
            'applying_date' => 'required|date',
            'applying_number' => 'nullable|string|max:255|unique:cm_members,applying_number' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'status_id' => 'nullable|exists:general_statuses,id',
            'sponsor_id' => 'nullable|exists:cm_sponsers,id',
            'member_type_id' => 'nullable|exists:cm_members_types,id',
            'financial_status_id' => 'nullable|exists:cm_financial_status,id',
            'member_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
            'gender' => 'nullable|in:0,1',
            'financial_year_id' => 'required|exists:general_financial_years,id',
        ];
    }
}
