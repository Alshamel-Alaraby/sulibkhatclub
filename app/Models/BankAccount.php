<?php

namespace App\Models;

use App\Traits\CompanyScopeTrait;
use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class BankAccount extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, LogTrait, MediaTrait   ;
    protected $table = 'general_bank_accounts';

    protected $fillable = [
        'bank_id',
        'account_number',
        'phone',
        'address',
        'email',
        'emp_id',
        'rp_code',
        "company_id",
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function rlstCustomers()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstCustomer::class,"bank_account_id");
    }

    public function rlstOwners()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstOwner::class,"bank_account_id");
    }
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('BankAccount')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
