<template>
    <div>
        <Department :id="'create_department_task_tasks'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getDepartment" />
        <!--  create   -->
        <b-modal
            id="create-task"
            :title="getCompanyKey('boardRent_task_create_form')"
            title-class="font-18"
            body-class="p-4 "
            size="lg"
            :hide-footer="true"
            @show="resetModal"
            @hidden="resetModalHidden"
        >
            <form>
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
                    <b-button variant="danger" type="button" @click.prevent="resetModalHidden">
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("boardRent_task_department") }}
                                <span class="text-danger">*</span>
                            </label>
                            <multiselect
                                @input="showDepartmentModal" v-model="createTask.department_id"
                                :options="departments.map((type) => type.id)"
                                :custom-label="(opt) => $i18n.locale == 'ar' ? departments.find((x) => x.id == opt).name : departments.find((x) => x.id == opt).name_e"
                                :class="{'is-invalid': $v.createTask.department_id.$error || errors.department_id,'is-valid': !$v.createTask.department_id.$invalid && !errors.department_id,}"
                            >
                            </multiselect>
                            <div v-if="$v.createTask.department_id.$error || errors.department_id" class="text-danger">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.department_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.department_id" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6"></div>
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
                            <div v-if="!$v.createTask.name_e.maxLength" class="invalid-feedback">
                                {{ $t("general.Itmustbeatmost") }}
                                {{ $v.createTask.name_e.$params.maxLength.max }}
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
            </form>
        </b-modal>
        <!--  /create   -->
    </div>

</template>

<script>
import ErrorMessage from "../widgets/errorMessage";
import loader from "../loader";
import {maxLength, minLength, requiredIf,required} from "vuelidate/lib/validators";
import adminApi from "../../api/adminAxios";
import Swal from "sweetalert2";
import {arabicValue, englishValue} from "../../helper/langTransform";
import transMixinComp from "../../helper/translation-comp-mixin";
import Department from "./department";
import Multiselect from "vue-multiselect";

export default {
    name: "departmentTask",
    data() {
        return {
            departments: [],
            isLoader: false,
            company_id: null,
            createTask: {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                department_id: null,
            },
            errors: {},
            tasks: [],
            is_disabled: false,
            is_disabledTask: false,
        };
    },
    validations: {
        createTask: {
            name: { required, minLength: minLength(2), maxLength: maxLength(255) },
            name_e: { required, minLength: minLength(2), maxLength: maxLength(255) },
            description: {required},
            description_e: {required},
            department_id: {required},
        },
    },
    props: {
        department_id:{
            default:null
        },
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
        Department,
        Multiselect
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.getDepartment();
        this.createTask.department_id = this.department_id;
    },
    methods: {
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
            this.createTask = {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                department_id : null,
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.is_disabledTask = false;
            this.errors = {};
            this.$bvModal.hide(`create-task`);
        },
        /**
         *  hidden Modal (create)
         */
        resetModal() {
            this.createTask = {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                department_id:this.department_id,
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        resetFormTask(){
            this.createTask = {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                department_id:this.department_id,
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabledTask = false;
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
                    .post(`/department-tasks`, {...this.createTask ,company_id: this.company_id})
                    .then((res) => {
                        this.is_disabledTask = true;
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
        async getDepartment() {
            this.isLoader = true;
            await adminApi
                .get(`/depertments`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف قسم", name_e: "Add Department" });
                    this.departments = l;
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
        async showDepartmentModal() {
            if (this.createTask.department_id == 0) {
                this.$bvModal.show("create_department_task_tasks");
                this.createTask.department_id = null;
            }
        },
    }
}
</script>

<style scoped>

</style>
