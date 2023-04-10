<script>
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import { required, minLength, maxLength, integer } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import { dynamicSortString } from "../../../helper/tableSort";
import { formatDateOnly } from "../../../helper/startDate";
import transMixinComp from "../../../helper/translation-comp-mixin";
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
  },
  data() {
    return {
      enabled3: false,
      isLoader: false,
      create: {
        name: "",
        name_e: "",
        is_default: 1,
      },
      errors: {},
      dropDownSenders: [],
      is_disabled: false,
      isCheckAll: false,
    };
  },
  validations: {
    create: {
      name: { required, minLength: minLength(3), maxLength: maxLength(100) },
      name_e: { required, minLength: minLength(3), maxLength: maxLength(100) },
      is_default: { required },
    },
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
    //     if (48 <= ew && ew <= 57) return true;
    //     if (65 <= ew && ew <= 90) return false;
    //     if (97 <= ew && ew <= 122) return false;
    //     return true;
    //   });
    // });
  },
 props:["companyKeys","defaultsKeys"],
  methods: {
    resetModalHidden() {
      this.create = { name: "", name_e: "", is_default: 1 };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.$bvModal.hide(`installment-payment-create`);
    },
    /**
     *  hidden Modal (create)
     */
    resetModal() {
      this.create = { name: "", name_e: "", is_default: 1 };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create module
     */
    resetForm() {
      this.create = { name: "", name_e: "", is_default: 1 };
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
          .post(`/recievable-payable/rp_installment_status`, this.create)
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
    formatDate(value) {
      return formatDateOnly(value);
    },
    arabicValue(txt){
          this.create.name = arabicValue(txt);
      },
    englishValue(txt){
       this.create.name_e = englishValue(txt);
    }
  },
};
</script>

<template>
  <!--  create   -->
    <b-modal
        id="installment-payment-create"
        :title="getCompanyKey('installment_status_create_form')"
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
                    type="button" :class="['font-weight-bold px-2',is_disabled?'mx-2': '']"
                >
                    {{ $t('general.AddNewRecord') }}
                </b-button>
                <template v-if="!is_disabled">
                    <b-button
                        variant="success"
                        type="button" class="mx-1"
                        v-if="!isLoader"
                        @click.prevent="AddSubmit"
                    >
                        {{ $t('general.Add') }}
                    </b-button>

                    <b-button variant="success" class="mx-1" disabled v-else>
                        <b-spinner small></b-spinner>
                        <span class="sr-only">{{ $t('login.Loading') }}...</span>
                    </b-button>
                </template>
                <!-- Emulate built in modal footer ok and cancel button actions -->

                <b-button variant="danger" type="button" @click.prevent="resetModalHidden">
                    {{ $t('general.Cancel') }}
                </b-button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="field-1" class="control-label">
                            {{ getCompanyKey('installment_status_name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div dir="rtl">
                            <input
                                type="text"
                                class="form-control arabicInput"
                                data-create="1"
                                @keyup="arabicValue(create.name)"
                                v-model="$v.create.name.$model"
                                :class="{
                                                        'is-invalid':$v.create.name.$error || errors.name,
                                                        'is-valid':!$v.create.name.$invalid && !errors.name
                                                    }"
                                id="field-1"
                            />
                        </div>
                        <div v-if="!$v.create.name.minLength" class="invalid-feedback">
                            {{ $t('general.Itmustbeatleast') }}
                            {{ $v.create.name.$params.minLength.min }} {{ $t('general.letters') }}
                        </div>
                        <div v-if="!$v.create.name.maxLength" class="invalid-feedback">
                            {{ $t('general.Itmustbeatmost') }}
                            {{ $v.create.name.$params.maxLength.max }} {{ $t('general.letters') }}
                        </div>
                        <template v-if="errors.name">
                            <ErrorMessage v-for="(errorMessage,index) in errors.name" :key="index">
                                {{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-12 ">
                    <div class="form-group">
                        <label for="field-2" class="control-label">
                            {{ getCompanyKey('installment_status_name_e') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div>
                            <input
                                type="text"
                                class="form-control englishInput"
                                data-create="2"
                                @keyup="englishValue(create.name_e)"
                                v-model="$v.create.name_e.$model"
                                :class="{
                                                'is-invalid':$v.create.name_e.$error || errors.name_e,
                                                'is-valid':!$v.create.name_e.$invalid && !errors.name_e
                                            }"
                                id="field-2"
                            />
                        </div>
                        <div v-if="!$v.create.name_e.minLength" class="invalid-feedback">
                            {{ $t('general.Itmustbeatleast') }}
                            {{ $v.create.name_e.$params.minLength.min }} {{ $t('general.letters') }}
                        </div>
                        <div v-if="!$v.create.name_e.maxLength" class="invalid-feedback">
                            {{ $t('general.Itmustbeatmost') }}
                            {{ $v.create.name_e.$params.maxLength.max }} {{ $t('general.letters') }}
                        </div>
                        <template v-if="errors.name_e">
                            <ErrorMessage v-for="(errorMessage,index) in errors.name_e"
                                          :key="index">{{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="mr-2">
                            {{ getCompanyKey('installment_status_default') }}
                        </label>
                        <b-form-group
                            id="field-11"
                            :class="{
                              'is-invalid': $v.create.is_default.$error || errors.is_default,
                              'is-valid': !$v.create.is_default.$invalid && !errors.is_default,
                            }"
                        >
                            <b-form-radio
                                class="d-inline-block"
                                v-model="$v.create.is_default.$model"
                                name="some-radios"
                                value="1"
                            >{{ $t("general.Yes") }}</b-form-radio
                            >
                            <b-form-radio
                                class="d-inline-block m-1"
                                v-model="$v.create.is_default.$model"
                                name="some-radios"
                                value="0"
                            >{{ $t("general.No") }}</b-form-radio
                            >
                        </b-form-group>
                        <template v-if="errors.is_default">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.is_default"
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
