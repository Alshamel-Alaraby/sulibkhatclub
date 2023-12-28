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
            /* 1 */ $table->integer('applying_number')->nullable()->default(0);
            /* 2 */ $table->integer('membership_number')->nullable()->default(0);
            /* 3 */$table->integer('acceptance')->nullable();
            /* 4 */$table->string('home_address')->nullable();
            /* 5 */$table->date('membership_date')->nullable();
            /* 6 */$table->string('national_id')->nullable();
            /* 7 */$table->string('nationality_class')->nullable(); // changed from nationality_number
            /* 8 */$table->string('first_name')->nullable();
            /* 9 */$table->string('second_name')->nullable();
            /* 10 */$table->string('third_name')->nullable();
            /* 11 */$table->string('last_name')->nullable();
            /* 12 */$table->string('family_name')->nullable();
            /* 13 */$table->longText('full_name')->nullable();
            /* 14 */$table->date('birth_date')->nullable();
            /* 15 */$table->boolean('accepted')->default(0);
            /* 16 */$table->foreignId('sponsor_id')->index()->nullable()->default(1);
            /* 17 */$table->string('degree')->nullable();
            /* 18 */$table->string('job')->nullable();
            /* 19 */$table->string('work_phone')->nullable();
            /* 20 */$table->string('work_address')->nullable();
            /* 21 */$table->string('home_phone')->nullable();
            /* 22 */$table->unsignedBigInteger('member_status_id')->index()->nullable();
            /* 23 */$table->date('last_transaction_date')->nullable();
            /* 24 */$table->year('last_transaction_year')->nullable();
            /* 25 */$table->string('last_transaction_id')->index()->nullable();
            /* 26 */$table->date('doc_date')->nullable();
            /* 27 */$table->string('doc_no')->nullable();
            /* 28 */$table->boolean('sponsership')->default(0);

            /* 29 */$table->date('session_date')->nullable();
            /* 30 */$table->string('session_number')->nullable();
            /* 31 */$table->foreignId('status_id')->nullable();
            /* 32 */$table->foreignId('member_type_id')->index()->default(1)->nullable();
            /* 33 */$table->foreignId('financial_status_id')->index()->default(2)->nullable();
            /* 34 */$table->string('member_type')->default('pending')->nullable();
            /* 35 */$table->string('notes')->nullable();
            /* 36 */$table->boolean('gender')->nullable();
            /* 37 */$table->unsignedBigInteger('financial_year_id')->index()->nullable();
            /* 38 */$table->date('applying_date')->nullable();
            /* 39 */$table->string('phone_code')->nullable();
            /* 40 */$table->foreignId('auto_member_type_id')->index()->nullable();

            /* 41 */$table->unsignedBigInteger('member_kind_id')->index()->nullable();
            /* 42 */$table->unsignedBigInteger('discharge_reson_id')->index()->nullable();
            /* 43 */$table->unsignedBigInteger('members_permissions_id')->index()->nullable();
            /* 44 */$table->unsignedInteger('national_no')->nullable();
            /* 45 */$table->unsignedInteger('company_id')->index()->nullable();

            /* 46 */$table->date('executive_office_date')->nullable();
            /* 47 */$table->string('executive_office_number')->nullable();

            /* 48 */$table->date('board_of_directors_date')->nullable();
            /* 49 */$table->string('board_of_directors_number')->nullable();


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
