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
        Schema::create('rlst_contract_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->references('id')->on('general_branches');
            $table->date('date');
            $table->foreignId('serial_id')->nullable()->references('id')->on('general_serials');
            $table->foreignId('related_document_id')->nullable()->references('id')->on('general_documents');
            $table->unsignedBigInteger('related_document_number')->nullable();
            $table->unsignedBigInteger('related_document_prefix')->nullable();
            $table->foreignId('employee_id')->references('id')->on('general_employees');
            $table->foreignId('tenant_id')->references('id')->on('rlst_tenants');
            $table->integer('notice_period')->default(0);
            $table->double('insurance_amount')->default(0);
            $table->string('attendants')->nullable();
            $table->boolean('automatic_renews')->default(0);
            $table->foreignId('external_salesmen_id')->nullable()->references('id')->on('general_external_salesmen');
            $table->double('commission')->default(0);
            $table->boolean('posted')->default(0);
            $table->boolean('receipt_print_detail')->default(0);
            $table->foreignId('document_id')->references('id')->on('general_documents');
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
        Schema::dropIfExists('rlst_contract_headers');
    }
};
