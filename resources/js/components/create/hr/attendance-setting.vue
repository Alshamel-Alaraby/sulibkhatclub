<template>
    <div>
        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit' ?getCompanyKey('attendance_setting_edit_form'):getCompanyKey('attendance_setting_edit_form')"
            title-class="font-18"
            body-class="p-4 "
            size="lg"
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
                        :class="[
                      'font-weight-bold px-2',
                      is_disabled ? 'mx-2' : '',
                    ]"
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
                    <div class="col-md-6" v-if="isVisible('pre_in')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey('attendance_setting_pre_in') }}
                                <span v-if="isRequired('pre_in')" class="text-danger">*</span>
                            </label>
                            <div dir="rtl">
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model="$v.create.pre_in.$model"
                                    :class="{
                                        'is-invalid': $v.create.pre_in.$error || errors.pre_in,
                                        'is-valid': !$v.create.pre_in.$invalid && !errors.pre_in,
                                      }"
                                />
                            </div>
                            <template v-if="errors.pre_in">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.pre_in"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('post_in')">
                        <div class="form-group">
                            <label  class="control-label">
                                {{ getCompanyKey('attendance_setting_post_in') }}
                                <span v-if="isRequired('post_in')" class="text-danger">*</span>
                            </label>
                            <div>
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model="$v.create.post_in.$model"
                                    :class="{
                                        'is-invalid': $v.create.post_in.$error || errors.post_in,
                                        'is-valid': !$v.create.post_in.$invalid && !errors.post_in,
                                      }"
                                />
                            </div>
                            <template v-if="errors.post_in">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.post_in"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('absent_minutes')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey('attendance_setting_absent_minutes') }}
                                <span v-if="isRequired('absent_minutes')" class="text-danger">*</span>
                            </label>
                            <div dir="rtl">
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model="$v.create.absent_minutes.$model"
                                    :class="{
                                        'is-invalid': $v.create.absent_minutes.$error || errors.absent_minutes,
                                        'is-valid': !$v.create.absent_minutes.$invalid && !errors.absent_minutes,
                                      }"
                                />
                            </div>
                            <template v-if="errors.absent_minutes">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.absent_minutes"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('pre_out')">
                        <div class="form-group">
                            <label  class="control-label">
                                {{ getCompanyKey('attendance_setting_pre_out') }}
                                <span v-if="isRequired('pre_out')" class="text-danger">*</span>
                            </label>
                            <div>
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model="$v.create.pre_out.$model"
                                    :class="{
                                        'is-invalid': $v.create.pre_out.$error || errors.pre_out,
                                        'is-valid': !$v.create.pre_out.$invalid && !errors.pre_out,
                                      }"

                                />
                            </div>
                            <template v-if="errors.pre_out">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.pre_out"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('post_out')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey('attendance_setting_post_out') }}
                                <span v-if="isRequired('post_out')" class="text-danger">*</span>
                            </label>
                            <div dir="rtl">
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model="$v.create.post_out.$model"
                                    :class="{
                                        'is-invalid': $v.create.post_out.$error || errors.post_out,
                                        'is-valid': !$v.create.post_out.$invalid && !errors.post_out,
                                      }"
                                />
                            </div>
                            <template v-if="errors.post_out">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.post_out"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('max_out')">
                        <div class="form-group">
                            <label for="field-2" class="control-label">
                                {{ getCompanyKey('attendance_setting_max_out') }}
                                <span v-if="isRequired('max_out')" class="text-danger">*</span>
                            </label>
                            <div>
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model="$v.create.max_out.$model"
                                    :class="{
                                        'is-invalid': $v.create.max_out.$error || errors.max_out,
                                        'is-valid': !$v.create.max_out.$invalid && !errors.max_out,
                                      }"
                                    id="field-2"
                                />
                            </div>
                            <template v-if="errors.max_out">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.max_out"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('location_fingerprint')" class="col-md-6">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("attendance_setting_location_fingerprint") }}
                                <span v-if="isRequired('location_fingerprint')" class="text-danger">*</span>
                            </label>
                            <b-form-group >
                                <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.create.location_fingerprint.$model"
                                    name="location_fingerprint"
                                    value="1"
                                >{{ $t("general.Yes") }}
                                </b-form-radio>
                                <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.create.location_fingerprint.$model"
                                    name="location_fingerprint"
                                    value="0"
                                >{{ $t("general.No") }}
                                </b-form-radio>
                            </b-form-group>
                            <template v-if="errors.location_fingerprint">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.location_fingerprint"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('mobile_id_fingerprint')" class="col-md-6">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("attendance_setting_mobile_id_fingerprint") }}
                                <span v-if="isRequired('mobile_id_fingerprint')" class="text-danger">*</span>
                            </label>
                            <b-form-group>
                                <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.create.mobile_id_fingerprint.$model"
                                    name="mobile_id_fingerprint"
                                    value="1"
                                >{{ $t("general.Yes") }}
                                </b-form-radio>
                                <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.create.mobile_id_fingerprint.$model"
                                    name="mobile_id_fingerprint"
                                    value="0"
                                >{{ $t("general.No") }}
                                </b-form-radio>
                            </b-form-group>
                            <template v-if="errors.mobile_id_fingerprint">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.mobile_id_fingerprint"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('pre_in_fingerprint')" class="col-md-6">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("attendance_setting_pe_in_fingerprint") }}
                                <span v-if="isRequired('pre_in_fingerprint')" class="text-danger">*</span>
                            </label>
                            <b-form-group>
                                <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.create.pre_in_fingerprint.$model"
                                    name="pre_in_fingerprint"
                                    value="1"
                                >{{ $t("general.Yes") }}
                                </b-form-radio>
                                <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.create.pre_in_fingerprint.$model"
                                    name="pre_in_fingerprint"
                                    value="0"
                                >{{ $t("general.No") }}
                                </b-form-radio>
                            </b-form-group>
                            <template v-if="errors.pre_in_fingerprint">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.pre_in_fingerprint"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('day_off_fingerprint')" class="col-md-6">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("attendance_setting_day_off_fingerprint") }}
                                <span v-if="isRequired('day_off_fingerprint')" class="text-danger">*</span>
                            </label>
                            <b-form-group>
                                <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.create.day_off_fingerprint.$model"
                                    name="day_off_fingerprint"
                                    value="1"
                                >{{ $t("general.Yes") }}
                                </b-form-radio>
                                <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.create.day_off_fingerprint.$model"
                                    name="day_off_fingerprint"
                                    value="0"
                                >{{ $t("general.No") }}
                                </b-form-radio>
                            </b-form-group>
                            <template v-if="errors.day_off_fingerprint">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.day_off_fingerprint"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('public_holiday_fingerprint')" class="col-md-6">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("attendance_setting_public_holiday_fingerprint") }}
                                <span v-if="isRequired('public_holiday_fingerprint')" class="text-danger">*</span>
                            </label>
                            <b-form-group>
                                <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.create.public_holiday_fingerprint.$model"
                                    name="public_holiday_fingerprint"
                                    value="1"
                                >{{ $t("general.Yes") }}
                                </b-form-radio>
                                <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.create.public_holiday_fingerprint.$model"
                                    name="public_holiday_fingerprint"
                                    value="0"
                                >{{ $t("general.No") }}
                                </b-form-radio>
                            </b-form-group>
                            <template v-if="errors.location_fingerprint">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.location_fingerprint"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('post_out_fingerprint')" class="col-md-6">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("attendance_setting_post_out_fingerprint") }}
                                <span v-if="isRequired('post_out_fingerprint')" class="text-danger">*</span>
                            </label>
                            <b-form-group>
                                <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.create.post_out_fingerprint.$model"
                                    name="post_out_fingerprint"
                                    value="1"
                                >{{ $t("general.Yes") }}
                                </b-form-radio>
                                <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.create.post_out_fingerprint.$model"
                                    name="post_out_fingerprint"
                                    value="0"
                                >{{ $t("general.No") }}
                                </b-form-radio>
                            </b-form-group>
                            <template v-if="errors.post_out_fingerprint">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.post_out_fingerprint"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('pre_out_fingerprint')" class="col-md-6">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("attendance_setting_pre_out_fingerprint") }}
                                <span v-if="isRequired('pre_out_fingerprint')" class="text-danger">*</span>
                            </label>
                            <b-form-group>
                                <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.create.pre_out_fingerprint.$model"
                                    name="pre_out_fingerprint"
                                    value="1"
                                >{{ $t("general.Yes") }}
                                </b-form-radio>
                                <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.create.pre_out_fingerprint.$model"
                                    name="pre_out_fingerprint"
                                    value="0"
                                >{{ $t("general.No") }}
                                </b-form-radio>
                            </b-form-group>
                            <template v-if="errors.pre_out_fingerprint">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.pre_out_fingerprint"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('holiday_fingerprint')" class="col-md-6">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("attendance_setting_holiday_fingerprint") }}
                                <span v-if="isRequired('holiday_fingerprint')" class="text-danger">*</span>
                            </label>
                            <b-form-group>
                                <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.create.holiday_fingerprint.$model"
                                    name="holiday_fingerprint"
                                    value="1"
                                >{{ $t("general.Yes") }}
                                </b-form-radio>
                                <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.create.holiday_fingerprint.$model"
                                    name="holiday_fingerprint"
                                    value="0"
                                >{{ $t("general.No") }}
                                </b-form-radio>
                            </b-form-group>
                            <template v-if="errors.holiday_fingerprint">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.holiday_fingerprint"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('training_course_fingerprint')" class="col-md-6">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("attendance_setting_training_course_fingerprint") }}
                                <span v-if="isRequired('training_course_fingerprint')" class="text-danger">*</span>
                            </label>
                            <b-form-group>
                                <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.create.training_course_fingerprint.$model"
                                    name="training_course_fingerprint"
                                    value="1"
                                >{{ $t("general.Yes") }}
                                </b-form-radio>
                                <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.create.training_course_fingerprint.$model"
                                    name="training_course_fingerprint"
                                    value="0"
                                >{{ $t("general.No") }}
                                </b-form-radio>
                            </b-form-group>
                            <template v-if="errors.training_course_fingerprint">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.training_course_fingerprint"
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
    </div>
