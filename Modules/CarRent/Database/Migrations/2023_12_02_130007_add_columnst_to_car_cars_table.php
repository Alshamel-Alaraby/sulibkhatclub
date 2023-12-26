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
        Schema::table('car_cars', function (Blueprint $table) {
            $table->double('daily_price')->comment('السعر اليومي')->nullable();
            $table->double('weekly_price')->comment('السعر الاسبوعي')->nullable();
            $table->double('monthly_price')->comment('السعر الشهري')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_cars', function (Blueprint $table) {
            //
        });
    }
};
