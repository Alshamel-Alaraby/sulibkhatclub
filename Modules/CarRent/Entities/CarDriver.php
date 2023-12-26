<?php

namespace Modules\CarRent\Entities;

use App\Models\Employee;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class CarDriver extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'car_drivers';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select(
                'id',
                'driving_license',
                'license_exp_date',
                'daily_price',
                'weekly_price',
                'monthly_price',
                'employee_id'
            )
            ->with('employee:id,name,name_e');
    }

    // relations
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Car Drivers')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
