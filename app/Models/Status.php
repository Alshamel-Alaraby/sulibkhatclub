<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BoardsRent\Entities\UnitStatus;
use Spatie\Activitylog\LogOptions;

class Status extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_statuses';

    protected $guarded = ['id'];

    public function unitStatuses()
    {
        return $this->hasMany(UnitStatus::class, 'status_id');
    }

    public function orders()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\Order::class);
    }

    public function documentCompanyModuleStatuses()
    {
        return $this->hasMany(DocumentCompanyModuleStatus::class,'status_id');

    }
    public function documentHeaderDetails()
    {
        return $this->hasMany(DocumentHeaderDetail::class,'unit_status_id');

    }
    public function tasks()
    {
        return $this->hasMany(Task::class, 'status_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'unit_status_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->unitStatuses()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'unit-Statuses',
                'count' => $this->unitStatuses()->count(),
                'ids' => $this->unitStatuses()->pluck('name')->toArray(),
            ];
        }
        if ($this->orders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'orders',
                'count' => $this->orders()->count(),
                'ids' => $this->orders()->pluck('date')->toArray(),
            ];
        }
        if ($this->units()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'units',
                'count' => $this->units()->count(),
                'ids' => $this->units()->pluck('name')->toArray(),
            ];
        }
        if ($this->documentCompanyModuleStatuses()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'document-Company-Module-Statuses',
                'count' => $this->documentCompanyModuleStatuses()->count(),
                'ids' => $this->documentCompanyModuleStatuses()->pluck('id')->toArray(),
            ];
        }
        if ($this->documentHeaderDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'document-Header-Details',
                'count' => $this->documentHeaderDetails()->count(),
                'ids' => $this->documentHeaderDetails()->pluck('id')->toArray(),
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
            ->useLogName('Status')
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
