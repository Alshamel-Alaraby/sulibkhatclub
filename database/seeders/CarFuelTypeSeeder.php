<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\CarRent\Entities\CarFuelType;

class CarFuelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('car_fuel_types')->delete();

        $attributes = [
            [
                'id' => 1,
                'name' => 'الغازولين',
                'name_e' => 'Gasoline',
            ],
            [
                'id' => 2,
                'name' => 'ديزل',
                'name_e' => 'Diesel',
            ],
            [
                'id' => 3,
                'name' => 'مختلط',
                'name_e' => 'Hybrid',
            ],
            [
                'id' => 4,
                'name' => 'كهرباء',
                'name_e' => 'Electric',
            ],
            [
                'id' => 5,
                'name' => 'وقود مرن',
                'name_e' => 'Flex Fuel',
            ],
            [
                'id' => 6,
                'name' => 'خلية وقود الهيدروجين',
                'name_e' => 'Hydrogen Fuel Cell',
            ],
            [
                'id' => 7,
                'name' => 'غاز طبيعي',
                'name_e' => 'Natural Gas',
            ]

        ];

        foreach ($attributes as $attribute) {
            CarFuelType::create($attribute);
        }
    }
}
