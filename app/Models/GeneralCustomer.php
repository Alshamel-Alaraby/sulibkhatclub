<?php

namespace App\Models;

use App\Models\Sector;
use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PointOfSale\Entities\Order;
use Modules\PointOfSale\Entities\OrderItem;
use Modules\RealEstate\Entities\RlstContract;
use Modules\RealEstate\Entities\RlstInvoice;
use Modules\RealEstate\Entities\RlstReservation;
use Modules\RecievablePayable\Entities\RpBreakDown;
use Modules\RecievablePayable\Entities\RpOpeningBalance;
use Spatie\MediaLibrary\HasMedia;

class GeneralCustomer extends Model implements HasMedia
{

    use HasFactory, MediaTrait, LogTrait;
    protected $table = 'general_customers';

    protected $guarded = ['id'];

    protected $appends = ['item_sold', 'sub_total', 'tax', 'discount', 'total', 'item_purchased', 'total_purchased'];

    public function scopeData($query)
    {
        return $query
            ->select(
                'id',
                'name',
                'name_e',
                'phone',
                'email',
                'country_id',
                'city_id',
                'rp_code',
                'nationality',
                'bank_account_id',
                'contact_person',
                'contact_phone',
                'national_id',
                'whatsapp',
                'passport_no',
                'note',
                'code_country',
                'facebook',
                'instagram',
                'linkedin',
                'snapchat',
                'twitter',
                'website',
                'salesman_id',
                'employee_id',
                'customer_source_id',
                'customer_group_id',
                'is_supplier',
                'sector_id',
                'type',
                'address',
                'id_number',
                'tax_record',
                'passport_expiry_date',
                'visa_no',
                'driving_license',
                'license_exp_date',
                'customer_main_category_id',
                'customer_sub_category_id'
            )
            ->with('media', 'sector:id,name,name_e', 'customerSource:id,name,name_e', 'salesmen:id,name,name_e',
                'employee:id,name,name_e', 'country:id,name,name_e',
                'city:id,name,name_e', 'bankAccount',
                'customerGroup:id,title,title_e', 'customer_main_category:id,name,name_e',
                'customer_sub_category:id,name,name_e'

            );
    }

    public function opening_balances()
    {
        return $this->hasMany(RpOpeningBalance::class, 'customer_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id');

    }

    public function customer_main_category()
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_main_category_id');
    }

    public function customer_sub_category()
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_sub_category_id', 'id');
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
    public function voucherHeaders()
    {
        return $this->hasMany(VoucherHeader::class, 'customer_id');
    }

    public function documentHeader()
    {
        return $this->hasMany(DocumentHeader::class, 'customer_id');
    }

    public function attendants()
    {
        return $this->hasMany(Attendant::class, 'customer_id');
    }
    public function tasks()
    {
        return $this->hasMany(Task::class, 'customer_id');
    }

    public function customerBranches()
    {
        return $this->hasMany(\App\Models\CustomerBranch::class, "customer_id");
    }

    public function rp_break_downs()
    {
        return $this->hasMany(RpBreakDown::class, 'customer_id');
    }

    public function contracts()
    {
        return $this->hasMany(RlstContract::class, 'customer_id');
    }

    public function RlstInvoices()
    {
        return $this->hasMany(RlstInvoice::class, 'customer_id');
    }
    public function reservations()
    {
        return $this->hasMany(RlstReservation::class);
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->reservations()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'reservations',
                'count' => $this->reservations()->count(),
                'ids' => $this->reservations()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->RlstInvoices()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'RlstInvoices',
                'count' => $this->RlstInvoices()->count(),
                'ids' => $this->RlstInvoices()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->contracts()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'contracts',
                'count' => $this->contracts()->count(),
                'ids' => $this->contracts()->pluck('date')->toArray(),
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
        if ($this->supplier()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'supplier',
                'count' => $this->supplier()->count(),
                'ids' => $this->supplier()->pluck('name')->toArray(),
            ];
        }
        if ($this->orders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'orders',
                'count' => $this->orders()->count(),
                'ids' => $this->orders()->pluck('date')->toArray(),
            ];
        }
        if ($this->voucherHeaders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'voucherHeaders',
                'count' => $this->voucherHeaders()->count(),
                'ids' => $this->voucherHeaders()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->documentHeader()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'documentHeader',
                'count' => $this->documentHeader()->count(),
                'ids' => $this->documentHeader()->pluck('prefix')->toArray(),
            ];
        }

        if ($this->attendants()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'attendants',
                'count' => $this->attendants()->count(),
                'ids' => $this->attendants()->pluck('name')->toArray(),
            ];
        }
        if ($this->customerBranches()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'customerBranches',
                'count' => $this->customerBranches()->count(),
                'ids' => $this->customerBranches()->pluck('id')->toArray(),
            ];
        }
        if ($this->tasks()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'tasks',
                'count' => $this->tasks()->count(),
                'ids' => $this->tasks()->pluck('contact_person')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }


    public function getItemSoldAttribute($key)
    {
        return 0;

        $order_id = $this->orders->where('customer_id', $this->id)->pluck('id')->toArray();

        return OrderItem::where('type', 'sales')->whereIn('order_id', $order_id)
            ->whereHas('order', function ($query) {
                $query->where('order_type', 'sales')
                    ->where('status', 'done');
            })->sum('quantity');
    }

    public function getSubTotalAttribute($key)
    {
        return 0;
        $order_id = $this->orders->where('customer_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($order_id) {
                $query->where('type', 'sales')->whereIn('order_id', $order_id);
            })->sum('sub_total');
    }

    public function getTaxAttribute($key)
    {
        return 0;
        $order_id = $this->orders->where('customer_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($order_id) {
                $query->where('type', 'sales')->whereIn('order_id', $order_id);
            })->sum('total_tax');
    }

    public function getDiscountAttribute($key)
    {
        return 0;
        $order_id = $this->orders->where('customer_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($order_id) {
                $query->where('type', 'sales')->whereIn('order_id', $order_id);
            })->sum('all_discount');
    }
    public function getTotalAttribute($key)
    {
        return 0;
        $order_id = $this->orders->where('customer_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($order_id) {
                $query->where('type', 'sales')->whereIn('order_id', $order_id);
            })->sum('total');
    }

    public function getItemPurchasedAttribute($key)
    {
        return 0;
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
        return 0;
        $order_id = $this->orders->where('customer_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'receiving')
            ->where('status', 'done')
            ->where('type', 'supplier')
            ->whereHas('items', function ($query) use ($order_id) {
                $query->where('type', 'receiving')->whereIn('order_id', $order_id);
            })->sum('total');
    }

}
