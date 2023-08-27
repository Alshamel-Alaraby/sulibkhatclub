<script>
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {
  required,
  minLength,
  maxLength,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../general/loader";

import {
  dynamicSortString,
  dynamicSortNumber,
} from "../../../helper/tableSort";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import Multiselect from "vue-multiselect";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "View",
    meta: [{ name: "description", content: "View" }],
  },
    props: {
        companyKeys: {
            default: [],
        },
        defaultsKeys: {
            default: [],
        },
    },
  mixins: [transMixinComp],
  components: {
    Switches,
    ErrorMessage,
    Multiselect,
    loader,
  },
  data() {
    return {
      per_page: 50,
      search: "",
      debounce: {},
      enabled3: true,
      viewsPagination: {},
      views: [],
      isLoader: false,
      Tooltip: "",
      mouseEnter: "",
      fields: [],
      create: {
        name: "",
        name_e: "",
      },
      edit: {
        name: "",
        name_e: "",
      },
      company_id: null,
      errors: {},
      isCheckAll: false,
      checkAll: [],
      current_page: 1,
      setting: {
        name: true,
        name_e: true,
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
        required,
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      name_e: {
        required,
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
    },
  },

  methods: {


    resetModalHidden() {
      this.create = {
        name: "",
        name_e: "",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.$bvModal.hide(`create_unit_type`);
    },
    /**
     *  hidden Modal (create)
     */
    resetModal() {
      this.create = {
        name: "",
        name_e: "",
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
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.is_disabled = false;
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
          .post(`/real-estate/unit-type`, {...this.create,company_id: this.$store.getters["auth/company_id"],})
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
    /**
     *  edit countrie
     */

    /**
     *   show Modal (edit)
     */
    /**
     *  hidden Modal (edit)
     */

    /*
     *  start  dynamicSortString
     */
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
    arabicValue(txt) {
      this.create.name = arabicValue(txt);
      this.edit.name = arabicValue(txt);
    },

    englishValue(txt) {
      this.create.name_e = englishValue(txt);
      this.edit.name_e = englishValue(txt);
    },
  },
};
</script>

<template>
  <!--  create   -->
  <b-modal
    id="create_unit_type"
    :title="getCompanyKey('unit_type_create_form')"
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
        <div class="col-md-12">
          <div class="form-group">
            <label for="field-1" class="control-label">
              {{ getCompanyKey("unit_type_name_ar") }}
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
              {{ getCompanyKey("unit_type_name_en") }}
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
    </form>
  </b-modal>
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
