<template>
    <div>
        <!--  create   -->
        <b-modal :id="id"
            :title="type != 'edit' ? getCompanyKey('hms_patients_create_form') : getCompanyKey('hms_patients_edit_form')"
            title-class="font-18" size="lg" body-class="p-4 " :hide-footer="true" @show="resetModal" @hidden="resetModalHidden">
            <form>
                <loader size="large" v-if="isCustom && !isPage" />
                <div class="mb-3 d-flex justify-content-end">
                    <b-button v-if="type != 'edit'" variant="success" :disabled="!is_disabled" @click.prevent="resetForm"
                        type="button" :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                        {{ $t("general.AddNewRecord") }}
                    </b-button>
                    <template v-if="!is_disabled">
                        <b-button variant="success" type="button" class="mx-1" v-if="!isLoader" @click.prevent="AddSubmit">
                            {{ type != 'edit' ? $t("general.Add") : $t("general.edit") }}
                        </b-button>

                        <b-button variant="success" class="mx-1" disabled v-else>
                            <b-spinner small></b-spinner>
                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                        </b-button>
                    </template>
                    <!-- Emulate built in modal footer ok and cancel button actions -->

                    <b-button variant="danger" type="button" @click.prevent="resetModalHidden">
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-6" v-if="isVisible('name')">
                        <div class="form-group">
                            <label for="field-1" class="control-label">
                                {{ getCompanyKey('hms_patients_name_ar') }}
                                <span v-if="isRequired('name')" class="text-danger">*</span>
                            </label>
                            <div dir="rtl">
                                <input type="text" class="form-control arabicInput" @keyup="arabicValueName(create.name)"
                                    v-model="$v.create.name.$model" :class="{
                                        'is-invalid': $v.create.name.$error || errors.name,
                                        'is-valid': !$v.create.name.$invalid && !errors.name,
                                    }" id="field-1" />
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
                                <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('name_e')">
                        <div class="form-group">
                            <label for="field-2" class="control-label">
                                {{ getCompanyKey('hms_patients_name_en') }}
                                <span v-if="isRequired('name_e')" class="text-danger">*</span>
                            </label>
                            <div dir="ltr">
                                <input type="text" class="form-control englishInput" data-create="2"
                                    @keyup="englishValueName(create.name_e)" v-model="$v.create.name_e.$model" :class="{
                                        'is-invalid': $v.create.name_e.$error || errors.name_e,
                                        'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                                    }" id="field-2" />
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
                                <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('email')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("hms_patients_email") }}
                                <span v-if="create.type == 'outpatient' ?false : isRequired('email')" class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" data-create="9" v-model="$v.create.email.$model" :class="{
                                'is-invalid': $v.create.email.$error || errors.email,
                                'is-valid': !$v.create.email.$invalid && !errors.email,
                            }" />
                            <template v-if="errors.email">
                                <ErrorMessage v-for="(errorMessage, index) in errors.email" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('mobile')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("hms_patients_mobile") }}
                                <span v-if="isRequired('phone')" class="text-danger">*</span>
                            </label>
                            <VuePhoneNumberInput v-model="$v.create.mobile.$model" :default-country-code="codeCountry"
                                valid-color="#28a745" error-color="#dc3545" :preferred-countries="['FR', 'EG', 'DE']"
                                @update="updatePhone" />
                            <div v-if="$v.create.mobile.$error || errors.mobile" class="text-danger">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.mobile">
                                <ErrorMessage v-for="(errorMessage, index) in errors.mobile" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('whatsapp')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("hms_patients_whatsapp") }}
                                <span v-if="isRequired('whatsapp')" class="text-danger">*</span>
                            </label>
                            <VuePhoneNumberInput v-model="$v.create.whatsapp.$model" :default-country-code="codeCountry"
                                valid-color="#28a745" error-color="#dc3545" :preferred-countries="['FR', 'EG', 'DE']"
                                @update="updateWhatsapp" />
                            <div v-if="$v.create.whatsapp.$error || errors.whatsapp" class="text-danger">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.whatsapp">
                                <ErrorMessage v-for="(errorMessage, index) in errors.whatsapp" :key="index">{{ errorMessage
                                }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('address')">
                        <div class="form-group">
                            <label for="field-22" class="control-label">
                                {{ getCompanyKey('hms_patients_address') }}
                                <span v-if="isRequired('address')" class="text-danger">*</span>
                            </label>
                            <div>
                                <input type="text" class="form-control " data-create="2" v-model="$v.create.address.$model"
                                    :class="{
                                        'is-invalid': $v.create.address.$error || errors.address,
                                        'is-valid': !$v.create.address.$invalid && !errors.address,
                                    }" id="field-22" />
                            </div>
                            <template v-if="errors.address">
                                <ErrorMessage v-for="(errorMessage, index) in errors.address" :key="index">{{ errorMessage
                                }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('date_of_birth')">
                        <div class="form-group">
                            <label for="field-23" class="control-label">
                                {{ getCompanyKey('hms_patients_date_of_birth') }}
                                <span v-if="isRequired('date_of_birth')" class="text-danger">*</span>
                            </label>
                            <div>
                                <input type="date" class="form-control " data-create="2"
                                    v-model="$v.create.date_of_birth.$model" :class="{
                                        'is-invalid': $v.create.date_of_birth.$error || errors.date_of_birth,
                                        'is-valid': !$v.create.date_of_birth.$invalid && !errors.date_of_birth,
                                    }" id="field-23" />
                            </div>
                            <template v-if="errors.date_of_birth">
                                <ErrorMessage v-for="(errorMessage, index) in errors.date_of_birth" :key="index">{{
                                    errorMessage }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('safe_number')">
                        <div class="form-group">
                            <label for="field-24" class="control-label">
                                {{ getCompanyKey('hms_patients_safe_number') }}
                                <span v-if="isRequired('safe_number')" class="text-danger">*</span>
                            </label>
                            <div>
                                <input type="number" class="form-control " data-create="2"
                                    v-model="$v.create.safe_number.$model" :class="{
                                        'is-invalid': $v.create.safe_number.$error || errors.safe_number,
                                        'is-valid': !$v.create.safe_number.$invalid && !errors.safe_number,
                                    }" id="field-24" />
                            </div>
                            <template v-if="errors.safe_number">
                                <ErrorMessage v-for="(errorMessage, index) in errors.safe_number" :key="index">{{
                                    errorMessage }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('patient_weight')">
                        <div class="form-group">
                            <label for="field-25" class="control-label">
                                {{ getCompanyKey('hms_patients_patient_weight') }}
                                <span v-if="isRequired('patient_weight')" class="text-danger">*</span>
                            </label>
                            <div>
                                <input type="number" class="form-control " data-create="2"
                                    v-model="$v.create.patient_weight.$model" :class="{
                                        'is-invalid': $v.create.patient_weight.$error || errors.patient_weight,
                                        'is-valid': !$v.create.patient_weight.$invalid && !errors.patient_weight,
                                    }" id="field-25" />
                            </div>
                            <template v-if="errors.patient_weight">
                                <ErrorMessage v-for="(errorMessage, index) in errors.patient_weight" :key="index">{{
                                    errorMessage }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('patient_height')">
                        <div class="form-group">
                            <label for="field-25" class="control-label">
                                {{ getCompanyKey('hms_patients_patient_height') }}
                                <span v-if="isRequired('patient_height')" class="text-danger">*</span>
                            </label>
                            <div>
                                <input type="number" class="form-control " data-create="2"
                                    v-model="$v.create.patient_height.$model" :class="{
                                        'is-invalid': $v.create.patient_height.$error || errors.patient_height,
                                        'is-valid': !$v.create.patient_height.$invalid && !errors.patient_height,
                                    }" id="field-25" />
                            </div>
                            <template v-if="errors.patient_height">
                                <ErrorMessage v-for="(errorMessage, index) in errors.patient_height" :key="index">{{
                                    errorMessage }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('gender')">
                        <div class="form-group">
                            <label for="field-26" class="control-label">
                                {{ getCompanyKey('hms_patients_gender') }}
                                <span v-if="isRequired('gender')" class="text-danger">*</span>
                            </label>
                            <select id="field-26" class="form-control " v-model="$v.create.gender.$model" :class="{
                                'is-invalid': $v.create.gender.$error || errors.gender,
                                'is-valid': !$v.create.gender.$invalid && !errors.gender,
                            }">
                                <option value=""> ---- </option>
                                <option value="Male">{{ $t("general.Male") }}</option>
                                <option value="Female">{{ $t("general.Female") }}</option>
                            </select>

                            <template v-if="errors.gender">
                                <ErrorMessage v-for="(errorMessage, index) in errors.gender" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('blood')">
                        <div class="form-group">
                            <label for="field-27" class="control-label">
                                {{ getCompanyKey('hms_patients_blood') }}
                                <span v-if="isRequired('blood')" class="text-danger">*</span>
                            </label>
                            <select class="form-control " id="field-27" v-model="$v.create.blood.$model" :class="{
                                'is-invalid': $v.create.blood.$error || errors.blood,
                                'is-valid': !$v.create.blood.$invalid && !errors.blood,
                            }">
                                <option value=""> ---- </option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                            </select>

                            <template v-if="errors.blood">
                                <ErrorMessage v-for="(errorMessage, index) in errors.blood" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="create.type == 'inpatient'">
                        <div class="form-group">
                            <label for="field-15" class="control-label">
                                {{ getCompanyKey("hms_patients_password") }}
                                <span v-if="isRequired('password')" class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" data-create="1" v-model="$v.create.password.$model"
                                :class="{
                                    'is-invalid':
                                        $v.create.password.$error || errors.password,
                                    'is-valid':
                                        !$v.create.password.$invalid && !errors.password,
                                }" id="field-15" />
                            <div v-if="!$v.create.password.minLength" class="invalid-feedback">
                                {{ $t("general.Itmustbeatleast") }}
                                {{ $v.create.password.$params.minLength.min }}
                                {{ $t("general.letters") }}
                            </div>
                            <template v-if="errors.password">
                                <ErrorMessage v-for="(errorMessage, index) in errors.password" :key="index">{{ errorMessage
                                }}</ErrorMessage>
                            </template>
                        </div>
                    </div>

                    <div v-if="create.type == 'inpatient'" class="col-md-6">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("hms_patients_is_active") }}
                                <span v-if="isRequired('is_active')" class="text-danger">*</span>
                            </label>
                            <b-form-group :class="{
                                'is-invalid': $v.create.is_active.$error || errors.is_active,
                                'is-valid': !$v.create.is_active.$invalid && !errors.is_active,
                            }">
                                <b-form-radio class="d-inline-block" v-model="$v.create.is_active.$model" name="some-radios-1"
                                    :value="1">{{ $t("general.Active") }}</b-form-radio>
                                <b-form-radio class="d-inline-block m-1" v-model="$v.create.is_active.$model"
                                    name="some-radios-1" :value="0">{{ $t("general.Inactive") }}</b-form-radio>
                            </b-form-group>
                            <template v-if="errors.is_active">
                                <ErrorMessage v-for="(errorMessage, index) in errors.is_active" :key="index">{{
                                    errorMessage
                                }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div  class="col-md-6" v-if="isVisible('type') && !(type == 'edit' && this.idObjEdit && this.idObjEdit.dataObj && this.idObjEdit.dataObj.type == 'inpatient')">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("hms_patients_type") }}
                                <span v-if="isRequired('type')" class="text-danger">*</span>
                            </label>
                            <b-form-group :class="{
                                'is-invalid': $v.create.type.$error || errors.type,
                                'is-valid': !$v.create.type.$invalid && !errors.type,
                            }">
                                <b-form-radio class="d-inline-block" v-model="$v.create.type.$model" name="some-radios"
                                    :value="'inpatient'">{{ $t("general.inpatient") }}</b-form-radio>
                                <b-form-radio class="d-inline-block m-1" v-model="$v.create.type.$model"
                                    name="some-radios" :value="'outpatient'">{{ $t("general.outpatient") }}</b-form-radio>
                            </b-form-group>
                            <template v-if="errors.type">
                                <ErrorMessage v-for="(errorMessage, index) in errors.type" :key="index">{{
                                    errorMessage
                                }}</ErrorMessage>
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
import ErrorMessage from "../../../../components/widgets/errorMessage.vue";
import loader from "../../../../components/general/loader";
import { maxLength, minLength, requiredIf ,email} from "vuelidate/lib/validators";
import adminApi from "../../../../api/adminAxios";
import { arabicValue, englishValue } from "../../../../helper/langTransform";
import transMixinComp from "../../../../helper/mixin/translation-comp-mixin";
import successError from "../../../../helper/mixin/success&error";

