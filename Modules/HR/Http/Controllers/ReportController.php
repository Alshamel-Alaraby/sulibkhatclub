<?php

namespace Modules\HR\Http\Controllers;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\EmployeesTimeTablesDetail;
use Modules\HR\Entities\EmployeesTimeTablesHeader;
use Modules\HR\Entities\TimeTablesDetail;
use Modules\HR\Entities\TimeTablesHeader;
use Modules\HR\Transformers\AttendanceDepartureReportResource;
use Modules\HR\Entities\Fingerprint;
use Modules\HR\Entities\AttendanceSetting;
use Illuminate\Support\Facades\DB;
use DateTime;


class ReportController extends Controller
{
    public function __construct(private Employee $model, private EmployeesTimeTablesDetail $employeesTimeTableDetailModel, private EmployeesTimeTablesHeader $employeesTimeTablesHeaderModel)
    {
        $this->model = $model;
        $this->employeesTimeTableDetailModel = $employeesTimeTableDetailModel;
        $this->employeesTimeTablesHeaderModel = $employeesTimeTablesHeaderModel;
    }

    // public function AttendanceDeparture(Request $request)
    // {

    //     $employeesIds = $this->employeesTimeTableDetailModel->pluck('employee_id');

    //     if ($request->month && $request->year) {
    //         $year = $request->year;
    //         $month = $request->month;

    //         $date_start = Carbon::create($year, $month)->startOfMonth();//->format('Y-m-d'); //returns 2020-03-01
    //         $date_end = Carbon::create($year, $month)->lastOfMonth();//->format('Y-m-d'); //returns 2020-03-31
    //     }


    //     $date_end->addDay(-1); // for the sake of for loop


    //     $reportData = [];
    //     $i = 0;


    //     $attendanceSetting = AttendanceSetting::first();


    //     $minsPreIn = $attendanceSetting['pre_in'];
    //     $minsPostIn = $attendanceSetting['post_in'];
    //     $minsAbsentIn = $attendanceSetting['absent_minutes'];

    //     $minsPreOut = $attendanceSetting['pre_out'];
    //     $minsPostOut = $attendanceSetting['post_out'];
    //     $minsMaxOut = $attendanceSetting['max_out'];


    //     foreach ($employeesIds as $employee_id) {

    //         $selectedEmployee = $this->model->find($employee_id, ['name', 'att_code']);

    //         $att_code = $selectedEmployee['att_code'];
    //         $employee_name = $selectedEmployee['name'];


    //         //return $allPeriodFingerprints->map->only(['att_type']);

    //         $row = 0;
    //         for ($date_i = $date_start; $date_i <= $date_end; $date_i->addDay())
    //         {
    //                 $date_i = "2023-10-02";

    //                 $reportData[$i]['month'][$row] = $month;

    //                 $reportData[$i]['year'][$row] = $year;
    //                 $reportData[$i]['employee_id'][$row] = $employee_id;
    //                 $reportData[$i]['name'][$row] = $employee_name;


    //                 $reportData[$i]['day'][$row] = $date_i;
    //                 $reportData[$i]['attend_1'][$row] = null;
    //                 $reportData[$i]['depart_1'][$row] = null;
    //                 $reportData[$i]['attend_2'][$row] = null;
    //                 $reportData[$i]['depart_2'][$row] = null;

    //                 $reportData[$i]['late'][$row] = null;
    //                 $reportData[$i]['overtime'][$row] = null;

    //                 $reportData[$i]['exact_hours'][$row] = null;
    //                 $reportData[$i]['absence'][$row] = false; // he attended as default
    //                 $reportData[$i]['notes'][$row] = null;


    //                 $currentDateDayNo = Carbon::parse($date_i)->dayOfWeekIso; // Monday: 1, Sunday: 7


    //                 $dayFingerprint = Fingerprint::where('att_code', 5) // 18, $att_code
    //                 ->where(DB::RAW('DATE_FORMAT(vdate, "%Y-%m-%d")'), '2023-10-02')
    //                 ->get(); // 2023-06-10, $date_i



    //                 //$attend_shift1 = $dayFingerprint


    //                 $employees_timetables_header_ids
    //                 = $this->employeesTimeTableDetailModel
    //                 ->where('employee_id', 18) // 18
    //                 ->pluck('employees_timetables_header_id'); // 1,4


