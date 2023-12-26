<?php

namespace Modules\Booking\Entities;

use App\Models\DocumentHeaderDetail;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class UnitStatus extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'booking_unit_statuses';
    protected $guarded = ['id'];

    public function documentHeaderDetails()
    {
        return $this->hasMany(DocumentHeaderDetail::class,'unit_status_id');

    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'unit_status_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->documentHeaderDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'document Header Details',
                'count' => $this->documentHeaderDetails()->count(),
                'ids' => $this->documentHeaderDetails()->pluck('id')->toArray(),
            ];
        }
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
            ->useLogName('Booking Unit')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
