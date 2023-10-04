<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import loader from "../../../components/general/loader";
import permissionGuard from "../../../helper/permission";
import TimeTableEmployee from "../../../components/create/hr/time-table-employee";
import searchPage from "../../../components/general/searchPage";
import actionSetting from "../../../components/general/actionSetting";
import tableCustom from "../../../components/general/tableCustom";
import translation from "../../../helper/mixin/translation-mixin";
import customTable from "../../../helper/mixin/customTable";
import successError from "../../../helper/mixin/success&error";
import crudHelper from "../../../helper/mixin/crudHelper";

/**
 * Advanced Table component
 */
export default {
    page: {title: "Attendance And Departure", meta: [{ name: "description", content: "Attendance And Departure" }],},
    mixins: [translation,customTable,successError,crudHelper],
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Attendance And Departure", "all Attendance And Departure Hr");
        });
    },
    components: {
        Layout, PageHeader, loader,
        searchPage,actionSetting, tableCustom,
        TimeTableEmployee
    },
    data() {
        return {
            url: '/hr/employees-time-tables-header',
            searchMain: '',
            tableSetting: [
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_employee_id",isV: 'id',
                    type: 'string',sort: true,setting: {"id":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_employee_name",isV: 'id',
                    type: 'string',sort: true,setting: {"id":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_day",isV: 'day',
                    type: 'string',sort: true,setting: {"day":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_attendance",isV: 'attendance',
                    type: 'string',sort: true,setting: {"attendance":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_departure",isV: 'departure',
                    type: 'string',sort: true,setting: {"departure":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"time_employee_start_from",isV: 'id',
                    type: 'string',sort: true,setting: {"id":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_attendance_shift2",isV: 'departure',
                    type: 'string',sort: true,setting: {"departure":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_departure_shift2",isV: 'id',
                    type: 'string',sort: true,setting: {"id":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_delay",isV: 'id',
                    type: 'string',sort: true,setting: {"id":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_additional",isV: 'id',
                    type: 'string',sort: true,setting: {"id":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_Actualnumberofhours",isV: 'id',
                    type: 'string',sort: true,setting: {"id":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_absence",isV: 'id',
                    type: 'string',sort: true,setting: {"id":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_note",isV: 'id',
                    type: 'string',sort: true,setting: {"id":true},isSetting: true
                },
                // {
                //     isFilter: false,isSet: true,trans:"time_employee_timetables_header",isV: 'timetables_header_id',
                //     type: 'relation',setting: {"timetables_header_id":true},isSetting: true,name: 'timetablesHeader',
                //     col1: 'name',col2: 'name_e'
                // }
            ],
            sendSetting: {},
            searchField: []
        };
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
        this.settingFun();
        this.getCustomTableFields('hr_employees_timetables_header');
        this.getData(1,this.url,this.filterSearch(this.searchField));
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
    }
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
                            page="general.timeEmployeeTable"
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
                            :isCreate="true" :isEdit="true" :isDelete="false"
                            :permissionCreate="isPermission('create attendance Hr')"
                            :permissionUpdate="isPermission('update attendance Hr')"
                            :permissionDelete="isPermission('delete attendance Hr')" :isExcl="true"
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

                        <!--  create or edit   -->
                        <TimeTableEmployee
                            :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                            :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired" :url="url"
                            :type="type" :idObjEdit="idEdit? {idEdit,dataObj: this.tables.find(el => el.id == idEdit)}:null"
                            @getDataTable="getData(1,url,filterSearch(searchField))" :isPermission="isPermission"
                        />
                        <!--  /create   -->

                        <!-- start .table-responsive-->
                        <div
                            class="table-responsive mb-3 custom-table-theme position-relative"
                            ref="exportable_table" id="printCustom"
                        >
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->

                            <tableCustom
                                :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                                :tables="tables" :isEdit="true" :isDelete="true"
                                :permissionUpdate="isPermission('update attendance Hr')"
                                :permissionDelete="isPermission('delete attendance Hr')"
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
