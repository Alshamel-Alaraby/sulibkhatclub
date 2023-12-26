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
        Schema::create('general_insurance_documents', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('related_id');

            $table->unsignedBigInteger('insurance_type_id')->unsigned()->index()->comment('نوع الوثيقه');
            $table->foreign('insurance_type_id')->references('id')->on('general_insurance_types');

            $table->string('insurance_number',50)->comment('رقم الوثيقه');

            $table->unsignedBigInteger('insurance_company_id')->unsigned()->index()->comment('جهة الاصدار');
            $table->foreign('insurance_company_id')->references('id')->on('general_insurance_companies');

            $table->double('amount')->comment('مبلغ التأمين');

            $table->date('from_date')->comment('تاريخ الاصدار');
            $table->date('to_date')->comment('تاريخ الانتهاء');
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
        Schema::dropIfExists('general_insurance_documents');
    }
};
