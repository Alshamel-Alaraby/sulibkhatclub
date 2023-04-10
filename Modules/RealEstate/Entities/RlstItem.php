<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class RlstItem extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, LogTrait, MediaTrait;

    protected $fillable = [
        "name",
        "name_e",
        "code_number",
        "type",
        "unit_id",
        "price",

    ];

    // relations
    public function units()
    {
        return $this->belongsTo(\App\Models\Unit::class, 'unit_id');
    }

    // relations

    public function categories()
    {
        return $this->belongsToMany(RlstCategoryItem::class, 'rlst_categories_item', 'item_id', 'category_item_id');

    }

    // scopes
    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Rlst Item')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
