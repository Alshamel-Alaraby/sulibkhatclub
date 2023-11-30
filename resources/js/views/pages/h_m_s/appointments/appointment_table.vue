<template>
    <table class="table table-borderless table-hover table-centered m-0">
        <loader size="large" v-if="isLoader" />

        <thead>
            <tr class="text-center">
                <th>{{ getCompanyKey("hms_appointments_doctor") }}</th>
                <th>{{ getCompanyKey("hms_appointments_patient") }}</th>
                <th>{{ getCompanyKey("hms_appointments_from_doctor") }}</th>
                <th>{{ getCompanyKey("hms_appointments_branch") }}</th>
                <th>{{ getCompanyKey("hms_appointments_room") }}</th>
                <th>{{ getCompanyKey("hms_appointments_date") }}</th>
                <th>{{ getCompanyKey("hms_appointments_times") }}</th>
                <th>{{ getCompanyKey("hms_appointments_number") }}</th>
                <th>{{ getCompanyKey("hms_appointments_status") }}</th>
                <th>{{ getCompanyKey("hms_appointments_created_at") }}</th>
                <th>{{ getCompanyKey("hms_appointments_number_of_prescriptions") }}</th>

                <th>{{ $t('general.Actions') }}</th>
            </tr>
        </thead>
        <tbody v-if="appointments.length > 0">
            <tr v-for="appointment in appointments" :key="appointment.id">
                <td>{{ $i18n.locale == 'ar' ? appointment.doctor.name : appointment.doctor.name_e }}
                </td>
                <td @click.prevent="show_patient_details(appointment.patient_id)">
                    <span :style="patient_details_id ? '' : 'display:inline-block;border-bottom:1px solid red;padding-bottom:2px;cursor:pointer'">
                        {{ $i18n.locale == 'ar' ? appointment.patient.name : appointment.patient.name_e }}
                    </span>
                </td>
                <td>{{ appointment.from_doctor ? ($i18n.locale == 'ar' ? appointment.from_doctor.name :
                    appointment.from_doctor.name_e) : '-' }}</td>
                <td>{{ $i18n.locale == 'ar' ? appointment.branch.name : appointment.branch.name_e }}
                </td>
                <td>{{ $i18n.locale == 'ar' ? appointment.room.name : appointment.room.name_e }}</td>
                <td>
                    <label class="badge badge-primary p-2 " style="border-radius:2rem"><i class="fas fa-calendar"></i> {{
                        appointment.date }}</label>
                </td>
                <td style="max-width:320px" class="d-flex flex-wrap justify-content-center">
                    <!-- <label class="badge badge-dark text-white  p-2 mx-1 col" style="border-radius:2rem;max-width:140px"
                        v-for="time in appointment.times" :key="time.id"><i class="fa fa-clock"></i> {{
                            time.start + " - " + time.end }}</label> -->

                    <label class="badge badge-dark text-white  p-2 mx-1 col" style="border-radius:2rem;max-width:140px"><i class="fa fa-clock"></i>
                     {{ appointment.final_time.start + " - " + appointment.final_time.end }}</label>
                </td>
                <td>{{ appointment.number }}</td>
                <td>

                    <label class="badge badge-dark text-white" v-show="appointment.status == 'Pending'">
                        <i class="fas fa-hourglass-start"></i> {{ $t('general.Not Yet Visited') }}
                    </label>
                    <label class="badge badge-success text-white" v-show="appointment.status == 'Attended'">
                        <i class="fas fa-check"></i> {{ $t('general.Attended') }}
                    </label>
                    <label class="badge badge-warning" v-show="appointment.status == 'Waiting'">
                        <i class="fas fa-hourglass-start"></i> {{ $t('general.Waiting') }}
                    </label>
                    <label class="badge badge-primary" v-show="appointment.status == 'Processing'">
                        <i class="fas fa-cogs"></i> {{ $t('general.Processing') }}
                    </label>
                    <label class="badge badge-danger" v-show="appointment.status == 'Cancelled'">
                        <i class="fas fa-user-times"></i> {{ $t('general.Cancelled') }}
                    </label>
                </td>
                <td>{{ appointment.created_at }}</td>
                <td>{{ appointment.number_of_prescriptions }}</td>
                <td>
                    <div class="btn-group" v-if="appointment.status != 'Cancelled'">

                        <button type="button" data-toggle="dropdown" aria-expanded="false"
                            class="btn btn-dark brd-20 dropdown-toggle btn-sm">
                            <i class="fas fa-cogs text-primary"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-custom">
                            <a class="dropdown-item" href="#"
                                @click.prevent="go_to_create_prescription(appointment.doctor_id, appointment.patient_id,appointment.id)"
                                v-if="appointment.status == 'Attended' && permissionCreatePrescription">

                                {{ $t('general.Prescription') }} <i class="fas fa-fw fa-prescription"></i></a>
                            <a class="dropdown-item"
                                @click.prevent="go_to_create_invoice(appointment.doctor_id, appointment.patient_id, appointment.from_doctor_id)"
                                v-if="appointment.status != 'Cancelled' && appointment.status != 'Pending' && permissionCreateInvoice" href="#">
                                {{ $t('general.Create Invoice') }} <i class="fa fa-plus"></i></a>

                            <template v-if="appointment.date == new Date().toISOString().slice(0, 10) && permissionUpdate">
                                <a @click.prevent="take_action(appointment.id, 'Pending')" href="#" class="dropdown-item"
                                    v-if="appointment.status == 'Waiting' || appointment.status == 'Processing'">{{
                                        $t('general.Not Yet Visited') }} <i class="fas fa-hourglass-start text-dark"></i></a>
                                <a @click.prevent="take_action(appointment.id, 'Waiting')" href="#" class="dropdown-item"
                                    v-if="appointment.status == 'Pending' || appointment.status == 'Processing'">{{
                                        $t('general.Waiting') }} <i class="fas fa-hourglass-start text-warning"></i></a>
                                <a @click.prevent="take_action(appointment.id, 'Processing')" href="#" class="dropdown-item"
                                    v-if="appointment.status == 'Pending' || appointment.status == 'Waiting'">{{
                                        $t('general.Processing') }} <i class="text-primary fas fa-cogs"></i></a>
                                <a v-if="appointment.status != 'Attended'"
                                    @click.prevent="take_action(appointment.id, 'Attended')" href="#"
                                    class="dropdown-item">{{ $t('general.Attended') }} <i
                                        class="text-success fas fa-check"></i></a>
                            </template>
                            <template v-if="appointment.status != 'Attended'">

                                <a @click.prevent="take_action(appointment.id, 'Cancelled')" href="#" v-if="permissionUpdate"
                                    class="dropdown-item">{{
                                        $t('general.cancel') }}<i class="text-danger fas fa-times"></i></a>

                                <a class="dropdown-item" href="#" @click.prevent="take_action(appointment.id, 'delete')" v-if="permissionDelete">{{
                                    $t('general.Delete') }}
                                    <i class="text-danger fas fa-trash"></i></a>

                            </template>

                        </div>
                    </div>

                </td>
            </tr>

        </tbody>
        <tbody v-else>
            <tr>
                <th class="text-center" colspan="30">
                    {{ $t("general.notDataFound") }}
                </th>
            </tr>
        </tbody>

        <PrescriptionModal
            :id="'createPrescription'" :patient_details_id="patient_details_id" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :url="'/h_m_s/prescriptions'" :doctors="doctors" :selected_appointment="selected_appointment"
            :patients="patients" :drugs="drugs" :diagnosis_tests="diagnosis_tests"
            :isPage="false" :type="'create'" @created="$emit('getAppointments');$bvModal.hide(`createPrescription`)"
        />


    </table>
