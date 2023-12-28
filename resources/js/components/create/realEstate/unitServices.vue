<template>
    <div>
        <Unit :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getUnits" :isPage="false" type="create"
            :isPermission="isPermission" :id="'unit-create'" />
        <Service :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getServices" :isPage="false"
            type="create" :isPermission="isPermission" :id="'service-create'" />

        <!--  create   -->
        <b-modal id="create" :title="type != 'edit'
                ? getCompanyKey('unit_service_create_form')
                : getCompanyKey('unit_service_edit_form')
            " dialog-class="modal-xl" title-class="font-18" body-class="p-4" :hide-footer="true" @show="resetModal"
            @hidden="resetModalHidden">
            <form>
                <loader size="large" v-if="isCustom && !isPage" />
                <div class="mb-3 d-flex justify-content-end">
                    <b-button v-if="type != 'edit'" variant="success" :disabled="!is_disabled" @click.prevent="resetForm"
                        type="button" :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                        {{ $t("general.AddNewRecord") }}
                    </b-button>
                    <template v-if="!is_disabled">
                        <b-button variant="success" type="submit" class="mx-1" v-if="!isLoader" @click.prevent="AddSubmit">
                            {{ type != "edit" ? $t("general.Add") : $t("general.edit") }}
                        </b-button>
                        <b-button variant="success" class="mx-1" disabled v-else>
                            <b-spinner small></b-spinner>
                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                        </b-button>
                    </template>
                    <b-button @click.prevent="resetModalHidden" variant="danger" type="button">
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-3 position-relative">
                        <div class="form-group">
                            <label class="my-1 mr-2">{{ getCompanyKey("unit") }}</label>
                            <span class="text-danger">*</span>

                            <multiselect :show-labels="false" @input="showUnitModal" :disabled="type == 'edit'"
                                v-model="$v.create.unit_id.$model" :options="units.map((type) => type.id)" :custom-label="(opt) =>
                                        units.find((x) => x.id == opt)
                                            ? $i18n.locale == 'ar'
                                                ? units.find((x) => x.id == opt).name
                                                : units.find((x) => x.id == opt).name_e
                                            : ''
                                    " :class="{ 'is-invalid': $v.create.unit_id.$error || errors.unit_id }">
                            </multiselect>

                            <template v-if="errors && errors.unit_id">
                                <ErrorMessage >{{ errors.unit_id}}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <button class="btn col-2 btn-sm btn-primary h-50 mt-4" @click.prevent="addNewField">
                        {{ $t('general.Add') }} <i class="fa fa-plus"></i></button>
                </div>

                <div class="row" v-for="(item, gIndex) in create.details" :key="gIndex">

                    <div class="col-md-4 position-relative">
                        <div class="form-group">
                            <label class="my-1 mr-2">{{ getCompanyKey("service") }}</label>
                            <span class="text-danger">*</span>
                            <multiselect @input="showServiceModal(gIndex)"
                                v-model="$v.create.details.$each[gIndex].service_id.$model"
                                :options="services.map((type) => type.id)" :custom-label="(opt) =>
                                        services.find((x) => x.id == opt)
                                            ? $i18n.locale == 'ar'
                                                ? services.find((x) => x.id == opt).name
                                                : services.find((x) => x.id == opt).name_e
                                            : ''
                                    " :class="{ 'is-invalid': $v.create.details.$each[gIndex].service_id.$error || errors[`details.${gIndex}.service_id`] }">
                            </multiselect>
                            <template v-if="errors && errors[`details.${gIndex}.service_id`]">
                                <ErrorMessage v-for="(errorMessage, gIndex) in errors[`details.${gIndex}.service_id`]"
                                    :key="gIndex">{{ errorMessage
                                    }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">
                                <label class="my-1 mr-2">{{
                                    getCompanyKey("unitServices_price")
                                }}</label>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control"
                                v-model="$v.create.details.$each[gIndex].default_price.$model" :class="{'is-valid': !$v.create.details.$each[gIndex].default_price.$invalid && !errors[`details.${gIndex}.default_price`],
                                }" />
                                <template v-if="errors && errors[`details.${gIndex}.default_price`]">
                                <ErrorMessage v-for="(errorMessage, gIndex) in errors[`details.${gIndex}.default_price`]"
                                    :key="gIndex">{{ errorMessage
                                    }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group pt-2">
                            <label class="control-label">
                                <label class="my-1 mr-2">{{
                                    getCompanyKey("unitServices_date")
                                }}</label>
                                <span class="text-danger">*</span>
                            </label>
                            <date-picker type="date"
                                v-model="$v.create.details.$each[gIndex].from_date.$model" format="YYYY-MM-DD"
                                valueType="format" :confirm="false"
                                :class="{ 'is-invalid': $v.create.details.$each[gIndex].from_date.$error }"></date-picker>
                                <template v-if="errors && errors[`details.${gIndex}.from_date`]">
                                <ErrorMessage v-for="(errorMessage, gIndex) in errors[`details.${gIndex}.from_date`]"
                                    :key="gIndex">{{ errorMessage
                                    }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-1 pt-3">
                        <button v-if="create.details.length > 1" type="button" @click.prevent="removeNewField(gIndex)"
                            class="btn btn-sm btn-danger p-2">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </form>
        </b-modal>

        <!--  /create   -->
    </div>
</template>

<script>
import adminApi from "../../../api/adminAxios";
import { required, numeric, minValue } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import Unit from "./unit";
import Service from "./services";
import Multiselect from "vue-multiselect";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import DatePicker from "vue2-datepicker";
import { formatDateOnly } from "../../../helper/startDate";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Unit Services",
        meta: [{ name: "description", content: "Unit Services" }],
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
        url: { default: "/real-estate/unit-service" },
    },
    components: {
        ErrorMessage,
        loader,
        Multiselect,
        DatePicker,
        Unit,
        Service,
    },
    data() {
        return {
            services: [],
            units: [],
            isLoader: false,
            isCustom: false,
            create: {
                unit_id: null,
                details: [
                    {
                        service_id: null,
                        default_price: 0,
                        from_date: this.formatDate(new Date()),
                    }
                ],
            },
            errors: {},
            is_disabled: false,
            current_page: 1,
            company_id: 48,
        };
    },
    validations: {
        create: {
            unit_id: { required },
            details: {
                required,
                $each: {
                    service_id: { required },
                    from_date: {},
                    default_price: { required, minValue: minValue(0) },
                }
            }
        }
    },
    methods: {

        removeNewField(index) {
            if (this.create.details.length > 1) {
                this.create.details.splice(index, 1);
            }
        },

        addNewField() {
            this.create.details.push({
                service_id: null,
                default_price: 0,
                from_date: this.formatDate(new Date()),
            });
        },
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/rlst_unit_services`)
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
        formatDate(value) {
            return formatDateOnly(value);
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
        showUnitModal() {
            if (this.create.unit_id == 0) {
                this.$bvModal.show("unit-create");
                this.create.unit_id = null;
            }
        },
        showServiceModal(index) {
            if (this.create.details[index].service_id == 0) {
                this.$bvModal.show("service-create");
                this.create.details[index].service_id = null;
            }
        },
        defaultData(edit = null) {
            this.create.details = [
                {
                    service_id: null,
                    default_price: 0,
                    from_date: this.formatDate(new Date()),
                }
            ];
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;

            this.errors = {};
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
                    this.$nextTick(() => {
                        this.$v.$reset();
                    });
                    this.getUnits();
                    this.getServices();
                } else {
                    if (this.idObjEdit.dataObj) {
                        let unitService = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.unit_id = unitService.id;
                        this.create.details = [];
                        unitService.services.forEach((e, index) => {
                            this.create.details.push({
                                service_id: e.pivot.service_id,
                                from_date: this.formatDate(e.pivot.from_date),
                                default_price: e.pivot.default_price,
                            });
                        });
                        this.getUnits();
                        this.getServices();
                    }
                }
            }, 50);
        },

        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            if (this.$v.create.$invalid) {
                this.$v.create.$touch();
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                console.log("this.create", this.create);
                if (this.type != "edit") {
                    adminApi
                        .post(this.url, {
                            ...this.create,
                            company_id: this.$store.getters["auth/company_id"],
                        })
                        .then((res) => {
                            this.is_disabled = true;
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

        async getUnits() {
            this.isLoader = true;
            await adminApi
                .get(`/real-estate/units`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    if (this.isPermission("create unit RealState")) {
                        l.unshift({ id: 0, name: "اضافة وحده", name_e: "Add unit" });
                    }
                    this.units = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },

        async getServices() {
            this.isLoader = true;
            await adminApi
                .get(`/real-estate/services`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    if (this.isPermission("create services")) {
                        l.unshift({ id: 0, name: "اضافة خدمه", name_e: "Add service" });
                    }
                    this.services = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
    },
};
</script>


