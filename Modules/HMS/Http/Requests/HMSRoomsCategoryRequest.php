<?php

namespace Modules\HMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HMSRoomsCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required' , Rule::unique('h_m_s_rooms_categories')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->rooms_category->id : 0,'id')],
            'name_e' => ['required' , Rule::unique('h_m_s_rooms_categories')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->rooms_category->id : 0,'id')],
            'parent_id' => 'nullable|exists:h_m_s_rooms_categories,id',
            'is_default' => 'required|boolean',
            'does_it_have_schedule' => 'required|boolean',
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
