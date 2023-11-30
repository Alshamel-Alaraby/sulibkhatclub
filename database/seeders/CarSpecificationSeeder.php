<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\CarRent\Entities\CarSpecification;

class CarSpecificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('car_specifications')->delete();

        $attributes = [
            [
                'id' => 1,
                'name' => 'كاملة المواصفات',
                'name_e' => 'Full Option',
            ],
            [
                'id' => 2,
                'name' => 'ايرباج امامية',
                'name_e' => 'Front Airbags',
            ],
            [
                'id' => 3,
                'name' => 'ايرباج جانبية',
                'name_e' => 'Side Airbags',
            ],
            [
                'id' => 4,
                'name' => 'سنسر',
                'name_e' => 'Parking Sensors',
            ],
            [
                'id' => 5,
                'name' => 'وقوف الى',
                'name_e' => 'Self Parking',
            ],
            [
                'id' => 6,
                'name' => 'نافيجيشن',
                'name_e' => 'Navigation',
            ],
            [
                'id' => 7,
                'name' => 'بلوتوث',
                'name_e' => 'Bluetooth',
            ],
            [
                'id' => 8,
                'name' => 'نظام انظار',
                'name_e' => 'Alarm System',
            ],
            [
                'id' => 9,
                'name' => 'تحكم فى درجة حرارة المقاعد',
                'name_e' => 'Temperature Controlled Seats',
            ],

        ];

        foreach ($attributes as $attribute) {
            CarSpecification::create($attribute);
        }
    }
}
