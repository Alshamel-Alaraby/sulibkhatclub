<template>
  <div>
    <Building
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getBuildings"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
      :id="'building-create'"
    />
    <!-- <Policy
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getPolicies"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
      :id="'policy-create'"
    /> -->

    <!--  create   -->
    <b-modal
      id="create"
      :title="
        type != 'edit'
          ? getCompanyKey('building_policy_create_form')
          : getCompanyKey('building_policy_form')
      "
      dialog-class="modal-lg"
      title-class="font-18"
      body-class="p-4"
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
          <div class="col-md-12 position-relative">
            <div class="form-group">
              <label class="my-1 mr-2">{{ getCompanyKey("building") }}</label>
              <multiselect
                @input="showBuildingModal"
                v-model="create.building_id"
                :options="buildings.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    buildings.find((x) => x.id == opt)
                      ? $i18n.locale == 'ar'
                        ? buildings.find((x) => x.id == opt).name
                        : buildings.find((x) => x.id == opt).name_e
                      : ''
                "
                :class="{
                  'is-invalid':
                    $v.create.building_id.$error || errors.building_id,
                }"
              >
              </multiselect>
              <div
                v-if="!$v.create.building_id.required"
                class="invalid-feedback"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>

              <template v-if="errors.building_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.building_id"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-12 position-relative">
            <div class="form-group">
              <label class="my-1 mr-2">{{ $t("general.policy") }}</label>
              <multiselect
                v-model="create.policy_id"
                :options="policies.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    policies.find((x) => x.id == opt)
                      ? $i18n.locale == 'ar'
                        ? policies.find((x) => x.id == opt).name
                        : policies.find((x) => x.id == opt).name_e
                      : ''
                "
                :class="{
                  'is-invalid': $v.create.policy_id.$error || errors.policy_id,
                }"
              >
              </multiselect>
              <div
                v-if="!$v.create.policy_id.required"
                class="invalid-feedback"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.policy_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.policy_id"
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
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">
                {{ $t("general.Year") }}
                <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <input
                  type="number"
                  class="form-control"
                  v-model="$v.create.year.$model"
                  :class="{
                    'is-invalid': $v.create.year.$error || errors.year,
                    'is-valid': !$v.create.year.$invalid && !errors.year,
                  }"
                  placeholder="YYYY"
                  data-provide="datepicker"
                  data-date-format="yyyy"
                  data-date-start-view="years"
                  data-date-min-view-mode="years"
                  data-date-autoclose="true"
                  data-date-min-view="years"
                />
                <div class="input-group-append">
                  <span class="input-group-text">
                    <i class="far fa-calendar"></i>
                  </span>
                </div>
              </div>
              <template v-if="errors.year">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.year"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">
                {{ $t("general.month") }}
                <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <input
                  type="number"
                  class="form-control"
                  v-model="$v.create.month.$model"
                  :class="{
                    'is-invalid': $v.create.month.$error || errors.month,
                    'is-valid': !$v.create.month.$invalid && !errors.month,
                  }"
                  placeholder="MM"
                  data-provide="datepicker"
                  data-date-format="mm"
                  data-date-start-view="months"
                  data-date-min-view-mode="months"
                  data-date-autoclose="true"
                  data-date-min-view="months"
                />
                <div class="input-group-append">
                  <span class="input-group-text">
                    <i class="far fa-calendar"></i>
                  </span>
                </div>
              </div>
              <template v-if="errors.month">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.month"
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
          <div class="col-md-4" v-if="showAmountInput">
            <div class="form-group">
              <label class="control-label">
                {{ $t("general.amount") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="number"
                class="form-control"
                step="0.1"
                v-model="$v.create.amount.$model"
                :class="{
                  'is-invalid': $v.create.amount.$error || errors.amount,
                  'is-valid': !$v.create.amount.$invalid && !errors.amount,
                }"
              />
              <template v-if="errors.amount">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.amount"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>

          <div class="col-md-4" v-if="showPercentInput">
            <div class="form-group">
              <label class="control-label">
                {{ $t("general.percent") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="number"
                class="form-control"
                step="0.1"
                v-model="$v.create.percent.$model"
                :class="{
                  'is-invalid': $v.create.percent.$error || errors.percent,
                  'is-valid': !$v.create.percent.$invalid && !errors.percent,
                }"
              />
              <template v-if="errors.percent">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.percent"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>

          <div class="col-md-4" v-if="showPercentAmountInput">
            <div class="form-group">
              <label class="control-label">
                {{ $t("general.percent_amount") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="number"
                class="form-control"
                step="0.1"
                v-model="$v.create.percent_amount.$model"
                :class="{
                  'is-invalid':
                    $v.create.percent_amount.$error || errors.percent_amount,
                  'is-valid':
                    !$v.create.percent_amount.$invalid &&
                    !errors.percent_amount,
                }"
              />
              <template v-if="errors.percent_amount">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.percent_amount"
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
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">
                {{ $t("general.after_expenses") }}
                <span class="text-danger">*</span>
              </label>
              <b-form-group
                :class="{
                  'is-invalid':
                    $v.create.after_expenses.$error || errors.after_expenses,
                  'is-valid':
                    !$v.create.after_expenses.$invalid &&
                    !errors.after_expenses,
                }"
              >
                <b-form-radio
                  class="d-inline-block"
                  v-model="$v.create.after_expenses.$model"
                  name="after_expenses"
                  value="yes"
                  >{{ $t("general.Yes") }}</b-form-radio
                >
                <b-form-radio
                  class="d-inline-block m-1"
                  v-model="$v.create.after_expenses.$model"
                  name="after_expenses"
                  value="no"
                  >{{ $t("general.No") }}</b-form-radio
                >
              </b-form-group>
              <template v-if="errors.after_expenses">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.after_expenses"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">
                {{ $t("general.plus_extra_revenues") }}
                <span class="text-danger">*</span>
              </label>
              <b-form-group
                :class="{
                  'is-invalid':
                    $v.create.plus_extra_revenues.$error ||
                    errors.plus_extra_revenues,
                  'is-valid':
                    !$v.create.plus_extra_revenues.$invalid &&
                    !errors.plus_extra_revenues,
                }"
              >
                <b-form-radio
                  class="d-inline-block"
                  v-model="$v.create.plus_extra_revenues.$model"
                  name="plus_extra_revenues"
                  value="yes"
                  >{{ $t("general.Yes") }}</b-form-radio
                >
                <b-form-radio
                  class="d-inline-block m-1"
                  v-model="$v.create.plus_extra_revenues.$model"
                  name="plus_extra_revenues"
                  value="no"
                  >{{ $t("general.No") }}</b-form-radio
                >
              </b-form-group>
              <template v-if="errors.plus_extra_revenues">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.plus_extra_revenues"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">
                {{ $t("general.collected_rent_type") }}
                <span class="text-danger">*</span>
              </label>
              <b-form-group
                :class="{
                  'is-invalid':
                    $v.create.collected_rent_type.$error ||
                    errors.collected_rent_type,
                  'is-valid':
                    !$v.create.collected_rent_type.$invalid &&
                    !errors.collected_rent_type,
                }"
              >
                <b-form-radio
                  class="d-inline-block"
                  v-model="$v.create.collected_rent_type.$model"
                  name="collected_rent_type"
                  value="actual"
                  >{{ $t("general.Actual") }}</b-form-radio
                >
                <b-form-radio
                  class="d-inline-block m-1"
                  v-model="$v.create.collected_rent_type.$model"
                  name="collected_rent_type"
                  value="accrued"
                  >{{ $t("general.Accrued") }}</b-form-radio
                >
              </b-form-group>
              <template v-if="errors.collected_rent_type">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.collected_rent_type"
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
          <div class="col-md-5" v-if="showCompanyPaysInput">
            <div class="form-group">
              <label class="control-label">
                {{ $t("general.company_pays_rest") }}
                <span class="text-danger">*</span>
              </label>
              <b-form-group
                :class="{
                  'is-invalid':
                    $v.create.company_pays_rest.$error ||
                    errors.company_pays_rest,
                  'is-valid':
                    !$v.create.company_pays_rest.$invalid &&
                    !errors.company_pays_rest,
                }"
              >
                <b-form-radio
                  class="d-inline-block"
                  v-model="$v.create.company_pays_rest.$model"
                  name="company_pays_rest"
                  value="yes"
                  >{{ $t("general.Yes") }}</b-form-radio
                >
                <b-form-radio
                  class="d-inline-block m-1"
                  v-model="$v.create.company_pays_rest.$model"
                  name="company_pays_rest"
                  value="no"
                  >{{ $t("general.No") }}</b-form-radio
                >
              </b-form-group>
              <template v-if="errors.company_pays_rest">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.company_pays_rest"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>

          <div class="col-md-5" v-if="showOwnerPaysInput">
            <div class="form-group">
              <label class="control-label">
                {{ $t("general.owner_pays_rest") }}
                <span class="text-danger">*</span>
              </label>
              <b-form-group
                :class="{
                  'is-invalid':
                    $v.create.owner_pays_rest.$error || errors.owner_pays_rest,
                  'is-valid':
                    !$v.create.owner_pays_rest.$invalid &&
                    !errors.owner_pays_rest,
                }"
              >
                <b-form-radio
                  class="d-inline-block"
                  v-model="$v.create.owner_pays_rest.$model"
                  name="owner_pays_rest"
                  value="yes"
                  >{{ $t("general.Yes") }}</b-form-radio
                >
                <b-form-radio
                  class="d-inline-block m-1"
                  v-model="$v.create.owner_pays_rest.$model"
                  name="owner_pays_rest"
                  value="no"
                  >{{ $t("general.No") }}</b-form-radio
                >
              </b-form-group>
              <template v-if="errors.owner_pays_rest">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.owner_pays_rest"
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
import { required, numeric } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import Building from "./building";
// import policy from "./policy";
import Multiselect from "vue-multiselect";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

