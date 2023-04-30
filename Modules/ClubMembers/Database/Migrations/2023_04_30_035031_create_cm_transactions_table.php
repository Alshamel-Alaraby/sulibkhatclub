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
        Schema::create('cm_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("document_id")->comment('NoEntry, NoDisplay');

            $table->unsignedInteger('branch_id')->comment('Entry');
            $table->unsignedInteger('serial_id')->comment('Entry');
            $table->string('prefix')->nullable()->comment('Entry'); // NULLABLE
            $table->string('serial_number')->comment('Entry');
            $table->date('date')->comment('Entry');
            $table->unsignedInteger('cm_member_id')->comment('Entry');

            $table->unsignedInteger('member_number')->nullable()->comment('Display, ZERO case applying'); // NULLABLE
            $table->unsignedInteger('sponsor_id')->nullable()->comment('Display'); // NULLABLE
            $table->string('type')->comment('(Display)subscribe,renew');
            $table->unsignedSmallInteger('year_from')->comment('Display');
            $table->unsignedSmallInteger('year_to')->comment('Display');
            $table->unsignedTinyInteger('number_of_years')->comment('Display, max: 255 years'); 
            $table->double('amount')->comment('Display');
            

            $table->unsignedInteger('invoice_id')->nullable(); // NULLABLE ???
            $table->unsignedInteger('break_id')->nullable(); // NULLABLE ???
            
            $table->string('notes')->nullable(); // NULLABLE
            
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
        Schema::dropIfExists('cm_transactions');
    }
};
