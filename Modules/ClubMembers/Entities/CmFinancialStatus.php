<?php

namespace Modules\ClubMembers\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class CmFinancialStatus extends Model
{
    use HasFactory, LogTrait, SoftDeletes;


    protected $fillable = [
        'name',
        'name_e',
        'company_id'
    ];

    protected $table = 'cm_financial_status';

    public function cm_members()
    {
        return $this->hasMany(CmMember::class, 'financial_status_id');
    }

    public function cm_type_permissions()
    {
        return $this->hasMany(CmTypePermission::class, 'cm_financial_status_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->cm_members()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'cm members',
                'count' => $this->cm_members()->count(),
                'ids' => $this->cm_members()->pluck('first_name')->toArray(),
            ];
        }
        if ($this->cm_type_permissions()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'cm_type_permissions',
                'count' => $this->cm_type_permissions()->count(),
                'ids' => $this->cm_type_permissions()->pluck('id')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Sponser')
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
