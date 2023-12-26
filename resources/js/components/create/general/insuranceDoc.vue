<template>
    <div>
        <InsuranceType
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            :isPage="false"
            type="create"
            :isPermission="isPermission"
            :id="'insurance-type-create-insurance-doc'"
            @created="getInsuranceTypes"
        />
        <InsuranceCompany
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            :isPage="false"
            type="create"
            :isPermission="isPermission"
            :id="'insurance-company-create-insurance-doc'"
            @created="getInsuranceCompany"
        />
        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit' ?getCompanyKey('car_insurance_document_create_form'):getCompanyKey('car_insurance_document_edit_form')"
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
                    <div class="col-md-12" v-if="isVisible('insurance_type_id')">
                        <div class="form-group">
                            <label
                            >{{ getCompanyKey("car_insurance_document_insurance_type") }}
                                <span
                                    v-if="isRequired('insurance_type_id')"
                                    class="text-danger"
                                >*</span
                                ></label
                            >

                            <multiselect
                                @input="showInsuranceTypeModal"
                                v-model="create.insurance_type_id"
                                :options="insurance_types.map((type) => type.id)"
                                :custom-label="
                          (opt) => insurance_types.find((x) => x.id == opt)?
                            $i18n.locale == 'ar'
                              ? insurance_types.find((x) => x.id == opt).name
                              : insurance_types.find((x) => x.id == opt).name_e: null
                        "
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.insurance_type_id.$error || errors.insurance_type_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('insurance_number')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("car_insurance_document_insurance_number") }}
                                <span v-if="isRequired('insurance_number')" class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="$v.create.insurance_number.$model"
                                :class="{
                                  'is-invalid':
                                    $v.create.insurance_number.$error ||
                                    errors.insurance_number,
                                  'is-valid':
                                    !$v.create.insurance_number.$invalid &&
                                    !errors.insurance_number,
                                }"
                            />
                            <template v-if="errors.insurance_number">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.insurance_number"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('insurance_company_id')">
                        <div class="form-group">
                            <label
                            >{{ getCompanyKey("car_insurance_document_insurance_company") }}
                                <span
                                    v-if="isRequired('insurance_company_id')"
                                    class="text-danger"
                                >*</span
                                ></label
                            >

                            <multiselect
                                @input="showInsuranceCompanyModal"
                                v-model="create.insurance_company_id"
                                :options="insurance_companies.map((type) => type.id)"
                                :custom-label="
                          (opt) => insurance_companies.find((x) => x.id == opt)?
                            $i18n.locale == 'ar'
                              ? insurance_companies.find((x) => x.id == opt).name
                              : insurance_companies.find((x) => x.id == opt).name_e: null
                        "
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.insurance_company_id.$error || errors.insurance_company_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('related_id')">
                        <div class="form-group">
                            <label>
                                {{ getCompanyKey("car_insurance_document_related") }}
                                <span v-if="isRequired('related_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                v-model="create.related_id"
                                :options="relateds.map((type) => type.id)"
                                :custom-label="
                                  (opt) => relateds.find((x) => x.id == opt)?
                                    relateds.find((x) => x.id == opt)[this.settings[0].db_field]
                                    :null
                                "
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.related_id.$error || errors.related_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('from_date')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("car_insurance_document_from_date") }}
                                <span v-if="isRequired('from_date')" class="text-danger">*</span>
                            </label>
                            <date-picker
                                type="date"
                                v-model="create.from_date"
                                format="YYYY-MM-DD"
                                valueType="format"
                                :confirm="false"
                            ></date-picker>
                            <div
                                v-if="$v.create.from_date.$error || errors.from_date"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.from_date">
                                <ErrorMessage v-for="(errorMessage,index) in errors.from_date"
                                              :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('to_date')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("car_insurance_document_to_date") }}
                                <span v-if="isRequired('to_date')" class="text-danger">*</span>
                            </label>
                            <date-picker
                                type="date"
                                v-model="create.to_date"
                                format="YYYY-MM-DD"
                                valueType="format"
                                :confirm="false"
                            ></date-picker>
                            <div
                                v-if="$v.create.to_date.$error || errors.to_date"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.to_date">
                                <ErrorMessage v-for="(errorMessage,index) in errors.to_date"
                                              :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('amount')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("car_insurance_document_amount") }}
                                <span v-if="isRequired('amount')" class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                step="0.01"
                                v-model.number="$v.create.amount.$model"
                                :class="{
                                  'is-invalid':
                                    $v.create.amount.$error ||
                                    errors.amount,
                                  'is-valid':
                                    !$v.create.amount.$invalid &&
                                    !errors.amount,
                                }"
                            />
                            <template v-if="errors.amount">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.amount"
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
import InsuranceType from "./insurance-type.vue";
import InsuranceCompany from "./insuranceCompany.vue";
import Swal from "sweetalert2";
import {formatDateOnly} from "../../../helper/startDate";

