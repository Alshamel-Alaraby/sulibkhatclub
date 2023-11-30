<template>
  <!--  create   -->
  <b-modal
    :id="id"
    :title="
      type != 'edit'
        ? getCompanyKey('services_create_form')
        : getCompanyKey('services_edit_form')
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
              {{ getCompanyKey("services_name_ar") }}
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
              {{ getCompanyKey("services_name_en") }}
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
    </form>
  </b-modal>
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
    url: { default: "/real-estate/services" },
  },
  data() {
    return {
      fields: [],
      isLoader: false,
      create: {
        name: "",
        name_e: "",
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
    },
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
  },
  methods: {
    getCustomTableFields() {
      this.isCustom = true;
      adminApi
        .get(`/customTable/table-columns/rlst_services`)
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
      this.create = { name: "", name_e: "" };
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
        } else {
          if (this.idObjEdit.dataObj) {
            let services = this.idObjEdit.dataObj;
            this.errors = {};
            this.create.name = services.name;
            this.create.name_e = services.name_e;
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
