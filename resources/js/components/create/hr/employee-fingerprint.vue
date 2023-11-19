<template>
    <div>
        <!--  create   -->
        <div>
            <!--  create   -->
            <b-modal
                :id="id"
                :title="
                (type != 'edit'
                    ? getCompanyKey('employee_fingerprint_create_form')
                    : getCompanyKey('employee_fingerprint_edit_form'))
                "
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
                            :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
                        >
                            {{ $t("general.AddNewRecord") }}
                        </b-button>
                        <template v-if="!is_disabled">
                            <b-button
                                variant="success"
                                type="submit"
                                class="mx-1"
                                v-if="!isLoader"
                                @click.prevent="AddSubmit"
                            >
                                {{ type != "edit" ? $t("general.Add") : $t("general.edit") }}
                            </b-button>

                            <b-button variant="success" class="mx-1" disabled v-else>
                                <b-spinner small></b-spinner>
                                <span class="sr-only">{{ $t("login.Loading") }}...</span>
                            </b-button>
                        </template>
                        <b-button
                            @click.prevent="resetModalHidden"
                            variant="danger"
                            type="button"
                        >
                            {{ $t("general.Cancel") }}
                        </b-button>
                    </div>
                    <b-tabs nav-class="nav-tabs nav-bordered">
                        <b-tab :title="$t('general.DataEntry')" active>
                            <div class="row justify-content-center">
                                <div class="col-md-12" v-if="isVisible('vdate')">
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("employee_fingerprint_vdate") }}
                                            <span v-if="isRequired('vdate')" class="text-danger"
                                            >*</span
                                            >
                                        </label>
                                        <date-picker
                                            v-model="$v.create.vdate.$model"
                                            type="date"
                                            format="YYYY-MM-DD"
                                            valueType="format"
                                        ></date-picker>
                                        <template v-if="errors.vdate">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.vdate"
                                                :key="index"
                                            >
                                                {{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="isVisible('has_fingerprint')">
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("employee_fingerprint_has_fingerprint") }}
                                            <span v-if="isRequired('has_fingerprint')" class="text-danger"
                                            >*</span
                                            >
                                        </label>
                                        <b-form-group >
                                            <b-form-radio
                                                class="d-inline-block"
                                                v-model="$v.create.has_fingerprint.$model"
                                                name="has_fingerprint"
                                                value="1"
                                            >{{ $t("general.Yes") }}
                                            </b-form-radio>
                                            <b-form-radio
                                                class="d-inline-block m-1"
                                                v-model="$v.create.has_fingerprint.$model"
                                                name="has_fingerprint"
                                                value="0"
                                            >{{ $t("general.No") }}
                                            </b-form-radio>
                                        </b-form-group>
                                        <template v-if="errors.has_fingerprint">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.has_fingerprint"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </b-tab>
                        <b-tab :disabled="!location_id" :title="$t('general.employees')">
                            <div class="d-flex justify-content-end">
                                <template v-if="!is_disabledBuilding">
                                    <b-button
                                        variant="success"
                                        type="submit"
                                        class="mx-1"
                                        v-if="!isLoader"
                                        @click.prevent="AddSubmitBuilding"
                                    >
                                        {{ $t("general.Add") }}
                                    </b-button>

                                    <b-button variant="success" class="mx-1" disabled v-else>
                                        <b-spinner small></b-spinner>
                                        <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                    </b-button>
                                </template>
                                <b-button
                                    variant="success"
                                    type="button"
                                    disabled
                                    class="mx-1"
                                    v-if="is_disabledBuilding"
                                >
                                    {{ $t("general.Add") }}
                                </b-button>
                            </div>
                            <div class="row">
                                <div class="col-md-12 float-md-left">
                                    <div class="form-group">
                                        <label class="my-1 mr-2">
                                            {{ $t('general.employees') }}
                                        </label>
                                        <multiselect
                                            :multiple="true"
                                            v-model="createBuilding.employee_ids"
                                            :options="employees.map((type) => type.id)"
                                            :custom-label="
                                                  (opt) =>
                                                    employees.find((x) => x.id == opt)
                                                      ? $i18n.locale == 'ar'
                                                        ? employees.find((x) => x.id == opt).name
                                                        : employees.find((x) => x.id == opt).name_e
                                                      : null
                                                "
                                            :class="{
                                                  'is-invalid':
                                                    $v.createBuilding.employee_ids.$error ||
                                                    errors.employee_ids,
                                                }"
                                        ></multiselect>
                                        <div
                                            v-if="!$v.createBuilding.employee_ids.required"
                                            class="invalid-feedback"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.employee_ids">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.employee_ids"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </b-tab>
                    </b-tabs>
                </form>
            </b-modal>
            <!--  /create   -->
        </div>
        <!--  /create   -->
    </div>
</template>

