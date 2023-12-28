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
                "unit_id"      => ['required','integer','exists:rlst_units,id'],
                'details' => 'required|array|min:1',
                "details.*.from_date"     => 'required|date|after:yesterday',
                "details.*.service_id" => ['required','integer','exists:rlst_services,id', new UniqueUnitService($this->id)],
                "details.*.default_price"         => ['required','numeric','gt:0'],
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
