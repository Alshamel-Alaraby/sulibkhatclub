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
    <country
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getCountries"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
      :id="'country-create'"
    />

    <!--  create   -->
    <b-modal
      :id="id"
      :title="
        type != 'edit'
          ? getCompanyKey('tenants_create_form')
          : getCompanyKey('tenants_edit_form')
      "
      title-class="font-18"
      body-class="p-4 "
      :hide-footer="true"
      dialog-class="modal-lg"
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
              <label for="field-1" class="control-label">
                {{ getCompanyKey("tenants_name_ar") }}

                <span class="text-danger">*</span>
              </label>
              <div dir="rtl">
                <input
                  id="field-1"
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
                {{ getCompanyKey("tenants_name_en") }}
                <span class="text-danger">*</span>
              </label>
              <div dir="ltr">
                <input
                  @keyup="englishValue(create.name_e)"
                  id="field-2"
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
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("tenants_blacklisted") }}
                <span class="text-danger">*</span>
              </label>
              <div class="d-flex align-items-center">
                <b-form-radio
                  class="mr-3"
                  v-model="$v.create.blacklisted.$model"
                  name="blacklisted"
                  value="yes"
                >
                  {{ $t("general.Yes") }}
                </b-form-radio>
                <b-form-radio
                  v-model="$v.create.blacklisted.$model"
                  name="blacklisted"
                  value="no"
                >
                  {{ $t("general.No") }}
                </b-form-radio>
              </div>
              <template v-if="errors.blacklisted">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.blacklisted"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group position-relative">
              <label class="control-label">
                {{ getCompanyKey("tenants_nationality") }}
                <span class="text-danger">*</span>
              </label>
              <multiselect
                @input="showCountryModal"
                v-model="$v.create.nationality.$model"
                :options="countries.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    countries.find((x) => x.id == opt)
                      ? countries.find((x) => x.id == opt).name
                      : ''
                "
              >
              </multiselect>
              <div
                v-if="$v.create.nationality.$error || errors.nationality"
                class="text-danger"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.nationality">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.nationality"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="mr-2">
                {{ getCompanyKey("tenants_gender") }}
                <span v-if="isRequired('gender')" class="text-danger">*</span>
              </label>
              <b-form-group
                :class="{
                  'is-invalid': $v.create.gender.$error || errors.gender,
                  'is-valid': !$v.create.gender.$invalid && !errors.gender,
                }"
              >
                <b-form-radio
                  class="d-inline-block"
                  v-model="$v.create.gender.$model"
                  name="create_gender"
                  value="male"
                  >{{ $t("general.male") }}
                  <span class="text-danger">*</span>
                </b-form-radio>
                <b-form-radio
                  class="d-inline-block m-1"
                  v-model="$v.create.gender.$model"
                  name="create_gender"
                  value="female"
                  >{{ $t("general.female") }}
                </b-form-radio>
              </b-form-group>
              <template v-if="errors.gender">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.gender"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group position-relative">
              <label class="control-label">
                {{ getCompanyKey("tenants_marital_status") }}
                <span class="text-danger">*</span>
              </label>
              <multiselect
                v-model="$v.create.martialStatus.$model"
                :options="martialStatusOptions"
                :class="{
                  'is-invalid':
                    $v.create.martialStatus.$error || errors.martialStatus,
                  'is-valid':
                    !$v.create.martialStatus.$invalid && !errors.martialStatus,
                }"
              >
              </multiselect>
              <div
                v-if="$v.create.martialStatus.$error || errors.martialStatus"
                class="text-danger"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.martialStatus">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.martialStatus"
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
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("tenants_mobile") }}
                <span class="text-danger">*</span>
              </label>
              <VuePhoneNumberInput
                v-model="$v.create.mobile.$model"
                :default-country-code="codeCountry"
                valid-color="#28a745"
                error-color="#dc3545"
                :preferred-countries="['FR', 'EG', 'DE']"
                @update="updatePhone"
              />
              <template v-if="errors.mobile">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.mobile"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="mr-2">
                <!-- {{ getCompanyKey("employee_is_sms") }} -->
                Is WhatsApp same as Mobile ?
              </label>
              <b-form-group>
                <b-form-radio
                  class="d-inline-block"
                  v-model="$v.create.is_whatsapp.$model"
                  name="manage_others_some-radios3"
                  value="yes"
                  >{{ $t("general.Yes") }}
                </b-form-radio>
                <b-form-radio
                  class="d-inline-block m-1"
                  v-model="$v.create.is_whatsapp.$model"
                  name="manage_others_some-radios3"
                  value="no"
                  >{{ $t("general.No") }}
                </b-form-radio>
              </b-form-group>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("tenants_email") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="text"
                class="form-control"
                v-model="$v.create.email.$model"
                :class="{
                  'is-invalid': $v.create.email.$error || errors.email,
                  'is-valid': !$v.create.email.$invalid && !errors.email,
                }"
              />
              <template v-if="errors.email">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.email"
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
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("tenants_home_address") }}
                <span class="text-danger">*</span>
              </label>
              <input
                v-model="$v.create.home_address.$model"
                class="form-control"
                type="text"
                :class="{
                  'is-invalid':
                    $v.create.home_address.$error || errors.home_address,
                  'is-valid':
                    !$v.create.home_address.$invalid && !errors.home_address,
                }"
              />
              <template v-if="errors.home_address">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.home_address"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("tenants_work_address") }}
                <span class="text-danger">*</span>
              </label>
              <input
                v-model="$v.create.work_address.$model"
                class="form-control"
                type="text"
                :class="{
                  'is-invalid':
                    $v.create.work_address.$error || errors.work_address,
                  'is-valid':
                    !$v.create.work_address.$invalid && !errors.work_address,
                }"
              />
              <template v-if="errors.work_address">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.work_address"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("bank_account") }}
                <span class="text-danger">*</span>
              </label>
              <multiselect
                @input="showAccountModal"
                v-model="create.chart"
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
                  'is-invalid': $v.create.chart.$error || errors.chart,
                }"
              >
              </multiselect>
              <div v-if="!$v.create.chart.required" class="invalid-feedback">
                {{ $t("general.fieldIsRequired") }}
              </div>

              <template v-if="errors.chart">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.chart"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("tenants_civil_no") }}
                <span class="text-danger">*</span>
              </label>
              <input
                v-model="$v.create.civil_no.$model"
                class="form-control"
                type="text"
                :class="{
                  'is-invalid': $v.create.civil_no.$error || errors.civil_no,
                  'is-valid': !$v.create.civil_no.$invalid && !errors.civil_no,
                }"
              />
              <template v-if="errors.civil_no">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.civil_no"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("tenants_passport_no") }}
                <span class="text-danger">*</span>
              </label>
              <input
                v-model="$v.create.passport_no.$model"
                class="form-control"
                type="text"
                :class="{
                  'is-invalid':
                    $v.create.passport_no.$error || errors.passport_no,
                  'is-valid':
                    !$v.create.passport_no.$invalid && !errors.passport_no,
                }"
              />
              <template v-if="errors.passport_no">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.passport_no"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("tenants_residence_no") }}
                <span class="text-danger">*</span>
              </label>
              <input
                v-model="$v.create.residence_no.$model"
                class="form-control"
                type="text"
                :class="{
                  'is-invalid':
                    $v.create.residence_no.$error || errors.residence_no,
                  'is-valid':
                    !$v.create.residence_no.$invalid && !errors.residence_no,
                }"
              />
              <template v-if="errors.residence_no">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.residence_no"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
        </div>
      </form>
    </b-modal>
  </div>
  <!--  /create   -->
