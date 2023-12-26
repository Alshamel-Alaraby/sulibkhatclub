<?php

namespace Modules\Booking\Entities;

use App\Models\DocumentHeaderDetail;
use App\Models\GeneralItem;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class Unit extends Model
{
    use HasFactory, SoftDeletes, LogTrait;

    protected $table = 'booking_units';
    protected $guarded = ['id'];

    // relations

    public function unitStatus()
    {
        return $this->belongsTo(\App\Models\Status::class, 'unit_status_id', 'id');
    }
    public function bookingUnitStatus()
    {
        return $this->belongsTo(\Modules\Booking\Entities\UnitStatus::class, 'unit_status_id', 'id');
    }

    public function documentHeaderDetails()
    {
        return $this->hasMany(DocumentHeaderDetail::class, 'unit_id');
    }

    public function Detail()
    {
        return $this->hasOne(DocumentHeaderDetail::class, 'unit_id')->latest();
    }

    public function DetailFilter()
    {
        return $this->hasOne(DocumentHeaderDetail::class, 'unit_id');
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class, 'booking_floor_id');
    }

    public function generalItems()
    {
        return $this->hasMany(GeneralItem::class, 'unit_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->documentHeaderDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'document Header Details',
                'count' => $this->documentHeaderDetails()->count(),
                'ids' => $this->documentHeaderDetails()->pluck('date_from')->toArray(),
            ];
        }
        if ($this->Detail()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'Details',
                'count' => $this->Detail()->count(),
                'ids' => $this->Detail()->pluck('date_from')->toArray(),
            ];
        }
        if ($this->DetailFilter()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'DetailFilter',
                'count' => $this->DetailFilter()->count(),
                'ids' => $this->DetailFilter()->pluck('date_from')->toArray(),
            ];
        }
        if ($this->generalItems()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'general Items',
                'count' => $this->generalItems()->count(),
                'ids' => $this->generalItems()->pluck('name')->toArray(),
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
