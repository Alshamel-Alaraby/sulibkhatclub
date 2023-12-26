<script>
import Layout from "../../../layouts/main.vue";
import PageHeader from "../../../../components/general/Page-header.vue";
import loader from "../../../../components/general/loader.vue";
import translation from "../../../../helper/mixin/translation-mixin";
import permissionGuard from "../../../../helper/permission";
import customTable from "../../../../helper/mixin/customTable";
import crudHelper from "../../../../helper/mixin/crudHelper";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";
import page_title from "../../../../helper/PageTitle"
import adminApi from '../../../../api/adminAxios';
import successError from "../../../../helper/mixin/success&error";
import Swal from "sweetalert2";
import Multiselect from "vue-multiselect";


/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Account Statement",
        meta: [{ name: "description", content: "Account Statement" }],
    },
    watch: {
        pageTitle: {
            handler(newV, old) {
                this.page_title = page_title.value;
            },
        },

    },
    computed: {
        pageTitle: function () {
            return page_title.value;
        },
    },
    mixins: [translation, customTable, crudHelper, successError],
    components: {
        Layout, PageHeader, loader, Multiselect,
        actionSetting, tableCustom,
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Account Statement", "Account Statement");
        });
    },
    data() {
        return {
            isLoader: false,
            last_balance: 0,
            total: '0 / 0',
            client: {},
            debounce: {},
            page_title: {},
            document: {},
            relatedDocuments: [],
            account_statement: [],
            customers: [],
            clientTypes: [],
            client_type_id: '',
            customer_id: '',
        };
    },
    mounted() {
        this.page_title = page_title.value
        this.getDocumentData()
    },

    methods: {
        async getDocumentData() {
            this.isLoader = true;
            await adminApi
                .get(`/document/5`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.document = l;
                    this.relatedDocuments = l.document_relateds;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                }).finally(() => {
                    this.getClientType()
                });
        },
        getClientType() {
            this.isLoader = true;
            adminApi
                .get(`/client-types`)
                .then((res) => {
                    let l = res.data.data;
                    let documentTypesIds = [];
                    this.document.clientTypes.forEach((e, index) => {
                        documentTypesIds.push(parseInt(e))
                    });
                    this.clientTypes = l.filter((x) => documentTypesIds.includes(x.id));
                    if (this.clientTypes.length == 1) {
                        this.create.client_type_id = this.clientTypes[0].id;
                        this.getCustomers(this.clientTypes[0].db_table);
                    }
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
        searchCustomer(e) {
            let search = e ?? '';
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                let table = this.clientTypes.find((e) => e.id == this.client_type_id) ? this.clientTypes.find((e) => e.id == this.client_type_id).db_table : '';
                if (table) {
                    this.getCustomers(table, search);
                }
            }, 500);

        },
        getCustomerData(e) {
            let type_id = e ?? '';
            if (type_id) {
                let table = this.clientTypes.find((e) => e.id == type_id).db_table;
                this.getCustomers(table);
            }
        },
        getCustomers(table = null, search = '') {
            this.isLoader = true;
            adminApi
                .get(`/client-types/get-drop-down-model?db_table=${table}&limet=10&search=${search}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.customers = l;

                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },

        getAccountStatement(id, client_type_id) {
            this.customers.forEach((element) => {
                if(element.id == id ){
                    this.client = element
                }
            })
            this.isLoader = true;
            adminApi
                .get(`/account-statement?client_type_id=${client_type_id}&customer_id=${id}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.account_statement = l;
                    let total_debit = 0
                    let total_credit_plus_paid_amount = 0
                    l.forEach(element => {
                        this.last_balance = element.balance
                        total_debit += element.debit && element.debit != '0' && element.debit > 0 ? parseInt(element.debit) : 0
                        total_credit_plus_paid_amount += element.credit && element.credit != '0' && element.credit > 0 ? parseInt(element.credit) : (element.paid_amount && element.paid_amount != '0' && element.paid_amount > 0 ? parseInt(element.paid_amount) : 0)
                    });

                    this.total = total_debit + " / " + total_credit_plus_paid_amount

                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
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
                                'ar' ? page_title.title : page_title.title_e) : $t('general.Account Statement') }}</h4>

                        </div>


                        <div class="col-lg-6 d-flex my-2" style="font-weight: 500">
                            <div class="col-md-2"  v-if="account_statement.length > 0">
                                <button class="custom-btn-dowonload h-100 pt-3"  v-print="'#printData'">
                                    <i class="fe-printer"></i>
                                </button>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey('money_voucher_client_type') }}
                                        <span class="text-danger">*</span>
                                    </label>

                                    <multiselect :show-labels="false" @input="getCustomerData" v-model="client_type_id"
                                        :options="clientTypes.map((type) => type.id)"
                                        :custom-label="(opt) => clientTypes.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? clientTypes.find((x) => x.id == opt).name : clientTypes.find((x) => x.id == opt).name_e : ''">
                                    </multiselect>

                                </div>
                            </div>
                            <div class="col-md-5" v-if="client_type_id">
                                <div class="form-group">
                                    <label class="control-label">{{ $t('general.client') }} <span
                                            class="text-danger">*</span></label>
                                    <multiselect :internalSearch="false" @search-change="searchCustomer"
                                        @input="getAccountStatement(customer_id, client_type_id)" v-model="customer_id"
                                        :options="customers.map((type) => type.id)"
                                        :custom-label="(opt) => customers.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? customers.find((x) => x.id == opt).name : customers.find((x) => x.id == opt).name_e : ''">
                                    </multiselect>

                                </div>
                            </div>

                        </div>

                        <div id="printData" :style="{ 'direction': $i18n.locale == 'ar' ? 'rtl!important' : '', 'text-align': $i18n.locale == 'ar' ? 'start' : '' }">
                            <h3 class="text-center my-3" v-if="Object.keys(client??[]).length">{{ $t('general.Account Statement For ') + client.name}}</h3>
                            <table class="table table-borderless table-hover table-centered m-0">
                                <thead>
                                    <tr class="text-center">
                                        <th>{{ $t("general.Historyofthebond") }}</th>
                                        <th>{{ $t("general.Bond Type") }}</th>
                                        <th>{{ $t("general.serial") }}</th>
                                        <th>{{ $t("general.Notes") }}</th>
                                        <th>{{ $t("general.debit") }}</th>
                                        <th>{{ $t("general.credit") }}</th>
                                        <th>{{ $t("general.balance") }}</th>

                                    </tr>
                                </thead>

                                <tbody v-if="account_statement.length > 0">
                                    <tr v-for="item in account_statement" :key="item.id">
                                        <td>{{ item.date }}</td>
                                        <td>{{ $i18n.locale == 'ar' ? item.name : item.name_e}}</td>
                                        <td>{{ item.prefix }}</td>
                                        <td>{{ item.notes ?? '-' }}</td>
                                        <td>{{ item.debit && item.debit != '0' && item.debit > 0 ? item.debit : '-' }}</td>
                                        <td>{{ item.credit && item.credit != '0' && item.credit > 0 ? item.credit :
                                            (item.paid_amount && item.paid_amount != '0' ? item.paid_amount : '-') }}</td>
                                        <td>{{ item.balance }}</td>
                                    </tr>

                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <th class="text-center" colspan="30">
                                            {{ $t("general.notDataFound") }}
                                        </th>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-borderless table-hover table-centered m-0"
                                v-if="client_type_id && customer_id">
                                <thead>
                                    <tr class="text-center">
                                        <th>{{ $t("general.total") }}</th>
                                        <th>{{ total }}</th>
                                        <th>{{ $t("general.balance") }}</th>
                                        <th>{{ last_balance }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