</template>
<script>
import adminApi from "../../../api/adminAxios";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import Multiselect from "vue-multiselect";
import Account from "../general/Accounts";
import Country from "../general/country";
import {
  required,
  minLength,
  maxLength,
  integer,
  numeric,
  decimal,
  email,
} from "vuelidate/lib/validators";

export default {
  name: "services",
  mixins: [transMixinComp, successError],
  components: {
    ErrorMessage,
    loader,
    Multiselect,
    Account,
    Country,
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
    url: { default: "/real-estate/tenants" },
  },
  data() {
    return {
      isValidMobile: false,
      fields: [],
      isLoader: false,
      codeCountry: "",
      martialStatusOptions: [
        "single",
        "married",
        "divorced",
        "widowed",
        "other",
      ],
      charts: [],
      countries: [],
      create: {
        name: "",
        name_e: "",
        blacklisted: "no",
        mobile: "",
        is_whatsapp: "no",
        email: "",
        gender: "",
        marital_status: "",
        work_address: "",
        home_address: "",
        nationality: null,
        civil_no: null,
        passport_no: null,
        residence_no: null,
        chart: null,
        tenantType: null,
        mobile_code: "",
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
      blacklisted: { required },
      mobile: { required, minLength: minLength(2), maxLength: maxLength(100) },
      is_whatsapp: { required },
      email: { required, email },
      gender: { required },
      martialStatus: { required },
      work_address: { required },
      home_address: { required },
      civil_no: { required },
      passport_no: { required },
      residence_no: { required },
      chart: { required },
      tenantType: { required },
      nationality: { required },
    },
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
  },
  methods: {
    getCustomTableFields() {
      this.isCustom = true;
      adminApi
        .get(`/customTable/table-columns/rlst_tenants`)
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
      this.codeCountry = this.$store.getters["locationIp/countryCode"];
      this.create = {
        name: "",
        name_e: "",
        blacklisted: "no",
        mobile: "",
        is_whatsapp: "",
        email: "",
        gender: "",
        martialStatus: "",
        work_address: "",
        home_address: "",
        civil_no: null,
        passport_no: null,
        residence_no: null,
        chart: null,
        tenantType: null,
        mobile_code: "",
        nationality: null,
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
      setTimeout(() => {
        if (this.type != "edit") {
          if (!this.isPage) this.getCustomTableFields();
          this.getAccounts();
          this.getCountries();
        } else {
          if (this.idObjEdit.dataObj) {
            let tenants = this.idObjEdit.dataObj;
            this.errors = {};
            this.create.name = tenants.name;
            this.create.name_e = tenants.name_e;
            this.create.blacklisted = tenants.blacklisted;
            this.create.mobile = tenants.mobile;
            this.create.is_whatsapp = tenants.is_whatsapp;
            this.create.email = tenants.email;
            this.create.gender = tenants.gender;
            this.create.martial_status = tenants.martialStatus;
            this.create.work_address = tenants.work_address;
            this.create.home_address = tenants.home_address;
            this.create.civil_no = tenants.civil_no;
            this.create.passport_no = tenants.passport_no;
            this.create.residence_no = tenants.residence_no;
            this.create.chart = tenants.chart.id;
            this.create.tenantType = tenants.tenantType.id;
            this.create.mobile_code = tenants.mobile_code;
            this.create.nationality = tenants.nationality.id;
            this.getAccounts();
            this.getCountries();
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

      if (this.$v.create.$invalid && !this.isValidMobile) {
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
    arabicValue(txt) {
      this.create.name = arabicValue(txt);
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
    },
    updatePhone(e) {
      this.create.mobile = e.phoneNumber;
      this.create.mobile_code = e.countryCallingCode;
      this.isValidMobile = e.isValid;
    },
    async getAccounts() {
      await adminApi
        .get(`/general-accounts`)
        .then((res) => {
          res.data.data.unshift({
            id: 0,
            name: "اضافة حساب",
            name_e: "Add Account",
          });
          this.charts = res.data.data;
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
      if (this.create.chart == 0) {
        this.$bvModal.show("account-create");
        this.create.chart = null;
      }
    },
    async getCountries() {
      await adminApi
        .get(`/countries/get-drop-down?is_active=active`)
        .then((res) => {
          res.data.data.unshift({
            id: 0,
            name: "اضافة دولة",
            name_e: "Add Country",
          });
          this.countries = res.data.data;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    showCountryModal() {
      if (this.create.nationality == 0) {
        this.$bvModal.show("country-create");
        this.create.nationality = null;
      }
    },
  },
};
</script>

<style scoped>
form {
  position: relative;
}
</style>
