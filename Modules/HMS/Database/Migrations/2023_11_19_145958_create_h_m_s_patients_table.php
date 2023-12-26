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
        Schema::create('h_m_s_patients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string("name_e")->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('safe_number')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('mobile')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('code_country')->nullable();
            $table->string('address')->nullable();
            $table->string('image')->nullable();
            $table->enum('gender',['Male','Female'])->nullable();
            $table->enum('blood',['A+','A-','B+','B-','O+','O-','AB+','AB-'])->nullable();
            $table->float('patient_weight',8,2)->default(0);
            $table->float('patient_height',8,2)->default(0);
            $table->foreignId('user_id')->nullable()->constrained('general_users')->cascadeOnDelete();
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
        Schema::dropIfExists('h_m_s_patients');
    }
};
