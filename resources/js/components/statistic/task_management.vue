<script>
// import Layout from "../../views/layouts/main";
import Swal from "sweetalert2";
import PageHeader from "../general/Page-header";
import MarketingReports from "../widgets/Marketing-reports";
import adminApi from "../../api/adminAxios";
import department_tasksVue from '../widgets/department_tasksVue.vue';
import locations_tasksVue from '../widgets/locations_tasksVue.vue';
import tasks_for_current_and_last_monthVue from '../widgets/tasks_for_current_and_last_monthVue.vue';
import total_tasks_for_each_month_in_yearVue from '../widgets/total_tasks_for_each_month_in_yearVue.vue';

/**
 * Sales-Dashboard component
 */
export default {
    name: "tasks_dashboard",
    components: {
        // Layout,
        PageHeader,
        MarketingReports,
        department_tasksVue,
        locations_tasksVue,
        tasks_for_current_and_last_monthVue,
        total_tasks_for_each_month_in_yearVue,
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
            tableTitle: "Tasks Statistics",
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

            adminApi.get(`/tasks/dashboard`)
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


                <div class="col-xl-3 col-md-3">
                    <div class="card py-0 mb-0">
                        <div class="card-body">
                            <router-link to="/dashboard/ticket-manager/tasks">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Tasks')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Tasks') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0' :endVal='statices.total_tasks ?? 0'
                                                :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-primary rounded"><i
                                                class="fas fa-tasks font-28 text-primary"></i></span></div>
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
                            <router-link to="/dashboard/ticket-manager/tasks">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Pending Tasks')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Pending Tasks') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.total_pending ?? 0' :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-warning rounded"><i
                                                        class="fas fa-hourglass-start font-28 text-warning"></i></span></div>
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
                            <router-link to="/dashboard/ticket-manager/tasks">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Completed Tasks')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Completed Tasks') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0' :endVal='statices.total_completed ?? 0'
                                                :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-success rounded"><i
                                                        class="fas fa-check-circle font-28 text-success"></i></span></div>
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
                            <router-link to="/dashboard/ticket-manager/tasks">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Rejected Tasks')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Rejected Tasks') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0' :endVal='statices.total_rejected ?? 0'
                                                :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-danger rounded"><i
                                                        class="fas fa-times-circle font-28 text-danger"></i></span></div>
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
                            <router-link to="/dashboard/employee">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Employees')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Employees') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0' :endVal='parseInt(statices.total_employees?? 0) '
                                                :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-dark rounded"><i
                                                        class="fas fa-users font-28 text-dark"></i></span></div>
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
                            <router-link to="/dashboard/department">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Departments')" class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Departments') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0' :endVal='statices.total_departments ?? 0'
                                                :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md">
                                                <span class="avatar-title bg-soft-success rounded"><i
                                                        class="fas fa-sitemap font-28 text-success"></i>
                                                </span>
                                            </div>
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
                            <router-link to="/dashboard/location">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Locations')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Locations') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='statices.total_locations ?? 0' :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-info rounded"><i
                                                        class="fas fa-map-marker-alt font-28 text-info"></i></span>
                                            </div>
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
                            <router-link to="/dashboard/equipment">
                                <div class="d-flex justify-content-between p-3 bg-white" style="border-radius: 4px;">
                                    <div class="w-100">
                                        <h4 :title="$t('general.Equipments')"
                                            class=" font-weight-normal mt-0 text-truncate">
                                            {{ $t('general.Equipments') }}</h4>
                                        <h3 class="my-2 py-1 d-flex flex-wrap justify-content-between">
                                            <countTo :class="'pt-2'" :startVal='0'
                                                :endVal='parseInt(statices.total_equipments ?? 0)' :duration='3000'>
                                            </countTo>
                                            <div class="avatar-md"><span class="avatar-title bg-soft-warning rounded"><i
                                                        class="fas fa-wrench font-28 text-warning"></i></span></div>
                                        </h3>
                                    </div>

                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>



            </div>



            <div class="row mt-2">

                <div class="col-xl-6">
                    <department_tasksVue :departments_tasks="statices.departments_tasks" />
                </div>

                <div class="col-xl-6 ">
                    <locations_tasksVue :locations_tasks="statices.locations_tasks" />
                </div>

                <div class="col-xl-6 ">
                    <tasks_for_current_and_last_monthVue
                        :tasks_for_current_month="statices.tasks_for_current_month"
                        :tasks_for_last_month="statices.tasks_for_last_month" />
                </div>

                <div class="col-xl-6 ">
                    <total_tasks_for_each_month_in_yearVue
                        :total_tasks_for_each_month_in_year="statices.total_tasks_for_each_month_in_year" />
                </div>

            </div>

        </div>



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
