<template>
    <div>
        <country :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'country-create-avenue'" @created="getCategory" />
        <governate
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
            :id="'governate-create-avenue'"
            @created="getGovernorate(create.country_id)"
        />
        <City
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
            :id="'city-create-avenue'"
            @created="getCity(create.country_id, create.governorate_id)"
        />
        <!--  create   -->
        <b-modal
            :id="id"
            :title="getCompanyKey('avenue_create_form')"
            title-class="font-18"
            size="lg"
            body-class="p-4 "
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
                            type="submit"
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
                    <b-button
                        @click.prevent="resetModalHidden"
                        variant="danger"
                        type="button"
                    >
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-6" v-if="isVisible('country_id')">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("country") }}
                                <span v-if="isRequired('country_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                @input="getGovernorate(create.country_id)"
                                v-model="$v.create.country_id.$model"
                                :options="countries.map((type) => type.id)"
                                :custom-label="(opt) => countries.find((x) => x.id == opt).name"
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.country_id.$error || errors.country_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.country_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.country_id"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('governorate_id')">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("governorate") }}
                                <span v-if="isRequired('governorate_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                @input="getCity(create.country_id, create.governorate_id)"
                                v-model="$v.create.governorate_id.$model"
                                :options="governorates.map((type) => type.id)"
                                :custom-label="
                          (opt) => governorates.find((x) => x.id == opt).name
                        "
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.governorate_id.$error || errors.governorate_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.governorate_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.governorate_id"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('city_id')">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("city") }}
                                <span v-if="isRequired('city_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                @input="showCityModal(create.city_id)"
                                v-model="$v.create.city_id.$model"
                                :options="cities.map((type) => type.id)"
                                :custom-label="(opt) => cities.find((x) => x.id == opt).name"
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.city_id.$error || errors.city_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.city_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.city_id"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6 direction" dir="rtl" v-if="isVisible('name')">
                        <div class="form-group">
                            <label for="field-1" class="control-label">
                                {{ getCompanyKey("avenue_name_ar") }}
                                <span v-if="isRequired('name')" class="text-danger">*</span>
                            </label>
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
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6 direction-ltr" dir="ltr" v-if="isVisible('name_e')">
                        <div class="form-group">
                            <label for="field-2" class="control-label">
                                {{ getCompanyKey("avenue_name_en") }}
                                <span v-if="isRequired('name_e')" class="text-danger">*</span>
                            </label>
                            <input
                                @keyup="englishValue(create.name_e)"

                                type="text"
                                class="form-control"
                                v-model="$v.create.name_e.$model"
                                :class="{
                          'is-invalid': $v.create.name_e.$error || errors.name_e,
                          'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                        }"
                                id="field-2"
                            />
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
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                       <div class="col-md-12" v-if="isVisible('is_active')">
                    <div class="form-group">
                      <label class="mr-2">
                        {{ getCompanyKey("avenue_status") }}
                        <span v-if="isRequired('is_active')" class="text-danger">*</span>
                      </label>
                      <b-form-group
                        :class="{
                          'is-invalid': $v.create.is_active.$error || errors.is_active,
                          'is-valid': !$v.create.is_active.$invalid && !errors.is_active,
                        }"
                      >
                        <b-form-radio
                          class="d-inline-block"
                          v-model="$v.create.is_active.$model"
                          name="some-radios"
                          value="active"
                          >{{ $t("general.Active") }}</b-form-radio
                        >
                        <b-form-radio
                          class="d-inline-block m-1"
                          v-model="$v.create.is_active.$model"
                          name="some-radios"
                          value="inactive"
                          >{{ $t("general.Inactive") }}</b-form-radio
                        >
                      </b-form-group>
                      <template v-if="errors.is_active">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.is_active"
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
import adminApi from "../../api/adminAxios";
import {required, minLength, maxLength, integer, alpha, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import Switches from "vue-switches";
import ErrorMessage from "../../components/widgets/errorMessage";
import loader from "../../components/loader";
import Multiselect from "vue-multiselect";
import governate from "../governate";
import country from "../country";
import City from "../city";
import transMixinComp from "../../helper/translation-comp-mixin";
import {dynamicSortNumber, dynamicSortString} from "../../helper/tableSort";
import {formatDateOnly} from "../../helper/startDate";
import {arabicValue, englishValue} from "../../helper/langTransform";

export default {
    components: {
        Switches,
        Multiselect,
        ErrorMessage,
        loader,
        governate,
        country,
        City
    },
    mixins: [transMixinComp],
    props: {
        id: {
            default: "avenue-create",
        },
        companyKeys: {
            default: [],
        },
        defaultsKeys: {
            default: [],
        },
    },
    mounted() {
        this.getCustomTableFields();
        this.company_id = this.$store.getters["auth/company_id"];
    },
    updated() {
        // $(function () {
        //     $(".englishInput").keypress(function (event) {
        //         var ew = event.which;
        //         if (ew == 32) return true;
        //         if (48 <= ew && ew <= 57) return true;
        //         if (65 <= ew && ew <= 90) return true;
        //         if (97 <= ew && ew <= 122) return true;
        //         return false;
        //     });
        //     $(".arabicInput").keypress(function (event) {
        //         var ew = event.which;
        //         if (ew == 32) return true;
        //         if (48 <= ew && ew <= 57) return false;
        //         if (65 <= ew && ew <= 90) return false;
        //         if (97 <= ew && ew <= 122) return false;
        //         return true;
        //     });
        // });
    },
    validations: {
        create: {
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            name_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }),
                minLength: minLength(2),
                maxLength: maxLength(100),
            },
            country_id: { required: requiredIf(function (model) {
                    return this.isRequired("country_id");
                }) },
            governorate_id: { required: requiredIf(function (model) {
                    return this.isRequired("governorate_id");
                }) },
            city_id: { required: requiredIf(function (model) {
                    return this.isRequired("city_id");
                }) },
            is_active: { required: requiredIf(function (model) {
                    return this.isRequired("is_active");
                }) },
        }
    },
    data() {
        return {
            is_disabled: false,
            isLoader: false,
            create: {
                name: "",
                name_e: "",
                country_id: null,
                governorate_id: null,
                city_id: null,
                is_active: "active",
            },
            errors: {},
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            countries: [],
            governorates: [],
            cities: [],
            fields: []
        };
    },
    methods: {
        getCustomTableFields() {
            adminApi
                .get(`/customTable/table-columns/general_avenues`)
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
        showScreen(module, screen) {
            let filterRes = this.$store.state.auth.allWorkFlow.filter(
                (workflow) => workflow.name_e == module
            );
            let _module = filterRes.length ? filterRes[0] : null;
            if (!_module) return false;
            return _module.screen ? _module.screen.name_e == screen : true;
        },
        showCityModal() {
            if (this.create.city_id == 0) {
                this.$bvModal.show("city-create-avenue");
                this.create.city_id = null;
            }
        },
        resetForm() {
            this.create = {
                name: "",
                name_e: "",
                country_id: null,
                governorate_id: null,
                city_id: null,
                is_active: "active",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });

            this.is_disabled = false;
        },
        /**
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.create = {
                name: "",
                name_e: "",
                country_id: null,
                governorate_id: null,
                city_id: null,
                is_active: "active",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
            this.$bvModal.hide(this.id);
        },
        /**
         *  hidden Modal (create)
         */
        resetModal() {
            this.create = {
                name: "",
                name_e: "",
                country_id: null,
                governorate_id: null,
                city_id: null,
                is_active: "active",
            };
            this.is_disabled = false;

            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.getCategory();
            this.governorates = [
                { id: 0, name: "اضف محافظة جديدة", name_e: "Add new governorate" },
            ];
            this.cities = [{ id: 0, name: "اضافة مدينة", name_e: "Add City" }];
            this.errors = {};
        },
        /**
         *  create countrie
         */

        AddSubmit() {
            if (this.create.name || this.create.name_e) {
                this.create.name = this.create.name ? this.create.name : this.create.name_e;
                this.create.name_e = this.create.name_e ? this.create.name_e : this.create.name;
            }
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;

                adminApi
                    .post(`/avenues`, {
                        ...this.create,
                        company_id: this.$store.getters["auth/company_id"],
                    })
                    .then((res) => {
                        this.is_disabled = true;
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

        /**
         *  end  ckeckRow
         */
        async getCategory() {
            this.create.governorate_id = null;
            this.create.city_id = null;
            await adminApi
                .get(`/countries?is_active=active`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضافة دولة", name_e: "Add Country" });
                    this.countries = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getGovernorate(id) {
            if (id == 0) {
                this.$bvModal.show("country-create-avenue");
                this.create.country_id = null;
                this.edit.country_id = null;
            }
            if (!id) {
                return;
            }
            this.create.city_id = null;
            this.create.governorate_id = null;
            await adminApi
                .get(`/governorates?country_id=${id}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف محافظة جديدة", name_e: "Add new governorate" });
                    this.governorates = l;
                    this.cities = [{ id: 0, name: "اضف مدينة", name_e: "Add City" }];
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getCity(id, id2) {
            if (id2 == 0) {
                this.$bvModal.show("governate-create-avenue");
                this.create.governorate_id = null;
            }
            if (!id2) {
                return;
            }
            this.cities = [];
            this.create.city_id = null;
            await adminApi
                .get(`/cities?country_id=${id}&governorate_id=${id2}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضافة مدينة", name_e: "Add City" });
                    this.cities = l;
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
        arabicValue(txt){
            this.create.name = arabicValue(txt);
        },

        englishValue(txt){
            this.create.name_e = englishValue(txt);
        }
    },
};
</script>
