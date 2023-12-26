<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\RealEstate\Rules\UniqueMobileNumber;

class RlstTenantRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:100|unique:rlst_tenants,name,'.($this->method() == 'PUT' ? $this->id : ''),
            'name_e' => 'required|string|max:100|unique:rlst_tenants,name_e,'.($this->method() == 'PUT' ? $this->id : ''),
            'blacklisted' => 'sometimes|in:yes,no',
            'is_whatsapp' => 'required|in:yes,no',
            'gender' => 'sometimes|in:m,f',
            'marital_status' => 'required|in:married,single,divorced,widowed',
            'country_id' => 'required|exists:general_countries,id',
            'work_address' => 'required|string|max:255',
            'home_address' => 'required|string|max:255',
            'civil_no' => 'required|string|max:20|unique:rlst_tenants,civil_no,'.($this->method() == 'PUT' ? $this->id : ''),
            'passport_no' => 'required|string|max:20|unique:rlst_tenants,passport_no,'.($this->method() == 'PUT' ? $this->id : ''),
            'residence_no' => 'required|string|max:20|unique:rlst_tenants,residence_no,'.($this->method() == 'PUT' ? $this->id : ''),
            'chart_id' => 'required|exists:gl_chart,id',
            'tenant_type_id' => 'required|exists:rlst_tenant_types,id',
            'mobile_code' => 'required|string|max:5',
            'mobile' => ['required','string','max:20', new UniqueMobileNumber($this->id)],
            'email' => 'required|email|max:100|unique:rlst_tenants,email,'.($this->method() == 'PUT' ? $this->id : ''),


        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
