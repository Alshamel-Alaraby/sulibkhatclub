<?php

namespace Modules\HMS\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class HMSRoomsCategory extends Model
{
    use HasFactory,SoftDeletes,LogTrait;

    protected $guarded = ["id"];

    public function children()
    {
        return $this->hasMany(HMSRoomsCategory::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(HMSRoomsCategory::class, 'parent_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('RoomsCategory')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
