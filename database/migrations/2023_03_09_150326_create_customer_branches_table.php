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
        Schema::create('general_customer_branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_e');
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedInteger('country_id')->index();
            $table->unsignedInteger('city_id')->index();
            $table->unsignedInteger('governorate_id')->index();
            $table->unsignedInteger('avenue_id')->index();
            $table->unsignedInteger('street_id')->index();
            $table->double('latitude');
            $table->double('longitude');
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
        Schema::dropIfExists('general_customer_branches');
    }
};
