<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepertmentRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "name" => ["nullable",Rule::unique('general_departments')->where(function($querry){
                $querry
                    ->where("company_id",request()->company_id);
            })],
            "name_e" => ["nullable",Rule::unique('general_departments')->where(function($querry){
                $querry
                    ->where("company_id",request()->company_id);
            })],
            "company_id" => "nullable",
        ];
    }
}
