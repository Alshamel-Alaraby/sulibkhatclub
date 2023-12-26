<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Booking\Entities\Unit;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;


class GeneralItem extends Model
{
    use HasFactory,LogTrait,SoftDeletes;

    protected $guarded = ['id'];

    // relations
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }


    public function documentHeaderDetails()
    {
        return $this->hasMany(DocumentHeaderDetail::class, 'item_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->documentHeaderDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'documentHeaderDetails',
                'count' => $this->documentHeaderDetails()->count(),
                'ids' => $this->documentHeaderDetails()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }


   public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('General Item')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
