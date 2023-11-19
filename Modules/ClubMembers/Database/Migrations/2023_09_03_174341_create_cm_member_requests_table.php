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
        Schema::create('cm_member_requests', function (Blueprint $table) {
            $table->id();
            /* 1 */ $table->string('first_name')->nullable();
            /* 2 */ $table->string('second_name')->nullable();
            /* 3 */ $table->string('third_name')->nullable();
            /* 4 */ $table->string('last_name')->nullable();
            /* 5 */ $table->string('family_name')->nullable();
            /* 6 */ $table->date('birth_date')->nullable();
            /* 7 */ $table->string('national_id')->nullable();
            /* 8 */ $table->string('nationality_class')->nullable(); // changed from nationality_number
            /* 9 */ $table->string('phone_code')->nullable();
            /* 10 */ $table->string('home_phone')->nullable();
            /* 11 */$table->string('work_phone')->nullable();
            /* 12 */$table->string('home_address')->nullable();
            /* 13 */$table->string('work_address')->nullable();
            /* 14 */$table->date('membership_date')->nullable();
            /* 15 */$table->integer('membership_number')->nullable()->default(0);
            /* 16 */$table->string('job')->nullable();
            /* 17 */$table->string('degree')->nullable();
            /* 18 */$table->integer('acceptance')->nullable();
            /* 19 */$table->date('session_date')->nullable();
            /* 20 */$table->string('session_number')->nullable();
            /* 21 */$table->date('applying_date')->nullable();
            /* 22 */$table->integer('applying_number')->nullable()->default(0);
            /* 23 */$table->foreignId('sponsor_id')->index()->default(1)->nullable();
            /* 24 */$table->foreignId('status_id')->index()->nullable();
            /* 25 */$table->foreignId('member_type_id')->index()->default(1)->nullable();
            /* 26 */$table->foreignId('financial_status_id')->index()->default(2)->nullable();
            /* 27 */$table->string('notes')->nullable();
            /* 28 */$table->boolean('gender')->nullable();
            /* 29 */$table->unsignedBigInteger('financial_year_id')->index()->nullable();
            /* 30 */$table->longText('full_name')->nullable();
            /* 31 */$table->unsignedInteger('national_no')->nullable();
            /* 32 */ $table->foreignId('auto_member_type_id')->index()->default(1);
            /* 33 */$table->date('last_transaction_date')->index()->nullable();
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
        Schema::dropIfExists('cm_member_requests');
    }
};
