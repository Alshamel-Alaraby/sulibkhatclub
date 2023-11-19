<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hr_attendance_settings', function (Blueprint $table) {
            $table->boolean('location_fingerprint')->default(0)->comment('عدم قبول البصمة من غير المكان المحدد');
            $table->boolean('mobile_id_fingerprint')->default(0)->comment('عدم قبول البصمة من غير الجهاز المحدد');
            $table->boolean('pre_in_fingerprint')->default(0)->comment('عدم قبول البصمة بعد موعد الحضور المسموح به');
            $table->boolean('day_off_fingerprint')->default(0)->comment('عدم قبول البصمة في الاجازات الاسبوعية');
            $table->boolean('public_holiday_fingerprint')->default(0)->comment('عدم قبول البصمة في الاجازات الرسمية');
            $table->boolean('post_out_fingerprint')->default(0)->comment('عدم قبول البصمة قبل موعد الانصراف المسموح به');
            $table->boolean('pre_out_fingerprint')->default(0)->comment('عدم قبول البصمة بعد موعد الانصراف المسموح به');
            $table->boolean('holiday_fingerprint')->default(0)->comment('قبول البصمة لو الموظف في اجازة');
            $table->boolean('Training_course_fingerprint')->default(0)->comment('قبول البصمة لو الموظف في دورة');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_attendance_settings', function (Blueprint $table) {

        });
    }
};
