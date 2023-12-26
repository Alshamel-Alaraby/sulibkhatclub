<?php

namespace Modules\HR\Entities;

use App\Models\Employee;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class LocationDate extends Model
{
    use HasFactory, LogTrait;

    protected $guarded = ['id'];

    protected $table = 'hr_location_dates';

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'hr_employee_location_dates', 'location_date_id', 'employee_id');
    }

    public function employeeLocationDates()
    {
        return $this->hasMany(EmployeeLocationDate::class, 'location_date_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->employeeLocationDates()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'employeeLocationDates',
                'count' => $this->employeeLocationDates()->count(),
                'ids' => $this->employeeLocationDates()->pluck('id')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('End Service')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
