<?php

namespace Modules\HMS\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class HMSPrescriptionsDiagnosisTest extends Model
{
    use HasFactory,SoftDeletes,LogTrait;

    protected $guarded = ["id"];

    public function prescription(){
        return $this->belongsTo(HMSPrescription::class,'prescription_id');
    }

    public function diagnosis_test(){
        return $this->belongsTo(HMSDiagnosisTest::class,'diagnosis_test_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('PrescriptionsDiagnosisTest')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
