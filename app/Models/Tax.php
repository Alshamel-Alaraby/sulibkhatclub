<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PointOfSale\Entities\OrderItem;
use Modules\PointOfSale\Entities\Product;

class Tax extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_taxes';

    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'tax_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'tax_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->items()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'items',
                'count' => $this->items()->count(),
                'ids' => $this->items()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->products()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'products',
                'count' => $this->products()->count(),
                'ids' => $this->products()->pluck('title')->toArray(),
            ];
        }
        return $relationsWithChildren;
    }



    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Tax')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
