<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class Role extends Model
{
    use HasFactory,SoftDeletes, LogTrait   ;
    protected $table = 'general_roles';

    protected $guarded = ['id'];

    public function roleType()
    {
        return $this->belongsTo(RoleType::class,'roletype_id','id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class,'general_role_user', 'role_id', 'user_id');
    }

    public function workflows()
    {
        return $this->hasMany(RoleWorkflow::class);
    }

    public function roleScreenHotFields()
    {
        return $this->hasMany(RoleScreenHotfield::class,'role_id','id');
    }

    public function roleUsers()
    {
        return $this->hasMany(RoleUser::class, 'role_id', 'id');
    }

    public function roleWorkFlowButtons()
    {
        return $this->hasMany(RoleWorkflowButton::class, 'role_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Role')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->workflows()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'workflows',
                'count' => $this->workflows()->count(),
                'ids' => $this->workflows()->pluck('workflow_name')->toArray(),
            ];
        }
        if ($this->roleWorkFlowButtons()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'role WorkFlow Buttons',
                'count' => $this->roleWorkFlowButtons()->count(),
                'ids' => $this->roleWorkFlowButtons()->pluck('id')->toArray(),
            ];
        }
        if ($this->roleUsers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'role Users',
                'count' => $this->roleUsers()->count(),
                'ids' => $this->roleUsers()->pluck('id')->toArray(),
            ];
        }
        if ($this->roleScreenHotFields()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'role Screen HotFields',
                'count' => $this->roleScreenHotFields()->count(),
                'ids' => $this->roleScreenHotFields()->pluck('id')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }
}
