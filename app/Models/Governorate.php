<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BoardsRent\Entities\Package;
// use Modules\BoardsRent\Entities\OrderDetails;
use Modules\BoardsRent\Entities\Panel;
use Modules\RealEstate\Entities\RlstBuilding;

class Governorate extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_governorates';
    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select('id',
                'name',
                'name_e',
                'is_default',
                'is_active',
                'phone_key',
                'country_id')
            ->with('country:id,name,name_e');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, "country_id");
    }

    public function cities()
    {
        return $this->hasMany(City::class, 'governorate_id');
    }

    public function avenues()
    {
        return $this->hasMany(Avenue::class, 'governorate_id');
    }
    public function customerBranches()
    {
        return $this->hasMany(\App\Models\CustomerBranch::class, 'governorate_id');
    }

    public function panels()
    {
        return $this->hasMany(Panel::class, 'governorate_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\OrderDetails::class);
    }

    public function documentHeaderDetails()
    {
        return $this->hasMany(DocumentHeaderDetail::class, 'governorate_id');
    }

    public function packages()
    {
        return $this->hasMany(Package::class, 'governorate_id');
    }

    public function buildings()
    {
        return $this->hasMany(RlstBuilding::class, 'governorate_id');
    }



    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->buildings()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildings',
                'count' => $this->buildings()->count(),
                'ids' => $this->buildings()->pluck('name')->toArray(),
            ];
        }
        if ($this->cities()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'cities',
                'count' => $this->cities()->count(),
                'ids' => $this->cities()->pluck('name')->toArray(),
            ];
        }
        if ($this->avenues()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'avenues',
                'count' => $this->avenues()->count(),
                'ids' => $this->avenues()->pluck('name')->toArray(),
            ];
        }
        if ($this->packages()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'packages',
                'count' => $this->packages()->count(),
                'ids' => $this->packages()->pluck('name')->toArray(),
            ];
        }
        if ($this->customerBranches()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'customerBranches',
                'count' => $this->customerBranches()->count(),
                'ids' => $this->customerBranches()->pluck('name')->toArray(),
            ];
        }
        if ($this->panels()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'panels',
                'count' => $this->panels()->count(),
                'ids' => $this->panels()->pluck('name')->toArray(),
            ];
        }
        if ($this->orderDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'orderDetails',
                'count' => $this->orderDetails()->count(),
                'ids' => $this->orderDetails()->pluck('from')->toArray(),
            ];
        }
        if ($this->documentHeaderDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'document-Header-Details',
                'count' => $this->documentHeaderDetails()->count(),
                'ids' => $this->documentHeaderDetails()->pluck('date_from')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }




    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Governorate')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
