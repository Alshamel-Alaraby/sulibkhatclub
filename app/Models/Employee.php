<?php

namespace App\Models;

use App\Models\Depertment;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BoardsRent\Entities\Task;
use Modules\HR\Entities\JobTitle;
use Modules\HR\Entities\RequestType;
use Spatie\Activitylog\LogOptions;

class Employee extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_employees';
    protected $casts = [ "manage_others" => "integer"];
    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select('id',
                'manager_id',
                'name',
                'name_e',
                'is_salesman',
                'customer_handel',
                'department_id',
                'code_country',
                'salesman_type_id',
                'for_all_customer',
                'mobile',
                'email',
                'whatsapp',
                'company_id',
                'job_id',
                'manage_others',
                'branch_id'
            )
            ->with(
                'jobTitle:id,name,name_e',
                'branch:id,name,name_e',
                'manager:id,name,name_e',
                'salesmanType:id,name,name_e',
                'department:id,name,name_e',
                'plans:id,name,name_e'
            );
    }

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

    public function department()
    {
        return $this->belongsTo(Depertment::class, 'department_id');
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
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function request_types()
    {

        return $this->belongsToMany(RequestType::class, 'hr_request_types_employees', 'employee_id', 'request_type_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->user()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'user',
                'count' => $this->user()->count(),
                'ids' => $this->user()->pluck('id')->toArray(),
            ];
        }
        if ($this->internalSalesman()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'internalSalesman',
                'count' => $this->internalSalesman()->count(),
                'ids' => $this->internalSalesman()->pluck('id')->toArray(),
            ];
        }

        if ($this->plans()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'plans',
                'count' => $this->plans()->count(),
                'ids' => $this->plans()->pluck('id')->toArray(),
            ];
        }
        if ($this->documents()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'documents',
                'count' => $this->documents()->count(),
                'ids' => $this->documents()->pluck('id')->toArray(),
            ];
        }
        if ($this->children()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'Employees',
                'count' => $this->children()->count(),
                'ids' => $this->children()->pluck('id')->toArray(),
            ];
        }
        if ($this->request_types()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'request_types',
                'count' => $this->request_types()->count(),
                'ids' => $this->request_types()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Employee')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
