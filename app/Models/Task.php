<?php

namespace App\Models;

use App\Models\Depertment;
use App\Models\DepertmentTask;
use App\Models\Employee;
use App\Models\GeneralCustomer;
use App\Models\Status;
use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class Task extends Model implements HasMedia
{
    use HasFactory,  MediaTrait;

    protected $guarded = ['id'];


    protected $table = "general_tasks";

    public function scopeData($query)
    {
        return $query
            ->select(
                'id','contact_person','created_by','contact_phone','task_title','execution_date','execution_duration','actual_execution_date','actual_execution_duration'
                ,'actual_end_time','actual_execution_end_date','actual_start_time','customer_id',
                'execution_end_date','notification_date','start_time','end_time','department_task_id','employee_id',
                'customer_id','department_id','status_id','note',
                'sub_location_id',
                'location_id',
                'priority_id',
                'is_closed',
                'admin_note',
                'task_requirement',
                'type','equipment_id','created_at'
            )
            ->with([
                'departmentTask:id,name,name_e','department:id,name,name_e','employee:id,name,name_e',
                'customer:id,name,name_e,contact_person,contact_phone','equipment:id,name,name_e,parent_id',
                'status:id,name,name_e'
                ,'location:id,name,name_e'
                ,'sub_location:id,name,name_e'
                ,'priority:id,name,name_e'
            ]);
    }

    public function departmentTask()
    {
        return $this->belongsTo(DepertmentTask::class, 'department_task_id');
    }
    public function department()
    {
        return $this->belongsTo(Depertment::class, 'department_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function customer()
    {
        return $this->belongsTo(GeneralCustomer::class, 'customer_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
    public function sub_location()
    {
        return $this->belongsTo(Location::class, 'sub_location_id');
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    public function owners()
    {

        return $this->belongsToMany(Employee::class, 'boards_rent_employee_tasks', 'task_id', 'owner_id', 'id', 'id')->withPivot('owner_id', 'supervisor_id', 'notification_id');
    }

    // public function supervisors()
    // {

    //     return $this->belongsToMany(Employee::class, 'boards_rent_employee_tasks', 'task_id', 'supervisor_id', 'id', 'id')->withPivot('owner_id', 'supervisor_id', 'notification_id');
    // }

    public function notifications()
    {

        return $this->belongsToMany(Employee::class, 'boards_rent_employee_tasks', 'task_id', 'notification_id', 'id', 'id')->withPivot('owner_id', 'supervisor_id', 'notification_id');
    }


    public function logs()
    {
        return $this->hasMany(TaskLog::class, 'task_id');
    }
    public function documentHeaders()
    {
        return $this->hasMany(DocumentHeader::class, 'task_id');
    }
    public function supervisors()
    {

        return $this->belongsToMany(Employee::class, 'general_employee_task', 'task_id', 'employee_id', 'id', 'id')->wherePivot('type', 'supervisor');
    }


    public function attentions()
    {

        return $this->belongsToMany(Employee::class, 'general_employee_task', 'task_id', 'employee_id', 'id', 'id')->wherePivot('type', 'attention');
    }

    public function scopeGetTasksThroughMonthGroubByWeeks($q,$year,$month){
        return $q
        ->whereYear('created_at',$year)
        ->whereMonth('created_at',$month)
        ->selectRaw('WEEK(created_at) as week, COUNT(*) as count')
        ->groupBy('week')
        ->get()
        ->map(function ($item) {
            $item->week = $this->getNameOfWeekInMounth(Carbon::now()->startOfMonth()->addWeeks($item->week - 1)->format('d'));
            return $item;
        })->groupBy('week')->map(function ($item) {
           return ['week' => $item[0]->week,'count' => $item->sum('count')];
        })->values();
    }

    public function getNameOfWeekInMounth($week_number)
    {
        switch(true){
            case ($week_number <= 7):
                $week_name = 'First Week';
                break;
            case ($week_number <= 15):
                $week_name = 'Second Week';
                break;
            case ($week_number <= 22):
                $week_name = 'Third Week';
                break;
            case ($week_number <= 29):
                $week_name = 'Fourth Week';
                break;
            case ($week_number <= 31):
                $week_name = 'Fifth Week';
                break;
        }
        return $week_name;
    }

    public function periodicMaintenances()
    {
        return $this->hasMany(PeriodicMaintenance::class, 'task_id');
    }
    public function taskLogs()
    {
        return $this->hasMany(TaskLog::class, 'task_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->logs()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'logs',
                'count' => $this->logs()->count(),
                'ids' => $this->logs()->pluck('action')->toArray(),
            ];
        }
        if ($this->documentHeaders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'document Headers',
                'count' => $this->documentHeaders()->count(),
                'ids' => $this->documentHeaders()->pluck('id')->toArray(),
            ];
        }
        if ($this->periodicMaintenances()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'periodic Maintenances',
                'count' => $this->periodicMaintenances()->count(),
                'ids' => $this->periodicMaintenances()->pluck('name')->toArray(),
            ];
        }
        if ($this->taskLogs()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'task logs',
                'count' => $this->taskLogs()->count(),
                'ids' => $this->taskLogs()->pluck('action')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }


    // scopes

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($q) use ($request) {

            if ($request->has('date')) {
                $q->whereDate('date', $request->date);
            }

            if ($request->search && $request->columns) {
                foreach ($request->columns as $column) {
                    if (strpos($column, ".") > 0) {
                        $column = explode(".", $column);
                        $q->orWhereRelation($column[0], $column[1], 'like', '%' . $request->search . '%');
                        // $q->orWhereHas($column[0], function ($q) use ($column, $request) {
                        //     $q->where($column[1], 'like', '%' . $request->search . '%');
                        // });
                    } else {
                        $q->orWhere($column, 'like', '%' . $request->search . '%');
                    }
                }
            }

            if (request()->header('company-id')) {
                if (in_array('company_id', $this->fillable)) {
                    $q->where('company_id', request()->header('company-id'));
                }
            }

            if ($request->key && $request->value) {
                $q->where($request->key, $request->value);
            }
        });
    }
}
