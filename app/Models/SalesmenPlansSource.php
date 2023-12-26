<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class SalesmenPlansSource extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_salesmen_plans_sources';

    protected $fillable = [
        'name',
        'name_e',
        'company_id',
    ];

    public function salesmenPlans()
    {
        return $this->hasMany(SalesmenPlan::class, 'salesmen_plans_source_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->salesmenPlans()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'salesmen Plans',
                'count' => $this->salesmenPlans()->count(),
                'ids' => $this->salesmenPlans()->pluck('name')->toArray(),
            ];
        }
        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('SalesmenPlansSource')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
