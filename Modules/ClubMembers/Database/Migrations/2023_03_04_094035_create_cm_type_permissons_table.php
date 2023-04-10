<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// hello

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
            $table->unsignedBigInteger('cm_permissions_id');
            $table->unsignedBigInteger("cm_financial_status_id");
            $table->unsignedBigInteger("membership_period");
            $table->unsignedBigInteger("allowed_subscription_date");
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
