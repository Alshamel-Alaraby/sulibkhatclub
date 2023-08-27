<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstBuildingWalletRequest extends FormRequest
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
            'wallet_id' => [
                'required', 'integer'
                // ,'exists:rlst_wallets,id,deleted_at,null'
            ],
            'building_id' => [
                'required', 'integer'
                // ,'exists:rlst_wallets,id,deleted_at,null'
            ],
            'bu_ty' => 'required|in:1,2',
            "company_id"=>'nullable',
        ];
    }

}