<script>
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import {
    required,
    requiredIf,
} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import Multiselect from "vue-multiselect";
import {formatDateOnly} from "../../../helper/startDate";
import DatePicker from "vue2-datepicker";
export default {
    name: "wallet",
    components: {
        ErrorMessage,
        loader,
        Multiselect,
        DatePicker
    },
    mixins: [transMixinComp, successError],
    props: {
        id: { default: "create" },
        companyKeys: { default: [] },
        defaultsKeys: { default: [] },
        isPage: { default: true },
        isVisiblePage: { default: null },
        isRequiredPage: { default: null },
        type: { default: "create" },
        idObjEdit: { default: null },
        isPermission: {},
        url: { default: "/hr/employee-fingerprint-headers" },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    data() {
        return {
            idEdit: false,
            isCustom: false,
            fields: [],
            employees: [],
            tab: "",
            parents: [],
            isLoader: false,
            create: {
                vdate: this.formatDate(new Date()),
                has_fingerprint: 0,
            },
            createBuilding: {
                employee_fingerprint_header_id: null,
                employee_ids: null,
            },
            location_id: null,
            errors: {},
            owners: [],
            buildings: [],
            is_disabled: false,
            is_disabledOwner: false,
            is_disabledBuilding: false,
            location_date_id: null,
            locationDates: [],
            dataObj: null
        };
    },
    validations: {
        create: {
            has_fingerprint: {
                required: requiredIf(function (model) {
                    return this.isRequired("has_fingerprint");
                })
            },
            vdate: {
                required: requiredIf(function (model) {
                    return this.isRequired("vdate");
                })
            },
        },
        createBuilding: {
            employee_fingerprint_header_id: { required },
            employee_ids: { required },
        },
    },
    methods: {
        getEmployees() {
            this.isLoader = true;
            adminApi
                .get(`/employees/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    this.employees = l;
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
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/hr_employee_fingerprint_headers`)
                .then((res) => {
                    this.fields = res.data;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
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
                vdate: this.formatDate(new Date()),
                has_fingerprint: 0,
            };
            this.createBuilding = {
                employee_fingerprint_header_id: null,
                employee_ids: [],
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_persentage = true;
            this.errors = {};
            this.location_id = null;
            this.is_disabled = false;
            this.is_disabledBuilding = false;
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        resetModal() {
            this.defaultData();
            setTimeout(async () => {
                if (this.type != "edit") {
                    if (!this.isPage) await this.getCustomTableFields();
                    this.getEmployees();
                } else {
                    if (this.idObjEdit.dataObj) {
                        this.create.public_holiday_details = [];
                        let module = this.idObjEdit.dataObj;
                        this.getEmployees();
                        this.location_id = module.id;
                        this.create.vdate = module.vdate;
                        this.create.has_fingerprint = module.has_fingerprint;
                        if(module.employees && module.employees.length > 0){
                            let emps = module.employees.map(el => el.id);
                            this.createBuilding.employee_ids = emps;
                        }
                        this.create.has_fingerprint = module.has_fingerprint;
                    }
                }
            }, 50);
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
                if (this.type != "edit") {
                    adminApi
                        .post(this.url, { ...this.create, company_id: this.company_id })
                        .then((res) => {
                            this.is_disabled = true;
                            this.location_id = res.data.data.id;
                            if (!this.isPage) this.$emit("created");
                            else this.$emit("getDataTable");

                            setTimeout(() => {
                                this.successFun("Addedsuccessfully");
                            }, 500);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun("Error", "Thereisanerrorinthesystem");
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
                            this.dataObj = null;
                            setTimeout(() => {
                                this.successFun("Editsuccessfully");
                            }, 500);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun("Error", "Thereisanerrorinthesystem");
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }
            }
        },
        AddSubmitBuilding() {
            this.createBuilding.employee_fingerprint_header_id = this.location_id;
            if (this.$v.createBuilding.$invalid) {
                this.$v.createBuilding.$touch();
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                if (this.type != "edit") {
                    adminApi
                        .post(`/hr/employee-fingerprint-details`, {
                            ...this.createBuilding,
                        })
                        .then((res) => {
                            if (!this.isPage) return;
                            else this.$emit("getDataTable");
                            this.is_disabledBuilding = true;
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
                } else {
                    adminApi
                        .post(`/hr/employee-fingerprint-details/update`, {
                            ...this.createBuilding,
                        })
                        .then((res) => {
                            if (!this.isPage) return;
                            else this.$emit("getDataTable");
                            setTimeout(() => {
                                Swal.fire({
                                    icon: "success",
                                    text: `${this.$t("general.Editsuccessfully")}`,
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
            }
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
    },
};
</script>

<style scoped>
form {
    position: relative;
}
.title_menu {
    display: inline-block;
    font-weight: bold;
    font-size: 18px;
}
</style>
