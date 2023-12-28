<?php

namespace Modules\HMS\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HMS\Entities\HMSAppointment;
use Modules\HMS\Entities\HMSDay;
use DateTime;
use Illuminate\Support\Facades\Mail;
use Modules\HMS\Emails\HMSNotifyPatientForAppointmentMail;
use Modules\HMS\Entities\HMSRoomsSchedule;
use Modules\HMS\Entities\HMSRoom;
use Modules\HMS\Entities\HMSSetting;
use Modules\HMS\Http\Requests\HMSAppointmentRequest;
use Modules\HMS\Transformers\HMSAppointmentResource;

class HMSRoomReservationAppointmentController extends Controller
{
    protected $interval;

    public function __construct(private HMSAppointment $model)
    {
        $this->model = $model;
        $this->interval = HMSSetting::first() ? HMSSetting::first()->interval : 15;////  Setting::first()->appointment_interval
    }

    public function index(Request $request)
    {
        $models = $this->model->whereType('room_reservation')->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if($request->page_name){
            $models->whereDate('date',$request->page_name =='waiting_rooms' ? '=' : '>',now());

            if($request->page_name == 'waiting_rooms' && (!$request->include_all_status || $request->include_all_status == 'false')){
                $models->whereIn('status',['Pending','Waiting','Processing']);
            }
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', HMSAppointmentResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }


    public function available_times(Request $request)
    {

        $request->validate(['date' => 'required|date|after:yesterday']);
        $day_id = HMSDay::whereCarbonDayNumber(Carbon::parse($request->date)->dayOfWeek)->first()->id;
        $doctor_work_time = HMSRoomsSchedule::whereDayId($day_id)->whereRoomId($request->room_id)->first();
        $time = [];


        if($doctor_work_time){
            $start = Carbon::parse($request->date." ". $doctor_work_time->from);
            $end = Carbon::parse($request->date." ". $doctor_work_time->to);
            $start_time = $start->format('H:i'); // Get time Format in Hour and minutes
            $end_time = $end->format('H:i');
            $i = 0;
            while (strtotime($start_time) <= strtotime($end_time)) {
                $start = $start_time;
                $end = date('H:i', strtotime('+' . $this->interval . ' minutes', strtotime($start_time)));
                $start_time = date('H:i', strtotime('+' . $this->interval . ' minutes', strtotime($start_time)));
                $i++;
                if (strtotime($start_time) <= strtotime($end_time) && now() < date('Y-m-d H:i', strtotime($request->date . ' ' . $start_time))) {
                    $time[$i]['start'] = $start;
                    $time[$i]['end'] = $end;
                    $time[$i]['available'] = $this->available_slot($request->date,$request->doctor_id,$request->room_id, $start, $end);
                }
            }

        }
        return $time;

    }

    public function available_slot($date,$doctor_id,$room_id,$start,$end){
    	$check = HMSAppointment::checkThisTimeIsExistsBeforeForThisDoctorInThisRoom($date,$doctor_id,$room_id,$start,$end)->count();
        if($check)
            return 1;
        return 0;
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(HMSAppointmentRequest $request)//HMSAppointmentRequest
    {
        $times = $this->arrage_time_and_divide_it($request->time);
        foreach($times as $time){
            // $appointment_and_status[0] -> is the appointment & $appointment_and_status[1] -> is the status of the appointment (added or extended)
            $appointment_and_status = $this->create_or_update_appointment($request,$time);
            $this->appointment_times($appointment_and_status[0],$time);
            $this->notify_patient($appointment_and_status[0],$appointment_and_status[1]);
        }

    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(HMSAppointmentRequest $request, HMSAppointment $appointment)
    {
        $appointment->update($request->validated());
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(HMSAppointment $appointment)
    {
        $this->notify_patient($appointment,'Deleted');
        $appointment->times()->delete();
        $appointment->delete();
    }

    public function change_appoinment_status(Request $request,HMSAppointment $appointment)
    {
        $request->validate(['action' => 'required|in:Pending,Waiting,Processing,Attended,Cancelled']);

        if($appointment->date > now() && $request->action != 'Cancelled')
            return response()->json(['message' => $request->header('Lang') == 'ar' ? 'لم يأتي هذا الموعد بعد' : "The appointment has not come yet"],422);

        if($appointment->status == 'Attended' || $appointment->status == 'Cancelled')
            return response()->json(['message' => $request->header('Lang') == 'ar' ? 'لا يوجد اجراءات لهذا الموعد' : "There is no actions for this appointment"],422);


        if($request->action == 'Waiting') { // give this patient number in queue
            $last_appointment_for_this_doctor = HMSAppointment::whereDoctorId($appointment->doctor_id)->whereRoomId($appointment->room_id)->whereDate('date',now())->latest('number')->first();
            $appointment->update(['number' => $last_appointment_for_this_doctor && $last_appointment_for_this_doctor->number ? $last_appointment_for_this_doctor->number+1 : 1]);
        }

        if($request->action == 'Pending')
            $appointment->update(['number' =>null]);

        $appointment->update(['status' => $request->action]);

        $this->notify_patient($appointment);

    }

    public function get_next_patient_for_each_doctor(){
        $next_appointments_for_each_doctor = HMSAppointment::whereDate('date', now())->whereStatus('Waiting')->orderBy('number')->get()->groupBy(['doctor_id','room_id'])
        ->flatMap(function($paitem){
           return  $paitem->map(function($item){
                return $item[0];
            });
        });

        $doctor_can_show_next_button =HMSAppointment::whereDate('date', now())->whereIn('status',['Processing','Waiting'])->count();

        return responseJson(200, 'success', ['appointments' => HMSAppointmentResource::collection($next_appointments_for_each_doctor),'doctor_can_show_next_button' => $doctor_can_show_next_button],null);

    }

    //this action only for doctors
    public function next_patient(Request $request){
        $user = $request->user();
        if($user && $user->type == 'doctor'){
            $processing_appointments = HMSAppointment::whereDate('date',now())->whereStatus('Processing')->get();
            foreach($processing_appointments as $appoint){
                $appoint->update(['status' => 'Attended']);
                $this->notify_patient($appoint);
            }
            $current_patient = HMSAppointment::whereDate('date', now())->whereStatus('Waiting')->orderBy('number')->first();
            if($current_patient){
                $current_patient->update(['status' => 'Processing']);
                $this->notify_patient($current_patient);
            }
        }

    }


    protected function arrage_time_and_divide_it($times){
        $data = collect($times)->sortBy('start')->values();
        $groupedArray = [];
        $currentGroup = [];

        foreach ($data as $index => $value) {
            $current_start_time_subtracted = date('H:i', strtotime('-' . $this->interval . ' minutes', strtotime($data[$index]['start'])));
            if ($index == 0 || $current_start_time_subtracted != $data[$index - 1]['start']) {
                if (!empty($currentGroup)) {
                    $groupedArray[] = $currentGroup;
                }
                $currentGroup = [$value];
            } else {
                $currentGroup[] = $value;
            }
        }
        if (!empty($currentGroup)) {
            $groupedArray[] = $currentGroup;
        }
        return $groupedArray;

    }

    protected function create_or_update_appointment($request,$time){
        $time_collected = collect($time);
        $first_item_start= $time_collected->first()['start'];
        $last_item_end = $time_collected->last()['end'];
        $existing_appointment = HMSAppointment::whereDate('date',$request->date)->whereDoctorId($request->doctor_id)
        ->whereRoomId($request->room_id)->wherePatientId($request->patient_id)->where(function($q) use($first_item_start,$last_item_end){
            $q->whereRelation('start_time','start',$last_item_end)->orWhereRelation('end_time','end',$first_item_start);
        })->first();
        if($existing_appointment){
            $existing_appointment->update($request->except(['time']));
            return [$existing_appointment,'Extended'];
        }
        return [$this->model->create($request->except(['time'])),'Added'];
    }


    protected function appointment_times($appointment,$times){
        foreach($times as $time){
            $appointment->times()->create($time);
        }
    }


    protected function notify_patient($appointment,$action = ''){
        $patient = $appointment->patient;
        if($appointment->email_notify && $patient && $patient->email){
            $lang = request()->header('Lang');
            $patient_name = $lang == 'ar' ? $patient->name : $patient->name_e;
            $message = $this->get_message($appointment, $lang , $action);
            Mail::to($patient->email)->send(new HMSNotifyPatientForAppointmentMail($message,$patient_name));
        }
    }

    protected function get_message($appointment, $lang, $action){
        $action = $action  == '' ? $appointment->status : $action;
        $doctor_name = $lang == 'ar' ? $appointment->doctor->name : $appointment->doctor->name_e;
        $appointment_time = $appointment->date . '  ( ' . $appointment->start_time->start . ' - ' . $appointment->end_time->end .' )';

        $message='';
        // Pending,Waiting,Processing,Attended,Cancelled
        switch($action){
            case 'Pending' : $message = $lang == 'ar' ?
                "تم تعليق الموعد الخاص بك لدى الدكتور $doctor_name" : "Your appointment with Dr. $doctor_name has been placed on hold" ;
                break;
            case 'Waiting' : $message = $lang == 'ar' ?
                "رقم الموعد الخاص بك لدى الدكتور $doctor_name هو $appointment->number" : "Your appointment number with Dr. $doctor_name is $appointment->number" ;
                break;
            case 'Processing' : $message = $lang == 'ar' ?
                "الموعد الخاص بك لدى الدكتور $doctor_name قيد التنفيذ" : "Your appointment with Dr. $doctor_name is in progress" ;
                break;
            case "Attended" : $message = $lang == "ar" ?
                "شكرا لحضورك الموعد الخاص بدكتور $doctor_name بنجاح نتمنى لك الشفاء العاجل": "Thank You for attended your appointment with Dr. $doctor_name. We wish you a speedy recovery" ;
                break;
            case "Cancelled" : $message = $lang == "ar" ?
                "تم الغاء الموعد الخاص بك مع دكتور $doctor_name" : "Your appointment with Dr. $doctor_name has been cancelled" ;
                break;
            case 'Added' :
                $message = $lang == 'ar' ?
                "تم حجز الموعد مع دكتور $doctor_name في تاريخ $appointment_time " : "The appointment was booked with Dr. $doctor_name on a date $appointment_time" ;
                break;
            case 'Extended' :
                $message = $lang == 'ar' ?
                "لقد تم تمديد موعدك مع الدكتور $doctor_name  : $appointment_time " : "The time of your appointment with Dr. $doctor_name has been extended $appointment_time" ;
                break;
            case 'Deleted' : $message = $lang == 'ar' ?
                "تم حذف الموعد الخاص بك مع دكتور $doctor_name" : "Your appointment with Dr. $doctor_name has been deleted" ;
                break;

        }

        return $message;
    }

}
