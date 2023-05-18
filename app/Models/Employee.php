<?php

namespace App\Models;

use App\Traits\LogTrait;
use App\Models\Depertment;
use Spatie\Activitylog\LogOptions;
use Modules\BoardsRent\Entities\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HR\Entities\JobTitle;

class Employee extends Model
{
    use HasFactory, SoftDeletes, LogTrait;
    protected $table = 'general_employees';

    protected $fillable = [
        'name',
        'name_e',
        'salesman_type_id',
        'is_salesman',
        "for_all_customer",
        // "company_id",
        "department_id",
        'manager_id',
        'whatsapp',
        'mobile',
        'email',
        'customer_handel',
        'job_id',
        'branch_id',
        'manage_others',

    ];

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class, 'job_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }



    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function internalSalesman()
    {
        return $this->hasOne(InternalSalesman::class);
    }

    public function depertment()
    {
        return $this->belongsTo(Depertment::class, 'department_id');
    }

    public function tasksOwner()
    {

        return $this->belongsToMany(Task::class, 'boards_rent_employee_tasks', 'task_id', 'owner_id')->withPivot('owner_id', 'supervisor_id', 'notification_id');
    }

    public function tasksSupervisor()
    {

        return $this->belongsToMany(Task::class, 'boards_rent_employee_tasks', 'task_id', 'supervisor_id')->withPivot('owner_id', 'supervisor_id', 'notification_id');
    }
    public function tasksnNotification()
    {

        return $this->belongsToMany(Task::class, 'boards_rent_employee_tasks', 'task_id', 'notification_id')->withPivot('owner_id', 'supervisor_id', 'notification_id');
    }

    public function salesmanType()
    {
        return $this->belongsTo(SalesmenType::class, 'salesman_type_id');
    }

    public function plans()
    {

        return $this->belongsToMany(SalesmenPlan::class, 'general_salesmen_plans_details_assignment', 'employee_id', 'plan_id');
    }
    public function documents()
    {
        return $this->belongsToMany(Document::class, 'general_documents_approve_personal', 'employee_id', 'document_id', 'id', 'id');
    }


    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function children()
    {
        return $this->hasMany(Employee::class, 'manger_id');
    }
    public function hasChildren()
    {
        $h = $this->internalSalesman()->exists();
        return $h;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Employee')
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
