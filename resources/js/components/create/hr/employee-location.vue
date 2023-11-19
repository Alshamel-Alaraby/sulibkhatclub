<template>
    <div>
        <LocationDate
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            @created="getLocationDate()"
            :isPage="false"
            :type="type"
            :location_id="location_id"
            :idObjEdit="dataObj"
            :isPermission="isPermission"
            :id="'location_date_create'"
        />

        <!--  create   -->
        <div>
            <!--  create   -->
            <b-modal
                :id="id"
                :title="
                (type != 'edit'
                    ? getCompanyKey('employee_location_create_form')
                    : getCompanyKey('employee_location_edit_form'))
                "
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
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <span
                            class="fas fa-thumbtack"
                            aria-hidden="true"
                        ></span>
                            <h5 v-if="location_id" class="title_menu">
                                {{
                                    $i18n.locale == "ar"
                                        ? create.name
                                        : create.name_e
                                }}
                            </h5>
                            <span
                                v-if="location_date_id"
                                :class="[
                            'fas',
                            $i18n.locale == 'ar'
                              ? 'fa-arrow-left'
                              : 'fa-arrow-right',
                          ]"
                                aria-hidden="true"
                            ></span>
                            <h5 v-if="location_date_id" class="title_menu">
                                {{ locationDates.find((x) => x.id == location_date_id).vdate }}
                            </h5>
                        </div>
                    </div>
                    <b-tabs nav-class="nav-tabs nav-bordered">
                        <b-tab :title="$t('general.DataEntry')" active>
                            <div class="row justify-content-center">
                                <div class="col-md-6" v-if="isVisible('name')">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">
                                            {{ getCompanyKey("employee_location_name_ar") }}
                                            <span v-if="isRequired('name')" class="text-danger"
                                            >*</span
                                            >
                                        </label>
                                        <div dir="rtl">
                                            <input
                                                @keyup="arabicValue(create.name)"
                                                type="text"
                                                class="form-control"
                                                v-model="$v.create.name.$model"
                                                :class="{
                                                  'is-invalid': $v.create.name.$error || errors.name,
                                                  'is-valid': !$v.create.name.$invalid && !errors.name,
                                                }"
                                                id="field-1"
                                            />
                                        </div>
                                        <div
                                            v-if="!$v.create.name.minLength"
                                            class="invalid-feedback"
                                        >
                                            {{ $t("general.Itmustbeatleast") }}
                                            {{ $v.create.name.$params.minLength.min }}
                                            {{ $t("general.letters") }}
                                        </div>
                                        <div
                                            v-if="!$v.create.name.maxLength"
                                            class="invalid-feedback"
                                        >
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
                                <div class="col-md-6" v-if="isVisible('name_e')">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">
                                            {{ getCompanyKey("employee_location_name_en") }}
                                            <span v-if="isRequired('name_e')" class="text-danger"
                                            >*</span
                                            >
                                        </label>
                                        <div>
                                            <input
                                                @keyup="englishValue(create.name_e)"
                                                type="text"
                                                class="form-control"
                                                v-model="$v.create.name_e.$model"
                                                :class="{
                                                  'is-invalid':
                                                    $v.create.name_e.$error || errors.name_e,
                                                  'is-valid':
                                                    !$v.create.name_e.$invalid && !errors.name_e,
                                                }"
                                                id="field-2"
                                            />
                                        </div>
                                        <div
                                            v-if="!$v.create.name_e.minLength"
                                            class="invalid-feedback"
                                        >
                                            {{ $t("general.Itmustbeatleast") }}
                                            {{ $v.create.name_e.$params.minLength.min }}
                                            {{ $t("general.letters") }}
                                        </div>
                                        <div
                                            v-if="!$v.create.name_e.maxLength"
                                            class="invalid-feedback"
                                        >
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
                                <div class="col-md-5" v-if="isVisible('longitude')">
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("employee_location_longitude") }}
                                            <span v-if="isRequired('longitude')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            step="0.00000000000001"
                                            v-model="$v.create.longitude.$model"
                                            :class="{
                                              'is-invalid': $v.create.longitude.$error || errors.longitude,
                                              'is-valid': !$v.create.longitude.$invalid && !errors.longitude,
                                            }"
                                        />
                                        <template v-if="errors.longitude">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.longitude"
                                                :key="index"
                                            >{{ errorMessage }}</ErrorMessage
                                            >
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-5" v-if="isVisible('latitude')">
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("employee_location_latitude") }}
                                            <span v-if="isRequired('latitude')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            data-create="9"
                                            step="0.00000000000001"
                                            v-model="$v.create.latitude.$model"
                                            :class="{
                                              'is-invalid': $v.create.latitude.$error || errors.latitude,
                                              'is-valid': !$v.create.latitude.$invalid && !errors.latitude,
                                            }"
                                        />
                                        <template v-if="errors.latitude">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.latitude"
                                                :key="index"
                                            >{{ errorMessage }}</ErrorMessage
                                            >
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-center mt-3">
                                    <button
                                        class="btn btn-primary"
                                        type="button"
                                        @click="getLocation"
                                    >
                                        {{ $t("general.location") }}
                                    </button>
                                </div>
                            </div>
                        </b-tab>
                        <b-tab :disabled="!location_id" :title="$t('general.date')">
                            <div
                                class="row justify-content-between align-items-center mb-2 px-1"
                            >
                                <div class="col-md-7 d-flex align-items-center mb-1 mb-xl-0">
                                    <b-button
                                        @click.prevent="showModelEdit('create')"
                                        variant="primary"
                                        class="btn-sm mx-1 font-weight-bold"
                                    >
                                        {{ $t("general.Create") }}
                                        <i class="fas fa-plus"></i>
                                    </b-button>
                                    <div style="width: 75%">
                                        <multiselect
                                            v-model="location_date_id"
                                            :options="locationDates.map((type) => type.id)"
                                            :custom-label="
                                              (opt) => ($i18n.locale == 'ar'?locationDates.find((x) => x.id == opt).location.name:locationDates.find((x) => x.id == opt).location.name_e)+ ' ' + locationDates.find((x) => x.id == opt).vdate
                                             "
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                                <div
                                    class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end"
                                >

                                </div>
                            </div>
                            <!-- start .table-responsive-->
                            <div
                                class="table-responsive mb-3 custom-table-theme position-relative"
                            >
                                <!--       start loader       -->
                                <loader size="large" v-if="isLoader" />
                                <!--       end loader       -->

                                <table
                                    class="table table-borderless table-hover table-centered m-0"
                                    ref="exportable_table"
                                    id="printMe"
                                >
                                    <thead>
                                    <tr>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{ $t("general.location") }}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{ $t("general.date") }}</span>
                                            </div>
                                        </th>
                                        <th class="do-not-print">
                                            {{ $t("general.Action") }}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="locationDates.length > 0">
                                        <tr
                                        @dblclick.prevent="showModelEdit('edit',data.id)"
                                        v-for="(data, index) in locationDates"
                                        :key="data.id"
                                        class="body-tr-custom"
                                    >
                                        <td>
                                            <h5 class="m-0 font-weight-normal">{{ $i18n.locale == 'ar'? data.location.name : data.location.name_e }}</h5>
                                        </td>
                                        <td>
                                            <h5 class="m-0 font-weight-normal">{{ data.vdate }}</h5>
                                        </td>
                                        <td class="do-not-print">
                                            <div class="btn-group">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                    data-toggle="dropdown"
                                                    aria-expanded="false"
                                                >
                                                    {{ $t("general.commands") }}
                                                    <i class="fas fa-angle-down"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-custom">
                                                    <a
                                                        class="dropdown-item"
                                                        href="#"
                                                        @click.prevent="showModelEdit('edit',data.id)"
                                                    >
                                                        <div
                                                            class="d-flex justify-content-between align-items-center text-black"
                                                        >
                                                            <span>{{ $t("general.edit") }}</span>
                                                            <i
                                                                class="mdi mdi-square-edit-outline text-info"
                                                            ></i>
                                                        </div>
                                                    </a>
                                                    <a
                                                        class="dropdown-item text-black"
                                                        href="#"
                                                        @click.prevent="deleteModule(data.id)"
                                                    >
                                                        <div
                                                            class="d-flex justify-content-between align-items-center text-black"
                                                        >
                                                            <span>{{ $t("general.delete") }}</span>
                                                            <i class="fas fa-times text-danger"></i>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tbody v-else>
                                    <tr>
                                        <th class="text-center" colspan="6">
                                            {{ $t("general.notDataFound") }}
                                        </th>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- end .table-responsive-->
                        </b-tab>
                        <b-tab :disabled="!location_date_id" @click="getLocationEmployee" :title="$t('general.employees')">
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
                                <div class="col-md-6 float-md-left">
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
    decimal,
    maxLength,
    minLength,
    minValue,
    required,
    requiredIf,
} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import LocationDate from "./location-date";
import Swal from "sweetalert2";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import successError from "../../../helper/mixin/success&error";
import Multiselect from "vue-multiselect";

