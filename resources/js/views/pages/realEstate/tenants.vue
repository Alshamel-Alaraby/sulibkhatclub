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
import Tenant from "../../../components/create/realEstate/tenants.vue";
/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Tenants",
    meta: [{ name: "description", content: "Tenants" }],
  },
  mixins: [translation, customTable, successError, crudHelper],
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Tenants", "all Tenants");
    });
  },
  components: {
    Layout,
    PageHeader,
    loader,
    searchPage,
    actionSetting,
    tableCustom,
    Tenant,
  },
  data() {
    return {
      url: "/real-estate/tenants",
      searchMain: "",
      tableSetting: [
        {
          isFilter: true,
          isSet: true,
          trans: "tenants_name_ar",
          isV: "name",
          type: "string",
          sort: true,
          setting: { name: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "tenants_name_en",
          isV: "name_e",
          type: "string",
          sort: true,
          setting: { name_e: true },
          isSetting: true,
        },
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
          setting: { unit_id: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "tenants_blacklisted",
          isV: "blacklisted",
          type: "string",
          sort: true,
          setting: { blacklisted: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "tenants_mobile",
          isV: "mobile",
          type: "string",
          sort: true,
          setting: { mobile: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "tenants_gender",
          isV: "gender",
          type: "string",
          sort: true,
          setting: { gender: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "tenants_marital_status",
          isV: "marital_status",
          type: "string",
          sort: true,
          setting: { marital_status: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "tenants_work_address",
          isV: "work_address",
          type: "string",
          sort: true,
          setting: { work_address: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "tenants_home_address",
          isV: "home_address",
          type: "string",
          sort: true,
          setting: { home_address: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "tenants_civil_no",
          isV: "civil_no",
          type: "string",
          sort: true,
          setting: { civil_no: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "tenants_passport_no",
          isV: "passport_no",
          type: "string",
          sort: true,
          setting: { passport_no: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "tenants_residence_no",
          isV: "residence_no",
          type: "string",
          sort: true,
          setting: { residence_no: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "tenants_email",
          isV: "email",
          type: "string",
          sort: true,
          setting: { email: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "account_number",
          isV: "chart_id",
          type: "relation",
          name: "chart",
          sort: false,
          col1: "account_number",
          col2: "account_number",
          setting: { unit_id: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "tenants_tenant_type",
          isV: "tenant_type_id",
          type: "relation",
          name: "tenantType",
          sort: false,
          col1: "name",
          col2: "name_e",
          setting: { tenant_type_id: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "tenants_nationality",
          isV: "country_id",
          type: "relation",
          name: "nationality",
          sort: false,
          col1: "name",
          col2: "name_e",
          setting: { country_id: true },
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
    this.getCustomTableFields("rlst_tenants");
    this.getData(1, this.url, this.filterSearch(this.searchField));
  },
  methods: {
    filterSearch(fields) {
      const fieldMappings = {
        chart: "chart.account_number",
        tenantType: "tenantType.name",
        nationality: "nationality.name",
      };
      const filter = fields
        .map((field, index) => fieldMappings[field] || field)
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

<template>
  <Layout>
    <PageHeader />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <searchPage
              page="general.TenantsTable"
              :isVisible="isVisible"
              :filterSetting="tableSetting"
              :companyKeys="companyKeys"
              :defaultsKeys="defaultsKeys"
              @dataSearch="() => getData(1, url, filterSearch(searchField))"
              @searchFun="(fields) => (searchField = fields)"
              @editSearch="(search) => (searchMain = search)"
              :isSearch="true"
            />
            <!-- end search -->

            <!-- start setting -->
            <actionSetting
              :companyKeys="companyKeys"
              :defaultsKeys="defaultsKeys"
              :current_page="current_page"
              :isCreate="true"
              :isEdit="true"
              :isDelete="true"
              :permissionCreate="isPermission('create Tenants')"
              :permissionUpdate="isPermission('update Tenants')"
              :permissionDelete="isPermission('delete Tenants')"
              :isExcl="true"
              :isPrint="true"
              :checkAll="checkAll"
              :sideAction="true"
              :sidePaginate="true"
              :isFilter="true"
              :group="true"
              :isGroup="true"
              :isVisible="isVisible"
              :isSetting="true"
              :isPaginate="true"
              :settings="tableSetting"
              @delete="(ids) => deleteRow(ids, url)"
              @gen_xsl="ExportExcel('xlsx')"
              @settingFun="(setting) => settingFun(setting)"
              :objPagination="objPagination"
              @perviousOrNext="
                (current) => getData(current, url, filterSearch(searchField))
              "
              @currentPage="(page) => (current_page = page)"
              @DataCurrentPage="(page) => getDataCurrentPage(page)"
              @actionChange="
                ({ typeAction, id }) => changeType({ typeAction, id })
              "
            />
            <!-- end setting -->

            <!--  create or edit   -->
            <Tenant
              :id="'create'"
              :companyKeys="companyKeys"
              :defaultsKeys="defaultsKeys"
              :isPage="true"
              :isVisiblePage="isVisible"
              :isRequiredPage="isRequired"
              :url="url"
              :type="type"
              :idObjEdit="
                idEdit
                  ? {
                      idEdit,
                      dataObj: this.tables.find((el) => el.id == idEdit),
                    }
                  : null
              "
              @getDataTable="getData(1, url, filterSearch(searchField))"
              :isPermission="isPermission"
            />
            <!--  /create   -->

            <!-- start .table-responsive-->
            <div
              class="table-responsive mb-3 custom-table-theme position-relative"
              ref="exportable_table"
              id="printCustom"
            >
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->

              <tableCustom
                :companyKeys="companyKeys"
                :defaultsKeys="defaultsKeys"
                :tables="tables"
                :isEdit="true"
                :isDelete="true"
                :permissionUpdate="isPermission('update Tenants')"
                :permissionDelete="isPermission('delete Tenants')"
                :isVisible="isVisible"
                :tableSetting="tableSetting"
                :enabled3="enabled3"
                :Tooltip="Tooltip"
                @logFire="(id) => log(id, url)"
                @delete="(ids) => deleteRow(ids, url)"
                @editRow="(id) => dbClickRow(id)"
                @checkRows="(cR) => (checkAll = cR)"
                @checkRowTable="(id) => checkRow(id)"
                :isInputCheck="true"
                :isLog="true"
                :isAction="true"
              />
            </div>
            <!-- end .table-responsive-->
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>
