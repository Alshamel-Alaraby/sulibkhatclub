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
                "unit_ids"      => 'required|array',
                "service_ids"   => 'required|array',
                "unit_ids.*"    => ['required','integer','exists:rlst_units,id'],
                "from_date"     => 'required|date',
                "service_ids.*" => ['required','integer','exists:rlst_services,id', new UniqueUnitService($this->id)],
                "price"         => ['required','numeric','gt:0'],
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
