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
        Schema::create('general_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_e');
            $table->unsignedBigInteger('parent_id')->index()->nullable();
            $table->tinyInteger('is_valid')->default(false);
            $table->tinyInteger('is_default')->default(true);
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
        Schema::dropIfExists('general_priorities');
    }
};
