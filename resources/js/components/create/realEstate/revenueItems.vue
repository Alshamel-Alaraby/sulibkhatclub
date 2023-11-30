<script>
import adminApi from "../../../api/adminAxios";
import {
  required,
  minLength,
  maxLength,
  minValue,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import successError from "../../../helper/mixin/success&error";
import Account from "../general/Accounts.vue";
/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Revenues Items",
    meta: [{ name: "description", content: "Revenues Items" }],
  },
  mixins: [transMixinComp, successError],
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
    url: { default: "/real-estate/revenues-items" },
  },
  components: {
    ErrorMessage,
    Multiselect,
    loader,
    Account,
  },
  data() {
    return {
      typeOptions: ["building", "unit", "building/unit"],
      isLoader: false,
      isCustom: false,
      charts: [],
      create: {
        name: "",
        name_e: "",
        chart_id: null,
        type: "",
      },
      company_id: null,
      errors: {},
      is_disabled: false,
    };
  },
  validations: {
    create: {
      name: {
        required,
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      name_e: {
        required,
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      chart_id: { required: true },
      type: {
        required: true,
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
    },
  },
  methods: {
    async getCustomTableFields() {
      this.isCustom = true;
      await adminApi
        .get(`/customTable/table-columns/rlst_revenues_items`)
        .then((res) => {
          this.fields = res.data;
          console.log("fields", this.fields);
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
      this.create = {
        name: "",
        name_e: "",
        chart_id: null,
        type: "",
      };
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
      setTimeout(async () => {
        if (this.type != "edit") {
          if (!this.isPage) await this.getCustomTableFields();
          this.$nextTick(() => {
            this.$v.$reset();
          });
          this.getAccounts();
        } else {
          if (this.idObjEdit.dataObj) {
            let revenuesItems = this.idObjEdit.dataObj;
            this.create.name = revenuesItems.name;
            this.create.name_e = revenuesItems.name_e;
            this.create.chart_id = revenuesItems.chart.id;
            this.create.type = revenuesItems.type;
            this.errors = {};
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
        this.is_disabled = false;
        if (this.type != "edit") {
          adminApi
            .post(this.url, {
              ...this.create,
              company_id: this.$store.getters["auth/company_id"],
            })
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
    moveInput(tag, c, index) {
      document.querySelector(`${tag}[data-${c}='${index}']`).focus();
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
          ? getCompanyKey('revenuesItems_create_form')
          : getCompanyKey('revenuesItems_edit_form')
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
              type="submit"
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
          <b-button
            @click.prevent="resetModalHidden"
            variant="danger"
            type="button"
          >
            {{ $t("general.Cancel") }}
          </b-button>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="field-1" class="control-label">
                {{ getCompanyKey("revenueItems_name_ar") }}
                <span class="text-danger">*</span>
              </label>
              <div dir="rtl">
                <input
                  type="text"
                  class="form-control arabicInput"
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
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label for="field-2" class="control-label">
                {{ getCompanyKey("revenueItems_name_en") }}
                <span class="text-danger">*</span>
              </label>
              <div dir="ltr">
                <input
                  type="text"
                  class="form-control englishInput"
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
                getCompanyKey("revenueItems_bank_account_number")
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
                {{ getCompanyKey("revenueItems_type") }}
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
  </div>
  <!--  /create   -->
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
</style>
