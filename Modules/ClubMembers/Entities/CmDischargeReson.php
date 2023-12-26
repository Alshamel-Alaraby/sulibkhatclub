<?php

namespace Modules\ClubMembers\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class CmDischargeReson extends Model
{
    use HasFactory,LogTrait;

    protected $guarded = ['id'];

    protected $table ='cm_discharge_reson';

    public function cm_member_rejects()
    {
        return $this->hasMany(CmMemberReject::class, 'discharge_reson_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->cm_member_rejects()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'cm member rejects',
                'count' => $this->cm_member_rejects()->count(),
                'ids' => $this->cm_member_rejects()->pluck('prefix')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }



    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Discharge Reson')
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