    //                 $timetables_header_id =
    //                 EmployeesTimetablesHeader::whereIn('id', $employees_timetables_header_ids)
    //                 ->where('start_from', '<=', '2023-05-10') // '2023-05-10'
    //                 ->orderBy('id', 'desc')->pluck('timetables_header_id')
    //                 ->first(); // 17


    //                 $timetables_types_id =
    //                 (TimeTablesHeader::where('id', $timetables_header_id)
    //                 ->pluck('timetable_types_id'))[0]; // 1: fixed time, one shift, 2: fixed time, two shifts


    //                 $dayDetail =
    //                 TimeTablesDetail::where('timetables_header_id', $timetables_header_id)
    //                 ->where('day_no', $currentDateDayNo)
    //                 ->get(); // id: 26, day_no: 1




    //                 $attend_shift1 = null;
    //                 $attend_shift2 = null;

    //                 $depart_shift1= null;
    //                 $depart_shift2= null;

    //                 //return Carbon::createFromFormat('Y-m-d H:i:s', $attend_shift1);


    //                 $attend_shift1 = $dayFingerprint->where("att_type", 0)->min('vdate');//->format('H:i:s');
    //                 $attend_shift1Time = (new DateTime($attend_shift1))->format('H:i:s');


    //                 $attend_shift2 = $dayFingerprint->where("att_type", 0)->max('vdate');
    //                 $attend_shift2Time = (new DateTime($attend_shift2))->format('H:i:s');

    //                 if($attend_shift2 === $attend_shift1)
    //                     $attend_shift2Time = null;

    //                 $depart_shift1 = $dayFingerprint->where("att_type", 1)->min('vdate');
    //                 $depart_shift1Time = (new DateTime($depart_shift1))->format('H:i:s');


    //                 $depart_shift2 = $dayFingerprint->where("att_type", 1)->max('vdate');
    //                 $depart_shift2Time = (new DateTime($depart_shift2))->format('H:i:s');

    //                 if($depart_shift2 === $depart_shift1)
    //                     $depart_shift2Time = null;



    //                 $is_off = ($dayDetail->map->only(['is_off'])->pluck('is_off'))[0];

    //                 $shift1_from =  ($dayDetail->map->only(['shift1_from'])->pluck('shift1_from'))[0]; // 08:00:00
    //                 $timePreIn_1 = Carbon::parse($shift1_from)->subMinutes($minsPreIn)->format('H:i:s'); // 07:30:00
    //                 $timePostIn_1 = Carbon::parse($shift1_from)->addMinutes($minsPostIn)->format('H:i:s'); // 08:15:00
    //                 $timeAbsentIn_1 = Carbon::parse($shift1_from)->addMinutes($minsAbsentIn)->format('H:i:s'); // 09:00:00

    //                 $shift1_to =  ($dayDetail->map->only(['shift1_to'])->pluck('shift1_to'))[0]; // 17:00:00
    //                 $timePreOut_1 = Carbon::parse($shift1_to)->subMinutes($minsPreOut)->format('H:i:s'); // 17:00:00
    //                 $timePostOut_1 = Carbon::parse($shift1_to)->addMinutes($minsPostOut)->format('H:i:s'); // 17:30:00
    //                 $timeMaxOut_1 = Carbon::parse($shift1_to)->addMinutes($minsMaxOut)->format('H:i:s'); // 19:00:00


    //                 $shift2_from =  ($dayDetail->map->only(['shift2_from'])->pluck('shift2_from'))[0];
    //                 $timePreIn_2 = Carbon::parse($shift2_from)->subMinutes($minsPreIn)->format('H:i:s');
    //                 $timePostIn_2 = Carbon::parse($shift2_from)->addMinutes($minsPostIn)->format('H:i:s');
    //                 $timeAbsentIn_2 = Carbon::parse($shift2_from)->addMinutes($minsAbsentIn)->format('H:i:s');

    //                 $shift2_to =  ($dayDetail->map->only(['shift2_to'])->pluck('shift2_to'))[0];
    //                 $timePreOut_2 = Carbon::parse($shift2_to)->subMinutes($minsPreOut)->format('H:i:s');
    //                 $timePostOut_2 = Carbon::parse($shift2_to)->addMinutes($minsPostOut)->format('H:i:s');
    //                 $timeMaxOut_2 = Carbon::parse($shift2_to)->addMinutes($minsMaxOut)->format('H:i:s');


