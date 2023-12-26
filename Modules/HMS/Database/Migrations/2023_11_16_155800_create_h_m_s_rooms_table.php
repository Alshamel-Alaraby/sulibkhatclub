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
        Schema::create('h_m_s_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('name_e')->nullable();
           $table->foreignId('branch_id')->nullable()->constrained('general_branches')->cascadeOnDelete();
            $table->foreignId('rooms_category_id')->nullable()->constrained('h_m_s_rooms_categories')->cascadeOnDelete();
            $table->longText('note')->nullable();
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
        Schema::dropIfExists('h_m_s_rooms');
    }
};
