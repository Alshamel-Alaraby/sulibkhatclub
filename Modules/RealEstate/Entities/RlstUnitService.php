<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class RlstUnitService extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_unit_services';

    protected $guarded = ['id'];

    protected $appends = ['unit_name'];

    protected $hidden = ['unit'];


    public function scopeData($query)
    {
        return $query->select('id', 'unit_id', 'service_id', 'default_price', 'from_date')
            ->with([
                'unit:id,name,name_e',
                'service:id,name,name_e'
            ]);
    }

    public function getUnitNameAttribute(){
        $unit = $this->unit;
        return ['name' => $unit->name , 'name_e' => $unit->name_e];
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
