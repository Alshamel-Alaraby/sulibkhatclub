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
        Schema::create('h_m_s_settings', function (Blueprint $table) {
            $table->id();
            $table->float('taxes',8,2)->default(0);
            $table->integer('interval')->default(15); // 15 minutes the default time of each appointment
            $table->boolean('is_there_insurance')->default(0);
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
        Schema::dropIfExists('h_m_s_settings');
    }
};
