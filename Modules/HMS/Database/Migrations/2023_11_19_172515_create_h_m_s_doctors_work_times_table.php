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
        Schema::create('h_m_s_doctors_work_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('day_id')->nullable()->constrained('h_m_s_days')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('h_m_s_rooms')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('h_m_s_doctors')->cascadeOnDelete();
            $table->time('from')->nullable();
            $table->time('to')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('h_m_s_doctors_work_times');
    }
};
