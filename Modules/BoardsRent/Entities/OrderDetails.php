<?php

namespace Modules\BoardsRent\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'category_id',
        'governorate_id',
        'package_id',
        'quantity',
        'from',
        'to',
        'price',
    ];

    protected $table ='boards_rent_order_details';

    public function order()
    {
        return $this->belongsTo(\Modules\BoardsRent\Entities\Order::class);
    }

    public function governorate()
    {
        return $this->belongsTo(\App\Models\Governorate::class);
    }

    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    public function package()
    {
        return $this->belongsTo(\Modules\BoardsRent\Entities\Package::class);
    }

}
