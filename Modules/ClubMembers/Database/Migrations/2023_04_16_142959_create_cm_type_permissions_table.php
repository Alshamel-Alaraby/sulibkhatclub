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
        Schema::create('cm_type_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cm_members_type_id');
            $table->json('cm_permissions_id');
            $table->unsignedBigInteger("cm_financial_status_id")->nullable();
            $table->string("membership_period")->nullable();
            $table->string("allowed_subscription_date")->nullable();
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
        Schema::dropIfExists('cm_type_permissions');
    }
};
