<?php

namespace Modules\HMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class HMSDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(HMSDaySeederTableSeeder::class);
        $this->call(HMSSettingSeederTableSeeder::class);
    }
}
