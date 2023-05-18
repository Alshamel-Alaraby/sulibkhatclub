<script>
import adminApi from "../../api/adminAxios";
import Switches from "vue-switches";
import {
    required,
    minLength,
    maxLength,
    integer,
    numeric,
    email,
    requiredIf
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../components/widgets/errorMessage";
import loader from "../../components/loader";
import Multiselect from "vue-multiselect";
import country from "../country";
import city from "../city";
import employee from "./employee";
import bankAccount from "../create/bankAccount"
import {arabicValue, englishValue} from "../../helper/langTransform";

// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
import transMixinComp from "../../helper/translation-comp-mixin";

/**
 * Advanced Table component
 */

export default {
    components: {
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
        country,
        bankAccount,
        city,
        employee
    },
    mixins: [transMixinComp],
    data() {
        return {
            fields: [],
            cities: [],
            countries: [],
            bank_accounts: [],
            employees: [],
            isLoader: false,
            create: {
                name: "",
                name_e: "",
                phone: "",
                email: "",
                rp_code: null,
                contact_person: "",
                contact_phone: "",
                national_id: null,
                nationality: null,
                bank_account_id: null,
                employee_id: null,
                country_id: null,
                city_id: null,
                whatsapp: "",
                passport_no: null,
                Note1: "",
                Note2: "",
                Note3: "",
                Note4: "",
            },
            codeCountry: '',
            errors: {},
            image: "",
            is_disabled: false,
        };
    },
    validations: {
        create: {
            name: {
                required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(2), maxLength: maxLength(100),
            },
            name_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(2), maxLength: maxLength(100),
            },
            phone: {
                required: requiredIf(function (model) {
                    return this.isRequired("phone");
                }), maxLength: maxLength(20)
            },
            email: {
                required: requiredIf(function (model) {
                    return this.isRequired("email");
                }), maxLength: maxLength(100), email
            },
            rp_code: {
                required: requiredIf(function (model) {
                    return this.isRequired("rp_code");
                }), maxLength: maxLength(9),
            },
            nationality: {
                required: requiredIf(function (model) {
                    return this.isRequired("nationality");
                })
            },
            contact_person: {
                required: requiredIf(function (model) {
                    return this.isRequired("contact_person");
                }), maxLength: maxLength(100)
            },
            contact_phone: {
                required: requiredIf(function (model) {
                    return this.isRequired("contact_phone");
                }), maxLength: maxLength(100)
            },
            national_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("national_id");
                }), integer, maxLength: maxLength(20)
            },
            country_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("country_id");
                })
            },
            city_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("city_id");
                })
            },
            bank_account_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("bank_account_id");
                })
            },
            employee_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("employee_id");
                })
            },
            whatsapp: {
                required: requiredIf(function (model) {
                    return this.isRequired("whatsapp");
                }), maxLength: maxLength(20)
            },
            passport_no: {
                required: requiredIf(function (model) {
                    return this.isRequired("passport_no");
                }), integer, maxLength: maxLength(20)
            }
        },
    },
    mounted() {
        this.getCustomTableFields();
        this.$store.dispatch('locationIp/getIp');
    },
    props: {
        id: {
            default: "customer-general-create",
        },
        companyKeys: {
            default: []
        },
        defaultsKeys: {
            default: []
        },
    },
    methods: {
        getCustomTableFields() {
            adminApi
                .get(`/customTable/table-columns/general_customers`)
                .then((res) => {
                    this.fields = res.data;
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
        isVisible(fieldName) {
            let res = this.fields.filter((field) => {
                return field.column_name == fieldName;
            });
            return res.length > 0 && res[0].is_visible == 1 ? true : false;
        },
        isRequired(fieldName) {
            let res = this.fields.filter((field) => {
                return field.column_name == fieldName;
            });
            return res.length > 0 && res[0].is_required == 1 ? true : false;
        },
        resetModalHidden() {
            this.create = {
                name: "",
                name_e: "",
                phone: "",
                email: "",
                rp_code: null,
                nationality: null,
                code_country: '',
                contact_person: "",
                contact_phone: "",
                national_id: null,
                passport_no:'',
                bank_account_id: null,
                employee_id: null,
                country_id: null,
                city_id: null,
                whatsapp: "",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.$bvModal.hide(this.id);
            this.is_disabled = false;
            this.$bvModal.hide(`customer-create`);
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            await this.getCategory();
            await this.getBankAcount();
            await this.getEmployees();
            this.create = {
                name: "",
                name_e: "",
                phone: "",
                email: "",
                rp_code: null,
                nationality: null,
                contact_person: "",
                contact_phone: "",
                code_country: '',
                national_id: null,
                passport_no:'',
                bank_account_id: null,
                employee_id: null,
                country_id: null,
                city_id: null,
                whatsapp: "",
            };
            this.codeCountry = this.$store.getters["locationIp/countryCode"];
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  create countrie
         */
        async resetForm() {
            await this.getCategory();
            await this.getBankAcount();
            await this.getEmployees();
            this.create = {
                name: "",
                name_e: "",
                phone: "",
                email: "",
                rp_code: null,
                code_country: '',
                nationality: null,
                contact_person: "",
                contact_phone: "",
                national_id: null,
                passport_no:'',
                bank_account_id: null,
                employee_id: null,
                country_id: null,
                city_id: null,
                whatsapp: "",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.codeCountry = this.$store.getters["locationIp/countryCode"];
            this.errors = {};
            this.is_disabled = false;
        },

        AddSubmit() {
            this.create.code_country = this.codeCountry;
            if (!this.create.name) {
                this.create.name = this.create.name_e;
            }
            if (!this.create.name_e) {
                this.create.name_e = this.create.name;
            }
            if (!this.create.description) {
                this.create.description = this.create.description_e;
            }
            if (!this.create.description_e) {
                this.create.description_e = this.create.description;
            }
            this.create.company_id = JSON.parse(localStorage.getItem("company_id"));

            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                adminApi
                    .post(`/general-customer`, this.create)
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
        getCurrentYear() {
            return new Date().getFullYear();
        },
        showCountryModal() {
            if (this.create.country_id == 0) {
                this.$bvModal.show("country-create-customer");
                this.create.country_id = null;
            } else {
                this.getCity(this.create.country_id)
            }
        },
        showEmployeeModal() {
            if (this.create.employee_id == 0) {
                this.$bvModal.show("employee-create");
                this.create.employee_id = null;
            }
        },
        showBankAccountModal() {
            if (this.create.bank_account_id == 0) {
                this.$bvModal.show("bank-account-create");
                this.create.bank_account_id = null;
            }
        },
        showCityModal() {
            if (this.create.city_id == 0) {
                this.$bvModal.show("city-create-customer");
                this.create.city_id = null;
            }
        },
        async getCategory() {
            this.create.city_id = null;
            await adminApi
                .get(`/countries?is_active=active`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({id: 0, name: "اضافة دولة", name_e: "Add Country"});
                    this.countries = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getEmployees() {
            await adminApi
                .get(`/employees?customer_handel=1`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضاف موظف", name_e: "Add Employee" });
                    this.employees = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getBankAcount() {
            this.bank_accounts = [];
            await adminApi
                .get(`/bank-accounts?`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({id: 0, name: "اضافة حساب بنكي", account_number: "Add Bank Account"});
                    this.bank_accounts = l;
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
        async getCity(id = null) {
            if (id) {
                this.showCityModal();
                await adminApi
                    .get(`/cities?country_id=${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.unshift({id: 0, name: "اضافة مدينة", name_e: "Add City"});
                        this.cities = l;
                    })
                    .catch((err) => {
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                        });
                    });
            }
        },

        arabicValue(txt) {
            this.create.name = arabicValue(txt);
        },

        englishValue(txt) {
            this.create.name_e = englishValue(txt);
        },
        updatePhone(e) {
            this.create.phone = e.phoneNumber;
        },
        updateWhatsapp(e) {
            this.create.whatsapp = e.phoneNumber;
        },
        updateContract(e) {
            this.create.contact_phone = e.phoneNumber;
        },
    },
};
</script>

<template>
    <div>
        <employee :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getEmployees" />
        <country :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'country-create-customer'"
                 @created="getCategory"/>
        <bankAccount :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getBankAcount"/>
        <city :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'city-create-customer'"
              @created="getCity(create.country_id)"/>

        <!--  create   -->
        <b-modal
            :id="id"
            :title="getCompanyKey('general_customer_create_form')"
            title-class="font-18"
            dialog-class="modal-full-width"
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
                    <div class="col-md-4" v-if="isVisible('name')">
                        <div class="form-group">
                            <label for="field-1" class="control-label">
                                {{ getCompanyKey('general_customer_name_ar') }}
                                <span v-if="isRequired('name')" class="text-danger">*</span>
                            </label>
                            <div dir="rtl">
                                <input
                                    @keyup="arabicValue(create.name)"
                                    type="text"
                                    class="form-control"
                                    data-create="1"
                                    v-model="$v.create.name.$model"
                                    :class="{
                                                    'is-invalid':$v.create.name.$error || errors.name,
                                                    'is-valid':!$v.create.name.$invalid && !errors.name
                                                }"
                                    id="field-1"
                                />
                            </div>
                            <div v-if="!$v.create.name.minLength" class="invalid-feedback">
                                {{ $t('general.Itmustbeatleast') }} {{ $v.create.name.$params.minLength.min }}
                                {{ $t('general.letters') }}
                            </div>
                            <div v-if="!$v.create.name.maxLength" class="invalid-feedback">
                                {{ $t('general.Itmustbeatmost') }} {{ $v.create.name.$params.maxLength.max }}
                                {{ $t('general.letters') }}
                            </div>
                            <template v-if="errors.name">
                                <ErrorMessage v-for="(errorMessage,index) in errors.name" :key="index">{{
                                        errorMessage
                                    }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-4" v-if="isVisible('name_e')">
                        <div class="form-group">
                            <label for="field-2" class="control-label">
                                {{ getCompanyKey('general_customer_name_en') }}
                                <span v-if="isRequired('name_e')" class="text-danger">*</span>
                            </label>
                            <div dir="ltr">
                                <input
                                    @keyup="englishValue(create.name_e)"
                                    type="text"
                                    class="form-control"
                                    data-create="2"
                                    v-model="$v.create.name_e.$model"
                                    :class="{
                                        'is-invalid':$v.create.name_e.$error || errors.name_e,
                                        'is-valid':!$v.create.name_e.$invalid && !errors.name_e
                                    }"
                                    id="field-2"
                                />
                            </div>
                            <div v-if="!$v.create.name_e.minLength" class="invalid-feedback">
                                {{ $t('general.Itmustbeatleast') }} {{ $v.create.name_e.$params.minLength.min }}
                                {{ $t('general.letters') }}
                            </div>
                            <div v-if="!$v.create.name_e.maxLength" class="invalid-feedback">
                                {{ $t('general.Itmustbeatmost') }} {{ $v.create.name_e.$params.maxLength.max }}
                                {{ $t('general.letters') }}
                            </div>
                            <template v-if="errors.name_e">
                                <ErrorMessage v-for="(errorMessage,index) in errors.name_e" :key="index">{{
                                        errorMessage
                                    }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                </div>
                <hr v-if="isVisible('nationality') || isVisible('country_id') || isVisible('city_id') || isVisible('national_id') || isVisible('passport_no')"
                    style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)"/>
                <div class="row">
                    <div class="col-md-4" v-if="isVisible('nationality')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey('general_customer_nationality') }}
                                <span v-if="isRequired('nationality')" class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                data-create="9"
                                v-model="$v.create.nationality.$model"
                                :class="{
                                                'is-invalid':$v.create.nationality.$error || errors.nationality,
                                                'is-valid':!$v.create.nationality.$invalid && !errors.nationality
                                            }"
                            />
                            <template v-if="errors.nationality">
                                <ErrorMessage v-for="(errorMessage,index) in errors.nationality" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-4" v-if="isVisible('country_id')">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey('general_customer_country') }}
                                <span v-if="isRequired('country_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                @input="showCountryModal"
                                v-model="$v.create.country_id.$model"
                                :options="countries.map((type) => type.id)"
                                :custom-label="(opt) => countries.find((x) => x.id == opt).name"
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.country_id.$error || errors.country_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.country_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.country_id"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-4" v-if="isVisible('city_id')">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey('general_customer_city') }}
                                <span v-if="isRequired('city_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                @input="getCity(create.country_id)"
                                v-model="$v.create.city_id.$model"
                                :options="cities.map((type) => type.id)"
                                :custom-label="(opt) => cities.find((x) => x.id == opt).name"
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.city_id.$error || errors.city_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.city_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.city_id"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-4" v-if="isVisible('national_id')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey('general_customer_national_id') }}
                                <span v-if="isRequired('national_id')" class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                data-create="9"
                                step="0.1"
                                v-model="$v.create.national_id.$model"
                                :class="{
                                    'is-invalid':$v.create.national_id.$error || errors.national_id,
                                    'is-valid':!$v.create.national_id.$invalid && !errors.national_id
                                }"
                            />
                            <template v-if="errors.national_id">
                                <ErrorMessage v-for="(errorMessage,index) in errors.national_id" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-4" v-if="isVisible('passport_no')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey('general_customer_passport_number') }}
                                <span v-if="isRequired('passport_no')" class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                data-create="9"
                                step="0.1"
                                v-model="$v.create.passport_no.$model"
                                :class="{
                                    'is-invalid':$v.create.passport_no.$error || errors.passport_no,
                                    'is-valid':!$v.create.passport_no.$invalid && !errors.passport_no
                                }"
                            />
                            <template v-if="errors.passport_no">
                                <ErrorMessage v-for="(errorMessage,index) in errors.passport_no" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                </div>
                <hr v-if="isVisible('bank_account_id') || isVisible('rp_code') || isVisible('phone') || isVisible('whatsapp') || isVisible('email')"
                    style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)"/>
                <div class="row">
                    <div class="col-md-4" v-if="isVisible('bank_account_id')">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey('bank_account') }}
                                <span v-if="isRequired('bank_account_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                @input="showBankAccountModal"
                                v-model="$v.create.bank_account_id.$model"
                                :options="bank_accounts.map((type) => type.id)"
                                :custom-label="(opt) => bank_accounts.find((x) => x.id == opt).account_number"
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.bank_account_id.$error || errors.bank_account_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.bank_account_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.bank_account_id"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-4" v-if="isVisible('rp_code')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey('general_customer_code') }}
                                <span v-if="isRequired('rp_code')" class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                data-create="9"
                                v-model="$v.create.rp_code.$model"
                                :class="{
                                                'is-invalid':$v.create.rp_code.$error || errors.rp_code,
                                                'is-valid':!$v.create.rp_code.$invalid && !errors.rp_code
                                            }"
                            />
                            <template v-if="errors.rp_code">
                                <ErrorMessage v-for="(errorMessage,index) in errors.rp_code" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-4" v-if="isVisible('phone')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey('general_customer_phone') }}
                                <span v-if="isRequired('phone')" class="text-danger">*</span>
                            </label>
                            <VuePhoneNumberInput
                                v-model="$v.create.phone.$model"
                                :default-country-code="codeCountry"
                                valid-color="#28a745"
                                error-color="#dc3545"
                                :preferred-countries="['FR', 'EG', 'DE']"
                                @update="updatePhone"
                            />
                            <template v-if="errors.phone">
                                <ErrorMessage v-for="(errorMessage,index) in errors.phone" :key="index">{{
                                        errorMessage
                                    }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-4" v-if="isVisible('whatsapp')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey('general_customer_whatsapp') }}
                                <span v-if="isRequired('whatsapp')" class="text-danger">*</span>
                            </label>
                            <VuePhoneNumberInput
                                v-model="$v.create.whatsapp.$model"
                                :default-country-code="codeCountry"
                                valid-color="#28a745"
                                error-color="#dc3545"
                                :preferred-countries="['FR', 'EG', 'DE']"
                                @update="updateWhatsapp"
                            />
                            <template v-if="errors.whatsapp">
                                <ErrorMessage v-for="(errorMessage,index) in errors.whatsapp" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-4" v-if="isVisible('email')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey('general_customer_email') }}
                                <span v-if="isRequired('email')" class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                data-create="9"
                                v-model="$v.create.email.$model"
                                :class="{
                                                'is-invalid':$v.create.email.$error || errors.email,
                                                'is-valid':!$v.create.email.$invalid && !errors.email
                                            }"
                            />
                            <template v-if="errors.email">
                                <ErrorMessage v-for="(errorMessage,index) in errors.email" :key="index">{{
                                        errorMessage
                                    }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                </div>
                <hr v-if="isVisible('contact_person') || isVisible('contact_phone') || isVisible('employee_id')" style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                <div class="row">
                    <div class="col-md-4" v-if="isVisible('contact_person')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey('general_customer_contact_person') }}
                                <span v-if="isRequired('contact_person')" class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                data-create="9"
                                v-model="$v.create.contact_person.$model"
                                :class="{
                                                'is-invalid':$v.create.contact_person.$error || errors.contact_person,
                                                'is-valid':!$v.create.contact_person.$invalid && !errors.contact_person
                                            }"
                            />
                            <template v-if="errors.contact_person">
                                <ErrorMessage v-for="(errorMessage,index) in errors.contact_person" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-4" v-if="isVisible('contact_phone')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey('general_customer_contact_phones') }}
                                <span v-if="isRequired('contact_phone')" class="text-danger">*</span>
                            </label>
                            <VuePhoneNumberInput
                                v-model="$v.create.contact_phone.$model"
                                :default-country-code="codeCountry"
                                valid-color="#28a745"
                                error-color="#dc3545"
                                :preferred-countries="['FR', 'EG', 'DE']"
                                @update="updateContract"
                            />
                            <template v-if="errors.contact_phone">
                                <ErrorMessage v-for="(errorMessage,index) in errors.contact_phone" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-4" v-if="isVisible('employee_id')">
                        <div class="form-group">
                            <label>{{ getCompanyKey('employee') }}<span v-if="isRequired('employee_id')" class="text-danger">*</span></label>
                            <multiselect
                                @input="showEmployeeModal"
                                v-model="create.employee_id"
                                :options="employees.map((type) => type.id)"
                                :custom-label=" (opt) => $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e "
                                :class="{'is-invalid': $v.create.employee_id.$error || errors.employee_id,'is-valid': !$v.create.employee_id.$invalid && !errors.employee_id,}"
                            >
                            </multiselect>
                            <template v-if="errors.employee_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.employee_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                </div>
            </form>
        </b-modal>
        <!--  /create   -->
    </div>
</template>
<style scoped>
.dropdown-menu-custom-company.dropdown .dropdown-menu {
    padding: 5px 10px !important;
    overflow-y: scroll;
    height: 300px;
}
</style>
