<template>
  <Layout>
    <PageHeader />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <searchPage
              page="general.buildingPolicyTable"
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
              :permissionCreate="
                isPermission('create building-policy RealState')
              "
              :permissionUpdate="
                isPermission('update building-policy RealState')
              "
              :permissionDelete="
                isPermission('delete building-policy RealState')
              "
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
            <BuildingPolicy
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
                :permissionUpdate="
                  isPermission('update building-policy RealState')
                "
                :permissionDelete="
                  isPermission('delete building-policy RealState')
                "
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
import BuildingPolicy from "../../../components/create/realEstate/buildingPolicy";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Building policy",
    meta: [{ name: "description", content: "Building policy" }],
  },
  mixins: [translation, customTable, successError, crudHelper],
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(
        vm,
        "BuildingPolicy",
        "all building-policy RealState"
      );
    });
  },
  components: {
    Layout,
    PageHeader,
    loader,
    searchPage,
    actionSetting,
    tableCustom,
    BuildingPolicy,
  },
  data() {
    return {
      url: "/real-estate/building-policy",
      searchMain: "",
      tableSetting: [
        {
          isFilter: true,
          isSet: true,
          trans: "building",
          isV: "building_id",
          type: "relation",
          name: "building",
          sort: false,
          col1: "name",
          col2: "name_e",
          setting: { building_id: true },
          isSetting: true,
        },
        {
          isFilter: false,
          isSet: true,
          trans: "building_policy_year",
          isV: "year",
          type: "string",
          setting: { year: true },
          isSetting: true,
        },
        {
          isFilter: false,
          isSet: true,
          trans: "building_policy_month",
          isV: "month",
          type: "string",
          setting: { month: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "policy",
          isV: "policy_id",
          type: "relation",
          name: "policy",
          sort: false,
          col1: "name",
          col2: "name_e",
          setting: { policy_id: true },
          isSetting: true,
        },

        {
          isFilter: false,
          isSet: true,
          trans: "building_policy_amount",
          isV: "amount",
          type: "string",
          setting: { amount: true },
          isSetting: true,
        },
        {
          isFilter: false,
          isSet: true,
          trans: "building_policy_percent",
          isV: "percent",
          type: "string",
          setting: { percent: true },
          isSetting: true,
        },
        {
          isFilter: false,
          isSet: true,
          trans: "building_policy_percent_amount",
          isV: "percent_amount",
          type: "string",
          setting: { percent_amount: true },
          isSetting: true,
        },
        {
          isFilter: false,
          isSet: true,
          trans: "building_policy_after_expenses",
          isV: "after_expenses",
          type: "string",
          setting: { after_expenses: true },
          isSetting: true,
        },
        {
          isFilter: false,
          isSet: true,
          trans: "building_policy_collected_rent_type",
          isV: "collected_rent_type",
          type: "string",
          setting: { collected_rent_type: true },
          isSetting: true,
        },
        {
          isFilter: false,
          isSet: true,
          trans: "building_policy_plus_extra_revenues",
          isV: "plus_extra_revenues",
          type: "string",
          setting: { plus_extra_revenues: true },
          isSetting: true,
        },
        {
          isFilter: false,
          isSet: true,
          trans: "building_policy_company_pays_rest",
          isV: "company_pays_rest",
          type: "string",
          setting: { company_pays_rest: true },
          isSetting: true,
        },
        {
          isFilter: false,
          isSet: true,
          trans: "building_policy_owner_pays_rest",
          isV: "owner_pays_rest",
          type: "string",
          setting: { owner_pays_rest: true },
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
    this.getCustomTableFields("rlst_building_policies");
    this.getData(1, this.url, this.filterSearch(this.searchField));
  },
  methods: {
    filterSearch(fields) {
      let indexC = fields.indexOf("building_id"),
        indexG = fields.indexOf("policy_id"),
        filter = "";
      if (indexC > -1) {
        fields[indexC] =
          this.$i18n.locale == "ar" ? "building.name" : "building.name_e";
      }
      if (indexG > -1) {
        fields[indexG] =
          this.$i18n.locale == "ar" ? "policy.name" : "policy.name_e";
      }
      for (let i = 0; i < fields.length; ++i) {
        filter += `columns[${i}]=${fields[i]}&`;
      }
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

