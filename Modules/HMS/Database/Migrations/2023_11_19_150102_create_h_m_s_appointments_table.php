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
        Schema::create('h_m_s_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained('h_m_s_patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('h_m_s_doctors')->onDelete('cascade');
            $table->foreignId('from_doctor_id')->nullable()->constrained('h_m_s_doctors')->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained('h_m_s_rooms')->onDelete('cascade');
            $table->date('date');
            $table->integer('number')->nullable();
            $table->mediumText('reason')->nullable();
            $table->boolean('sms_notify')->default(0);
            $table->boolean('whatsapp_notify')->default(0);
            $table->boolean('email_notify')->default(0);
            $table->enum('status',['Pending','Processing','Waiting','Attended','Cancelled'])->default('Pending');
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
        Schema::dropIfExists('h_m_s_appointments');
    }
};
