<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RlstView extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $table = 'rlst_views';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e');

    }

    public function RlstUnits()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstUnit::class, 'view');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->RlstUnits()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'RlstUnits',
                'count' => $this->RlstUnits()->count(),
                'ids' => $this->RlstUnits()->pluck('name')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }
    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Unit Type')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
