<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\CarRent\Entities\CarSunroof;

class CarSunroofSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('car_sunroofs')->delete();

        $attributes = [
            [
                'id' => 1,
                'name' => 'لايوجد',
                'name_e' => 'None',
            ],
            [
                'id' => 2,
                'name' => 'عادى',
                'name_e' => 'Normal',
            ],
            [
                'id' => 3,
                'name' => 'بانوراما',
                'name_e' => 'Panoramic',
            ],

        ];

        foreach ($attributes as $attribute) {
            CarSunroof::create($attribute);
        }
    }
}
