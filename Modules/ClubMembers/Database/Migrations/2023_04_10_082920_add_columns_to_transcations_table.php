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

        // add columns to general_transactions
        Schema::table('general_transactions', function (Blueprint $table) {
            $table->unsignedInteger('cm_member_id')->nullable();
            $table->date("date_from")->nullable();
            $table->date("date_to")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cm_memberships_renewals');
    }
};
