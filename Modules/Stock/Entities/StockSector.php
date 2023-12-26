<?php

namespace Modules\Stock\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogTrait;

class StockSector extends Model
{
    use HasFactory, LogTrait;

    protected $fillable = [
        'name',
        'name_e',
        'company_id',
    ];

    public function stocks(){

        return $this->hasMany(Stock::class, 'sector_id', 'id');
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
