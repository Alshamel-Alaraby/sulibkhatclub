<?php

namespace Modules\ClubMembers\Entities;

use App\Traits\LogTrait;

use Spatie\Activitylog\LogOptions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElectoralCommittee extends Model
{
    use HasFactory, LogTrait;

    protected $guarded = ['id'];
    protected $table = 'cm_electoral_committees';


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Electoral Committee')
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
