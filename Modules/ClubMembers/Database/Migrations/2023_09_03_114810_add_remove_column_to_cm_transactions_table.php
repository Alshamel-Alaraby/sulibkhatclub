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
            $table->year('year')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->dropColumn('number_of_years');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cm_transactions', function (Blueprint $table) {
        });

    }
};
