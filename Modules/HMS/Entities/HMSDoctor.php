<?php

namespace Modules\HMS\Entities;

use App\Models\Employee;
use App\Models\User;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class HMSDoctor extends Model
{
    use HasFactory,SoftDeletes,LogTrait;

    protected $guarded = ["id"];

    protected $table='h_m_s_doctors';

    public function specialty(){
        return $this->belongsTo(HMSSpecialty::class,'specialty_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function prescriptions(){
        return $this->hasMany(HMSPrescription::class,'doctor_id');
    }

    public function work_times(){
        return $this->hasMany(HMSDoctorsWorkTime::class,'doctor_id');
    }

    protected static function booted()
    {
        static::addGlobalScope(function ($query) {
            $user = request()->user();
            if ($user && $user->type == 'doctor') {
                $query->whereUserId($user->id);
            }
        });
    }
    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Doctor')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
