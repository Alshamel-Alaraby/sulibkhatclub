<template>
    <div>
        <!--  filter   -->
        <b-modal
            id="filter"
            :title="$t('general.filter')"
            title-class="font-18"
            body-class="p-4 "
            dialog-class="modal-full-width"
            :hide-footer="true"
            @show="resetModalFilter"
            @hidden="resetModalHiddenFilter"
        >
            <form>
                <div class="my-3 d-flex justify-content-end">
                    <template v-if="!is_disabled">
                        <b-button
                            variant="success"
                            type="button"
                            class="mx-1"
                            v-if="!isLoader"
                            @click.prevent="getFilter"
                        >
                            {{ $t("general.filter") }}
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
                        @click.prevent="resetModalHiddenFilter"
                    >
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("car_car_brand") }}
                                <span class="text-danger">*</span>
                            </label>
                            <multiselect
                                @input="showBrandModal"
                                v-model="create.brand_id"
                                :options="brands.map((type) => type.id)"
                                :custom-label="
                                          (opt) => brands.find((x) => x.id == opt)?
                                                $i18n.locale == 'ar'? brands.find((x) => x.id == opt).name:
                                                brands.find((x) => x.id == opt).name_e:
                                                null
                                          "
                            >
                            </multiselect>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("car_car_model") }}
                                <span class="text-danger">*</span>
                            </label>
                            <multiselect
                                v-model="create.model_id"
                                :options="models.map((type) => type.id)"
                                :custom-label="
                                          (opt) => models.find((x) => x.id == opt)?
                                                $i18n.locale == 'ar'? models.find((x) => x.id == opt).name:
                                                models.find((x) => x.id == opt).name_e:
                                                null
                                          "
                            >
                            </multiselect>
                        </div>
                    </div>
                    <div class="col-md-3" >
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("financial_year_year") }}
                                <span class="text-danger">*</span>
                            </label>
                            <date-picker
                                type="year"
                                v-model="create.year_manufacture"
                                format="YYYY"
                                valueType="format"
                                :confirm="false"
                            ></date-picker>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("car_car_sunroof") }}
                                <span class="text-danger">*</span>
                            </label>
                            <multiselect
                                v-model="create.sunroof_id"
                                :options="sunroofs.map((type) => type.id)"
                                :custom-label="
                                          (opt) => sunroofs.find((x) => x.id == opt)?
                                                $i18n.locale == 'ar'? sunroofs.find((x) => x.id == opt).name:
                                                sunroofs.find((x) => x.id == opt).name_e:
                                                null
                                          "
                            >
                            </multiselect>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("car_car_body_type") }}
                                <span class="text-danger">*</span>
                            </label>
                            <multiselect
                                v-model="create.body_type_id"
                                :options="body_types.map((type) => type.id)"
                                :custom-label="
                                              (opt) => body_types.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'? body_types.find((x) => x.id == opt).name:
                                                    body_types.find((x) => x.id == opt).name_e:
                                                    null
                                            "
                            >
                            </multiselect>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="field-1" class="control-label">
                                {{ getCompanyKey("car_car_cylinders") }}
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                data-create="1"
                                v-model="$v.create.cylinders.$model"
                                :class="{
                                              'is-invalid':
                                                $v.create.cylinders.$error ||
                                                errors.cylinders,
                                              'is-valid':
                                                !$v.create.cylinders.$invalid &&
                                                !errors.cylinders,
                                            }"
                                id="field-1"
                            />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("car_car_color_seats_material") }}
                                <span class="text-danger">*</span>
                            </label>
                            <multiselect
                                v-model="create.seats_material_id"
                                :options="seats_materials.map((type) => type.id)"
                                :custom-label="
                                              (opt) => seats_materials.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'? seats_materials.find((x) => x.id == opt).name:
                                                    seats_materials.find((x) => x.id == opt).name_e:
                                                    null
                                            "
                            >
                            </multiselect>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("car_car_passengers") }}
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                v-model.number="$v.create.passengers.$model"
                                :class="{
                                              'is-invalid':
                                                $v.create.passengers.$error ||
                                                errors.passengers,
                                              'is-valid':
                                                !$v.create.passengers.$invalid &&
                                                !errors.passengers,
                                            }"
                            />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("car_car_specifications") }}
                                <span class="text-danger">*</span>
                            </label>
                            <multiselect
                                :multiple="true"
                                v-model="create.specifications"
                                :options="specifications.map((type) => type.id)"
                                :custom-label="
                                          (opt) => specifications.find((x) => x.id == opt)?
                                                $i18n.locale == 'ar'? specifications.find((x) => x.id == opt).name:
                                                specifications.find((x) => x.id == opt).name_e:
                                                null
                                          "
                            >
                            </multiselect>
                        </div>
                    </div>
                </div>
            </form>
        </b-modal>
        <!--  /filter   -->
        <!--  create   -->
        <b-modal
            :id="'create'"
            :title="$t('general.pricing')"
            title-class="font-18"
            body-class="p-4"
            :hide-footer="true"
            @show="resetModal"
            @hidden="resetModalHidden"
        >
            <form>
                <div class="mb-3 d-flex justify-content-end">
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
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("car_car_daily_price") }}
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                step="0.01"
                                v-model.number="$v.edit.daily_price.$model"
                                :class="{
                                  'is-invalid':
                                    $v.edit.daily_price.$error ||
                                    errors.daily_price,
                                  'is-valid':
                                    !$v.edit.daily_price.$invalid &&
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
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("car_car_weekly_price") }}
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                step="0.01"
                                v-model.number="$v.edit.weekly_price.$model"
                                :class="{
                                  'is-invalid':
                                    $v.edit.weekly_price.$error ||
                                    errors.weekly_price,
                                  'is-valid':
                                    !$v.edit.weekly_price.$invalid &&
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
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("car_car_monthly_price") }}
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                step="0.01"
                                v-model.number="$v.edit.monthly_price.$model"
                                :class="{
                                  'is-invalid':
                                    $v.edit.monthly_price.$error ||
                                    errors.monthly_price,
                                  'is-valid':
                                    !$v.edit.monthly_price.$invalid &&
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
import {maxLength, minLength, requiredIf,minValue,maxValue,required,} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";

