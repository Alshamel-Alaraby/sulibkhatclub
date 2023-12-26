<?php

namespace Modules\CarRent\Entities;

use App\Models\Brand;
use App\Models\Color;
use App\Models\Country;
use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class CarCar extends Model implements HasMedia
{
    use HasFactory, LogTrait, MediaTrait;

    protected $table = 'car_cars';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select(
                'id',
                'plate_number',
                'brand_id',
                'model_id',
                'year_manufacture',
                'chassis_number',
                'transmission_id',
                'sunroof_id',
                'body_type_id',
                'cylinders',
                'country_id',
                'color_exterior_id',
                'fuel_type_id',
                'color_interior_id',
                'seats_material_id',
                'passengers',
                'financial_value',
                'current_odometer',
                'specifications',
                'depreciation_percentage',
                'barcode',
                'jpscode',
                'daily_price',
                'weekly_price',
                'monthly_price',
            )
            ->with(
                'brand:id,name,name_e',
                'model:id,name,name_e',
                'transmission:id,name,name_e',
                'sunroof:id,name,name_e',
                'body_type:id,name,name_e',
                'country:id,name,name_e',
                'exterior_color:id,name,name_e',
                'interior_color:id,name,name_e',
                'fuel_type:id,name,name_e',
                'seats_material:id,name,name_e',
                'media'
                // 'specifications:id,name,name_e',

            );

    }

    protected $casts = [
        'specifications' => 'array',
    ];

    // relations
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'model_id');
    }
    public function transmission()
    {
        return $this->belongsTo(CarTransmission::class, 'transmission_id');
    }

    public function sunroof()
    {
        return $this->belongsTo(CarSunroof::class, 'sunroof_id');
    }

    public function body_type()
    {
        return $this->belongsTo(CarBodyType::class, 'body_type_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function exterior_color()
    {
        return $this->belongsTo(Color::class, 'color_exterior_id');
    }

    public function interior_color()
    {
        return $this->belongsTo(Color::class, 'color_interior_id');
    }

    public function fuel_type()
    {
        return $this->belongsTo(CarFuelType::class, 'fuel_type_id');
    }

    public function seats_material()
    {
        return $this->belongsTo(CarSeatsMaterial::class, 'seats_material_id');
    }
    public function specifications()
    {
        return $this->belongsTo(CarSpecification::class, 'specifications');
    }

    public function car_pricings()
    {
        return $this->hasMany(CarPricing::class, 'car_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Car Car')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function setSpecificationsAttribute($value)
    {
        $this->attributes['specifications'] = json_encode($value);
    }
    public function getSpecificationsAttribute($value)
    {
        return json_decode($value);
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->car_pricings()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'car pricing',
                'count' => $this->car_pricings()->count(),
                'ids' => $this->car_pricings()->pluck('id')->toArray(),
            ];
        }
        return $relationsWithChildren;
    }

    public function scopeGeneralFilter($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                $q->when($request->brand_id, function ($q) use ($request) {
                    $q->where('brand_id', $request->brand_id);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->model_id, function ($q) use ($request) {
                    $q->where('model_id', $request->model_id);
                });

            })->where(function ($q) use ($request) {
                $q->when($request->year_manufacture, function ($q) use ($request) {
                    $q->where('year_manufacture', $request->year_manufacture);
                });
            })
            ->where(function ($q) use ($request) {
                $q->when($request->sunroof_id, function ($q) use ($request) {
                    $q->where('sunroof_id', $request->sunroof_id);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->body_type_id, function ($q) use ($request) {
                    $q->where('body_type_id', $request->body_type_id);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->cylinders, function ($q) use ($request) {
                    $q->where('cylinders', $request->cylinders);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->seats_material_id, function ($q) use ($request) {
                    $q->where('seats_material_id', $request->seats_material_id);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->passengers, function ($q) use ($request) {
                    $q->where('passengers', $request->passengers);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->specifications, function ($q) use ($request) {
                    $q->whereJsonContains('specifications', $request->specifications);
                });
            });

        });
    }
}
