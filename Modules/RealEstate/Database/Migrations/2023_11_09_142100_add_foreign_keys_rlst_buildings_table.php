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

            $table->unsignedBigInteger('building_type_id')->change();

            // if there a problem, talk first to Ezzat & Mohamed before changing this code
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE rlst_buildings ENGINE = InnoDB;');
            }

            $table->foreign('building_type_id', 'building_type_id_foreign')->references('id')->on('rlst_building_types')->onDelete('restrict');
            $table->index('building_type_id', 'building_type_id_index');

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

            // if there a problem, talk first to Ezzat & Mohamed before changing this code
            $table->dropForeign('building_type_id_foreign');
            $table->dropIndex('building_type_id_index');

        });
    }
};
