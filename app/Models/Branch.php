<?php

namespace App\Models;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ClubMembers\Entities\CmMemberReject;
use Modules\ClubMembers\Entities\CmTransaction;
use Modules\PointOfSale\Entities\CashRegister;
use Modules\PointOfSale\Entities\Order;
use Modules\RealEstate\Entities\RlstContract;
use Modules\RealEstate\Entities\RlstContractHeader;
use Modules\RealEstate\Entities\RlstInvoice;
use Modules\RealEstate\Entities\RlstReservation;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class Branch extends Model implements HasMedia
{
    use HasFactory, LogTrait, MediaTrait;
    protected $table = 'general_branches';

    protected $guarded = [];

    public function scopeData($query)
    {
        return $query
            ->select('id', 'name', 'name_e', 'is_active', 'parent_id', 'code_country', 'phone', 'second_phone', 'fax', 'p_o_pox', 'address', 'email')
            ->with([
                'parent:id,name,name_e',
                'children',
                'serials',
                'stores',
                'media',
            ]);
    }

    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class, 'branch_id');
    }
    public function cm_transactions()
    {
        return $this->hasMany(CmTransaction::class, 'branch_id');
    }

    public function stores()
    {
        return $this->hasMany(Store::class, 'branch_id');
    }

    public function serials()
    {
        return $this->hasMany(Serial::class, 'branch_id');
    }

    public function children()
    {
        return $this->hasMany(Branch::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Branch::class, 'parent_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(\Modules\PointOfSale\Entities\Product::class , 'branch_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function documentHeaders()
    {
        return $this->hasMany(DocumentHeader::class, 'branch_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'branch_id');
    }
    public function cash_registers()
    {
        return $this->hasMany(CashRegister::class, 'branch_id');
    }
    public function cm_member_rejects()
    {
        return $this->hasMany(CmMemberReject::class, 'branch_id');
    }

    public function contracts()
    {
        return $this->hasMany(RlstContract::class, 'branch_id');
    }

    public function contractHeaders()
    {
        return $this->hasMany(RlstContractHeader::class, 'branch_id');
    }

    public function RlstInvoices()
    {
        return $this->hasMany(RlstInvoice::class, 'branch_id');
    }

    public function reservations()
    {
        return $this->hasMany(RlstReservation::class,'branch_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->cm_member_rejects()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'cm member rejects',
                'count' => $this->cm_member_rejects()->count(),
                'ids' => $this->cm_member_rejects()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->reservations()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'reservations',
                'count' => $this->reservations()->count(),
                'ids' => $this->reservations()->pluck('id')->toArray(),
            ];
        }
        if ($this->RlstInvoices()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'RlstInvoices',
                'count' => $this->RlstInvoices()->count(),
                'ids' => $this->RlstInvoices()->pluck('id')->toArray(),
            ];
        }
        if ($this->contractHeaders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'contractHeaders',
                'count' => $this->contractHeaders()->count(),
                'ids' => $this->contractHeaders()->pluck('id')->toArray(),
            ];
        }
        if ($this->contracts()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'contracts',
                'count' => $this->contracts()->count(),
                'ids' => $this->contracts()->pluck('date')->toArray(),
            ];
        }
        if ($this->cash_registers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'cash_registers',
                'count' => $this->cash_registers()->count(),
                'ids' => $this->cash_registers()->pluck('title')->toArray(),
            ];
        }
        if ($this->stores()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'stores',
                'count' => $this->stores()->count(),
                'ids' => $this->stores()->pluck('name')->toArray(),
            ];
        }
        if ($this->serials()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'serials',
                'count' => $this->serials()->count(),
                'ids' => $this->serials()->pluck('name')->toArray(),
            ];
        }
        if ($this->children()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'children',
                'count' => $this->children()->count(),
                'ids' => $this->children()->pluck('name')->toArray(),
            ];
        }
        if ($this->products()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'products',
                'count' => $this->products()->count(),
                'ids' => $this->products()->pluck('title')->toArray(),
            ];
        }
        if ($this->orders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'orders',
                'count' => $this->orders()->count(),
                'ids' => $this->orders()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->documentHeaders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'documentHeaders',
                'count' => $this->documentHeaders()->count(),
                'ids' => $this->documentHeaders()->pluck('id')->toArray(),
            ];
        }
        if ($this->employees()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'employees',
                'count' => $this->employees()->count(),
                'ids' => $this->employees()->pluck('id')->toArray(),
            ];
        }
        if ($this->transactions()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'transactions',
                'count' => $this->transactions()->count(),
                'ids' => $this->transactions()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->cm_transactions()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'cm transactions',
                'count' => $this->cm_transactions()->count(),
                'ids' => $this->cm_transactions()->pluck('prefix')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Branch')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
