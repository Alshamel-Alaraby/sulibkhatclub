<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BoardsRent\Entities\Panel;
use Modules\RealEstate\Entities\RlstBuilding;

class Street extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_streets';

    protected $fillable = ['name', 'name_e', "avenue_id", 'is_active', 'company_id'];

    protected $casts = [
        // 'is_active' => '\App\Enums\IsActive',
    ];

    public function scopeData($query)
    {
        return $query->select(
            'id',
            'name',
            'name_e',
            'is_active',
            'avenue_id',
        )->with('avenue:id,name,name_e');
    }

    public function avenue()
    {
        return $this->belongsTo(Avenue::class, 'avenue_id');
    }

    public function customerBranches()
    {
        return $this->hasMany(\App\Models\CustomerBranch::class, 'street_id');
    }

    public function panels()
    {
        return $this->hasMany(Panel::class, 'street_id');
    }

    public function buildings()
    {
        return $this->hasMany(RlstBuilding::class, 'street_id');
    }



    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->customerBranches()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'customer-Branches',
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
        if ($this->buildings()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildings',
                'count' => $this->buildings()->count(),
                'ids' => $this->buildings()->pluck('name')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Street')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
