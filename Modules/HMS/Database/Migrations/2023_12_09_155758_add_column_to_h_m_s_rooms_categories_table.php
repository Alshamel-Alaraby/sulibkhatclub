<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\HMS\Entities\HMSRoomsCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('h_m_s_rooms_categories', function (Blueprint $table) {
            $table->after('is_default', function (Blueprint $table) {
                $table->boolean('does_it_have_schedule')->default(0);
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('h_m_s_rooms_categories', function (Blueprint $table) {
            $table->dropColumn('does_it_have_schedule');
        });
    }
};