</template>

<script>
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import {maxLength, minLength, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

export default {
    name: "group",
    components: {
        ErrorMessage,
        loader,
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/hr/attendance_settings'}
    },
    data() {
        return {
            fields: [],
            isLoader: false,
            isCustom:false,
            company_id:null,
            create: {
                pre_in: 0,
                post_in: 0,
                absent_minutes: 0,
                pre_out: 0,
                post_out: 0,
                max_out: 0,
                location_fingerprint: 0,
                mobile_id_fingerprint: 0,
                pre_in_fingerprint: 0,
                day_off_fingerprint: 0,
                public_holiday_fingerprint: 0,
                post_out_fingerprint: 0,
                pre_out_fingerprint: 0,
                holiday_fingerprint: 0,
                training_course_fingerprint: 0
            },
            errors: {},
            is_disabled: false,
        };
    },
    validations: {
        create: {
            pre_in: { required: requiredIf(function (model) {
                    return this.isRequired("pre_in");
                }) },
            post_in: { required: requiredIf(function (model) {
                    return this.isRequired("post_in");
                }), },
            pre_out: { required: requiredIf(function (model) {
                    return this.isRequired("pre_out");
                }) },
            post_out: { required: requiredIf(function (model) {
                    return this.isRequired("post_out");
                }), },
            absent_minutes: { required: requiredIf(function (model) {
                    return this.isRequired("absent_minutes");
                }) },
            max_out: { required: requiredIf(function (model) {
                    return this.isRequired("max_out");
                }), },
            location_fingerprint: { required: requiredIf(function (model) {
                    return this.isRequired("location_fingerprint");
                }), },
            mobile_id_fingerprint: { required: requiredIf(function (model) {
                    return this.isRequired("mobile_id_fingerprint");
                }), },
            pre_in_fingerprint: { required: requiredIf(function (model) {
                    return this.isRequired("pre_in_fingerprint");
                }), },
            day_off_fingerprint: { required: requiredIf(function (model) {
                    return this.isRequired("day_off_fingerprint");
                }), },
            public_holiday_fingerprint: { required: requiredIf(function (model) {
                    return this.isRequired("public_holiday_fingerprint");
                }), },
            post_out_fingerprint: { required: requiredIf(function (model) {
                    return this.isRequired("post_out_fingerprint");
                }), },
            pre_out_fingerprint: { required: requiredIf(function (model) {
                    return this.isRequired("pre_out_fingerprint");
                }), },
            holiday_fingerprint: { required: requiredIf(function (model) {
                    return this.isRequired("holiday_fingerprint");
                }), },
            training_course_fingerprint: { required: requiredIf(function (model) {
                    return this.isRequired("training_course_fingerprint");
                }), },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/hr_attendance_settings`)
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
                pre_in: 0,
                post_in: 0,
                absent_minutes: 0,
                pre_out: 0,
                post_out: 0,
                max_out: 0,
                location_fingerprint: 0,
                mobile_id_fingerprint: 0,
                pre_in_fingerprint: 0,
                day_off_fingerprint: 0,
                public_holiday_fingerprint: 0,
                post_out_fingerprint: 0,
                pre_out_fingerprint: 0,
                holiday_fingerprint: 0,
                training_course_fingerprint: 0
            };
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
            setTimeout( async () => {
                if(this.type != 'edit'){
                    if(!this.isPage) await  this.getCustomTableFields();
                }else {
                    if(this.idObjEdit.dataObj){
                        let group = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.pre_in = group.pre_in;
                        this.create.post_in = group.post_in;
                        this.create.absent_minutes = group.absent_minutes;
                        this.create.pre_out = group.pre_out;
                        this.create.post_out = group.post_out;
                        this.create.max_out = group.max_out;
                        this.create.location_fingerprint= group.location_fingerprint;
                        this.create.mobile_id_fingerprint = group.mobile_id_fingerprint;
                        this.create.pre_in_fingerprint = group.pre_in_fingerprint;
                        this.create.day_off_fingerprint = group.day_off_fingerprint;
                        this.create.public_holiday_fingerprint = group.public_holiday_fingerprint;
                        this.create.post_out_fingerprint = group.post_out_fingerprint;
                        this.create.pre_out_fingerprint = group.pre_out_fingerprint;
                        this.create.holiday_fingerprint = group.holiday_fingerprint;
                        this.create.training_course_fingerprint = group.training_course_fingerprint;
                    }
                }
            },50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                if(this.type != 'edit'){
                    adminApi
                        .post(this.url, {...this.create,company_id:this.company_id})
                        .then((res) => {
                            this.is_disabled = true;
                            if(!this.isPage)
                                this.$emit("created");
                            else
                                this.$emit("getDataTable");
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
        }
    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
