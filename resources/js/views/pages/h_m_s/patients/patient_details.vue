<template>
    <b-modal id="patientDetails" size="xll" title-class="font-18" body-class="p-4 " :title="$t('general.patientDetails')">
        <ModalService
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            id="addService"
            :isPage="false"
            type="create"
            :isPermission="isPermission"
            @created="getServices()"
        />
        <InsuranceCompany :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" id="addCompanyInsurance"
                            :isPage="false" type="create" :isPermission="isPermission" @created="get_insurance_companies" />
        <b-tabs nav-class="nav-tabs nav-bordered w-100">
            <loader size="large" v-if="isLoader" style="margin:0px -37px;" />

            <b-tab :title="$t('general.Health History')" v-if="isPermission('show Health History')" active>

                <div class="row">
                    <healthHistory :patient_id="id" @created="get_patient_data" :histories="patient.health_history ?? []"
                        :createHealthHistory="isPermission('create Health History')"
                        :deleteHealthHistory="isPermission('delete Health History')" />
                </div>

            </b-tab>
            <b-tab :title="$t('general.Medical Files')" v-if="isPermission('show Medical Files')">

                <div class="row">
                    <MedicalFiles :patient_id="id" @created="get_patient_data"
                        :createMedicalFiles="isPermission('create Medical Files')"
                        :deleteMedicalFiles="isPermission('delete Medical Files')"
                        :medical_files="patient.medical_files ?? []" />
                </div>

            </b-tab>
            <b-tab :title="$t('general.Appointments')"
                v-if="isPermission('show Patient Appointments') || isPermission('all Appointment')">

                <div class="row">
                    <div class="d-inline-block mb-3">
                        <b-button @click.prevent="$bvModal.show(`createAppointment`);" variant="primary"
                            v-if="isPermission('create Patient Appointments') || isPermission('create Appointment')"
                            class="font-weight-bold">
                            {{ $t("general.Create") }}
                            <i class="fas fa-plus"></i>
                        </b-button>
                    </div>

                    <appointmentTable :appointments="patient.appointments ?? []" :doctors="doctors" :patients="[]"
                        :companies_insurances="companies_insurances" :paymentMethods="paymentMethods" :services="services"
                        :patient_details_id="id" :diagnosis_tests="diagnosis_tests" :drugs="drugs" :branches="branches"

                        :permissionUpdate="isPermission('update Appointment')"
                        :permissionDelete="isPermission('delete Appointment')"
                        :permissionCreatePrescription="isPermission('create Prescription')"
                        :permissionCreateInvoice="isPermission('create HMS Invoice')"
                        @getAppointments="get_patient_data()" />

                    <!--  create   -->
                    <ModalAppointment :id="'createAppointment'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                        :patient_details_id="id" :url="'h_m_s/appointments'" :doctors="doctors" :patients="[]"
                        :branches="branches" :isPage="false" :isVisiblePage="isVisible" :isRequiredPage="isRequired"
                        :type="'create'" @created="get_patient_data(); $bvModal.hide(`createAppointment`)"
                        :isPermission="isPermission"
                        v-if="isPermission('create Patient Appointments') || isPermission('create Appointment')" />

                </div>

            </b-tab>

            <b-tab :title="$t('general.Prescriptions')"
                v-if="isPermission('show Patient Prescriptions') || isPermission('all Prescription')">
                <div class="d-inline-block mb-3">
                    <b-button @click.prevent="$bvModal.show(`createPrescriptionInPatient`); type = 'create'"
                        variant="primary" v-if="isPermission('create Prescription')" class="font-weight-bold">
                        {{ $t("general.Create") }}
                        <i class="fas fa-plus"></i>
                    </b-button>
                </div>
                <div class="row">
                    <div class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table"
                        id="printCustom">
                        <loader size="large" v-if="isLoader" />

                        <tableCustom :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :isLog="false"
                            :tables="patient.prescriptions ?? []" :isEdit="true" :isDelete="true"
                            :permissionUpdate="isPermission('update Prescription')"
                            :permissionDelete="isPermission('delete Prescription')" :isVisible="isVisible"
                            :tableSetting="tableSetting" :enabled3="true" @delete="ids => delete_prescription(ids)"
                            @editRow="id => dbClickRow(id, 'createPrescriptionInPatient')"
                            @checkRowTable="id => checkRow(id)" :isInputCheck="false" :isAction="true" />

                    </div>
                    <PrescriptionModal id="createPrescriptionInPatient" :companyKeys="companyKeys"
                        :defaultsKeys="defaultsKeys" :selected_appointment="{}" :url="'/h_m_s/prescriptions'"
                        :doctors="doctors" :patient_details_id="id" :patients="[]" :drugs="drugs"
                        :diagnosis_tests="diagnosis_tests" :isPage="false" :type="type"
                        :idObjEdit="idEdit ? { idEdit, dataObj: this.patient.prescriptions.find(el => el.id == idEdit) } : null"
                        @created="get_patient_data(); $bvModal.hide(`createPrescriptionInPatient`)"
                        @getDataTable="get_patient_data(); $bvModal.hide(`createPrescriptionInPatient`)" />




                </div>
            </b-tab>

            <b-tab :title="$t('general.Payment History')"
                v-if="isPermission('show Patient Invoices') || isPermission('all HMS Invoice')">
                <DocumentWithItem :document_id="45" :patient_details_id="id" :branches="branches" :doctors="doctors"
                    :services="services" :companies_insurances="companies_insurances" :paymentMethods="paymentMethods" />

            </b-tab>


        </b-tabs>

    </b-modal>
