<?php

namespace Modules\HMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\HMS\Entities\HMSAppointment;

class HMSAppointmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'doctor_id' => 'required|exists:h_m_s_doctors,id',
            'from_doctor_id' => 'nullable|exists:h_m_s_doctors,id|different:doctor_id',
            'patient_id' => 'required|exists:h_m_s_patients,id',
            'room_id' => 'required|exists:h_m_s_rooms,id',
            'date' => 'required|date|date_format:Y-m-d|after:yesterday',
            "reason" => 'nullable',
            "type" => 'nullable',
            "sms_notify" => 'nullable',
            "whatsapp_notify" => 'nullable',
            "email_notify" => 'nullable',
            "time" =>  ['nullable','array','min:1',function ($attribute, $value, $fail) {

                foreach($value as $time){
                     $check = HMSAppointment::checkThisTimeIsExistsBeforeForThisDoctorInThisRoom(request()->input('date'),
                    request()->input('doctor_id'),request()->input('room_id'),$time['start'],$time['end'])->select(['h_m_s_appointment_times.start','h_m_s_appointment_times.end'])->first();

                    if ($check) {
                        $en_message = "This time ( ".$time['start'] ." - ".$time['end'] . " ) is intersects with this time for this doctor in this room ( $check->start - $check->end)";

                        $ar_message = " يعارض موعد محجوز مسبقا في هذا التوقيت ( $check->start - $check->end ) لهذا الطبيب في نفس الغرفة  ( ".$time['start'] ." - ".$time['end'] . " ) هذا الموعد " ;
                        return request()->header('Lang') == 'en' ? $fail($en_message) :$fail($ar_message);
                    }
                }


            }],
            'time.*.start' => 'required|date_format:H:i',
            'time.*.end' => 'required|date_format:H:i|after:time.*.start',
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
