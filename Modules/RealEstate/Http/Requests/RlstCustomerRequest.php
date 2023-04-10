<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstCustomerRequest extends FormRequest
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
            'email' => 'required|string|max:100',
            'country_id' => 'required|integer|exists:general_countries,id',
            'city_id' => 'required|integer|exists:general_cities,id',
            'rb_code' => 'required|string|max:255',
            'nationality_id' => 'required|integer|exists:general_countries,id',
            "bank_account_id" => "required|integer|exists:general_bank_accounts,id",
            "contact_person" => "required|string|max:100",
            "contact_phones" => "required|string|max:100",
            "national_id" => "required|string|max:20",
            "passport_no" => "required|string|max:20",
            "note1" => 'required|max:100',
            "note2" => 'required|max:100',
            "note3" => 'required|max:100',
            "note4" => 'required|max:100',
            "whatsapp" => "required|string|max:20",
            // "categories" => "required",
            // "attachments" => 'required',
        ];
    }


}
