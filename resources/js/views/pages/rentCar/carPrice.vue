<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import loader from "../../../components/general/loader";
import translation from "../../../helper/mixin/translation-mixin";
import customTable from "../../../helper/mixin/customTable";
import crudHelper from "../../../helper/mixin/crudHelper";
import CarPriceFilter from "../../../components/create/rent-car/carPriceFilter";
import searchPage from "../../../components/general/searchPage";
import actionSetting from "../../../components/general/actionSetting";
import tableCustom from "../../../components/general/tableCustom";
import permissionGuard from "../../../helper/permission";
/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Car Price",
        meta: [{ name: "description", content: "Car Price" }],
    },
    mixins: [translation,customTable,crudHelper],
    components: {
        Layout, PageHeader, loader, searchPage,
        actionSetting, tableCustom, CarPriceFilter
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Car Price", "all Car Price");
        });
    },
    data() {
        return {
            url: '/car-rent/car-cars',
            searchMain: '',
            tableSetting: [
                {
                    isFilter: true,isSet: true,trans:"car_car_plate_number",isV: 'plate_number',
                    type: 'string',sort: true,setting: {"plate_number":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_car_year_manufacture",isV: 'year_manufacture',
                    type: 'string',sort: true,setting: {"year_manufacture":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_car_financial_value",isV: 'financial_value',
                    type: 'string',sort: true,setting: {"financial_value":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_car_country", isV: 'country_id',
                    type: 'relation',name: 'country',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"country_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_car_brand", isV: 'brand_id',
                    type: 'relation',name: 'brand',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"brand_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_car_model", isV: 'model_id',
                    type: 'relation',name: 'model',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"model_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_car_transmission", isV: 'transmission_id',
                    type: 'relation',name: 'transmission',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"model_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_car_sunroof", isV: 'sunroof_id',
                    type: 'relation',name: 'sunroof',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"sunroof_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_car_color_exterior", isV: 'color_exterior_id',
                    type: 'relation',name: 'exterior_color',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"color_exterior_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_car_fuel_type", isV: 'fuel_type_id',
                    type: 'relation',name: 'fuel_type',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"fuel_type_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_car_color_interior", isV: 'color_interior_id',
                    type: 'relation',name: 'interior_color',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"color_interior_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_car_passengers",isV: 'passengers',
                    type: 'string',sort: true,setting: {"passengers":true},isSetting: true
                },
            ],
            sendSetting: {},
            searchField: [],
        };
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
        this.settingFun();
        this.searchField = [1,2];
        this.getCustomTableFields('car_cars');
    },
    methods: {
        filterSearch(fields){
            let indexC = fields.indexOf("country_id"),
                indexG = fields.indexOf("brand_id"),
                indexCty = fields.indexOf("model_id"),
                indexcc = fields.indexOf("transmission_id"),
                indexGgg = fields.indexOf("sunroof_id"),
                indexYY = fields.indexOf("color_exterior_id"),
                indexzz = fields.indexOf("fuel_type_id"),
                indexYz = fields.indexOf("color_interior_id"),
                filter = '';
            if (indexC > -1) {
                fields[indexC] = this.$i18n.locale == "ar" ? "country.name" : "country.name_e";
            }
            if (indexG > -1) {
                fields[indexG] = this.$i18n.locale == "ar" ? "brand.name" : "brand.name_e";
            }
            if (indexCty > -1) {
                fields[indexCty] = this.$i18n.locale == "ar" ? "model.name" : "model.name_e";
            }
            if (indexcc > -1) {
                fields[indexcc] = this.$i18n.locale == "ar" ? "transmission.name" : "transmission.name_e";
            }
            if (indexGgg > -1) {
                fields[indexGgg] = this.$i18n.locale == "ar" ? "model.name" : "model.name_e";
            }
            if (indexYY > -1) {
                fields[indexYY] = this.$i18n.locale == "ar" ? "exterior_color.name" : "exterior_color.name_e";
            }
            if (indexzz > -1) {
                fields[indexzz] = this.$i18n.locale == "ar" ? "fuel_type.name" : "fuel_type.name_e";
            }
            if (indexYz > -1) {
                fields[indexYz] = this.$i18n.locale == "ar" ? "interior_color.name" : "interior_color.name_e";
            }

            for (let i = 0; i < fields.length; ++i) {
                filter += `columns[${i}]=${fields[i]}&`;
            }
            return filter;
        },
        settingFun(setting = null){
            if(setting) this.tableSetting = setting;
            let l = {},names = [];
            names = this.tableSetting.filter(e => e.isSet ).map(el => el.setting);
            this.tableSetting.forEach((e,i) => {
                l[e.isV] = names[i][e.isV];
                e['isSetting'] = l[e.isV];
            });
            this.sendSetting = l;
        },
    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- start search -->
                        <searchPage
                            page="general.carPriceTable"
                            :isVisible="isVisible"
                            :filterSetting="tableSetting"
                            :companyKeys="companyKeys"
                            :defaultsKeys="defaultsKeys"
                            @dataSearch="() => getData(1,url,filterSearch(searchField))"
                            @searchFun="(fields) => searchField = fields"
                            @editSearch="(search) => searchMain = search"
                            :isSearch="false"
                        />
                        <!-- end search -->

                        <!-- start setting -->
                        <actionSetting
                            :titleCreate="'pricing'"
                            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :current_page="current_page"
                            :isCreate="tables.length > 0 ? true : false" :isEdit="false" :isDelete="false"
                            :permissionCreate="isPermission('create group Club')"
                            :permissionUpdate="isPermission('update group Club')"
                            :permissionDelete="isPermission('delete group Club')" :isExcl="false"
                            :isPrint="false" :checkAll="checkAll" :sideAction="true" :sidePaginate="true"
                            :isFilter="true" :group="false" :isGroup="false" :isVisible="isVisible"
                            :isSetting="true" :isPaginate="true" :settings="tableSetting"
                            @delete="ids => deleteRow(ids,url)"
                            @gen_xsl="ExportExcel('xlsx')"
                            @settingFun="setting => settingFun(setting)"
                            :objPagination="objPagination"
                            @perviousOrNext="(current) => getData(current,url,filterSearch(searchField))"
                            @currentPage="(page) => current_page = page"
                            @DataCurrentPage="(page) => getDataCurrentPage(page)"
                            @actionChange="({typeAction,id}) => changeType({typeAction,id})"
                        />
                        <!-- end setting -->

                        <!--  create   -->
                        <CarPriceFilter
                            :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                            :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired" :url="url"
                            :type="type" :idObjEdit="idEdit? {idEdit,dataObj: this.tables.find(el => el.id == idEdit)}:null"
                            @getDataTable="(fter) => getData(1,url,fter)" :isPermission="isPermission"
                            @emptyTable="tables = []" :tables="tables"
                        />
                        <!--  /create   -->

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table"
                             id="printCustom">
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->

                            <tableCustom
                                :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                                :tables="tables" :isEdit="false" :isDelete="false"
                                :permissionUpdate="isPermission('update group Club')"
                                :permissionDelete="isPermission('delete group Club')"
                                :isVisible="isVisible" :tableSetting="tableSetting"
                                :enabled3="enabled3" :Tooltip="Tooltip" @logFire="(id) => log(id,url)"
                                @delete="ids => deleteRow(ids,url)" @editRow="id => dbClickRow(id)"
                                @checkRows="(cR) => checkAll = cR" @checkRowTable="id => checkRow(id)"
                                :isInputCheck="false" :isLog="false" :isAction="false"
                            />

                        </div>
                        <!-- end .table-responsive-->
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>

</style>
