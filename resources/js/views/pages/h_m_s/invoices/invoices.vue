<template>
    <Layout>
        <PageHeader />
        <DocumentWithItem :document_id="45" :patient_details_id="''" :doctors="doctors" :services="services" :companies_insurances="companies_insurances" :paymentMethods="paymentMethods"/>
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
    </Layout>
</template>

<script>
import Layout from "../../../layouts/main.vue";
import PageHeader from "../../../../components/general/Page-header.vue";
import DocumentWithItem from "../../../../components/document/h_m_s/documentWithItem.vue";
import Swal from "sweetalert2";
import permissionGuard from "../../../../helper/permission";
import adminApi from '../../../../api/adminAxios';
import ModalService from "../services_types/modal.vue";
import translation from "../../../../helper/mixin/translation-mixin";
import customTable from "../../../../helper/mixin/customTable";
import InsuranceCompany from "../../../../components/create/general/insuranceCompany.vue";

export default {
    data(){
        return {
            doctors: [],
            services: [],
            companies_insurances: [],
            paymentMethods: [],
        }
    },
    mixins:[translation,customTable],
    page: {
        title: "checkIn",
        meta: [{ name: "HMS Invoices", content: "HMS Invoices" }],
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "HMS Invoices", "all HMS Invoice");
        });
    },
    components: {
        Layout,
        InsuranceCompany,
        ModalService,
        PageHeader,
        DocumentWithItem
    },
    mounted(){
        this.get_doctors();
        this.getServices();
        this.getPaymentMethod();
        this.get_insurance_companies();
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
        get_doctors() {
            adminApi.get(`h_m_s/doctors?drop_down=1&company_id=${this.company_id}`).then((res) => {
                this.doctors = res.data
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
    }
}
</script>

<style scoped></style>
