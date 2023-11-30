<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\CarRent\Entities\CarSeatsMaterial;

class CarSeatsMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('car_seats_materials')->delete();

        $attributes = [
            [
                'id' => 1,
                'name' => 'نايلون',
                'name_e' => 'Nylon',
            ],
            [
                'id' => 2,
                'name' => 'بوليستر',
                'name_e' => 'Polyster',
            ],
            [
                'id' => 3,
                'name' => 'الكانتارا',
                'name_e' => 'Alcantara',
            ],
            [
                'id' => 4,
                'name' => 'فينيل',
                'name_e' => 'Vinyl',
            ],
            [
                'id' => 5,
                'name' => 'جلد صناعى',
                'name_e' => 'Faux Leather',
            ],
            [
                'id' => 6,
                'name' => 'جلد',
                'name_e' => 'Leather',
            ],

        ];

        foreach ($attributes as $attribute) {
            CarSeatsMaterial::create($attribute);
        }
    }
}
