<?php

namespace Modules\HMS\Entities;

use App\Models\User;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class HMSPatient extends Model
{
    use HasFactory,SoftDeletes,LogTrait;

    protected $guarded = ["id"];


    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function prescriptions(){
        return $this->hasMany(HMSPrescription::class,'patient_id');
    }

    public function appointments(){
        return $this->hasMany(HMSAppointment::class,'patient_id');
    }

    public function getImageAttribute($value){
        return $value ?? '/images/patient-icon.png';
    }

    public function health_history(){
        return $this->hasMany(HMSPatientHealthHistory::class,'patient_id');
    }

    public function medical_files(){
        return $this->hasMany(HMSPatientMedicalFile::class,'patient_id');
    }

    protected static function booted()
    {
        static::addGlobalScope(function ($query) {
            $user = request()->user();
            if ($user && $user->type == 'patient') {
                $query->whereUserId($user->id);
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Patient')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
