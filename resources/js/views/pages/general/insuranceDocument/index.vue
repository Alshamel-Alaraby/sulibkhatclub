<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import loader from "../../../../components/general/loader";
import translation from "../../../../helper/mixin/translation-mixin";
import customTable from "../../../../helper/mixin/customTable";
import crudHelper from "../../../../helper/mixin/crudHelper";
import InsuranceDoc from "../../../../components/create/general/insuranceDoc";
import searchPage from "../../../../components/general/searchPage";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";
import permissionGuard from "../../../../helper/permission";
/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Insurance Documents",
        meta: [{ name: "description", content: "Insurance Documents" }],
    },
    mixins: [translation,customTable,crudHelper],
    components: {
        Layout, PageHeader, loader, searchPage,
        actionSetting, tableCustom, InsuranceDoc
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Insurance Documents", "all Insurance Documents");
        });
    },
    data() {
        return {
            url: '/insurance-documents',
            searchMain: '',
            tableSetting: [
                {
                    isFilter: true,isSet: true,trans:"car_insurance_document_insurance_type", isV: 'insurance_type_id',
                    type: 'relation',name: 'insurance_type',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"insurance_type_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_insurance_document_insurance_company", isV: 'insurance_company_id',
                    type: 'relation',name: 'insurance_company',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"insurance_company_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_insurance_document_related",isV: 'related_field',
                    type: 'string',sort: true,setting: {"related_field":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_insurance_document_insurance_number",isV: 'insurance_number',
                    type: 'string',sort: true,setting: {"insurance_number":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_insurance_document_amount",isV: 'amount',
                    type: 'string',sort: true,setting: {"amount":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_insurance_document_from_date",isV: 'from_date',
                    type: 'string',sort: true,setting: {"from_date":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"car_insurance_document_to_date",isV: 'to_date',
                    type: 'string',sort: true,setting: {"to_date":true},isSetting: true
                },
            ],
            sendSetting: {},
            searchField: [],
        };
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
        this.settingFun();
        this.getCustomTableFields('general_insurance_documents');
        this.getData(1,this.url,this.filterSearch(this.searchField));
    },
    methods: {
        filterSearch(fields){
            let indexC = fields.indexOf("insurance_type_id"),
                indexG = fields.indexOf("insurance_company_id"),
                filter = '';

            if (indexC > -1) {
                fields[indexC] = this.$i18n.locale == "ar" ? "insuranceType.name" : "insuranceType.name_e";
            }
            if (indexG > -1) {
                fields[indexG] = this.$i18n.locale == "ar" ? "insuranceCompany.name" : "insuranceCompany.name_e";
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
                            page="general.insuranceDocumentsTable"
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
                            :permissionCreate="isPermission('create group Club')"
                            :permissionUpdate="isPermission('update group Club')"
                            :permissionDelete="isPermission('delete group Club')" :isExcl="true"
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
                        <InsuranceDoc
                            :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                            :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired" :url="url"
                            :type="type" :idObjEdit="idEdit? {idEdit,dataObj: this.tables.find(el => el.id == idEdit)}:null"
                            @getDataTable="getData(1,url,filterSearch(searchField))" :isPermission="isPermission"
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
                                :tables="tables" :isEdit="true" :isDelete="true"
                                :permissionUpdate="isPermission('update group Club')"
                                :permissionDelete="isPermission('delete group Club')"
                                :isVisible="isVisible" :tableSetting="tableSetting"
                                :enabled3="enabled3" :Tooltip="Tooltip" @logFire="(id) => log(id,url)"
                                @delete="ids => deleteRow(ids,url)" @editRow="id => dbClickRow(id)"
                                @checkRows="(cR) => checkAll = cR" @checkRowTable="id => checkRow(id)"
                                :isInputCheck="true" :isLog="true" :isAction="true"
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
