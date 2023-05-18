<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {
  required,
  requiredIf,
  minLength,
  maxLength,
  maxValue,
  integer,
  minValue,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import { dynamicSortString } from "../../../helper/tableSort";
import { formatDateOnly } from "../../../helper/startDate";
import translation from "../../../helper/translation-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import Multiselect from "vue-multiselect";
import InstallmentCond from "../../../components/create/receivablePayment/installmentCondition.vue";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Installment Payment Type",
    meta: [{ name: "Installment Payment Type", content: "Installment Payment Type" }],
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
    InstallmentCond,
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      if (vm.$store.state.auth.work_flow_trees.includes("receivable payable-e")) {
        Swal.fire({
          icon: "error",
          title: `${vm.$t("general.Error")}`,
          text: `${vm.$t("general.ModuleExpired")}`,
        });
        return vm.$router.push({ name: "home" });
      }

      if (
        vm.$store.state.auth.work_flow_trees.includes("installment payment type") ||
        vm.$store.state.auth.work_flow_trees.includes("receivable payable") ||
        vm.$store.state.auth.user.type == "super_admin"
      ) {
        return true;
      } else {
        return vm.$router.push({ name: "home" });
      }
    });
  },
  data() {
    return {
      enabled3: true,
      printLoading: true,
      printObj: {
        id: "printCustom",
      },
      per_page: 50,
      search: "",
      debounce: {},
      installmentPaymentsPagination: {},
      installmentPayments: [],
      isLoader: false,
      create: {
        name: "",
        name_e: "",
        auto_freq: 0,
        is_partially: 0,
        is_passed: 0,
        is_passed_all: 0,
        Freq_period: 0,
        is_conditional: 0,
        Condition_id: null,
        installmentPaymentType_freq: 0,
        is_passed_contract_plan: 0,
      },
      conditions: [],
      edit: {
        name: "",
        name_e: "",
        auto_freq: 0,
        is_partially: 0,
        is_passed: 0,
        is_passed_all: 0,
        Freq_period: 0,
        is_conditional: 0,
        Condition_id: null,
        installmentPaymentType_freq: 0,
        is_passed_contract_plan: 0,
      },
      errors: {},
      dropDownSenders: [],
      is_disabled: false,
      isCheckAll: false,
      checkAll: [],
      current_page: 1,
      setting: {
        name: true,
        name_e: true,
        is_passed: true,
        is_partially: true,
      },
      filterSetting: ["name", "name_e"],
      Tooltip: "",
      mouseEnter: null,
    };
  },
  validations: {
    create: {
      name: { required, minLength: minLength(3), maxLength: maxLength(100) },
      name_e: { required, minLength: minLength(3), maxLength: maxLength(100) },
      is_partially: { required },
      is_passed: { required },
      is_passed_all: { required },
      Freq_period: { required },
      is_conditional: { required },
      installmentPaymentType_freq: { required, minValue: minValue(1) },
      is_passed_contract_plan: { required },
      Condition_id: {
        required: requiredIf(function (model) {
          return this.create.is_conditional == 1;
        }),
      },
      auto_freq: { required },
    },
    edit: {
      name: { required, minLength: minLength(3), maxLength: maxLength(100) },
      name_e: { required, minLength: minLength(3), maxLength: maxLength(100) },
      is_partially: { required },
      is_passed: { required },
      is_passed_all: { required },
      Freq_period: { required },
      is_conditional: { required },
      installmentPaymentType_freq: { required, minValue: minValue(1) },
      is_passed_contract_plan: { required },
      Condition_id: {
        required: requiredIf(function (model) {
          return this.edit.is_conditional == 1;
        }),
      },
      auto_freq: { required },
    },
  },
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
        this.installmentPayments.forEach((el) => {
            if (!this.checkAll.includes(el.id) && el.id > 1) {
                this.checkAll.push(el.id);
            }
        });
      } else {
        this.checkAll = [];
      }
    },
  },
  // updated() {
  //   $(function () {
  //     $(".englishInput").keypress(function (event) {
  //       var ew = event.which;
  //       if (ew == 32) return true;
  //       if (48 <= ew && ew <= 57) return true;
  //       if (65 <= ew && ew <= 90) return true;
  //       if (97 <= ew && ew <= 122) return true;
  //       return false;
  //     });
  //     $(".arabicInput").keypress(function (event) {
  //       var ew = event.which;
  //       if (ew == 32) return true;
  //       if (48 <= ew && ew <= 57) return true;
  //       if (65 <= ew && ew <= 90) return false;
  //       if (97 <= ew && ew <= 122) return false;
  //       return true;
  //     });
  //   });
  // },
  mounted() {
    this.getData();
  },
  methods: {
    showConditionModal() {
      if (this.create.Condition_id == 0) {
        this.$bvModal.show("install_condition_create");
        this.create.Condition_id = null;
      }
    },
    showConditionModalEdit() {
      if (this.edit.Condition_id == 0) {
        this.$bvModal.show("install_condition_create");
        this.edit.Condition_id = null;
      }
    },
    getConditions() {
      adminApi
        .get(`/recievable-payable/rp_installment_condation`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({
            id: 0,
            name: "اضف شرط",
            name_e: "Add condition",
          });
          this.conditions = l;
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
    /**
     *  start get Data module && pagination
     */
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      for (let i = 0; i < this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/recievable-payable/rp_installment_payment_types?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.installmentPayments = l.data;
          this.installmentPaymentsPagination = l.pagination;
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
        this.current_page <= this.installmentPaymentsPagination.last_page &&
        this.current_page != this.installmentPaymentsPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/recievable-payable/rp_installment_payment_types?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.installmentPayments = l.data;
            this.installmentPaymentsPagination = l.pagination;
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
    /**
     *  end get Data module && pagination
     */
    /**
     *  start delete module
     */
    deleteModule(id, index) {
      if (Array.isArray(id)) {
        Swal.fire({
          title: `${this.$t("general.Areyousure")}`,
          text: `${this.$t("general.Youwontbeabletoreverthis")}`,
          type: "warning",
          showCancelButton: true,
          confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
          cancelButtonText: `${this.$t("general.Nocancel")}`,
          confirmButtonClass: "btn btn-success mt-2",
          cancelButtonClass: "btn btn-danger ml-2 mt-2",
          buttonsStyling: false,
        }).then((result) => {
          if (result.value) {
            this.isLoader = true;
            adminApi
              .post(`/recievable-payable/rp_installment_payment_types/bulk-delete`, {
                ids: id,
              })
              .then((res) => {
                this.checkAll = [];
                this.getData();
                Swal.fire({
                  icon: "success",
                  title: `${this.$t("general.Deleted")}`,
                  text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                  showConfirmButton: false,
                  timer: 1500,
                });
              })
              .catch((err) => {
                if (err.response.status == 400) {
                  Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.CantDeleteRelation")}`,
                  });
                  this.getData();
                } else {
                  Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
                }
              })
              .finally(() => {
                this.isLoader = false;
              });
          }
        });
      } else {
        Swal.fire({
          title: `${this.$t("general.Areyousure")}`,
          text: `${this.$t("general.Youwontbeabletoreverthis")}`,
          type: "warning",
          showCancelButton: true,
          confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
          cancelButtonText: `${this.$t("general.Nocancel")}`,
          confirmButtonClass: "btn btn-success mt-2",
          cancelButtonClass: "btn btn-danger ml-2 mt-2",
          buttonsStyling: false,
        }).then((result) => {
          if (result.value) {
            this.isLoader = true;

            adminApi
              .delete(`/recievable-payable/rp_installment_payment_types/${id}`)
              .then((res) => {
                this.checkAll = [];
                this.getData();
                Swal.fire({
                  icon: "success",
                  title: `${this.$t("general.Deleted")}`,
                  text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                  showConfirmButton: false,
                  timer: 1500,
                });
              })

              .catch((err) => {
                if (err.response.status == 400) {
                  Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.CantDeleteRelation")}`,
                  });
                } else {
                  Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
                }
              })
              .finally(() => {
                this.isLoader = false;
              });
          }
        });
      }
    },
    /**
     *  end delete module
     */
    /**
     *  reset Modal (create)
     */
    resetModalHidden() {
     this.resetForm();
        this.is_disabled = false;
      this.$bvModal.hide(`create`);
    },
    /**
     *  hidden Modal (create)
     */
    resetModal() {
      this.getConditions();
     this.resetForm();
    },
    /**
     *  create module
     */
    resetForm() {
      this.create = {
        name: "",
        name_e: "",
        auto_freq: 0,
        is_partially: 0,
        is_passed: 0,
        is_passed_all: 0,
        Freq_period: 0,
        is_conditional: 0,
        Condition_id: null,
        installmentPaymentType_freq: 0,
        is_passed_contract_plan: 0,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_disabled = false;
      this.errors = {};
    },
    AddSubmit() {
      if (!this.create.name) {
        this.create.name = this.create.name_e;
      }
      if (!this.create.name_e) {
        this.create.name_e = this.create.name;
      }
      this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        adminApi
          .post(`/recievable-payable/rp_installment_payment_types`, {
            ...this.create,
            installment_payment_type_freq: this.create.installmentPaymentType_freq,
            freq_period: this.create.Freq_period,
            installment_condation_id: this.create.Condition_id,
          })
          .then((res) => {
            this.is_disabled = true;
            this.getData();
            setTimeout(() => {
              Swal.fire({
                icon: "success",
                text: `${this.$t("general.Addedsuccessfully")}`,
                showConfirmButton: false,
                timer: 1500,
              });
            }, 500);
          })
          .catch((err) => {
            if (err.response.data) {
              this.errors = err.response.data.errors;
            } else {
              Swal.fire({
                icon: "error",
                title: `${this.$t("general.Error")}`,
                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
              });
            }
          })
          .finally(() => {
            this.isLoader = false;
          });
      }
    },
    /**
     *  edit module
     */
    editSubmit(id) {
      if (!this.edit.name) {
        this.edit.name = this.edit.name_e;
      }
      if (!this.edit.name_e) {
        this.edit.name_e = this.edit.name;
      }
      this.$v.edit.$touch();

      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        adminApi
          .put(`/recievable-payable/rp_installment_payment_types/${id}`, {
            ...this.edit,
            installment_payment_type_freq: this.edit.installmentPaymentType_freq,
            freq_period: this.edit.Freq_period,
            installment_condation_id: this.edit.Condition_id,
          })
          .then((res) => {
            this.$bvModal.hide(`modal-edit-${id}`);
            this.getData();
            setTimeout(() => {
              Swal.fire({
                icon: "success",
                text: `${this.$t("general.Editsuccessfully")}`,
                showConfirmButton: false,
                timer: 1500,
              });
            }, 500);
          })
          .catch((err) => {
            if (err.response.data) {
              this.errors = err.response.data.errors;
            } else {
              Swal.fire({
                icon: "error",
                title: `${this.$t("general.Error")}`,
                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
              });
            }
          })
          .finally(() => {
            this.isLoader = false;
          });
      }
    },

    /**
     *   show Modal (edit)
     */
    resetModalEdit(id) {
      this.getConditions();
      let module = this.installmentPayments.find((e) => id == e.id);
      this.edit.name = module.name;
      this.edit.name_e = module.name_e;
      this.edit.auto_freq = module.auto_freq;
      this.edit.is_partially = module.is_partially;
      this.edit.is_passed = module.is_passed;
      this.edit.is_passed_all = module.is_passed_all;
      this.edit.Freq_period = module.freq_period;
      this.edit.installmentPaymentType_freq = module.installment_payment_type_freq;
      this.edit.is_passed_contract_plan = module.is_passed_contract_plan;
      this.edit.is_conditional = module.is_conditional;
      this.edit.Condition_id = module.installment_condation_id;
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.errors = {};
      this.edit = {
        name: "",
        name_e: "",
        auto_freq: 0,
        is_partially: 0,
        is_passed: 0,
        is_passed_all: 0,
        Freq_period: 0,
        is_conditional: 0,
        Condition_id: null,
        installmentPaymentType_freq: 0,
        is_passed_contract_plan: 0,
      };
    },

    /**
     *  start  dynamicSortString
     */
    sortString(value) {
      return dynamicSortString(value);
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
    formatDate(value) {
      return formatDateOnly(value);
    },
    log(id) {
      if (this.mouseEnter != id) {
        this.Tooltip = "";
        this.mouseEnter = id;
        adminApi
          .get(`/recievable-payable/rp_installment_payment_types/logs/${id}`)
          .then((res) => {
            let l = res.data.data;
            l.forEach((e) => {
              this.Tooltip += `Created By: ${e.causer_type}; Event: ${
                e.event
              }; Description: ${e.description} ;Created At: ${this.formatDate(
                e.created_at
              )} \n`;
            });
            $(`#tooltip-${id}`).tooltip();
          })
          .catch((err) => {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.Thereisanerrorinthesystem")}`,
            });
          });
      }
    },
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
              ("InstallmentPaymentType" + "." || "SheetJSTableExport.") + (type || "xlsx")
          );
        }
        this.enabled3 = true;
      }, 100);
    },
    arabicValueName(txt) {
      this.create.name = arabicValue(txt);
      this.edit.name = arabicValue(txt);
    },
    englishValueName(txt) {
      this.create.name_e = englishValue(txt);
      this.edit.name_e = englishValue(txt);
    },
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <InstallmentCond
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getConditions"
    />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">
                {{ $t("general.installmentPaymentTypeTable") }}
              </h4>
              <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                <div class="d-inline-block" style="width: 22.2%">
                  <!-- Basic dropdown -->
                  <b-dropdown
                    variant="primary"
                    :text="$t('general.searchSetting')"
                    ref="dropdown"
                    class="btn-block setting-search"
                  >
                    <b-form-checkbox v-model="filterSetting" value="name" class="mb-1">
                      {{ getCompanyKey("installment_payment_type_name") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="filterSetting" value="name_e" class="mb-1">
                      {{ getCompanyKey("installment_payment_type_name_e") }}
                    </b-form-checkbox>
                  </b-dropdown>
                  <!-- Basic dropdown -->
                </div>

                <div class="d-inline-block position-relative" style="width: 77%">
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

            <div class="row justify-content-between align-items-center mb-2 px-1">
              <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                <!-- start create and printer -->
                <b-button
                  v-b-modal.create
                  variant="primary"
                  class="btn-sm mx-1 font-weight-bold"
                >
                  {{ $t("general.Create") }}
                  <i class="fas fa-plus"></i>
                </b-button>
                <div class="d-inline-flex">
                  <button class="custom-btn-dowonload" @click="ExportExcel('xlsx')">
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button class="custom-btn-dowonload" v-print="'#printCustom'">
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
                    @click.prevent="deleteModule(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1"
                    @click.prevent="deleteModule(checkAll[0])"
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
                    <b-button class="mx-1 custom-btn-background">
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
                      :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                      ref="dropdown"
                      class="dropdown-custom-ali"
                    >
                      <b-form-checkbox v-model="setting.name" class="mb-1"
                        >{{ getCompanyKey("installment_payment_type_name") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.name_e" class="mb-1">
                        {{ getCompanyKey("installment_payment_type_name_e") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.is_passed" class="mb-1">
                        {{ getCompanyKey("is_passed") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.is_partially" class="mb-1">
                        {{ getCompanyKey("installment_payment_is_partially") }}
                      </b-form-checkbox>
                      <div class="d-flex justify-content-end">
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm"
                          >Apply</a
                        >
                      </div>
                    </b-dropdown>
                    <!-- Basic dropdown -->
                  </div>
                  <!-- end filter and setting -->

                  <!-- start Pagination -->
                  <div class="d-inline-flex align-items-center pagination-custom">
                    <div class="d-inline-block" style="font-size: 13px">
                      {{ installmentPaymentsPagination.from }}-{{
                        installmentPaymentsPagination.to
                      }}
                      /
                      {{ installmentPaymentsPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            installmentPaymentsPagination.current_page == 1 ? 'none' : '',
                        }"
                        @click.prevent="
                          getData(installmentPaymentsPagination.current_page - 1)
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
                            installmentPaymentsPagination.last_page ==
                            installmentPaymentsPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(installmentPaymentsPagination.current_page + 1)
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

            <!--  create   -->
            <b-modal
              id="create"
              size="lg"
              :title="getCompanyKey('installment_payment_type_create_form')"
              title-class="font-18"
              body-class="p-4 "
              :hide-footer="true"
              @show="resetModal"
              @hidden="resetModalHidden"
            >
              <form>
                <div class="mb-3 d-flex justify-content-end">
                  <b-button
                    variant="success"
                    :disabled="!is_disabled"
                    @click.prevent="resetForm"
                    type="button"
                    :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
                  >
                    {{ $t("general.AddNewRecord") }}
                  </b-button>
                  <template v-if="!is_disabled">
                    <b-button
                      variant="success"
                      type="button"
                      class="mx-1"
                      v-if="!isLoader"
                      @click.prevent="AddSubmit"
                    >
                      {{ $t("general.Add") }}
                    </b-button>

                    <b-button variant="success" class="mx-1" disabled v-else>
                      <b-spinner small></b-spinner>
                      <span class="sr-only">{{ $t("login.Loading") }}...</span>
                    </b-button>
                  </template>
                  <!-- Emulate built in modal footer ok and cancel button actions -->

                  <b-button
                    variant="danger"
                    type="button"
                    @click.prevent="resetModalHidden"
                  >
                    {{ $t("general.Cancel") }}
                  </b-button>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">
                        {{ getCompanyKey("installment_payment_type_name") }}
                        <span class="text-danger">*</span>
                      </label>
                      <div dir="rtl">
                        <input
                          type="text"
                          class="form-control"
                          data-create="1"
                          @keyup="arabicValueName(create.name)"
                          v-model="$v.create.name.$model"
                          :class="{
                            'is-invalid': $v.create.name.$error || errors.name,
                            'is-valid': !$v.create.name.$invalid && !errors.name,
                          }"
                        />
                      </div>
                      <div v-if="!$v.create.name.minLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatleast") }}
                        {{ $v.create.name.$params.minLength.min }}
                        {{ $t("general.letters") }}
                      </div>
                      <div v-if="!$v.create.name.maxLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatmost") }}
                        {{ $v.create.name.$params.maxLength.max }}
                        {{ $t("general.letters") }}
                      </div>
                      <template v-if="errors.name">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.name"
                          :key="index"
                        >
                          {{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">
                        {{ getCompanyKey("installment_payment_type_name_e") }}
                        <span class="text-danger">*</span>
                      </label>
                      <div>
                        <input
                          type="text"
                          class="form-control englishInput"
                          data-create="2"
                          @keyup="englishValueName(create.name_e)"
                          v-model="$v.create.name_e.$model"
                          :class="{
                            'is-invalid': $v.create.name_e.$error || errors.name_e,
                            'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                          }"
                        />
                      </div>
                      <div v-if="!$v.create.name_e.minLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatleast") }}
                        {{ $v.create.name_e.$params.minLength.min }}
                        {{ $t("general.letters") }}
                      </div>
                      <div v-if="!$v.create.name_e.maxLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatmost") }}
                        {{ $v.create.name_e.$params.maxLength.max }}
                        {{ $t("general.letters") }}
                      </div>
                      <template v-if="errors.name_e">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.name_e"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="mr-2">
                        {{ getCompanyKey("is_conditional") }}
                      </label>
                      <b-form-group
                        id="create-11"
                        :class="{
                          'is-invalid':
                            $v.create.is_conditional.$error || errors.is_conditional,
                          'is-valid':
                            !$v.create.is_conditional.$invalid && !errors.is_conditional,
                        }"
                      >
                        <b-form-radio
                          class="d-inline-block"
                          v-model="$v.create.is_conditional.$model"
                          name="some-radios-create-is_conditional"
                          value="1"
                        >
                          {{ $t("general.Yes") }}
                        </b-form-radio>
                        <b-form-radio
                          class="d-inline-block m-1"
                          v-model="$v.create.is_conditional.$model"
                          name="some-radios-create-is_conditional"
                          value="0"
                        >
                          {{ $t("general.No") }}
                        </b-form-radio>
                      </b-form-group>
                      <template v-if="errors.is_conditional">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.is_conditional"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <template v-if="create.is_conditional == 1">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="my-1 mr-2">
                          {{ getCompanyKey("condition") }}
                          <span class="text-danger">*</span>
                        </label>
                        <multiselect
                          @input="showConditionModal"
                          v-model="create.Condition_id"
                          :options="conditions.map((type) => type.id)"
                          :custom-label="
                            (opt) =>
                              $i18n.locale == 'ar'
                                ? conditions.find((x) => x.id == opt).name
                                : conditions.find((x) => x.id == opt).name_e
                          "
                          :class="{
                            'is-invalid':
                              $v.create.Condition_id.$error || errors.Condition_id,
                          }"
                        >
                        </multiselect>
                        <div
                          v-if="!$v.create.Condition_id.required"
                          class="invalid-feedback"
                        >
                          {{ $t("general.fieldIsRequired") }}
                        </div>

                        <template v-if="errors.condition_id">
                          <ErrorMessage
                            v-for="(errorMessage, index) in errors.condition_id"
                            :key="index"
                            >{{ errorMessage }}
                          </ErrorMessage>
                        </template>
                      </div>
                    </div>
                  </template>
                  <template v-else>
                    <div class="col-md-6"></div>
                  </template>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">
                        {{ getCompanyKey("installmentPaymentType_freq") }}
                        <span class="text-danger">*</span>
                      </label>

                      <b-form-group
                        id="edit-11"
                        :class="{
                          'is-invalid':
                            $v.create.installmentPaymentType_freq.$error ||
                            errors.installmentPaymentType_freq,
                          'is-valid':
                            !$v.create.installmentPaymentType_freq.$invalid &&
                            !errors.installmentPaymentType_freq,
                        }"
                      >
                        <b-form-radio
                          class="d-inline-block"
                          v-model="$v.create.installmentPaymentType_freq.$model"
                          name="installmentPaymentType_freq"
                          value="1"
                        >
                          {{ $t("general.Yes") }}
                        </b-form-radio>
                        <b-form-radio
                          class="d-inline-block m-1"
                          v-model="$v.create.installmentPaymentType_freq.$model"
                          name="installmentPaymentType_freq"
                          value="0"
                        >
                          {{ $t("general.No") }}
                        </b-form-radio>
                      </b-form-group>
                      <template v-if="errors.installmentPaymentType_freq">
                        <ErrorMessage
                          v-for="(
                            errorMessage, index
                          ) in errors.installmentPaymentType_freq"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <template v-if="create.installmentPaymentType_freq == 1">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">
                          {{ getCompanyKey("freq_period") }}
                          <span class="text-danger">*</span>
                        </label>
                        <input
                          type="number"
                          class="form-control"
                          data-create="2"
                          v-model="$v.create.Freq_period.$model"
                          :class="{
                            'is-invalid':
                              $v.create.Freq_period.$error || errors.Freq_period,
                            'is-valid':
                              !$v.create.Freq_period.$invalid && !errors.Freq_period,
                          }"
                        />
                        <template v-if="errors.Freq_period">
                          <ErrorMessage
                            v-for="(errorMessage, index) in errors.Freq_period"
                            :key="index"
                            >{{ errorMessage }}
                          </ErrorMessage>
                        </template>
                      </div>
                    </div>
                  </template>
                  <template v-else>
                    <div class="col-md-6"></div>
                  </template>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="mr-2">
                        {{ getCompanyKey("installment_payment_is_partially") }}
                      </label>
                      <b-form-group
                        id="edit-11"
                        :class="{
                          'is-invalid':
                            $v.create.is_partially.$error || errors.is_partially,
                          'is-valid':
                            !$v.create.is_partially.$invalid && !errors.is_partially,
                        }"
                      >
                        <b-form-radio
                          class="d-inline-block"
                          v-model="$v.create.is_partially.$model"
                          name="some-radios-is_partially"
                          value="1"
                        >
                          {{ $t("general.Yes") }}
                        </b-form-radio>
                        <b-form-radio
                          class="d-inline-block m-1"
                          v-model="$v.create.is_partially.$model"
                          name="some-radios-is_partially"
                          value="0"
                        >
                          {{ $t("general.No") }}
                        </b-form-radio>
                      </b-form-group>
                      <template v-if="errors.is_partially">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.is_partially"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="mr-2">
                        {{ getCompanyKey("is_passed") }}
                      </label>
                      <b-form-group
                        :class="{
                          'is-invalid': $v.create.is_passed.$error || errors.is_passed,
                          'is-valid': !$v.create.is_passed.$invalid && !errors.is_passed,
                        }"
                      >
                        <b-form-radio
                          class="d-inline-block"
                          v-model="$v.create.is_passed.$model"
                          name="some-radioscreate-is_passed"
                          value="1"
                        >
                          {{ $t("general.Yes") }}
                        </b-form-radio>
                        <b-form-radio
                          class="d-inline-block m-1"
                          v-model="$v.create.is_passed.$model"
                          name="some-radioscreate-is_passed"
                          value="0"
                        >
                          {{ $t("general.No") }}
                        </b-form-radio>
                      </b-form-group>
                      <template v-if="errors.is_passed">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.is_passed"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <!-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="mr-2" for="field-12">
                                                {{ getCompanyKey("is_passed_all") }}
                                            </label>
                                            <b-form-group id="create-11" :class="{
                                                'is-invalid':
                                                $v.create.is_passed_all.$error || errors.is_passed_all,
                                                'is-valid':
                                                !$v.create.is_passed_all.$invalid && !errors.is_passed_all,
                                            }">
                                                <b-form-radio class="d-inline-block"
                                                              v-model="$v.create.is_passed_all.$model"
                                                              name="some-radios-create-is_passed_all" value="1">
                                                    {{ $t("general.Yes") }}
                                                </b-form-radio>
                                                <b-form-radio class="d-inline-block m-1"
                                                              v-model="$v.create.is_passed_all.$model"
                                                              name="some-radios-create-is_passed_all" value="0">
                                                    {{ $t("general.No") }}
                                                </b-form-radio>
                                            </b-form-group>
                                            <template v-if="errors.is_passed_all">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.is_passed_all"
                                                              :key="index">{{
                                                        errorMessage
                                                    }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div> -->
                  <!-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="mr-2" for="field-12">
                                                {{ getCompanyKey("is_passed_contract_plan") }}
                                            </label>
                                            <b-form-group id="create-11" :class="{
                                                    'is-invalid':
                                                    $v.create.is_passed_contract_plan.$error || errors.is_passed_contract_plan,
                                                    'is-valid':
                                                    !$v.create.is_passed_contract_plan.$invalid &&
                                                    !errors.is_passed_contract_plan,
                                                }">
                                                <b-form-radio class="d-inline-block"
                                                              v-model="$v.create.is_passed_contract_plan.$model"
                                                              name="some-radios-create-is_passed_contract_plan"
                                                              value="1">{{
                                                        $t("general.Yes")
                                                    }}
                                                </b-form-radio>
                                                <b-form-radio class="d-inline-block m-1"
                                                              v-model="$v.create.is_passed_contract_plan.$model"
                                                              name="some-radios-create-is_passed_contract_plan"
                                                              value="0">{{
                                                        $t("general.No")
                                                    }}
                                                </b-form-radio>
                                            </b-form-group>
                                            <template v-if="errors.is_passed_contract_plan">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.is_passed_contract_plan"
                                                    :key="index">{{
                                                        errorMessage
                                                    }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div> -->
                  <!-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="mr-2" for="field-11">
                                                {{ getCompanyKey("installment_payment_auto_freq") }}
                                            </label>
                                            <b-form-group id="edit-11" :class="{
                                                'is-invalid': $v.create.auto_freq.$error || errors.auto_freq,
                                                'is-valid': !$v.create.auto_freq.$invalid && !errors.auto_freq,
                                            }">
                                                <b-form-radio class="d-inline-block"
                                                              v-model="$v.create.auto_freq.$model"
                                                              name="some-radioscreate-auto_freq" value="1">{{
                                                        $t("general.Yes")
                                                    }}
                                                </b-form-radio>
                                                <b-form-radio class="d-inline-block m-1"
                                                              v-model="$v.create.auto_freq.$model"
                                                              name="some-radioscreate-auto_freq" value="0">{{
                                                        $t("general.No")
                                                    }}
                                                </b-form-radio>
                                            </b-form-group>
                                            <template v-if="errors.auto_freq">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.auto_freq"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div> -->
                </div>
              </form>
            </b-modal>
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

              <table class="table table-borderless table-hover table-centered m-0">
                <thead>
                  <tr>
                    <th scope="col" style="width: 0" v-if="enabled3" class="do-not-print">
                      <div class="form-check custom-control">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          v-model="isCheckAll"
                          style="width: 17px; height: 17px"
                        />
                      </div>
                    </th>
                    <th v-if="setting.name">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("installment_payment_type_name") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="installmentPayments.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="installmentPayments.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.name_e">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("installment_payment_type_name_e")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="installmentPayments.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="installmentPayments.sort(sortString('-name_e'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.is_passed">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("is_passed") }}</span>
                      </div>
                    </th>
                    <th v-if="setting.is_partially">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("installment_payment_is_partially")
                        }}</span>
                      </div>
                    </th>
                    <th v-if="enabled3" class="do-not-print">
                      {{ $t("general.Action") }}
                    </th>
                    <th v-if="enabled3" class="do-not-print">
                      <i class="fas fa-ellipsis-v"></i>
                    </th>
                  </tr>
                </thead>
                <tbody v-if="installmentPayments.length > 0">
                  <tr
                    @click.capture="data.id>1?checkRow(data.id):''"
                    @dblclick.prevent="data.id>1?$bvModal.show(`modal-edit-${data.id}`):''"
                    v-for="(data, index) in installmentPayments"
                    :key="data.id"
                    class="body-tr-custom"
                  >
                    <td v-if="enabled3" class="do-not-print">
                      <div class="form-check custom-control" style="min-height: 1.9em">
                        <input :disabled="data.id == 1"
                          style="width: 17px; height: 17px"
                          class="form-check-input"
                          type="checkbox"
                          :value="data.id"
                          v-model="checkAll"
                        />
                      </div>
                    </td>
                    <td v-if="setting.name">
                      <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                    </td>
                    <td v-if="setting.name_e">
                      <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
                    </td>
                    <td v-if="setting.is_passed">
                      <span
                        :class="[
                          data.is_passed == 1 ? 'text-success' : 'text-danger',
                          'badge',
                        ]"
                      >
                        {{
                          data.is_passed == 1
                            ? `${$t("general.Active")}`
                            : `${$t("general.Inactive")}`
                        }}
                      </span>
                    </td>
                    <td v-if="setting.is_partially">
                      <span
                        :class="[
                          data.is_partially == 1 ? 'text-success' : 'text-danger',
                          'badge',
                        ]"
                      >
                        {{
                          data.is_partially == 1
                            ? `${$t("general.Active")}`
                            : `${$t("general.Inactive")}`
                        }}
                      </span>
                    </td>
                    <td v-if="enabled3" class="do-not-print">
                      <div class="btn-group">
                        <button
                          type="button"
                          class="btn btn-sm dropdown-toggle dropdown-coustom"
                          data-toggle="dropdown"
                          aria-expanded="false"
                        >
                          {{ $t("general.commands") }}
                          <i class="fas fa-angle-down"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-custom">
                          <a v-if="data.id > 1"
                            class="dropdown-item"
                            href="#"
                            @click="$bvModal.show(`modal-edit-${data.id}`)"
                          >
                            <div
                              class="d-flex justify-content-between align-items-center text-black"
                            >
                              <span>{{ $t("general.edit") }}</span>
                              <i class="mdi mdi-square-edit-outline text-info"></i>
                            </div>
                          </a>
                          <a v-if="data.id > 1"
                            class="dropdown-item text-black"
                            href="#"
                            @click.prevent="deleteModule(data.id)"
                          >
                            <div
                              class="d-flex justify-content-between align-items-center text-black"
                            >
                              <span>{{ $t("general.delete") }}</span>
                              <i class="fas fa-times text-danger"></i>
                            </div>
                          </a>
                        </div>
                      </div>

                      <!--  edit   -->
                      <b-modal
                        size="lg"
                        :id="`modal-edit-${data.id}`"
                        :title="getCompanyKey('installment_payment_type_edit_form')"
                        title-class="font-18"
                        body-class="p-4"
                        :ref="`edit-${data.id}`"
                        :hide-footer="true"
                        @show="resetModalEdit(data.id)"
                        @hidden="resetModalHiddenEdit(data.id)"
                      >
                        <form>
                          <div class="mb-3 d-flex justify-content-end">
                            <!-- Emulate built in modal footer ok and cancel button actions -->
                            <b-button
                              variant="success"
                              type="submit"
                              class="mx-1"
                              v-if="!isLoader"
                              @click.prevent="editSubmit(data.id)"
                            >
                              {{ $t("general.Edit") }}
                            </b-button>

                            <b-button variant="success" class="mx-1" disabled v-else>
                              <b-spinner small></b-spinner>
                              <span class="sr-only">{{ $t("login.Loading") }}...</span>
                            </b-button>

                            <b-button
                              variant="danger"
                              type="button"
                              @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)"
                            >
                              {{ $t("general.Cancel") }}
                            </b-button>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="field-1" class="control-label">
                                  {{ getCompanyKey("installment_payment_type_name") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <div dir="rtl">
                                  <input
                                    type="text"
                                    class="form-control"
                                    data-create="1"
                                    @keyup="arabicValueName(edit.name)"
                                    v-model="$v.edit.name.$model"
                                    :class="{
                                      'is-invalid': $v.edit.name.$error || errors.name,
                                      'is-valid': !$v.edit.name.$invalid && !errors.name,
                                    }"
                                    id="field-1"
                                  />
                                </div>
                                <div
                                  v-if="!$v.edit.name.minLength"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.Itmustbeatleast") }}
                                  {{ $v.edit.name.$params.minLength.min }}
                                  {{ $t("general.letters") }}
                                </div>
                                <div
                                  v-if="!$v.edit.name.maxLength"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.Itmustbeatmost") }}
                                  {{ $v.edit.name.$params.maxLength.max }}
                                  {{ $t("general.letters") }}
                                </div>
                                <template v-if="errors.name">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.name"
                                    :key="index"
                                  >
                                    {{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="field-2" class="control-label">
                                  {{ getCompanyKey("installment_payment_type_name_e") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <div>
                                  <input
                                    type="text"
                                    class="form-control englishInput"
                                    data-create="2"
                                    @keyup="englishValueName(edit.name_e)"
                                    v-model="$v.edit.name_e.$model"
                                    :class="{
                                      'is-invalid':
                                        $v.edit.name_e.$error || errors.name_e,
                                      'is-valid':
                                        !$v.edit.name_e.$invalid && !errors.name_e,
                                    }"
                                    id="field-2"
                                  />
                                </div>
                                <div
                                  v-if="!$v.edit.name_e.minLength"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.Itmustbeatleast") }}
                                  {{ $v.edit.name_e.$params.minLength.min }}
                                  {{ $t("general.letters") }}
                                </div>
                                <div
                                  v-if="!$v.edit.name_e.maxLength"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.Itmustbeatmost") }}
                                  {{ $v.edit.name_e.$params.maxLength.max }}
                                  {{ $t("general.letters") }}
                                </div>
                                <template v-if="errors.name_e">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.name_e"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="mr-2" for="field-12">
                                  {{ getCompanyKey("is_conditional") }}
                                </label>
                                <b-form-group
                                  id="create-11"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.is_conditional.$error ||
                                      errors.is_conditional,
                                    'is-valid':
                                      !$v.edit.is_conditional.$invalid &&
                                      !errors.is_conditional,
                                  }"
                                >
                                  <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.edit.is_conditional.$model"
                                    name="some-radios-create-is_conditional"
                                    value="1"
                                    >{{ $t("general.Yes") }}
                                  </b-form-radio>
                                  <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.edit.is_conditional.$model"
                                    name="some-radios-create-is_conditional"
                                    value="0"
                                    >{{ $t("general.No") }}
                                  </b-form-radio>
                                </b-form-group>
                                <template v-if="errors.is_conditional">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.is_conditional"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <template v-if="edit.is_conditional == 1">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="my-1 mr-2">
                                    {{ getCompanyKey("condition") }}
                                    <span class="text-danger">*</span>
                                  </label>
                                  <multiselect
                                    @input="showConditionModal"
                                    v-model="edit.Condition_id"
                                    :options="conditions.map((type) => type.id)"
                                    :custom-label="
                                      (opt) =>
                                        $i18n.locale == 'ar'
                                          ? conditions.find((x) => x.id == opt).name
                                          : conditions.find((x) => x.id == opt).name_e
                                    "
                                    :class="{
                                      'is-invalid':
                                        $v.edit.Condition_id.$error ||
                                        errors.Condition_id,
                                    }"
                                  >
                                  </multiselect>
                                  <div
                                    v-if="!$v.edit.Condition_id.required"
                                    class="invalid-feedback"
                                  >
                                    {{ $t("general.fieldIsRequired") }}
                                  </div>

                                  <template v-if="errors.condition_id">
                                    <ErrorMessage
                                      v-for="(errorMessage, index) in errors.condition_id"
                                      :key="index"
                                      >{{ errorMessage }}
                                    </ErrorMessage>
                                  </template>
                                </div>
                              </div>
                            </template>
                            <template v-else>
                                <div class="col-md-6"></div>
                              </template>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="field-2" class="control-label">
                                  {{ getCompanyKey("installmentPaymentType_freq") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <b-form-group
                                  id="edit-11"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.installmentPaymentType_freq.$error ||
                                      errors.installmentPaymentType_freq,
                                    'is-valid':
                                      !$v.edit.installmentPaymentType_freq.$invalid &&
                                      !errors.installmentPaymentType_freq,
                                  }"
                                >
                                  <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.edit.installmentPaymentType_freq.$model"
                                    name="some-radios-installmentPaymentType_freq"
                                    value="1"
                                    >{{ $t("general.Yes") }}
                                  </b-form-radio>
                                  <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.edit.installmentPaymentType_freq.$model"
                                    name="some-radios-installmentPaymentType_freq"
                                    value="0"
                                    >{{ $t("general.No") }}
                                  </b-form-radio>
                                </b-form-group>
                                <template v-if="errors.installmentPaymentType_freq">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.installmentPaymentType_freq"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <template v-if="edit.installmentPaymentType_freq == 1">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="control-label">
                                    {{ getCompanyKey("freq_period") }}
                                    <span class="text-danger">*</span>
                                  </label>
                                  <input
                                    type="number"
                                    class="form-control"
                                    data-create="2"
                                    @keypress.enter="moveInput('select', 'create', 3)"
                                    v-model="$v.edit.Freq_period.$model"
                                    :class="{
                                      'is-invalid':
                                        $v.edit.Freq_period.$error || errors.Freq_period,
                                      'is-valid':
                                        !$v.edit.Freq_period.$invalid &&
                                        !errors.Freq_period,
                                    }"
                                  />
                                  <template v-if="errors.Freq_period">
                                    <ErrorMessage
                                      v-for="(errorMessage, index) in errors.Freq_period"
                                      :key="index"
                                      >{{ errorMessage }}
                                    </ErrorMessage>
                                  </template>
                                </div>
                              </div>
                            </template>
                            <template v-else>
                                <div class="col-md-6"></div>
                            </template>

                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="mr-2">
                                  {{ getCompanyKey("installment_payment_is_partially") }}
                                </label>
                                <b-form-group
                                  :class="{
                                    'is-invalid':
                                      $v.edit.is_partially.$error || errors.is_partially,
                                    'is-valid':
                                      !$v.edit.is_partially.$invalid &&
                                      !errors.is_partially,
                                  }"
                                >
                                  <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.edit.is_partially.$model"
                                    name="some-radios-is_partially"
                                    value="1"
                                  >
                                    {{ $t("general.Yes") }}
                                  </b-form-radio>
                                  <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.edit.is_partially.$model"
                                    name="some-radios-is_partially"
                                    value="0"
                                  >
                                    {{ $t("general.No") }}
                                  </b-form-radio>
                                </b-form-group>
                                <template v-if="errors.is_partially">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.is_partially"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="mr-2" for="field-11">
                                  {{ getCompanyKey("is_passed") }}
                                </label>
                                <b-form-group
                                  id="edit-11"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.is_passed.$error || errors.is_passed,
                                    'is-valid':
                                      !$v.edit.is_passed.$invalid && !errors.is_passed,
                                  }"
                                >
                                  <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.edit.is_passed.$model"
                                    name="some-radioscreate-is_passed"
                                    value="1"
                                    >{{ $t("general.Yes") }}
                                  </b-form-radio>
                                  <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.edit.is_passed.$model"
                                    name="some-radioscreate-is_passed"
                                    value="0"
                                    >{{ $t("general.No") }}
                                  </b-form-radio>
                                </b-form-group>
                                <template v-if="errors.is_passed">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.is_passed"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>

                            <!-- <div class="col-md-4">
                              <div class="form-group">
                                <label class="mr-2" for="field-12">
                                  {{ getCompanyKey("is_passed_all") }}
                                </label>
                                <b-form-group
                                  id="create-11"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.is_passed_all.$error ||
                                      errors.is_passed_all,
                                    'is-valid':
                                      !$v.edit.is_passed_all.$invalid &&
                                      !errors.is_passed_all,
                                  }"
                                >
                                  <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.edit.is_passed_all.$model"
                                    name="some-radios-create-is_passed_all"
                                    value="1"
                                    >{{ $t("general.Yes") }}
                                  </b-form-radio>
                                  <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.edit.is_passed_all.$model"
                                    name="some-radios-create-is_passed_all"
                                    value="0"
                                    >{{ $t("general.No") }}
                                  </b-form-radio>
                                </b-form-group>
                                <template v-if="errors.is_passed_all">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.is_passed_all"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label class="mr-2" for="field-12">
                                  {{ getCompanyKey("is_passed_contract_plan") }}
                                </label>
                                <b-form-group
                                  id="create-11"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.is_passed_contract_plan.$error ||
                                      errors.is_passed_contract_plan,
                                    'is-valid':
                                      !$v.edit.is_passed_contract_plan.$invalid &&
                                      !errors.is_passed_contract_plan,
                                  }"
                                >
                                  <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.edit.is_passed_contract_plan.$model"
                                    name="some-radios-create-is_passed_contract_plan"
                                    value="1"
                                    >{{ $t("general.Yes") }}
                                  </b-form-radio>
                                  <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.edit.is_passed_contract_plan.$model"
                                    name="some-radios-create-is_passed_contract_plan"
                                    value="0"
                                    >{{ $t("general.No") }}
                                  </b-form-radio>
                                </b-form-group>
                                <template v-if="errors.is_passed_contract_plan">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.is_passed_contract_plan"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label class="mr-2" for="field-11">
                                  {{ getCompanyKey("installment_payment_auto_freq") }}
                                </label>
                                <b-form-group
                                  id="edit-11"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.auto_freq.$error || errors.auto_freq,
                                    'is-valid':
                                      !$v.edit.auto_freq.$invalid && !errors.auto_freq,
                                  }"
                                >
                                  <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.edit.auto_freq.$model"
                                    name="some-radioscreate-auto_freq"
                                    value="1"
                                    >{{ $t("general.Yes") }}
                                  </b-form-radio>
                                  <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.edit.auto_freq.$model"
                                    name="some-radioscreate-auto_freq"
                                    value="0"
                                    >{{ $t("general.No") }}
                                  </b-form-radio>
                                </b-form-group>
                                <template v-if="errors.auto_freq">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.auto_freq"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div> -->
                          </div>
                        </form>
                      </b-modal>
                      <!--  /edit   -->
                    </td>
                    <td v-if="enabled3" class="do-not-print">
                      <button
                        @mousemove="log(data.id)"
                        @mouseover="log(data.id)"
                        type="button"
                        class="btn"
                        :id="`tooltip-${data.id}`"
                        :data-placement="$i18n.locale == 'en' ? 'left' : 'right'"
                        :title="Tooltip"
                      >
                        <i class="fe-info" style="font-size: 22px"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr>
                    <th class="text-center" colspan="9">
                      {{ $t("general.notDataFound") }}
                    </th>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- end .table-responsive-->
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>
