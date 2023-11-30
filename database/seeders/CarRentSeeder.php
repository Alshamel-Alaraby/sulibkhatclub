<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\CarRent\Entities\CarTransmission;

class CarRentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CarTransmissionSeeder::class,
            CarSunroofSeeder::class,
            CarBodyTypeSeeder::class,
            CarFuelTypeSeeder::class,
            CarSeatsMaterialSeeder::class,
            CarSpecificationSeeder::class,
            CarBrandsSeeder::class,
            CarModelsSeeder::class,
        ]);
    }
}
