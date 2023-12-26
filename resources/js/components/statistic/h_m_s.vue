<script>
// import Layout from "../../views/layouts/main";
import Swal from "sweetalert2";
import PageHeader from "../general/Page-header";
import MarketingReports from "../widgets/Marketing-reports";
import adminApi from "../../api/adminAxios";
import h_m_s_patients_ageVue from '../widgets/h_m_s_patients_age.vue';
import h_m_s_patients_blood_typeVue from '../widgets/h_m_s_patients_blood_type.vue';
import h_m_s_appointments_timesVue from '../widgets/h_m_s_appointments_times.vue';
import h_m_s_appointments_for_current_and_last_monthVue from '../widgets/h_m_s_appointments_for_current_and_last_month.vue';
import h_m_s_total_amount_for_each_month_in_yearVue from '../widgets/h_m_s_total_amount_for_each_month_in_year.vue';
import h_m_s_total_and_paid_amount_for_each_doctorsVue from '../widgets/h_m_s_total_and_paid_amount_for_each_doctors.vue';
import h_m_s_total_and_paid_amount_for_each_company_insuranceVue from '../widgets/h_m_s_total_and_paid_amount_for_each_company_insurance.vue';

/**
 * Sales-Dashboard component
 */
export default {
    name: "h_m_s_dashboard",
    components: {
        // Layout,
        PageHeader,
        MarketingReports,
        h_m_s_patients_ageVue,
        h_m_s_patients_blood_typeVue,
        h_m_s_appointments_timesVue,
        h_m_s_appointments_for_current_and_last_monthVue,
        h_m_s_total_amount_for_each_month_in_yearVue,
        h_m_s_total_and_paid_amount_for_each_doctorsVue,
        h_m_s_total_and_paid_amount_for_each_company_insuranceVue,
    },
    data() {
        return {
            title: "Welcome !",
            items: [
                {
                    text: "Minton",
                },
                {
                    text: "Dashboards",
                },
                {
                    text: "Sales",
                    active: true,
                },
            ],
            tableTitle: "HMS Statistics",
            productData: [],
            isLoader: false,
            statices: {}
        };
    },
    mounted() {
        this.getStatices();

    },
    methods: {
        getStatices() {
            this.isLoader = true;

            adminApi.get(`/h_m_s/dashboard`)
                .then((res) => {
                    let l = res.data.data;
                    this.statices = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: 'error',
                        title: `${this.$t('general.Error')}`,
                        text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                    });
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
    }
};
</script>

<template>
    <div>
        <PageHeader :title="title" :items="items" />
        <div class="dashboard-fluid my-3">
            <div class="row">

                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/appointments">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.All Appointments')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.All Appointments') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.total_appointments ?? 0' :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-primary rounded"><i
                                                class="far fa-calendar-check font-28 text-primary"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/appointments/waiting_rooms">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Today Appointments')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Today Appointments') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.total_appointments_today ?? 0' :duration='3000'></countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-warning rounded"><i
                                                        class="fa fa-calendar-day font-28 text-warning"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/appointments/upcoming_appointments">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Upcoming Appointments')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Upcoming Appointments') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.total_upcoming_appointments ?? 0' :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-danger rounded"><i
                                                        class="far fa-calendar-alt font-28 text-danger"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/rooms_reservation">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Rooms Reservations')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Rooms Reservations') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.total_rooms_reservations ?? 0' :duration='3000'></countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-info rounded"><i
                                                        class="fas fa-clipboard-list font-28 text-info"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/rooms_reservation">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Today Reservations')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Today Reservations') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.total_rooms_reservations_today ?? 0' :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-dark rounded"><i
                                                        class="fa fa-calendar-day font-28 text-dark"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/rooms_reservation">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Upcoming Reservations')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Upcoming Reservations') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.total_upcoming_rooms_reservations ?? 0' :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-success rounded"><i
                                                        class="far fa-calendar-alt font-28 text-success"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/patients">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Inpatients')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Inpatients') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0' :endVal='statices.total_in_patients ?? 0'
                                                :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-warning rounded"><i
                                                class="fas fa-user-injured font-28 text-warning"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/patients">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Outpatients')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Outpatients') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.total_out_patients ?? 0' :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-danger rounded"><i
                                                        class="fas fa-ambulance font-28 text-danger"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/doctors">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Internal Doctors')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Internal Doctors') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0' :endVal='statices.total_in_doctors ?? 0'
                                                :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-primary rounded"><i
                                                        class="fas fa-user-md font-28 text-primary"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/doctors">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Outdoctors')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Outdoctors') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0' :endVal='statices.total_out_doctors ?? 0'
                                                :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-info rounded"><i
                                                        class="fa fa-user-md font-28 text-info"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/specialties">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Specialties')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Specialties') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0' :endVal='statices.total_specialties ?? 0'
                                                :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-dark rounded"><i
                                                        class="fas fa-stethoscope font-28 text-dark"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/drugs">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Drugs')" class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Drugs') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0' :endVal='statices.total_drugs ?? 0'
                                                :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md">
                                                <span class="avatar-title bg-soft-success rounded"><i
                                                        class="fas fa-pills font-28 text-success"></i>
                                                </span>
                                            </div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/prescriptions">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Prescriptions')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Prescriptions') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.total_prescriptions ?? 0' :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-info rounded"><i
                                                        class="fas fa-prescription-bottle-alt font-28 text-info"></i></span>
                                            </div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/diagnosis_tests">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Diagnosis Tests')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Diagnosis Tests') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.total_diagnosis_tests ?? 0' :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-warning rounded"><i
                                                        class="fas fa-vial font-28 text-warning"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/services_types">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Services Types')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Services Types') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.total_services_types ?? 0' :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-success rounded"><i
                                                        class="fas fa-medkit font-28 text-success"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/insurance-companies">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Insurance Companies')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Insurance Companies') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.total_insurances_companies ?? 0' :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-dark rounded"><i
                                                        class="fas fa-shield-alt font-28 text-dark"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/branch">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.branches')" class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.branches') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0' :endVal='statices.total_branches ?? 0'
                                                :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-danger rounded"><i
                                                        class="fas fa-building font-28 text-danger"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/rooms">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Rooms')" class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Rooms') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0' :endVal='statices.total_rooms ?? 0'
                                                :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-primary rounded"><i
                                                        class="fas fa-bed font-28 text-primary"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/h_m_s/invoices">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Number of Invoices')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Number of Invoices') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.total_number_invocies ?? 0' :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-warning rounded"><i
                                                        class="fas fa-file-invoice font-28 text-warning"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                <div class="w-100">
                                    <h4 :title="$t('general.Total Amount of Invoices')"
                                        class=" font-weight-normal mt-0 text-truncate">
                                        {{ $t('general.Total Amount of Invoices') }}</h4>
                                    <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                        <span class="pt-2">
                                            <countTo :startVal='0' :endVal='statices.total_amount_of_invocies ?? 0'
                                                :duration='3000'></countTo> {{ $t('general.KD') }}
                                        </span>
                                        <div class="avatar-md"><span class="avatar-title bg-soft-danger rounded"><i
                                                    class="fas fa-money-bill-wave font-28 text-danger"></i></span></div>
                                    </h3>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                <div class="w-100">
                                    <h4 :title="$t('general.Total Amount for Patients')"
                                        class=" font-weight-normal mt-0 text-truncate">
                                        {{ $t('general.Total Amount for Patients') }}</h4>
                                    <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                        <span class="pt-2">

                                            <countTo :startVal='0' :endVal='statices.total_amount_for_patients ?? 0'
                                                :duration='3000'></countTo> {{ $t('general.KD') }}
                                        </span>
                                        <div class="avatar-md"><span class="avatar-title bg-soft-dark rounded"><i
                                                    class="fas fa-money-bill font-28 text-dark"></i></span></div>
                                    </h3>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                <div class="w-100">
                                    <h4 :title="$t('general.Paid Amount for Patients')"
                                        class=" font-weight-normal mt-0 text-truncate">
                                        {{ $t('general.Paid Amount for Patients') }}</h4>
                                    <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                        <span class="pt-2">

                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.patients_paid_amount ?? 0' :duration='3000'>
                                            </countTo> {{ $t('general.KD') }}
                                        </span>
                                        <div class="avatar-md"><span class="avatar-title bg-soft-primary rounded"><i
                                                    class="fas fa-money-bill-wave  font-28 text-primary"></i></span></div>
                                    </h3>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between p-3 bg-white"
                                style="border-radius: 4px;cursor:pointer"
                                @click.prevent="$bvModal.show(`h_m_s_total_and_paid_amount_for_each_doctors`);">
                                <div class="w-100">
                                    <h4 :title="$t('general.Total Amount for Doctors')"
                                        class=" font-weight-normal mt-0 text-truncate">
                                        {{ $t('general.Total Amount for Doctors') }}</h4>
                                    <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                        <span class="pt-2">

                                            <countTo :startVal='0' :endVal='statices.total_amount_for_doctors ?? 0'
                                                :duration='3000'></countTo> {{ $t('general.KD') }}
                                        </span>
                                        <div class="avatar-md">
                                            <span class="avatar-title bg-soft-success rounded"><i
                                                    class="fas fa-money-bill font-28 text-success"></i></span>
                                        </div>
                                    </h3>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between p-3 bg-white"
                                style="border-radius: 4px;cursor:pointer"
                                @click.prevent="$bvModal.show(`h_m_s_total_and_paid_amount_for_each_doctors`);">

                                <div class="w-100">
                                    <h4 :title="$t('general.Paid Amount for Doctors')"
                                        class=" font-weight-normal mt-0 text-truncate">
                                        {{ $t('general.Paid Amount for Doctors') }}</h4>
                                    <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                        <span class="pt-2">

                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.doctors_paid_amount ?? 0' :duration='3000'>
                                            </countTo> {{ $t('general.KD') }}
                                        </span>
                                        <div class="avatar-md"><span class="avatar-title bg-soft-warning rounded"><i
                                                    class="fas fa-money-bill-wave  font-28 text-warning"></i></span></div>
                                    </h3>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between p-3 bg-white"
                                style="border-radius: 4px;cursor:pointer"
                                @click.prevent="$bvModal.show(`h_m_s_total_and_paid_amount_for_each_insurance_company`);">
                                <div class="w-100">
                                    <h4 :title="$t('general.Total Amount for Insurance Companies')"
                                        class=" font-weight-normal mt-0 text-truncate">
                                        {{ $t('general.Total Amount for Insurance Companies') }}</h4>
                                    <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                        <span class="pt-2">

                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.total_amount_for_insurance_companies ?? 0'
                                                :duration='3000'>
                                            </countTo> {{ $t('general.KD') }}
                                        </span>
                                        <div class="avatar-md"><span class="avatar-title bg-soft-info rounded"><i
                                                    class="fas fa-money-bill-wave font-28 text-info"></i></span></div>
                                    </h3>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between p-3 bg-white"
                                style="border-radius: 4px;cursor:pointer"
                                @click.prevent="$bvModal.show(`h_m_s_total_and_paid_amount_for_each_insurance_company`);">
                                <div class="w-100">
                                    <h4 :title="$t('general.Paid Amount for Insurance Companies')"
                                        class=" font-weight-normal mt-0 text-truncate">
                                        {{ $t('general.Paid Amount for Insurance Companies') }}</h4>
                                    <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                        <span class="pt-2">

                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.insurance_companies_paid_amount ?? 0' :duration='3000'>
                                            </countTo> {{ $t('general.KD') }}
                                        </span>
                                        <div class="avatar-md"><span class="avatar-title bg-soft-danger rounded"><i
                                                    class="fas fa-money-bill font-28 text-danger"></i></span></div>
                                    </h3>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>







            </div>



            <div class="row mt-2">

                <div class="col-xl-4">
                    <h_m_s_patients_blood_typeVue :patient_group_by_blood_type="statices.patient_group_by_blood_type" />
                </div>

                <div class="col-xl-4 ">
                    <h_m_s_patients_ageVue :patient_group_by_age="statices.patient_group_by_age" />
                </div>

                <div class="col-xl-4 ">
                    <h_m_s_appointments_timesVue :peak_appointment_periods="statices.peak_appointment_periods" />
                </div>

                <div class="col-xl-4 ">
                    <h_m_s_appointments_for_current_and_last_monthVue
                        :appointments_for_current_month="statices.appointments_for_current_month"
                        :appointments_for_last_month="statices.appointments_for_last_month" />
                </div>

                <div class="col-xl-8 ">
                    <h_m_s_total_amount_for_each_month_in_yearVue
                        :total_amount_for_each_month_in_year="statices.total_amount_for_each_month_in_year" />
                </div>

            </div>

        </div>
        <h_m_s_total_and_paid_amount_for_each_doctorsVue
            :total_and_paid_amount_for_each_doctors="statices.total_and_paid_amount_for_each_doctors" />
        <h_m_s_total_and_paid_amount_for_each_company_insuranceVue
            :total_and_paid_amount_for_each_insurance_company="statices.total_and_paid_amount_for_each_insurance_company" />

        <!-- end row -->
</div></template>

<style scope>.content-page {
    padding: 70px 15px 5px !important;
}

.card {
    margin-bottom: 24px;
    box-shadow: 0 0.75rem 6rem rgba(56, 65, 74, .03);
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid #e7eaed;
    border-radius: 0.25rem;
}

.apexcharts-legend-text,tspan {
    font-weight: bold;
    font-size: 14px;
    color:#000
}

.apexcharts-tooltip.apexcharts-theme-light.apexcharts-active {
    z-index: 99999999999 !important
}</style>
