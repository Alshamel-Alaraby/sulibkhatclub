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
            $conn = Schema::getConnection();
            $dbSchemaManager = $conn->getDoctrineSchemaManager();
            $foreignKeys = $dbSchemaManager->listTableForeignKeys($table->getTable());
    
            foreach ($foreignKeys as $foreignKey) {
                if ($foreignKey->getLocalColumns() === ['building_type_id']) {
                    $table->dropForeign($foreignKey->getName());
                    break;
                }
            }            
            
            if (Schema::hasColumn('rlst_building_wallet', 'building_type_id')) {
              $table->dropColumn('building_type_id');
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
        Schema::table('rlst_building_wallet', function (Blueprint $table) {
            if(!Schema::hasColumn('rlst_building_wallet', 'building_type_id')){
                $table->dropColumn('building_type_id');
            }
            
        });
    }
};
