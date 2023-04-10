<?php

namespace Modules\ClubMembers\Entities;

use App\Models\Status;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class CmMember extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $guarded = [];
    protected $table = 'cm_members';

    protected $casts = [
        'is_accept' => 'App\Enums\IsAccept',
        'is_sponsor' => 'App\Enums\IsSponsor',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class,'status_id');
    }
    public function sponsors()
    {
        return $this->belongsTo(\Modules\ClubMembers\Entities\CmSponser::class,'sponsor_id');
    }

    public function memberType()
    {
        return $this->belongsTo(CmMemberType::class,'member_type_id');
    }

    public function financialStatus()
    {
        return $this->belongsTo(CmFinancialStatus::class,'financial_status_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Sponser')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
