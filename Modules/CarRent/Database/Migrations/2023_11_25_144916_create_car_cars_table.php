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
        Schema::create('car_cars', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number',50)->unique()->comment('رقم اللوحة');

            $table->unsignedBigInteger('brand_id')->unsigned()->index()->comment('الماركة');
            $table->foreign('brand_id')->references('id')->on('general_brands');

            $table->unsignedBigInteger('model_id')->unsigned()->index()->comment('موديل السيارة');
            $table->foreign('model_id')->references('id')->on('car_models');

            $table->year('year_manufacture')->comment('سنة الصنع');
            $table->string('chassis_number',50)->unique()->comment('رقم الشاسيه');

            $table->unsignedBigInteger('transmission_id')->unsigned()->index()->comment('ناقل الحركة');
            $table->foreign('transmission_id')->references('id')->on('car_transmissions');

            $table->unsignedBigInteger('sunroof_id')->unsigned()->index()->comment('فتحة السقف');
            $table->foreign('sunroof_id')->references('id')->on('car_sunroofs');

            $table->unsignedBigInteger('body_type_id')->unsigned()->index()->comment('نوع الهيكل');
            $table->foreign('body_type_id')->references('id')->on('car_body_types');

            $table->integer('cylinders')->comment('عدد الاسطوانات');

            $table->unsignedBigInteger('country_id')->unsigned()->index()->comment('بلد الصنع');
            $table->foreign('country_id')->references('id')->on('general_countries');

            $table->unsignedBigInteger('color_exterior_id')->unsigned()->index()->comment('اللون الخارجى');
            $table->foreign('color_exterior_id')->references('id')->on('general_colors');

            $table->unsignedBigInteger('fuel_type_id')->unsigned()->index()->comment('نوع الوقود');
            $table->foreign('fuel_type_id')->references('id')->on('car_fuel_types');

            $table->unsignedBigInteger('color_interior_id')->unsigned()->index()->comment('اللون الداخلي');
            $table->foreign('color_interior_id')->references('id')->on('general_colors');

            $table->unsignedBigInteger('seats_material_id')->unsigned()->index()->comment('نوع المقاعد');
            $table->foreign('seats_material_id')->references('id')->on('car_seats_materials');

            $table->integer('passengers')->comment('عدد المقاعد');

            $table->double('financial_value')->comment('القيمة المالية');

            $table->integer('current_odometer')->comment('عداد المسافات الحالي');

            $table->json('specifications')->comment('المواصفات');

            // $table->unsignedBigInteger('specification_id')->comment('المواصفات');

            $table->double('depreciation_percentage')->comment('نسبة الاهلاك');

            $table->string('barcode',50)->comment('الباركود');

            $table->string('jpscode',50)->comment('jpscode');

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
        Schema::dropIfExists('car_cars');
    }
};
