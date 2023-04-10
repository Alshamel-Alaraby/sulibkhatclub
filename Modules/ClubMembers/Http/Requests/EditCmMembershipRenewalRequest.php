<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditCmMembershipRenewalRequest extends FormRequest
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

         // required|date_format:m-d|unique:customers,email_address


        return [

            'from'      => 'required|date_format:d-m|unique:cm_memberships_renewals,from' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'to'      => 'required|date_format:d-m|after:from|unique:cm_memberships_renewals,to' . ($this->method() == 'PUT' ? ',' . $this->id : ''),

            'membership_availability'    => 'required|in:0,1' ,
            'membership_cost'      => 'required|regex:/^\d+(\.\d{1,2})?$/' ,

            'renewal_availability'    => 'required|in:0,1' ,
            'renewal_cost'      => 'required|regex:/^\d+(\.\d{1,2})?$/',

        ];

    }



}

