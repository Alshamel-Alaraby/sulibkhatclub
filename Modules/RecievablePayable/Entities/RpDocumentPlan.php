<?php

namespace Modules\RecievablePayable\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RpDocumentPlan extends Model
{
    use HasFactory,LogTrait;

    protected $guarded = ['id'];

    public function installPaymentPlan(){
        return $this->belongsTo(RpInstallmentPaymentPlan::class,"plan_id");
    }
}
