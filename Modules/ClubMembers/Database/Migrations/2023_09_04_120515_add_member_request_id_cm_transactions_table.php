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
        Schema::table('cm_transactions', function (Blueprint $table) {
            $table->unsignedInteger('member_request_id')->nullable();
            $table->unsignedInteger('cm_member_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('', function (Blueprint $table) {
            $table->unsignedInteger('cm_member_id')->comment('Entry');
        });
    }
};
