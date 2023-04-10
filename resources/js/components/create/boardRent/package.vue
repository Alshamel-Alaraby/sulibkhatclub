<template>
    <!--  create   -->
    <b-modal
        id="package-create"
        :title="getCompanyKey('boardRent_package_create_form')"
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
                            {{ getCompanyKey("boardRent_package_name_ar") }}
                            <span class="text-danger">*</span>
                        </label>
                        <div dir="rtl">
                            <input
                                @keyup="arabicValue(create.name)"
                                type="text"
                                class="form-control"
                                data-create="1"
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
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="field-2" class="control-label">
                            {{ getCompanyKey("boardRent_package_name_en") }}
                            <span class="text-danger">*</span>
                        </label>
                        <div dir="ltr">
                            <input
                                @keyup="englishValue(create.name_e)"
                                type="text"
                                class="form-control englishInput"
                                data-create="2"
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
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="field-3" class="control-label">
                            {{ getCompanyKey("boardRent_package_code") }}
                            <span class="text-danger">*</span>
                        </label>
                        <div >
                            <input
                                type="number"
                                class="form-control"
                                v-model="$v.create.code.$model"
                                :class="{
                                                    'is-invalid': $v.create.code.$error || errors.code,
                                                    'is-valid': !$v.create.code.$invalid && !errors.code,
                                                  }"
                                id="field-3"
                            />
                        </div>
                        <template v-if="errors.code">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.code"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="field-4" class="control-label">
                            {{ getCompanyKey("boardRent_package_price") }}
                            <span class="text-danger">*</span>
                        </label>
                        <div>
                            <input
                                step=".01"
                                type="number"
                                class="form-control"
                                v-model="$v.create.price.$model"
                                :class="{
                                                    'is-invalid': $v.create.price.$error || errors.price,
                                                    'is-valid': !$v.create.price.$invalid && !errors.price,
                                                  }"
                                id="field-4"
                            />
                        </div>
                        <template v-if="errors.price">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.price"
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
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../loader";
import {decimal, maxLength, minLength, minValue, required} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import transMixinComp from "../../../helper/translation-comp-mixin";

export default {
    name: "package",
    components: {
        ErrorMessage,
        loader,
    },
    mixins: [transMixinComp],
    props: ["companyKeys", "defaultsKeys"],
    data() {
        return {
            isLoader: false,
            create: {
                name: "",
                name_e: "",
                code: "",
                price: ""
            },
            company_id: null,
            errors: {},
            is_disabled: false,
        };
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    validations: {
        create: {
            name: { required, minLength: minLength(2), maxLength: maxLength(255) },
            name_e: { required, minLength: minLength(2), maxLength: maxLength(255) },
            code: { required, decimal , minValue: minValue(0.01)},
            price: { required, decimal , minValue: minValue(0.01) }
        },
    },
    methods: {
        /**
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.create = {
                name: "",
                name_e: "",
                code: "",
                price: ""
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.$bvModal.hide(`package-create`);
        },
        /**
         *  hidden Modal (create)
         */
        resetModal() {
            this.create = {
                name: "",
                name_e: "",
                code: "",
                price: ""
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
                code: "",
                price: ""
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
                    .post(`/boards-rent/packages`, {...this.create,company_id: this.company_id})
                    .then((res) => {
                        this.is_disabled = true;
                        this.$emit('created');
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
        arabicValue(txt) {
            this.create.name = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.name_e = englishValue(txt);
        },
    }
}
</script>

<style scoped>

</style>
