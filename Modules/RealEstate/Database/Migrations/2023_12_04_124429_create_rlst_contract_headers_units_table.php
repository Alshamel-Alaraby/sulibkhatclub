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
        Schema::create('rlst_contract_headers_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_header_id')->references('id')->on('rlst_contract_headers');
            $table->foreignId('unit_service_id')->references('id')->on('rlst_unit_services');
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
        Schema::dropIfExists('rlst_contract_headers_units');
    }
};
