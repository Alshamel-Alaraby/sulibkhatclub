<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes, LogTrait   ;
    protected $table = 'general_modules';

    protected $fillable = [
        'name',
        'name_e',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'App\Enums\IsActive',
    ];

    // relations
    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_module', 'module_id', 'company_id');
    }

    public function getHaveChildrenAttribute()
    {
        return static::where("parent_id", $this->id)->count() > 0;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Module')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function buildings()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class);
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

}
