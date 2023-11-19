<?php

namespace Modules\RealEstate\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogTrait;
use Modules\RealEstate\Entities\RlstBuilding;


class RlstGuard extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_guards';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select('id', 'name', 'name_e', 'building_id')
            ->with([
                'building:id,name,name_e'
            ]);
            
    }

    public function building()
    {
        return $this->belongsTo(RlstBuilding::class, 'building_id');
    }


    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Rlst Guard')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }


}
