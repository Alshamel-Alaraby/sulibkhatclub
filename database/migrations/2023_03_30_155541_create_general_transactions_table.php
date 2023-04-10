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
            $table->unsignedInteger('invoice_id')->nullable();
            $table->unsignedInteger('break_id')->nullable();
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
