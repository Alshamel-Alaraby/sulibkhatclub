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
        Schema::table('cm_members', function (Blueprint $table) {
            $table->foreignId('auto_member_type_id')->default(1);

            $table->dropColumn('member_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cm_members', function (Blueprint $table) {
            $table->string('member_type')->default('pending')->nullable();

            $table->dropColumn('auto_member_type_id');
        });
    }
};
