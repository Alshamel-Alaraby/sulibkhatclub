<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        $conn = Schema::getConnection();
        $dbSchemaManager = $conn->getDoctrineSchemaManager();

        $foreignKeys = $dbSchemaManager->listTableForeignKeys('rlst_building_wallet');
        foreach ($foreignKeys as $foreignKey) {
            if ($foreignKey->getLocalColumns() === ['building_type_id']) {
                $this->dropForeignKeyIfExists('rlst_building_wallet', $foreignKey->getName());
            }
        }

        $indexes = $dbSchemaManager->listTableIndexes('rlst_building_wallet');
        foreach ($indexes as $index) {
            if ($index->getColumns() === ['building_type_id']) {
                $this->dropIndexIfExists('rlst_building_wallet', $index->getName());
            }
        }
    }

    public function down()
    {
        Schema::table('rlst_building_wallet', function (Blueprint $table) {
            $table->foreign('building_type_id', 'building_wallet_building_type_id_foreign')
                ->references('id')->on('rlst_building_types')->onDelete('restrict');

            $table->index('building_type_id', 'building_wallet_building_type_id_index');
        });
    }

    private function dropForeignKeyIfExists($table, $foreignKey)
    {
        if (Schema::hasTable($table)) {
            Schema::table($table, function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey);
            });
        }
    }

    private function dropIndexIfExists($table, $index)
    {
        if (Schema::hasTable($table)) {
            Schema::table($table, function (Blueprint $table) use ($index) {
                $table->dropIndex($index);
            });
        }
    }
};
