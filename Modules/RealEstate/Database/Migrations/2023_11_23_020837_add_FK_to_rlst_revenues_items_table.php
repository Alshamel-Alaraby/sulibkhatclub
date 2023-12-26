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
        Schema::table('rlst_revenues_items', function (Blueprint $table) {
            if(Schema::hasColumn('rlst_revenues_items', 'chart_id')) {
                $table->foreign('chart_id', 'rlst_revenues_items_chart_id_foreign')->references('id')->on('gl_chart')->onDelete('restrict');
                $table->index('chart_id', 'rlst_revenues_items_chart_id_index');
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
        Schema::table('rlst_revenues_items', function (Blueprint $table) {
            if(Schema::hasColumn('rlst_revenues_items', 'chart_id')) {
                $table->dropForeign(['chart_id']);
            }

            $conn = Schema::getConnection();
            $dbSchemaManager = $conn->getDoctrineSchemaManager();
            $indexes = $dbSchemaManager->listTableIndexes('rlst_revenues_items');
            
            foreach ($indexes as $index) {
                $columns = $index->getColumns();
                
                if (in_array('chart_id', $columns)) {
                    $table->dropIndex($index->getName());
                }
            }
            
        });
    }
};
