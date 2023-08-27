<?php

namespace App\Models;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PointOfSale\Entities\Order;
use Modules\RecievablePayable\Entities\RpOpeningBalance;
use Spatie\MediaLibrary\HasMedia;

class Supplier extends Model implements HasMedia
{
    use HasFactory, MediaTrait, LogTrait;
    protected $table = 'general_suppliers';

    protected $guarded = ['id'];


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
        return $this->belongsTo(CustomerGroup::class,'customer_group_id');
    }

    public function customer()
    {
        return $this->belongsTo(GeneralCustomer::class,'customer_id');

    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }


    public function hasChildren()
    {
       return $this->customer()->count() > 0;
    }
}
