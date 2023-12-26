<?php

namespace Modules\HMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HMS\Entities\HMSDoctorsWorkTime;

class HMSDoctorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'name_e' => 'required',
            'email' => [request()->type =='outdoctor' ?'nullable' : 'required','email',Rule::unique('h_m_s_doctors','email')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->doctor->id : 0,'id'),
            Rule::unique('general_users','email')->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? ($this->doctor->type !='outdoctor' ? $this->doctor->user_id: 0) : 0,'id')],
            'mobile' => 'required',
            'whatsapp' => 'nullable',
            'type' => 'required|in:indoctor,outdoctor',
            'analytic_account' => 'nullable',
            'selling_items_commission' => 'nullable|numeric|min:0|max:100',
            'transfer_patient_commission' => 'nullable|numeric|min:0|max:100',
            'medical_commission' => 'nullable|numeric|min:0|max:100',
            'specialty_id' => 'required|exists:h_m_s_specialties,id',
            'work_times.*' => ['nullable','array',function ($attribute, $value, $fail) {

                // Custom validation logic to check if record exists in the work_times array or this room is busy in database or for specific doctor
                $workTimes = request()->input('work_times');

                $index = explode('.',$attribute)[1];

                $check_exists_in_request = collect($workTimes)->except($index)->where('day_id',$value['day_id'])->where('room_id',$value['room_id'])
                ->where('from','<=',$value['from'])->where('to','>',$value['from'])->first();

                $check_exists_in_database = HMSDoctorsWorkTime::where('day_id',$value['day_id'])->where('room_id',$value['room_id'])->where(function($q) use($value){
                    if($value['from'])
                        $q->where('from','<=',$value['from'])->where('to','>',$value['from']);
                });

                if($this->method() == 'PUT'){
                    $check_exists_in_database->where('doctor_id','!=',$this->doctor->id);
                }

                if ($check_exists_in_request || ($check_exists_in_database->first() && $value['from'] && $value['to'])) {
                    return $fail('You already selected this time before');
                }

            },],
            'work_times.*.day_id' => ['required','exists:h_m_s_days,id', ],
            'work_times.*.room_id' => 'required|exists:h_m_s_rooms,id',
            'work_times.*.branch_id' => 'required|exists:general_branches,id',
            'work_times.*.from' => 'required|date_format:H:i',
            'work_times.*.to' => 'required|date_format:H:i|after:work_times.*.from',
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
