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
            'first_name' => 'sometimes|string|max:255',
            'second_name' =>'sometimes|string|max:255',
            'third_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'family_name' => 'sometimes|string|max:255',
            'birth_date' => 'nullable|date',
            'national_id' =>'nullable|string|max:20',
            'nationality_number' => 'nullable|string|max:255',
            'home_phone' => 'nullable|string|max:255',
            'work_phone' => 'nullable|string|max:255',
            'home_address' => 'nullable|string|max:255',
            'work_address' => 'nullable|string|max:255',
            'membership_date' =>'nullable|date',
            'membership_number' =>  'nullable|string|max:255|unique:cm_members,membership_number' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'job' =>  'nullable|string|max:255',
            'degree' =>  'nullable|string|max:255',
            'acceptance' => 'nullable|in:0,1,2',
            'acceptance_date'=>'nullable|date',
            'acceptance_number'=>'nullable|string|max:255',
            'applying_date' => 'required|date',
            'applying_number' =>  'required|string|max:255|unique:cm_members,applying_number' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'status_id' => 'nullable|exists:general_statuses,id',
            'sponsor_id' => 'nullable|exists:cm_sponsers,id',
            'member_type_id' => 'nullable|exists:cm_members_types,id',
            'financial_status_id' => 'nullable|exists:cm_financial_status,id',
            'member_type'=>'nullable|string|max:255',
            'notes'=>'nullable|string|max:255',
            'gender'=> 'nullable|in:0,1',
        ];
    }
}

