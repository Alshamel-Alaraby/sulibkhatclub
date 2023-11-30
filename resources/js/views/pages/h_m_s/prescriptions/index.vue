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
import adminApi from '../../../../api/adminAxios';

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Prescriptions",
        meta: [{ name: "description", content: "Prescriptions" }],
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
          return permissionGuard(vm, "Prescriptions", "Prescriptions");
        });
   },
    data() {
        return {
            url: '/h_m_s/prescriptions',
            page_title: {},
            days: [],
            branches: [],
            doctors: [],
            patients: [],
            diagnosis_tests: [],
            drugs: [],
            searchMain: '',
            tableSetting: [

                {
                    isFilter: true,isSet: true,trans:"hms_prescriptions_doctor", isV: 'doctor_id'
                    ,type: 'relation', name: 'doctor',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"doctor_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_prescriptions_patient", isV: 'patient_id'
                    ,type: 'relation', name: 'patient',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"patient_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"hms_prescriptions_date",isV: 'date',
                    type: 'date',sort: true,setting: {"date":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"hms_prescriptions_content",isV: 'content',
                    type: 'badge',prop_type:'array',badge_color:'badge-primary',sort: false,setting: {"content":true},isSetting: true
                },

            ],
            sendSetting: {},
            searchField: [],
        };
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
        this.settingFun();
        this.getData(1,this.url,this.filterSearch(this.searchField));
        this.get_doctors();
        this.get_patients();
        this.get_drugs();
        this.get_diagnosis_tests();
        this.page_title = page_title.value
        this.getCustomTableFields('h_m_s_prescriptions')

    },
    methods: {
        get_doctors(){
            adminApi.get(`h_m_s/doctors?drop_down=1&company_id=${this.company_id}`).then((res) => {
            this.doctors = res.data
            });
        },
        get_patients(){
            adminApi.get(`h_m_s/patients?drop_down=1`).then((res) => {
            this.patients = res.data
            });
        },
        get_drugs() {
            adminApi.get(`/h_m_s/drugs?drop_down=1`).then((res) => {
                this.drugs = res.data;
            })
            .catch((err) => {
                this.errorFun('Error','Thereisanerrorinthesystem');
            })
        },
        get_diagnosis_tests() {
            adminApi.get(`/h_m_s/diagnosis_tests?drop_down=1`).then((res) => {
                this.diagnosis_tests = res.data;
            })
            .catch((err) => {
                this.errorFun('Error','Thereisanerrorinthesystem');
            })
        },

        filterSearch(fields){
            let index_patient_id = fields.indexOf("patient_id")
            if (index_patient_id > -1) {
                fields.splice(index_patient_id,1)
                fields.push("patient.name")
                fields.push("patient.name_e")
            }
            let index_doctor_id = fields.indexOf("doctor_id")
            if (index_doctor_id > -1) {
                fields.splice(index_doctor_id,1)
                fields.push("doctor.name")
                fields.push("doctor.name_e")
            }
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
                            page="hms.Prescriptions"
                            :title="page_title && page_title.url == $route.fullPath ? ($i18n.locale == 'ar' ? page_title.title :page_title.title_e ): $t('general.Prescriptions')"
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
                            :permissionCreate="isPermission('create Prescription')"
                            :permissionUpdate="isPermission('update Prescription')"
                            :permissionDelete="isPermission('delete Prescription')" :isExcl="true"
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



                        <div class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table"
                             id="printCustom">
                            <loader size="large" v-if="isLoader" />

                            <tableCustom
                                :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :isLog="false"
                                :tables="tables" :isEdit="true" :isDelete="true"
                                :permissionUpdate="isPermission('update Prescription')"
                                :permissionDelete="isPermission('delete Prescription')"
                                :isVisible="isVisible" :tableSetting="tableSetting"
                                :enabled3="enabled3" :Tooltip="Tooltip" @logFire="(id) => log(id,url)"
                                @delete="ids => deleteRow(ids,url)" @editRow="id => dbClickRow(id)"
                                @checkRows="(cR) => checkAll = cR" @checkRowTable="id => checkRow(id)"
                                :isInputCheck="true" :isAction="true"
                            />

                        </div>
                        <!-- end .table-responsive-->


                          <!--  create   -->
                          <Modal
                            :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :url="url" :doctors="doctors"
                            :patients="patients" :drugs="drugs" :diagnosis_tests="diagnosis_tests"
                            :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired"
                            :selected_appointment="{}"
                            :type="type" :idObjEdit="idEdit? {idEdit,dataObj: this.tables.find(el => el.id == idEdit)}:null"
                            @getDataTable="getData(1,url,filterSearch(searchField))" :isPermission="isPermission"
                        />

                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
