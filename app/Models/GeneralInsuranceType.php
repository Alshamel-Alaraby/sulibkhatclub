<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class GeneralInsuranceType extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_insurance_types';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select(
            'id',
            'name',
            'name_e',
        );
    }

    public function GeneralInsuranceDocuments()
    {
        return $this->hasMany(GeneralInsuranceDocument::class, 'insurance_type_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->GeneralInsuranceDocuments()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'General Insurance Documents',
                'count' => $this->GeneralInsuranceDocuments()->count(),
                'ids' => $this->GeneralInsuranceDocuments()->pluck('name')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('general insurance types')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
