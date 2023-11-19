<?php

namespace Modules\RealEstate\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogTrait;


class RlstBuildingCategory extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_building_categories';

    protected $guarded = ['id'];


    public function scopeData($query)
    {
        return $query
        ->select('id', 'name', 'name_e')
        ->with([
                'buildings:id,name,name_e'
            ]);
    }


    public function buildings()
    {
        return $this->hasMany(RlstBuilding::class, 'building_category_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->buildings()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildings',
                'count' => $this->buildings()->count(),
                'ids' => $this->buildings()->pluck('rlst_buildings.id')->toArray(),
            ];
        }

        return $relationsWithChildren;

    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Building Category')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
