<script>
import Layout from "../../../layouts/main.vue";
import PageHeader from "../../../../components/general/Page-header.vue";
import loader from "../../../../components/general/loader.vue";
import translation from "../../../../helper/mixin/translation-mixin";
import permissionGuard from "../../../../helper/permission";
import customTable from "../../../../helper/mixin/customTable";
import crudHelper from "../../../../helper/mixin/crudHelper";
import searchPage from "../../../../components/general/searchPage";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";
import Modal from "./modal.vue";
import appointmentTable from "./appointment_table.vue";
import page_title from "../../../../helper/PageTitle"
import adminApi from '../../../../api/adminAxios';
import successError from "../../../../helper/mixin/success&error";
import patient_details from "../patients/patient_details.vue";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Appointments",
        meta: [{ name: "description", content: "Appointments" }],
    },
    watch: {
        pageTitle: {
            handler(newV, old) {
                this.page_title = page_title.value;
            },
        },
        search(after, befour) {
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.get_appointments();
            }, 900);
        },
        current_page(after, befour) {
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.get_appointments(after);
            }, 300);
        },
    },
    computed: {
        pageTitle: function () {
            return page_title.value;
        },
    },
    mixins: [translation, customTable, crudHelper, successError],
    components: {
        Layout, PageHeader, loader, searchPage, appointmentTable, patient_details,
        actionSetting, tableCustom, Modal
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Upcoming Appointments", "Upcoming Appointment");
        });
    },
    data() {
        return {
            url: '/h_m_s/appointments',
            debounce: {},
            objPagination: {},
            page_title: {},
            per_page: 50,
            current_page: 1,
            search: '',
            patient_id: null,
            branches: [],
            doctors: [],
            next_patient_for_each_doctor: [],
            patients: [],
            appointments: [],
            services: [],
            paymentMethods: [],
            companies_insurances: [],
        };
    },
    mounted() {
        this.get_appointments();
        this.get_doctors();
        this.get_patients();
        this.getBranch();
        this.get_insurance_companies();
        this.getPaymentMethod();
        this.getServices();
        this.page_title = page_title.value
        this.getCustomTableFields('h_m_s_appointments')
    },
    methods: {
        get_insurance_companies() {
            adminApi.get(`insurance_companies?drop_down=1&company_id=${this.company_id}`).then((res) => {
                this.companies_insurances = res.data
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
                    let l = res.data.data;
                    this.services = l;
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
        async get_appointments(page = 1) {
            this.isLoader = true
            await adminApi.get(`${this.url}?page_name=upcoming_rooms&search=${this.search}&page=${page}&per_page=${this.per_page}&company_id=${this.company_id}
            &columns[0]=doctor.name&columns[1]=doctor.name_e
            &columns[2]=from_doctor.name&columns[3]=from_doctor.name_e
            &columns[4]=room.name&columns[5]=room.name_e
            &columns[6]=patient.name&columns[7]=patient.name_e
            &columns[8]=times.start&columns[9]=times.end
            &columns[10]=date&columns[11]=created_at&columns[12]=number`).then((res) => {
                this.appointments = res.data.data
                this.objPagination = res.data.pagination;
            });
            this.isLoader = false
        },

        get_doctors() {
            adminApi.get(`h_m_s/doctors?drop_down=1&company_id=${this.company_id}`).then((res) => {
                this.doctors = res.data
            });
        },
        get_patients() {
            adminApi.get(`h_m_s/patients?drop_down=1`).then((res) => {
                this.patients = res.data
            });
        },
        show_patient_details(id) {
            this.patient_id = id
            this.$bvModal.show(`patientDetails`)
        },
        getBranch() {
            adminApi
                .get(`/branches?company_id=${this.company_id}&notParent=1&document_id=45`)
                .then((res) => {
                    this.branches = res.data.data;
                })
                .catch((err) => {
                    this.errorFun('Error', 'Thereisanerrorinthesystem');
                })

        },

    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <loader size="large" v-if="isLoader" />

                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ page_title && page_title.url == $route.fullPath ? ($i18n.locale ==
                                'ar' ? page_title.title : page_title.title_e) : $t('general.Upcoming Appointments') }}</h4>

                        </div>


                        <div class="col-lg-4 d-flex my-2" style="font-weight: 500">
                            <div class="d-inline-block">
                                <b-button @click.prevent="$bvModal.show(`create`);" variant="primary"
                                    v-if="isPermission('create Appointment')" class="font-weight-bold">
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                            </div>

                            <div class="d-inline-block position-relative col-9">
                                <span :class="[
                                    'search-custom position-absolute',
                                    $i18n.locale == 'ar' ? 'search-custom-ar' : '',
                                ]">
                                    <i class="fe-search"></i>
                                </span>
                                <input class="form-control" style="display: block; width: 93%; padding-top: 3px" type="text"
                                    v-model.trim="search" :placeholder="`${$t('general.Search')}...`" />


                            </div>


                        </div>
                        <!-- start Pagination -->
                        <div class="d-inline-flex align-items-center pagination-custom mb-2">
                            <div class="d-inline-block" style="font-size: 13px">
                                {{ objPagination.from }}-{{ objPagination.to }}
                                /
                                {{ objPagination.total }}
                            </div>
                            <div class="d-inline-block">
                                <a href="javascript:void(0)" :style="{
                                    'pointer-events':
                                        parseInt(objPagination.current_page == 1) ? 'none' : '',
                                }" @click.prevent="current_page = (current_page - 1 <= 0 ? 1 : current_page - 1)">
                                    <span>&lt;</span>
                                </a>
                                <input type="text" v-model="current_page" class="pagination-current-page" />
                                <a href="javascript:void(0)" :style="{
                                    'pointer-events':
                                        objPagination.last_page ==
                                            parseInt(objPagination.current_page)
                                            ? 'none'
                                            : '',
                                }" @click.prevent="current_page = current_page + 1">
                                    <span>&gt;</span>
                                </a>
                            </div>
                        </div>
                        <!-- end Pagination -->
                        <!-- end setting -->

                        <appointmentTable :branches="branches" :appointments="appointments"
                            @getAppointments="get_appointments()" :doctors="[]" :patients="[]" :diagnosis_tests="[]"
                            :drugs="[]" :companies_insurances="companies_insurances" :paymentMethods="paymentMethods"
                            :services="services" :permissionUpdate="isPermission('update Appointment')"
                            @showPatientDetails="id => show_patient_details(id)"
                            :permissionDelete="isPermission('delete Appointment')"
                            :permissionCreatePrescription="isPermission('create Prescription')"
                            :permissionCreateInvoice="isPermission('create HMS Invoice')" />

                        <!--  create   -->
                        <Modal :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :url="url"
                            v-if="isPermission('create Appointment')" :doctors="doctors" :patients="patients"
                            :branches="branches" :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired"
                            :type="type" @getDataTable="get_appointments(1)" :isPermission="isPermission" />

                        <patient_details :patient_id="patient_id" />

                        <!-- end .table-responsive-->
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

