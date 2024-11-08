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
            $table->foreignId('sub_location_id')->nullable()->constrained('general_locations');
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
            $table->dropForeign(['sub_location_id']);
            $table->dropColumn('sub_location_id');
        });
    }
};
