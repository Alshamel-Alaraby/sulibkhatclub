<?php

namespace App\Http\Requests;

use App\Http\Resources\TaskResource;
use Illuminate\Foundation\Http\FormRequest;

class PeriodicMaintenanceRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => "required|max:255",
            'name_e' => "required|max:255",
            'date' => "required|date_format:Y-m-d|after_or_equal:" . date('Y-m-d'),
            'task_id' => "required|exists:general_tasks,id",
            'department_id' => "required|exists:general_departments,id",
            'is_active' => "nullable|in:true,false",
        ];
    }
}
