<template>
    <div>
        <!--  create   -->
        <b-modal :id="id"
            :title="type != 'edit' ? getCompanyKey('hms_doctors_create_form') : getCompanyKey('hms_doctors_edit_form')"
            title-class="font-18" body-class="p-4 " size="xl" :hide-footer="true" @show="resetModal"
            @hidden="resetModalHidden">
            <form>
                <loader size="large" v-if="isCustom && !isPage" />
                <div class="mb-3 d-flex justify-content-start">
                    <b-button v-if="type != 'edit'" variant="success" :disabled="!is_disabled" @click.prevent="resetForm"
                        type="button" :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                        {{ $t("general.AddNewRecord") }}
                    </b-button>
                    <template v-if="!is_disabled">
                        <b-button variant="success" type="button" class="mx-1" @click.prevent="AddSubmit" v-if="!isLoader">
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
                <h4 v-if="errors && Object.keys(errors ?? []).length">{{ $t('general.There is an error in data') }}</h4>
                <b-tabs nav-class="nav-tabs nav-bordered w-100">
                    <b-tab :title="$t('general.Doctor')" active>
                        <div class="row">
                            <div class="col-md-4" v-if="isVisible('name')">
                                <div class="form-group">
                                    <label for="field-1" class="control-label">
                                        {{ getCompanyKey('hms_doctors_name_ar') }}
                                        <span v-if="isRequired('name')" class="text-danger">*</span>
                                    </label>
                                    <div dir="rtl">
                                        <input type="text" class="form-control arabicInput"
                                            @keyup="arabicValueName(create.name)" v-model="$v.create.name.$model" :class="{
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
                                        <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index">{{
                                            errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-4" v-if="isVisible('name_e')">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">
                                        {{ getCompanyKey('hms_doctors_name_en') }}
                                        <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                    </label>
                                    <div dir="ltr">
                                        <input type="text" class="form-control englishInput" data-create="2"
                                            @keyup="englishValueName(create.name_e)" v-model="$v.create.name_e.$model"
                                            :class="{
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
                                        <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index">{{
                                            errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-4" v-if="isVisible('email')">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("hms_doctors_email") }}
                                        <span v-if="isRequired('email')" class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" data-create="9" v-model="$v.create.email.$model"
                                        :class="{
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
                            <div class="col-md-4" v-if="isVisible('mobile')">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("hms_doctors_mobile") }}
                                        <span v-if="isRequired('phone')" class="text-danger">*</span>
                                    </label>
                                    <VuePhoneNumberInput v-model="$v.create.mobile.$model"
                                        :default-country-code="codeCountry" valid-color="#28a745" error-color="#dc3545"
                                        :preferred-countries="['FR', 'EG', 'DE']" @update="updatePhone" />
                                    <div v-if="$v.create.mobile.$error || errors.mobile" class="text-danger">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.mobile">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.mobile" :key="index">{{
                                            errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-4" v-if="isVisible('whatsapp')">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("hms_doctors_whatsapp") }}
                                        <span v-if="isRequired('whatsapp')" class="text-danger">*</span>
                                    </label>
                                    <VuePhoneNumberInput v-model="$v.create.whatsapp.$model"
                                        :default-country-code="codeCountry" valid-color="#28a745" error-color="#dc3545"
                                        :preferred-countries="['FR', 'EG', 'DE']" @update="updateWhatsapp" />
                                    <div v-if="$v.create.whatsapp.$error || errors.whatsapp" class="text-danger">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.whatsapp">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.whatsapp" :key="index">{{
                                            errorMessage
                                        }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-4" v-if="isVisible('specialty_id')">
                                <div class="form-group">
                                    <label>
                                        {{ getCompanyKey("hms_doctors_specialty") }}
                                    </label>
                                    <multiselect v-model="create.specialty_id" :options="specialties.map((type) => type.id)"
                                        :custom-label="(opt) => specialties.find((x) => x.id == opt) ?
                                            $i18n.locale == 'ar'
                                                ? specialties.find((x) => x.id == opt).name
                                                : specialties.find((x) => x.id == opt).name_e : null"
                                        :class="{ 'is-invalid': errors.specialty_id || $v.create.specialty_id.$error }">
                                    </multiselect>
                                    <div v-if="$v.create.specialty_id.$error" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.specialty_id">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.specialty_id" :key="index">{{
                                            errorMessage
                                        }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-4" v-if="isVisible('selling_items_commission')">
                                <div class="form-group">
                                    <label for="field-302" class="control-label">
                                        {{
                                            getCompanyKey(
                                                "hms_doctors_selling_items_commission"
                                            )
                                        }}
                                        <span v-if="isRequired('selling_items_commission')" class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" data-create="1"
                                        v-model="$v.create.selling_items_commission.$model" :class="{
                                            'is-invalid':
                                                $v.create.selling_items_commission.$error ||
                                                errors.selling_items_commission,
                                            'is-valid':
                                                !$v.create.selling_items_commission.$invalid &&
                                                !errors.selling_items_commission,
                                        }" id="field-302" />
                                </div>
                            </div>
                            <div class="col-md-4" v-if="isVisible('transfer_patient_commission')">
                                <div class="form-group">
                                    <label for="field-303" class="control-label">
                                        {{
                                            getCompanyKey(
                                                "hms_doctors_transfer_patient_commission"
                                            )
                                        }}
                                        <span v-if="isRequired('transfer_patient_commission')" class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" data-create="1"
                                        v-model="$v.create.transfer_patient_commission.$model" :class="{
                                            'is-invalid':
                                                $v.create.transfer_patient_commission.$error ||
                                                errors.transfer_patient_commission,
                                            'is-valid':
                                                !$v.create.transfer_patient_commission.$invalid &&
                                                !errors.transfer_patient_commission,
                                        }" id="field-303" />
                                </div>
                            </div>
                            <div class="col-md-4" v-if="isVisible('medical_commission')">
                                <div class="form-group">
                                    <label for="field-304" class="control-label">
                                        {{
                                            getCompanyKey(
                                                "hms_doctors_medical_commission"
                                            )
                                        }}
                                        <span v-if="isRequired('medical_commission')" class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" data-create="1"
                                        v-model="$v.create.medical_commission.$model" :class="{
                                            'is-invalid':
                                                $v.create.medical_commission.$error ||
                                                errors.medical_commission,
                                            'is-valid':
                                                !$v.create.medical_commission.$invalid &&
                                                !errors.medical_commission,
                                        }" id="field-304" />
                                </div>
                            </div>

                            <div class="col-md-4" v-if="isVisible('password')">
                                <div class="form-group">
                                    <label for="field-15" class="control-label">
                                        {{ getCompanyKey("hms_doctors_password") }}
                                        <span v-if="isRequired('password')" class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" data-create="1"
                                        v-model="$v.create.password.$model" :class="{
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
                                        <ErrorMessage v-for="(errorMessage, index) in errors.password" :key="index">{{
                                            errorMessage
                                        }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>

                            <div v-if="isVisible('is_active')" class="col-md-4">
                                <div class="form-group">
                                    <label class="mr-2">
                                        {{ getCompanyKey("hms_doctors_is_active") }}
                                        <span v-if="isRequired('is_active')" class="text-danger">*</span>
                                    </label>
                                    <b-form-group :class="{
                                        'is-invalid': $v.create.is_active.$error || errors.is_active,
                                        'is-valid': !$v.create.is_active.$invalid && !errors.is_active,
                                    }">
                                        <b-form-radio class="d-inline-block" v-model="$v.create.is_active.$model"
                                            name="some-radios" :value="1">{{ $t("general.Active") }}</b-form-radio>
                                        <b-form-radio class="d-inline-block m-1" v-model="$v.create.is_active.$model"
                                            name="some-radios" :value="0">{{ $t("general.Inactive") }}</b-form-radio>
                                    </b-form-group>
                                    <template v-if="errors.is_active">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.is_active" :key="index">{{
                                            errorMessage
                                        }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>

                        </div>

                    </b-tab>
                    <b-tab :title="$t('general.Work Times')">

                        <div class="card shadow mb-4 col-12 p-2">
                            <div class="card-header d-flex justify-content-between">
                                <h4>{{ getCompanyKey('hms_doctors_work_times') }}</h4>
                                <button @click.prevent="add_new_time" class="btn btn-primary me-2">
                                    <i class="fa fa-plus"></i>
                                    {{ $t('general.AddNewTime') }}
                                </button>
                            </div>

                            <div class="card-body">

                                <div class="row col-12">
                                    <div class="col-md-2">
                                        <label>
                                            {{ getCompanyKey("hms_doctors_branch") }}
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>
                                            {{ getCompanyKey("hms_doctors_room") }}
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>
                                            {{ getCompanyKey("hms_doctors_day") }}
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>
                                            {{ getCompanyKey("hms_doctors_from") }}
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>
                                            {{ getCompanyKey("hms_doctors_to") }}
                                        </label>
                                    </div>
                                </div>
                                <div class="row col-12" v-for="(time, index) in create.work_times" :key="index">
                                    <div class="col-md-2">
                                        <div class="form-group">

                                            <multiselect v-model="create.work_times[index].branch_id" placeholder="" selectLabel="" selectLabel=""
                                                :options="branches.map((type) => type.id)"
                                                @select="get_rooms_by_branch(i, index)" :custom-label="(opt) => branches.find((x) => x.id == opt) ?
                                                    $i18n.locale == 'ar'
                                                        ? branches.find((x) => x.id == opt).name
                                                        : branches.find((x) => x.id == opt).name_e : null">
                                            </multiselect>

                                            <template v-if="errors && errors[`work_times.${index}.branch_id`]">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors[`work_times.${index}.branch_id`]"
                                                    :key="index">{{ errorMessage
                                                    }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">


                                            <input type="text"
                                                :value="$i18n.locale == 'ar' ? create.work_times[index].room.name : create.work_times[index].room.name_e"
                                                class="form-control"
                                                v-if="create.work_times[index].room_id && Object.keys(create.work_times[index].room ?? []).length && rooms[index].length == 0"
                                                disabled>
                                            <template v-else>
                                                <multiselect v-model="create.work_times[index].room_id" placeholder="" selectLabel="" deselectLabel=""
                                                    :options="rooms[index].map((type) => type.id)" :custom-label="(opt) => rooms[index].find((x) => x.id == opt) ?
                                                        $i18n.locale == 'ar'
                                                            ? rooms[index].find((x) => x.id == opt).name
                                                            : rooms[index].find((x) => x.id == opt).name_e : null">
                                                </multiselect>
                                            </template>


                                            <template v-if="errors && errors[`work_times.${index}.room_id`]">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors[`work_times.${index}.room_id`]"
                                                    :key="index">{{ errorMessage
                                                    }}
                                                </ErrorMessage>
                                            </template>

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">

                                            <multiselect v-model="create.work_times[index].day_id" placeholder="" selectLabel="" deselectLabel=""
                                                :options="days.map((type) => type.id)" :custom-label="(opt) => days.find((x) => x.id == opt) ?
                                                    $i18n.locale == 'ar'
                                                        ? days.find((x) => x.id == opt).name
                                                        : days.find((x) => x.id == opt).name_e : null">
                                            </multiselect>
                                            <template v-if="errors && errors[`work_times.${index}.day_id`]">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors[`work_times.${index}.day_id`]"
                                                    :key="index">{{ errorMessage
                                                    }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="col-md-2">

                                        <input type="time" class="form-control" v-model="create.work_times[index].from">
                                        <template v-if="errors && errors[`work_times.${index}.from`]">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors[`work_times.${index}.from`]"
                                                :key="index">{{ errorMessage
                                                }}
                                            </ErrorMessage>
                                        </template>
                                    </div>

                                    <div class="col-md-2">

                                        <input type="time" class="form-control" v-model="create.work_times[index].to">
                                        <template v-if="errors && errors[`work_times.${index}.to`]">
                                            <ErrorMessage v-for="(errorMessage, index) in errors[`work_times.${index}.to`]"
                                                :key="index">{{ errorMessage
                                                }}
                                            </ErrorMessage>
                                        </template>

                                    </div>

                                    <div class="col-md-2 d-flex  justify-content-center align-items-center">

                                        <button @click.prevent="delete_time(index)"
                                            class="btn btn-danger mx-2 col-6">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
                                    </div>

                                    <template v-if="errors && errors[`work_times.${index}`]">
                                        <div class="mx-s">
                                            <ErrorMessage v-for="(errorMessage, index) in errors[`work_times.${index}`]"
                                                :key="index">{{
                                                    $t(`general.` + errorMessage) }}</ErrorMessage>
                                        </div>

                                    </template>


                                </div>
                            </div>

                        </div>

                    </b-tab>
                </b-tabs>

            </form>
        </b-modal>
    </div>
</template>

<script>
import loader from "../../../../components/general/loader";
import { maxLength, minLength, requiredIf, email } from "vuelidate/lib/validators";
import adminApi from "../../../../api/adminAxios";
import { arabicValue, englishValue } from "../../../../helper/langTransform";
import transMixinComp from "../../../../helper/mixin/translation-comp-mixin";
import successError from "../../../../helper/mixin/success&error";
import ErrorMessage from "../../../../components/widgets/errorMessage.vue";

import Multiselect from "vue-multiselect";
export default {
    name: "doctors_modal",
    components: {
        Multiselect,
        ErrorMessage,
        loader,
    },
    mixins: [transMixinComp, successError],
    props: {
        id: { default: "create", }, companyKeys: { default: [], }, defaultsKeys: { default: [], },
        isPage: { default: true }, isVisiblePage: { default: null }, isRequiredPage: { default: null },
        type: { default: 'create' }, idObjEdit: { default: null }, isPermission: {}, url: { default: '/h_m_s/doctors' }, specialties: { Array, default: [] }, branches: { Array, default: [] }, days: { Array, default: [] }
    },
    data() {
        return {
            fields: [],
            rooms: [[]],
            codeCountry: "",
            isCustom: false,
            isLoader: false,
            company_id: null,
            create: {
                name: "",
                name_e: "",
                mobile: null,
                email: "",
                whatsapp: null,
                specialty_id: null,
                password: "",
                code_country: "",
                selling_items_commission: 0,
                transfer_patient_commission: 0,
                medical_commission: 0,
                is_active: false,
                work_times: []
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
            mobile: {
                required: requiredIf(function (model) {
                    return this.isRequired("mobile");
                }),
            },
            email: {
                required: requiredIf(function (model) {
                    return this.isRequired("email");
                }),
                email,
            },
            whatsapp: {
                required: requiredIf(function (model) {
                    return this.isRequired("whatsapp");
                }),
            },
            specialty_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("specialty_id");
                }),
            },
            selling_items_commission: {
                required: requiredIf(function (model) {
                    return this.isRequired("selling_items_commission");
                }),
            },
            transfer_patient_commission: {
                required: requiredIf(function (model) {
                    return this.isRequired("transfer_patient_commission");
                }),
            },
            medical_commission: {
                required: requiredIf(function (model) {
                    return this.isRequired("medical_commission");
                }),
            },
            password: {
                required: requiredIf(function (model) {
                    return this.isRequired("password") && this.type != 'edit';
                }),
                minLength: minLength(8),
            },
            work_times: [],
            is_active: {}
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        add_new_time() {
            this.rooms.push([]);
            this.create.work_times.push({
                day_id: null,
                branch_id: null,
                room_id: null,
                day_id: null,
                from: '',
                to: '',
            })
        },
        delete_time(index) {
            this.create.work_times.splice(index, 1);
            this.rooms.splice(index, 1);
        },
        get_rooms_by_branch(i, index) {
            console.log(i, index)
            adminApi
                .get(`/h_m_s/rooms?branch_id=${this.create.work_times[index].branch_id}`)
                .then((res) => {
                    this.$set(this.rooms, index, res.data.data)
                })
                .catch((err) => {
                    this.errorFun('Error', 'Thereisanerrorinthesystem');
                })

        },
        getCustomTableFields() {
            this.isCustom = true;
            adminApi
                .get(`/customTable/table-columns/h_m_s_doctors`)
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
                mobile: null,
                email: "",
                password: "",
                selling_items_commission: 0,
                transfer_patient_commission: 0,
                medical_commission: 0,
                whatsapp: null,
                is_active: false,
                work_times: []
            };
            this.rooms = [[]];
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
                        let doctor = this.idObjEdit.dataObj;
                        this.rooms = [];
                        this.errors = {};
                        this.create.name = doctor.name;
                        this.create.name_e = doctor.name_e;
                        this.create.mobile = doctor.mobile;
                        this.create.email = doctor.email;
                        this.create.whatsapp = doctor.whatsapp;
                        this.create.specialty_id = doctor.specialty_id;
                        this.create.is_active = doctor.is_active;
                        this.create.code_country = doctor.code_country;
                        this.create.analytic_account = doctor.analytic_account;
                        this.create.selling_items_commission = doctor.selling_items_commission;
                        this.create.transfer_patient_commission = doctor.transfer_patient_commission;
                        this.create.medical_commission = doctor.medical_commission;
                        let work_times = []
                        doctor.work_times.forEach(element => {
                            this.rooms.push([])
                            work_times.push({
                                room: element.room,
                                day_id: element.day_id,
                                branch_id: element.branch_id,
                                room_id: element.room_id,
                                day_id: element.day_id,
                                from: element.from,
                                to: element.to,
                            })
                        });
                        this.create.work_times = work_times
                    }
                }
            }, 50);
        },
        resetForm() {
            this.defaultData();
            this.codeCountry = this.$store.getters["locationIp/countryCode"];
        },
        async AddSubmit() {
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
                    await adminApi
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
                    this.isLoader = false;

                } else {
                    await adminApi
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

                    this.isLoader = false;

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
