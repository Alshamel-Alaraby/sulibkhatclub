<?php

namespace Modules\RealEstate\Entities;

use App\Models\Avenue;
use App\Models\City;
use App\Models\Country;
use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use App\Traits\VideoLink;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class RlstBuilding extends Model implements HasMedia
{
    use HasFactory, LogTrait, MediaTrait, VideoLink;

    protected $guarded = ['id'];

    // attributes
    public function setPropertiesAttribute($value)
    {
        $this->attributes['properties'] = json_encode($value);
    }
    public function getPropertiesAttribute($value)
    {
        return json_decode($value);
    }

    public function setAttachmentsAttribute($value)
    {
        $this->attributes['attachments'] = json_encode($value);
    }

    public function getAttachmentsAttribute($value)
    {
        return json_decode($value);
    }

    // relations
    public function buildingWallet()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuildingWallet::class, 'wallet_id');
    }

    public function module()
    {
        return $this->belongsTo(\App\Models\Module::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function avenue()
    {
        return $this->belongsTo(Avenue::class);
    }

    // public function hasChildren()
    // {

    //     return $this->buildingWallet()->count() > 0;
    // }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->buildingWallet()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'building Wallet',
                'count' => $this->buildingWallet()->count(),
                'ids' => $this->buildingWallet()->pluck('id')->toArray()
            ];
        }
       

        return $relationsWithChildren;
    }

}
