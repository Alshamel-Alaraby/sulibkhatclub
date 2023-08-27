<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import permissionGuard from "../../../helper/permission";

import adminApi from "../../../api/adminAxios";
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
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import alphaArabic from "../../../helper/alphaArabic";
import alphaEnglish from "../../../helper/alphaEnglish";
import {
  dynamicSortString,
  dynamicSortNumber,
} from "../../../helper/tableSort";
import translation from "../../../helper/mixin/translation-mixin";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Unsold unit report",
    meta: [{ name: "description", content: "Unsold unit report" }],
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
      owners: [],
      to_date: "",
      from_date: "",
      owner_id: null,
      buildings: [],
      building_id: null,
      wallets: [],
      wallet_id: null,
      properties: [],
      property_id: null,
      countries: [],
      country_id: null,
      governorates: [],
      governorate_id: null,
      cities: [],
      city_id: null,
      unit_ty: "",
      unit_area: "",
      rooms: "",
      path: "",
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
        unit: true,
        building: true,
        unit_status: true,
        owner: true,
        wallet: true,
        unit_ty: true,
        unit_area: true,
        rooms: true,
        path: true,
      },
      is_disabled: false,
      filterSetting: [],
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
    this.getWallets();
    this.getCountries();
    this.getProperties();
  },
    beforeRouteEnter(to, from, next) {
          next((vm) => {
      return permissionGuard(vm, "Unsold Unit Report", "all unSold_Unit RealState");
    });

    },
    methods: {
    onOwnerSelected(wallet_id) {
      this.owner_id = null;
      this.building_id = null;
      this.getOwners(wallet_id);
      this.getBuildings(wallet_id);
    },
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
      adminApi
        .get(
          `/real-estate/units/general-filter?page=${page}&per_page=${
            this.per_page
          }${this.building_id ? `&building_id=${this.building_id}` : ""}
          ${this.unit_ty ? `&unit_ty=${this.unit_ty}` : ""}
          ${this.unit_area ? `&unit_area=${this.unit_area}` : ""}
          ${this.rooms ? `&rooms=${this.rooms}` : ""}
          ${this.path ? `&path=${this.path}` : ""}
          ${this.property_id ? `&properties=${this.property_id}` : ""}
          ${this.wallet_id ? `&wallet_id=${this.wallet_id}` : ""}
          ${this.owner_id ? `&owner_id=${this.owner_id}` : ""}
          ${this.country_id ? `&country_id=${this.country_id}` : ""}
          ${this.city_id ? `&city_id=${this.city_id}` : ""}
          ${this.governorate_id ? `&governorate_id=${this.governorate_id}` : ""}
          `
        )
        .then((res) => {
          let l = res.data;
          this.items = l.data;
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
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/real-estate/units/general-filter?page=${
              this.current_page
            }&per_page=${this.per_page}${
              this.building_id ? `&building_id=${this.building_id}` : ""
            }
          ${this.unit_ty ? `&unit_ty=${this.unit_ty}` : ""}
          ${this.unit_area ? `&unit_area=${this.unit_area}` : ""}
          ${this.rooms ? `&rooms=${this.rooms}` : ""}
          ${this.path ? `&path=${this.path}` : ""}
          ${this.property_id ? `&properties=${this.property_id}` : ""}
          ${this.wallet_id ? `&wallet_id=${this.wallet_id}` : ""}
          ${this.owner_id ? `&owner_id=${this.owner_id}` : ""}
          ${this.country_id ? `&country_id=${this.country_id}` : ""}
          ${this.city_id ? `&city_id=${this.city_id}` : ""}
          ${this.governorate_id ? `&governorate_id=${this.governorate_id}` : ""}
          `
          )
          .then((res) => {
            let l = res.data;
            this.items = l.data;
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
    async getOwners(wallet_id) {
      this.isLoader = true;
      await adminApi
        .get(`real-estate/units/owner-wallet/${wallet_id}`)
        .then((res) => {
          let l = res.data.data.owners;
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
    async getBuildings(wallet_id) {
      this.isLoader = true;

      await adminApi
        .get(`/real-estate/units/building-wallet/${wallet_id}`)
        .then((res) => {
          let l = res.data.data.buildings;
          this.buildings = l;
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
    async getCountries() {
      this.isLoader = true;
      await adminApi
        .get(`/countries`)
        .then((res) => {
          let l = res.data.data;
          this.countries = l;
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
    async getGovernorates(countryId) {
      this.isLoader = true;
      this.governorate_id = null;
      this.city_id = null;
      await adminApi
        .get(`/governorates?country_id=${countryId}`)
        .then((res) => {
          let l = res.data.data;
          this.governorates = l;
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
    async getCities(governate_id) {
      this.isLoader = true;
      this.city_id = null;
      await adminApi
        .get(
          `/cities?country_id=${this.country_id}&governorate_id=${governate_id}`
        )
        .then((res) => {
          let l = res.data.data;
          this.cities = l;
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
            fn || ("Branch" + "." || "SheetJSTableExport.") + (type || "xlsx")
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
      dialog-class="modal-full-width"
      id="filter"
      :title="$t('general.filter')"
      title-class="font-18"
      body-class="p-4 "
      :hide-footer="true"
    >
      <div class="row mb-2 px-2">
        <div class="col-md-2">
          <label>{{ $t("general.wallet") }}</label>
          <multiselect
            v-model="wallet_id"
            @input="onOwnerSelected"
            :options="wallets.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? wallets.find((x) => x.id == opt).name
                  : wallets.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.owner") }}</label>
          <multiselect
            v-model="owner_id"
            :options="owners.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? owners.find((x) => x.id == opt).name
                  : owners.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.building") }}</label>
          <multiselect
            v-model="building_id"
            :options="buildings.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? buildings.find((x) => x.id == opt).name
                  : buildings.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.country") }}</label>
          <multiselect
            @input="getGovernorates"
            v-model="country_id"
            :options="countries.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? countries.find((x) => x.id == opt).name
                  : countries.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.governorate") }}</label>
          <multiselect
            @input="getCities"
            v-model="governorate_id"
            :options="governorates.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? governorates.find((x) => x.id == opt).name
                  : governorates.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.city") }}</label>
          <multiselect
            v-model="city_id"
            :options="cities.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? cities.find((x) => x.id == opt).name
                  : cities.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
      </div>
      <hr class="mx-2" />
      <div class="row px-2 mb-4">
        <div class="col-md-2">
          <label>{{ $t("general.property") }}</label>
          <multiselect
            v-model="property_id"
            :options="properties.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? properties.find((x) => x.id == opt).name
                  : properties.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.Unit_type") }}</label>
          <input class="form-control" type="text" v-model="unit_ty" />
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.Unit_area") }}</label>
          <input class="form-control" type="text" v-model="unit_area" />
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.rooms") }}</label>
          <input class="form-control" type="text" v-model="rooms" />
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.path") }}</label>
          <input class="form-control" type="text" v-model="path" />
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
                {{ $t("general.UnsolidUnitReport") }}
              </h4>
              <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                <div class="d-inline-block" style="width: 22.2%">
                  <!-- Basic dropdown -->
                  <!-- <b-dropdown
                    variant="primary"
                    :text="$t('general.searchSetting')"
                    ref="dropdown"
                    class="btn-block setting-search"
                  >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="id"
                      class="mb-1"
                    >
                      {{ $t("general.id") }}
                    </b-form-checkbox>
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="path"
                      class="mb-1"
                    >
                      {{ $t("general.path") }}
                    </b-form-checkbox>
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="created_at"
                      class="mb-1"
                    >
                      {{ $t("general.created_at") }}
                    </b-form-checkbox>
                  </b-dropdown> -->
                  <!-- Basic dropdown -->
                </div>

                <div
                  class="d-inline-block position-relative"
                  style="width: 77%"
                >
                  <div class="form-group position-relative"></div>
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
                      <b-form-checkbox v-model="setting.unit" class="mb-1">
                        {{ getCompanyKey("unit") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.building" class="mb-1">
                        {{ getCompanyKey("building") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.unit_status"
                        class="mb-1"
                      >
                        {{ getCompanyKey("unit_status") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.owner" class="mb-1">
                        {{ getCompanyKey("owner") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.wallet" class="mb-1">
                        {{ getCompanyKey("wallet") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.unit_ty" class="mb-1">
                        {{ getCompanyKey("admin_report_unit_ty") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.unit_area" class="mb-1">
                        {{ getCompanyKey("admin_report_unit_area") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.rooms" class="mb-1">
                        {{ getCompanyKey("admin_report_rooms") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.path" class="mb-1">
                        {{ getCompanyKey("admin_report_path") }}
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
                    <th v-if="setting.unit">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("unit") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.building">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("building") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.unit_status">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("unit_status") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.owner">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("owner") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.wallet">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("wallet") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.unit_ty">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("admin_report_unit_ty") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('unit_ty'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-unit_ty'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.unit_area">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("admin_report_unit_area")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('unit_area'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-unit_area'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.rooms">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("admin_report_rooms") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('rooms'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-rooms'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.path">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("admin_report_path") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('path'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-path'))"
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
                    <td v-if="setting.unit">
                      {{ $i18n.locale == "ar" ? data.name : data.name_e }}
                    </td>
                    <td v-if="setting.building">
                      <template v-if="data.building">
                        {{
                              data.building?
                          $i18n.locale == "ar"
                            ? data.building.name
                            : data.building.name_e:''
                        }}
                      </template>
                    </td>
                    <td v-if="setting.unit_status">
                      <template v-if="data.unit_status">
                        {{
                              data.unit_status?
                          $i18n.locale == "ar"
                            ? data.unit_status.name
                            : data.unit_status.name_e:''
                        }}
                      </template>
                    </td>
                    <td v-if="setting.owner">
                      <template v-if="data.owner">
                        {{
                              data.owner?$i18n.locale == "ar"
                            ? data.owner.name
                            : data.owner.name_e:""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.wallet">
                      <template v-if="data.wallet">
                        {{
                              data.wallet?$i18n.locale == "ar"
                            ? data.wallet.name
                            : data.wallet.name_e:""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.unit_ty">{{ data.unit_ty }}</td>
                    <td v-if="setting.unit_area">{{ data.unit_area }}</td>
                    <td v-if="setting.rooms">{{ data.rooms }}</td>
                    <td v-if="setting.path">{{ data.path }}</td>
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
