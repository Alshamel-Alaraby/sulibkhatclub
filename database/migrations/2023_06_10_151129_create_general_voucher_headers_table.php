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
        Schema::create('general_voucher_headers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id')->index();
            $table->unsignedBigInteger('branch_id')->index();
            $table->date('date');
            $table->unsignedBigInteger('serial_id')->index()->nullable();
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('salesmen_id')->index();
            $table->unsignedBigInteger('payment_method_id')->index();
            $table->double('amount')->default(0);
            $table->string('serial_number')->nullable();
            $table->string('prefix')->nullable();
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
        Schema::dropIfExists('general_voucher_headers');
    }
};
