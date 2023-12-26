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
        Schema::create('h_m_s_diagnosis_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('name_e')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->mediumText('comment')->nullable();
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
        Schema::dropIfExists('h_m_s_diagnosis_tests');
    }
};
