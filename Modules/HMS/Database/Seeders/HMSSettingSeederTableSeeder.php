<?php

namespace Modules\HMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\HMS\Entities\HMSSetting;

class HMSSettingSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        HMSSetting::create();
        // $this->call("OthersTableSeeder");
    }
}
