<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Modules\RealEstate\Entities\RlstUnit;
use Modules\RealEstate\Entities\RlstCategoryItem;


class RlstItem extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, LogTrait, MediaTrait;

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            /*
            ->select(
                "id",
                "name",
                "name_e",
                "code_number",
                "type",
                "price",
            )
            */
            ->with('unit:id,name,name_e', 'media', 'categories:id,name,name_e');
    }

    // relations
    public function unit()
    {
        return $this->belongsTo(RlstUnit::class, 'unit_id');
    }

    // relations

    public function categories()
    {
        return $this->belongsToMany(RlstCategoryItem::class, 'rlst_categories_item', 'item_id', 'category_item_id');

    }

    // public function hasChildren()
    // {

    //     return $this->categories()->count() > 0;
    // }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->categories()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'categories',
                'count' => $this->categories()->count(),
                'ids' => $this->categories()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
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
