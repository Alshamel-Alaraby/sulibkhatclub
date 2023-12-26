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
import Swal from "sweetalert2";
import ModalDrug from "../drugs/modal.vue";
import ModalDiagnosisTest from "../diagnosis_tests/modal.vue";
import ModalDoctor from "../doctors/modal.vue";
import ModalPatient from "../patients/modal.vue";
import ModalBranch from "../../../../components/create/general/branch.vue"
import ModalService from "../services_types/modal.vue";
import InsuranceCompany from "../../../../components/create/general/insuranceCompany.vue";

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
        Layout, PageHeader, loader, searchPage, appointmentTable, patient_details,InsuranceCompany, ModalDrug, ModalDiagnosisTest, ModalDoctor, ModalPatient,ModalService, ModalBranch,
        actionSetting, tableCustom, Modal
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "All Appointments", "all Appointment");
        });
    },
    data() {
        return {
            url: '/h_m_s/appointments',
            debounce: {},
            objPagination: {},
            page_title: {},
            per_page: 50,
            doctor_can_show_next_button: 0,
            current_page: 1,
            _interval: {},
            time_out: [],
            patient_id: null,
            search: '',
            branches: [],
            doctors: [],
            patients: [],
            drugs: [],
            diagnosis_tests: [],
            next_patient_for_each_doctor: [],
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
        this.get_drugs();
        this.get_insurance_companies();
        this.getPaymentMethod();
        this.getServices();
        this.get_diagnosis_tests();
        this.get_next_patient_for_each_doctor();
        this.page_title = page_title.value
        this.getCustomTableFields('h_m_s_appointments')
    },
    beforeDestroy() {
        this.destroyInterVal()
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
        next_patient() {
            this.isLoader = true;

            Swal.fire({
                title: `${this.$t("general.Areyousure")}`,
                text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                type: "warning",
                showCancelButton: true,
                confirmButtonText: `${this.$t("general.YesChangeStatus")}`,
                cancelButtonText: `${this.$t("general.Nocancel")}`,
                confirmButtonClass: "btn btn-success mt-2",
                cancelButtonClass: "btn btn-danger ml-2 mt-2",
                buttonsStyling: false,
            }).then((result) => {
                if (result.value) {
                    this.isLoader = true;

                    adminApi
                        .post(`h_m_s/next_patient`)
                        .then((res) => {
                            this.get_appointments();
                            this.get_next_patient_for_each_doctor();
                        })
                        .catch((err) => {
                            console.log(err)
                            this.errorFun('Error', 'Thereisanerrorinthesystem');
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }
            })

        },
        async get_appointments(page = 1) {
            this.isLoader = true
            await adminApi.get(`${this.url}?search=${this.search}&page=${page}&per_page=${this.per_page}&company_id=${this.company_id}
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
        get_next_patient_for_each_doctor() {
            adminApi.get(`h_m_s/get_next_patient_for_each_doctor?company_id=${this.company_id}`).then((res) => {
                this.doctor_can_show_next_button = res.data.data.doctor_can_show_next_button
                this.destroyInterVal()
                if (res.data.data.appointments.length > 0) {
                    let interval = 1000 * 15 * res.data.data.appointments.length;
                    let start = 0;
                    let increment = interval / res.data.data.appointments.length;

                    res.data.data.appointments.forEach((element) => {
                        this.time_out.push(setTimeout(() => {
                            this.toast(element, increment)
                        }, start));

                        start += increment;
                    });

                    this._interval = setInterval(() => {
                        start = 0;

                        res.data.data.appointments.forEach((element) => {
                            this.time_out.push(setTimeout(() => {
                                this.toast(element, increment)
                            }, start));

                            start += increment;
                        });
                    }, interval);
                }
            });
        },
        get_doctors() {
            adminApi.get(`h_m_s/doctors?drop_down=1&company_id=${this.company_id}`).then((res) => {
                let data = res.data
                if (this.isPermission("create Doctor")) {
                    data.unshift({ id: 0, name: "اضف طبيب", name_e: "Add Doctor" });
                }
                this.doctors = data
            });
        },
        get_patients() {
            adminApi.get(`h_m_s/patients?drop_down=1`).then((res) => {
                let data = res.data
                if (this.isPermission("create Patient")) {
                    data.unshift({ id: 0, name: "اضف مريض", name_e: "Add Patient" });
                }
                this.patients = data
            });
        },
        getBranch() {
            adminApi.get(`/branches/get-drop-down?company_id=${this.company_id}&notParent=1`)
                .then((res) => {
                    let data = res.data.data
                    if (this.isPermission("create Branch")) {
                        data.unshift({ id: 0, name: "اضف فرع", name_e: "Add Branch" });
                    }
                    this.branches = data
                })
                .catch((err) => {
                    console.log(err)
                })
        },
        get_drugs() {
            adminApi.get(`/h_m_s/drugs?drop_down=1`).then((res) => {
                let data = res.data
                if (this.isPermission("create Drug")) {
                    data.unshift({ id: 0, trade_name: "اضف دواء", trade_name_e: "Add Drug" });
                }
                this.drugs = data
            })
                .catch((err) => {
                    console.log(err)
                })
        },
        get_diagnosis_tests() {
            adminApi.get(`/h_m_s/diagnosis_tests?drop_down=1`).then((res) => {
                let data = res.data
                if (this.isPermission("create DiagnosisTest")) {
                    data.unshift({ id: 0, name: "اضف اختبار تشخيص", name_e: "Add Diagnosis Test" });
                }
                this.diagnosis_tests = data
            })
                .catch((err) => {
                    console.log(err)
                })
        },
        destroyInterVal() {
            clearInterval(this._interval)
            for (var i = 0; i < this.time_out.length; i++) {
                clearTimeout(this.time_out[i]);
            }
        },
        show_patient_details(id) {
            this.patient_id = id
            this.$bvModal.show(`patientDetails`)
        },
        toast(item, time) {
            VanillaToasts.create({
                // notification title
                title: `<h4>${this.$t('general.The next patient for doctor')} ( ${this.$i18n.locale == 'ar' ? item.doctor.name : item.doctor.name_e} )</h4>`,

                // notification message
                text: `<h5 class="mt-2 mb-0">${this.$t('general.Patient')} : <span style="font-size:15px">${this.$i18n.locale == 'ar' ? item.patient.name : item.patient.name_e}</span></h5>
                <div class="d-flex flex-wrap"><h5 class="mt-2 mb-0 col-6 px-0">${this.$t('general.Branch')} : <span style="font-size:15px">${this.$i18n.locale == 'ar' ? item.branch.name : item.branch.name_e}</span></h5>
              <h5 class="mt-2 mb-0 col-6 px-0">${this.$t('general.Room')} : <span style="font-size:15px">${this.$i18n.locale == 'ar' ? item.room.name : item.room.name_e}</span></h5>
              <h5 class="mt-2 mb-0 col-6 px-0">${this.$t('general.Number')} : <span style="font-size:15px">${item.number}</span></h5>
              <h5 class="mt-2 mb-0 col-6 px-0">${this.$t('general.Time')} : <label class="badge badge-dark text-white  p-2 " style="border-radius:2rem;max-width:140px"><i class="fa fa-clock"></i>
                     ${item.final_time.start + " - " + item.final_time.end}</label></h5></div>`,

                // success, info, warning, error   / optional parameter
                type: "warning",

                timeout: time,

                // path to notification icon
                icon: '/images/shamel-logo-006.png',

                // topRight, topLeft, topCenter, bottomRight, bottomLeft, bottomCenter
                positionClass: "topCenter",

                // auto dismiss after 5000ms
            });
        }


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
                                'ar' ? page_title.title : page_title.title_e) : $t('general.All Appointments') }}</h4>

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

                        <div class="row justify-content-between align-items-center mb-2">
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

                            <b-button @click.prevent="next_patient" variant="primary"
                                v-if="$store.state.auth.user.type == 'doctor' && doctor_can_show_next_button"
                                class="font-weight-bold mx-3">
                                <i class="fa fa-arrow-right" v-if="$i18n.locale == 'ar'"></i>

                                {{ $t("general.Next Patient") }}
                                <i class="fa fa-arrow-left" v-if="$i18n.locale == 'en'"></i>
                            </b-button>
                        </div>


                        <!-- end Pagination -->
                        <!-- end setting -->

                        <appointmentTable :appointments="appointments" :doctors="doctors" :patients="patients"
                            :branches="branches" :companies_insurances="companies_insurances"
                            :paymentMethods="paymentMethods" :services="services"
                            @showPatientDetails="id => show_patient_details(id)" :diagnosis_tests="diagnosis_tests"
                            :drugs="drugs" :permissionUpdate="isPermission('update Appointment')"
                            :permissionDelete="isPermission('delete Appointment')"
                            :permissionCreatePrescription="isPermission('create Prescription')"
                            :permissionCreateInvoice="isPermission('create HMS Invoice')"
                            @getAppointments="get_appointments(); get_next_patient_for_each_doctor()" />

                        <!--  create   -->
                        <Modal :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :url="url"
                            :doctors="doctors" :patients="patients" :branches="branches" :isPage="true"
                            :isVisiblePage="isVisible" :isRequiredPage="isRequired" :type="type"
                            @getDataTable="get_appointments(1)" :isPermission="isPermission"
                            v-if="isPermission('create Appointment')" />

                        <patient_details :patient_id="patient_id" />

                        <ModalBranch :tables="[]" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" id="addAppointmentBranch"
                            :isPage="false" type="create" :isPermission="isPermission" @created="getBranch" />

                        <ModalService :tables="[]" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" id="addService"
                            :isPage="false" type="create" :isPermission="isPermission" @created="getServices" />

                        <ModalDoctor :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" id="addDoctorFromAppointment" :isPage="false"
                            type="create" :isPermission="isPermission" @created="get_doctors" />

                        <ModalPatient :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" id="addPatientFromAppointment"
                            :isPage="false" type="create" :isPermission="isPermission" @created="get_patients" />

                        <ModalDrug :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" id="addDrugFromPrescription" :isPage="false"
                            type="create" :isPermission="isPermission" @created="get_drugs" />

                        <ModalDiagnosisTest :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" id="addDiagnosisTestFromPrescription"
                            :isPage="false" type="create" :isPermission="isPermission" @created="get_diagnosis_tests" />

                        <InsuranceCompany :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" id="addCompanyInsurance"
                            :isPage="false" type="create" :isPermission="isPermission" @created="get_insurance_companies" />

                        <!-- end .table-responsive-->
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

