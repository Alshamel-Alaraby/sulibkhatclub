<template>
    <div>
        <!--  create   -->
        <b-modal :id="id"
            :title="type != 'edit' ? getCompanyKey('hms_prescriptions_create_form') : getCompanyKey('hms_prescriptions_edit_form')"
            title-class="font-18" body-class="p-4 " size="xl" style="max-width: 1500px;" :hide-footer="true"
            @show="resetModal" @hidden="resetModalHidden">
            <form>
                <loader size="large" v-if="isCustom && !isPage" />
                <div class="mb-3 d-flex justify-content-end">
                    <b-button v-if="type != 'edit'" variant="success" :disabled="!is_disabled" @click.prevent="resetForm"
                        type="button" :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                        {{ $t("general.AddNewRecord") }}
                    </b-button>
                    <template v-if="!is_disabled">
                        <b-button variant="success" type="button" class="mx-1" @click.prevent="AddSubmit" v-if="!isLoader">
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
                <h4 v-if="errors && Object.keys(errors ?? []).length">{{ $t('general.There is an error in data') }}</h4>
                <b-tabs nav-class="nav-tabs nav-bordered w-100">
                    <b-tab :title="$t('general.Doctor And Patient')" active
                        v-if="Object.keys(selected_appointment).length == 0">
                        <div class="row">
                            <div class="col-md-6" v-if="isVisible('doctor_id')">
                                <div class="form-group">
                                    <label>
                                        {{ getCompanyKey("hms_prescriptions_doctor") }}
                                    </label>
                                    <multiselect @input="showDoctorhModal" v-model="create.doctor_id" :options="doctors.map((type) => type.id)"
                                        :disabled="type == 'edit'" :custom-label="(opt) => doctors.find((x) => x.id == opt) ?
                                            $i18n.locale == 'ar'
                                                ? doctors.find((x) => x.id == opt).name
                                                : doctors.find((x) => x.id == opt).name_e : null"
                                        :class="{ 'is-invalid': errors.doctor_id || $v.create.doctor_id.$error }">
                                    </multiselect>
                                    <div v-if="$v.create.doctor_id.$error" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.doctor_id">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.doctor_id" :key="index">{{
                                            errorMessage
                                        }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-6" v-if="isVisible('patient_id') && !patient_details_id">
                                <div class="form-group">
                                    <label>
                                        {{ getCompanyKey("hms_prescriptions_patient") }}
                                    </label>
                                    <multiselect @input="showPatientModal" v-model="create.patient_id" :options="patients.map((type) => type.id)"
                                        :disabled="type == 'edit'" :custom-label="(opt) => patients.find((x) => x.id == opt) ?
                                            $i18n.locale == 'ar'
                                                ? patients.find((x) => x.id == opt).name
                                                : patients.find((x) => x.id == opt).name_e : null"
                                        :class="{ 'is-invalid': errors.patient_id || $v.create.patient_id.$error }">
                                    </multiselect>
                                    <div v-if="$v.create.patient_id.$error" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.patient_id">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.patient_id" :key="index">{{
                                            errorMessage
                                        }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                        </div>

                    </b-tab>


                    <b-tab :title="$t('general.Prescription')" :active="Object.keys(selected_appointment).length != 0">
                        <div class="card shadow mb-4 col-12 p-2 mt-4">
                            <div class="card-header py-3 d-flex justify-content-between">
                                <h4>{{ getCompanyKey('hms_prescriptions_drug_list') }}</h4>
                                <button @click.prevent="add_new_drug" class="btn btn-primary me-2 mt-2">
                                    <i class="fa fa-plus"></i>
                                    {{ $t('general.AddDrug') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="row col-12" v-for="(time, index) in create.drug_list" :key="index">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>
                                                {{ getCompanyKey("hms_prescriptions_drugs") }}
                                            </label>
                                            <multiselect @input="showDrugModal(index)" v-model="create.drug_list[index].drug_id"
                                                :options="drugs.map((type) => type.id)" :custom-label="(opt) => drugs.find((x) => x.id == opt) ?
                                                    $i18n.locale == 'ar'
                                                        ? drugs.find((x) => x.id == opt).trade_name
                                                        : drugs.find((x) => x.id == opt).trade_name_e : null">
                                            </multiselect>

                                            <template v-if="errors && errors[`drug_list.${index}.drug_id`]">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors[`drug_list.${index}.drug_id`]"
                                                    :key="index">{{ errorMessage
                                                    }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>


                                    <div class="col-md-3">
                                        <label>
                                            {{ getCompanyKey("hms_prescriptions_type") }}
                                        </label>
                                        <input type="text" class="form-control" v-model="create.drug_list[index].type">
                                        <template v-if="errors && errors[`drug_list.${index}.type`]">
                                            <ErrorMessage v-for="(errorMessage, index) in errors[`drug_list.${index}.type`]"
                                                :key="index">{{ errorMessage
                                                }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                    <div class="col-md-3">
                                        <label>
                                            {{ getCompanyKey("hms_prescriptions_strength") }}
                                        </label>
                                        <input type="text" class="form-control" v-model="create.drug_list[index].strength"
                                            placeholder="Mg/Ml">
                                        <template v-if="errors && errors[`drug_list.${index}.strength`]">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors[`drug_list.${index}.strength`]"
                                                :key="index">{{ errorMessage
                                                }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                    <div class="col-md-3">
                                        <label>
                                            {{ getCompanyKey("hms_prescriptions_dose") }}
                                        </label>
                                        <input type="text" class="form-control" v-model="create.drug_list[index].dose">
                                        <template v-if="errors && errors[`drug_list.${index}.dose`]">
                                            <ErrorMessage v-for="(errorMessage, index) in errors[`drug_list.${index}.dose`]"
                                                :key="index">{{ errorMessage
                                                }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                    <div class="col-md-3">
                                        <label>
                                            {{ getCompanyKey("hms_prescriptions_duration") }}
                                        </label>
                                        <input type="text" class="form-control" v-model="create.drug_list[index].duration">
                                        <template v-if="errors && errors[`drug_list.${index}.duration`]">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors[`drug_list.${index}.duration`]"
                                                :key="index">{{ errorMessage
                                                }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                    <div class="col-md-6">
                                        <label>
                                            {{ getCompanyKey("hms_prescriptions_drug_advice") }}
                                        </label>
                                        <textarea class="form-control" v-model="create.drug_list[index].drug_advice"
                                            cols="30" rows="1"></textarea>
                                        <template v-if="errors && errors[`drug_list.${index}.drug_advice`]">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors[`drug_list.${index}.drug_advice`]"
                                                :key="index">{{ errorMessage
                                                }}
                                            </ErrorMessage>
                                        </template>
                                    </div>

                                    <div class="col-md-2 d-flex  justify-content-center align-items-center">

                                        <button v-if="index" @click.prevent="delete_drug(index)"
                                            class="btn btn-danger mx-2 col-6">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
                                    </div>

                                    <template v-if="errors && errors[`drug_list.${index}`]">
                                        <div class="mx-s">
                                            <ErrorMessage v-for="(errorMessage, index) in errors[`drug_list.${index}`]"
                                                :key="index">{{
                                                    $t(`general.` + errorMessage) }}</ErrorMessage>
                                        </div>

                                    </template>


                                </div>
                            </div>

                        </div>
                    </b-tab>
                    <b-tab :title="$t('general.Diagnosis Tests')">

                        <div class="card shadow mb-4 col-12 p-2 mt-4">
                            <div class="card-header py-3 d-flex justify-content-between">
                                <h4>{{ getCompanyKey('hms_prescriptions_diagnosis_test') }}</h4>
                                <button @click.prevent="add_new_test" class="btn btn-primary me-2 mt-2">
                                    <i class="fa fa-plus"></i>
                                    {{ $t('general.AddTest') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="row col-12">
                                    <div class="col-md-4">
                                        {{ getCompanyKey('hms_prescriptions_diagnosis_test') }}
                                    </div>
                                    <div class="col-md-6">
                                        {{ getCompanyKey('hms_prescriptions_description') }}
                                    </div>
                                </div>
                                <div class="row col-12" v-for="(time, index) in create.diagnosis_test_list" :key="index">
                                    <div class="col-md-4">
                                        <div class="form-group">

                                            <multiselect @input="showDiagnosistestModal(index)" v-model="create.diagnosis_test_list[index].diagnosis_test_id"
                                                :options="diagnosis_tests.map((type) => type.id)" :custom-label="(opt) => diagnosis_tests.find((x) => x.id == opt) ?
                                                    $i18n.locale == 'ar'
                                                        ? diagnosis_tests.find((x) => x.id == opt).name
                                                        : diagnosis_tests.find((x) => x.id == opt).name_e : null">
                                            </multiselect>

                                            <template
                                                v-if="errors && errors[`diagnosis_test_list.${index}.diagnosis_test_id`]">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors[`diagnosis_test_list.${index}.diagnosis_test_id`]"
                                                    :key="index">{{ errorMessage
                                                    }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="col-md-6">

                                        <textarea class="form-control"
                                            v-model="create.diagnosis_test_list[index].description" cols="30"
                                            rows="1"></textarea>
                                        <template v-if="errors && errors[`diagnosis_test_list.${index}.description`]">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors[`diagnosis_test_list.${index}.description`]"
                                                :key="index">{{ errorMessage
                                                }}
                                            </ErrorMessage>
                                        </template>
                                    </div>

                                    <div class="col-md-2 d-flex  justify-content-center align-items-center">

                                        <button v-if="index" @click.prevent="delete_test(index)"
                                            class="btn btn-danger mx-2 col-6">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
                                    </div>

                                    <template v-if="errors && errors[`diagnosis_test_list.${index}`]">
                                        <div class="mx-s">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors[`diagnosis_test_list.${index}`]"
                                                :key="index">{{
                                                    $t(`general.` + errorMessage) }}</ErrorMessage>
                                        </div>

                                    </template>


                                </div>
                            </div>

                        </div>

                    </b-tab>
                </b-tabs>

            </form>
        </b-modal>
    </div>
</template>

<script>
import ErrorMessage from "../../../../components/widgets/errorMessage.vue";
import loader from "../../../../components/general/loader";
import { maxLength, minLength, requiredIf, email } from "vuelidate/lib/validators";
import adminApi from "../../../../api/adminAxios";
import { arabicValue, englishValue } from "../../../../helper/langTransform";
import transMixinComp from "../../../../helper/mixin/translation-comp-mixin";
import successError from "../../../../helper/mixin/success&error";
import Multiselect from "vue-multiselect";

export default {
    name: "prescriptions_modal",
    components: {
        Multiselect,
        ErrorMessage,
        loader,
    },
    mixins: [transMixinComp, successError],
    props: {
        id: { default: "create", }, companyKeys: { default: [], }, defaultsKeys: { default: [], },
        isPage: { default: true }, isVisiblePage: { default: null }, isRequiredPage: { default: null }, patient_details_id: { default: null },
        type: { default: 'create' }, idObjEdit: { default: null }, isPermission: {}, selected_appointment: { Object, default: {} }, url: { default: '/h_m_s/prescriptions' }, doctors: { Array, default: [] }, patients: { Array, default: [] }, drugs: { Array, default: [] }, diagnosis_tests: { Array, default: [] }
    },
    watch: {
        selected_appointment: {
            deep: true,
            handler(newVal, oldVal) {
                if (newVal.appointment_id) {
                    this.create.patient_id = newVal.patient_id
                    this.create.doctor_id = newVal.doctor_id
                    this.create.appointment_id = newVal.appointment_id
                }
            }
        }
    },
    data() {
        return {
            fields: [],
            isCustom: false,
            isLoader: false,
            company_id: null,
            create: {
                doctor_id: null,
                patient_id: null,
                appointment_id: null,
                drug_list: [],
                diagnosis_test_list: []
            },
            errors: {},
            is_disabled: false,
        };
    },
    validations: {
        create: {
            doctor_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("doctor_id");
                }),
            },
            patient_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("patient_id");
                }),
            },

            drug_list: [],
            diagnosis_test_list: [],
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        showDoctorhModal() {
            if (this.create.doctor_id == 0) {
                this.$bvModal.show("addDoctorFromPrescription");
                this.create.doctor_id = null;
            }
        },
        showPatientModal() {
            if (this.create.patient_id == 0) {
                this.$bvModal.show("addPatientFromPrescription");
                this.create.patient_id = null;
            }
        },
        showDrugModal(index) {
            if (this.create.drug_list[index].drug_id == 0) {
                this.$bvModal.show("addDrugFromPrescription");
                this.create.drug_list[index].drug_id = null
            }
        },
        showDiagnosistestModal(index) {
            if (this.create.diagnosis_test_list[index].diagnosis_test_id == 0) {
                this.$bvModal.show("addDiagnosisTestFromPrescription");
                this.create.diagnosis_test_list[index].diagnosis_test_id = null
            }
        },
        add_new_drug() {
            this.create.drug_list.push({
                type: '',
                drug_id: null,
                strength: '',
                dose: '',
                duration: '',
                drug_advice: '',
            })
        },
        add_new_test() {
            this.create.diagnosis_test_list.push({
                diagnosis_test_id: null,
                description: '',

            })
        },
        delete_drug(index) {
            this.create.drug_list.splice(index, 1);
        },
        delete_test(index) {
            this.create.diagnosis_test_list.splice(index, 1);
        },
        getCustomTableFields() {
            this.isCustom = true;
            adminApi
                .get(`/customTable/table-columns/h_m_s_prescriptions`)
                .then((res) => {
                    this.fields = res.data;
                })
                .catch((err) => {
                    console.log(err)
                    // this.errorFun('Error', 'Thereisanerrorinthesystem');
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

                doctor_id: null,
                appointment_id: null,
                patient_id: null,
                drug_list: [
                    {
                        type: '',
                        drug_id: null,
                        strength: '',
                        dose: '',
                        duration: '',
                        drug_advice: '',
                    }
                ],
                diagnosis_test_list: [
                    {
                        diagnosis_test_id: null,
                        description: '',
                    }
                ]
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
            if (!this.isPage && this.fields.length == 0)
                this.getCustomTableFields();
            // if (this.type != 'edit') {

            // }
            setTimeout(() => {
                if (this.type == 'edit') {
                    if (this.idObjEdit.dataObj) {
                        let prescription = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.patient_id = prescription.patient_id;
                        this.create.doctor_id = prescription.doctor_id;
                        this.create.appointment_id = prescription.appointment_id;
                        let drug_list = []
                        prescription.drug_list.forEach(element => {
                            drug_list.push({
                                type: element.type,
                                drug_id: element.drug_id,
                                strength: element.strength,
                                dose: element.dose,
                                duration: element.duration,
                                drug_advice: element.drug_advice,
                            })
                        });
                        this.create.drug_list = drug_list

                        let diagnosis_test_list = []
                        prescription.diagnosis_test_list.forEach(element => {
                            diagnosis_test_list.push({
                                diagnosis_test_id: element.diagnosis_test_id,
                                description: element.description,
                            })
                        });
                        this.create.diagnosis_test_list = diagnosis_test_list
                    }
                }
            }, 50);
        },
        resetForm() {
            this.defaultData();
        },
        async AddSubmit() {
            if (this.patient_details_id)
                this.create.patient_id = this.patient_details_id
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                if (this.type != 'edit') {
                    await adminApi
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
                    this.isLoader = false;

                } else {
                    await adminApi
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

                    this.isLoader = false;

                }

            }
        },


    }
}
</script>

<style scoped>
form {
    position: relative;
}</style>
