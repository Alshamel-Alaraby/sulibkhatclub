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
        Schema::table('rlst_unit_services', function (Blueprint $table) {
            if(Schema::hasColumn('rlst_unit_services', 'unit_id')) {
                $table->foreign('unit_id', 'rlst_unit_services_unit_id_foreign')->references('id')->on('rlst_units')->onDelete('restrict');
                $table->index('unit_id', 'rlst_unit_services_unit_id_index');
            }
            if(Schema::hasColumn('rlst_unit_services', 'service_id')) {
                $table->foreign('service_id', 'rlst_unit_services_service_id_foreign')->references('id')->on('rlst_services')->onDelete('restrict');
                $table->index('service_id', 'rlst_unit_services_service_id_index');
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
        Schema::table('rlst_unit_services', function (Blueprint $table) {
            if(Schema::hasColumn('rlst_unit_services', 'unit_id')) {
                $table->dropForeign(['unit_id']);
            }
            if(Schema::hasColumn('rlst_unit_services', 'service_id')) {
                $table->dropForeign(['service_id']);
            }
            $conn = Schema::getConnection();
            $dbSchemaManager = $conn->getDoctrineSchemaManager();
            $indexes = $dbSchemaManager->listTableIndexes('rlst_unit_services');

            foreach ($indexes as $index) {
                $columns = $index->getColumns();

                if (in_array('unit_id', $columns)) {
                    $table->dropIndex($index->getName());
                }
                if (in_array('service_id', $columns)) {
                    $table->dropIndex($index->getName());
                }
            }
        });
    }
};
