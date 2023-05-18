<template>
  <div>
    <Department
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getDepartnent"
    />
    <SalemanType
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getSaleMenType"
    />
    <SalemanPlan
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getPlans"
    />
    <!--  create   -->
    <b-modal
      id="employee-create"
      :title="getCompanyKey('employee_create_form')"
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
          <div class="col-md-6" v-if="isVisible('name')">
            <div class="form-group">
              <label for="field-1" class="control-label">
                {{ getCompanyKey("employee_name_ar") }}
                <span v-if="isRequired('name')" class="text-danger">*</span>
              </label>
              <div dir="rtl">
                <input
                  type="text"
                  class="form-control arabicInput"
                  data-create="1"
                  @keyup="arabicValueName(create.name)"
                  v-model="$v.create.name.$model"
                  :class="{
                    'is-invalid': $v.create.name.$error || errors.name,
                    'is-valid': !$v.create.name.$invalid && !errors.name,
                  }"
                  id="field-1"
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
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-6" v-if="isVisible('name_e')">
            <div class="form-group">
              <label for="field-2" class="control-label">
                {{ getCompanyKey("employee_name_en") }}
                <span v-if="isRequired('name_e')" class="text-danger">*</span>
              </label>
              <div dir="ltr">
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
                  id="field-2"
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
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-6" v-if="isVisible('department_id')">
            <div class="form-group position-relative">
              <label class="control-label">
                {{ getCompanyKey("employee_department") }}
                <span v-if="isRequired('department_id')" class="text-danger"
                  >*</span
                >
              </label>
              <multiselect
                @input="showDepartmentModal"
                v-model="create.department_id"
                :options="departments.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    $i18n.locale == 'ar'
                      ? departments.find((x) => x.id == opt).name
                      : departments.find((x) => x.id == opt).name_e
                "
              >
              </multiselect>
              <div
                v-if="$v.create.department_id.$error || errors.department_id"
                class="text-danger"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.department_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.department_id"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6" v-if="isVisible('manger_id')">
            <div class="form-group">
              <label class="mr-2">
                {{ getCompanyKey("manager") }}
              </label>
              <multiselect
                v-model="create.manger_id"
                :options="managers.map((type) => type.id)"
                :custom-label="(opt) => managers.find((x) => x.id == opt).name"
              >
              </multiselect>
              <div
                v-if="$v.create.manger_id.$error || errors.manger_id"
                class="text-danger"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.manger_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.manger_id"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div v-if="isVisible('customer_handel')" class="col-md-6">
            <div class="form-group">
              <label class="mr-2">
                {{ getCompanyKey("customer_handel") }}
                <span v-if="isRequired('customer_handel')" class="text-danger"
                  >*</span
                >
              </label>
              <select
                class="custom-select"
                v-model="$v.create.customer_handel.$model"
              >
                <option value="non_customer">
                  {{ $t("general.non_customer") }}
                </option>
                <option value="his_customer">
                  {{ $t("general.his_customer") }}
                </option>
                <option value="all_customer">
                  {{ $t("general.all_customer") }}
                </option>
              </select>
              <template v-if="errors.customer_handel">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.customer_handel"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div v-if="isVisible('is_salesman')" class="col-md-6">
            <div class="form-group">
              <label class="mr-2">
                {{ getCompanyKey("is_salesman") }}
                <span v-if="isRequired('is_salesman')" class="text-danger"
                  >*</span
                >
              </label>
              <b-form-group
                :class="{
                  'is-invalid':
                    $v.create.is_salesman.$error || errors.is_salesman,
                  'is-valid':
                    !$v.create.is_salesman.$invalid && !errors.is_salesman,
                }"
              >
                <b-form-radio
                  class="d-inline-block"
                  v-model="$v.create.is_salesman.$model"
                  name="some-radios"
                  value="active"
                  >{{ $t("general.Yes") }}</b-form-radio
                >
                <b-form-radio
                  class="d-inline-block m-1"
                  v-model="$v.create.is_salesman.$model"
                  name="some-radios"
                  value="inactive"
                  >{{ $t("general.No") }}</b-form-radio
                >
              </b-form-group>
              <template v-if="errors.is_salesman">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.is_salesman"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div
            v-if="
              isVisible('salesman_type_id') && create.is_salesman == 'active'
            "
            class="col-md-6"
          >
            <div class="form-group">
              <label class="mr-2">
                {{ getCompanyKey("sale_man_type") }}
                <span v-if="isRequired('salesman_type_id')" class="text-danger"
                  >*</span
                >
              </label>
              <multiselect
                @input="showSalesManTypeModal"
                v-model="create.salesman_type_id"
                :options="salesmenTypes.map((type) => type.id)"
                :custom-label="
                  (opt) => salesmenTypes.find((x) => x.id == opt).name
                "
              >
              </multiselect>
              <div
                v-if="
                  $v.create.salesman_type_id.$error || errors.salesman_type_id
                "
                class="text-danger"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.salesman_type_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.salesman_type_id"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div v-if="create.is_salesman == 'active'" class="col-md-6">
            <div class="form-group">
              <label class="mr-2">
                {{ getCompanyKey("plan") }}
                <span class="text-danger">*</span>
              </label>
              <multiselect
                @input="showPlanModal"
                :multiple="true"
                v-model="create.plan_id"
                :options="plans.map((type) => type.id)"
                :custom-label="(opt) => plans.find((x) => x.id == opt).name"
              >
              </multiselect>
              <div
                v-if="$v.create.plan_id.$error || errors.plan_id"
                class="text-danger"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.plan_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.plan_id"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
        </div>
      </form>
    </b-modal>
    <!--  /create   -->
  </div>