    //                 if($is_off == 1)
    //                     $reportData[$i]['notes'][$row] = "عطلة اسبوعية";

    //                 // we can check if the fingerprints are valid
    //                 if($is_off == 0){

    //                     if($timetables_types_id == 1 || $timetables_types_id == 2) // case one-shift or two-shifts
    //                     {
    //                         // attendance - shift1

    //                         if($attend_shift1Time >= $timePreIn_1 && $attend_shift1Time <= $timePostIn_1)
    //                             $reportData[$i]['attend_1'][$row] = $shift1_from;

    //                         else if($attend_shift1Time > $timePostIn_1  && $attend_shift1Time <= $timeAbsentIn_1)
    //                             $reportData[$i]['attend_1'][$row] = $attend_shift1Time;

    //                         // departure - shift1


    //                         if($depart_shift1Time >= $timePreOut_1 && $depart_shift1Time <= $timePostOut_1)
    //                             $reportData[$i]['depart_1'][$row] = $shift1_to;

    //                         else if($depart_shift1Time > $timePostOut_1 && $depart_shift1Time <= $timeMaxOut_1)
    //                             $reportData[$i]['depart_1'][$row] = $depart_shift1Time;


    //                         //return $reportData[$i]['depart_1'][$row];

    //                         // one of the two fingerprints is NOT valid
    //                         if($reportData[$i]['attend_1'][$row] == null || $reportData[$i]['depart_1'][$row] == null)
    //                             $reportData[$i]['absence'][$row] = true;

    //                         $late_shift1 = '00:00:00';
    //                         $overTime_shift1 = '00:00:00';
    //                         $exactHours_shift1 = '00:00:00';

    //                         if($reportData[$i]['attend_1'][$row] != null)
    //                             $late_shift1 = Carbon::parse($reportData[$i]['attend_1'][$row])->diff(Carbon::parse($timePostIn_1))->format('%H:%I:%S');

    //                         if($reportData[$i]['depart_1'][$row] != null)
    //                             $overTime_shift1 = Carbon::parse($reportData[$i]['depart_1'][$row])->diff(Carbon::parse($timePostOut_1))->format('%H:%I:%S');

    //                         if($reportData[$i]['attend_1'][$row] != null && $reportData[$i]['depart_1'][$row] != null)
    //                             $exactHours_shift1 = Carbon::parse($reportData[$i]['depart_1'][$row])
    //                             ->diff(Carbon::parse($reportData[$i]['attend_1'][$row]))->format('%H:%I:%S');




    //                     } // case one-shift or two-shifts

    //                     if($timetables_types_id == 2) // case two-shifts
    //                     {
    //                         // attendance - shift2

    //                         if($attend_shift2Time >= $timePreIn_2 && $attend_shift2Time <= $timePostIn_2)
    //                             $reportData[$i]['attend_2'][$row] = $shift2_from;

    //                         else if($attend_shift2Time > $timePostIn_2  && $attend_shift2Time <= $timeAbsentIn_2)
    //                             $reportData[$i]['attend_2'][$row] = $attend_shift2Time;


    //                         // departure - shift2

    //                         if($depart_shift2Time >= $timePreOut_2 && $depart_shift2Time <= $timePostOut_2)
    //                             $reportData[$i]['depart_2'][$row] = $shift1_to;

    //                         else if($depart_shift2Time > $timePostOut_2 && $depart_shift2Time <= $timeMaxOut_2)
    //                             $reportData[$i]['depart_2'][$row] = $depart_shift2Time;


    //                         // one of the two fingerprints is NOT valid
    //                         if($reportData[$i]['attend_2'][$row] == null || $reportData[$i]['depart_2'][$row] == null)
    //                             $reportData[$i]['absence'][$row] = true;

    //                         $late_shift2 = '00:00:00';
    //                         $overTime_shift2 = '00:00:00';
    //                         $exactHours_shift2 = '00:00:00';

    //                         if($reportData[$i]['attend_2'][$row] != null)
    //                             $late_shift2 = Carbon::parse($reportData[$i]['attend_2'][$row])->diff(Carbon::parse($timePostIn_2))->format('%H:%I:%S');

    //                         if($reportData[$i]['depart_2'][$row] != null)
    //                             $overTime_shift2 = Carbon::parse($reportData[$i]['depart_2'][$row])->diff(Carbon::parse($timePostOut_2))->format('%H:%I:%S');

