<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import loader from "../../../components/general/loader";
import searchPage from "../../../components/general/searchPage";
import actionSetting from "../../../components/general/actionSetting";
import tableCustom from "../../../components/general/tableCustom";
import translation from "../../../helper/mixin/translation-mixin";
import customTable from "../../../helper/mixin/customTable";
import successError from "../../../helper/mixin/success&error";
import crudHelper from "../../../helper/mixin/crudHelper";
import CreateOrUpdateUnits from "../../../components/document/RealEstate/components/create-or-update-units.vue";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import printGeneralUnits from "../../../components/document/RealEstate/print/print-general-uints.vue";

/**
 * Advanced Table component
 */
export default {
    name: "documentWithUnits",
    props: {
        document_id: {
            default: null,
        },
    },
    mixins: [translation, customTable, successError, crudHelper],

    components: {
        Layout,
        PageHeader,
        loader,
        searchPage,
        actionSetting,
        tableCustom,
        CreateOrUpdateUnits,
        printGeneralUnits
    },
    data() {
        return {
            document: null,
            selectedPrintRowId: null,
            url: "/real-estate/contract-headers",
            searchMain: "",
            tableSetting: [
                {
                    isFilter: true,
                    isSet: true,
                    trans: "contract_date",
                    isV: "date",
                    type: "string",
                    sort: true,
                    setting: {date: true},
                    isSetting: true,
                },

                {
                    isFilter: true,
                    isSet: true,
                    trans: "tenants_name_en",
                    isV: "tenant_id",
                    type: "relation",
                    name: "tenant",
                    sort: false,
                    col1: "name",
                    col2: "name_e",
                    setting: {tenant_id: true},
                    isSetting: true,
                },
                {
                    isFilter: true,
                    isSet: true,
                    trans: "branch",
                    isV: "branch_id",
                    type: "relation",
                    name: "branch",
                    sort: false,
                    col1: "name",
                    col2: "name_e",
                    setting: {branch_id: true},
                    isSetting: true,
                },
                {
                    isFilter: true,
                    isSet: true,
                    trans: "employee",
                    isV: "employee_id",
                    type: "relation",
                    name: "employee",
                    sort: false,
                    col1: "name",
                    col2: "name_e",
                    setting: {employee_id: true},
                    isSetting: true,
                },
            ],
            sendSetting: {},
            searchField: [],
        };
    },
    mounted() {
        this.getDocumentData();
        this.searchField = this.tableSetting
            .filter((e) => e.isFilter)
            .map((el) => el.isV);
        this.settingFun();
        this.getCustomTableFields("rlst_contract_headers");
        this.getData(1, this.url, this.filterSearch(this.searchField));
    },

    methods: {
        filterSearch(fields) {
            const fieldMappings = {
                tenant_id: "tenant.name",
                branch: "branch.name",
                employee_id: "employee.name",
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
            names = this.tableSetting
                .filter((e) => e.isSet)
                .map((el) => el.setting);
            this.tableSetting.forEach((e, i) => {
                l[e.isV] = names[i][e.isV];
                e["isSetting"] = l[e.isV];
            });
            this.sendSetting = l;
        },
        async getDocumentData() {
            this.isLoader = true;
            await adminApi
                .get(`/document/${this.document_id}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.document = l;
                    // this.relatedDocuments = l.document_relateds;
                    console.log("this is for document", this.document);
                })

                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        openPrintModal(rowId) {
            console.log(rowId)
            this.selectedPrintRowId = rowId;
            console.log('selectedRowId', this.selectedPrintRowId)
            setTimeout(() => {
                this.$refs.printModalRef.$bvModal.show("print");
            }, 550);
        },
    },
};
</script>

<template>
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
                        page="general.RentContractTable"
                        @dataSearch="
                            () => getData(1, url, filterSearch(searchField))
                        "
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
                        :permissionCreate="
                            isPermission('create RentContract RealState')
                        "
                        :permissionDelete="
                            isPermission('delete RentContract RealState')
                        "
                        :permissionUpdate="
                            isPermission('update RentContract RealState')
                        "
                        :settings="tableSetting"
                        :sideAction="true"
                        :sidePaginate="true"
                        @DataCurrentPage="(page) => getDataCurrentPage(page)"
                        @actionChange="
                            ({ typeAction, id }) =>
                                changeType({ typeAction, id })
                        "
                        @currentPage="(page) => (current_page = page)"
                        @delete="(ids) => deleteRow(ids, url)"
                        @gen_xsl="ExportExcel('xlsx')"
                        @perviousOrNext="
                            (current) =>
                                getData(current, url, filterSearch(searchField))
                        "
                        @settingFun="(setting) => settingFun(setting)"
                    />
                    <!-- end setting -->

                    <CreateOrUpdateUnits
                        :id="'create'"
                        :companyKeys="companyKeys"
                        :defaultsKeys="defaultsKeys"
                        :document="document"
                        :document_id="document_id"
                        :idObjEdit="
                            idEdit
                                ? {
                                      idEdit,
                                      dataObj: this.tables.find(
                                          (el) => el.id == idEdit
                                      ),
                                  }
                                : null
                        "
                        :isPage="true"
                        :isPermission="isPermission"
                        :isRequiredPage="isRequired"
                        :isVisiblePage="isVisible"
                        :type="type"
                        :url="url"
                        @getDataTable="
                            getData(1, url, filterSearch(searchField))
                        "
                    />

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
                            :isEdit="false"
                            :isInputCheck="true"
                            :isLog="true"
                            :isPrint="true"
                            :isVisible="isVisible"
                            :permissionDelete="
                                isPermission('delete RentContract RealState')
                            "
                            :permissionUpdate="
                                isPermission('update RentContract RealState')
                            "
                            :tableSetting="tableSetting"
                            :tables="tables"
                            @checkRowTable="(id) => checkRow(id)"
                            @checkRows="(cR) => (checkAll = cR)"
                            @delete="(ids) => deleteRow(ids, url)"
                            @editRow="(id) => dbClickRow(id)"
                            @logFire="(id) => log(id, url)"
                            @printRow="openPrintModal"
                        />
                    </div>
                    <!-- end .table-responsive-->
                    <div>
                        <printGeneralUnits
                            :id="'print'"
                            ref="printModalRef"
                            :document_row_id="selectedPrintRowId"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
