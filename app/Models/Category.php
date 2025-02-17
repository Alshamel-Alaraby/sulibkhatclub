<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BoardsRent\Entities\Package;
use Modules\BoardsRent\Entities\Panel;
use Modules\PointOfSale\Entities\Order;
use Modules\PointOfSale\Entities\OrderItem;
use Spatie\Activitylog\LogOptions;

class Category extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_categories';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e');
    }

    protected $appends = ['item_sold', 'sub_total', 'tax', 'discount', 'total','item_purchased','total_purchased'];


    public function orderDetails()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\OrderDetails::class);
    }

    public function products()
    {
        return $this->hasMany(\Modules\PointOfSale\Entities\Product::class, 'category_id');
    }

    public function documentHeaderDetails()
    {
        return $this->hasMany(DocumentHeaderDetail::class,'category_id');
    }

    public function packages()
    {
        return $this->hasMany(Package::class, 'category_id');
    }
    public function panels()
    {
        return $this->hasMany(Panel::class, 'category_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->products()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'products',
                'count' => $this->products()->count(),
                'ids' => $this->products()->pluck('title')->toArray(),
            ];
        }
        if ($this->orderDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'orderDetails',
                'count' => $this->orderDetails()->count(),
                'ids' => $this->orderDetails()->pluck('price')->toArray(),
            ];
        }
        if ($this->packages()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'packages',
                'count' => $this->packages()->count(),
                'ids' => $this->packages()->pluck('name')->toArray(),
            ];
        }
        if ($this->panels()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'panels',
                'count' => $this->panels()->count(),
                'ids' => $this->panels()->pluck('name')->toArray(),
            ];
        }
        if ($this->documentHeaderDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'document-Header-Details',
                'count' => $this->documentHeaderDetails()->count(),
                'ids' => $this->documentHeaderDetails()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Category')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }



    public function getItemSoldAttribute($key)
    {
        $product_id = $this->products->where('category_id', $this->id)->pluck('id')->toArray();

        return OrderItem::where('type', 'sales')->whereIn('product_id', $product_id)
            ->whereHas('order', function ($query) {
                $query->where('order_type', 'sales')
                    ->where('status', 'done');
            })->sum('quantity');
    }

    public function getSubTotalAttribute($key)
    {
        $product_ids = $this->products->where('category_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($product_ids) {
                $query->where('type', 'sales')->whereIn('product_id', $product_ids);
            })->sum('sub_total');
    }

    public function getTaxAttribute($key)
    {
        $product_ids = $this->products->where('category_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($product_ids) {
                $query->where('type', 'sales')->whereIn('product_id', $product_ids);
            })->sum('total_tax');
    }

    public function getDiscountAttribute($key)
    {
        $product_ids = $this->products->where('category_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($product_ids) {
                $query->where('type', 'sales')->whereIn('product_id', $product_ids);
            })->sum('all_discount');
    }
    public function getTotalAttribute($key)
    {
        $product_ids = $this->products->where('category_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($product_ids) {
                $query->where('type', 'sales')->whereIn('product_id', $product_ids);
            })->sum('total');
    }

    public function getItemPurchasedAttribute($key)
    {
        $product_id = $this->products->where('category_id', $this->id)->pluck('id')->toArray();

        return OrderItem::where('type', 'receiving')->whereIn('product_id', $product_id)
            ->whereHas('order', function ($query) {
                $query->where('order_type', 'receiving')
                ->where('status', 'done')
                ->where('type', 'supplier');
            })->sum('quantity');
    }
    public function getTotalPurchasedAttribute($key)
    {
        $product_ids = $this->products->where('category_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'receiving')
        ->where('status', 'done')
        ->where('type', 'supplier')
            ->whereHas('items', function ($query) use ($product_ids) {
                $query->where('type', 'receiving')->whereIn('product_id', $product_ids);
            })->sum('total');
    }
}
