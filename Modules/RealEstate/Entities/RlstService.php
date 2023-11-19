<?php

namespace Modules\RealEstate\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogTrait;


class RlstService extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_services';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e');        
    }

    public function units()
    {
        return $this->belongsToMany(\Modules\RealEstate\Entities\RlstUnit::class, 'rlst_unit_services', 'service_id', 'unit_id');    
    }

    public function unitService()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstUnitService::class, 'service_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->unitService()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'units',
                'count' => $this->unitService()->count(),
                'ids' => $this->unitService()->pluck('rlst_unit_services.id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Rlst Service')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }


}
