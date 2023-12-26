<template>
    <div>
        <!--  create   -->
        <b-modal :id="id"
            :title="type != 'edit' ? getCompanyKey('hms_settings_create_form') : getCompanyKey('hms_settings_edit_form')"
            title-class="font-18" body-class="p-4 " :hide-footer="true" @show="resetModal" @hidden="resetModalHidden">
            <form>
                <loader size="large" v-if="isCustom && !isPage" />
                <div class="mb-3 d-flex justify-content-end">
                    <b-button v-if="type != 'edit'" variant="success" :disabled="!is_disabled" @click.prevent="resetForm"
                        type="button" :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                        {{ $t("general.AddNewRecord") }}
                    </b-button>
                    <template v-if="!is_disabled">
                        <b-button variant="success" type="button" class="mx-1" v-if="!isLoader" @click.prevent="AddSubmit">
                            {{ type != 'edit' ? $t("general.Add") : $t("general.edit") }}
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
                    <div class="col-md-12" v-if="isVisible('interval')">
                        <div class="form-group">
                            <label for="field-1" class="control-label">
                                {{ getCompanyKey('hms_settings_interval') }}
                                <span v-if="isRequired('interval')" class="text-danger">*</span>
                            </label>
                            <div>
                                <select class="form-control" id="field-1" v-model="$v.create.interval.$model" :class="{
                                    'is-invalid': $v.create.interval.$error || errors.interval,
                                    'is-valid': !$v.create.interval.$invalid && !errors.interval,
                                }">
                                    <template v-for="n in 60">
                                        <option :value="n" v-if="n % 5 === 0" :key="n">{{ n }}</option>
                                    </template>
                                </select>

                            </div>

                            <template v-if="errors.interval">
                                <ErrorMessage v-for="(errorMessage, index) in errors.interval" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('taxes')">
                        <div class="form-group">
                            <label for="field-2" class="control-label">
                                {{ getCompanyKey('hms_settings_taxes') }}
                                <span v-if="isRequired('taxes')" class="text-danger">*</span>
                            </label>
                            <div dir="ltr">
                                <input type="number" step="0.01" class="form-control" data-create="2"
                                    v-model="$v.create.taxes.$model" :class="{
                                        'is-invalid': $v.create.taxes.$error || errors.taxes,
                                        'is-valid': !$v.create.taxes.$invalid && !errors.taxes,
                                    }" id="field-2" />
                            </div>

                            <template v-if="errors.taxes">
                                <ErrorMessage v-for="(errorMessage, index) in errors.taxes" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2" v-if="isVisible('is_there_insurance')">
                        <input type="checkbox" id="is_there_insurance" v-model="$v.create.is_there_insurance.$model" :class="{
                            'is-invalid': $v.create.is_there_insurance.$error || errors.is_there_insurance,
                            'is-valid': !$v.create.is_there_insurance.$invalid && !errors.is_there_insurance,
                        }">
                        <label class="form-check-label" for="is_there_insurance">
                            {{ getCompanyKey('hms_settings_is_there_insurance') }}
                            <span v-if="isRequired('is_there_insurance')" class="text-danger">*</span>
                        </label>



                    </div>

                    <template v-if="errors.is_there_insurance">
                        <ErrorMessage v-for="(errorMessage, index) in errors.is_there_insurance" :key="index">{{
                            errorMessage }}</ErrorMessage>
                    </template>
                </div>
            </form>
        </b-modal>
        <!--  /create   -->
    </div>
</template>

<script>
import ErrorMessage from "../../../../components/widgets/errorMessage.vue";
import loader from "../../../../components/general/loader";
import { maxLength, minLength, required } from "vuelidate/lib/validators";
import adminApi from "../../../../api/adminAxios";
import { arabicValue, englishValue } from "../../../../helper/langTransform";
import transMixinComp from "../../../../helper/mixin/translation-comp-mixin";
import successError from "../../../../helper/mixin/success&error";

export default {
    name: "settings_modal",
    components: {
        ErrorMessage,
        loader,
    },
    mixins: [transMixinComp, successError],
    props: {
        id: { default: "create", }, companyKeys: { default: [], }, defaultsKeys: { default: [], },
        isPage: { default: true }, isVisiblePage: { default: null }, isRequiredPage: { default: null },
        type: { default: 'create' }, idObjEdit: { default: null }, isPermission: {}, url: { default: '/h_m_s/settings' }
    },
    data() {
        return {
            fields: [],
            isCustom: false,
            isLoader: false,
            company_id: null,
            create: {
                taxes: "",
                interval: "",
                is_there_insurance: "",
            },
            errors: {},
            is_disabled: false,
        };
    },
    validations: {
        create: {
            is_there_insurance: { required},
            interval: { required},
            taxes: { required},

        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        getCustomTableFields() {
            this.isCustom = true;
            adminApi
                .get(`/customTable/table-columns/h_m_s_settings`)
                .then((res) => {
                    this.fields = res.data;
                })
                .catch((err) => {
                    this.errorFun('Error', 'Thereisanerrorinthesystem');
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
                taxes: "",
                interval: "",
                is_there_insurance: "",
            };
            this.errors = {};
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        resetModal() {
            this.defaultData();
            setTimeout(() => {
                if (this.type != 'edit') {
                    if (!this.isPage) this.getCustomTableFields();
                } else {
                    if (this.idObjEdit.dataObj) {
                        let settings = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.taxes = settings.taxes;
                        this.create.interval = settings.interval;
                        this.create.is_there_insurance = settings.is_there_insurance;
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
                this.is_disabled = false;
                if (this.type != 'edit') {
                    adminApi
                        .post(this.url, { ...this.create, company_id: this.company_id })
                        .then((res) => {
                            this.is_disabled = true;
                            if (!this.isPage)
                                this.$emit("created");
                            else
                                this.$emit("getDataTable");

                            setTimeout(() => {
                                this.successFun('Addedsuccessfully');
                            }, 500);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error', 'Thereisanerrorinthesystem');
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
                                this.successFun('Editsuccessfully');
                            }, 500);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error', 'Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }

            }
        },

    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
