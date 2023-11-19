<?php

namespace Modules\RealEstate\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogTrait;


class RlstUnitService extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_unit_services';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select('id', 'unit_id', 'service_id', 'price')
            ->with([
                'unit:id,name,name_e,code',
                'service:id,name,name_e,code'
            ]);
    }


    public function service()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstService::class, 'service_id');
    }

    public function unit()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstUnit::class, 'unit_id');
    }



    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Unit Service')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
