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
        Schema::create('cm_members', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('second_name');
            $table->string('third_name');
            $table->string('last_name');
            $table->string('family_name');
            $table->date('birth_date')->nullable();
            $table->string('national_id')->nullable();
            $table->string('nationality_number')->nullable();
            $table->string('home_phone')->nullable();
            $table->string('work_phone')->nullable();
            $table->string('home_address')->nullable();
            $table->string('work_address')->nullable();
            $table->date('membership_date')->nullable();
            $table->integer('membership_number')->nullable()->default(0);
            $table->string('job')->nullable();
            $table->string('degree')->nullable();
            $table->integer('acceptance')->default(0);
            $table->date('session_date')->nullable();
            $table->string('session_number')->nullable();
            $table->date('applying_date')->nullable();
            $table->integer('applying_number')->unique()->nullable()->default(0);
            $table->foreignId('sponsor_id')->default(1)->nullable();
            $table->foreignId('status_id')->nullable();
            $table->foreignId('member_type_id')->default(1)->nullable();
            $table->foreignId('financial_status_id')->nullable();
            $table->string('member_type')->default('pending')->nullable();
            $table->string('notes')->nullable();
            $table->boolean('gender')->nullable();
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
        Schema::dropIfExists('cm_members');
    }
};
