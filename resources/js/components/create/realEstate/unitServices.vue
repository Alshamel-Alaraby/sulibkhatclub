<template>
  <div>
    <Unit
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getUnits"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
      :id="'unit-create'"
    />
    <Service
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getServices"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
      :id="'service-create'"
    />

    <!--  create   -->
    <b-modal
      id="create"
      :title="
        type != 'edit'
          ? getCompanyKey('unit_service_create_form')
          : getCompanyKey('unit_service_edit_form')
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
              <label class="my-1 mr-2">{{ getCompanyKey("unit") }}</label>
              <span class="text-danger">*</span>
              <multiselect
                :multiple="true"
                @input="showUnitModal"
                v-model="create.unit_ids"
                :options="units.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    units.find((x) => x.id == opt)
                      ? $i18n.locale == 'ar'
                        ? units.find((x) => x.id == opt).name
                        : units.find((x) => x.id == opt).name_e
                      : ''
                "
                :class="{
                  'is-invalid': $v.create.unit_ids.$error || errors.unit_ids,
                }"
              >
              </multiselect>
              <div v-if="!$v.create.unit_ids.required" class="invalid-feedback">
                {{ $t("general.fieldIsRequired") }}
              </div>

              <template v-if="errors.unit_ids">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.unit_ids"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-12 position-relative">
            <div class="form-group">
              <label class="my-1 mr-2">{{ getCompanyKey("service") }}</label>
              <span class="text-danger">*</span>
              <multiselect
                @input="showServiceModal"
                :multiple="true"
                v-model="create.service_ids"
                :options="services.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    services.find((x) => x.id == opt)
                      ? $i18n.locale == 'ar'
                        ? services.find((x) => x.id == opt).name
                        : services.find((x) => x.id == opt).name_e
                      : ''
                "
                :class="{
                  'is-invalid':
                    $v.create.service_ids.$error || errors.service_ids,
                }"
              >
              </multiselect>
              <div
                v-if="!$v.create.service_ids.required"
                class="invalid-feedback"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.service_ids">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.service_ids"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">
                <label class="my-1 mr-2">{{
                  getCompanyKey("unitServices_price")
                }}</label>
                <span class="text-danger">*</span>
              </label>
              <input
                type="number"
                class="form-control"
                v-model="$v.create.price.$model"
                :class="{
                  'is-invalid': $v.create.price.$error || errors.price,
                  'is-valid': !$v.create.price.$invalid && !errors.price,
                }"
              />
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
import Unit from "./unit";
import Service from "./services";
import Multiselect from "vue-multiselect";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Unit Services",
    meta: [{ name: "description", content: "Unit Services" }],
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
    url: { default: "/real-estate/unit-service" },
  },
  components: {
    ErrorMessage,
    loader,
    Multiselect,
    Unit,
    Service,
  },
  data() {
    return {
      services: [],
      units: [],
      isLoader: false,
      isCustom: false,
      create: {
        service_ids: [],
        unit_ids: [],
        price: 0,
      },
      errors: {},
      is_disabled: false,
      current_page: 1,
      company_id: 48,
    };
  },
  validations: {
    create: {
      service_ids: { required: true },
      unit_ids: { required: true },
      price: { required: true, numeric },
    },
  },
  methods: {
    async getCustomTableFields() {
      this.isCustom = true;
      await adminApi
        .get(`/customTable/table-columns/rlst_unit_services`)
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
    showUnitModal() {
      if (this.create.unit_ids == 0) {
        this.$bvModal.show("unit-create");
        this.create.unit_ids = null;
      }
    },
    showServiceModal() {
      if (this.create.service_ids == 0) {
        this.$bvModal.show("service-create");
        this.create.service_ids = null;
      }
    },
    defaultData(edit = null) {
      this.create = {
        unit_ids: [],
        service_ids: [],
        price: 0,
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
          this.getUnits();
          this.getServices();
        } else {
          if (this.idObjEdit.dataObj) {
            let unitService = this.idObjEdit.dataObj;
            this.errors = {};
            this.create.unit_ids = unitService.unit.id;
            this.create.service_ids = unitService.service.id;
            this.create.price = unitService.price;
            this.getUnits();
            this.getServices();
          }
        }
      }, 50);
    },

    resetForm() {
      this.defaultData();
    },
    AddSubmit() {
      if (this.$v.create.$invalid) {
        this.$v.create.$touch();
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;
        console.log("this.create", this.create);
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

    async getUnits() {
      this.isLoader = true;
      await adminApi
        .get(`/real-estate/units`)
        .then((res) => {
          this.isLoader = false;
          let l = res.data.data;
          if (this.isPermission("create unit RealState")) {
            l.unshift({ id: 0, name: "اضافة وحده", name_e: "Add unit" });
          }
          this.units = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },

    async getServices() {
      this.isLoader = true;
      await adminApi
        .get(`/real-estate/services`)
        .then((res) => {
          this.isLoader = false;
          let l = res.data.data;
          if (this.isPermission("create services")) {
            l.unshift({ id: 0, name: "اضافة خدمه", name_e: "Add service" });
          }
          this.services = l;
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


