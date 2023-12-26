<?php

namespace Modules\HMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\HMS\Entities\HMSDay;

class HMSDaySeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $days = [
            ['name' => "السبت","name_e" => "Saturday","carbon_day_number" => 6],
            ['name' => "الحد","name_e" => "Sunday","carbon_day_number" => 0],
            ['name' => "الاتنين","name_e" => "Monday","carbon_day_number" => 1],
            ['name' => "الثلاثاء","name_e" => "Tuesday","carbon_day_number" => 2],
            ['name' => "الاربعاء","name_e" => "Wednesday","carbon_day_number" => 3],
            ['name' => "الخميس","name_e" => "Thursday","carbon_day_number" => 4],
            ['name' => "الجمعة","name_e" => "Friday","carbon_day_number" => 5],
        ];

        foreach($days as $day)
            HMSDay::create($day);
    }
}
