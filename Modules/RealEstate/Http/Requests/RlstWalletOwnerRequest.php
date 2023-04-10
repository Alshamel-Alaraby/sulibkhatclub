<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstWalletOwnerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "wallet-owner" => 'required|array',
            'wallet-owner.*.wallet_id'         => 'required|integer|exists:rlst_wallets,id',
            'wallet-owner.*.owner_id'          => 'required|integer|exists:rlst_owners,id',
            'wallet-owner.*.percentage'        => 'required|numeric',
        ];
    }


}
