<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import loader from "../../../../components/general/loader";
import translation from "../../../../helper/mixin/translation-mixin";
import searchPage from "../../../../components/general/searchPage";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";
import customTable from "../../../../helper/mixin/customTable";
import successError from "../../../../helper/mixin/success&error";
import crudHelper from "../../../../helper/mixin/crudHelper";
import adminApi from "../../../../api/adminAxios";
import Multiselect from "vue-multiselect";
import ErrorMessage from "../../../../components/widgets/errorMessage.vue";
import DatePicker from "vue2-datepicker";
import permissionGuard from "../../../../helper/permission";

export default {
    page: {
        title: "Tasks Tasks",
        meta: [{name: "description", content: 'External Tasks'}],
    },
    mixins: [translation, customTable, successError, crudHelper],
    components: {
        ErrorMessage,
        Layout, PageHeader, loader, Multiselect,
        searchPage, actionSetting, tableCustom, DatePicker,
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Tasks Report", "all Tasks Report");
        });
    },
    props: ['notification_data', 'id'],

    watch: {
        $route(to, from) {
            // react to route changes...
            if (Object.keys(this.notification_data ?? []).length) {
                let index;
                this.tables.forEach((ele, i) => {
                    if (ele.id == this.notification_data.id) {
                        index = i
                    }
                })
                if (index) {
                    this.tables[index] = this.notification_data
                } else {
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
            searchMain: '',
            locations: [],
            departments: [],
            errors: {},
            create: {
                status_id: '',
                start_date: '',
                end_date: '',
                department_id: '',
                location_id: '',
            },
            tableSetting: [
                {
                    isFilter: true, isSet: true, trans: "task_number", isV: 'id', forceVisible: true,
                    type: 'string', sort: true, setting: {"id": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "task_type", isV: 'type',
                    type: 'string', sort: true, setting: {"type": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "employee", isV: 'employee_id',
                    type: 'relation', name: 'employee', sort: false, col1: 'name', col2: 'name_e',
                    setting: {"employee_id": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "boardRent_task_department", isV: 'department_id'
                    , type: 'relation', name: 'department', sort: false, col1: 'name', col2: 'name_e',
                    setting: {"department_id": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "customer", isV: 'customer_id',
                    type: 'relation', name: 'customer', sort: false, col1: 'name', col2: 'name_e',
                    setting: {"customer_id": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "city", isV: 'department_task_id',
                    type: 'relation', name: 'department_task', sort: false, col1: 'name', col2: 'name_e',
                    setting: {"department_task_id": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "task_status", isV: 'status_id',
                    type: 'relation', name: 'status', sort: false, col1: 'name', col2: 'name_e',
                    setting: {"status_id": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "boardRent_task_equipment", isV: 'equipment_id',
                    type: 'relation', name: 'equipment', sort: false, col1: 'name', col2: 'name_e',
                    setting: {"equipment_id": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "boardRent_task_location", isV: 'location_id',
                    type: 'relation', name: 'location', sort: false, col1: 'name', col2: 'name_e',
                    setting: {"location_id": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "task_priority", isV: 'priority_id',
                    type: 'relation', name: 'priority', sort: false, col1: 'name', col2: 'name_e',
                    setting: {"priority_id": true}, isSetting: true
                },
                {
                    isFilter: false, isSet: true, trans: "task_owners", isV: 'owners',
                    type: 'relation', name: 'owners', sort: false, col1: 'name', col2: 'name_e',
                    setting: {"owners": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "task_supervisors", isV: 'supervisors',
                    type: 'relation', name: 'supervisors', sort: false, col1: 'name', col2: 'name_e',
                    setting: {"supervisors": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "task_notifications", isV: 'notifications',
                    type: 'relation', name: 'notifications', sort: false, col1: 'name', col2: 'name_e',
                    setting: {"notifications": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "general_customer_contact_person", isV: 'contact_person',
                    type: 'string', sort: true, setting: {"contact_person": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "general_customer_contact_phones", isV: 'contact_phone',
                    type: 'string', sort: true, setting: {"contact_phone": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "task_title", isV: 'task_title',
                    type: 'string', sort: true, setting: {"task_title": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "execution_date", isV: 'execution_date',
                    type: 'string', sort: true, setting: {"execution_date": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "task_start_time", isV: 'start_time',
                    type: 'string', sort: true, setting: {"start_time": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "execution_end_date", isV: 'execution_end_date',
                    type: 'string', sort: true, setting: {"execution_end_date": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "task_end_time", isV: 'end_time',
                    type: 'string', sort: true, setting: {"end_time": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "execution_duration", isV: 'execution_duration',
                    type: 'string', sort: true, setting: {"execution_duration": true}, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "notification_date", isV: 'notification_date',
                    type: 'string', sort: true, setting: {"notification_date": true}, isSetting: true
                },

                {
                    isFilter: false, isSet: true, trans: "boardRent_task_is_closed", isV: 'is_closed',
                    type: 'boolean', setting: {"is_closed": true}, isSetting: true
                }
            ],
            is_disabled: false,
            sendSetting: {},
            searchField: [],
            statuses: [],
        }
    },
    validations: {
        create: {
            start_date: {},
            end_date: {},
            status_id: {},
            department_id: {},
            location_id: {}
        },
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter).map(el => el.isV);
        this.settingFun();
        this.getStatus();
        this.getLocation();
        this.getDepartment();
        this.$store.dispatch('locationIp/getIp');
        this.getCustomTableFields('general_tasks');
        this.getData(1, this.url, this.filterSearch(this.searchField));

    },
    methods: {
        resetModalHidden() {
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
            this.$bvModal.hide(`create`);
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
        },
        getStatus() {
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
                        if (Object.keys(this.notification_data ?? []).length) {
                            if (!this.tables.find(el => el.id == this.notification_data.id))
                                this.tables.push(this.notification_data)

                            this.dbClickRow(this.notification_data.id)
                            this.$router.push('/dashboard/ticket-manager/external_tasks')

                        }
                    }, 1500)
                });
        },
        getDepartment() {
            this.isLoader = true;
            adminApi
                .get(`/depertments`)
                .then((res) => {
                    this.departments = res.data.data;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getLocation() {
            this.isLoader = true;
            adminApi
                .get(`/locations/get-drop-down?not_parent=1`)
                .then((res) => {
                    this.locations = res.data.data;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        filterSearch(fields) {
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
            return filter + 'status_id=' + this.create.status_id + '&location_id=' + this.create.location_id + '&department_id=' + this.create.department_id + '&start_date=' + this.create.start_date??'' + '&end_date=' + this.create.end_date ?? '';
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
    }
};
</script>

<template>
    <Layout>
        <PageHeader/>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <b-modal
                            id="create"
                            :title="$t('general.Search')"
                            title-class="font-18"
                            body-class="p-4"
                            size="lg"
                            :hide-footer="true"
                            @show="resetModal"
                            @hidden="resetModalHidden"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <template v-if="!is_disabled">
                                        <b-button
                                            variant="success"
                                            type="button" class="mx-1"
                                            v-if="!isLoader"
                                            @click.prevent="getData(1,url,filterSearch(searchField))"
                                        >
                                            {{ $t('general.Search') }}
                                        </b-button>

                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                        </b-button>
                                    </template>
                                    <!-- Emulate built in modal footer ok and cancel button actions -->

                                    <b-button variant="danger" type="button" @click.prevent="resetModalHidden">
                                        {{ $t('general.Cancel') }}
                                    </b-button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.fromDate') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="$v.create.start_date.$model"
                                                format="YYYY-MM-DD"
                                                valueType="format"
                                                :confirm="false"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.start_date.$error ||
                                                        errors.start_date,
                                                    'is-valid':
                                                        !$v.create.start_date
                                                            .$invalid &&
                                                        !errors.start_date,
                                                    }"
                                            ></date-picker>

                                            <template v-if="errors.start_date">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.start_date"
                                                              :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.toDate') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="$v.create.end_date.$model"
                                                format="YYYY-MM-DD"
                                                valueType="format"
                                                :confirm="false"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.end_date.$error ||
                                                        errors.end_date,
                                                    'is-valid':
                                                        !$v.create.end_date
                                                            .$invalid &&
                                                        !errors.end_date,
                                                    }"
                                            ></date-picker>

                                            <template v-if="errors.end_date">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.end_date"
                                                              :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label>
                                                {{ getCompanyKey('task_status') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                v-model="create.status_id"
                                                :options="statuses.map((type) => type.id)"
                                                :custom-label=" (opt) => statuses.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                                :class="{'is-invalid': $v.create.status_id.$error || errors.status_id,'is-valid': !$v.create.status_id.$invalid && !errors.status_id,}"
                                            >
                                            </multiselect>
                                            <div v-if="!$v.create.status_id.required" class="invalid-feedback">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>

                                            <template v-if="errors.status_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.status_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_task_department") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                v-model="$v.create.department_id.$model"
                                                :options="departments.map((type) => type.id)"
                                                :custom-label=" (opt) => departments.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? departments.find((x) => x.id == opt).name : departments.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                                :class="{'is-invalid': $v.create.department_id.$error || errors.department_id,'is-valid': !$v.create.department_id.$invalid && !errors.department_id,}"
                                            >
                                            </multiselect>
                                            <div v-if="$v.create.department_id.$error || errors.department_id"
                                                 class="text-danger">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.department_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.department_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label>
                                                {{ getCompanyKey("boardRent_task_location") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                v-model="$v.create.location_id.$model"
                                                :options="locations.map((type) => type.id)"
                                                :custom-label=" (opt) => locations.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? locations.find((x) => x.id == opt).name : locations.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                                :class="{'is-invalid': $v.create.location_id.$error || errors.location_id,'is-valid': !$v.create.location_id.$invalid && !errors.location_id,}"
                                            >
                                            </multiselect>

                                            <template v-if="errors.location_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.location_id"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
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
                            :isCreate="false" :isEdit="false" :isDelete="false"
                            :isExcl="true" :checkAll="[]"
                            :isPrint="true" :sideAction="true" :sidePaginate="true"
                            :isFilter="false" :group="true" :isGroup="true" :isVisible="isVisible"
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
                            <b-button
                                v-b-modal.create
                                class="mx-1 custom-btn-background"
                            >
                                {{ $t('general.filter') }}
                                <i class="fas fa-filter"></i>
                            </b-button>
                        </actionSetting>
                        <!-- end setting -->


                        <div
                            class="table-responsive mb-3 custom-table-theme position-relative"
                            ref="exportable_table"
                            id="printCustom"
                        >

                            <loader size="large" v-if="isLoader"/>

                            <tableCustom
                                :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                                :tables="tables" :isEdit="false" :isDelete="false"
                                :isVisible="isVisible" :tableSetting="tableSetting"
                                :enabled3="enabled3" @logFire="(id) => getLog(data.id,url)"
                                @delete="ids => deleteRow(ids,url)" @editRow="id => dbClickRow(id)"
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
