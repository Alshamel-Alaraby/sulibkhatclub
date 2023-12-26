<?php

namespace Modules\CarRent\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class CarFuelType extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'car_fuel_types';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select(
                'id',
                'name',
                'name_e',
            );
    }

    public function cars()
    {
        return $this->hasMany(CarCar::class, 'fuel_type_id');
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
            ->useLogName('Car Fuel Types')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
