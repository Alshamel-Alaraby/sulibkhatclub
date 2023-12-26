<?php

namespace App\Models;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\BoardsRent\Entities\Panel;
use Modules\CarRent\Entities\CarCar;
use Modules\RealEstate\Entities\RlstBuilding;
use Modules\RealEstate\Entities\RlstCustomer;
use Modules\RealEstate\Entities\RlstTenant;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Country extends Model implements HasMedia
{
    use HasFactory, MediaTrait, SoftDeletes, LogTrait;

    protected $table = "general_countries";

    protected $fillable = [
        'name',
        'name_e',
        'is_active',
        'is_default',
        "phone_key",
        'national_id_length',
        "long_name",
        "long_name_e",
        "short_code",
        "company_id",
    ];

    public function scopeData($query)
    {
        return $query
            ->select('id', 'name',
                'name_e',
                'is_active',
                'is_default',
                "phone_key",
                'national_id_length',
                "long_name",
                "long_name_e",
                "short_code")
            ->with('media');

    }

    // relations

    public function governorates()
    {
        return $this->hasMany(\App\Models\Governorate::class, "country_id");
    }

    public function externalSalesmen()
    {
        return $this->hasMany(\App\Models\ExternalSalesmen::class, "country_id");
    }

    public function avenues()
    {
        return $this->hasMany(\App\Models\Avenue::class);
    }

    public function cities()
    {
        return $this->hasMany(\App\Models\City::class);
    }

    public function rlstOwners()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstOwner::class);
    }

    public function banks()
    {
        return $this->hasMany(\App\Models\Bank::class);
    }

    public function customerBranches()
    {
        return $this->hasMany(\App\Models\CustomerBranch::class, "country_id");
    }


    public function Panels()
    {
        return $this->hasMany(Panel::class, 'country_id');
    }
    public function cars()
    {
        return $this->hasMany(CarCar::class, 'country_id');
    }
    public function customers()
    {
        return $this->hasMany(GeneralCustomer::class, 'country_id');
    }
    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'country_id');
    }

    public function buildings()
    {
        return $this->hasMany(RlstBuilding::class, 'country_id');
    }

    public function RlstCustomers()
    {
        return $this->hasMany(RlstCustomer::class, 'country_id');
    }
    public function RlstTenants()
    {
        return $this->hasMany(RlstTenant::class, 'country_id');
    }


    // logs activities

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Country')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }



    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->RlstTenants()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'RlstTenants',
                'count' => $this->RlstTenants()->count(),
                'ids' => $this->RlstTenants()->pluck('name')->toArray(),
            ];
        }
        if ($this->RlstCustomers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'RlstCustomers',
                'count' => $this->RlstCustomers()->count(),
                'ids' => $this->RlstCustomers()->pluck('name')->toArray(),
            ];
        }
        if ($this->buildings()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildings',
                'count' => $this->buildings()->count(),
                'ids' => $this->buildings()->pluck('name')->toArray(),
            ];
        }
        if ($this->governorates()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'governorates',
                'count' => $this->governorates()->count(),
                'ids' => $this->governorates()->pluck('name')->toArray(),
            ];
        }
        if ($this->externalSalesmen()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'externalSalesmen',
                'count' => $this->externalSalesmen()->count(),
                'ids' => $this->externalSalesmen()->pluck('name')->toArray(),
            ];
        }
        if ($this->avenues()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'avenues',
                'count' => $this->avenues()->count(),
                'ids' => $this->avenues()->pluck('name')->toArray(),
            ];
        }
        if ($this->cities()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'cities',
                'count' => $this->cities()->count(),
                'ids' => $this->cities()->pluck('name')->toArray(),
            ];
        }
        if ($this->rlstOwners()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'rlstOwners',
                'count' => $this->rlstOwners()->count(),
                'ids' => $this->rlstOwners()->pluck('name')->toArray(),
            ];
        }
        if ($this->banks()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'banks',
                'count' => $this->banks()->count(),
                'ids' => $this->banks()->pluck('name')->toArray(),
            ];
        }
        if ($this->customerBranches()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'customerBranches',
                'count' => $this->customerBranches()->count(),
                'ids' => $this->customerBranches()->pluck('name')->toArray(),
            ];
        }
        if ($this->Panels()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'Panels',
                'count' => $this->Panels()->count(),
                'ids' => $this->Panels()->pluck('name')->toArray(),
            ];
        }
        if ($this->suppliers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'suppliers',
                'count' => $this->suppliers()->count(),
                'ids' => $this->suppliers()->pluck('name')->toArray(),
            ];
        }
        if ($this->cars()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'cars',
                'count' => $this->cars()->count(),
                'ids' => $this->cars()->pluck('plate_number')->toArray(),
            ];
        }
        if ($this->customers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'customers',
                'count' => $this->customers()->count(),
                'ids' => $this->customers()->pluck('plate_number')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }



}