export default {
    name: "group",
    components: {
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/car-rent/car-cars'},
        tables: {default: []}
    },
    data() {
        return {
            fields: [],
            isLoader: false,
            isCustom:false,
            company_id:null,
            create: {
                brand_id: null, // 1
                model_id: null, // 2
                year_manufacture: null, // 4
                sunroof_id: null, //7
                body_type_id: null, // 8
                cylinders: 0, //10
                seats_material_id: null,
                passengers: 0,//14
                specifications: [],//17
            },
            edit:{
                daily_price: 0,
                weekly_price: 0,
                monthly_price: 0
            },
            errors: {},
            is_disabled: false,
            brands: [],
            models: [],
            transmissions: [],
            sunroofs: [],
            body_types: [],
            seats_materials: [],
            specifications: []
        };
    },
    validations: {
        create: {
            brand_id: {},
            model_id: {},
            year_manufacture: {},
            sunroof_id: {},
            body_type_id: {},
            cylinders: {},
            seats_material_id: {},
            passengers: {},
            specifications: {},
        },
        edit: {
            daily_price: {required,minValue: minValue(0)},
            weekly_price: {required,minValue: minValue(0)},
            monthly_price: {required,minValue: minValue(0)}
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        getFilter(){
            this.isLoader = true;
            let filter = '';
            filter += this.create.brand_id ? `brand_id=${this.create.brand_id}&` : ``;
            filter += this.create.model_id ? `model_id=${this.create.model_id}&` : ``;
            filter += this.create.year_manufacture ? `year_manufacture=${this.create.year_manufacture}&` : ``;
            filter += this.create.sunroof_id ? `sunroof_id=${this.create.sunroof_id}&` : ``;
            filter += this.create.body_type_id ? `body_type_id=${this.create.body_type_id}&` : ``;
            filter += this.create.cylinders ? `cylinders=${this.create.cylinders}&` : ``;
            filter += this.create.seats_material_id ? `seats_material_id=${this.create.seats_material_id}&` : ``;
            filter += this.create.passengers ? `passengers=${this.create.passengers}&` : ``;
            if(this.create.specifications.length > 0){
                for (let i = 0; i < this.create.specifications.length ; ++i) {
                    filter += `specifications[${i}]=${this.create.specifications[i]}&`;
                }
            }
            this.$emit("getDataTable",filter);
            setTimeout(() => {
                this.isLoader = false;
            },1000);
        },
        defaultData(edit = null){
            this.edit = {
                daily_price: 0,
                weekly_price: 0,
                monthly_price: 0
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        defaultDataFilter(edit = null){
            this.create = {
                brand_id: null, // 1
                model_id: null, // 2
                year_manufacture: null, // 4
                sunroof_id: null, //7
                body_type_id: null, // 8
                cylinders: 0, //10
                seats_material_id: null,
                passengers: 0,//14
                specifications: [],//17;
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabled = false;
            this.daily_price = 0;
            this.weekly_price = 0;
            this.monthly_price = 0;
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide('create');
        },
        resetModalHiddenFilter() {
            this.defaultData();
            this.$bvModal.hide('filter');
        },
        resetModal() {
            this.defaultData();
        },
        resetModalFilter() {
            this.defaultDataFilter();
            setTimeout( async () => {
                this.getBrand();
                this.getSunroof();
                this.bodyType();
                this.seatsMaterial();
                this.specification();
            },50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {

            this.$v.edit.$touch();

            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                adminApi
                    .post(`${this.url}/update`, {
                        ...this.edit,
                        company_id:this.company_id,
                        car_ids: this.tables.length > 0 ? this.tables.map(el => el.id) : []
                    })
                    .then((res) => {
                        this.is_disabled = true;

                        this.$emit('emptyTable');
                        this.$bvModal.hide('create');

                        setTimeout(() => {
                            this.successFun('Addedsuccessfully');
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
        },
        showBrandModal() {
            if (this.create.brand_id > 0) {
                this.create.model_id = null;
                this.models = this.brands.find( el => el.id == this.create.brand_id).children;
            }else {
                this.create.model_id = null;
                this.models = [];
            }
        },
        async getBrand() {
            this.isLoader = true;

            await adminApi
                .get(`/brands?isModel=1`)
                .then((res) => {
                    let l = res.data.data;
                    this.brands = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getTransmission() {
            this.isLoader = true;

            adminApi
                .get(`/car-rent/car-transmissions`)
                .then((res) => {
                    let l = res.data.data;
                    this.transmissions = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getSunroof(){
            this.isLoader = true;

            adminApi
                .get(`/car-rent/car-sunroofs`)
                .then((res) => {
                    let l = res.data.data;
                    this.sunroofs = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        bodyType(){
            this.isLoader = true;

            adminApi
                .get(`/car-rent/car-body-types`)
                .then((res) => {
                    let l = res.data.data;
                    this.body_types = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        fuelType(){
            this.isLoader = true;

            adminApi
                .get(`/car-rent/car-fuel-types`)
                .then((res) => {
                    let l = res.data.data;
                    this.fuel_types = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        seatsMaterial(){
            this.isLoader = true;

            adminApi
                .get(`/car-rent/car-seats-material`)
                .then((res) => {
                    let l = res.data.data;
                    this.seats_materials = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        specification(){
            this.isLoader = true;

            adminApi
                .get(`/car-rent/car-specifications`)
                .then((res) => {
                    let l = res.data.data;
                    this.specifications = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
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
