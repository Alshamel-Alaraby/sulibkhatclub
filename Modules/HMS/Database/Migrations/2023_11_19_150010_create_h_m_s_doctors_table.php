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
        Schema::create('h_m_s_doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('name_e')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('mobile')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('analytic_account')->nullable();
            $table->float('selling_items_commission',8,2)->nullable();
            $table->float('transfer_patient_commission',8,2)->nullable();
            $table->float('medical_commission',8,2)->nullable();
            $table->foreignId('specialty_id')->nullable()->constrained('h_m_s_specialties')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('general_users')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('general_employees')->cascadeOnDelete();
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
        Schema::dropIfExists('h_m_s_doctors');
    }
};
