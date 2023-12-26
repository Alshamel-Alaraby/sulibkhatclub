<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\RecievablePayable\Entities\RpInstallmentPaymentType;

class RpInstallmentPaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RpInstallmentPaymentType::create([
            "name"                          => "دفعة مستحقة",
            "name_e"                        => "Payment due",
            "is_conditional"                => 0,
            "installment_condation_id"      => null,
            "installment_payment_type_freq" => 1,
            "is_partially"                  => 0,
            "is_passed"                     => 0,
            "is_passed_all"                 => 0,
            "is_passed_contract_plan"       => 0,
            "auto_freq"                     => 0,
            "freq_period"                   => 7,
            "step"                          =>"D"
        ]);

        RpInstallmentPaymentType::create([
            "name"                          => "دفعة شهرية",
            "name_e"                        => "Monthly payment",
            "is_conditional"                => 0,
            "installment_condation_id"      => null,
            "installment_payment_type_freq" => 1,
            "is_partially"                  => 0,
            "is_passed"                     => 0,
            "is_passed_all"                 => 0,
            "is_passed_contract_plan"       => 0,
            "auto_freq"                     => 3,
            "freq_period"                   => 7,
            "step"                          =>"M"
        ]);

        RpInstallmentPaymentType::create([
            "name"                          => "دفعة سنوية",
            "name_e"                        => "Annual payment",
            "is_conditional"                => 0,
            "installment_condation_id"      => null,
            "installment_payment_type_freq" => 1,
            "is_partially"                  => 0,
            "is_passed"                     => 0,
            "is_passed_all"                 => 0,
            "is_passed_contract_plan"       => 0,
            "auto_freq"                     => 0,
            "freq_period"                   => 1,
            "step"                          =>"Y"
        ]);



    }
}
