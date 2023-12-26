<?php

namespace Modules\HMS\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class HMSRoomsSchedule extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];


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

}
