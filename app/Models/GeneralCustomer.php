<?php

namespace App\Models;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\RecievablePayable\Entities\RpOpeningBalance;
use Spatie\MediaLibrary\HasMedia;

class GeneralCustomer extends Model implements HasMedia
{

    use HasFactory, MediaTrait, LogTrait;
    protected $table = 'general_customers';

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


}
