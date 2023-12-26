<?php

namespace Modules\RealEstate\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogTrait;

class RlstEvacuationType extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_evacuation_types';

    protected $fillable = ['name', 'name_e', 'parent_id'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            if(empty($model->parent_id) || !$model->parent_id){
                $model->parent_id = $model->id;
                $model->save();
            }
        });

    }


    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e','parent_id')
            ->with([
                'parent:id,name,name_e',
            ]);

    }


    // relations

    public function parent()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstEvacuationType::class, 'parent_id');

    }

    public function children()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstEvacuationType::class, 'parent_id');

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
        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('EvacuationType')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
