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
        Schema::create('hotkey_role', function (Blueprint $table) {
            $table->id();
            $table->string('column_name');
            $table->string('table');
            $table->boolean('can_see');
            $table->boolean('can_edit');
            $table->boolean('can_plus');
            $table->integer('percentage');

            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

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
        Schema::dropIfExists('hotkey_role');
    }
};
