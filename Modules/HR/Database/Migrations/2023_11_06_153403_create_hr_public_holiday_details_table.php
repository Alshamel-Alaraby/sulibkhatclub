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
        Schema::create('hr_public_holiday_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('public_holiday_header_id');
            $table->date('vdate');
            $table->string('name', 50);
            $table->string('name_e', 50);
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
        Schema::dropIfExists('hr_public_holiday_details');
    }
};
