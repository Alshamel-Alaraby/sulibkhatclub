<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class GeneralInsuranceDocument extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_insurance_documents';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select(
            'id',
            'related_id',
            'insurance_type_id',
            'insurance_number',
            'insurance_company_id',
            'amount',
            'from_date',
            'to_date',
            'related_field',
        )->with('insuranceType:id,name,name_e', 'insuranceCompany:id,name,name_e');
    }

    public function insuranceType()
    {
        return $this->belongsTo(GeneralInsuranceType::class, 'insurance_type_id');
    }

    public function insuranceCompany()
    {
        return $this->belongsTo(InsuranceCompany::class, 'insurance_company_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('general insurance documents')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
