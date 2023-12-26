<?php

namespace Modules\HMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HMS\Entities\HMSRoomsSchedule;

class HMSRoomRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'name' => ['required' , Rule::unique('h_m_s_rooms')->where('branch_id',request()->branch_id)->where('rooms_category_id',request()->rooms_category_id)->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->room->id : 0,'id')],
            'name_e' => ['required' , Rule::unique('h_m_s_rooms')->where('branch_id',request()->branch_id)->where('rooms_category_id',request()->rooms_category_id)->whereNull('deleted_at')->ignore($this->method() == 'PUT' ? $this->room->id : 0,'id')],
            'branch_id' => ['required' ,Rule::exists('general_branches','id')],
            'rooms_category_id' => ['required' ,Rule::exists('h_m_s_rooms_categories','id')],
            'note' => 'nullable',
            'schedule_times.*' => ['nullable','array',function ($attribute, $value, $fail) {

                // Custom validation logic to check if record exists in the schedule_times array or this room is busy in database or for specific doctor
                $scheduleTimes = request()->input('schedule_times');

                $index = explode('.',$attribute)[1];

                $check_exists_in_request = collect($scheduleTimes)->except($index)->where('day_id',$value['day_id'])
                ->where('from','<=',$value['from'])->where('to','>',$value['from'])->first();

                $check_exists_in_database = 0;

                if($this->method() == 'PUT'){
                    $check_exists_in_database =HMSRoomsSchedule::where('day_id',$value['day_id'])
                    ->where(function($q) use($value){
                        if($value['from'])
                            $q->where('from','<=',$value['from'])->where('to','>',$value['from']);
                    })->where('room_id','!=',$this->room->id)
                    ->first();
                }

                if ($check_exists_in_request || ($check_exists_in_database && $value['from'] && $value['to'])) {
                    return $fail('You already selected this time before');
                }

            },],
            'schedule_times.*.day_id' => ['required','exists:h_m_s_days,id', ],
            'schedule_times.*.from' => 'required|date_format:H:i',
            'schedule_times.*.to' => 'required|date_format:H:i|after:schedule_times.*.from',
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