    //                         if($reportData[$i]['attend_2'][$row] != null && $reportData[$i]['depart_2'][$row] != null)
    //                             $exactHours_shift2 = Carbon::parse($reportData[$i]['depart_2'][$row])
    //                             ->diff(Carbon::parse($reportData[$i]['attend_2'][$row]))->format('%H:%I:%S');


    //                     } // case two-shifts


    //                 // Sum two-shifts late

    //                     // Split time1 into minutes and seconds
    //                     list($hours1, $minutes1, $seconds1) = explode(':', $late_shift1);

    //                     // Split time2 into minutes and seconds
    //                     list($hours2, $minutes2, $seconds2) = explode(':', $late_shift2);

    //                     // Calculate the total minutes and seconds
    //                     $totalHours = $hours1 + $hours2;
    //                     $totalMinutes = $minutes1 + $minutes2;
    //                     $totalSeconds = $seconds1 + $seconds2;

    //                     // Handle carry-over from seconds to minutes
    //                     if ($totalSeconds >= 60) {
    //                         $totalMinutes += floor($totalSeconds / 60);
    //                         $totalSeconds = $totalSeconds % 60;
    //                     }

    //                     // Handle carry-over from minutes to hours
    //                     if($totalMinutes >= 60){
    //                         $totalHours += floor($totalMinutes/ 60);
    //                         $totalMinutes = $totalMinutes % 60;
    //                     }

    //                     // Format the result
    //                     $reportData[$i]['late'][$row] = sprintf("%02d:%02d:%02d", $totalHours, $totalMinutes, $totalSeconds);

    //                     //eturn $reportData[$i]['late'][$row];

    //                 // Sum two-shifts overtime

    //                     // Split time1 into minutes and seconds
    //                     list($hours1, $minutes1, $seconds1) = explode(':', $overTime_shift1);

    //                     // Split time2 into minutes and seconds
    //                     list($hours2, $minutes2, $seconds2) = explode(':', $overTime_shift2);

    //                     // Calculate the total minutes and seconds
    //                     $totalHours = $hours1 + $hours2;
    //                     $totalMinutes = $minutes1 + $minutes2;
    //                     $totalSeconds = $seconds1 + $seconds2;

    //                     // Handle carry-over from seconds to minutes
    //                     if ($totalSeconds >= 60) {
    //                         $totalMinutes += floor($totalSeconds / 60);
    //                         $totalSeconds = $totalSeconds % 60;
    //                     }

    //                     // Handle carry-over from minutes to hours
    //                     if($totalMinutes >= 60){
    //                         $totalHours += floor($totalMinutes/ 60);
    //                         $totalMinutes = $totalMinutes % 60;
    //                     }

    //                     // Format the result
    //                     $reportData[$i]['overtime'][$row] = sprintf("%02d:%02d:%02d", $totalHours, $totalMinutes, $totalSeconds);

    //                 // Sum exact hours for two shifts

    //                     // Split time1 into minutes and seconds
    //                     list($hours1, $minutes1, $seconds1) = explode(':', $exactHours_shift1);

    //                     // Split time2 into minutes and seconds
    //                     list($hours2, $minutes2, $seconds2) = explode(':', $exactHours_shift2);

    //                     // Calculate the total minutes and seconds
    //                     $totalHours = $hours1 + $hours2;
    //                     $totalMinutes = $minutes1 + $minutes2;
    //                     $totalSeconds = $seconds1 + $seconds2;

    //                     // Handle carry-over from seconds to minutes
    //                     if ($totalSeconds >= 60) {
    //                         $totalMinutes += floor($totalSeconds / 60);
    //                         $totalSeconds = $totalSeconds % 60;
    //                     }

    //                     // Handle carry-over from minutes to hours
    //                     if($totalMinutes >= 60){
    //                         $totalHours += floor($totalMinutes/ 60);
    //                         $totalMinutes = $totalMinutes % 60;
    //                     }

    //                     // Format the result
    //                     $reportData[$i]['exact_hours'][$row] = sprintf("%02d:%02d:%02d", $totalHours, $totalMinutes, $totalSeconds);

    //                 } // we can check if the fingerprints are valid



    //                 $perPage = $request->per_page ?? null;
    //                 $page = $request->page ?? null;

