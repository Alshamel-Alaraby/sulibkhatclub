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
        Schema::create('general_insurance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title', 50)->comment('العنوان عربى');
            $table->string('title_e', 50)->comment('العنوان انجليزي');
            $table->string('db_table', 50)->comment('جدول قواعد البيانات');
            $table->string('db_field', 50)->comment('الحقل');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_insurance_settings');
    }
};
