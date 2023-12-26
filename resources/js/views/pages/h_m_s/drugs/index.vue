<script>
import Layout from "../../../layouts/main.vue";
import PageHeader from "../../../../components/general/Page-header.vue";
import loader from "../../../../components/general/loader.vue";
import translation from "../../../../helper/mixin/translation-mixin";
import permissionGuard from "../../../../helper/permission";
import customTable from "../../../../helper/mixin/customTable";
import crudHelper from "../../../../helper/mixin/crudHelper";
import searchPage from "../../../../components/general/searchPage";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";
import Modal from "./modal.vue";
import page_title from "../../../../helper/PageTitle"

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Drugs",
        meta: [{ name: "description", content: "Drugs" }],
    },
    watch:{
        pageTitle: {
        handler(newV, old) {
            this.page_title = page_title.value;
        },
        },
    },
    computed: {
        pageTitle: function () {
        return page_title.value;
        },
    },
    mixins: [translation,customTable,crudHelper],
    components: {
        Layout, PageHeader, loader, searchPage,
        actionSetting, tableCustom, Modal
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
          return permissionGuard(vm, "Drugs", "all Drug");
        });
   },
    data() {
        return {
            url: '/h_m_s/drugs',
            page_title: {},
            searchMain: '',
            tableSetting: [
                {
                    isFilter: true,isSet: true,trans:"hms_drugs_trade_name_ar",isV: 'trade_name',
                    type: 'string',sort: true,setting: {"trade_name":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_drugs_trade_name_en",isV: 'trade_name_e',
                    type: 'string',sort: true,setting: {"trade_name_e":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_drugs_generic_name_ar",isV: 'generic_name',
                    type: 'string',sort: true,setting: {"name_e":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_drugs_generic_name_en",isV: 'generic_name_e',
                    type: 'string',sort: true,setting: {"generic_name_e":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_drugs_sell_price",isV: 'sell_price',
                    type: 'string',sort: true,setting: {"sell_price":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_drugs_note",isV: 'note',
                    type: 'string',sort: true,setting: {"note":true},isSetting: true
                }
            ],
            sendSetting: {},
            searchField: [],
        };
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
        this.settingFun();
        this.page_title = page_title.value
        this.getData(1,this.url,this.filterSearch(this.searchField));
        this.getCustomTableFields('h_m_s_drugs');

    },
    methods: {
        filterSearch(fields){
            let filter = '';
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
                            page="hms.Drugs"
                            :title="page_title && page_title.url == $route.fullPath ? ($i18n.locale == 'ar' ? page_title.title :page_title.title_e ):$t('general.Drugs')"
                            :isVisible="isVisible"
                            :filterSetting="tableSetting"
                            :companyKeys="companyKeys"
                            :defaultsKeys="defaultsKeys"
                            @dataSearch="() => getData(1,url,filterSearch(searchField))"
                            @searchFun="(fields) => searchField = fields"
                            @editSearch="(search) => searchMain = search"
                            :isSearch="true"
                        />
                        <!-- end search -->

                        <!-- start setting -->
                        <actionSetting
                            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :current_page="current_page"
                            :isCreate="true" :isEdit="true" :isDelete="true"
                            :permissionCreate="isPermission('create Drug')"
                            :permissionUpdate="isPermission('update Drug')"
                            :permissionDelete="isPermission('delete Drug')" :isExcl="true"
                            :isPrint="true" :checkAll="checkAll" :sideAction="true" :sidePaginate="true"
                            :isFilter="true" :group="true" :isGroup="true" :isVisible="isVisible"
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
                        <Modal
                            :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :url="url"
                            :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired"
                            :type="type" :idObjEdit="idEdit? {idEdit,dataObj: this.tables.find(el => el.id == idEdit)}:null"
                            @getDataTable="getData(1,url,filterSearch(searchField))" :isPermission="isPermission"
                        />

                        <div class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table"
                             id="printCustom">
                            <loader size="large" v-if="isLoader" />

                            <tableCustom
                                :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                                :tables="tables" :isEdit="true" :isDelete="true"
                                :permissionUpdate="isPermission('update Drug')"
                                :permissionDelete="isPermission('delete Drug')"
                                :isVisible="isVisible" :tableSetting="tableSetting"
                                :enabled3="enabled3"  :Tooltip="Tooltip" @logFire="(id) => log(id,url)"
                                @delete="ids => deleteRow(ids,url)" @editRow="id => dbClickRow(id)"
                                @checkRows="(cR) => checkAll = cR" @checkRowTable="id => checkRow(id)"
                                :isInputCheck="true" :isLog="false" :isAction="true"
                            />

                        </div>
                        <!-- end .table-responsive-->
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
