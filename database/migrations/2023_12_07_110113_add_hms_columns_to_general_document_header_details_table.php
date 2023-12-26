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
        Schema::table('general_document_header_details', function (Blueprint $table) {
            $table->double('company_insurance_amount')->default(0);
            $table->double('patient_amount')->default(0);
            $table->foreignId('service_id')->nullable()->constrained('h_m_s_service_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_document_header_details', function (Blueprint $table) {
            //
        });
    }
};
