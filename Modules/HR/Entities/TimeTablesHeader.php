<?php

namespace Modules\HR\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class TimeTablesHeader extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'hr_timetables_header';
    protected $guarded = ['id'];
    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e', 'timetable_types_id', 'tt_monthly_hours')
            ->with('timetablesDetails:id,timetables_header_id,day_no,shift1_from,shift1_to,is_off',
                'TimeTableType:id,name,name_e');
    }

    public function timetablesDetails()
    {
        return $this->hasMany(TimeTablesDetail::class, 'timetables_header_id', 'id');
    }

    public function TimeTableType()
    {
        return $this->belongsTo(TimeTableType::class, 'timetable_types_id', 'id');
    }

    public function employeesTimeTablesHeaders()
    {
        return $this->hasMany(EmployeesTimeTablesHeader::class, 'timetables_header_id', 'id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->timetablesDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'time tables Details',
                'count' => $this->timetablesDetails()->count(),
                'ids' => $this->timetablesDetails()->pluck('shift1_from')->toArray(),
            ];
        }
        if ($this->employeesTimeTablesHeaders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'employeesTimeTablesHeaders',
                'count' => $this->employeesTimeTablesHeaders()->count(),
                'ids' => $this->employeesTimeTablesHeaders()->pluck('id')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Time Tables Header')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
