<?php

namespace Modules\HMS\Entities;

use App\Traits\LogTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class HMSDoctorsWorkTime extends Model
{
    use HasFactory,SoftDeletes,LogTrait;


    protected $guarded = ['id'];

    public function doctor(){
        return $this->belongsTo(HMSDoctor::class,'doctor_id');
    }
    public function day(){
        return $this->belongsTo(HMSDay::class,'day_id');
    }

    public function room(){
        return $this->belongsTo(HMSRoom::class,'room_id');
    }

    public function getFromAttribute($value){
        return Carbon::parse($value)->format('H:i');
    }

    public function getToAttribute($value){
        return Carbon::parse($value)->format('H:i');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('DoctorsWorkTime')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
