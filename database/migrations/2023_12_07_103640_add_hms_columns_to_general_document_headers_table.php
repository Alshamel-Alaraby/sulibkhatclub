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
        Schema::table('general_document_headers', function (Blueprint $table) {
            $table->foreignId('patient_id')->nullable()->constrained('h_m_s_patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('h_m_s_doctors')->onDelete('cascade');
            $table->foreignId('from_doctor_id')->nullable()->constrained('h_m_s_doctors')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('h_m_s_appointments')->onDelete('cascade');
            $table->foreignId('company_insurance_id')->nullable()->constrained('general_insurance_companies')->onDelete('cascade');
            $table->string('patient_insurance_number')->nullable();
            $table->double('total_company_insurance_amount')->default(0);
            $table->double('total_patient_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_document_headers', function (Blueprint $table) {
            //
        });
    }
};
