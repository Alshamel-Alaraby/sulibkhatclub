<?php

namespace App\Models;

use App\Models\Sector;
use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PointOfSale\Entities\Order;
use Modules\PointOfSale\Entities\OrderItem;
use Modules\RecievablePayable\Entities\RpOpeningBalance;
use Spatie\MediaLibrary\HasMedia;

class GeneralCustomer extends Model implements HasMedia
{

    use HasFactory, MediaTrait, LogTrait;
    protected $table = 'general_customers';

    protected $guarded = ['id'];

    protected $appends = ['item_sold', 'sub_total', 'tax', 'discount', 'total', 'item_purchased', 'total_purchased'];




    public function opening_balances()
    {
        return $this->hasMany(RpOpeningBalance::class, 'customer_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);

    }

    public function customer_main_category()
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_main_category_id');
    }

    public function customer_sub_category()
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_sub_category_id');
    }

    public function salesmen()
    {
        return $this->belongsTo(Salesman::class, 'salesman_id');
    }

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class, 'customer_id');
    }
    public function customerSource()
    {
        return $this->belongsTo(GeneralCustomerSource::class, 'customer_source_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->opening_balances()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'opening_balances',
                'count' => $this->opening_balances()->count(),
                'ids' => $this->opening_balances()->pluck('id')->toArray(),
            ];
        }
        if ($this->orders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'orders',
                'count' => $this->orders()->count(),
                'ids' => $this->orders()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function getItemSoldAttribute($key)
    {

        $order_id = $this->orders->where('customer_id', $this->id)->pluck('id')->toArray();

        return OrderItem::where('type', 'sales')->whereIn('order_id', $order_id)
            ->whereHas('order', function ($query) {
                $query->where('order_type', 'sales')
                    ->where('status', 'done');
            })->sum('quantity');
    }

    public function getSubTotalAttribute($key)
    {
        $order_id = $this->orders->where('customer_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($order_id) {
                $query->where('type', 'sales')->whereIn('order_id', $order_id);
            })->sum('sub_total');
    }

    public function getTaxAttribute($key)
    {
        $order_id = $this->orders->where('customer_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($order_id) {
                $query->where('type', 'sales')->whereIn('order_id', $order_id);
            })->sum('total_tax');
    }

    public function getDiscountAttribute($key)
    {
        $order_id = $this->orders->where('customer_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($order_id) {
                $query->where('type', 'sales')->whereIn('order_id', $order_id);
            })->sum('all_discount');
    }
    public function getTotalAttribute($key)
    {
        $order_id = $this->orders->where('customer_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($order_id) {
                $query->where('type', 'sales')->whereIn('order_id', $order_id);
            })->sum('total');
    }

    public function getItemPurchasedAttribute($key)
    {
        $order_id = $this->orders->where('customer_id', $this->id)->pluck('id')->toArray();

        return OrderItem::where('type', 'receiving')->whereIn('order_id', $order_id)
            ->whereHas('order', function ($query) {
                $query->where('order_type', 'receiving')
                    ->where('status', 'done')
                    ->where('type', 'supplier');
            })->sum('quantity');
    }
    public function getTotalPurchasedAttribute($key)
    {
        $order_id = $this->orders->where('customer_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'receiving')
            ->where('status', 'done')
            ->where('type', 'supplier')
            ->whereHas('items', function ($query) use ($order_id) {
                $query->where('type', 'receiving')->whereIn('order_id', $order_id);
            })->sum('total');
    }

}
