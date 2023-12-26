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
        Schema::table('rlst_building_wallet', function (Blueprint $table) {
            // if there is a problem talk first to mohamed/zizo before changing anything here
            // this will be for  adding foreign keys to the table and indexing

            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE rlst_building_wallet ENGINE = InnoDB;');
                DB::statement('ALTER TABLE rlst_wallets ENGINE = InnoDB;');
                DB::statement('ALTER TABLE rlst_buildings ENGINE = InnoDB;');
                DB::statement('ALTER TABLE rlst_building_types ENGINE = InnoDB;');
            }

            // changing the type of all columns to unsignedBigInteger
            $table->unsignedBigInteger('building_type_id')->change();


            // adding the foreign keys
            $table->foreign('wallet_id', 'building_wallet_wallet_id_foreign')->references('id')->on('rlst_wallets')->onDelete('restrict');
            $table->index('wallet_id', 'building_wallet_wallet_id_index');

            $table->foreign('building_id', 'building_wallet_building_id_foreign')->references('id')->on('rlst_buildings')->onDelete('restrict');
            $table->index('building_id', 'building_wallet_building_id_index');

            $table->foreign('building_type_id', 'building_wallet_building_type_id_foreign')->references('id')->on('rlst_building_types')->onDelete('restrict');
            $table->index('building_type_id', 'building_wallet_building_type_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rlst_building_wallet', function (Blueprint $table) {
            $table->dropForeign('building_wallet_wallet_id_foreign');
            $table->dropIndex('building_wallet_wallet_id_index');

            $table->dropForeign('building_wallet_building_id_foreign');
            $table->dropIndex('building_wallet_building_id_index');

            $table->dropForeign('building_wallet_building_type_id_foreign');
            $table->dropIndex('building_wallet_building_type_id_index');
        });
    }
};