</template>
<script>
import adminApi from "../../api/adminAxios";
import Switches from "vue-switches";
import {
  required,
  minLength,
  maxLength,
  integer,
  requiredIf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../components/widgets/errorMessage";
import loader from "../../components/loader";
import alphaArabic from "../../helper/alphaArabic";
import alphaEnglish from "../../helper/alphaEnglish";
import { dynamicSortString, dynamicSortNumber } from "../../helper/tableSort";
import senderHoverHelper from "../../helper/senderHoverHelper";
import { formatDateOnly } from "../../helper/startDate";
import transMixinComp from "../../helper/translation-comp-mixin";

import { arabicValue, englishValue } from "../../helper/langTransform";
import Department from "../../components/create/department";
import Multiselect from "vue-multiselect";
import SalemanType from "../../components/create/salesManType.vue";
import SalemanPlan from "../../components/create/salesmanPlan.vue";
/**
 * Advanced Table component
 */
export default {
  mixins: [transMixinComp],
  components: {
    SalemanPlan,
    Switches,
    ErrorMessage,
    loader,
    SalemanType,
    Multiselect,
    Department,
  },
  props: {
    id: {
      default: "employee-create",
    },
    companyKeys: {
      default: [],
    },
    defaultsKeys: {
      default: [],
    },
  },
  data() {
    return {
      fields: [],
      enabled3: true,
      printLoading: true,
      printObj: {
        id: "printBuilding",
      },
      per_page: 50,
      search: "",
      debounce: {},
      departments: [],
      isLoader: false,
      Tooltip: "",
      mouseEnter: null,
      company_id: null,
      salesmenTypes: [],
      managers: [],
      plans: [],
      create: {
        name: "",
        name_e: "",
        is_salesman: "active",
        customer_handel: "non_customer",
        department_id: null,
        manger_id: null,
        salesman_type_id: null,
        plan_id: [],
      },
      errors: {},
      isCheckAll: false,
      checkAll: [],
      current_page: 1,
      is_disabled: false,
    };
  },
  validations: {
    create: {
      name: {
        required: requiredIf(function (model) {
          return this.isRequired("name");
        }),
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      name_e: {
        required: requiredIf(function (model) {
          return this.isRequired("name_e");
        }),
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      department_id: {
        required: requiredIf(function (model) {
          return this.isRequired("department_id");
        }),
      },
      is_salesman: {
        required: requiredIf(function (model) {
          return this.isRequired("is_salesman");
        }),
      },
      customer_handel: {
        required: requiredIf(function (model) {
          return this.isRequired("customer_handel");
        }),
      },
      manger_id: {},
      salesman_type_id: {
        required: requiredIf(function (model) {
          return (
            this.isRequired("salesman_type_id") &&
            this.create.is_salesman == "active"
          );
        }),
      },
      plan_id: {
        required: requiredIf(function (model) {
          return this.create.is_salesman == "active";
        }),
      },
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
    this.company_id = this.$store.getters["auth/company_id"];
    this.getCustomTableFields();
  },

  methods: {
    async getSaleMenType() {
      this.isLoader = true;
      await adminApi
        .get(`/salesmen-types?is_empolyee=1`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({
            id: 0,
            name: "اضف نوع رجل المبيعات",
            name_e: "Add saleman type",
          });
          this.salesmenTypes = l;
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
    getCustomTableFields() {
      adminApi
        .get(`/customTable/table-columns/general_employees`)
        .then((res) => {
          this.fields = res.data;
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
    isVisible(fieldName) {
      let res = this.fields.filter((field) => {
        return field.column_name == fieldName;
      });
      return res.length > 0 && res[0].is_visible == 1 ? true : false;
    },
    isRequired(fieldName) {
      let res = this.fields.filter((field) => {
        return field.column_name == fieldName;
      });
      return res.length > 0 && res[0].is_required == 1 ? true : false;
    },
    /**
     *  start get Data countrie && pagination
     */

    async getDepartnent() {
      this.isLoader = true;

      await adminApi
        .get(`/depertments`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضف قسم", name_e: "Add Department" });
          this.departments = l;
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
    async getPlans() {
      this.isLoader = true;

      await adminApi
        .get(`/salesmen-plans`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضف خطة", name_e: "Add plan" });
          this.plans = l;
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
    async getManagers() {
      this.isLoader = true;
      await adminApi
        .get(`/employees?search=true&columns[0]=is_salesman`)
        .then((res) => {
          let l = res.data.data;
          this.managers = l;
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
    showDepartmentModal() {
      if (this.create.department_id == 0) {
        this.$bvModal.show("department-create");
        this.create.department_id = null;
      }
    },
    showSalesManTypeModal() {
      if (this.create.salesman_type_id == 0) {
        this.$bvModal.show("sales-man-type-create");
        this.create.salesman_type_id = null;
      }
    },
    showPlanModal() {
      let index = this.create.plan_id.findIndex((id) => id == 0);
      if (index > -1) {
        this.$bvModal.show("create-salesman-plan");
        this.create.plan_id.splice(index, 1);
      }
    },
    resetModalHidden() {
      this.create = {
        name: "",
        name_e: "",
        customer_handel: "non_customer",

        is_salesman: "active",
        department_id: null,
        manger_id: null,
        salesman_type_id: null,
        plan_id: [],
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.is_disabled = false;
      this.$bvModal.hide(`employee-create`);
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      await this.getDepartnent();
      await this.getManagers();
      await this.getPlans();
      await this.getSaleMenType();
      this.create = {
        name: "",
        name_e: "",
        customer_handel: "non_customer",

        department_id: null,
        is_salesman: "active",
        manger_id: null,
        salesman_type_id: null,
        plan_id: [],
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create countrie
     */
    resetForm() {
      this.create = {
        name: "",
        name_e: "",
        department_id: null,
        is_salesman: "active",
        manger_id: null,
        salesman_type_id: null,
        customer_handel: "non_customer",

        plan_id: [],
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
          .post(`/employees`, {
            ...this.create,
            plans: this.create.plan_id,
            company_id: this.company_id,
            is_salesman: this.create.is_salesman == "active" ? "true" : "false",
          })
          .then((res) => {
            this.is_disabled = true;
            this.$emit("created");
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
    formatDate(value) {
      return formatDateOnly(value);
    },

    arabicValueName(txt) {
      this.create.name = arabicValue(txt);
    },
    englishValueName(txt) {
      this.create.name_e = englishValue(txt);
    },
  },
};
</script>

<style scoped></style>
