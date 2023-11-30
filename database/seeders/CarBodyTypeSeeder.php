<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\CarRent\Entities\CarBodyType;

class CarBodyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('car_body_types')->delete();

        $attributes = [
            [
                'id' => 1,
                'name' => 'سيدان',
                'name_e' => 'Sedan',
            ],
            [
                'id' => 2,
                'name' => 'كوبيه',
                'name_e' => 'Coupe',
            ],
            [
                'id' => 3,
                'name' => 'اخرى',
                'name_e' => 'Other',
            ],
            [
                'id' => 4,
                'name' => 'اس يو فى',
                'name_e' => 'SUV',
            ],
            [
                'id' => 5,
                'name' => 'سقف مكشوف',
                'name_e' => 'Cabriolet',
            ],
            [
                'id' => 6,
                'name' => 'هاتشباك',
                'name_e' => 'Hatchback',
            ],
            [
                'id' => 7,
                'name' => 'سيارة رياضية',
                'name_e' => 'Sports Car',
            ],
            [
                'id' => 8,
                'name' => 'شاحنة',
                'name_e' => 'Van',
            ],
            [
                'id' => 9,
                'name' => 'سيارة نقل',
                'name_e' => 'Truck',
            ],

        ];

        foreach ($attributes as $attribute) {
            CarBodyType::create($attribute);
        }
    }
}
