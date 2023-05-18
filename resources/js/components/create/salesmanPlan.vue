<template>
  <!--  create   -->
  <b-modal
    id="create-salesman-plan"
    :title="getCompanyKey('salesmanplan_create_form')"
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
            type="submit"
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
        <b-button
          @click.prevent="$bvModal.hide(`create-salesman-plan`)"
          variant="danger"
          type="button"
        >
          {{ $t("general.Cancel") }}
        </b-button>
      </div>
      <div class="row">
        <div class="col-md-12" v-if="isVisible('name')">
          <div class="form-group">
            <label for="field-1" class="control-label">
              {{ getCompanyKey("salesmanplan_name_ar") }}
              <span v-if="isRequired('name')" class="text-danger">*</span>
            </label>
            <div dir="rtl">
              <input
                type="text"
                class="form-control arabicInput"
                data-create="1"
                v-model="$v.create.name.$model"
                :class="{
                  'is-invalid': $v.create.name.$error || errors.name,
                  'is-valid': !$v.create.name.$invalid && !errors.name,
                }"
                @keyup="arabicValue(create.name)"
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
                >{{ errorMessage }}
              </ErrorMessage>
            </template>
          </div>
        </div>
        <div class="col-md-12" v-if="isVisible('name_e')">
          <div class="form-group">
            <label for="field-2" class="control-label">
              {{ getCompanyKey("salesmanplan_name_en") }}
              <span v-if="isRequired('name_e')" class="text-danger">*</span>
            </label>
            <div dir="ltr">
              <input
                type="text"
                class="form-control englishInput"
                data-create="2"
                v-model="$v.create.name_e.$model"
                :class="{
                  'is-invalid': $v.create.name_e.$error || errors.name_e,
                  'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                }"
                @keyup="englishValue(create.name_e)"
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
                >{{ errorMessage }}
              </ErrorMessage>
            </template>
          </div>
        </div>
        <div class="col-md-12" v-if="isVisible('salesmen_plans_source_id')">
          <div class="form-group position-relative">
            <label class="control-label">
              {{ getCompanyKey("source") }}
              <span
                v-if="isRequired('salesmen_plans_source_id')"
                class="text-danger"
                >*</span
              >
            </label>
            <multiselect
              v-model="create.salesmen_plans_source_id"
              :options="sources.map((type) => type.id)"
              :custom-label="(opt) => sources.find((x) => x.id == opt).name"
            >
            </multiselect>
            <div
              v-if="
                $v.create.salesmen_plans_source_id.$error ||
                errors.salesmen_plans_source_id
              "
              class="text-danger"
            >
              {{ $t("general.fieldIsRequired") }}
            </div>
            <template v-if="errors.salesmen_plans_source_id">
              <ErrorMessage
                v-for="(errorMessage, index) in errors.branch_id"
                :key="index"
                >{{ errorMessage }}
              </ErrorMessage>
            </template>
          </div>
        </div>
        <div class="col-md-12" v-if="isVisible('restart_period_id')">
          <div class="form-group position-relative">
            <label class="control-label">
              {{ getCompanyKey("period") }}
              <span v-if="isRequired('restart_period_id')" class="text-danger"
                >*</span
              >
            </label>
            <multiselect
              v-model="create.restart_period_id"
              :options="periods.map((type) => type.id)"
              :custom-label="(opt) => periods.find((x) => x.id == opt).name"
            >
            </multiselect>
            <div
              v-if="
                $v.create.restart_period_id.$error || errors.restart_period_id
              "
              class="text-danger"
            >
              {{ $t("general.fieldIsRequired") }}
            </div>
            <template v-if="errors.restart_period_id">
              <ErrorMessage
                v-for="(errorMessage, index) in errors.restart_period_id"
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
import {
  dynamicSortString,
  dynamicSortNumber,
} from "../../helper/tableSort";
import transMixinComp from "../../helper/translation-comp-mixin";
import senderHoverHelper from "../../helper/senderHoverHelper";
import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../helper/startDate";
import { arabicValue, englishValue } from "../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
   props: ["companyKeys", "defaultsKeys"],

  mixins: [transMixinComp],
  components: {
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
  },
  data() {
    return {
      fields: [],
      per_page: 50,
      search: "",
      debounce: {},
      enabled3: true,
      isLoader: false,
      Tooltip: "",
      mouseEnter: "",
      sources: [],
      periods: [],
      create: {
        name: "",
        name_e: "",
        salesmen_plans_source_id: null,
        restart_period_id: null,
      },
      company_id: null,
      errors: {},
      isCheckAll: false,
      checkAll: [],
      branches: [],
      current_page: 1,
      setting: {
        name: true,
        name_e: true,
        salesmen_plans_source_id: true,
        restart_period_id: true,
      },
      is_disabled: false,
      filterSetting: ["name", "name_e"],
      printLoading: true,
      printObj: {
        id: "printData",
      },
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
      salesmen_plans_source_id: {
        required: requiredIf(function (model) {
          return this.isRequired("salesmen_plans_source_id");
        }),
      },
      restart_period_id: {
        required: requiredIf(function (model) {
          return this.isRequired("restart_period_id");
        }),
      },
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
        this.plans.forEach((el) => {
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
    this.getCustomTableFields();
  },
  updated() {
    // $(function () {
    //   $(".englishInput").keypress(function (event) {
    //     var ew = event.which;
    //     if (ew == 32) return true;
    //     if (48 <= ew && ew <= 57) return true;
    //     if (65 <= ew && ew <= 90) return true;
    //     if (97 <= ew && ew <= 122) return true;
    //     return false;
    //   });
    //   $(".arabicInput").keypress(function (event) {
    //     var ew = event.which;
    //     if (ew == 32) return true;
    //     if (48 <= ew && ew <= 57) return false;
    //     if (65 <= ew && ew <= 90) return false;
    //     if (97 <= ew && ew <= 122) return false;
    //     return true;
    //   });
    // });
  },
  methods: {
    getCustomTableFields() {
      adminApi
        .get(`/customTable/table-columns/general_salesmen_plans`)
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
    formatDate(value) {
      return formatDateOnly(value);
    },
    log(id) {
      if (this.mouseEnter != id) {
        this.Tooltip = "";
        this.mouseEnter = id;
        adminApi
          .get(`/salesmen-plans/logs/${id}`)
          .then((res) => {
            let l = res.data.data;
            l.forEach((e) => {
              this.Tooltip += `Created By: ${e.causer_type}; Event: ${
                e.event
              }; Description: ${e.description} ;Created At: ${this.formatDate(
                e.created_at
              )} \n`;
            });
          })
          .catch((err) => {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.Thereisanerrorinthesystem")}`,
            });
          });
      } else {
      }
    },
    
   
    resetModalHidden() {
      this.create = {
        name: "",
        name_e: "",
        salesmen_plans_source_id: null,
        restart_period_id: null,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.is_disabled = false;
      this.$bvModal.hide(`create-salesman-plan`);
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      if (this.isVisible("salesmen_plans_source_id")) await this.getSources();
      if (this.isVisible("restart_period_id")) await this.getPeriods();
      this.create = {
        name: "",
        name_e: "",
        salesmen_plans_source_id: null,
        restart_period_id: null,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create countrie
     */
    async resetForm() {
      if (this.isVisible("salesmen_plans_source_id")) await this.getSources();
      if (this.isVisible("restart_period_id")) await this.getPeriods();
      this.create = {
        name: "",
        name_e: "",
        salesmen_plans_source_id: null,
        restart_period_id: null,
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
          .post(`/salesmen-plans`, this.create)
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

    async getSources() {
      this.isLoader = true;

      await adminApi
        .get(`/salesmen-plans-source`)
        .then((res) => {
          let l = res.data.data;
          this.sources = l;
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
    async getPeriods() {
      this.isLoader = true;

      await adminApi
        .get(`/restart-period/get-Restart-period-data`)
        .then((res) => {
          let l = res.data.message;
          this.periods = l;
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
     *   Export Excel
     */

    arabicValue(txt) {
      this.create.name = arabicValue(txt);
    },

    englishValue(txt) {
      this.create.name_e = englishValue(txt);
    },
  },
};
</script>

<style scoped></style>
