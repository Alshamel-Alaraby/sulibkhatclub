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
        Schema::table('rlst_units', function (Blueprint $table) {
            // if there is a problem talk first to mohamed/zizo before changing anything here
            // this will be for  adding foreign keys to the table and indexing

            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE rlst_units ENGINE = InnoDB;');
                DB::statement('ALTER TABLE rlst_buildings ENGINE = InnoDB;');
                DB::statement('ALTER TABLE rlst_units_type ENGINE = InnoDB;');
                DB::statement('ALTER TABLE rlst_unit_statuses ENGINE = InnoDB;');
                DB::statement('ALTER TABLE rlst_views ENGINE = InnoDB;');
                DB::statement('ALTER TABLE rlst_finishings ENGINE = InnoDB;');
            }

            // changing the type of all columns to unsignedBigInteger
            $table->unsignedBigInteger('building_id')->change();
            $table->unsignedBigInteger('unit_ty')->change();
            $table->unsignedBigInteger('unit_status_id')->change();
            $table->unsignedBigInteger('view')->change();
            $table->unsignedBigInteger('finishing')->change();


            $table->foreign('building_id', 'building_id_foreign')->references('id')->on('rlst_buildings')->onDelete('restrict');
            $table->index('building_id', 'building_id_index');

            $table->foreign('unit_ty', 'unit_ty_foreign')->references('id')->on('rlst_units_type')->onDelete('restrict');
            $table->index('unit_ty', 'unit_ty_index');

            $table->foreign('unit_status_id', 'unit_status_id_foreign')->references('id')->on('rlst_unit_statuses')->onDelete('restrict');
            $table->index('unit_status_id', 'unit_status_id_index');

            $table->foreign('view', 'view_foreign')->references('id')->on('rlst_views')->onDelete('restrict');
            $table->index('view', 'view_index');

            $table->foreign('finishing', 'finishing_foreign')->references('id')->on('rlst_finishings')->onDelete('restrict');
            $table->index('finishing', 'finishing_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rlst_units', function (Blueprint $table) {
            $table->dropForeign('building_id_foreign');
            $table->dropIndex('building_id_index');

            $table->dropForeign('unit_ty_foreign');
            $table->dropIndex('unit_ty_index');

            $table->dropForeign('unit_status_id_foreign');
            $table->dropIndex('unit_status_id_index');

            $table->dropForeign('view_foreign');
            $table->dropIndex('view_index');

            $table->dropForeign('finishing_foreign');
            $table->dropIndex('finishing_index');
        });
    }
};
