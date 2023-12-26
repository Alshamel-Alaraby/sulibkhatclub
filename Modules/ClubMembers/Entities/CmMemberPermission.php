<?php

namespace Modules\ClubMembers\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class CmMemberPermission extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $guarded = [];

    protected $table = 'cm_members_permissions';

    public function cm_type_permissions()
    {
        return $this->hasMany(CmTypePermission::class, 'cm_permissions_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

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
            ->useLogName('CmMemberPermission')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