export default {
    name: "patients_modal",
    components: {
        ErrorMessage,
        loader,
    },
    mixins: [transMixinComp, successError],
    props: {
        id: { default: "create", }, companyKeys: { default: [], }, defaultsKeys: { default: [], },
        isPage: { default: true }, isVisiblePage: { default: null }, isRequiredPage: { default: null },
        type: { default: 'create' }, idObjEdit: { default: null }, isPermission: {}, url: { default: '/h_m_s/patients' }
    },
    data() {
        return {
            fields: [],
            codeCountry: "",
            isCustom: false,
            isLoader: false,
            company_id: null,
            is_active: false,
            create: {
                name: "",
                name_e: "",
                safe_number: "",
                email: "",
                mobile: "",
                whatsapp: "",
                patient_height: "",
                patient_weight: "",
                gender: "",
                address: "",
                blood: "",
                date_of_birth: "",
                code_country: "",
                type: "inpatient",
                password: "",
            },
            errors: {},
            is_disabled: false,
        };
    },
    validations: {
        create: {
            name: {
                required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(2), maxLength: maxLength(100)
            },
            name_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(2), maxLength: maxLength(100)
            },
            address: {
                required: requiredIf(function (model) {
                    return this.isRequired("address");
                }), minLength: minLength(2), maxLength: maxLength(100)
            },
            mobile: {
                required: requiredIf(function (model) {
                    return this.isRequired("mobile");
                }),
            },
            email: {
                required: requiredIf(function (model) {
                    return this.create.type =='outpatient' ? false : this.isRequired("email") ;
                }),
                email,
            },
            whatsapp: {
                required: requiredIf(function (model) {
                    return this.isRequired("whatsapp");
                }),
            },
            date_of_birth: {
                required: requiredIf(function (model) {
                    return this.isRequired("date_of_birth");
                }),
            },
            patient_weight: {
                required: requiredIf(function (model) {
                    return this.isRequired("patient_weight");
                }),
            },
            patient_height: {
                required: requiredIf(function (model) {
                    return this.isRequired("patient_height");
                }),
            },
            blood: {
                required: requiredIf(function (model) {
                    return this.isRequired("blood");
                }),
            },
            type: {
                required: requiredIf(function (model) {
                    return this.isRequired("type");
                }),
            },
            gender: {
                required: requiredIf(function (model) {
                    return this.isRequired("gender");
                }),
            },
            safe_number: {
                required: requiredIf(function (model) {
                    return this.isRequired("safe_number");
                }),
            },
            password: {
                required: requiredIf(function (model) {
                    return this.isRequired("password") && this.type != 'edit';
                }),
                minLength: minLength(8),
            },
            is_active: {}
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        getCustomTableFields() {
            this.isCustom = true;
            adminApi
                .get(`/customTable/table-columns/h_m_s_patients`)
                .then((res) => {
                    this.fields = res.data;
                })
                .catch((err) => {
                    this.errorFun('Error', 'Thereisanerrorinthesystem');
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
            this.create = {
                name: "",
                name_e: "",
                safe_number: "",
                email: "",
                mobile: "",
                whatsapp: "",
                patient_weight: "",
                patient_height: "",
                code_country: "",
                is_active: false,
                gender: "",
                address: "",
                blood: "",
                date_of_birth: "",
                type: "inpatient",
                password: "",
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
            this.codeCountry = this.$store.getters["locationIp/countryCode"];
            setTimeout(() => {
                if (this.type != 'edit') {
                    if (!this.isPage) this.getCustomTableFields();
                } else {
                    if (this.idObjEdit.dataObj) {
                        let patient = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.name = patient.name;
                        this.create.name_e = patient.name_e;
                        this.create.date_of_birth = patient.date_of_birth;
                        this.create.safe_number = patient.safe_number;
                        this.create.email = patient.email;
                        this.create.mobile = patient.mobile;
                        this.create.whatsapp = patient.whatsapp;
                        this.create.patient_height = patient.patient_height;
                        this.create.patient_weight = patient.patient_weight;
                        this.create.gender = patient.gender;
                        this.create.address = patient.address;
                        this.create.blood = patient.blood;
                        this.create.code_country = patient.code_country;
                        this.create.type = patient.type;
                        this.create.is_active = patient.is_active;
                    }
                }
            }, 50);
        },
        resetForm() {
            this.defaultData();
            this.codeCountry = this.$store.getters["locationIp/countryCode"];
        },
        AddSubmit() {
            this.create.code_country = this.codeCountry;
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
                if (this.type != 'edit') {
                    adminApi
                        .post(this.url, { ...this.create, company_id: this.company_id })
                        .then((res) => {
                            this.is_disabled = true;
                            if (!this.isPage)
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
                                this.errorFun('Error', 'Thereisanerrorinthesystem');
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
                                this.successFun('Editsuccessfully');
                            }, 500);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error', 'Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }

            }
        },
        arabicValueName(txt) {
            // this.create.name = arabicValue(txt);
        },
        englishValueName(txt) {
            // this.create.name_e = englishValue(txt);
        },
        updatePhone(e) {
            this.create.phone = e.phoneNumber;
        },

        updateWhatsapp(e) {
            this.create.whatsapp = e.phoneNumber;
        },
    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
