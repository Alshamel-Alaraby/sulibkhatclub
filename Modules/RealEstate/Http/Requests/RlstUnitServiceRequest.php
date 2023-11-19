<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\RealEstate\Rules\UniqueUnitService;

class RlstUnitServiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
                "unit-service" => 'required|array',
                "unit-service.*.unit_id" => ['required','integer','exists:rlst_units,id'],
                "unit-service.*.service_id" => ['required','integer','exists:rlst_services,id', new UniqueUnitService($this->id)],
                "unit-service.*.company_id"=>'nullable',
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
