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
        Schema::table('general_tasks', function (Blueprint $table) {
            $table->time('actual_start_time')->nullable();
            $table->time('actual_end_time')->nullable();
            $table->date('actual_execution_date')->nullable();
            $table->string('actual_execution_duration')->nullable();
            $table->date('actual_execution_end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_tasks', function (Blueprint $table) {
            $table->dropColumn('actual_start_time')->nullable();
            $table->dropColumn('actual_end_time')->nullable();
            $table->dropColumn('actual_execution_date')->nullable();
            $table->dropColumn('actual_execution_duration')->nullable();
            $table->dropColumn('actual_execution_end_date')->nullable();
        });
    }
};
