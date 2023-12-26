<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_pricings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_id')->unsigned()->index()->comment('العربيه');
            $table->foreign('car_id')->references('id')->on('car_cars');
            $table->double('daily_price')->comment('السعر اليومي')->nullable();
            $table->double('weekly_price')->comment('السعر الاسبوعي')->nullable();
            $table->double('monthly_price')->comment('السعر الشهري')->nullable();
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
        Schema::dropIfExists('car_pricings');
    }
};
