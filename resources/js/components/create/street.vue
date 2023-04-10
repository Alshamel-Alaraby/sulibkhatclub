<template>
    <div>
        <Avenue :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getAvenue" />
        <!--  create   -->
        <b-modal id="create-street" :title="getCompanyKey('street_create_form')" title-class="font-18" body-class="p-4 "
                 :hide-footer="true" @show="resetModal" @hidden="resetModalHidden">
            <form>
                <div class="mb-3 d-flex justify-content-end">
                    <b-button variant="success" :disabled="!is_disabled" @click.prevent="resetForm" type="button"
                              :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                        {{ $t("general.AddNewRecord") }}
                    </b-button>
                    <template v-if="!is_disabled">
                        <b-button variant="success" type="submit" class="mx-1" v-if="!isLoader" @click.prevent="AddSubmit">
                            {{ $t("general.Add") }}
                        </b-button>
                        <b-button variant="success" class="mx-1" disabled v-else>
                            <b-spinner small></b-spinner>
                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                        </b-button>

                    </template>
                    <b-button @click.prevent="$bvModal.hide(`create`)" variant="danger" type="button">
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-12" v-if="isVisible('avenue_id')">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("street_avenue") }}
                                <span v-if="isRequired('avenue_id')" class="text-danger">*</span>
                            </label>
                            <multiselect @input="showBranchModal" v-model="create.avenue_id"
                                         :options="avenues.map((type) => type.id)"
                                         :custom-label="(opt) => avenues.find((x) => x.id == opt).name">
                            </multiselect>
                            <div v-if="$v.create.avenue_id.$error || errors.avenue_id" class="text-danger">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.avenue_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.avenue_id" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('name')">
                        <div class="form-group">
                            <label for="field-1" class="control-label">
                                {{ getCompanyKey("street_name_ar") }}
                                <span v-if="isRequired('name')" class="text-danger">*</span>
                            </label>
                            <div dir="rtl">
                                <input type="text" class="form-control arabicInput" data-create="1"
                                       v-model="$v.create.name.$model" :class="{
                            'is-invalid': $v.create.name.$error || errors.name,
                            'is-valid': !$v.create.name.$invalid && !errors.name,
                          }" @keyup="arabicValue(create.name)" id="field-1" />
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
                                <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('name_e')">
                        <div class="form-group">
                            <label for="field-2" class="control-label">
                                {{ getCompanyKey("street_name_en") }}
                                <span v-if="isRequired('name_e')" class="text-danger">*</span>
                            </label>
                            <div dir="ltr">
                                <input type="text" class="form-control englishInput" data-create="2"
                                       v-model="$v.create.name_e.$model" :class="{
                            'is-invalid': $v.create.name_e.$error || errors.name_e,
                            'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                          }" @keyup="englishValue(create.name_e)" id="field-2" />
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
                                <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('is_active')">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("street_status") }}
                                <span v-if="isRequired('is_active')" class="text-danger">*</span>
                            </label>
                            <b-form-group :class="{
                        'is-invalid': $v.create.is_active.$error || errors.is_active,
                        'is-valid': !$v.create.is_active.$invalid && !errors.is_active,
                      }">
                                <b-form-radio class="d-inline-block" v-model="$v.create.is_active.$model" name="some-radios"
                                              value="active">{{ $t("general.Active") }}</b-form-radio>
                                <b-form-radio class="d-inline-block m-1" v-model="$v.create.is_active.$model" name="some-radios"
                                              value="inactive">{{ $t("general.Inactive") }}</b-form-radio>
                            </b-form-group>
                            <template v-if="errors.is_active">
                                <ErrorMessage v-for="(errorMessage, index) in errors.is_active" :key="index">{{ errorMessage }}
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
import Swal from "sweetalert2";
import {maxLength, minLength, requiredIf} from "vuelidate/lib/validators";
import Layout from "../../views/layouts/main";
import PageHeader from "../Page-header";
import Switches from "vue-switches";
import ErrorMessage from "../widgets/errorMessage";
import loader from "../loader";
import Multiselect from "vue-multiselect";
import Avenue from "./avenue";
import { formatDateOnly } from "../../helper/startDate";
import { arabicValue, englishValue } from "../../helper/langTransform";
import transMixinComp from "../../helper/translation-comp-mixin";

export default {
    name: "street",
    mixins: [transMixinComp],
    props: {
        companyKeys:{
            default:[]
        },
        defaultsKeys:{
            default:[]
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.getCustomTableFields();
    },
    data() {
        return {
            fields: [],
            isLoader: false,
            create: {
                name: "",
                name_e: "",
                avenue_id: "",
                is_active: "active",
            },
            company_id: null,
            errors: {},
            avenues: [],
            is_disabled: false,
        };
    },
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
        Avenue,
    },
    validations: {
        create: {
            name: {
                required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(2), maxLength: maxLength(100),
            },
            name_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(2), maxLength: maxLength(100),
            },
            avenue_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("avenue_id");
                })
            },
            is_active: {
                required: requiredIf(function (model) {
                    return this.isRequired("is_active");
                })
            },
        },
    },
    methods: {
        async getAvenue(){
            this.isLoader = true;
            await adminApi
                .get(`/avenues`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضافة منطقه", name_e: "Add Avenue" });
                    this.avenues = l;
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
        showBranchModal() {
            if (this.create.avenue_id == 0) {
                this.$bvModal.show("avenue-create");
                this.create.avenue_id = null;
            }
        },
        showBranchModalEdit() {
            if (this.edit.avenue_id == 0) {
                this.$bvModal.show("avenue-create");
                this.edit.avenue_id = null;
            }
        },
        /**
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.create = {
                name: "",
                name_e: "",
                avenue_id: "",
                is_active: "active",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabled = false;
            this.$bvModal.hide(`create-street`);
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
                let data = {
                    name: this.create.name,
                    name_e: this.create.name_e,
                    avenue_id: this.create.avenue_id,
                    is_active: this.create.is_active,
                    company_id: this.company_id,
                };
                adminApi
                    .post(`/streets`, data)
                    .then((res) => {
                        this.is_disabled = true;
                        this.$emit('created');
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
         *  hidden Modal (create)
         */
        async resetModal() {
            if (this.isVisible('avenue_id')) await this.getAvenue();
            this.create = {
                name: "",
                name_e: "",
                avenue_id: null,
                is_active: "active",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  create countrie
         */
        async resetForm() {
            if (this.isVisible('avenue_id')) await this.getAvenue();
            this.create = {
                name: "",
                name_e: "",
                avenue_id: null,
                is_active: "active",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
        },
        getCustomTableFields() {
            adminApi
                .get(`/customTable/table-columns/general_streets`)
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
        arabicValue(txt) {
            this.create.name = arabicValue(txt);
            this.edit.name = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.name_e = englishValue(txt);
            this.edit.name_e = englishValue(txt);
        }
    }
}
</script>

<style scoped>

</style>
