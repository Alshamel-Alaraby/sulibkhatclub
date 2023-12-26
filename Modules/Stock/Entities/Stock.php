<?php

namespace Modules\Stock\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory, LogTrait;

    protected $fillable = [
        'name',
        'name_e',
        'stock_market_id',
        'sector_id',
        'company_id',
        'symbol',
    ];

    public function stockMarket()
    {
        return $this->belongsTo(StockMarket::class, 'stock_market_id', 'id');
    }
    public function stockSector()
    {
        return $this->belongsTo(StockSector::class, 'sector_id', 'id');
    }

    public function closing()
    {
        return $this->belongsTo(ClosingBalance::class, 'stock_id', 'id');
    }

    public function sspd()
    {
        return $this->hasMany(StockSalePurchaseDetail::class, 'stock_id');
    }

    public function closing_balances()
    {
        return $this->hasMany(ClosingBalance::class, 'stock_id', 'id');
    }

    public function opening_balances()
    {
        return $this->hasMany(OpenningBalance::class, 'stock_id', 'id');
    }

    public function stock_sale_purchase_details()
    {
        return $this->hasMany(StockSalePurchaseDetail::class, 'stock_id', 'id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->sspd()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'sspd',
                'count' => $this->sspd()->count(),
                'ids' => $this->sspd()->pluck('id')->toArray(),
            ];
        }
        if ($this->closing_balances()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'closing_balances',
                'count' => $this->closing_balances()->count(),
                'ids' => $this->closing_balances()->pluck('date')->toArray(),
            ];
        }
        if ($this->opening_balances()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'opening_balances',
                'count' => $this->opening_balances()->count(),
                'ids' => $this->opening_balances()->pluck('id')->toArray(),
            ];
        }
        if ($this->stock_sale_purchase_details()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'stock_sale_purchase_details',
                'count' => $this->stock_sale_purchase_details()->count(),
                'ids' => $this->stock_sale_purchase_details()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

}
