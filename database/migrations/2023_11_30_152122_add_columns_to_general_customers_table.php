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
        Schema::table('general_customers', function (Blueprint $table) {
            $table->string('driving_license', 50)->unique()->nullable()->comment('رخصة القيادة');
            $table->date('license_exp_date')->nullable()->comment('تاريخ انتهاء رخصة القياده');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_customers', function (Blueprint $table) {
            //
        });
    }
};
