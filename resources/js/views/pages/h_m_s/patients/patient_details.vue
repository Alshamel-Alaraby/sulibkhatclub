<template>
    <b-modal id="patientDetails" size="xl" title-class="font-18" body-class="p-4 " :title="$t('general.patientDetails')">

        <b-tabs nav-class="nav-tabs nav-bordered w-100">
            <loader size="large" v-if="isLoader" style="margin:0px -37px;" />

            <b-tab :title="$t('general.Health History')" active>

                <div class="row">
                    <healthHistory :patient_id="id" @created="get_patient_data" :histories="patient.health_history ?? []" />
                </div>

            </b-tab>
            <b-tab :title="$t('general.Medical Files')">

                <div class="row">
                    <MedicalFiles :patient_id="id" @created="get_patient_data"
                        :medical_files="patient.medical_files ?? []" />
                </div>

            </b-tab>
            <b-tab :title="$t('general.Appointments')">

                <div class="row">
                    <div class="d-inline-block mb-3">
                        <b-button @click.prevent="$bvModal.show(`createAppointment`);" variant="primary"
                            v-if="isPermission('create Appointment')" class="font-weight-bold">
                            {{ $t("general.Create") }}
                            <i class="fas fa-plus"></i>
                        </b-button>
                    </div>

                    <appointmentTable :appointments="patient.appointments ?? []" :doctors="doctors" :patients="[]"
                        :patient_details_id="id" :diagnosis_tests="diagnosis_tests" :drugs="drugs"
                        :permissionUpdate="isPermission('update Appointment')"
                        :permissionDelete="isPermission('delete Appointment')"
                        :permissionCreatePrescription="isPermission('create Prescription')"
                        :permissionCreateInvoice="isPermission('create AppointmentInvoice')"
                        @getAppointments="get_patient_data()" />

                    <!--  create   -->
                    <ModalAppointment :id="'createAppointment'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                        :patient_details_id="id" :url="'h_m_s/appointments'" :doctors="doctors" :patients="[]"
                        :branches="branches" :isPage="false" :isVisiblePage="isVisible" :isRequiredPage="isRequired"
                        :type="'create'" @created="get_patient_data(); $bvModal.hide(`createAppointment`)"
                        :isPermission="isPermission" v-if="isPermission('create Appointment')" />

                </div>

            </b-tab>

            <b-tab :title="$t('general.Prescriptions')">
                <div class="d-inline-block mb-3">
                    <b-button @click.prevent="$bvModal.show(`createPrescriptionInPatient`); type = 'create'" variant="primary"
                        v-if="isPermission('create Appointment')" class="font-weight-bold">
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
                            :tableSetting="tableSetting" :enabled3="true"
                            @delete="ids => delete_prescription(ids)"
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

            <b-tab :title="$t('general.Payment History')">

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

export default {
    name: 'PatientDetails',
    props: ['patient_id'],
    mixins: [transMixinComp, translation, customTable, crudHelper,successError],
    components: {
        loader, healthHistory, MedicalFiles, PrescriptionModal, appointmentTable, tableCustom, ModalAppointment
    },
    data() {
        return {
            isLoader: false,
            id: null,
            patient: [],
            doctors: [],
            drugs: [],
            branches: [],
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
    },
    methods: {
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
                .get(`/branches/get-drop-down?company_id=${this.company_id}&notParent=1`)
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
                confirmButtonText:`${this.$t("general.Yesdeleteit")}`,
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
