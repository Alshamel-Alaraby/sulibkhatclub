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
        Schema::table('cm_statues', function (Blueprint $table) {
            $table->unsignedBigInteger('member_type_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cm_statues', function (Blueprint $table) {

        });
    }
};
