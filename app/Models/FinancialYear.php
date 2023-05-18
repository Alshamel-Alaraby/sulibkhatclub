<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class FinancialYear extends Model
{
    use HasFactory, SoftDeletes, LogTrait;
    protected $table = 'general_financial_years';


    protected $fillable = [
        'name',
        'name_e',
        'start_date',
        'end_date',
        "company_id",
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // public function getIsActive()
    // {
    //     return $this->is_active == 1 ? 'Active' : 'Non Active';
    // }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Financial Year')
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
