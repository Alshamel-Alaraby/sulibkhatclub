<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\RecievablePayable\Entities\RpBreakDown;
use Modules\RecievablePayable\Entities\RpOpeningBalance;
use Modules\Stock\Entities\OpenningBalance;

class Currency extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_currencies';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select(
                'id', 'name', 'name_e', 'code', 'code_e', 'fraction', 'fraction_e', 'fraction_no', 'symbol', 'symbol_e', 'is_default', 'is_active');
    }

    public function rp_break_downs()
    {
        return $this->hasMany(RpBreakDown::class, 'currency_id');
    }

    public function opening_balances()
    {
        return $this->hasMany(RpOpeningBalance::class, 'currency_id');
    }

    public function stock_opening_balances()
    {
        return $this->hasMany(OpenningBalance::class, 'currency_id', 'id');
    }

    protected function buildings()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'building_currency_id');
    }

    public function RlstInstallments()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstInstallment::class, 'currency_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->RlstInstallments()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'RlstInstallments',
                'count' => $this->RlstInstallments()->count(),
                'ids' => $this->RlstInstallments()->pluck('date')->toArray(),
            ];
        }
        if ($this->buildings()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildings',
                'count' => $this->buildings()->count(),
                'ids' => $this->buildings()->pluck('name')->toArray(),
            ];
        }
        if ($this->rp_break_downs()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'rp_break_downs',
                'count' => $this->rp_break_downs()->count(),
                'ids' => $this->rp_break_downs()->pluck('rate')->toArray(),
            ];
        }
        if ($this->opening_balances()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'opening_balances',
                'count' => $this->opening_balances()->count(),
                'ids' => $this->opening_balances()->pluck('date')->toArray(),
            ];
        }
        if ($this->stock_opening_balances()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'stock_opening_balances',
                'count' => $this->stock_opening_balances()->count(),
                'ids' => $this->stock_opening_balances()->pluck('id')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }



    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Currency')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
