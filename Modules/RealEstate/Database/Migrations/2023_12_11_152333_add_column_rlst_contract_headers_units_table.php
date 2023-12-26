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
        Schema::table('rlst_contract_headers_units', function (Blueprint $table) {
            $table->dropForeignIdFor(\Modules\RealEstate\Entities\RlstUnitService::class,'unit_service_id');
            $table->json('unit_service')->nullable();
            $table->foreignId('unit_id')->nullable()->references('id')->on('rlst_units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rlst_contract_headers_units', function (Blueprint $table) {

        });
    }
};
