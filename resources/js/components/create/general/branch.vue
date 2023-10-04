<template>
  <!--  create   -->
  <b-modal
    :id="id"
    :title="type != 'edit'?getCompanyKey('branch_create_form'):getCompanyKey('branch_edit_form')"
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
              {{ type != 'edit'? $t("general.Add"): $t("general.edit") }}
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
        <div v-if="isVisible('name')" class="col-md-12">
          <div class="form-group">
            <label for="field-1" class="control-label">
              {{ getCompanyKey("branch_name_ar") }}
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
                >{{ errorMessage }}</ErrorMessage
              >
            </template>
          </div>
        </div>
        <div v-if="isVisible('name_e')" class="col-md-12">
          <div class="form-group">
            <label for="field-2" class="control-label">
              {{ getCompanyKey("branch_name_en") }}
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
                >{{ errorMessage }}</ErrorMessage
              >
            </template>
          </div>
        </div>
        <div class="col-md-12 position-relative" v-if="isVisible('parent_id')">
          <div class="form-group">
            <label>
                {{ getCompanyKey("branch_parent") }}
            </label>
            <multiselect
              v-model="create.parent_id"
              :options="parent.map((type) => type.id)"
              :custom-label="
                (opt) => parent.find((x) => x.id == opt)?
                  $i18n.locale == 'ar'
                    ? parent.find((x) => x.id == opt).name
                    : parent.find((x) => x.id == opt).name_e: null
              "
              :class="{ 'is-invalid': errors.parent_id }"
            >
            </multiselect>

            <template v-if="errors.parent_id">
              <ErrorMessage
                v-for="(errorMessage, index) in errors.parent_id"
                :key="index"
                >{{ errorMessage }}
              </ErrorMessage>
            </template>
          </div>
        </div>
        <div v-if="isVisible('is_active')" class="col-md-12">
          <div class="form-group">
            <label class="mr-2">
              {{ getCompanyKey("branch_status") }}
              <span v-if="isRequired('is_active')" class="text-danger">*</span>
            </label>
            <b-form-group
              :class="{
                'is-invalid': $v.create.is_active.$error || errors.is_active,
                'is-valid': !$v.create.is_active.$invalid && !errors.is_active,
              }"
            >
              <b-form-radio
                class="d-inline-block"
                v-model="$v.create.is_active.$model"
                name="some-radios"
                :value="1"
                >{{ $t("general.Active") }}</b-form-radio
              >
              <b-form-radio
                class="d-inline-block m-1"
                v-model="$v.create.is_active.$model"
                name="some-radios"
                :value="0"
                >{{ $t("general.Inactive") }}</b-form-radio
              >
            </b-form-group>
            <template v-if="errors.is_active">
              <ErrorMessage
                v-for="(errorMessage, index) in errors.is_active"
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
import {
  maxLength,
  minLength,
  requiredIf,
} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import ErrorMessage from "../../widgets/errorMessage";
import Multiselect from "vue-multiselect";
import loader from "../../general/loader";
import successError from "../../../helper/mixin/success&error";

export default {
  name: "branch",
  mixins: [transMixinComp,successError],
  props: {
      id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
      isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
      type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/branches'}
  },
  mounted() {
      this.company_id = this.$store.getters["auth/company_id"];
  },
  components: {
    ErrorMessage,
    Multiselect,
    loader,
  },
  data() {
    return {
      fields: [],
      parent: [],
      errors: {},
      isCustom: false,
      is_disabled: false,
      isLoader: false,
      create: {
        name: "",
        name_e: "",
        is_active: 1,
        parent_id: null,
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
      is_active: {
        required: requiredIf(function (model) {
          return this.isRequired("is_active");
        }),
      },
    },
  },
  methods: {
    getBranch() {
      this.isLoader = true;

       adminApi
        .get(`/branches/get-drop-down?company_id=${this.company_id}`)
        .then((res) => {
          let l = res.data.data;
          this.parent = l;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    async getCustomTableFields() {
        this.isCustom = true;
        await adminApi
        .get(`/customTable/table-columns/general_branches`)
        .then((res) => {
            this.fields = res.data;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
        })
        .finally(() => {
          this.isCustom = false;
        });
    },
    isVisible(fieldName) {
          if(!this.isPage){
              let res = this.fields.filter((field) => {
                  return field.column_name == fieldName;
              });
              return res.length > 0 && res[0].is_visible == 1 ? true : false;
          }else {
              return this.isVisiblePage(fieldName);
          }
      },
    isRequired(fieldName) {
          if(!this.isPage) {
              let res = this.fields.filter((field) => {
                  return field.column_name == fieldName;
              });
              return res.length > 0 && res[0].is_required == 1 ? true : false;
          }else {
              return this.isRequiredPage(fieldName);
          }
      },
    defaultData(edit = null){
          this.create = {
              name: "",
              name_e: "",
              is_active: 1,
              parent_id: null,
          };
          this.errors = {};
          this.is_disabled = false;
          this.$nextTick(() => {
              this.$v.$reset();
          });
      },
    resetModalHidden() {
      this.defaultData();
      this.$bvModal.hide(this.id);
    },
    resetModal() {
      this.defaultData();
      setTimeout(async () => {
            if(this.type != 'edit'){
                if(!this.isPage) await this.getCustomTableFields();
                this.$nextTick(() => {this.$v.$reset();});
                if (this.isVisible("parent_id")) this.getBranch();
            }else {
                if(this.idObjEdit.dataObj){
                    let branch = this.idObjEdit.dataObj;
                    this.errors = {};
                    this.create.name = branch.name;
                    this.create.name_e = branch.name_e;
                    this.create.is_active = branch.is_active;
                    if (this.isVisible("parent_id")) this.getBranch();
                    this.create.parent_id = branch.parent_id;
                }
            }
        },50);
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
        if(this.type != 'edit'){
              adminApi
                  .post(this.url, {
                      ...this.create,
                      company_id: this.$store.getters["auth/company_id"]
                  })
                  .then((res) => {
                      this.is_disabled = true;
                      if(!this.isPage)
                          this.$emit("created");
                      else
                          this.$emit("getDataTable");

                      setTimeout(() => {
                          this.successFun('Addedsuccessfully');
                      }, 500);
                  })
                  .catch((err) => {
                      if (err.response.data) {
                          this.errors = err.response.data.errors;
                      } else {
                          this.errorFun('Error','Thereisanerrorinthesystem');
                      }
                  })
                  .finally(() => {
                      this.isLoader = false;
                  });
          }else {
            adminApi
                .put(`${this.url}/${this.idObjEdit.idEdit}`, {
                    ...this.create,
                    company_id: this.$store.getters["auth/company_id"],
                })
                .then((res) => {
                    this.$bvModal.hide(this.id);
                    this.$emit("getDataTable");
                    setTimeout(() => {
                        this.successFun('Editsuccessfully');
                    }, 500);
                })
                .catch((err) => {
                    if (err.response.data) {
                        this.errors = err.response.data.errors;
                    } else {
                        this.errorFun('Error','Thereisanerrorinthesystem');
                    }
                })
                .finally(() => {
                    this.isLoader = false;
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

<style>
form {
    position: relative;
}
</style>
