<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\CarRent\Entities\CarTransmission;

class CarTransmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('car_transmissions')->delete();

        $attributes = [
           [
               'id' => 1,
               'name' =>'أوتوماتيكي',
               'name_e' => 'Automatic',
           ],
           [
               'id' => 2,
               'name' => 'يدوي',
               'name_e' => 'Manual',
           ],
           [
               'id' => 3,
               'name' => 'تيبترونيك',
               'name_e' => 'Tiptronic',
           ]


        ];

        foreach ($attributes as $attribute) {
            CarTransmission::create($attribute);
        }
    }
}
