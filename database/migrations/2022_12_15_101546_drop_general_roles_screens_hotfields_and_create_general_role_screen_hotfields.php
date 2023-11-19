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
        Schema::dropIfExists('general_roles_screens_hotfields');
        Schema::create('general_role_screen_hotfields', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('role_id')->index()->nullable();
            $table->unsignedInteger('workflow_id')->index()->nullable();
            $table->unsignedInteger('hotfield_id')->index()->nullable();
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
        //
    }
};
