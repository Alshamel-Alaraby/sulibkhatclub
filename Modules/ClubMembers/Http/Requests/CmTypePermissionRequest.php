<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CmTypePermissionRequest extends FormRequest
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
        if ($this->method() == 'PUT') {
            return [
                'cm_members_type_id' => 'required|exists:cm_members_types,id',
                'cm_permissions_id' => 'required|exists:cm_members_permissions,id',
                'cm_financial_status_id' => 'required|exists:cm_financial_status,id',
                'membership_period' => 'required|numeric',
                'allowed_subscription_date' => 'required',
                'allowed_vote_date' => 'nullable',
                "company_id" => 'nullable',
            ];
        }

        return [
            'data' => 'required|array',
            'data.*.cm_members_type_id' => 'required|exists:cm_members_types,id',
            'data.*.cm_permissions_id' => 'required|exists:cm_members_permissions,id',
            'data.*.cm_financial_status_id' => 'required|exists:cm_financial_status,id',
            'data.*.membership_period' => 'required|numeric',
            'data.*.allowed_subscription_date' => 'required',
            'data.*.allowed_vote_date' => 'nullable',
            "data.*.company_id" => 'nullable',
        ];
    }

}
