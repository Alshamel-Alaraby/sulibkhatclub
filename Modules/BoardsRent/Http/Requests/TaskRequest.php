<?php

namespace Modules\BoardsRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:boards_rent_tasks,name,'. ($this->method() == 'PUT' ?  $this->id : ''),
            'name_e' => 'required|unique:boards_rent_tasks,name_e,'. ($this->method() == 'PUT' ?  $this->id : ''),
            "description" => "required|string",
            "description_e" => "required|string",
            "department_id" => "required|exists:general_departments,id",
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
