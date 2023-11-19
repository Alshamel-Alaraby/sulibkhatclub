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
        Schema::create('cm_member_history_transform', function (Blueprint $table) {
            $table->id();
            $table->integer('sponser_id_from')->index();
            $table->integer('sponser_id_to')->index();
            $table->integer('member_id')->index();
            $table->date('date')->comment('date when the transform is process');
            $table->unsignedInteger('company_id')->index()->nullable();
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
        Schema::dropIfExists('cm_member_history_transform');
    }
};
