<template>
    <Layout>
        <PageHeader/>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- start search -->
                        <searchPage
                            :companyKeys="companyKeys"
                            :defaultsKeys="defaultsKeys"
                            :filterSetting="tableSetting"
                            :isSearch="true"
                            :isVisible="isVisible"
                            page="general.unitServicesTable"
                            @dataSearch="() => getData(1, url, filterSearch(searchField))"
                            @editSearch="(search) => (searchMain = search)"
                            @searchFun="(fields) => (searchField = fields)"
                        />
                        <!-- end search -->

                        <!-- start setting -->
                        <actionSetting
                            :checkAll="checkAll"
                            :companyKeys="companyKeys"
                            :current_page="current_page"
                            :defaultsKeys="defaultsKeys"
                            :group="true"
                            :isCreate="true"
                            :isDelete="true"
                            :isEdit="true"
                            :isExcl="true"
                            :isFilter="true"
                            :isGroup="true"
                            :isPaginate="true"
                            :isPrint="true"
                            :isSetting="true"
                            :isVisible="isVisible"
                            :objPagination="objPagination"
                            :permissionCreate="isPermission('create Unit Services RealState')"
                            :permissionDelete="isPermission('delete Unit Services RealState')"
                            :permissionUpdate="isPermission('update Unit Services RealState')"
                            :settings="tableSetting"
                            :sideAction="true"
                            :sidePaginate="true"
                            @DataCurrentPage="(page) => getDataCurrentPage(page)"
                            @actionChange="
                ({ typeAction, id }) => changeType({ typeAction, id })
              "
                            @currentPage="(page) => (current_page = page)"
                            @delete="(ids) => deleteRow(ids, url)"
                            @gen_xsl="ExportExcel('xlsx')"
                            @perviousOrNext="
                (current) => getData(current, url, filterSearch(searchField))
              "
                            @settingFun="(setting) => settingFun(setting)"
                        />
                        <!-- end setting -->

                        <!--  create or edit   -->
                        <unitServices
                            :id="'create'"
                            :companyKeys="companyKeys"
                            :defaultsKeys="defaultsKeys"
                            :idObjEdit="
                idEdit
                  ? {
                      idEdit,
                      dataObj: this.tables.find((el) => el.id == idEdit),
                    }
                  : null
              "
                            :isPage="true"
                            :isPermission="isPermission"
                            :isRequiredPage="isRequired"
                            :isVisiblePage="isVisible"
                            :type="type"
                            :url="url"
                            @getDataTable="getData(1, url, filterSearch(searchField))"
                        />
                        <!--  /create   -->

                        <!-- start .table-responsive-->
                        <div
                            id="printCustom"
                            ref="exportable_table"
                            class="table-responsive mb-3 custom-table-theme position-relative"
                        >
                            <!--       start loader       -->
                            <loader v-if="isLoader" size="large"/>
                            <!--       end loader       -->

                            <tableCustom
                                :Tooltip="Tooltip"
                                :companyKeys="companyKeys"
                                :defaultsKeys="defaultsKeys"
                                :enabled3="enabled3"
                                :isAction="true"
                                :isDelete="true"
                                :isEdit="true"
                                :isInputCheck="true"
                                :isLog="true"
                                :isVisible="isVisible"
                                :permissionDelete="
                  isPermission('delete Unit Services RealState')
                "
                                :permissionUpdate="
                  isPermission('update Unit Services RealState')
                "
                                :tableSetting="tableSetting"
                                :tables="tables"
                                @checkRowTable="(id) => checkRow(id)"
                                @checkRows="(cR) => (checkAll = cR)"
                                @delete="(ids) => deleteRow(ids, url)"
                                @editRow="(id) => dbClickRow(id)"
                                @logFire="(id) => log(id, url)"
                            />
                        </div>
                        <!-- end .table-responsive-->
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import loader from "../../../components/general/loader";
import permissionGuard from "../../../helper/permission";
import searchPage from "../../../components/general/searchPage";
import actionSetting from "../../../components/general/actionSetting";
import tableCustom from "../../../components/general/tableCustom";
import translation from "../../../helper/mixin/translation-mixin";
import customTable from "../../../helper/mixin/customTable";
import successError from "../../../helper/mixin/success&error";
import crudHelper from "../../../helper/mixin/crudHelper";
import unitServices from "../../../components/create/realEstate/unitServices";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Unit Services",
        meta: [{name: "description", content: "Unit Services"}],
    },
    mixins: [translation, customTable, successError, crudHelper],
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "unitServices", "all Unit Services RealState");
        });
    },
    components: {
        Layout,
        PageHeader,
        loader,
        searchPage,
        actionSetting,
        tableCustom,
        unitServices,
    },
    data() {
        return {
            url: "/real-estate/unit-service",
            searchMain: "",
            tableSetting: [
                {
                    isFilter: true,
                    isSet: true,
                    trans: "unit",
                    isV: "unit_id",
                    type: "relation",
                    name: "unit",
                    sort: false,
                    col1: "name",
                    col2: "name_e",
                    setting: {unit_id: true},
                    isSetting: true,
                },
                {
                    isFilter: true,
                    isSet: true,
                    trans: "service",
                    isV: "service_id",
                    type: "relation",
                    name: "service",
                    sort: false,
                    col1: "name",
                    col2: "name_e",
                    setting: {service_id: true},
                    isSetting: true,
                },
                {
                    isFilter: true,
                    isSet: true,
                    trans: "unitServices_price",
                    isV: "default_price",
                    type: "string",
                    sort: true,
                    setting: {default_price: true},
                    isSetting: true,
                },
                {
                    isFilter: true,
                    isSet: true,
                    trans: "date_from",
                    isV: "from_date",
                    type: "string",
                    sort: true,
                    setting: {from_date: true},
                    isSetting: true,
                },
            ],
            sendSetting: {},
            searchField: [],
        };
    },
    mounted() {
        this.searchField = this.tableSetting
            .filter((e) => e.isFilter)
            .map((el) => el.isV);
        this.settingFun();
        this.getCustomTableFields("rlst_unit_services");
        this.getData(1, this.url, this.filterSearch(this.searchField));
    },
    methods: {
        filterSearch(fields) {
            const fieldMappings = {
                unit_id: "unit.name",
                service_id: "service.name",
            };

            const filter = fields
                .map((field, index) => fieldMappings[field] || field) // Map fields based on conditions
                .map((mappedField, index) => `columns[${index}]=${mappedField}`)
                .join("&");

            return filter;
        },
        settingFun(setting = null) {
            if (setting) this.tableSetting = setting;
            let l = {},
                names = [];
            names = this.tableSetting.filter((e) => e.isSet).map((el) => el.setting);
            this.tableSetting.forEach((e, i) => {
                l[e.isV] = names[i][e.isV];
                e["isSetting"] = l[e.isV];
            });
            this.sendSetting = l;
        },
    },
};
</script>

