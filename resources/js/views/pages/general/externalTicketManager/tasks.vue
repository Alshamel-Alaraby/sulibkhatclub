<script>
import permissionGuard from "../../../../helper/permission";
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import loader from "../../../../components/general/loader";
import translation from "../../../../helper/mixin/translation-mixin";
import Task from "../../../../components/create/general/external_task";
import TrustTask from "../../../../components/create/general/trust_external_task";
import searchPage from "../../../../components/general/searchPage";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";
import customTable from "../../../../helper/mixin/customTable";
import successError from "../../../../helper/mixin/success&error";
import crudHelper from "../../../../helper/mixin/crudHelper";
import adminApi from "../../../../api/adminAxios";
import Multiselect from "vue-multiselect";

export default {
    page: {
        title: "External Tasks",
        meta: [{ name: "description", content: 'External Tasks' }],
    },
    mixins: [translation,customTable,successError,crudHelper],
    components: {
        Layout, PageHeader, loader, Task,Multiselect,TrustTask,
        searchPage,actionSetting, tableCustom
    },
    beforeRouteEnter(to, from, next) {
         next((vm) => {
              return permissionGuard(vm, "External Tasks", "all External Task");
         });
    },
    props:['notification_data','id'],

    watch:{
        $route(to, from) {
            // react to route changes...
            if(Object.keys(this.notification_data ?? []).length){
                let index;
                this.tables.forEach((ele,i) => {
                    if(ele.id == this.notification_data.id){
                        index = i
                    }
                })
                if(index){
                    this.tables[index] = this.notification_data
                }else{
                    this.tables.push(this.notification_data)
                }

                this.dbClickRow(this.notification_data.id)
                this.$router.push('/dashboard/ticket-manager/external_tasks')
            }
           },

        deep: true,
        immediate: true
    },
    data() {
        return {
            url: '/tasks',
            status_id: 1,
            owner: 'trust',
            searchMain: '',
            tableSetting: [
                {
                    isFilter: true,isSet: true,trans:"task_number",isV: 'id',forceVisible:true,
                    type: 'string',sort: true,setting: {"id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"general_task_signature",isV: 'signature',forceVisible:true,
                    type: 'string',sort: true,setting: {"signature":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"task_type",isV: 'type',trans_value:true,
                    type: 'string',sort: true,setting: {"type":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"employee", isV: 'employee_id',
                    type: 'relation',name: 'employee',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"employee_id":true},isSetting: true
                },
                {
                    //boardRent_task_department
                    isFilter: true,isSet: true,trans: "boardRent_work_type", isV: 'department_id'
                    ,type: 'relation', name: 'department',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"department_id":true},isSetting: true
                },
                // {
                //     isFilter: true,isSet: true,trans:"customer",isV: 'customer_id',
                //     type: 'relation', name:'customer',sort: false,col1: 'name',col2: 'name_e',
                //     setting: {"customer_id":true},isSetting: true
                // },
                // {
                //     isFilter: true,isSet: true,trans:"city",isV: 'department_task_id',
                //     type: 'relation', name:'department_task',sort: false,col1: 'name',col2: 'name_e',
                //     setting: {"department_task_id":true},isSetting: true
                // },
                {
                    isFilter: true,isSet: true,trans:"task_status",isV: 'status_id',
                    type: 'relation', name:'status',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"status_id":true},isSetting: true
                },
                // {
                //     isFilter: true,isSet: true,trans:"boardRent_task_equipment",isV: 'equipment_id',
                //     type: 'relation', name:'equipment',sort: false,col1: 'name',col2: 'name_e',
                //     setting: {"equipment_id":true},isSetting: true
                // },
                {
                    isFilter: true,isSet: true,trans:"boardRent_task_location",isV: 'location_id',
                    type: 'relation', name:'location',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"location_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_trust_task_sub_location",isV: 'sub_location_id',
                    type: 'relation', name:'sub_location',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"sub_location_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"task_priority",isV: 'priority_id',
                    type: 'relation', name:'priority',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"priority_id":true},isSetting: true
                },
                // {
                //     isFilter: false,isSet: true,trans:"task_owners",isV: 'owners',
                //     type: 'relation', name:'owners',sort: false,col1: 'name',col2: 'name_e',
                //     setting: {"owners":true},isSetting: true
                // },
                // {
                //     isFilter: true,isSet: true,trans:"task_supervisors",isV: 'supervisors',
                //     type: 'relation', name:'supervisors',sort: false,col1: 'name',col2: 'name_e',
                //     setting: {"supervisors":true},isSetting: true
                // },
                // {
                //     isFilter: true,isSet: true,trans:"task_notifications",isV: 'notifications',
                //     type: 'relation', name:'notifications',sort: false,col1: 'name',col2: 'name_e',
                //     setting: {"notifications":true},isSetting: true
                // },
                {
                    //general_customer_contact_person
                    isFilter: true,isSet: true,trans:'general_task_informer_name',isV: 'contact_person',
                    type: 'string',sort: true,setting: {"contact_person":true},isSetting: true
                },
                {
                    //general_customer_contact_phones
                    isFilter: true,isSet: true,trans:'company_phone',isV: 'contact_phone',
                    type: 'string',sort: true,setting: {"contact_phone":true},isSetting: true
                },
                // {
                //     //task_title
                //     isFilter: true,isSet: true,trans:'trust_task_title',isV: 'task_title',
                //     type: 'string',sort: true,setting: {"task_title":true},isSetting: true
                // },
                {
                    isFilter: true,isSet: true,trans:"execution_date",isV: 'execution_date',
                    type: 'string',sort: true,setting: {"execution_date":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"task_start_time",isV: 'start_time',
                    type: 'string',sort: true,setting: {"start_time":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"execution_end_date",isV: 'execution_end_date',
                    type: 'string',sort: true,setting: {"execution_end_date":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"task_end_time",isV: 'end_time',
                    type: 'string',sort: true,setting: {"end_time":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"execution_duration",isV: 'execution_duration',
                    type: 'string',sort: true,setting: {"execution_duration":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"notification_date",isV: 'notification_date',
                    type: 'string',sort: true,setting: {"notification_date":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"boardRent_task_is_closed",isV: 'is_closed',
                    type: 'boolean1',setting: {"is_closed":true},isSetting: true
                }
            ],
            sendSetting: {},
            searchField: [],
            statuses: [],
        }
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
        this.settingFun();
        this.getStatus();
        this.$store.dispatch('locationIp/getIp');
        this.getCustomTableFields('general_tasks');
        this.getData(1,this.url,this.filterSearch(this.searchField));

    },
    methods: {
        getStatus(){
            this.isLoader = true;
             adminApi
                .get(`/statuses/get-drop-down?module_type=bordRent`)
                .then((res) => {
                    this.statuses = res.data.data;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                    setTimeout(() => {
                    if(Object.keys(this.notification_data ?? []).length){
                        if(!this.tables.find(el => el.id == this.notification_data.id))
                            this.tables.push(this.notification_data)

                            this.dbClickRow(this.notification_data.id)
                            this.$router.push('/dashboard/ticket-manager/external_tasks')

                    }
                    },1500)
                });
        },
        filterSearch(fields){
            let indexemployee = fields.indexOf("employee_id"),
                indexG = fields.indexOf("department_id"),
                indexCty = fields.indexOf("customer_id"),
                indexdepartmentTask = fields.indexOf("department_task_id"),
                indexstatus = fields.indexOf("status_id"),
                indeSupervisors = fields.indexOf("supervisors"),
                indexAttentions = fields.indexOf("notifications"),
                filter = '';
            if (indexAttentions > -1) {
                fields[indexAttentions] = this.$i18n.locale == "ar" ? "attentions.name" : "attentions.name_e";
            }
            if (indeSupervisors > -1) {
                fields[indeSupervisors] = this.$i18n.locale == "ar" ? "supervisors.name" : "supervisors.name_e";
            }
            if (indexemployee > -1) {
                fields[indexemployee] = this.$i18n.locale == "ar" ? "employee.name" : "employee.name_e";
            }
            if (indexG > -1) {
                fields[indexG] = this.$i18n.locale == "ar" ? "department.name" : "department.name_e";
            }
            if (indexCty > -1) {
                fields[indexCty] = this.$i18n.locale == "ar" ? "customer.name" : "customer.name_e";
            }
            if (indexdepartmentTask > -1) {
                fields[indexdepartmentTask] = this.$i18n.locale == "ar" ? "departmentTask.name" : "departmentTask.name_e";
            }
            if (indexstatus > -1) {
                fields[indexstatus] = this.$i18n.locale == "ar" ? "departmentTask.name" : "departmentTask.name_e";
            }
            for (let i = 0; i < fields.length; ++i) {
                filter += `columns[${i}]=${fields[i]}&`;
            }
            return filter+'status_id='+this.status_id??''+'&external_task=1';
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
                            page="general.tasksTable"
                            :isVisible="isVisible"
                            :filterSetting="tableSetting"
                            :companyKeys="companyKeys"
                            :defaultsKeys="defaultsKeys"
                            @dataSearch="() => getData(1,url,filterSearch(searchField))"
                            @searchFun="(fields) => searchField = fields"
                            @editSearch="(search) => searchMain = search"
                            :isSearch="true"
                        />

                        <actionSetting
                            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :current_page="current_page"
                            :isCreate="true" :isEdit="false" :isDelete="false"
                            :permissionCreate="isPermission('create External Task')"
                            :permissionUpdate="isPermission('update External Task')"
                            :permissionDelete="isPermission('delete External Task')" :isExcl="true"
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
                        >
                        <div class="d-flex col-5">
                            <label>
                                {{ getCompanyKey('task_status') }}
                            </label>
                            <multiselect
                                v-model="status_id"
                                :options="statuses.map((type) => type.id)"
                                @input="getData(1,url,filterSearch(searchField))"
                                :custom-label=" (opt) => statuses.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar' ? statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e
                                    : null
                                "
                            >
                            </multiselect>

                        </div>
                        </actionSetting>
                        <!-- end setting -->

                        <!--  create   -->
                        <TrustTask v-if="owner == 'trust'"
                            :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                            :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired" :url="url"
                            :type="type" :idObjEdit="idEdit? {idEdit,dataObj: this.tables.find(el => el.id == idEdit)}:null"
                            @getDataTable="getData(1,url,filterSearch(searchField))" :isPermission="isPermission"
                        />
                        <Task v-else
                            :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                            :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired" :url="url"
                            :type="type" :idObjEdit="idEdit? {idEdit,dataObj: this.tables.find(el => el.id == idEdit)}:null"
                            @getDataTable="getData(1,url,filterSearch(searchField))" :isPermission="isPermission"
                        />
                        <!--  /create   -->



                        <div
                            class="table-responsive mb-3 custom-table-theme position-relative"
                            ref="exportable_table"
                            id="printCustom"
                        >

                            <loader size="large" v-if="isLoader" />

                            <tableCustom
                                :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                                :tables="tables" :isEdit="true" :isDelete="false"
                                :permissionUpdate="isPermission('update External Task')"
                                :permissionDelete="isPermission('delete External Task')"
                                :isVisible="isVisible" :tableSetting="tableSetting"
                                :enabled3="enabled3" :Tooltip="Tooltip" @logFire="(id) => getLog(data.id,url)"
                                @delete="ids => deleteRow(ids,url)" @editRow="id => dbClickRow(id)"
                                @checkRows="(cR) => checkAll = cR" @checkRowTable="id => checkRow(id)"
                                :isInputCheck="false" :isLog="false" :isAction="false"
                                :isLogClick="false"
                            />

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
