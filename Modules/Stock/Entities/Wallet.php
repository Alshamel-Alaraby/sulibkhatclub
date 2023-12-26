<?php

namespace Modules\Stock\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory, LogTrait;
    protected $fillable = [
        'name',
        'name_e',
        'stock_balance'
    ];

    public function stock_opening_balances()
    {
        return $this->hasMany(OpenningBalance::class, 'wallet_id', 'id');
    }

    public function stock_sale_purchase_details()
    {
        return $this->hasMany(StockSalePurchaseDetail::class, 'wallet_id', 'id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->stock_opening_balances()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'stock_opening_balances',
                'count' => $this->stock_opening_balances()->count(),
                'ids' => $this->stock_opening_balances()->pluck('id')->toArray(),
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
