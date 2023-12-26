<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CarRent\Entities\CarCar;
use Spatie\Activitylog\LogOptions;

class Color extends Model
{
    use HasFactory, SoftDeletes, LogTrait;
    protected $table = 'general_colors';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e', 'is_active');
    }

    protected $casts = [
        'is_active' => '\App\Enums\IsActive',
    ];

    public function colorExterior()
    {
        return $this->hasMany(CarCar::class, 'color_exterior_id');
    }
    public function colorInterior()
    {
        return $this->hasMany(CarCar::class, 'color_interior_id');
    }
    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->colorExterior()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'colorExterior',
                'count' => $this->colorExterior()->count(),
                'ids' => $this->colorExterior()->pluck('plate_number')->toArray(),
            ];
        }
        if ($this->colorInterior()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'colorInterior',
                'count' => $this->colorInterior()->count(),
                'ids' => $this->colorInterior()->pluck('plate_number')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Color')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
