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
            $table->foreignId('work_nature_id')->nullable()->constrained('general_work_natures');
            $table->string('signature')->nullable();
            $table->string('address')->nullable();
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
            $table->dropForeign(['work_nature_id']);
            $table->dropColumn('work_nature_id');
            $table->dropColumn('signature');
            $table->dropColumn('address');
        });
    }
};
