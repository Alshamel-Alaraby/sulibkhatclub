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
import patientdetailsmodal from "./patient_details.vue";
import page_title from "../../../../helper/PageTitle"

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Patients",
        meta: [{ name: "description", content: "Patients" }],
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
        Layout, PageHeader, loader, searchPage,patientdetailsmodal,
        actionSetting, tableCustom, Modal
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
          return permissionGuard(vm, "Patients", "Patients");
        });
   },
    data() {
        return {
            url: '/h_m_s/patients',
            page_title: {},
            patient_id: null,
            searchMain: '',
            tableSetting: [
                {
                    isFilter: true,isSet: true,trans:"hms_patients_name_ar",isV: 'name',
                    type: 'string',sort: true,setting: {"name":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_patients_name_en",isV: 'name_e',
                    type: 'string',sort: true,setting: {"name_e":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_patients_safe_number",isV: 'safe_number',
                    type: 'string',sort: true,setting: {"safe_number":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_patients_email",isV: 'email',
                    type: 'string',sort: true,setting: {"email":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_patients_mobile",isV: 'mobile',
                    type: 'string',sort: true,setting: {"mobile":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_patients_whatsapp",isV: 'whatsapp',
                    type: 'string',sort: true,setting: {"whatsapp":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_patients_patient_height",isV: 'patient_height',
                    type: 'string',sort: true,setting: {"patient_height":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_patients_patient_weight",isV: 'patient_weight',
                    type: 'string',sort: true,setting: {"patient_weight":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_patients_gender",isV: 'gender',
                    type: 'string',sort: true,setting: {"gender":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_patients_blood",isV: 'blood',
                    type: 'string',sort: true,setting: {"blood":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_patients_address",isV: 'address',
                    type: 'string',sort: true,setting: {"address":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_patients_date_of_birth",isV: 'date_of_birth',
                    type: 'string',sort: true,setting: {"date_of_birth":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"hms_patients_is_active",isV: 'is_active',
                    type: 'boolean',setting: {"is_active":true},isSetting: true
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
        this.getCustomTableFields('h_m_s_patients');
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
                            page="hms.Patients"
                            :title="page_title && page_title.url == $route.fullPath ? ($i18n.locale == 'ar' ? page_title.title :page_title.title_e ):$t('general.Patients')"
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
                            :permissionCreate="isPermission('create Patient')"
                            :permissionUpdate="isPermission('update Patient')"
                            :permissionDelete="isPermission('delete Patient')" :isExcl="true"
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

                        <patientdetailsmodal :patient_id="patient_id" />

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
                                :tables="tables" :isEdit="true" :isDelete="true" :modalButtonIcon="'fa fa-eye'" :modalButton="'patientDetails'"
                                :permissionUpdate="isPermission('update Patient')"
                                :permissionDelete="isPermission('delete Patient')"
                                :isVisible="isVisible" :tableSetting="tableSetting"
                                :enabled3="enabled3" :Tooltip="Tooltip" @logFire="(id) => log(id,url)"
                                @patientDetails="id=> patient_id = id"
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
