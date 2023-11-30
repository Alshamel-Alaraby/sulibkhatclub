<template>
  <div>
    <Account
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getAccounts"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
      :id="'account-create'"
    />
    <!--  create   -->
    <b-modal
      :id="id"
      :title="
        type != 'edit'
          ? getCompanyKey('expensesItems_create_form')
          : getCompanyKey('expensesItems_edit_form')
      "
      title-class="font-18"
      body-class="p-4 "
      :hide-footer="true"
      @show="resetModal"
      @hidden="resetModalHidden"
    >
      <form>
        <loader size="large" v-if="isCustom && !isPage" />
        <div class="mb-3 d-flex justify-content-end">
          <b-button
            v-if="type != 'edit'"
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
              {{ type != "edit" ? $t("general.Add") : $t("general.edit") }}
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
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("expensesItems_name_ar") }}
                <span class="text-danger">*</span>
              </label>
              <div dir="rtl">
                <input
                  @keyup="arabicValue(create.name)"
                  type="text"
                  class="form-control"
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
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("expensesItems_name_en") }}
                <span class="text-danger">*</span>
              </label>
              <div dir="ltr">
                <input
                  @keyup="englishValue(create.name_e)"
                  type="text"
                  class="form-control"
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
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
        </div>
        <hr
          style="
            margin: 10px 0 !important;
            border-top: 1px solid rgb(141 163 159 / 42%);
          "
        />
        <div class="row">
          <div class="col-md-12 position-relative">
            <div class="form-group">
              <label class="my-1 mr-2">{{
                getCompanyKey("expensesItems_bank_account_number")
              }}</label>
              <multiselect
                @input="showAccountModal"
                v-model="create.chart_id"
                :options="charts.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    charts.find((x) => x.id == opt)
                      ? $i18n.locale == 'ar'
                        ? charts.find((x) => x.id == opt).name
                        : charts.find((x) => x.id == opt).name_e
                      : ''
                "
                :class="{
                  'is-invalid': $v.create.chart_id.$error || errors.chart_id,
                }"
              >
              </multiselect>
              <div v-if="!$v.create.chart_id.required" class="invalid-feedback">
                {{ $t("general.fieldIsRequired") }}
              </div>

              <template v-if="errors.chart_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.chart_id"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group position-relative">
              <label class="control-label">
                {{ getCompanyKey("expensesItems_type") }}
                <span class="text-danger">*</span>
              </label>
              <multiselect
                v-model="$v.create.type.$model"
                :options="typeOptions"
                :class="{
                  'is-invalid': $v.create.type.$error || errors.type,
                  'is-valid': !$v.create.type.$invalid && !errors.type,
                }"
              >
              </multiselect>
              <div
                v-if="$v.create.type.$error || errors.type"
                class="text-danger"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.type">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.type"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
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
import adminApi from "../../../api/adminAxios";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import Multiselect from "vue-multiselect";
import Account from "../general/Accounts.vue";
import {
  required,
  minLength,
  maxLength,
  integer,
  numeric,
  decimal,
} from "vuelidate/lib/validators";

