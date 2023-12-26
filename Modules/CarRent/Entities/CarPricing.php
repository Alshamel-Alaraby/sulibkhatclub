<?php

namespace Modules\CarRent\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class CarPricing extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'car_pricings';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select(
                'id',
                'daily_price',
                'weekly_price',
                'monthly_price',
                'car_id',
            )
            ->with(
                'car:id,plate_number,year_manufacture,chassis_number,cylinders,passengers',
            );
    }

 // relations
    public function car()
    {
        return $this->belongsTo(CarCar::class, 'car_id');
    }
   public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Car Pricing')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
