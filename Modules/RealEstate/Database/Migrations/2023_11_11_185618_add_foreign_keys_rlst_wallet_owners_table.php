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
        Schema::table('rlst_wallet_owners', function (Blueprint $table) {
            // if there is a problem talk first to mohamed/zizo before changing anything here
            // this will be for  adding foreign keys to the table and indexing

            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE rlst_wallet_owners ENGINE = InnoDB;');
                DB::statement('ALTER TABLE rlst_wallets ENGINE = InnoDB;');
                DB::statement('ALTER TABLE rlst_owners ENGINE = InnoDB;');
            }

        });

        Schema::table('rlst_wallet_owners', function (Blueprint $table) {
            $table->foreign('wallet_id', 'wallet_id_foreign')->references('id')->on('rlst_wallets')->onDelete('restrict');
            $table->index('wallet_id', 'wallet_id_index');

            $table->foreign('owner_id', 'owner_id_foreign')->references('id')->on('rlst_owners')->onDelete('restrict');
            $table->index('owner_id', 'owner_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rlst_wallet_owners', function (Blueprint $table) {
            $table->dropForeign('wallet_id_foreign');
            $table->dropIndex('wallet_id_index');

            $table->dropForeign('owner_id_foreign');
            $table->dropIndex('owner_id_index');

        });
    }
};
