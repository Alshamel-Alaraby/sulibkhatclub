<script>
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import { required, minLength, maxLength, integer } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import { dynamicSortString } from "../../../helper/tableSort";
import { formatDateOnly, formatDateTime } from "../../../helper/startDate";
import transMixinComp from "../../../helper/translation-comp-mixin";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";
import InstallmentPaymentType from "./installmentPaymentType.vue";
import {arabicValue, englishValue} from "../../../helper/langTransform";
/**
 * Advanced Table component
 */
export default {
  mixins: [transMixinComp],
  components: {
    Switches,
    ErrorMessage,
    loader,
    DatePicker,
    Multiselect,
    InstallmentPaymentType,
  },
  data() {
    return {
      debounce: {},
      enabled3: false,
      isLoader: false,
      create: {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        is_default: 1,
        is_active: "active",
      },
      errors: {},
      dropDownSenders: [],
      is_disabled: false,
      isCheckAll: false,
      checkAll: [],
    };
  },
  validations: {
    create: {
      name: { required, minLength: minLength(3), maxLength: maxLength(100) },
      name_e: { required, minLength: minLength(3), maxLength: maxLength(100) },
      description: { maxLength: maxLength(1000) },
      description_e: { maxLength: maxLength(1000) },
      is_default: { required },
      is_active: { required },
    },
  },
 props:["companyKeys","defaultsKeys"],
  methods: {
    resetModalHidden() {
      this.create = {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        is_default: 1,
        is_active: "active",
      };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.$bvModal.hide(`installment-payment-plan-create`);
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      this.create = {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        is_default: 1,
        is_active: "active",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create module
     */
    resetForm() {
      this.create = {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        is_default: 1,
        is_active: "active",
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
          .post(`/recievable-payable/rp_installment_p_plan`, {
            ...this.create,
            is_active: this.create.is_active == "active" ? 1 : 0,
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
      arabicValueDescription(txt){
          this.create.description = arabicValue(txt);
          this.edit.description = arabicValue(txt);
      },
      englishValueDescription(txt){
          this.create.description_e = englishValue(txt);
          this.edit.description_e = englishValue(txt);
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

<template>
  <div>
      <!--  create   -->
      <b-modal
          id="installment-payment-plan-create"
          :title="getCompanyKey('installment_payment_plan_create')"
          title-class="font-18"
          body-class="p-4 "
          size="lg"
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
                  <div class="col-md-6">
                      <div class="form-group">
                          <label for="field-1" class="control-label">
                              {{ getCompanyKey("installment_payment_name_ar") }}
                              <span class="text-danger">*</span>
                          </label>
                          <div dir="rtl">
                              <input
                                  type="text"
                                  class="form-control"
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
                              >
                                  {{ errorMessage }}
                              </ErrorMessage>
                          </template>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                          <label for="field-2" class="control-label">
                              {{ getCompanyKey("installment_payment_name_en") }}
                              <span class="text-danger">*</span>
                          </label>
                          <div>
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
                              >{{ errorMessage }}
                              </ErrorMessage>
                          </template>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                          <label class="mr-2">
                              {{ getCompanyKey("is_default") }}
                              <span class="text-danger">*</span>
                          </label>
                          <b-form-group
                              :class="{
                          'is-invalid': $v.create.is_default.$error || errors.is_default,
                          'is-valid':
                            !$v.create.is_default.$invalid && !errors.is_default,
                        }"
                          >
                              <b-form-radio
                                  class="d-inline-block"
                                  v-model="$v.create.is_default.$model"
                                  name="some-radios__create"
                                  value="1"
                              >{{ $t("general.Yes") }}</b-form-radio
                              >
                              <b-form-radio
                                  class="d-inline-block m-1"
                                  v-model="$v.create.is_default.$model"
                                  name="some-radios__create"
                                  value="0"
                              >{{ $t("general.No") }}</b-form-radio
                              >
                          </b-form-group>
                          <template v-if="errors.is_default">
                              <ErrorMessage
                                  v-for="(errorMessage, index) in errors.is_default"
                                  :key="index"
                              >{{ errorMessage }}</ErrorMessage
                              >
                          </template>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                          <label class="mr-2">
                              {{ getCompanyKey("is_active") }}
                              <span class="text-danger">*</span>
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
                                  value="active"
                              >{{ $t("general.Active") }}</b-form-radio
                              >
                              <b-form-radio
                                  class="d-inline-block m-1"
                                  v-model="$v.create.is_active.$model"
                                  name="some-radios"
                                  value="inactive"
                              >{{ $t("general.Inactive") }}</b-form-radio
                              >
                          </b-form-group>
                          <template v-if="errors.is_active">
                              <ErrorMessage
                                  v-for="(errorMessage, index) in errors.is_active"
                                  :key="index"
                              >{{ errorMessage }}
                              </ErrorMessage>
                          </template>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                          <label class="mr-2">
                              {{ getCompanyKey("installment_payment_description_ar") }}
                              <span class="text-danger">*</span>
                          </label>
                          <textarea @input="arabicValueDescription(create.description)" v-model="$v.create.description.$model" class="form-control" :maxlength="1000" rows="5"></textarea>
                          <template v-if="errors.description">
                              <ErrorMessage
                                  v-for="(errorMessage, index) in errors.description"
                                  :key="index"
                              >{{ errorMessage }}</ErrorMessage
                              >
                          </template>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                          <label class="mr-2">
                              {{ getCompanyKey("installment_payment_description_en") }}
                              <span class="text-danger">*</span>
                          </label>
                          <textarea  @input="englishValueDescription(create.description_e)" v-model="$v.create.description_e.$model" class="form-control" :maxlength="1000" rows="5"></textarea>
                          <template v-if="errors.description_e">
                              <ErrorMessage
                                  v-for="(errorMessage, index) in errors.description_e"
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
