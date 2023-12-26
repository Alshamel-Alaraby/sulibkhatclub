<?php

namespace Database\Seeders;

use App\Models\ClientType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clientTypes = [
            [
                'name'     => 'عميل',
                'name_e'   => 'customer',
                'db_table' => 'general_customers',
            ],
            [
                'name'     => 'مورد',
                'name_e'   => 'suppler',
                'db_table' => 'general_suppliers',
            ],
            [
                'name'     => 'موظف',
                'name_e'   => 'employee',
                'db_table' => 'general_employees',
            ],
            [
                'name'     => 'مريض',
                'name_e'   => 'patient',
                'db_table' => 'h_m_s_patients',
            ],
            [
                'name'     => 'دكتور',
                'name_e'   => 'doctor',
                'db_table' => 'h_m_s_doctors',
            ],
            [
                'name'     => 'شركة التأمين',
                'name_e'   => 'insurance company',
                'db_table' => 'general_insurance_companies',
            ],
            [
                'name'     => 'المستأجرين',
                'name_e'   => 'tenants',
                'db_table' => 'rlst_tenants',
            ]

        ];




        foreach ($clientTypes as $type):
            ClientType::create($type);
        endforeach;
    }
}
