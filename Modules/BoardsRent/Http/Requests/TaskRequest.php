<?php

namespace Modules\BoardsRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
        return [

            "execution_date" => "nullable|date",
            "execution_end_date" => "nullable|date|after_or_equal:execution_date",
            "notification_date" => "nullable|date",
            "execution_duration" => "nullable|string",
            "contact_phone" => "nullable|string",
            "contact_person" => "nullable|string",
            "note" => "nullable|string",
            "task_title"    => "nullable|string",
            "department_id" => "nullable|exists:general_departments,id",
            "employee_id" => "nullable|exists:general_employees,id",
            "department_task_id" => "nullable|exists:general_depertment_tasks,id",
            "status_id" => "nullable|exists:general_statuses,id",
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s|after:start_time',
            "type" => "nullable|in:equipment,customer",
            "equipment_id" => "required_if:type,==,equipment|exists:general_equipments,id",
            "customer_id" => "required_if:type,==,customer|exists:general_customers,id",
            "media" => "nullable|array",
            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],
            "supervisors" => "nullable|array",
            "supervisors.*" => "nullable|exists:general_employees,id",
            "attentions" => "nullable|array",
            "attentions.*" => "nullable|exists:general_employees,id",
            "location_id" => "required|exists:general_locations,id",
            "priority_id" => "required|exists:general_priorities,id",
        ];
    }
}
