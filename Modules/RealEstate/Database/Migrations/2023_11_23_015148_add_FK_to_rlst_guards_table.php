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
        Schema::table('rlst_guards', function (Blueprint $table) {
            if(Schema::hasColumn('rlst_guards', 'building_id')) {
                $table->foreign('building_id', 'rlst_guards_building_id_foreign')->references('id')->on('rlst_buildings')->onDelete('restrict');
                $table->index('building_id', 'rlst_guards_building_id_index');
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rlst_guards', function (Blueprint $table) {
            if(Schema::hasColumn('rlst_guards', 'building_id')) {
                $table->dropForeign(['building_id']);
            }

            $conn = Schema::getConnection();
            $dbSchemaManager = $conn->getDoctrineSchemaManager();
            $indexes = $dbSchemaManager->listTableIndexes('rlst_unit_services');

            foreach ($indexes as $index) {
                $columns = $index->getColumns();

                if (in_array('building_id', $columns)) {
                    $table->dropIndex($index->getName());
                }
            }
        });
    }
};
