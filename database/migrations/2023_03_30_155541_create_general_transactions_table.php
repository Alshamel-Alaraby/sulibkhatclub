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
        Schema::create('general_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->double('amount');
            $table->string('module_type')->nullable();
            $table->unsignedInteger('serial_id')->index()->nullable();
            $table->unsignedInteger('invoice_id')->index()->nullable();
            $table->unsignedInteger('break_id')->index()->nullable();
            $table->unsignedInteger('sponsor_id')->index()->nullable();
            $table->unsignedInteger("branch_id")->index()->nullable();
            $table->unsignedInteger("document_id")->index()->nullable();
            $table->unsignedInteger("cm_member_id")->index()->nullable();
            $table->string('serial_number')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('prefix')->nullable();
            $table->string('type')->nullable()->comment('subscribe,renew');
            $table->integer('number_of_years')->default(1);
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
        Schema::dropIfExists('general_transactions');
    }
};
