<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstExpenseItemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:100|unique:rlst_expenses_items,name,'.($this->method() == 'PUT' ? $this->id : ''),
            'name_e' => 'required|string|max:100|unique:rlst_expenses_items,name_e,'.($this->method() == 'PUT' ? $this->id : ''),
            'chart_id' => 'required|integer|exists:gl_chart,id',
            'type' => 'sometimes|integer|in:building,unit,unit/building,general',
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