export default {
  name: "services",
  mixins: [transMixinComp, successError],
  components: {
    ErrorMessage,
    loader,
    Multiselect,
    Account,
  },
  props: {
    id: { default: "create" },
    companyKeys: { default: [] },
    defaultsKeys: { default: [] },
    isPage: { default: true },
    isVisiblePage: { default: null },
    isRequiredPage: { default: null },
    type: { default: "create" },
    idObjEdit: { default: null },
    isPermission: {},
    url: { default: "/real-estate/expenses-items" },
  },
  data() {
    return {
      typeOptions: ["building", "unit", "building/unit", "general"],
      fields: [],
      charts: [],
      isLoader: false,
      create: {
        name: "",
        name_e: "",
        chart_id: null,
        type: "",
      },
      company_id: null,
      errors: {},
      isCustom: false,
      is_disabled: false,
      types: [],
      dates: [],
    };
  },
  validations: {
    create: {
      name: { required, minLength: minLength(2), maxLength: maxLength(100) },
      name_e: { required, minLength: minLength(2), maxLength: maxLength(100) },
      chart_id: { required },
      type: { required, minLength: minLength(2), maxLength: maxLength(100) },
    },
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
  },
  methods: {
    getCustomTableFields() {
      this.isCustom = true;
      adminApi
        .get(`/customTable/table-columns/rlst_expenses_items`)
        .then((res) => {
          this.fields = res.data;
        })
        .catch((err) => {
          this.errorFun("Error", "Thereisanerrorinthesystem");
        })
        .finally(() => {
          this.isCustom = false;
        });
    },
    isVisible(fieldName) {
      if (!this.isPage) {
        let res = this.fields.filter((field) => {
          return field.column_name == fieldName;
        });
        return res.length > 0 && res[0].is_visible == 1 ? true : false;
      } else {
        return this.isVisiblePage(fieldName);
      }
    },
    isRequired(fieldName) {
      if (!this.isPage) {
        let res = this.fields.filter((field) => {
          return field.column_name == fieldName;
        });
        return res.length > 0 && res[0].is_required == 1 ? true : false;
      } else {
        return this.isRequiredPage(fieldName);
      }
    },
    defaultData(edit = null) {
      this.create = { name: "", name_e: "", chart_id: null, type: "" };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.is_disabled = false;
    },
    resetModalHidden() {
      this.defaultData();
      this.$bvModal.hide(this.id);
    },
    resetModal() {
      this.defaultData();
      setTimeout(() => {
        if (this.type != "edit") {
          if (!this.isPage) this.getCustomTableFields();
          this.getAccounts();
        } else {
          if (this.idObjEdit.dataObj) {
            let expensesItems = this.idObjEdit.dataObj;
            this.errors = {};
            this.create.name = expensesItems.name;
            this.create.name_e = expensesItems.name_e;
            this.create.chart_id = expensesItems.chart.id;
            this.create.type = expensesItems.type;
            this.getAccounts();
          }
        }
      }, 50);
    },
    resetForm() {
      this.defaultData();
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
        if (this.type != "edit") {
          adminApi
            .post(this.url, { ...this.create, company_id: this.company_id })
            .then((res) => {
              this.is_disabled = true;
              if (!this.isPage) this.$emit("created");
              else this.$emit("getDataTable");

              setTimeout(() => {
                this.successFun("Addedsuccessfully");
              }, 500);
            })
            .catch((err) => {
              if (err.response.data) {
                this.errors = err.response.data.errors;
              } else {
                this.errorFun("Error", "Thereisanerrorinthesystem");
              }
            })
            .finally(() => {
              this.isLoader = false;
              this.resetModalHidden();
            });
        } else {
          adminApi
            .put(`${this.url}/${this.idObjEdit.idEdit}`, {
              ...this.create,
              company_id: this.$store.getters["auth/company_id"],
            })
            .then((res) => {
              this.$bvModal.hide(this.id);
              this.$emit("getDataTable");
              setTimeout(() => {
                this.successFun("Editsuccessfully");
              }, 500);
            })
            .catch((err) => {
              if (err.response.data) {
                this.errors = err.response.data.errors;
              } else {
                this.errorFun("Error", "Thereisanerrorinthesystem");
              }
            })
            .finally(() => {
              this.isLoader = false;
              this.resetModalHidden();
            });
        }
      }
    },
    async getAccounts() {
      await adminApi
        .get(`/general-accounts`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, account_number: "Add Account Number" });
          this.charts = l;
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
    showAccountModal() {
      if (this.create.chart_id == 0) {
        this.$bvModal.show("account-create");
        this.create.chart_id = null;
      }
    },
    arabicValue(txt) {
      this.create.name = arabicValue(txt);
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
    },
  },
};
</script>

<style scoped>
form {
  position: relative;
}
</style>
