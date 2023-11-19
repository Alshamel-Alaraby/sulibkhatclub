<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
