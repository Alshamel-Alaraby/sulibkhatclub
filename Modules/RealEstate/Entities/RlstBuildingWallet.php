<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class RlstBuildingWallet extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_building_wallet';
    protected $fillable = [
        'wallet_id',
        'company_id',
        'building_id',
        'bu_ty',
    ];

    public function scopeData($query)
    {
        return $query
            ->select('id',
                'wallet_id',
                'building_id',
                'bu_ty')
            ->with('wallet:id,name,name_e', 'building:id,name,name_e');
//        ,'owner:id,name,name_e','currency:id,link,model_id,model_type'
    }

    public function wallet()
    {
        return $this->belongsTo(RlstWallet::class, 'wallet_id');
    }

    public function building()
    {
        return $this->belongsTo(RlstBuilding::class, 'building_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Real Estate  Building Wallet')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
