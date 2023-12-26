<?php

namespace Modules\RecievablePayable\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\RealEstate\Entities\RlstContractDetail;
use Modules\RealEstate\Entities\RlstInvoice;
use Modules\RealEstate\Entities\RlstReservation;

class RpInstallmentPaymentPlan extends Model
{
    use HasFactory, LogTrait;

    protected $guarded = ['id'];

    public function payment_plan_installments()
    {
        return $this->hasMany(RpPaymentPlanInstallment::class, "installment_payment_plan_id");
    }

    public function rp_document_plans()
    {
        return $this->hasMany(RpDocumentPlan::class, "plan_id");
    }

    public function contractDetails()
    {
        return $this->hasMany(RlstContractDetail::class, 'installment_payment_plans_id');
    }

    public function RlstInvoices()
    {
        return $this->hasMany(RlstInvoice::class, 'payment_plan_id');
    }



    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->payment_plan_installments()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'payment plan installments',
                'count' => $this->payment_plan_installments()->count(),
                'ids' => $this->payment_plan_installments()->pluck('v_date')->toArray(),
            ];
        }
        if ($this->RlstInvoices()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'RlstInvoices',
                'count' => $this->RlstInvoices()->count(),
                'ids' => $this->RlstInvoices()->pluck('id')->toArray(),
            ];
        }
        if ($this->contractDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'contractDetails',
                'count' => $this->contractDetails()->count(),
                'ids' => $this->contractDetails()->pluck('id')->toArray(),
            ];
        }
        if ($this->rp_document_plans()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'rp_document_plans',
                'count' => $this->rp_document_plans()->count(),
                'ids' => $this->rp_document_plans()->pluck('id')->toArray(),
            ];
        }
        return $relationsWithChildren;
    }

}
