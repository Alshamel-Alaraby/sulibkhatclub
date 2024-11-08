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
        Schema::create('general_salesmen_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_e');
            $table->unsignedBigInteger('restart_period_id')->index();
            $table->unsignedBigInteger('salesmen_plans_source_id')->index();
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
        Schema::dropIfExists('general_salesmen_plans');
    }
};
