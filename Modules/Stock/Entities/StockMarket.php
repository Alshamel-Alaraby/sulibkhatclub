<?php

namespace Modules\Stock\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogTrait;

class StockMarket extends Model
{
    use HasFactory, LogTrait;

    protected $fillable = [
        'name',
        'name_e',
        'company_id',
    ];

    public function stocks(){

        return $this->hasMany(Stock::class, 'stock_market_id', 'id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->stocks()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'stocks',
                'count' => $this->stocks()->count(),
                'ids' => $this->stocks()->pluck('name')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }
}
