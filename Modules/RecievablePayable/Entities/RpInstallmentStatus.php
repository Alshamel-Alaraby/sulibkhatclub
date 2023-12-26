<?php

namespace Modules\RecievablePayable\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RpInstallmentStatus extends Model
{
    use HasFactory,LogTrait;

    protected $guarded = ['id'];

    public function rp_break_downs()
    {
        return $this->hasMany(RpBreakDown::class, 'installment_statu_id');
    }

    public function payment_plan_installments()
    {
        return $this->hasMany(RpPaymentPlanInstallment::class, 'installment_status_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->rp_break_downs()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'rp_break_downs',
                'count' => $this->rp_break_downs()->count(),
                'ids' => $this->rp_break_downs()->pluck('rate')->toArray(),
            ];
        }
        if ($this->payment_plan_installments()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'payment_plan_installments',
                'count' => $this->payment_plan_installments()->count(),
                'ids' => $this->payment_plan_installments()->pluck('v_date')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }



}
