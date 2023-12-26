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
        Schema::table('rlst_tenants', function (Blueprint $table) {
            $table->enum('blacklisted', ['yes', 'no'])->default('no');
            $table->enum('is_whatsapp', ['yes', 'no']);
            $table->enum('gender', ['m', 'f'])->default('m');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed']);
            $table->unsignedBigInteger('country_id')->comment('nationality, from genenal_countries table');
            $table->string('work_address', 255);
            $table->string('home_address', 255);
            $table->string('civil_no', 20);
            $table->string('passport_no', 20);
            $table->string('residence_no', 20);
            $table->unsignedBigInteger('chart_id')->comment('from gl_chart table, رقم الحساب');
            $table->unsignedBigInteger('tenant_type_id')->comment('from rlst_tenant_types table');
            $table->string('mobile_code', 5);
            $table->string('mobile', 20);
            $table->string('email', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rlst_tenants', function (Blueprint $table) {
            $table->dropColumn('blacklisted');
            $table->dropColumn('is_whatsapp');
            $table->dropColumn('gender');
            $table->dropColumn('marital_status');
            $table->dropColumn('country_id');
            $table->dropColumn('work_address');
            $table->dropColumn('home_address');
            $table->dropColumn('civil_no');
            $table->dropColumn('passport_no');
            $table->dropColumn('residence_no');
            $table->dropColumn('chart_id');
            $table->dropColumn('tenant_type_id');
            $table->dropColumn('mobile_code');
            $table->dropColumn('mobile');
            $table->dropColumn('email');
        });
    }
};
