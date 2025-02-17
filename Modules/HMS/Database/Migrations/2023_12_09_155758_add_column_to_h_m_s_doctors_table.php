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
        Schema::table('h_m_s_doctors', function (Blueprint $table) {
            $table->after('name_e', function (Blueprint $table) {
                $table->enum('type',['indoctor','outdoctor'])->default('indoctor');
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
        Schema::table('h_m_s_doctors', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
