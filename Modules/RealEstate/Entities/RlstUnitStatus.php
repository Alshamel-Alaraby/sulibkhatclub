<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Modules\RealEstate\Entities\RlstUnit;

class RlstUnitStatus extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_default' => '\App\Enums\IsDefault',
        'is_active' => '\App\Enums\IsActive',
    ];

    public function scopeData($query)
    {
        return $query

            ->select(
                'id',
                'name',
                'name_e',
                'is_default',
                'is_active',
            )
            //->with('units:id,code,name,name_e')
            ->whereNull('deleted_at');

    }

    public function units()
    {
        return $this->hasMany(RlstUnit::class, 'unit_status_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->units()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'units',
                'count' => $this->units()->count(),
                'ids' => $this->units()->pluck('name')->toArray(),
            ];
        }
        return $relationsWithChildren;
    }


public function getActivitylogOptions(): LogOptions
{
    $user = auth()->user()->id ?? "system";

    return \Spatie\Activitylog\LogOptions::defaults()
        ->logAll()
        ->useLogName('Real Estate Unit Status')
        ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
}





}
