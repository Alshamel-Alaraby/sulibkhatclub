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
        Schema::table('rlst_buildings', function (Blueprint $table) {
            if(Schema::hasColumn('rlst_buildings', 'building_category_id')) {
                $table->foreign('building_category_id', 'rlst_buildings_building_category_id_foreign')->references('id')->on('rlst_building_categories')->onDelete('restrict');
                $table->index('building_category_id', 'rlst_buildings_building_category_id_index');
               
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
        Schema::table('rlst_buildings', function (Blueprint $table) {
            if(Schema::hasColumn('rlst_buildings', 'building_category_id')) {
                $table->dropForeign(['building_category_id']);
            }

            $conn = Schema::getConnection();
            $dbSchemaManager = $conn->getDoctrineSchemaManager();
            $indexes = $dbSchemaManager->listTableIndexes('rlst_revenues_items');
            
            foreach ($indexes as $index) {
                $columns = $index->getColumns();
                
                if (in_array('building_category_id', $columns)) {
                    $table->dropIndex($index->getName());
                }
            }
            
        });
    }
};
