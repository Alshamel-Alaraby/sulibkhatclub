<?php

namespace Modules\HR\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class PublicHolidayHeader extends Model
{
    use HasFactory, LogTrait;

    protected $guarded = ['id'];

    protected $table = 'hr_public_holiday_headers';

    public function PublicHolidayDetails()
    {
        return $this->hasMany(PublicHolidayDetail::class, 'public_holiday_header_id', 'id');
    }

    public function employeePublicHolidayHeaders()
    {
        return $this->hasMany(EmployeePublicHolidayHeader::class, 'public_holiday_header_id', 'id');
    }
    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->PublicHolidayDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'Public Holiday Details',
                'count' => $this->PublicHolidayDetails()->count(),
                'ids' => $this->PublicHolidayDetails()->pluck('name')->toArray(),
            ];
        }
        if ($this->employeePublicHolidayHeaders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'employeePublicHolidayHeaders',
                'count' => $this->employeePublicHolidayHeaders()->count(),
                'ids' => $this->employeePublicHolidayHeaders()->pluck('id')->toArray(),
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
