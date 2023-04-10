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
            'birth_date' => 'sometimes|date',
            'national_id' =>'sometimes|string|max:20',
            'nationality_number' => 'sometimes|string|max:255',
            'home_phone' => 'sometimes|string|max:255',
            'work_phone' => 'sometimes|string|max:255',
            'home_address' => 'sometimes|string|max:255',
            'work_address' => 'sometimes|string|max:255',
            'membership_date' =>'sometimes|date',
            'membership_number' =>  'nullable|string|max:255',
            'membership_request_number' => 'sometimes|string|max:255',
            'job' =>  'sometimes|string|max:255',
            'degree' =>  'sometimes|string|max:255',
            'acceptance' => 'sometimes|in:0,1,2',
            'acceptance_date'=>'sometimes|date',
            'acceptance_number'=>'sometimes|string|max:255',
            'session_date' => 'sometimes|date',
            'session_number' =>  'sometimes|string|max:255',
            'sponsor' => 'sometimes|in:active,inactive',
            'status_id' => 'sometimes|exists:general_statuses,id',
            'sponsor_id' => 'nullable|exists:cm_sponsers,id',
            'member_type_id' => 'nullable|exists:cm_members_types,id',
            'financial_status_id' => 'nullable|exists:cm_financial_status,id',
            'member_type'=>'nullable|string|max:255',
        ];
    }
}

