<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Sector extends Model
{
    use LogTrait;

    protected $table = 'general_sectors';

    protected $fillable = [
        'id',
        'name',
        'name_e',
        'parent_id',
        'company_id',
    ];

    public function scopeData($query)
    {
        return $query->select(
            'id',
            'name',
            'name_e',
            'parent_id',
        )->with(['parent:id,name,name_e']);
    }

    protected $appends = ['haveChildren'];

    // relations

    public function getHaveChildrenAttribute()
    {
        return static::where("parent_id", $this->id)->count() > 0;
    }

    public function parent()
    {
        return $this->belongsTo(Sector::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Sector::class, 'parent_id');
    }

    public function customers()
    {
        return $this->hasMany(GeneralCustomer::class, 'sector_id');
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'sector_id');
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
        if ($this->suppliers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'suppliers',
                'count' => $this->suppliers()->count(),
                'ids' => $this->suppliers()->pluck('name')->toArray(),
            ];
        }
        if ($this->customers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'customers',
                'count' => $this->customers()->count(),
                'ids' => $this->customers()->pluck('name')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Sector')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
