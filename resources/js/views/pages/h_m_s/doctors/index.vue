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
        title: "Doctors",
        meta: [{ name: "description", content: "Doctors" }],
    },
    watch: {
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
    mixins: [translation, customTable, crudHelper],
    components: {
        Layout, PageHeader, loader, searchPage,
        actionSetting, tableCustom, Modal
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Doctors", "all Doctor");
        });
    },
    data() {
        return {
            url: '/h_m_s/doctors',
            page_title: {},

            doctor_work_times: [],
            searchMain: '',
            tableSetting: [
                {
                    isFilter: true, isSet: true, trans: "hms_doctors_name_ar", isV: 'name',
                    type: 'string', sort: true, setting: { "name": true }, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "hms_doctors_name_en", isV: 'name_e',
                    type: 'string', sort: true, setting: { "name_e": true }, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "hms_doctors_specialty", isV: 'specialty_id'
                    , type: 'relation', name: 'specialty', sort: false, col1: 'name', col2: 'name_e',
                    setting: { "specialty_id": true }, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "hms_doctors_email", isV: 'email',
                    type: 'string', sort: true, setting: { "email": true }, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "hms_doctors_mobile", isV: 'mobile',
                    type: 'string', sort: true, setting: { "mobile": true }, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "hms_doctors_whatsapp", isV: 'whatsapp',
                    type: 'string', sort: true, setting: { "whatsapp": true }, isSetting: true
                },
                {
                    isFilter: false, isSet: true, trans: "hms_doctors_medical_commission", isV: 'medical_commission',
                    type: 'string', sort: true, setting: { "medical_commission": true }, isSetting: true
                },
                {
                    isFilter: false, isSet: true, trans: "hms_doctors_selling_items_commission", isV: 'selling_items_commission',
                    type: 'string', sort: true, setting: { "selling_items_commission": true }, isSetting: true
                },
                {
                    isFilter: false, isSet: true, trans: "hms_doctors_transfer_patient_commission", isV: 'transfer_patient_commission',
                    type: 'string', sort: true, setting: { "transfer_patient_commission": true }, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "hms_doctors_type", isV: 'type',
                    type: 'string', sort: true, setting: { "type": true }, isSetting: true
                },
                {
                    isFilter: false, isSet: true, trans: "hms_doctors_is_active", isV: 'is_active',forceVisible:true,
                    type: 'boolean', setting: { "is_active": true }, isSetting: true
                }
            ],
            sendSetting: {},
            searchField: [],
        };
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter).map(el => el.isV);
        this.settingFun();
        this.getCustomTableFields('h_m_s_doctors')

        this.page_title = page_title.value
        this.getData(1, this.url, this.filterSearch(this.searchField));
    },
    methods: {

        filterSearch(fields) {
            let index_specialty_id = fields.indexOf("specialty_id")
            if (index_specialty_id > -1) {
                fields.splice(index_specialty_id, 1)
                fields.push("specialty.name")
                fields.push("specialty.name_e")
            }
            let filter = '';
            for (let i = 0; i < fields.length; ++i) {
                filter += `columns[${i}]=${fields[i]}&`;
            }
            return filter;
        },
        settingFun(setting = null) {
            if (setting) this.tableSetting = setting;
            let l = {}, names = [];
            names = this.tableSetting.filter(e => e.isSet).map(el => el.setting);
            this.tableSetting.forEach((e, i) => {
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
                        <searchPage page="hms.Doctors"
                            :title="page_title && page_title.url == $route.fullPath ? ($i18n.locale == 'ar' ? page_title.title : page_title.title_e) : $t('general.Doctors')"
                            :isVisible="isVisible" :filterSetting="tableSetting" :companyKeys="companyKeys"
                            :defaultsKeys="defaultsKeys" @dataSearch="() => getData(1, url, filterSearch(searchField))"
                            @searchFun="(fields) => searchField = fields" @editSearch="(search) => searchMain = search"
                            :isSearch="true" />
                        <!-- end search -->

                        <!-- start setting -->
                        <actionSetting :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :current_page="current_page"
                            :isCreate="true" :isEdit="true" :isDelete="true"
                            :permissionCreate="isPermission('create Doctor')"
                            :permissionUpdate="isPermission('update Doctor')"
                            :permissionDelete="isPermission('delete Doctor')" :isExcl="true" :isPrint="true"
                            :checkAll="checkAll" :sideAction="true" :sidePaginate="true" :isFilter="true" :group="true"
                            :isGroup="true" :isVisible="isVisible" :isSetting="true" :isPaginate="true"
                            :settings="tableSetting" @delete="ids => deleteRow(ids, url)" @gen_xsl="ExportExcel('xlsx')"
                            @settingFun="setting => settingFun(setting)" :objPagination="objPagination"
                            @perviousOrNext="(current) => getData(current, url, filterSearch(searchField))"
                            @currentPage="(page) => current_page = page"
                            @DataCurrentPage="(page) => getDataCurrentPage(page)"
                            @actionChange="({ typeAction, id }) => changeType({ typeAction, id })" />
                        <!-- end setting -->

                        <!--  create   -->
                        <Modal :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :url="url"
                            :isPage="true"
                            :isVisiblePage="isVisible" :isRequiredPage="isRequired" :type="type"
                            :idObjEdit="idEdit ? { idEdit, dataObj: this.tables.find(el => el.id == idEdit) } : null"
                            @getDataTable="getData(1, url, filterSearch(searchField))" :isPermission="isPermission" />

                        <div class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table"
                            id="printCustom">
                            <loader size="large" v-if="isLoader" />

                            <tableCustom :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :isLog="false"
                                :modalButtonIcon="'fa fa-clock'" :modalButton="'doctorWorkTimes'"
                                :modalButtonCondition="{key:'type',value:'indoctor',default_val:'-'}"
                                @doctorWorkTimes="work_times => doctor_work_times = work_times" :modalEmitAttribute="'work_times'"
                                :tables="tables" :isEdit="true" :isDelete="true"
                                :permissionUpdate="isPermission('update Doctor')"
                                :permissionDelete="isPermission('delete Doctor')" :isVisible="isVisible"
                                :tableSetting="tableSetting" :enabled3="enabled3" :Tooltip="Tooltip"
                                @logFire="(id) => log(id, url)" @delete="ids => deleteRow(ids, url)"
                                @editRow="id => dbClickRow(id)" @checkRows="(cR) => checkAll = cR"
                                @checkRowTable="id => checkRow(id)" :isInputCheck="true" :isAction="true" />

                        </div>


                        <b-modal id="doctorWorkTimes" :title="$t('general.Work Times')" title-class="font-18"
                            body-class="p-4 " size="lg" :hide-footer="true">
                            <table class="table table-borderless table-hover table-centered m-0">
                                <loader size="large" v-if="isLoader" />

                                <thead>
                                    <tr class="text-center">
                                        <th>{{ getCompanyKey("hms_doctors_branch") }}</th>
                                        <th>{{ getCompanyKey("hms_doctors_room") }}</th>
                                        <th>{{ getCompanyKey("hms_doctors_day") }}</th>
                                        <th>{{ getCompanyKey("hms_doctors_from") }}</th>
                                        <th>{{ getCompanyKey("hms_doctors_to") }}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="doctor_work_times.length > 0">
                                    <tr v-for="time in doctor_work_times" :key="time.id">
                                        <td>{{ $i18n.locale == 'ar' ? time.branch.name : time.branch.name_e }}</td>
                                        <td>{{ $i18n.locale == 'ar' ? time.room.name : time.room.name_e }}</td>
                                        <td>{{ $i18n.locale == 'ar' ? time.day.name : time.day.name_e }}</td>
                                        <td>
                                            <label data-v-42f9c4a2="" class="badge badge-dark text-white p-2 mx-1 col"
                                                style="border-radius: 2rem; max-width: 140px;">
                                                <i class="fa fa-clock"></i>
                                                {{ time.from }}</label>
                                        </td>
                                        <td>
                                            <label data-v-42f9c4a2="" class="badge badge-dark text-white p-2 mx-1 col"
                                                style="border-radius: 2rem; max-width: 140px;">
                                                <i class="fa fa-clock"></i>
                                                {{ time.to }}</label>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </b-modal>
                        <!-- end .table-responsive-->
                    </div>
                </div>
            </div>
        </div>
</Layout></template>
