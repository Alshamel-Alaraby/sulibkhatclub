<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import loader from "../../../components/general/loader";
import permissionGuard from "../../../helper/permission";
import Building from "../../../components/create/realEstate/building.vue";
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
  page: {
    title: "Building",
    meta: [{ name: "description", content: "Building" }],
  },
  mixins: [translation, customTable, successError, crudHelper],
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Building", "all building RealState");
    });
  },
  components: {
    Layout,
    PageHeader,
    loader,
    Building,
    searchPage,
    actionSetting,
    tableCustom,
  },
  data() {
    return {
      url: "/real-estate/buildings",
      searchMain: "",
      tableSetting: [
        {
          isFilter: true,
          isSet: true,
          trans: "building_name_ar",
          isV: "name",
          type: "string",
          sort: true,
          setting: { name: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "building_name_en",
          isV: "name_e",
          type: "string",
          sort: true,
          setting: { name_e: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "building_description_ar",
          isV: "description",
          type: "string",
          sort: true,
          setting: { description: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "building_description_en",
          isV: "description_e",
          type: "string",
          sort: true,
          setting: { description_e: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "building_construction_year",
          isV: "construction_year",
          type: "string",
          sort: true,
          setting: { construction_year: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "building_area",
          isV: "building_area",
          type: "string",
          sort: true,
          setting: { building_area: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "building_land_area",
          isV: "land_area",
          type: "string",
          sort: true,
          setting: { land_area: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "building_latitude",
          isV: "lat",
          type: "string",
          sort: true,
          setting: { lat: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "building_longitude",
          isV: "lng",
          type: "string",
          sort: true,
          setting: { lng: true },
          isSetting: true,
        },

        // {
        //   isFilter: true,
        //   isSet: true,
        //   trans: "module",
        //   isV: "module_id",
        //   type: "relation",
        //   name: "module",
        //   sort: false,
        //   col1: "name",
        //   col2: "name_e",
        //   setting: { module_id: true },
        //   isSetting: true,
        // },
        {
          isFilter: true,
          isSet: true,
          trans: "country",
          isV: "country_id",
          type: "relation",
          name: "country",
          sort: false,
          col1: "name",
          col2: "name_e",
          setting: { country_id: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "city",
          isV: "city_id",
          type: "relation",
          name: "city",
          sort: false,
          col1: "name",
          col2: "name_e",
          setting: { city_id: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "avenue",
          isV: "avenue_id",
          type: "relation",
          name: "avenue",
          sort: false,
          col1: "name",
          col2: "name_e",
          setting: { avenue_id: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "building_type",
          isV: "building_type_id",
          type: "relation",
          name: "building_type",
          sort: false,
          col1: "name",
          col2: "name_e",
          setting: { building_type_id: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "company_ownership",
          isV: "company_ownership",
          type: "string",
          sort: true,
          setting: { company_ownership: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "floors_number",
          isV: "floors_number",
          type: "string",
          sort: true,
          setting: { floors_number: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "vaults_number",
          isV: "vaults_number",
          type: "string",
          sort: true,
          setting: { vaults_number: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "ground_floors_number",
          isV: "ground_floors_number",
          type: "string",
          sort: true,
          setting: { ground_floors_number: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "mediums_number",
          isV: "mediums_number",
          type: "string",
          sort: true,
          setting: { mediums_number: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "elevators_number",
          isV: "elevators_number",
          type: "string",
          sort: true,
          setting: { elevators_number: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "electricity_meters_number",
          isV: "electricity_meters_number",
          type: "string",
          sort: true,
          setting: { electricity_meters_number: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "water_meters_number",
          isV: "water_meters_number",
          type: "string",
          sort: true,
          setting: { water_meters_number: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "gas_meters_number",
          isV: "gas_meters_number",
          type: "string",
          sort: true,
          setting: { gas_meters_number: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "central_air_conditioning",
          isV: "central_air_conditioning",
          type: "string",
          sort: true,
          setting: { central_air_conditioning: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "buying_price",
          isV: "buying_price",
          type: "string",
          sort: true,
          setting: { buying_price: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "buying_date",
          isV: "buying_date",
          type: "string",
          sort: true,
          setting: { buying_date: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "middleman_cost",
          isV: "middleman_cost",
          type: "string",
          sort: true,
          setting: { middleman_cost: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "registration_cost",
          isV: "registration_cost",
          type: "string",
          sort: true,
          setting: { registration_cost: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "building_currency",
          isV: "building_currency_id",
          type: "relation",
          name: "building_currency",
          sort: false,
          col1: "name",
          col2: "name_e",
          setting: { building_currency_id: true },
          isSetting: true,
        },

        {
          isFilter: true,
          isSet: true,
          trans: "main_cost_center",
          isV: "main_cost_center_id",
          type: "relation",
          name: "main_cost_center",
          sort: false,
          col1: "name",
          col2: "name_e",
          setting: { main_cost_center_id: true },
          isSetting: true,
        },
        {
          isFilter: true,
          isSet: true,
          trans: "financial_period",
          isV: "financial_period",
          type: "string",
          sort: true,
          setting: { financial_period: true },
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
    this.getCustomTableFields("rlst_buildings");
    this.getData(1, this.url, this.filterSearch(this.searchField));
  },
  methods: {
    filterSearch(fields) {
      const fieldMappings = {
        module: "module.name",
        country_id: "country.name",
        city_id: "city.name",
        avenue_id: "avenue.name",
        building_type_id: "buildingType.name",
        building_currency_id: "buildingCurrency.name",
        main_cost_center_id: "mainCostCenter.name",
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

<template>
  <Layout>
    <PageHeader />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <searchPage
              page="general.buildsTable"
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
              :permissionCreate="isPermission('create building RealState')"
              :permissionUpdate="isPermission('update building RealState')"
              :permissionDelete="isPermission('delete building RealState')"
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
            <Building
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
                :permissionUpdate="isPermission('update building RealState')"
                :permissionDelete="isPermission('delete building RealState')"
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
