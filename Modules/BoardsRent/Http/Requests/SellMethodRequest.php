<?php

namespace Modules\BoardsRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellMethodRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        // if method put
        if ($this->method() == 'PUT') {
            return [
                'name' => 'required|string|max:255|unique:boards_rent_sell_methods,name,' . $this->id,
                'name_e' => 'required|string|max:255|unique:boards_rent_sell_methods,name_e,' . $this->id,
            ];
        }

        return [
            'name' => 'required|string|max:255|unique:boards_rent_sell_methods,name',
            'name_e' => 'required|string|max:255|unique:boards_rent_sell_methods,name_e',
        ];
    }

}
