<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class GeneralInsuranceSetting extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_insurance_settings';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select(
            'id',
            'title',
            'title_e',
            'db_table',
            'db_field',
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('general insurance setting')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
