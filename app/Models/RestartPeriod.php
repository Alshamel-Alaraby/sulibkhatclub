<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestartPeriod extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_restart_period';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select(
            'id',
            'name',
            'name_e',
        );
    }

    public function salesmenPlans()
    {
        return $this->hasMany(SalesmenPlan::class, 'restart_period_id');
    }

    public function periodicMaintenances()
    {
        return $this->hasMany(PeriodicMaintenance::class, 'restart_period_id');
    }
    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->salesmenPlans()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'salesmenPlans',
                'count' => $this->salesmenPlans()->count(),
                'ids' => $this->salesmenPlans()->pluck('name')->toArray(),
            ];
        }
        if ($this->periodicMaintenances()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'periodic Maintenances',
                'count' => $this->periodicMaintenances()->count(),
                'ids' => $this->periodicMaintenances()->pluck('name')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }


    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Restart Period')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
