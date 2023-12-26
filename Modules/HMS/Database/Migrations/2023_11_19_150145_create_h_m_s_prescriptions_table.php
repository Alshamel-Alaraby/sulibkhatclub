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
        Schema::create('h_m_s_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained('h_m_s_patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('h_m_s_doctors')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('h_m_s_appointments')->onDelete('cascade');
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
        Schema::dropIfExists('h_m_s_prescriptions');
    }
};
