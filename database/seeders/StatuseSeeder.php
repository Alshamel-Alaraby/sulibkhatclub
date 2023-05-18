<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatuseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Admin report
        Status::insert([
            [
                "name" => "قيد الانتظار",
                "name_e" => "pending",
                "module_type" => "bordRent",
            ],
            [
                "name" => "في تَقَدم",
                "name_e" => "in_progress",
                "module_type" => "bordRent",
            ],
            [
                "name" => "مكتمل",
                "name_e" => "completed",
                "module_type" => "bordRent",
            ],
            [
                "name" => "مرفوض",
                "name_e" => "rejected",
                "module_type" => "bordRent",
            ],
            [
                "name" => "انتظار الموافقة",
                "name_e" => "waiting for approval",
                "module_type" => "bordRent",
            ],
            [
                "name" => "إلغاء",
                "name_e" => "canceled",
                "module_type" => "bordRent",
            ],
            [
                "name" => "متاحة",
                "name_e" => "Available",
                "module_type" => "panel",
            ],
            [
                "name" => "مؤجرة",
                "name_e" => "Sold",
                "module_type" => "panel",
            ],
            [
                "name" => "محجوزة",
                "name_e" => "Reserved",
                "module_type" => "panel",
            ],
            [
                "name" => "فى مجموعة",
                "name_e" => "In Strip",
                "module_type" => "panel",
            ],
        ]);
    }
}
