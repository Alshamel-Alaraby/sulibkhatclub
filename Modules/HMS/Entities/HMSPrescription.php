<?php

namespace Modules\HMS\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class HMSPrescription extends Model
{
    use HasFactory,SoftDeletes,LogTrait;

    protected $guarded = ["id"];

    public function patient(){
        return $this->belongsTo(HMSPatient::class,'patient_id');
    }

    public function doctor(){
        return $this->belongsTo(HMSDoctor::class,'doctor_id');
    }

    public function appointment(){
        return $this->belongsTo(HMSAppointment::class,'appointment_id');
    }

    public function drug_list(){
        return $this->hasMany(HMSPrescriptionsDrug::class,'prescription_id');
    }

    public function diagnosis_list(){
        return $this->hasMany(HMSPrescriptionsDiagnosisTest::class,'prescription_id');
    }

    protected static function booted()
    {
        static::addGlobalScope(function ($query) {
            $user = request()->user();
            if ($user && $user->type == 'doctor') {
                $query->where('doctor_id', $user->doctor->id);
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Prescription')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
