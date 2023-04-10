<?php

namespace Modules\RecievablePayable\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpInstallmentPaymentPlan extends Model
{
    use HasFactory, LogTrait;

    protected $guarded = ['id'];

    public function payment_plan_installments()
    {
        return $this->hasMany(RpPaymentPlanInstallment::class, "installment_payment_plan_id");
    }

}
