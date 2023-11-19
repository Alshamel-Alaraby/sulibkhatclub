<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\HR\Entities\AttendanceSetting;

class HrAttendanceSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('hr_attendance_settings')->delete();

        $attributes = [
            [
                'id' => 1,
                'pre_in' => 30,
                'post_in' => 15,
                'absent_minutes' => 60,
                'pre_out' => 0,
                'post_out' => 30,
                'max_out' => 120,
                'location_fingerprint' => 0,
                'mobile_id_fingerprint' => 0,
                'pre_in_fingerprint' => 0,
                'day_off_fingerprint' => 0,
                'public_holiday_fingerprint' => 0,
                'post_out_fingerprint' => 0,
                'pre_out_fingerprint' => 0,
                'holiday_fingerprint' => 0,
                'Training_course_fingerprint' => 0
            ],
        ];

        foreach ($attributes as $attribute) {
            AttendanceSetting::create($attribute);
        }

    }
}
