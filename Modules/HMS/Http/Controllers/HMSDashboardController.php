<?php

namespace Modules\HMS\Http\Controllers;

use App\Models\Branch;
use App\Models\DocumentHeader;
use App\Models\InsuranceCompany;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\HMS\Entities\HMSAppointment;
use Modules\HMS\Entities\HMSDiagnosisTest;
use Modules\HMS\Entities\HMSDoctor;
use Modules\HMS\Entities\HMSDrug;
use Modules\HMS\Entities\HMSPatient;
use Modules\HMS\Entities\HMSPrescription;
use Modules\HMS\Entities\HMSRoom;
use Modules\HMS\Entities\HMSServiceType;
use Modules\HMS\Entities\HMSSpecialty;

class HMSDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $data['total_appointments_today'] = HMSAppointment::whereType('doctor_appointment')->whereDate('date', now())->count();
        $data['total_upcoming_appointments'] = HMSAppointment::whereType('doctor_appointment')->whereDate('date', '>', now())->count();
        $data['total_appointments'] = HMSAppointment::whereType('doctor_appointment')->count();
        $data['total_rooms_reservations_today'] = HMSAppointment::whereType('room_reservation')->whereDate('date', now())->count();
        $data['total_upcoming_rooms_reservations'] = HMSAppointment::whereType('room_reservation')->whereDate('date', '>', now())->count();
        $data['total_rooms_reservations'] = HMSAppointment::whereType('room_reservation')->count();

        $data['total_in_patients'] = HMSPatient::whereType('inpatient')->count();
        $data['total_in_doctors'] = HMSDoctor::whereType('indoctor')->count();
        $data['total_out_patients'] = HMSPatient::whereType('outpatient')->count();
        $data['total_out_doctors'] = HMSDoctor::whereType('outdoctor')->count();
        $data['total_specialties'] = HMSSpecialty::count();
        $data['total_drugs'] = HMSDrug::count();
        $data['total_insurances_companies'] = InsuranceCompany::count();
        $data['total_branches'] = Branch::count();
        $data['total_rooms'] = HMSRoom::count();
        $data['total_prescriptions'] = HMSPrescription::count();
        $data['total_diagnosis_tests'] = HMSDiagnosisTest::count();
        $data['total_services_types'] = HMSServiceType::count();


        $invoices_amount=DB::table('rp_break_downs')
        ->leftJoin('general_break_settlements','general_break_settlements.break_id','rp_break_downs.id')
        ->whereIn('client_type_id',[4,5,6])
        ->whereNull('rp_break_downs.deleted_at')->where('rp_break_downs.document_id',45)
        ->whereNull('general_break_settlements.deleted_at')
        ->selectRaw('client_type_id, SUM(rp_break_downs.total) as total_amount,SUM(general_break_settlements.amount) as paid_amount')
        ->groupBy('client_type_id')
        ->get();

        $data['total_number_invocies'] = DocumentHeader::whereDocumentId(45)->count();
        $data['total_amount_of_invocies'] = $invoices_amount->sum('total_amount');
        $data['total_amount_for_patients'] =$invoices_amount->where('client_type_id',4)->sum('total_amount') ;
        $data['total_amount_for_insurance_companies'] =$invoices_amount->where('client_type_id',6)->sum('total_amount');
        $data['total_amount_for_doctors'] =$invoices_amount->where('client_type_id',5)->sum('total_amount');
        $data['patients_paid_amount'] =$invoices_amount->where('client_type_id',4)->sum('paid_amount') ;
        $data['insurance_companies_paid_amount'] =$invoices_amount->where('client_type_id',6)->sum('paid_amount');
        $data['doctors_paid_amount'] =$invoices_amount->where('client_type_id',5)->sum('paid_amount');


        $data['patient_group_by_age'] = $this->calculate_patient_age_and_group_them_through_period_of_age();
        $data['patient_group_by_blood_type'] = HMSPatient::select(DB::raw('COUNT(blood) as count'), 'blood')->groupBy('blood')->get();
        $data['peak_appointment_periods'] = $this->peak_appointment_periods();

        $date = $this->getCurrentMonthAndLastMonthDate();
        $data['appointments_for_current_month'] =  HMSAppointment::getAppointmentsThroughMonthGroubByWeeks($date['current_year'],$date['current_month']);
        $data['appointments_for_last_month'] =  HMSAppointment::getAppointmentsThroughMonthGroubByWeeks($date['last_year'],$date['last_month']);
        $data['total_amount_for_each_month_in_year'] =  $this->get_total_amount_for_each_month_in_year($date['current_year']);


        $data['total_and_paid_amount_for_each_doctors'] = $this->total_and_paid_amount_for_each_doctors();
        $data['total_and_paid_amount_for_each_insurance_company'] = $this->total_and_paid_amount_for_each_insurance_company();


        return responseJson(200, 'success', $data, null);
    }


    protected function calculate_patient_age_and_group_them_through_period_of_age()
    {
        return  DB::table('h_m_s_patients')
            ->select(
                'gender',
                DB::raw('CASE
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 0 AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= 10 THEN "0-10"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) > 10 AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= 20 THEN "11-20"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) > 20 AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= 30 THEN "21-30"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) > 30 AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= 40 THEN "31-40"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) > 40 AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= 50 THEN "41-50"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) > 50 AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= 60 THEN "51-60"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) > 60 AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= 70 THEN "61-70"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) > 70 AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= 80 THEN "71-80"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) > 80 AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= 90 THEN "81-90"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) > 90 AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= 1000 THEN "91-100"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) > 100 AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= 110 THEN "101-110"
                ELSE "Unknown"
            END AS age_group'),
                DB::raw('COUNT(*) as count')
            )->whereNull('deleted_at')
            ->distinct('id')
            ->groupBy('gender','age_group')
            ->orderBy('age_group')
            ->get();
    }

    protected function peak_appointment_periods()
    {
        $query = '';
        $periods = $this->get_start_and_end_time_of_all_appointments_and_divide_the_time_to_periods();
        foreach($periods as $period){
            $start = $period['start'];
            $end = $period['end'];
            $query .= "WHEN start >= '$start' AND start < '$end' THEN '$start - $end' ";
        }
        return DB::table('h_m_s_appointment_times')
            ->select(
                DB::raw('CASE '.$query.'ELSE "Unknown" END AS period_group'),
                DB::raw('COUNT(*) as count')
            )->whereNull('deleted_at')
            ->groupBy('period_group')
            ->get();
    }


    protected function get_start_and_end_time_of_all_appointments_and_divide_the_time_to_periods(){ // each period 60 minutes
        $min_max_time = DB::table('h_m_s_appointment_times')
        ->select(
            DB::raw('MIN(start) as start'),
            DB::raw('MAX(end) as end')
        )->whereNull('deleted_at')->first();
        $time=[];
        $start_time = $min_max_time->start;
        $end_time = $min_max_time->end;
        while (strtotime($start_time) <= strtotime($end_time)) {
            $start = $start_time;
            $end = date('H:i', strtotime('+' . 60 . ' minutes', strtotime($start_time)));
            $start_time = date('H:i', strtotime('+' . 60 . ' minutes', strtotime($start_time)));
            if (strtotime($start_time) <= strtotime($end_time)) {
                $time[]=['start' => $start, 'end' => $end];
            }else{
                $time[]=['start' => $start, 'end' => $min_max_time->end];
            }
        }
        return $time;
    }



    protected function getCurrentMonthAndLastMonthDate(){
        $date['current_month']= Carbon::now()->month;
        $date['current_year']= Carbon::now()->year;
        if($date['current_month'] == 1){
            $date['last_month']=12;
            $date['last_year']=$date['current_year']-1;
        }else{
            $date['last_month']=$date['current_month']-1;
            $date['last_year']=$date['current_year'];
        }

        return $date;
    }

    protected function get_total_amount_for_each_month_in_year($year){

        $totalAmountByMonth = DocumentHeader::selectRaw('MONTH(date) as month, SUM(net_invoice) as total_amount')
            ->whereYear('date', $year)
            ->groupBy('month')
            ->get();

        // If you want to get month names instead of month numbers, you can map them accordingly
        $monthNames = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        $totalAmountByMonth->transform(function ($item) use ($monthNames) {
            $item->month = $monthNames[$item->month];
            return $item;
        });

        return $totalAmountByMonth;

    }


    protected function total_and_paid_amount_for_each_doctors(){
       return  DB::table('rp_break_downs')
        ->join('h_m_s_doctors','h_m_s_doctors.id','rp_break_downs.customer_id')
        ->leftJoin('general_break_settlements','rp_break_downs.id','general_break_settlements.break_id')
        ->selectRaw('customer_id , SUM(rp_break_downs.total) as total_amount,name,name_e , COALESCE(SUM(general_break_settlements.amount), 0) as total_paid')
        ->where('client_type_id',5)
        ->groupBy('customer_id')->get();
    }

    protected function total_and_paid_amount_for_each_insurance_company(){
       return  DB::table('rp_break_downs')
        ->join('general_insurance_companies','general_insurance_companies.id','rp_break_downs.customer_id')
        ->leftJoin('general_break_settlements','rp_break_downs.id','general_break_settlements.break_id')
        ->selectRaw('customer_id , SUM(rp_break_downs.total) as total_amount,name,name_e , COALESCE(SUM(general_break_settlements.amount), 0) as total_paid')
        ->where('client_type_id',6)
        ->groupBy('customer_id')
        ->get();
    }


}