</template>


<script>
import loader from "../../../../components/general/loader.vue";
import adminApi from '../../../../api/adminAxios';
import successError from "../../../../helper/mixin/success&error";
import Swal from "sweetalert2";
import translation from "../../../../helper/mixin/translation-mixin";
import PrescriptionModal from "../prescriptions/modal.vue"
export default {
    data() {
        return {
            isLoader: false,
            selected_appointment:{},
        }
    },
    mixins: [translation, successError],
    props: ['appointments','doctors','drugs','diagnosis_tests','patient_details_id','patients','permissionUpdate','permissionDelete','permissionCreateInvoice','permissionCreatePrescription'],
    components: { loader,PrescriptionModal },
    methods: {
        show_patient_details(patientId){
            if(!this.patient_details_id){
                this.$emit('showPatientDetails',patientId)
            }
        },
        go_to_create_prescription(doctor_id, patient_id,appointment_id) {
            this.selected_appointment = {
                doctor_id:doctor_id,
                patient_id:patient_id,
                appointment_id:appointment_id
            };
            this.$bvModal.show(`createPrescription`);

        },
        go_to_create_invoice(doctor_id, patient_id, from_doctor_id) {

        },
        take_action(appointment_id, action) {
            Swal.fire({
                title: `${this.$t("general.Areyousure")}`,
                text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                type: "warning",
                showCancelButton: true,
                confirmButtonText: action == 'delete' ? `${this.$t("general.Yesdeleteit")}` : `${this.$t("general.YesChangeStatus")}`,
                cancelButtonText: `${this.$t("general.Nocancel")}`,
                confirmButtonClass: "btn btn-success mt-2",
                cancelButtonClass: "btn btn-danger ml-2 mt-2",
                buttonsStyling: false,
            }).then((result) => {
                if (result.value) {
                    this.isLoader = true;

                    if (action == 'delete')
                        adminApi
                            .delete(`h_m_s/appointments/${appointment_id}`)
                            .then((res) => {
                                this.$emit('getAppointments');
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

                    else
                        adminApi
                            .post(`h_m_s/change_appointment_status/${appointment_id}`, { action })
                            .then((res) => {
                                this.$emit('getAppointments');
                                this.successFun('Editsuccessfully');
                            })

                            .catch((err) => {
                                console.log(err)
                                if (err.response.status == 400) {
                                    this.errorFun('Error', 'CantChangeStatus');
                                } else {
                                    this.errorFun('Error', 'Thereisanerrorinthesystem');
                                }
                            })
                            .finally(() => {
                                this.isLoader = false;
                            });

                }
            });
        },
    }
}
</script>
<style scoped>
.badge {
    padding: 11px;
    font-size: 14px;
    border-radius: 2rem;
}</style>
