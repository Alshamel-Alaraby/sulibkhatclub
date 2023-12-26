<?php

namespace Modules\HR\Entities;

use App\Models\Employee;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class EmployeePublicHolidayHeader extends Model
{
    use HasFactory, LogTrait;

    protected $guarded = ['id'];

    protected $table = 'hr_employee_public_holiday_headers';

    public function publicHolidayHeader()
    {
        return $this->belongsTo(PublicHolidayHeader::class, 'public_holiday_header_id');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'hr_employee_public_holiday_details', 'employee_public_holiday_header_id', 'employee_id');
    }

    public function employeePublicHolidayDetails()
    {
        return $this->hasMany(EmployeePublicHolidayDetail::class, 'employee_public_holiday_header_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->employeePublicHolidayDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'employeePublicHolidayDetails',
                'count' => $this->employeePublicHolidayDetails()->count(),
                'ids' => $this->employeePublicHolidayDetails()->pluck('id')->toArray(),
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
