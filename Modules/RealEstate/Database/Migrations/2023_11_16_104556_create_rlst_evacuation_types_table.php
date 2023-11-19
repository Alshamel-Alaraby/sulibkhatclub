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
        Schema::create('rlst_evacuation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('name_e', 50);
            $table->unsignedBigInteger('parent_id')->comment('tree, 2-levels');
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
        Schema::dropIfExists('rlst_evacuation_types');
    }
};
