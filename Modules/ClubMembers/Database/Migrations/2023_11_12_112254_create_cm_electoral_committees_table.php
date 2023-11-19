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
        Schema::create('cm_electoral_committees', function (Blueprint $table) {
            $table->id();
            $table->string('name',100)->unique();
            $table->string('name_e',100)->unique();
            $table->integer('number_of_individuals');
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
        Schema::dropIfExists('cm_electoral_committees');
    }
};