export default {
    components: {
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
        InsuranceType,
        InsuranceCompany
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/insurance-documents'}
    },
    data() {
        return {
            fields: [],
            isLoader: false,
            isCustom:false,
            company_id:null,
            insurance_types: [],
            insurance_companies: [],
            relateds: [],
            settings: [],
            create: {
                related_id: null,
                insurance_type_id: null,
                insurance_company_id: null,
                amount: 0,
                from_date: this.formatDate(new Date()),
                to_date: this.formatDate(new Date()),
                insurance_number: "",
                related_field: ""
            },
            errors: {},
            is_disabled: false,
        };
    },
    validations: {
        create: {
            related_id: { required: requiredIf(function (model) {
                    return this.isRequired("related_id");
                }) },
            insurance_type_id: { required: requiredIf(function (model) {
                    return this.isRequired("insurance_type_id");
                }) },
            insurance_number: { required: requiredIf(function (model) {
                    return this.isRequired("insurance_number");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            insurance_company_id: { required: requiredIf(function (model) {
                    return this.isRequired("insurance_company_id");
                })},
            amount: { required: requiredIf(function (model) {
                    return this.isRequired("amount");
                }),minValue: minValue(0)},
            from_date: { required: requiredIf(function (model) {
                    return this.isRequired("from_date");
                })},
            to_date: { required: requiredIf(function (model) {
                    return this.isRequired("to_date");
                })},
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/general_insurance_documents`)
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
                related_id: null,
                insurance_type_id: null,
                insurance_company_id: null,
                amount: 0,
                from_date: this.formatDate(new Date()),
                to_date: this.formatDate(new Date()),
                insurance_number: "",
                related_field: ""
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
                    this.getInsuranceTypes();
                    this.getInsuranceCompany();
                    await this.getInsuranceSetting();
                    this.getRelated(this.settings[0].db_table);
                }else {
                    if(this.idObjEdit.dataObj){
                        let group = this.idObjEdit.dataObj;
                        this.errors = {};

                        this.create.related_id = group.related_id;
                        this.create.insurance_type_id = group.insurance_type_id;
                        this.create.insurance_company_id = group.insurance_company_id;
                        this.create.amount = group.amount;
                        this.create.from_date = group.from_date;
                        this.create.to_date = group.to_date;
                        this.create.insurance_number = group.insurance_number;
                        this.getInsuranceTypes();
                        this.getInsuranceCompany();
                        await this.getInsuranceSetting();
                        this.getRelated(this.settings[0].db_table);
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
                this.create.related_field = this.relateds.
                    find(el => el.id == this.create.related_id)[this.settings[0].db_field];
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
        showInsuranceTypeModal() {
            if (this.create.insurance_type_id == 0) {
                this.$bvModal.show("insurance-type-create-insurance-doc");
                this.create.insurance_type_id = null;
            }
        },
        showInsuranceCompanyModal(){
            if (this.create.insurance_company_id == 0) {
                this.$bvModal.show("insurance-company-create-insurance-doc");
                this.create.insurance_company_id = null;
            }
        },
        getInsuranceTypes() {
            adminApi
                .get(`/insurance-types`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف نوع التأمين", name_e: "Add Insurance type" });
                    this.insurance_types = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        getInsuranceCompany() {
            adminApi
                .get(`/insurance_companies`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف جهه الاصدار", name_e: "Add Insurance Company" });
                    this.insurance_companies = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getInsuranceSetting() {
            await adminApi
                .get(`/insurance-settings`)
                .then((res) => {
                    let l = res.data.data;
                    this.settings = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        getRelated(table) {
            adminApi
                .get(`/insurance-documents/get-table-data?db_table=${table}`)
                .then((res) => {
                    let l = res.data.data;
                    this.relateds = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
