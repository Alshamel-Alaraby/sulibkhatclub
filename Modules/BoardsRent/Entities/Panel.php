<?php

namespace Modules\BoardsRent\Entities;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class Panel extends Model implements HasMedia
{
    use HasFactory, LogTrait, MediaTrait;

    protected $fillable = [
        'name',
        'name_e',
        'description',
        'description_e',
        'price',
        'code',
        'new_code',
        'size',
        'note',
        'face',
        'unit_status_id',
        "category_id",
        "country_id",
        "governorate_id",
        "city_id",
        "avenue_id",
        "street_id",
        "lat",
        "lng",

    ];
    protected $table = "boards_rent_panels";

    protected $casts = [
        'price' => 'array',
    ];

    public function unitStatus()
    {
        return $this->belongsTo(UnitStatus::class, 'unit_status_id');
    }

    public function category()
    {

        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }

    public function country()
    {

        return $this->belongsTo(\App\Models\Country::class, 'country_id');
    }

    public function governorate()
    {

        return $this->belongsTo(\App\Models\Governorate::class, 'governorate_id');
    }

    public function city()
    {
        return $this->belongsTo(\App\Models\City::class, 'city_id');
    }

    public function avenue()
    {
        return $this->belongsTo(\App\Models\Avenue::class, 'avenue_id');
    }

    public function street()
    {
        return $this->belongsTo(\App\Models\Street::class, 'street_id');
    }

    public function orderDetails()
    {
        return $this->belongsToMany(OrderDetails::class, 'boards_rent_order_detail_panel', 'panel_id', 'order_detail_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Panel')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    // attributes

    public function price(): Attribute
    {
        return Attribute::make(
            set:fn($value) => json_encode($value),
            get:fn($value) => json_decode($value, true),
        );

    }
}
