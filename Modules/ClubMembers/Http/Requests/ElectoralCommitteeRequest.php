<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ElectoralCommitteeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'      => 'required|string|max:100|unique:cm_electoral_committees,name' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'name_e'    => 'required|string|max:100|unique:cm_electoral_committees,name_e' . ($this->method() == 'PUT' ? ','. $this->id : '') ,
            "number_of_individuals"=>'required|integer',
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