export default {
    name: "wallet",
    components: {
        ErrorMessage,
        loader,
        Multiselect,
        LocationDate
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
        url: { default: "/hr/locations" },
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
                name: "",
                name_e: "",
                longitude: 0,
                latitude: 0
            },
            createBuilding: {
                location_date_id: null,
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
            name: {
                required: requiredIf(function (model) {
                    return this.isRequired("name");
                }),
                minLength: minLength(3),
                maxLength: maxLength(100),
            },
            name_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }),
                minLength: minLength(3),
                maxLength: maxLength(100),
            },
            longitude: { required: requiredIf(function (model) {
                    return this.isRequired("longitude");
                }),decimal },
            latitude: { required: requiredIf(function (model) {
                    return this.isRequired("latitude");
                }),decimal },
        },
        createBuilding: {
            location_date_id: { required },
            employee_ids: { required },
        },
    },
    methods: {
        deleteModule(id) {
            if (Array.isArray(id)) {
                Swal.fire({
                    title: `${this.$t("general.Areyousure")}`,
                    text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
                    cancelButtonText: `${this.$t("general.Nocancel")}`,
                    confirmButtonClass: "btn btn-success mt-2",
                    cancelButtonClass: "btn btn-danger ml-2 mt-2",
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.value) {
                        this.isLoader = true;
                        adminApi
                            .post(`/hr/location-dates/bulk-delete`, { ids: id })
                            .then((res) => {
                                this.checkAll = [];
                                this.getData(1, this.url);
                                this.successFun('Yourrowhasbeendeleted', 'Deleted');
                            })
                            .catch((err) => {
                                if (err.response.status == 400) {
                                    this.errorFun('Error', 'CantDeleteRelation');
                                } else {
                                    this.errorFun('Error', 'Thereisanerrorinthesystem');
                                }
                            })
                            .finally(() => {
                                this.isLoader = false;
                            });
                    }
                });
            } else {
                Swal.fire({
                    title: `${this.$t("general.Areyousure")}`,
                    text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
                    cancelButtonText: `${this.$t("general.Nocancel")}`,
                    confirmButtonClass: "btn btn-success mt-2",
                    cancelButtonClass: "btn btn-danger ml-2 mt-2",
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.value) {
                        this.isLoader = true;

                        adminApi
                            .delete(`/hr/location-dates/${id}`)
                            .then((res) => {
                                this.checkAll = [];
                                this.getData(1, this.url);
                                this.successFun('Yourrowhasbeendeleted', 'Deleted');
                            })

                            .catch((err) => {
                                if (err.response.status == 400) {
                                    this.errorFun('Error', 'CantDeleteRelation');
                                } else {
                                    this.errorFun('Error', 'Thereisanerrorinthesystem');
                                }
                            })
                            .finally(() => {
                                this.isLoader = false;
                            });
                    }
                });
            }
        },
        getLocationDate() {
            this.isLoader = true;

            adminApi
                .get(`/hr/location-dates?location_id=${this.location_id}`)
                .then((res) => {
                    let l = res.data.data;
                    this.locationDates = l;
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
        changeNumber(add) {
            let totel = this.createOwner.wallet_owners.reduce(
                (accumulator, curValue) =>
                    parseFloat(accumulator) + parseFloat(curValue.percentage),
                0
            );
            this.is_persentage = totel == 100 ? true : false;
        },
        getLocationEmployee(){
            let loc = this.locationDates.find(el => el.id == this.location_date_id);
            if(loc){
                if(loc.employees && loc.employees.length > 0){
                    let emps = loc.employees.map(el => el.id);
                    this.createBuilding.employee_ids = emps;
                }
            }
        },
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/hr_locations`)
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
                name: "",
                name_e: "",
                longitude: 0,
                latitude: 0
            };
            this.createBuilding = {
                location_date_id: null,
                employee_ids: [],
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_persentage = true;
            this.errors = {};
            this.location_id = null;
            this.location_date_id = null;
            this.is_disabled = false;
            this.is_disabledBuilding = false;
            this.is_disabledOwner = false;
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
                        let module = this.idObjEdit.dataObj;
                        this.getEmployees();
                        this.location_id = module.id;
                        this.create.name = module.name;
                        this.create.name_e = module.name_e;
                        this.create.latitude = module.latitude;
                        this.create.longitude = module.longitude;
                        this.getLocationDate();
                    }
                }
            }, 50);
        },
        resetForm() {
            this.defaultData();
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
                if (this.type != "edit") {
                    adminApi
                        .post(this.url, { ...this.create, company_id: this.company_id })
                        .then((res) => {
                            this.is_disabled = true;
                            this.location_id = res.data.data.id;
                            this.getLocationDate();
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
        arabicValue(txt) {
            this.create.name = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.name_e = englishValue(txt);
        },
        AddSubmitBuilding() {
            this.createBuilding.location_date_id = this.location_date_id;
            if (this.$v.createBuilding.$invalid) {
                this.$v.createBuilding.$touch();
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                if (this.type != "edit") {
                    adminApi
                        .post(`/hr/Employee-location-date`, {
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
                        .post(`/hr/Employee-location-date/update`, {
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
        async getLocation() {
            if (navigator.geolocation) {
                return await navigator.geolocation.getCurrentPosition(
                    this.showPosition
                );
            }
        },
        showPosition(position) {
            this.create.latitude = position.coords.latitude;
            this.create.longitude = position.coords.longitude;
        },
        showModelEdit(type = 'create',id = null){
            if(type == 'create'){
                this.type = 'create';
            }else {
                this.type = type;
                this.dataObj = {idEdit:id,dataObj: this.locationDates.find(el => el.id == id)};
            }
            this.$bvModal.show(`location_date_create`);
        }
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
