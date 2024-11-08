<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Depertment extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_departments';

    protected $guarded = ['id'];

    protected $casts = ['locations' => 'json'];

    /*
    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e', 'supervisors', 'attentions');
    }
    */

    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    public function depertmentTasks()
    {
        return $this->hasMany(DepertmentTask::class, 'department_id');
    }

    public function periodicMaintenances()
    {
        return $this->hasMany(PeriodicMaintenance::class, 'department_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'department_id');
    }
    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->employees()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'employees',
                'count' => $this->employees()->count(),
                'ids' => $this->employees()->pluck('name')->toArray(),
            ];
        }
        if ($this->depertmentTasks()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'depertmentTasks',
                'count' => $this->depertmentTasks()->count(),
                'ids' => $this->depertmentTasks()->pluck('name')->toArray(),
            ];
        }
        if ($this->periodicMaintenances()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'periodicMaintenances',
                'count' => $this->periodicMaintenances()->count(),
                'ids' => $this->periodicMaintenances()->pluck('name')->toArray(),
            ];
        }
        if ($this->tasks()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'tasks',
                'count' => $this->tasks()->count(),
                'ids' => $this->tasks()->pluck('contact_person')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Depertment')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function getAttentionsAttribute($value)
    {
        return json_decode($value);
    }

    public function setAttentionsAttribute($value)
    {
        $this->attributes['attentions'] = json_encode($value);
    }

    public function getSupervisorsAttribute($value)
    {
        return json_decode($value);
    }

    public function setSupervisorsAttribute($value)
    {
        $this->attributes['supervisors'] = json_encode($value);
    }



//    public function getLocationsAttribute($value)
//    {
//        return json_decode($value);
//    }


}
