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
        Schema::create('general_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('model_id')->index();
            $table->string('model_type');
            $table->string('name');
            $table->string('name_e');
            $table->text('description')->nullable();
            $table->text('description_e')->nullable();
            $table->json('socials')->nullable();
            $table->json('phones')->nullable();
            $table->unsignedBigInteger('job_id')->index();
            $table->unsignedBigInteger('priority_id')->index();
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
        Schema::dropIfExists('general_contacts');
    }
};
