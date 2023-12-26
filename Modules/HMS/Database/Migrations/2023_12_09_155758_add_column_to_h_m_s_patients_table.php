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
        Schema::table('h_m_s_patients', function (Blueprint $table) {
            $table->after('gender', function (Blueprint $table) {
                $table->enum('type',['inpatient','outpatient'])->default('inpatient');
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
        Schema::table('h_m_s_patients', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