    //                 if ($perPage && $page) {
    //                     $reportDataChunk = array_slice($reportData, ($page - 1) * $perPage, $perPage);
    //                 } else {
    //                     $reportDataChunk = $reportData;
    //                 }

    //                 return [
    //                     "message" => "success",
    //                     "data" => $reportDataChunk,
    //                     "pagination" => $perPage && $page ? [
    //                         "per_page" => $perPage,
    //                         "path" => $request->url(),
    //                         "total" => count($reportData),
    //                         "current_page" => $page,
    //                         "next_page_url" => $page < ceil(count($reportData) / $perPage) ? $request->url() . "?page=" . ($page + 1) : null,
    //                         "previous_page_url" => $page > 1 ? $request->url() . "?page=" . ($page - 1) : null,
    //                         "last_page" => ceil(count($reportData) / $perPage),
    //                         "has_more_pages" => $page < ceil(count($reportData) / $perPage),
    //                         "from" => (($page - 1) * $perPage) + 1,
    //                         "to" => min(count($reportData), $page * $perPage)
    //                     ] : null
    //                 ];
    //                 $row++;

    //         }

    //         $i++;

    //     }

    // }

    public function AttendanceDeparture(Request $request)
    {

        $employeesIds = $this->employeesTimeTableDetailModel->pluck('employee_id');

        if ($request->month && $request->year) {
            $year = $request->year;
            $month = $request->month;

            $date_start = Carbon::create($year, $month)->startOfMonth();//->format('Y-m-d'); //returns 2020-03-01
            $date_end = Carbon::create($year, $month)->lastOfMonth();//->format('Y-m-d'); //returns 2020-03-31
        }


        $date_end->addDay(-1); // for the sake of for loop


        $reportData = [];
        $i = 0;


        $attendanceSetting = AttendanceSetting::first();


        $minsPreIn = $attendanceSetting['pre_in'];
        $minsPostIn = $attendanceSetting['post_in'];
        $minsAbsentIn = $attendanceSetting['absent_minutes'];

        $minsPreOut = $attendanceSetting['pre_out'];
        $minsPostOut = $attendanceSetting['post_out'];
        $minsMaxOut = $attendanceSetting['max_out'];


        foreach ($employeesIds as $employee_id) {

            $selectedEmployee = $this->model->find($employee_id, ['name', 'att_code']);

            $att_code = $selectedEmployee['att_code'];
            $employee_name = $selectedEmployee['name'];


            //return $allPeriodFingerprints->map->only(['att_type']);

            $row = 0;
            for ($date_i = $date_start; $date_i <= $date_end; $date_i->addDay())
            {
                    $date_i = "2023-10-02";

                    $reportData[$i]['month'][$row] = $month;

                    $reportData[$i]['year'][$row] = $year;
                    $reportData[$i]['employee_id'][$row] = $employee_id;
                    $reportData[$i]['name'][$row] = $employee_name;


                    $reportData[$i]['day'][$row] = $date_i;
                    $reportData[$i]['attend_1'][$row] = null;
                    $reportData[$i]['depart_1'][$row] = null;
                    $reportData[$i]['attend_2'][$row] = null;
                    $reportData[$i]['depart_2'][$row] = null;

                    $reportData[$i]['late'][$row] = null;
                    $reportData[$i]['overtime'][$row] = null;

                    $reportData[$i]['exact_hours'][$row] = null;
                    $reportData[$i]['absence'][$row] = false; // he attended as default
                    $reportData[$i]['notes'][$row] = null;


                    $currentDateDayNo = Carbon::parse($date_i)->dayOfWeekIso; // Monday: 1, Sunday: 7


                    $dayFingerprint = Fingerprint::where('att_code', 5) // 18, $att_code
                    ->where(DB::RAW('DATE_FORMAT(vdate, "%Y-%m-%d")'), '2023-10-02')
                    ->get(); // 2023-06-10, $date_i



                    //$attend_shift1 = $dayFingerprint


                    $employees_timetables_header_ids
                    = $this->employeesTimeTableDetailModel
                    ->where('employee_id', 18) // 18
                    ->pluck('employees_timetables_header_id'); // 1,4


                    $timetables_header_id =
                    EmployeesTimetablesHeader::whereIn('id', $employees_timetables_header_ids)
                    ->where('start_from', '<=', '2023-05-10') // '2023-05-10'
                    ->orderBy('id', 'desc')->pluck('timetables_header_id')
                    ->first(); // 17


                    $timetables_types_id =
                    (TimeTablesHeader::where('id', $timetables_header_id)
                    ->pluck('timetable_types_id'))[0]; // 1: fixed time, one shift, 2: fixed time, two shifts


                    $dayDetail =
                    TimeTablesDetail::where('timetables_header_id', $timetables_header_id)
                    ->where('day_no', $currentDateDayNo)
                    ->get(); // id: 26, day_no: 1




                    $attend_shift1 = null;
                    $attend_shift2 = null;

                    $depart_shift1= null;
                    $depart_shift2= null;

                    //return Carbon::createFromFormat('Y-m-d H:i:s', $attend_shift1);


                    $attend_shift1 = $dayFingerprint->where("att_type", 0)->min('vdate');//->format('H:i:s');
                    $attend_shift1Time = (new DateTime($attend_shift1))->format('H:i:s');


                    $attend_shift2 = $dayFingerprint->where("att_type", 0)->max('vdate');
                    $attend_shift2Time = (new DateTime($attend_shift2))->format('H:i:s');

                    if($attend_shift2 === $attend_shift1)
                        $attend_shift2Time = null;

                    $depart_shift1 = $dayFingerprint->where("att_type", 1)->min('vdate');
                    $depart_shift1Time = (new DateTime($depart_shift1))->format('H:i:s');


                    $depart_shift2 = $dayFingerprint->where("att_type", 1)->max('vdate');
                    $depart_shift2Time = (new DateTime($depart_shift2))->format('H:i:s');

                    if($depart_shift2 === $depart_shift1)
                        $depart_shift2Time = null;



                    $is_off = ($dayDetail->map->only(['is_off'])->pluck('is_off'))[0];

                    $shift1_from =  ($dayDetail->map->only(['shift1_from'])->pluck('shift1_from'))[0]; // 08:00:00
                    $timePreIn_1 = Carbon::parse($shift1_from)->subMinutes($minsPreIn)->format('H:i:s'); // 07:30:00
                    $timePostIn_1 = Carbon::parse($shift1_from)->addMinutes($minsPostIn)->format('H:i:s'); // 08:15:00
                    $timeAbsentIn_1 = Carbon::parse($shift1_from)->addMinutes($minsAbsentIn)->format('H:i:s'); // 09:00:00

                    $shift1_to =  ($dayDetail->map->only(['shift1_to'])->pluck('shift1_to'))[0]; // 17:00:00
                    $timePreOut_1 = Carbon::parse($shift1_to)->subMinutes($minsPreOut)->format('H:i:s'); // 17:00:00
                    $timePostOut_1 = Carbon::parse($shift1_to)->addMinutes($minsPostOut)->format('H:i:s'); // 17:30:00
                    $timeMaxOut_1 = Carbon::parse($shift1_to)->addMinutes($minsMaxOut)->format('H:i:s'); // 19:00:00


                    $shift2_from =  ($dayDetail->map->only(['shift2_from'])->pluck('shift2_from'))[0];
                    $timePreIn_2 = Carbon::parse($shift2_from)->subMinutes($minsPreIn)->format('H:i:s');
                    $timePostIn_2 = Carbon::parse($shift2_from)->addMinutes($minsPostIn)->format('H:i:s');
                    $timeAbsentIn_2 = Carbon::parse($shift2_from)->addMinutes($minsAbsentIn)->format('H:i:s');

                    $shift2_to =  ($dayDetail->map->only(['shift2_to'])->pluck('shift2_to'))[0];
                    $timePreOut_2 = Carbon::parse($shift2_to)->subMinutes($minsPreOut)->format('H:i:s');
                    $timePostOut_2 = Carbon::parse($shift2_to)->addMinutes($minsPostOut)->format('H:i:s');
                    $timeMaxOut_2 = Carbon::parse($shift2_to)->addMinutes($minsMaxOut)->format('H:i:s');


                    if($is_off == 1)
                        $reportData[$i]['notes'][$row] = "عطلة اسبوعية";

                    // we can check if the fingerprints are valid
                    if($is_off == 0){

                        if($timetables_types_id == 1 || $timetables_types_id == 2) // case one-shift or two-shifts
                        {
                            // attendance - shift1

                            if($attend_shift1Time >= $timePreIn_1 && $attend_shift1Time <= $timePostIn_1)
                                $reportData[$i]['attend_1'][$row] = $shift1_from;

                            else if($attend_shift1Time > $timePostIn_1  && $attend_shift1Time <= $timeAbsentIn_1)
                                $reportData[$i]['attend_1'][$row] = $attend_shift1Time;

                            // departure - shift1


                            if($depart_shift1Time >= $timePreOut_1 && $depart_shift1Time <= $timePostOut_1)
                                $reportData[$i]['depart_1'][$row] = $shift1_to;

                            else if($depart_shift1Time > $timePostOut_1 && $depart_shift1Time <= $timeMaxOut_1)
                                $reportData[$i]['depart_1'][$row] = $depart_shift1Time;


                            //return $reportData[$i]['depart_1'][$row];

                            // one of the two fingerprints is NOT valid
                            if($reportData[$i]['attend_1'][$row] == null || $reportData[$i]['depart_1'][$row] == null)
                                $reportData[$i]['absence'][$row] = true;

                            $late_shift1 = '00:00:00';
                            $overTime_shift1 = '00:00:00';
                            $exactHours_shift1 = '00:00:00';

                            if($reportData[$i]['attend_1'][$row] != null)
                                $late_shift1 = Carbon::parse($reportData[$i]['attend_1'][$row])->diff(Carbon::parse($timePostIn_1))->format('%H:%I:%S');

                            if($reportData[$i]['depart_1'][$row] != null)
                                $overTime_shift1 = Carbon::parse($reportData[$i]['depart_1'][$row])->diff(Carbon::parse($timePostOut_1))->format('%H:%I:%S');

                            if($reportData[$i]['attend_1'][$row] != null && $reportData[$i]['depart_1'][$row] != null)
                                $exactHours_shift1 = Carbon::parse($reportData[$i]['depart_1'][$row])
                                ->diff(Carbon::parse($reportData[$i]['attend_1'][$row]))->format('%H:%I:%S');




                        } // case one-shift or two-shifts

                        if($timetables_types_id == 2) // case two-shifts
                        {
                            // attendance - shift2

                            if($attend_shift2Time >= $timePreIn_2 && $attend_shift2Time <= $timePostIn_2)
                                $reportData[$i]['attend_2'][$row] = $shift2_from;

                            else if($attend_shift2Time > $timePostIn_2  && $attend_shift2Time <= $timeAbsentIn_2)
                                $reportData[$i]['attend_2'][$row] = $attend_shift2Time;


                            // departure - shift2

                            if($depart_shift2Time >= $timePreOut_2 && $depart_shift2Time <= $timePostOut_2)
                                $reportData[$i]['depart_2'][$row] = $shift1_to;

                            else if($depart_shift2Time > $timePostOut_2 && $depart_shift2Time <= $timeMaxOut_2)
                                $reportData[$i]['depart_2'][$row] = $depart_shift2Time;


                            // one of the two fingerprints is NOT valid
                            if($reportData[$i]['attend_2'][$row] == null || $reportData[$i]['depart_2'][$row] == null)
                                $reportData[$i]['absence'][$row] = true;

                            $late_shift2 = '00:00:00';
                            $overTime_shift2 = '00:00:00';
                            $exactHours_shift2 = '00:00:00';

                            if($reportData[$i]['attend_2'][$row] != null)
                                $late_shift2 = Carbon::parse($reportData[$i]['attend_2'][$row])->diff(Carbon::parse($timePostIn_2))->format('%H:%I:%S');

                            if($reportData[$i]['depart_2'][$row] != null)
                                $overTime_shift2 = Carbon::parse($reportData[$i]['depart_2'][$row])->diff(Carbon::parse($timePostOut_2))->format('%H:%I:%S');

                            if($reportData[$i]['attend_2'][$row] != null && $reportData[$i]['depart_2'][$row] != null)
                                $exactHours_shift2 = Carbon::parse($reportData[$i]['depart_2'][$row])
                                ->diff(Carbon::parse($reportData[$i]['attend_2'][$row]))->format('%H:%I:%S');


                        } // case two-shifts


                    // Sum two-shifts late

                        // Split time1 into minutes and seconds
                        list($hours1, $minutes1, $seconds1) = explode(':', $late_shift1);

                        // Split time2 into minutes and seconds
                        list($hours2, $minutes2, $seconds2) = explode(':', $late_shift2);

                        // Calculate the total minutes and seconds
                        $totalHours = $hours1 + $hours2;
                        $totalMinutes = $minutes1 + $minutes2;
                        $totalSeconds = $seconds1 + $seconds2;

                        // Handle carry-over from seconds to minutes
                        if ($totalSeconds >= 60) {
                            $totalMinutes += floor($totalSeconds / 60);
                            $totalSeconds = $totalSeconds % 60;
                        }

                        // Handle carry-over from minutes to hours
                        if($totalMinutes >= 60){
                            $totalHours += floor($totalMinutes/ 60);
                            $totalMinutes = $totalMinutes % 60;
                        }

                        // Format the result
                        $reportData[$i]['late'][$row] = sprintf("%02d:%02d:%02d", $totalHours, $totalMinutes, $totalSeconds);

                        //eturn $reportData[$i]['late'][$row];

                    // Sum two-shifts overtime

                        // Split time1 into minutes and seconds
                        list($hours1, $minutes1, $seconds1) = explode(':', $overTime_shift1);

                        // Split time2 into minutes and seconds
                        list($hours2, $minutes2, $seconds2) = explode(':', $overTime_shift2);

                        // Calculate the total minutes and seconds
                        $totalHours = $hours1 + $hours2;
                        $totalMinutes = $minutes1 + $minutes2;
                        $totalSeconds = $seconds1 + $seconds2;

                        // Handle carry-over from seconds to minutes
                        if ($totalSeconds >= 60) {
                            $totalMinutes += floor($totalSeconds / 60);
                            $totalSeconds = $totalSeconds % 60;
                        }

                        // Handle carry-over from minutes to hours
                        if($totalMinutes >= 60){
                            $totalHours += floor($totalMinutes/ 60);
                            $totalMinutes = $totalMinutes % 60;
                        }

                        // Format the result
                        $reportData[$i]['overtime'][$row] = sprintf("%02d:%02d:%02d", $totalHours, $totalMinutes, $totalSeconds);

                    // Sum exact hours for two shifts

                        // Split time1 into minutes and seconds
                        list($hours1, $minutes1, $seconds1) = explode(':', $exactHours_shift1);

                        // Split time2 into minutes and seconds
                        list($hours2, $minutes2, $seconds2) = explode(':', $exactHours_shift2);

                        // Calculate the total minutes and seconds
                        $totalHours = $hours1 + $hours2;
                        $totalMinutes = $minutes1 + $minutes2;
                        $totalSeconds = $seconds1 + $seconds2;

                        // Handle carry-over from seconds to minutes
                        if ($totalSeconds >= 60) {
                            $totalMinutes += floor($totalSeconds / 60);
                            $totalSeconds = $totalSeconds % 60;
                        }

                        // Handle carry-over from minutes to hours
                        if($totalMinutes >= 60){
                            $totalHours += floor($totalMinutes/ 60);
                            $totalMinutes = $totalMinutes % 60;
                        }

                        // Format the result
                        $reportData[$i]['exact_hours'][$row] = sprintf("%02d:%02d:%02d", $totalHours, $totalMinutes, $totalSeconds);

                    } // we can check if the fingerprints are valid


                    // return $reportData;

                    //$reportData[$i]['']

                    $perPage = $request->per_page ?? null;
                    $page = $request->page ?? null;

                    if ($perPage || $page) {
                        $reportDataChunk = array_slice($reportData, ($page - 1) * $perPage, $perPage);
                    } else {
                        $reportDataChunk = $reportData;
                    }

                    return [
                        "message" => "success",
                        "data" => $reportDataChunk,
                        "pagination" => $perPage || $page ? [
                            "per_page" => $perPage,
                            "path" => $request->url(),
                            "total" => count($reportData),
                            "current_page" => $page,
                            "next_page_url" => $page < ceil(count($reportData) / $perPage) ? $request->url() . "?page=" . ($page + 1) : null,
                            "previous_page_url" => $page > 1 ? $request->url() . "?page=" . ($page - 1) : null,
                            "last_page" => ceil(count($reportData) / $perPage),
                            "has_more_pages" => $page < ceil(count($reportData) / $perPage),
                            "from" => (($page - 1) * $perPage) + 1,
                            "to" => min(count($reportData), $page * $perPage)
                        ] : null
                    ];

                    $row++;

            }

            $i++;

        }




    }
}
