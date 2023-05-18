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
        Schema::create('general_document_headers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_status_id')->nullable();
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->date('date')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('prefix')->nullable();
            $table->unsignedBigInteger('serial_id')->nullable();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('related_document_id')->nullable();
            $table->unsignedBigInteger('related_document_number')->nullable();
            $table->unsignedBigInteger('related_document_prefix')->nullable();
            $table->unsignedBigInteger('sell_method_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('task_id')->nullable();
            $table->unsignedBigInteger('external_salesmen_id')->nullable();
            $table->double('total_invoice')->nullable();
            $table->double('invoice_discount')->nullable();
            $table->double('net_invoice')->nullable();
            $table->string('remaining')->nullable();
            $table->string('complete_status')->nullable()->comment('UnDelivered - partially - Delivered');
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
        Schema::dropIfExists('general_document_headers');
    }
};
