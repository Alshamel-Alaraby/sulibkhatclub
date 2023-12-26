<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rlst_buildings', function (Blueprint $table) {
            // if there is a problem talk first to mohamed/zizo before changing anything here
            // this will be for  adding foreign keys to the table and indexing

            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE rlst_buildings ENGINE = InnoDB;');
                DB::statement('ALTER TABLE general_countries ENGINE = InnoDB;');
                DB::statement('ALTER TABLE general_cities ENGINE = InnoDB;');
                DB::statement('ALTER TABLE general_governorates ENGINE = InnoDB;');
                DB::statement('ALTER TABLE general_avenues ENGINE = InnoDB;');
                DB::statement('ALTER TABLE general_streets ENGINE = InnoDB;');
                DB::statement('ALTER TABLE general_currencies ENGINE = InnoDB;');
            }


            // changing the type of all columns to unsignedBigInteger

            $table->unsignedBigInteger('country_id')->change();
            $table->unsignedBigInteger('city_id')->change();
            $table->unsignedBigInteger('governorate_id')->change();
            $table->unsignedBigInteger('avenue_id')->change();
            $table->unsignedBigInteger('street_id')->change();
            $table->unsignedBigInteger('building_currency_id')->change();





            $table->foreign('country_id', 'country_id_foreign')->references('id')->on('general_countries')->onDelete('restrict');
            $table->index('country_id', 'country_id_index');

            $table->foreign('city_id', 'city_id_foreign')->references('id')->on('general_cities')->onDelete('restrict');
            $table->index('city_id', 'city_id_index');

            $table->foreign('governorate_id', 'governorate_id_foreign')->references('id')->on('general_governorates')->onDelete('restrict');
            $table->index('governorate_id', 'governorate_id_index');

            $table->foreign('avenue_id', 'avenue_id_foreign')->references('id')->on('general_avenues')->onDelete('restrict');
            $table->index('avenue_id', 'avenue_id_index');


            $table->foreign('street_id', 'street_id_foreign')->references('id')->on('general_streets')->onDelete('restrict');
            $table->index('street_id', 'street_id_index');


            $table->foreign('building_currency_id', 'building_currency_id_foreign')->references('id')->on('general_currencies')->onDelete('restrict');
            $table->index('building_currency_id', 'building_currency_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rlst_buildings', function (Blueprint $table) {

            $table->dropForeign('country_id_foreign');
            $table->dropIndex('country_id_index');

            $table->dropForeign('city_id_foreign');
            $table->dropIndex('city_id_index');

            $table->dropForeign('governorate_id_foreign');
            $table->dropIndex('governorate_id_index');

            $table->dropForeign('avenue_id_foreign');
            $table->dropIndex('avenue_id_index');

            $table->dropForeign('street_id_foreign');
            $table->dropIndex('street_id_index');

            $table->dropForeign('building_currency_id_foreign');
            $table->dropIndex('building_currency_id_index');
        });
    }
};
