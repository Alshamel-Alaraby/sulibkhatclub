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
            $table->date('birth_date');
            $table->string('national_id');
            $table->string('nationality_number');
            $table->string('home_phone');
            $table->string('work_phone');
            $table->string('home_address');
            $table->string('work_address');
            $table->date('membership_date');
            $table->string('membership_number')->nullable();
            $table->string('membership_request_number');
            $table->string('job');
            $table->string('degree');

            $table->string('acceptance')->default(0);
            $table->date('acceptance_date');
            $table->string('acceptance_number');

            $table->date('session_date');
            $table->string('session_number');
            $table->string('sponsor')->default(0);
            $table->foreignId('sponsor_id');
            $table->foreignId('status_id')->nullable();
            $table->foreignId('member_type_id')->default(1)->nullable();
            $table->foreignId('financial_status_id')->nullable();
            $table->string('member_type')->nullable();
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
        Schema::dropIfExists('cm_pending_members');
    }
};
