<?php

namespace Modules\HR\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class AttendanceSetting extends Model
{
    use HasFactory, LogTrait;

    protected $guarded = ['id'];

    protected $table = 'hr_attendance_settings';

    public function scopeData($query)
    {
        return $query->select('id', 'pre_in', 'post_in', 'absent_minutes', 'pre_out', 'post_out', 'max_out',
            'location_fingerprint',
            'mobile_id_fingerprint',
            'pre_in_fingerprint',
            'day_off_fingerprint',
            'public_holiday_fingerprint',
            'post_out_fingerprint',
            'pre_out_fingerprint',
            'holiday_fingerprint',
            'Training_course_fingerprint',
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Attendance Setting')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
