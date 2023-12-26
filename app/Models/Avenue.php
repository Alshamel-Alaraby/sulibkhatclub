<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\BoardsRent\Entities\Panel;
use Modules\RealEstate\Entities\RlstBuilding;

class Avenue extends Model
{
    use HasFactory, SoftDeletes, LogTrait;
    protected $table = 'general_avenues';

    protected $fillable = [
        'name',
        'name_e',
        'is_active',
        'country_id',
        'governorate_id',
        'city_id',
        "company_id",
    ];

    public function scopeData($query)
    {
        return $query->select('id', 'name',
            'name_e',
            'is_active',
            'country_id',
            'governorate_id',
            'city_id'
        )->with(['country:id,name,name_e', 'governorate:id,name,name_e', 'city:id,name,name_e']);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class, 'governorate_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function customerBranches()
    {
        return $this->hasMany(\App\Models\CustomerBranch::class, 'avenue_id');
    }
    public function streets()
    {
        return $this->hasMany(Street::class, 'avenue_id');
    }

    public function panels()
    {
        return $this->hasMany(Panel::class, 'avenue_id');
    }

    public function buildings()
    {
        return $this->hasMany(RlstBuilding::class, 'avenue_id');
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
        if ($this->customerBranches()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'customerBranches',
                'count' => $this->customerBranches()->count(),
                'ids' => $this->customerBranches()->pluck('name')->toArray(),
            ];
        }
        if ($this->streets()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'streets',
                'count' => $this->streets()->count(),
                'ids' => $this->streets()->pluck('name')->toArray(),
            ];
        }
        if ($this->panels()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'panels',
                'count' => $this->panels()->count(),
                'ids' => $this->panels()->pluck('name')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Avenue')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
