<script>
import Layout from "../../../layouts/main";
import permissionGuard from "../../../../helper/permission";

import PageHeader from "../../../../components/general/Page-header";
import adminApi from "../../../../api/adminAxios";
import Switches from "vue-switches";
import Multiselect from "vue-multiselect";
import {
  required,
  minLength,
  maxLength,
  integer,
  requiredIf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../../components/widgets/errorMessage";
import loader from "../../../../components/general/loader";
import alphaArabic from "../../../../helper/alphaArabic";
import alphaEnglish from "../../../../helper/alphaEnglish";
import {
  dynamicSortString,
  dynamicSortNumber,
} from "../../../../helper/tableSort";
import translation from "../../../../helper/mixin/translation-mixin";
import { formatDateOnly } from "../../../../helper/startDate";
import { arabicValue, englishValue } from "../../../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Stock Adjustment",
    meta: [{ name: "description", content: "Stock Adjustment" }],
  },
  mixins: [translation],
  components: {
    Multiselect,
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
  },
  data() {
    return {
      from_date: "",
      to_date: "",
      type: null,
      types: [],
      group: null,
      cash_register: null,
      cash_registers: [],
      payment_method: null,
      payment_methods: [],
      brand_id: null,
      brands: [],
      product_id: null,
      products: [],
      adjustment_type: null,
      adjustmentTypes: [],
      group_d: null,
      groups: [],
      branch_id: null,
      branches: [],
      reorders: [],
      reorder: null,
      customer_id: null,
      customers: [],
      employee_id: null,
      employees: [],
      owners: [],
      owner_id: null,
      buildings: [],
      building_id: null,
      wallets: [],
      wallet_id: null,
      properties: [],
      property_id: null,
      units: [],
      unit_id: null,
      sales: [],
      salesman_id: null,
      lng: "",
      lat: "",
      from_price: "",
      to_price: "",
      per_page: 50,
      search: "",
      debounce: {},
      enabled3: true,
      itemsPagination: {},
      progress: 0,
      items: [],
      isLoader: false,
      Tooltip: "",
      mouseEnter: "",
      fields: [],
      company_id: null,
      errors: {},
      isCheckAll: false,
      checkAll: [],
      current_page: 1,
      setting: {
        date: true,
        product: true,
        variant: true,
        adjustment_item: true,
        branch: true,
        adjustment_type: true,
        sold_by: true,
      },
      is_disabled: false,
      filterSetting: ["product"],
      printLoading: true,
      printObj: {
        id: "printData",
      },
    };
  },
  validations: {},
  watch: {
    /**
     * watch per_page
     */
    per_page(after, befour) {
      this.getData();
    },
    /**
     * watch search
     */
    search(after, befour) {
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.getData();
      }, 400);
    },
    /**
     * watch check All table
     */
    isCheckAll(after, befour) {
      if (after) {
        this.items.forEach((el) => {
          if (!this.checkAll.includes(el.id)) {
            this.checkAll.push(el.id);
          }
        });
      } else {
        this.checkAll = [];
      }
    },
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
    this.getProducts();
    this.getBranches();
    this.getAdjustmentTypes();
    this.getData();
  },
  beforeRouteEnter(to, from, next) {
        next((vm) => {
    return permissionGuard(vm, "POS stock adjustment", "all sold_Unit RealState");
  });

   },
  methods: {
    showReport() {
      this.$bvModal.hide(`filter`);
      this.getData(1);
    },
    createBackup() {
      setTimeout(() => {
        let bar = document.getElementById("progress-bar");
        let self = this;
        const config = {
          onUploadProgress: function (progressEvent) {
            const percentCompleted = Math.round(
              (progressEvent.loaded / progressEvent.total) * 100
            );
            self.progress = percentCompleted;
            bar.innerHTML = `${percentCompleted}%`;
            bar.style.width = `${percentCompleted}%`;
          },
        };
        adminApi
          .post(`/backups`, {}, config)
          .then((res) => {
            this.getData();
          })
          .catch((err) => {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.Thereisanerrorinthesystem")}`,
            });
          })
          .finally(() => {
            this.isLoader = false;
          });
      }, 500);
    },

    formatDate(value) {
      return formatDateOnly(value);
    },
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      for (let i = 0; i < this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }
      this.from_date = this.from_date ? this.from_date : this.to_date;
      this.to_date = this.to_date ? this.to_date : this.from_date;
      this.from_price = this.from_price ? this.from_price : this.to_price;
      this.to_price = this.to_price ? this.to_price : this.from_price;
      this.lng = this.lng ? this.lng : this.lat;
      this.lat = this.lat ? this.lat : this.lng;
      adminApi
        .get(
          `/real-estate/contracts/general-filter?page=${page}&per_page=${
            this.per_page
          }${this.building_id ? `&building_id=${this.building_id}` : ""}
          ${this.owner_id ? `&owner_id=${this.owner_id}` : ""}
          ${this.wallet_id ? `&wallet_id=${this.wallet_id}` : ""}
          ${this.from_date ? `&start_date=${this.from_date}` : ""}
          ${this.to_date ? `&end_date=${this.to_date}` : ""}
          ${this.lat ? `&lat=${this.lat}` : ""}
          ${this.lng ? `&lng=${this.lng}` : ""}
          ${this.from_price ? `&unit_value_from=${this.from_price}` : ""}
          ${this.to_price ? `&unit_value_to=${this.to_price}` : ""}
          ${this.property_id ? `&properties=${this.property_id}` : ""}
          ${this.salesman_id ? `&salesman_id=${this.salesman_id}` : ""}
          ${this.unit_id ? `&unit_id=${this.unit_id}` : ""}`
        )
        .then((res) => {
          let l = res.data;
          let items = res.data.data;
          this.items = items;
          this.itemsPagination = l.pagination;
          this.current_page = l.pagination.current_page;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    getDataCurrentPage(page = 1) {
      if (
        this.current_page <= this.itemsPagination.last_page &&
        this.current_page != this.itemsPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;

        this.from_date = this.from_date ? this.from_date : this.to_date;
        this.to_date = this.to_date ? this.to_date : this.from_date;
        this.from_price = this.from_price ? this.from_price : this.to_price;
        this.to_price = this.to_price ? this.to_price : this.from_price;
        this.lng = this.lng ? this.lng : this.lat;
        this.lat = this.lat ? this.lat : this.lng;

        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/real-estate/contracts/general-filter?page=${
              this.current_page
            }&per_page=${this.per_page}${
              this.building_id ? `&building_id=${this.building_id}` : ""
            }
          ${this.owner_id ? `&owner_id=${this.owner_id}` : ""}
          ${this.wallet_id ? `&wallet_id=${this.wallet_id}` : ""}
          ${this.from_date ? `&start_date=${this.from_date}` : ""}
          ${this.to_date ? `&end_date=${this.to_date}` : ""}
          ${this.lat ? `&lat=${this.lat}` : ""}
          ${this.lng ? `&lng=${this.lng}` : ""}
          ${this.from_price ? `&unit_value_from=${this.from_price}` : ""}
          ${this.to_price ? `&unit_value_to=${this.to_price}` : ""}
          ${this.property_id ? `&properties=${this.property_id}` : ""}
          ${this.salesman_id ? `&salesman_id=${this.salesman_id}` : ""}
          ${this.unit_id ? `&unit_id=${this.unit_id}` : ""}`
          )
          .then((res) => {
            let l = res.data;
            let items = res.data.data;
            this.items = items;
            this.itemsPagination = l.pagination;
            this.current_page = l.pagination.current_page;
          })
          .catch((err) => {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.Thereisanerrorinthesystem")}`,
            });
          })
          .finally(() => {
            this.isLoader = false;
          });
      }
    },
    async getOwners() {
      this.isLoader = true;

      await adminApi
        .get(`/real-estate/owners`)
        .then((res) => {
          let l = res.data.data;
          this.owners = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    async getCustomers() {
      this.isLoader = true;
      await adminApi
        .get(`/general-customer`)
        .then((res) => {
          let l = res.data.data;
          this.customers = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    async getEmployees() {
      this.isLoader = true;
      await adminApi
        .get(`/employees`)
        .then((res) => {
          let l = res.data.data;
          this.employees = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },

    async getCategories() {
      this.isLoader = true;
      await adminApi
        .get(`/categories`)
        .then((res) => {
          let l = res.data.data;
          this.categories = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    async getBrands() {
      this.isLoader = true;
      await adminApi
        .get(`/brands`)
        .then((res) => {
          let l = res.data.data;
          this.brands = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    async getBranches() {
      this.isLoader = true;
      await adminApi
        .get(`/branches`)
        .then((res) => {
          let l = res.data.data;
          this.branches = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    async getProducts() {
      this.isLoader = true;
      await adminApi
        .get(`/point-of-sale/product`)
        .then((res) => {
          let l = res.data.data;
          this.products = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    async getGroups() {
      this.isLoader = true;
      await adminApi
        .get(`/groups`)
        .then((res) => {
          let l = res.data.data;
          this.groups = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },

    async getSalesTypes() {
      this.isLoader = true;
      await adminApi
        .get(`/salesmen-types`)
        .then((res) => {
          let l = res.data.data;
          this.salesTypes = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    getTypes() {
      this.types = [
        { value: "sales", name: "مبيعات", name_e: "Sales" },
        { value: "purchasing", name: "شراء", name_e: "Purchasing" },
      ];
    },
    getGroupsBy() {
      this.groups = [
        { value: "date", name: "تاريخ", name_e: "Date" },
        { value: "customer", name: "زبون", name_e: "Customer" },
        { value: "employee", name: "موظف", name_e: "Employee" },
      ];
    },
    getCashRegisters() {
      this.cash_registers = [
        {
          value: "main_cash_register",
          name: "التسجيل النقدي الاساسي",
          name_e: "Main Cash Register",
        },
      ];
    },
    getAdjustmentTypes() {
      this.adjustmentTypes = [
        {
          value: "cash",
          name: "منتج تالف",
          name_e: "Damaged Product",
        },
      ];
    },
    async getWallets() {
      this.isLoader = true;

      await adminApi
        .get(`/real-estate/wallets`)
        .then((res) => {
          let l = res.data.data;
          this.wallets = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    async getProperties() {
      this.isLoader = true;

      await adminApi
        .get(`/tree-properties`)
        .then((res) => {
          let l = res.data.data;
          this.properties = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    async getSales() {
      this.isLoader = true;

      await adminApi
        .get(`/salesmen`)
        .then((res) => {
          let l = res.data.data;
          this.sales = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    async getUnits() {
      this.isLoader = true;

      await adminApi
        .get(`/real-estate/units`)
        .then((res) => {
          let l = res.data.data;
          this.units = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    sortString(value) {
      return dynamicSortString(value);
    },
    SortNumber(value) {
      return dynamicSortNumber(value);
    },
    /**
     *  start  ckeckRow
     */
    checkRow(id) {
      if (!this.checkAll.includes(id)) {
        this.checkAll.push(id);
      } else {
        let index = this.checkAll.indexOf(id);
        this.checkAll.splice(index, 1);
      }
    },
    /**
     *  end  ckeckRow
     */
    moveInput(tag, c, index) {
      document.querySelector(`${tag}[data-${c}='${index}']`).focus();
    },
    /**
     *   Export Excel
     */
    ExportExcel(type, fn, dl) {
      this.enabled3 = false;
      setTimeout(() => {
        let elt = this.$refs.exportable_table;
        let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
        if (dl) {
          XLSX.write(wb, { bookType: type, bookSST: true, type: "base64" });
        } else {
          XLSX.writeFile(
            wb,
            fn ||
              ("Sales Report" + "." || "SheetJSTableExport.") + (type || "xlsx")
          );
        }
        this.enabled3 = true;
      }, 100);
    },
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <b-modal
      id="filter"
      :title="$t('general.filter')"
      title-class="font-18"
      body-class="p-4 "
      :hide-footer="true"
    >
      <div class="row mb-4 px-2">
        <div class="col-md-12 mb-2">
          <label>{{ $t("general.from_date") }}</label>
          <input class="form-control" type="date" v-model="from_date" />
        </div>
        <div class="col-md-12 mb-2">
          <label>{{ $t("general.to_date") }}</label>
          <input type="date" class="form-control" v-model="to_date" />
        </div>
        <div class="col-md-12 mb-2">
          <label>{{ $t("general.Product") }}</label>
          <multiselect
            v-model="product_id"
            :options="products.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? products.find((x) => x.id == opt).title
                  : products.find((x) => x.id == opt).title_e
            "
          >
          </multiselect>
        </div>
        <div class="col-md-12 mb-2">
          <label>{{ $t("general.branch") }}</label>
          <multiselect
            v-model="branch_id"
            :options="branches.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? branches.find((x) => x.id == opt).name
                  : branches.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
        <div class="col-md-12">
          <label>{{ $t("general.adjustmentType") }}</label>
          <multiselect
            v-model="adjustment_type"
            :options="adjustmentTypes.map((type) => type.value)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? adjustmentTypes.find((x) => x.value == opt).name
                  : adjustmentTypes.find((x) => x.value == opt).name_e
            "
          >
          </multiselect>
        </div>
      </div>
      <div class="d-flex">
        <b-button
          variant="success"
          type="submit"
          class="mx-2"
          v-if="!isLoader"
          @click.prevent="showReport"
        >
          {{ $t("general.filter") }}
        </b-button>

        <b-button variant="success" class="mx-1" disabled v-else>
          <b-spinner small></b-spinner>
          <span class="sr-only">{{ $t("login.Loading") }}...</span>
        </b-button>
      </div>
    </b-modal>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">
                {{ $t("general.stockAdjustment") }}
              </h4>
              <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                <div class="d-inline-block" style="width: 22.2%">
                  <!-- Basic dropdown -->
                  <b-dropdown
                    variant="primary"
                    :text="$t('general.searchSetting')"
                    ref="dropdown"
                    class="btn-block setting-search dropdown-menu-custom-company"
                  >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="product"
                      class="mb-1"
                    >
                      {{ getCompanyKey("product") }}
                    </b-form-checkbox>
                  </b-dropdown>
                  <!-- Basic dropdown -->
                </div>

                <div
                  class="d-inline-block position-relative"
                  style="width: 77%"
                >
                  <span
                    :class="[
                      'search-custom position-absolute',
                      $i18n.locale == 'ar' ? 'search-custom-ar' : '',
                    ]"
                  >
                    <i class="fe-search"></i>
                  </span>
                  <input
                    class="form-control"
                    style="display: block; width: 93%; padding-top: 3px"
                    type="text"
                    v-model.trim="search"
                    :placeholder="`${$t('general.Search')}...`"
                  />
                </div>
              </div>
            </div>
            <!-- end search -->

            <div
              class="row justify-content-between align-items-center mb-2 px-1"
            >
              <div class="col-md-3 d-flex align-items-center mb-1 mt-2 mb-xl-0">
                <!-- start create and printer -->
                <!-- <b-button
                  v-b-modal.progress
                  variant="primary"
                  class="btn-sm mx-1 font-weight-bold"
                >
                  {{ $t("general.Create") }}
                  <i class="fas fa-plus"></i>
                </b-button> -->
                <div class="d-inline-flex">
                  <button
                    style="margin: 0 15px"
                    @click="ExportExcel('xlsx')"
                    class="custom-btn-dowonload"
                  >
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button v-print="'#printData'" class="custom-btn-dowonload">
                    <i class="fe-printer"></i>
                  </button>
                  <button
                    class="custom-btn-dowonload"
                    @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                    v-if="checkAll.length == 1"
                  >
                    <i class="mdi mdi-square-edit-outline"></i>
                  </button>
                  <!-- start mult delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length > 1"
                    @click.prevent="deleteBranch(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1"
                    @click.prevent="deleteBranch(checkAll[0])"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!--  end one delete  -->
                </div>
                <!-- end create and printer -->
              </div>
              <div
                class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end"
              >
                <div class="d-fex">
                  <!-- start filter and setting -->
                  <div class="d-inline-block">
                    <b-button
                      v-b-modal.filter
                      class="mx-1 custom-btn-background"
                    >
                      {{ $t("general.filter") }}
                      <i class="fas fa-filter"></i>
                    </b-button>
                    <b-button class="mx-1 custom-btn-background">
                      {{ $t("general.group") }}
                      <i class="fe-menu"></i>
                    </b-button>
                    <!-- Basic dropdown -->
                    <b-dropdown
                      variant="primary"
                      :html="`${$t(
                        'general.setting'
                      )} <i class='fe-settings'></i>`"
                      ref="dropdown"
                      class="dropdown-custom-ali"
                    >
                      <b-form-checkbox v-model="setting.date" class="mb-1">
                        {{ getCompanyKey("sales_list_date") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.product" class="mb-1">
                        {{ getCompanyKey("product") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.variant" class="mb-1">
                        {{ getCompanyKey("variant") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.branch" class="mb-1">
                        {{ getCompanyKey("branch") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.adjustment_type"
                        class="mb-1"
                      >
                        {{ getCompanyKey("adjustment_type") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.sold_by" class="mb-1">
                        {{ getCompanyKey("sales_list_sold_by") }}
                      </b-form-checkbox>

                      <div class="d-flex justify-content-end">
                        <a
                          href="javascript:void(0)"
                          class="btn btn-primary btn-sm"
                          >Apply</a
                        >
                      </div>
                    </b-dropdown>
                    <!-- Basic dropdown -->
                  </div>
                  <!-- end filter and setting -->

                  <!-- start Pagination -->
                  <div
                    class="d-inline-flex align-items-center pagination-custom"
                  >
                    <div class="d-inline-block" style="font-size: 13px">
                      {{ itemsPagination.from }}-{{ itemsPagination.to }} /
                      {{ itemsPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            itemsPagination.current_page == 1 ? 'none' : '',
                        }"
                        @click.prevent="
                          getData(itemsPagination.current_page - 1)
                        "
                      >
                        <span>&lt;</span>
                      </a>
                      <input
                        type="text"
                        @keyup.enter="getDataCurrentPage()"
                        v-model="current_page"
                        class="pagination-current-page"
                      />
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            itemsPagination.last_page ==
                            itemsPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(itemsPagination.current_page + 1)
                        "
                      >
                        <span>&gt;</span>
                      </a>
                    </div>
                  </div>
                  <!-- end Pagination -->
                </div>
              </div>
            </div>
            <!-- start .table-responsive-->
            <div
              class="table-responsive mb-3 custom-table-theme position-relative"
            >
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->
              <table
                class="table table-borderless table-hover table-centered m-0"
                ref="exportable_table"
                id="printData"
              >
                <thead>
                  <tr>
                    <th
                      v-if="enabled3"
                      class="do-not-print"
                      scope="col"
                      style="width: 0"
                    >
                      <div class="form-check custom-control">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          v-model="isCheckAll"
                          style="width: 17px; height: 17px"
                        />
                      </div>
                    </th>
                    <th v-if="setting.date">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("sales_list_date") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.product">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("product") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('start_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-start_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.variant">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("variant") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('start_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-start_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.branch">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("branch") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('start_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-start_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.adjustment_type">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("adjustment_type") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('start_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-start_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.sold_by">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("sales_list_sold_by") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('start_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-start_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody v-if="items.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in items"
                    :key="data.id"
                    class="body-tr-custom"
                  >
                    <td v-if="enabled3" class="do-not-print">
                      <div
                        class="form-check custom-control"
                        style="min-height: 1.9em"
                      >
                        <input
                          style="width: 17px; height: 17px"
                          class="form-check-input"
                          type="checkbox"
                          :value="data.id"
                          v-model="checkAll"
                        />
                      </div>
                    </td>
                    <td :class="{ boldTd: data.bold }" v-if="setting.type">
                      <template v-if="!data.bold">
                        {{ data.date }}
                      </template>
                      <template v-else>{{
                        $i18n.locale == "ar" ? data.name : data.name_e
                      }}</template>
                    </td>
                    <td v-if="setting.product">
                      <template v-if="data.product">
                        {{
                          data.product
                            ? $i18n.locale == "ar"
                              ? data.product.name
                              : data.product.name_e
                            : ""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.variant">
                      <template v-if="data.variant">
                        {{
                          data.variant
                            ? $i18n.locale == "ar"
                              ? data.variant.name
                              : data.variant.name_e
                            : ""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.adjustment_item">
                      <template v-if="data.adjustment_item">
                        {{
                          data.adjustment_item
                            ? $i18n.locale == "ar"
                              ? data.adjustment_item.name
                              : data.adjustment_item.name_e
                            : ""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.branch">
                      <template v-if="data.branch">
                        {{
                          data.branch
                            ? $i18n.locale == "ar"
                              ? data.branch.name
                              : data.branch.name_e
                            : ""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.adjustment_type">
                      <template v-if="data.adjustment_type">
                        {{
                          data.adjustment_type
                            ? $i18n.locale == "ar"
                              ? data.adjustment_type.name
                              : data.adjustment_type.name_e
                            : ""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.sold_by">
                      <template v-if="data.sold_by">
                        {{
                          data.sold_by
                            ? $i18n.locale == "ar"
                              ? data.sold_by.name
                              : data.sold_by.name_e
                            : ""
                        }}
                      </template>
                    </td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr>
                    <th class="text-center" colspan="50">
                      {{ $t("general.notDataFound") }}
                    </th>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<style scoped>
.boldTd {
  font-weight: bold;
}
table th,
table td {
  white-space: nowrap !important;
}
@media print {
  .do-not-print {
    display: none;
  }

  .arrow-sort {
    display: none;
  }
}
hr {
  border-bottom: 1px solid #fff;
}
</style>
