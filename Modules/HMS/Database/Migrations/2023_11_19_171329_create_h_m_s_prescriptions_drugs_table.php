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
        Schema::create('h_m_s_prescriptions_drugs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->nullable()->constrained('h_m_s_prescriptions')->cascadeOnDelete();
            $table->foreignId('drug_id')->nullable()->constrained('h_m_s_drugs')->cascadeOnDelete();
            $table->string('type')->nullable();
            $table->string('strength')->nullable();
            $table->string('dose')->nullable();
            $table->string('duration')->nullable();
            $table->mediumText('drug_advice')->nullable();
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
        Schema::dropIfExists('h_m_s_prescriptions_drugs');
    }
};
