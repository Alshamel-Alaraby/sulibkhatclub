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
        Schema::table('general_document_header_details', function (Blueprint $table) {
            $table->foreignId('building_id')->nullable()->references('id')->on('rlst_buildings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_document_header_details', function (Blueprint $table) {
            //
        });
    }
};