/**
 * Advanced Table component
 */
export default {
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
    url: { default: "/real-estate/building-policy" },
  },
  components: {
    ErrorMessage,
    loader,
    Multiselect,
    Building,
    // policy,
  },
  data() {
    return {
      showAmountInput: false,
      showPercentInput: false,
      showPercentAmountInput: false,
      showCompanyPaysInput: false,
      showOwnerPaysInput: false,
      policies: [],
      buildings: [],
      isLoader: false,
      isCustom: false,
      create: {
        policy_id: null,
        building_id: null,
        year: null,
        month: null,
        amount: null,
        percent: null,
        percent_amount: "",
        after_expenses: "",
        plus_extra_revenues: "",
        collected_rent_type: "",
        company_pays_rest: "",
        owner_pays_rest: "",
      },
      errors: {},
      is_disabled: false,
      current_page: 1,
      company_id: 48,
    };
  },
  validations: {
    create: {
      building_id: { required: true },
      policy_id: { required: true },
      year: { required: true },
      month: { required: true },
      amount: { numeric: true },
      percent: { numeric: true },
      percent_amount: { numeric: true },
      after_expenses: { required: true },
      plus_extra_revenues: { required: true },
      collected_rent_type: { required: true },
      company_pays_rest: { string: true },
      owner_pays_rest: { string: true },
    },
  },
  computed: {
    selectedPolicyId() {
      return this.create.policy_id;
    },
  },
  watch: {
    selectedPolicyId: function (newPolicyId) {
      this.showAmountInput = [1, 2, 4, 5].includes(newPolicyId);
      this.showPercentInput = [2, 3, 5].includes(newPolicyId);
      this.showPercentAmountInput = [5].includes(newPolicyId);

      if (newPolicyId === 1 || newPolicyId === 4) {
        this.create.after_expenses = "no";
        this.create.plus_extra_revenues = "no";
        this.create.collected_rent_type = "accrued";
      } else {
        this.create.after_expenses = "yes";
        this.create.plus_extra_revenues = "yes";
        this.create.collected_rent_type = "actual";
      }
      this.showCompanyPaysInput = [1].includes(newPolicyId);
      this.showOwnerPaysInput = [2, 4, 5].includes(newPolicyId);
    },
  },
  methods: {
    async getCustomTableFields() {
      this.isCustom = true;
      await adminApi
        .get(`/customTable/table-columns/rlst_building_policies`)
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
    showBuildingModal() {
      if (this.create.building_id == 0) {
        this.$bvModal.show("building-create");
        this.create.building_id = null;
      }
    },
    showPolicyModal() {
      if (this.create.policy_id == 0) {
        this.$bvModal.show("policy-create");
        this.create.policy_id = null;
      }
    },
    defaultData(edit = null) {
      this.create = {
        building_id: null,
        policy_id: null,
        year: null,
        month: null,
        amount: null,
        percent: null,
        percent_amount: null,
        after_expenses: "",
        plus_extra_revenues: "",
        collected_rent_type: "",
        company_pays_rest: "",
        owner_pays_rest: "",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_disabled = false;

      this.errors = {};
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
          this.getBuildings();
          this.getPolicies();
        } else {
          if (this.idObjEdit.dataObj) {
            let buildingPolicy = this.idObjEdit.dataObj;
            this.errors = {};
            this.create.building_id = buildingPolicy.building.id;
            this.create.policy_id = buildingPolicy.policy.id;
            this.create.year = buildingPolicy.year;
            this.create.month = buildingPolicy.month;
            this.create.amount = buildingPolicy.amount;
            this.create.percent = buildingPolicy.percent;
            this.create.percent_amount = buildingPolicy.percent_amount;
            this.create.after_expenses = buildingPolicy.after_expenses;
            this.create.plus_extra_revenues =
              buildingPolicy.plus_extra_revenues;
            this.create.collected_rent_type =
              buildingPolicy.collected_rent_type;
            this.create.company_pays_rest = buildingPolicy.company_pays_rest;
            this.create.owner_pays_rest = buildingPolicy.owner_pays_rest;
            this.getBuildings();
            this.getPolicies();
          }
        }
      }, 50);
    },

    resetForm() {
      this.defaultData();
    },
    AddSubmit() {
      if (this.create.amount === null) {
        delete this.create.amount;
      }
      if (this.create.percent === null) {
        delete this.create.percent;
      }
      if (this.create.percent_amount === null) {
        delete this.create.percent_amount;
      }
      if (
        this.create.company_pays_rest === null ||
        this.create.company_pays_rest === ""
      ) {
        delete this.create.company_pays_rest;
      }
      if (
        this.create.owner_pays_rest === null ||
        this.create.owner_pays_rest === ""
      ) {
        delete this.create.owner_pays_rest;
      }

      if (this.$v.create.$invalid) {
        this.$v.create.$touch();
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;

        const buildingPolicyData = [
          {
            building_id: this.create.building_id,
            policy_id: this.create.policy_id,
            year: this.create.year,
            month: this.create.month,
            amount: this.create.amount,
            percent: this.create.percent,
            percent_amount: this.create.percent_amount,
            after_expenses: this.create.after_expenses,
            plus_extra_revenues: this.create.plus_extra_revenues,
            collected_rent_type: this.create.collected_rent_type,
            company_pays_rest: this.create.company_pays_rest,
            owner_pays_rest: this.create.owner_pays_rest,
          },
        ];

        if (this.type != "edit") {
          adminApi
            .post(this.url, {
              "building-policy": buildingPolicyData,
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
              "building-policy": buildingPolicyData,
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

    async getBuildings() {
      this.isLoader = true;
      await adminApi
        .get(`/real-estate/buildings`)
        .then((res) => {
          this.isLoader = false;
          let l = res.data.data;
          if (this.isPermission("create building RealState")) {
            l.unshift({ id: 0, name: "اضافة مبنى", name_e: "Add building" });
          }
          this.buildings = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },

    async getPolicies() {
      this.isLoader = true;
      await adminApi
        .get(`/real-estate/policy`)
        .then((res) => {
          this.isLoader = false;
          let l = res.data.data;
          if (this.isPermission("create policy RealState")) {
            l.unshift({ id: 0, name: "اضافة سياسة", name_e: "Add Policy" });
          }
          this.policies = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
  },
};
</script>


