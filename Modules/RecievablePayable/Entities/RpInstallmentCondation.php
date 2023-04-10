<?php

namespace Modules\RecievablePayable\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RpInstallmentCondation extends Model
{
    use HasFactory,LogTrait;

    protected $guarded = ['id'];

    protected static function newFactory()
    {
        return \Modules\RecievablePayable\Database\factories\Entities/RpInstallmentCondationFactory::new();
    }

    public function installment_payment_types()
    {
        return $this->hasMany(RpInstallmentPaymentType::class, "installment_condation_id");
    }
}
