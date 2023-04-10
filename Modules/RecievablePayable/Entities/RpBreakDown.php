<?php

namespace Modules\RecievablePayable\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RealEstate\Entities\RlstContract;
use Modules\RealEstate\Entities\RlstInvoice;
use Modules\RealEstate\Entities\RlstReservation;

class RpBreakDown extends Model
{
    use HasFactory,LogTrait,SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = ["terms" => "json"];



    public function document()
    {
        return $this->belongsTo(\App\Models\Document::class,'document_id',);
    }

    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class,'currency_id',);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\GeneralCustomer::class,'customer_id',);
    }

    public function installment_status()
    {
        return $this->belongsTo(RpInstallmentStatus::class,'installment_statu_id',);
    }

    public function installment_payment_type()
    {
        return $this->belongsTo(RpInstallmentPaymentType::class,'instalment_type_id',);
    }

    public function openingBalance()
    {
        return $this->belongsTo(RpOpeningBalance::class,'break_id',);
    }

    public function contract()
    {
        return $this->belongsTo(RlstContract::class,'break_id',);
    }

    public function invoice()
    {
        return $this->belongsTo(RlstInvoice::class,'break_id',);
    }
    public function reservation()
    {
        return $this->belongsTo(RlstReservation::class,'break_id',);
    }

    public function children()
    {
        return $this->hasMany(RpBreakDown::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(RpBreakDown::class, 'parent_id');
    }

    public function hisChildren()
    {
        return $this->children()->count() > 0;
    }




    protected static function newFactory()
    {
        return \Modules\RecievablePayable\Database\factories\RpBreakDownFactory::new();
    }
}
