<?php

namespace Modules\ClubMembers\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class Status extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $guarded = [];
    protected $table = 'cm_statues';

    public function members()
    {
        return $this->hasMany(\Modules\ClubMembers\Entities\CmMember::class, 'status_id');
    }
}
