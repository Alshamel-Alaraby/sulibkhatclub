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
        Schema::create('h_m_s_patient_medical_files', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_e');
            $table->longText('note');
            $table->foreignId('patient_id')->constrained('h_m_s_patients')->cascadeOnDelete();
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
        Schema::dropIfExists('h_m_s_patient_medical_files');
    }
};
