<template>
    <div>
        <employee
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            :isPage="false"
            type="create"
            :isPermission="isPermission"
            :id="'employee-create-car-drive'"
            @created="getEmployees"
        />
        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit' ?getCompanyKey('car_car_drive_create_form'):getCompanyKey('car_car_drive_edit_form')"
            title-class="font-18"
            body-class="p-4 "
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
                    <div class="col-md-12" v-if="isVisible('employee_id')">
                        <div class="form-group">
                            <label
                            >{{ getCompanyKey("employee") }}
                                <span
                                    v-if="isRequired('employee_id')"
                                    class="text-danger"
                                >*</span
                                ></label
                            >

                            <multiselect
                                @input="showEmployeeModal"
                                v-model="create.employee_id"
                                :options="employees.map((type) => type.id)"
                                :custom-label="
                          (opt) => employees.find((x) => x.id == opt)?
                            $i18n.locale == 'ar'
                              ? employees.find((x) => x.id == opt).name
                              : employees.find((x) => x.id == opt).name_e: null
                        "
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.employee_id.$error || errors.employee_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('driving_license')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("car_car_drive_driving_license") }}
                                <span v-if="isRequired('driving_license')" class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="$v.create.driving_license.$model"
                                :class="{
                                  'is-invalid':
                                    $v.create.driving_license.$error ||
                                    errors.driving_license,
                                  'is-valid':
                                    !$v.create.driving_license.$invalid &&
                                    !errors.driving_license,
                                }"
                            />
                            <template v-if="errors.driving_license">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.driving_license"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('license_exp_date')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("car_car_drive_license_exp_date") }}
                                <span v-if="isRequired('license_exp_date')" class="text-danger">*</span>
                            </label>
                            <date-picker
                                type="date"
                                v-model="create.license_exp_date"
                                format="YYYY-MM-DD"
                                valueType="format"
                                :confirm="false"
                            ></date-picker>
                            <div
                                v-if="$v.create.license_exp_date.$error || errors.license_exp_date"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.license_exp_date">
                                <ErrorMessage v-for="(errorMessage,index) in errors.license_exp_date"
                                              :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('daily_price')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("car_car_daily_price") }}
                                <span v-if="isRequired('daily_price')" class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                step="0.01"
                                v-model.number="$v.create.daily_price.$model"
                                :class="{
                                  'is-invalid':
                                    $v.create.daily_price.$error ||
                                    errors.daily_price,
                                  'is-valid':
                                    !$v.create.daily_price.$invalid &&
                                    !errors.daily_price,
                                }"
                            />
                            <template v-if="errors.daily_price">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.daily_price"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('weekly_price')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("car_car_weekly_price") }}
                                <span v-if="isRequired('weekly_price')" class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                step="0.01"
                                v-model.number="$v.create.weekly_price.$model"
                                :class="{
                                  'is-invalid':
                                    $v.create.weekly_price.$error ||
                                    errors.weekly_price,
                                  'is-valid':
                                    !$v.create.weekly_price.$invalid &&
                                    !errors.weekly_price,
                                }"
                            />
                            <template v-if="errors.weekly_price">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.weekly_price"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('monthly_price')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("car_car_monthly_price") }}
                                <span v-if="isRequired('monthly_price')" class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                step="0.01"
                                v-model.number="$v.create.monthly_price.$model"
                                :class="{
                                  'is-invalid':
                                    $v.create.monthly_price.$error ||
                                    errors.monthly_price,
                                  'is-valid':
                                    !$v.create.monthly_price.$invalid &&
                                    !errors.monthly_price,
                                }"
                            />
                            <template v-if="errors.monthly_price">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.monthly_price"
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
import {maxLength, minLength, minValue, required, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";
import Employee from "../general/employee";
import Swal from "sweetalert2";

export default {
    name: "group",
    components: {
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
        Employee
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/car-rent/car-drivers'}
    },
    data() {
        return {
            fields: [],
            isLoader: false,
            isCustom:false,
            company_id:null,
            employees: [],
            create: {
                employee_id: null,
                driving_license: "",
                license_exp_date: "",
                daily_price: 0,
                weekly_price: 0,
                monthly_price: 0
            },
            errors: {},
            is_disabled: false,
        };
    },
    validations: {
        create: {
            employee_id: { required: requiredIf(function (model) {
                    return this.isRequired("employee_id");
                }) },
            driving_license: { required: requiredIf(function (model) {
                    return this.isRequired("driving_license");
                }), maxLength: maxLength(100) },
            license_exp_date: { required: requiredIf(function (model) {
                    return this.isRequired("license_exp_date");
                })},
            daily_price: { required: requiredIf(function (model) {
                    return this.isRequired("daily_price");
                }),minValue: minValue(0)},
            weekly_price: { required: requiredIf(function (model) {
                    return this.isRequired("weekly_price");
                }),minValue: minValue(0)},
            monthly_price: { required: requiredIf(function (model) {
                    return this.isRequired("monthly_price");
                }),minValue: minValue(0)},
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/car-drivers`)
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
                employee_id: null,
                driving_license: "",
                license_exp_date: "",
                daily_price: 0,
                weekly_price: 0,
                monthly_price: 0
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
                    this.getEmployees();
                }else {
                    if(this.idObjEdit.dataObj){
                        let group = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.employee_id = group.employee_id;
                        this.create.driving_license = group.driving_license;
                        this.create.license_exp_date = group.license_exp_date;
                        this.create.daily_price = group.daily_price;
                        this.create.weekly_price = group.weekly_price;
                        this.create.monthly_price = group.monthly_price;
                        this.getEmployees();
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
                            setTimeout(() => {
                                this.successFun('Addedsuccessfully');
                            }, 500);
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
        },
        showEmployeeModal() {
            if (this.create.employee_id == 0) {
                this.$bvModal.show("employee-create-car-drive");
                this.create.employee_id = null;
            }
        },
        getEmployees() {
            adminApi
                .get(`/employees/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف موظف", name_e: "Add Employee" });
                    this.employees = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        }
    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
