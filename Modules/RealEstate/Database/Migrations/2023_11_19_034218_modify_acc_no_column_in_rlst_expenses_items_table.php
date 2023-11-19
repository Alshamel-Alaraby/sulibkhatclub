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
        Schema::table('rlst_expenses_items', function (Blueprint $table) {
            $table->dropColumn('acc_no');
            $table->unsignedBigInteger('chart_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rlst_expenses_items', function (Blueprint $table) {
            $table->string('acc_no', 20);
            $table->dropColumn('chart_id');
        });
    }
};
