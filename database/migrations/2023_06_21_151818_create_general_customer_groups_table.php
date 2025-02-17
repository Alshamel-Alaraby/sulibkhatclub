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
        Schema::create('general_customer_groups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_e');
            $table->double('discount')->default(0);
            $table->boolean('is_default')->default(0);
            $table->unsignedBigInteger('company_id')->index()->nullable();
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
        Schema::dropIfExists('general_customer_groups');
    }
};
