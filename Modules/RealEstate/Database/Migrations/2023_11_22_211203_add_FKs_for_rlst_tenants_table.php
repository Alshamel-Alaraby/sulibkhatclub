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
        Schema::table('rlst_tenants', function (Blueprint $table) {
            
            if(Schema::hasColumn('rlst_tenants', 'chart_id')) {
                $table->foreign('chart_id', 'rlst_tenants_chart_id_foreign')->references('id')->on('gl_chart')->onDelete('restrict');
                $table->index('chart_id', 'rlst_tenants_chart_id_index');
            }
            
            
            if(Schema::hasColumn('rlst_tenants', 'tenant_type_id')) {
                $table->foreign('tenant_type_id', 'rlst_tenants_tenant_type_id_foreign')->references('id')->on('rlst_tenant_types')->onDelete('restrict');
                $table->index('tenant_type_id', 'rlst_tenants_tenant_type_id_index');
            }

            if(Schema::hasColumn('rlst_tenants', 'country_id')) {
                $table->foreign('country_id', 'rlst_tenants_country_id_foreign')->references('id')->on('general_countries')->onDelete('restrict');
                $table->index('country_id', 'rlst_tenants_country_id_index');
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
        Schema::table('rlst_tenants', function (Blueprint $table) {
            if(Schema::hasColumn('rlst_tenants', 'chart_id')) {
                $table->dropForeign(['chart_id']);
            }
            
            if(Schema::hasColumn('rlst_tenants', 'tenant_type_id')) {
                $table->dropForeign(['tenant_type_id']);
            }

            if(Schema::hasColumn('rlst_tenants', 'country_id')) {
                $table->dropForeign(['country_id']);
            }

            $conn = Schema::getConnection();
            $dbSchemaManager = $conn->getDoctrineSchemaManager();
            $indexes = $dbSchemaManager->listTableIndexes('rlst_unit_services');
            
            foreach ($indexes as $index) {
                $columns = $index->getColumns();
                
                if (in_array('chart_id', $columns)) {
                    $table->dropIndex($index->getName());
                }

                if (in_array('tenant_type_id', $columns)) {
                    $table->dropIndex($index->getName());
                }

                if (in_array('country_id', $columns)) {
                    $table->dropIndex($index->getName());
                }


            }

        });
    }
};
