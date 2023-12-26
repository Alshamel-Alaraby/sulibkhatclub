<?php

namespace Modules\CarRent\Entities;

use App\Models\Brand;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class CarModel extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'car_models';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select(
                'id',
                'name',
                'name_e',
                'brand_id'
            )
            ->with('brand:id,name,name_e');
    }

    // relations
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function cars()
    {
        return $this->hasMany(CarCar::class, 'model_id');
    }
    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->cars()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'cars',
                'count' => $this->cars()->count(),
                'ids' => $this->cars()->pluck('plate_number')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Car Models')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
