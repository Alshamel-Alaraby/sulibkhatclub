<template>
    <!--  create   -->
    <b-modal
        id="create-department-task"
        :title="getCompanyKey('department_create_form')"
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
            <b-tabs nav-class="nav-tabs nav-bordered">
                <b-tab :title="$t('general.DataEntry')" active>
                    <div class="row justify-content-center">
                        <div class="col-md-8" v-if="isVisible('name')">
                            <div class="form-group">
                                <label for="field-1" class="control-label">
                                    {{ getCompanyKey("department_name_ar") }}
                                    <span v-if="isRequired('name')"  class="text-danger">*</span>
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
                        <div class="col-md-8" v-if="isVisible('name_e')">
                            <div class="form-group">
                                <label for="field-2" class="control-label">
                                    {{ getCompanyKey("department_name_en") }}
                                    <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                </label>
                                <div dir="ltr">
                                    <input
                                        type="text"
                                        class="form-control"
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
                    </div>
                </b-tab>
                <b-tab
                    :title="$t('general.tasks')"
                    :disabled="!department_id"
                >
                    <div class="mb-2 d-flex justify-content-end">
                        <b-button
                            variant="success"
                            :disabled="!is_disabledTask"
                            @click.prevent="resetFormTask"
                            type="button"
                            :class="['font-weight-bold px-2', is_disabledTask ? 'mx-2' : '']"
                        >
                            {{ $t("general.AddNewRecord") }}
                        </b-button>
                        <template v-if="!is_disabledTask">
                            <b-button
                                variant="success"
                                type="button"
                                class="mx-1"
                                v-if="!isLoader"
                                @click.prevent="AddSubmitTask"
                            >
                                {{ $t("general.Add") }}
                            </b-button>

                            <b-button variant="success" class="mx-1" disabled v-else>
                                <b-spinner small></b-spinner>
                                <span class="sr-only">{{ $t("login.Loading") }}...</span>
                            </b-button>
                        </template>
                        <!-- Emulate built in modal footer ok and cancel button actions -->
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fieldTask-1" class="control-label">
                                    {{ getCompanyKey("boardRent_task_name_ar") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div dir="rtl">
                                    <input
                                        @keyup="arabicValueTask(createTask.name)"
                                        type="text"
                                        class="form-control"
                                        data-create="1"
                                        v-model="$v.createTask.name.$model"
                                        :class="{
                                                'is-invalid': $v.createTask.name.$error || errors.name,
                                                'is-valid': !$v.createTask.name.$invalid && !errors.name,
                                              }"
                                        id="fieldTask-1"
                                    />
                                </div>
                                <div v-if="!$v.createTask.name.minLength" class="invalid-feedback">
                                    {{ $t("general.Itmustbeatleast") }}
                                    {{ $v.createTask.name.$params.minLength.min }}
                                    {{ $t("general.letters") }}
                                </div>
                                <div v-if="!$v.createTask.name.maxLength" class="invalid-feedback">
                                    {{ $t("general.Itmustbeatmost") }}
                                    {{ $v.createTask.name.$params.maxLength.max }}
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fieldTask-2" class="control-label">
                                    {{ getCompanyKey("boardRent_task_name_en") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div dir="ltr">
                                    <input
                                        @keyup="englishValueTask(createTask.name_e)"
                                        type="text"
                                        class="form-control englishInput"
                                        data-create="2"
                                        v-model="$v.createTask.name_e.$model"
                                        :class="{
                            'is-invalid': $v.createTask.name_e.$error || errors.name_e,
                            'is-valid': !$v.createTask.name_e.$invalid && !errors.name_e,
                          }"
                                        id="fieldTask-2"
                                    />
                                </div>
                                <div v-if="!$v.createTask.name_e.minLength" class="invalid-feedback">
                                    {{ $t("general.Itmustbeatleast") }}
                                    {{ $v.createTask.name_e.$params.minLength.min }}
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="mr-2">
                                    {{ getCompanyKey('boardRent_task_description_ar') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea @input="arabicValueDescriptionTask(createTask.description)" v-model="$v.createTask.description.$model" class="form-control" :maxlength="1000" rows="5"></textarea>
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
                                    {{ getCompanyKey('boardRent_task_description_en') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea @input="englishValueDescriptionTask(createTask.description_e)" v-model="$v.createTask.description_e.$model" class="form-control" :maxlength="1000" rows="5"></textarea>
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
                </b-tab>
            </b-tabs>
        </form>
    </b-modal>
    <!--  /create   -->
</template>

<script>
import ErrorMessage from "../widgets/errorMessage";
import loader from "../loader";
import {maxLength, minLength, requiredIf,required} from "vuelidate/lib/validators";
import adminApi from "../../api/adminAxios";
import Swal from "sweetalert2";
import {arabicValue, englishValue} from "../../helper/langTransform";
import transMixinComp from "../../helper/translation-comp-mixin";

export default {
    name: "departmentTask",
    data() {
        return {
            fields: [],
            per_page: 50,
            isLoader: false,
            create: {
                name: "",
                name_e: "",
            },
            company_id: null,
            createTask: {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
            },
            department_id: null,
            errors: {},
            tasks: [],
            is_disabled: false,
            is_disabledTask: false,
        };
    },
    validations: {
        create: {
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }) , minLength: minLength(3), maxLength: maxLength(100) },
            name_e: { required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(3), maxLength: maxLength(100) },
        },
        createTask: {
            name: { required, minLength: minLength(2), maxLength: maxLength(255) },
            name_e: { required, minLength: minLength(2), maxLength: maxLength(255) },
            description: {required},
            description_e: {required},
        },
    },
    props: {
        companyKeys:{
            default:[]
        },
        defaultsKeys:{
            default:[]
        },
    },
    mixins: [transMixinComp],
    components: {
        ErrorMessage,
        loader,
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.getCustomTableFields();
    },
    methods: {
        getCustomTableFields() {
            adminApi
                .get(`/customTable/table-columns/general_departments`)
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
        arabicValueTask(txt) {
            this.createTask.name = arabicValue(txt);
        },
        englishValueTask(txt) {
            this.createTask.name_e = englishValue(txt);
        },
        arabicValueDescriptionTask(txt){
            this.createTask.description = arabicValue(txt);
        },
        englishValueDescriptionTask(txt){
            this.createTask.description_e = englishValue(txt);
        },
        resetModalHidden() {
            this.create = { name: "", name_e: ""};
            this.createTask = {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.department_id = null;
            this.is_disabled = false;
            this.is_disabledTask = false;
            this.errors = {};
            this.$bvModal.hide(`create-department-task`);
        },
        arabicValueName(txt){
            this.create.name = arabicValue(txt);
        },
        englishValueName(txt){
            this.create.name_e = englishValue(txt);
        },
        /**
         *  hidden Modal (create)
         */
        resetModal() {
            this.create = { name: "", name_e: ""};
            this.createTask = {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        resetForm() {
            this.create = { name: "", name_e: "" };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.department_id = null;
            this.is_disabled = false;
            this.is_disabledTask = false;
            this.errors = {};
        },
        resetFormTask(){
            this.createTask = {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabledTask = false;
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
                    .post(`/depertments`, { ...this.create, company_id: this.company_id })
                    .then((res) => {
                        this.is_disabled = true;
                        this.department_id = res.data.data.id;
                        this.$emit('create');
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
        AddSubmitTask() {
            if (!this.createTask.name) {
                this.createTask.name = this.createTask.name_e;
            }
            if (!this.createTask.name_e) {
                this.createTask.name_e = this.createTask.name;
            }
            if(!this.createTask.description){ this.createTask.description = this.createTask.description_e}
            if(!this.createTask.description_e){ this.createTask.description_e = this.createTask.description}
            this.$v.createTask.$touch();

            if (this.$v.createTask.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                adminApi
                    .post(`/department-tasks`, {...this.createTask,department_id: this.department_id ,company_id: this.company_id})
                    .then((res) => {
                        this.is_disabledTask = true;
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
    }
}
</script>

<style scoped>

</style>
