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
        Schema::create('boards_rent_order_task', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('general_customers')->onDelete('cascade');
            $table->foreign('task_id')
                ->references('id')
                ->on('boards_rent_tasks')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('general_users')
                ->onDelete('set null');
            $table->foreign('order_id')
                ->references('id')
                ->on('boards_rent_orders')
                ->onDelete('set null');
            $table->string('status');
            $table->date('notification_date')->nullable();
            $table->timestamp('start')->nullable();
            $table->timestamp('end')->nullable();
            $table->text('note')->nullable();
            $table->boolean('must_completed')->nullable();
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
        Schema::dropIfExists('boards_rent_order_task');
    }
};
