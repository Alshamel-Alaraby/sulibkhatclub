<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogTrait;

class ContractTemplate extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_contract_templates';

    protected $guarded = ['id'];


    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e','module');            
    }

    
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('ContractTemplate')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
