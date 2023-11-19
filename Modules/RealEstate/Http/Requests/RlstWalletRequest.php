<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstWalletRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

            'name' => 'required|string|max:100|unique:rlst_wallets,name,'. ($this->method() == 'PUT' ?  $this->id : '').',id,deleted_at,NULL',
            'name_e' => 'required|string|max:100|unique:rlst_wallets,name_e,'. ($this->method() == 'PUT' ?  $this->id : '').',id,deleted_at,NULL',
            'company_id' =>'nullable',

        ];
    }


}
