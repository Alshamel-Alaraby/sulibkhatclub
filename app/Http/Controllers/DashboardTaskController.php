<?php

namespace App\Http\Controllers;

use App\Models\Depertment;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Location;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DashboardTaskController extends Controller
{

    public function index()
    {
        $data['total_locations'] = Location::count();
        $data['total_departments'] = Depertment::count();
        $data['total_employees'] = Employee::count();
        $data['total_equipments'] = Equipment::count();
        $data['total_tasks'] = Task::count();
        $data['total_pending'] = Task::whereStatusId(1)->count();
        $data['total_completed'] = Task::whereStatusId(3)->count();
        $data['total_rejected'] = Task::whereStatusId(4)->count();

        $data['departments_tasks'] = DB::table('general_tasks')->join('general_departments','general_departments.id','general_tasks.department_id')
            ->select('general_departments.name','general_departments.name_e','general_tasks.department_id', DB::raw('COUNT(*) as count'))
            ->groupBy('general_tasks.department_id','general_departments.name','general_departments.name_e')
            ->get();

        $data['locations_tasks'] = DB::table('general_tasks')->join('general_locations','general_locations.id','general_tasks.location_id')
            ->select('general_locations.name','general_locations.name_e','general_tasks.location_id', DB::raw('COUNT(*) as count'))
            ->groupBy('general_tasks.location_id','general_locations.name','general_locations.name_e')
            ->get();

        $date = $this->getCurrentMonthAndLastMonthDate();
        $data['tasks_for_current_month'] =  Task::getTasksThroughMonthGroubByWeeks($date['current_year'],$date['current_month']);
        $data['tasks_for_last_month'] =  Task::getTasksThroughMonthGroubByWeeks($date['last_year'],$date['last_month']);
        $data['total_tasks_for_each_month_in_year'] =  $this->get_total_tasks_for_each_month_in_year($date['current_year']);


        return responseJson(200, 'success', $data, null);
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

    protected function get_total_tasks_for_each_month_in_year($year){

        $totalAmountByMonth = Task::selectRaw('MONTH(created_at) as month,COUNT(*) as total')
            ->whereYear('created_at', $year)
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
}
