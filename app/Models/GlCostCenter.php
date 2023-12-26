<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogTrait;
use Spatie\Activitylog\LogOptions;

class GlCostCenter extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'gl_cost_centers';

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            if(!$model->parent_id){
                $model->parent_id = $model->id;
                $model->save();
            }

        });

    }

    public function scopeData($query)
    {
        return $query
            ->select(
                'id',
                'name',
                'name_e',
                'parent_id',
            )
            ->with([
                'parent:id,name,name_e',
            ]);


    }

    public function parent()
    {
        return $this->belongsTo(\App\Models\GlCostCenter::class, 'parent_id');
    }


    public function children()
    {
        return $this->hasMany(\App\Models\GlCostCenter::class, 'parent_id');
    }

    public function buildings()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'main_cost_center_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->children()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'children',
                'count' => $this->children()->count(),
                'ids' => $this->children()->pluck('name')->toArray(),
            ];
        }
        if ($this->buildings()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildings',
                'count' => $this->buildings()->count(),
                'ids' => $this->buildings()->pluck('name')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }



    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('GL Cost Centers')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
