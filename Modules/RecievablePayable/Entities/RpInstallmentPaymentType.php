<?php

namespace Modules\RecievablePayable\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RpInstallmentPaymentType extends Model
{
    use HasFactory,LogTrait;

    protected $guarded = ['id'];


    protected static function newFactory()
    {
        return \Modules\RecievablePayable\Database\factories\RpInstallmentPaymentTypeFactory::new();
    }

    public function installment_condation()
    {
        return $this->belongsTo(RpInstallmentCondation::class, "installment_condation_id");
    }

    public function payment_plan_installment()
    {
        return $this->hasOne(RpPaymentPlanInstallment::class,"installment_payment_type_id");
    }
}
