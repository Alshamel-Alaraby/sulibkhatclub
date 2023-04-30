<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use App\Traits\VideoLink;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;


class RlstUnit extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, LogTrait,MediaTrait, VideoLink;

    protected $guarded = ['id'];

    // relations

    public function unitStatus()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstUnitStatus::class);
    }

    public function building()
    {
        return $this->belongsTo(RlstBuilding::class);
    }

    public function owner()
    {
        return $this->belongsTo(RlstOwner::class);
    }

    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class);
    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Rlst Unit')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    // attributes

    protected function attachments(): Attribute
    {
        return Attribute::make(
            get:fn($value) => json_decode($value),
            set:fn($value) => json_encode($value),
        );
    }

    protected function properties(): Attribute
    {
        return Attribute::make(
            get:fn($value) => json_decode($value),
            set:fn($value) => json_encode($value),
        );
    }
}
