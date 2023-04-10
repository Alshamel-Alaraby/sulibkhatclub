<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class RlstWallet extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $fillable = [
        'name',
        'name_e',

    ];

    // relations

    public function owners()
    {
        return $this->belongsToMany(\Modules\RealEstate\Entities\RlstOwner::class, 'rlst_wallet_owners', 'wallet_id', 'owner_id');

    }
    
    public function walletOwner()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstWalletOwner::class, 'wallet_id');
    }
    public function buildingWallet()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuildingWallet::class, 'wallet_id');
    }

    public function buildings()
    {
        return $this->belongsToMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'rlst_building_wallet', 'wallet_id', 'building_id');

    }



    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Real Estate Wallets')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }


}
