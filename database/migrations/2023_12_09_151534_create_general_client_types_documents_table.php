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
        Schema::create('general_client_types_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->nullable()->references('id')->on('general_documents');
            $table->foreignId('client_type_id')->nullable()->references('id')->on('general_client_types');
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
        Schema::dropIfExists('general_client_types_documents');
    }
};
