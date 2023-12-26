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
        Schema::create('h_m_s_service_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('name_e')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            // $table->decimal('price_after_sale', 8, 2)->default(0);
            $table->integer('hours')->default(0);
            $table->integer('minutes')->default(0);
            $table->longText("note")->nullable();
            $table->foreignId('specialty_id')->nullable()->constrained('h_m_s_specialties')->cascadeOnDelete();
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
        Schema::dropIfExists('h_m_s_service_types');
    }
};
