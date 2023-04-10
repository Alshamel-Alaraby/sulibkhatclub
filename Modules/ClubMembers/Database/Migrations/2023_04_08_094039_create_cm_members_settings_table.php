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
        Schema::create('cm_members_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_type_id')->nullable();
            $table->foreignId('financial_status_id')->nullable();
            $table->foreignId('general_status_id')->nullable();
            $table->date('date');
            $table->softDeletes();
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
        Schema::dropIfExists('cm_members_settings');
    }
};
