<?php

namespace Modules\HMS\Entities;

use App\Models\Branch;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class HMSRoom extends Model
{
    use HasFactory,SoftDeletes,LogTrait;

    protected $guarded = ["id"];

    public function rooms_category(){
        return $this->belongsTo(HMSRoomsCategory::class,'rooms_category_id');
    }

    public function branch(){
        return $this->belongsTo(Branch::class,'branch_id');
    }
    public function schedule(){
        return $this->hasMany(HMSRoomsSchedule::class,'room_id');
    }
    public function doctor_work_times(){
        return $this->hasMany(HMSDoctorsWorkTime::class,'room_id');
    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Room')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
