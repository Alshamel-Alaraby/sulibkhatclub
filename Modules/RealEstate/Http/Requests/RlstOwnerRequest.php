<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstOwnerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'name_e' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|string|max:100',
            'country_id' => 'required|integer|exists:general_countries,id',
            'city_id' => 'required|integer|exists:general_cities,id',
            'rb_code' => 'required|string|max:255',
            'phone_code' => 'required|string|max:255',
            'nationality_id' => 'required|integer|exists:general_countries,id',
            "bank_account_id" => "required|integer|exists:general_bank_accounts,id",
            "contact_person" => "nullable|string|max:100",
            "contact_phones" => "nullable|string|max:100",
            "national_id" => "required|string|max:20",
            "whatsapp" => "nullable|string|max:20",
            "categories" => "nullable|array",
            "attachments" => "nullable|array",
            "company_id"=>'nullable',
        ];
    }


}
