<?php

namespace App\Models;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class BankAccount extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, LogTrait, MediaTrait;
    protected $table = 'general_bank_accounts';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            //->select('id', 'bank_id', 'account_number', 'phone', 'address', 'email', 'emp_id', 'rp_code')
            ->with('bank:id,name,name_e', 'employee:id,name,name_e', 'media');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function rlstCustomers()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstCustomer::class, "bank_account_id");
    }

    public function rlstOwners()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstOwner::class, "bank_account_id");
    }

    // public function hasChildren()
    // {
    //     return $this->rlstCustomers()->count() > 0 ||
    //     $this->rlstOwners()->count() > 0 ;
    // }

    public function customers()
    {
        return $this->hasMany(GeneralCustomer::class, 'bank_account_id');
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'bank_account_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->rlstCustomers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'rlstCustomers',
                'count' => $this->rlstCustomers()->count(),
                'ids' => $this->rlstCustomers()->pluck('name')->toArray(),
            ];
        }
        if ($this->rlstOwners()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'rlstOwners',
                'count' => $this->rlstOwners()->count(),
                'ids' => $this->rlstOwners()->pluck('name')->toArray(),
            ];
        }

        if ($this->customers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'customers',
                'count' => $this->customers()->count(),
                'ids' => $this->customers()->pluck('name')->toArray(),
            ];
        }
        if ($this->suppliers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'suppliers',
                'count' => $this->suppliers()->count(),
                'ids' => $this->suppliers()->pluck('name')->toArray(),
            ];
        }

        return $relationsWithChildren;
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
