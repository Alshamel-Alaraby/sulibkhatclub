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
        Schema::table('general_document_headers', function (Blueprint $table) {
            $table->foreignId('installment_payment_type_id')->nullable()->references('id')->on('rp_installment_payment_types');
            $table->unsignedBigInteger('print_day')->nullable();
            $table->unsignedBigInteger('due_day')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_document_headers', function (Blueprint $table) {
            //
        });
    }
};
