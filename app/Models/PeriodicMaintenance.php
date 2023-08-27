<?php

namespace App\Models;

use App\Traits\LogTrait;
use App\Enums\BooleanStatus;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PeriodicMaintenance extends Model
{
    use HasFactory, LogTrait;

    protected $table = "general_periodic_maintenances";

    protected $fillable = [
        'name',
        'name_e',
        'date',
        'task_id',
        'department_id',
        'restart_period_id',
        'is_active',
        'company_id',
    ];


    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }


    public function department()
    {
        return $this->belongsTo(Depertment::class, 'department_id');
    }

    public function restartPeriod()
    {
        return $this->belongsTo(RestartPeriod::class, 'restart_period_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Periodic Maintenance')
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
