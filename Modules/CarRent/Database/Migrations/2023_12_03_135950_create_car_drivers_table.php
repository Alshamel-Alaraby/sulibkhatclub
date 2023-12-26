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
        Schema::create('car_drivers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->unsigned()->index()->comment('الموظفين')->unique();
            $table->foreign('employee_id')->references('id')->on('general_employees');
            $table->string('driving_license', 50)->unique()->comment('رخصة القياده');
            $table->date('license_exp_date')->comment('تاريخ انتهاء الرخصة');
            $table->double('daily_price')->comment('السعر اليومي');
            $table->double('weekly_price')->comment('السعر الاسبوعي');
            $table->double('monthly_price')->comment('السعر الشهري');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_drivers');
    }
};