</template>

<script>
import { watch } from 'vue'
import adminApi from '../../../../api/adminAxios'
import loader from "../../../../components/general/loader";
import healthHistory from './health_history.vue';
import transMixinComp from "../../../../helper/mixin/translation-comp-mixin";
import MedicalFiles from './medical_files.vue';
import PrescriptionModal from "../prescriptions/modal.vue"
import appointmentTable from "../appointments/appointment_table.vue";
import ModalAppointment from "../appointments/modal.vue";
import translation from "../../../../helper/mixin/translation-mixin";
import customTable from "../../../../helper/mixin/customTable";
import tableCustom from "../../../../components/general/tableCustom.vue";
import crudHelper from '../../../../helper/mixin/crudHelper';
import Swal from "sweetalert2";
import successError from "../../../../helper/mixin/success&error";
import DocumentWithItem from "../../../../components/document/h_m_s/documentWithItem.vue";
import ModalService from "../services_types/modal.vue";
import InsuranceCompany from "../../../../components/create/general/insuranceCompany";

export default {
    name: 'PatientDetails',
    props: ['patient_id'],
    mixins: [transMixinComp, translation, customTable, crudHelper, successError],
    components: {
        loader, healthHistory, MedicalFiles, PrescriptionModal, appointmentTable,InsuranceCompany, tableCustom, ModalAppointment, DocumentWithItem,ModalService
    },
    data() {
        return {
            isLoader: false,
            id: null,
            patient: [],
            doctors: [],
            drugs: [],
            branches: [],
            companies_insurances:[],
            paymentMethods:[],
            services:[],
            diagnosis_tests: [],
            tableSetting: [

                {
                    isFilter: true, isSet: true, trans: "hms_prescriptions_doctor", isV: 'doctor_id'
                    , type: 'relation', name: 'doctor', sort: false, col1: 'name', col2: 'name_e',
                    setting: { "doctor_id": true }, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "hms_prescriptions_patient", isV: 'patient_id'
                    , type: 'relation', name: 'patient', sort: false, col1: 'name', col2: 'name_e',
                    setting: { "patient_id": true }, isSetting: true
                },
                {
                    isFilter: true, isSet: true, trans: "hms_prescriptions_date", isV: 'date',
                    type: 'date', sort: true, setting: { "date": true }, isSetting: true
                },
                {
                    isFilter: false, isSet: true, trans: "hms_prescriptions_content", isV: 'content',
                    type: 'badge', prop_type: 'array', badge_color: 'badge-primary', sort: false, setting: { "content": true }, isSetting: true
                },

            ],
        }
    },
    watch: {
        patient_id: {
            handler(newVal, oldVal) {
                console.log('new val ' + newVal)
                if (newVal && this.id != newVal) {
                    this.id = newVal
                    this.get_patient_data();
                }
            }
        }
    },

    mounted() {
        this.get_doctors();
        this.getBranch();
        this.get_drugs();
        this.get_diagnosis_tests();
        this.get_insurance_companies()
        this.getPaymentMethod()
        this.getServices()
    },
    methods: {
        get_insurance_companies() {
            adminApi.get(`insurance_companies?drop_down=1&company_id=${this.company_id}`).then((res) => {
                let data = res.data
                        if (this.isPermission("create InsuranceCompany")) {
                            data.unshift({ id: 0, name: "اضف شركة تأمين", name_e: "Add Insurance Company" });
                    }
                    this.companies_insurances = data
            });
        },
        getPaymentMethod() {
            this.isLoader = true;
            adminApi.get(`/payment-methods`)
                .then((res) => {
                    let l = res.data.data;
                    this.paymentMethods = l;
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
        getServices() {
            this.isLoader = true;
            adminApi
                .get(`/h_m_s/service_types`)
                .then((res) => {
                    let data = res.data.data
                        if (this.isPermission("create ServiceType")) {
                            data.unshift({ id: 0, name: "اضف خدمة", name_e: "Add Service" });
                    }
                    this.services = data
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
        get_patient_data() {
            this.isLoader = true
            adminApi.get(`h_m_s/get_patient_data/${this.id}`).then((res) => {
                this.patient = res.data.data
            }).finally(() => {
                this.isLoader = false
            })
        },
        get_doctors() {
            adminApi.get(`h_m_s/doctors?drop_down=1&company_id=${this.company_id}`).then((res) => {
                this.doctors = res.data
            });
        },

        getBranch() {
            this.isLoader = true;
            adminApi
                .get(`/branches?company_id=${this.company_id}&notParent=1&document_id=45`)
                .then((res) => {
                    this.branches = res.data.data;
                })
                .catch((err) => {
                    console.log(err)
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        get_drugs() {
            adminApi.get(`/h_m_s/drugs?drop_down=1`).then((res) => {
                this.drugs = res.data;
            })
                .catch((err) => {
                    console.log(err)
                })
        },
        get_diagnosis_tests() {
            adminApi.get(`/h_m_s/diagnosis_tests?drop_down=1`).then((res) => {
                this.diagnosis_tests = res.data;
            })
                .catch((err) => {
                    console.log(err)
                })
        },
        delete_prescription(prescription_id) {
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
                        .delete(`h_m_s/prescriptions/${prescription_id}`)
                        .then((res) => {
                            this.get_patient_data();
                            this.successFun('Yourrowhasbeendeleted', 'Deleted');
                        })

                        .catch((err) => {
                            console.log(err)
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
            })
        }


    }

}

</script>

<style>
.modal {
    overflow-y: scroll;
}</style>
