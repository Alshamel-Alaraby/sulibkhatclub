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
        Schema::create('rlst_contract_header_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_header_id')->references('id')->on('rlst_contract_headers');
            $table->date('from_date');
            $table->date('to_date');
            $table->double('rent_amount')->default(0);
            $table->foreignId('payment_type_id')->references('id')->on('rp_installment_payment_types');
            $table->unsignedBigInteger('print_day')->nullable();
            $table->unsignedBigInteger('due_day')->nullable();

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
        Schema::dropIfExists('rlst_contract_header_details');
    }
};
