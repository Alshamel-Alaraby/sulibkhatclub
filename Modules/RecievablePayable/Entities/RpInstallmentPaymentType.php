<?php

namespace Modules\RecievablePayable\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RpInstallmentPaymentType extends Model
{
    use HasFactory,LogTrait;

    protected $guarded = ['id'];

    public function installment_condation()
    {
        return $this->belongsTo(RpInstallmentCondation::class, "installment_condation_id");
    }

    public function payment_plan_installment()
    {
        return $this->hasOne(RpPaymentPlanInstallment::class,"installment_payment_type_id");
    }

    public function rp_break_downs()
    {
        return $this->hasMany(RpBreakDown::class, 'instalment_type_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->payment_plan_installment()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'payment plan installment',
                'count' => $this->payment_plan_installment()->count(),
                'ids' => $this->payment_plan_installment()->pluck('v_date')->toArray(),
            ];
        }
        if ($this->rp_break_downs()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'rp_break_downs',
                'count' => $this->rp_break_downs()->count(),
                'ids' => $this->rp_break_downs()->pluck('rate')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }

}
