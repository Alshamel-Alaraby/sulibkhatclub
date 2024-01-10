<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import adminApi from "../../../../api/adminAxios";
import Switches from "vue-switches";
import {required} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../../components/widgets/errorMessage";
import loader from "../../../../components/general/loader";
import {dynamicSortString} from "../../../../helper/tableSort";
import {formatDateOnly} from "../../../../helper/startDate";
import translation from "../../../../helper/mixin/translation-mixin";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Detailed Payments Report",
        meta: [{name: "Detailed Payments Report", content: 'Detailed Payments Report'}],
    },
    mixins: [translation],
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
    },
    // beforeRouteEnter(to, from, next) {
    //     next((vm) => {
    //         return permissionGuard(vm, "Detailed Payments Report ", "all DetailedPaymentReport RP");
    //     });
    //
    // },
    data() {
        return {
            per_page: 50,
            search: '',
            debounce: {},
            installmentStatusPagination: {},
            installmentStatus: [],
            customers: [],
            payment_types: [],
            isLoader: false,
            branches: [],
            clients: [],
            displayData: [],
            create: {
                financial_year: '',
                client_type_id: null,
            },
            errors: {},
            is_disabled: false,
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            enabled3: true,
            printLoading: true,
            printObj: {
                id: "printCustom",
            },
            openingBreak: '',
            setting: {
                client_account: true,
                instalment_date: true,
                dues: true,
                paid: true,
                remaining: true,
            },
            filterSetting: [
                'client_account',
                'instalment_date',
                'dues',
                'paid',
                'remaining',
            ],
            Tooltip: '',
            mouseEnter: null,
            columns: [
                'PastYears.Dues',
                'PastYears.Paid',
                'PastYears.Remaining',
                'CurrentYear.Dues',
                'CurrentYear.Paid',
                'CurrentYear.Remaining',
                'ExistingYears.Dues',
                'ExistingYears.Paid',
                'ExistingYears.Remaining',
                'FutureYears.Dues',
                'FutureYears.Paid',
                'FutureYears.Remaining',
            ],
        }
    },
    validations: {
        create: {
            financial_year: {required},
            client_type_id: {required},
        },
    },
    computed: {
        totalNetAmount() {
            return this.installmentStatus.reduce((total, installment) => {
                return total + installment.net_amount;
            }, 0)
        },
    },
    watch: {
        /**
         * watch per_page
         */
        per_page(after, befour) {
            this.getData();
        },
        /**
         * watch search
         */
        search(after, befour) {
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.getData();
            }, 400);
        },
        /**
         * watch check All table
         */
        isCheckAll(after, befour) {
            if (after) {
                this.installmentStatus.forEach((el) => {
                    if (!this.checkAll.includes(el.id)) {
                        this.checkAll.push(el.id);
                    }
                });
            } else {
                this.checkAll = [];
            }
        },
        installmentsStatus: {
            handler(newVal) {
                // Calculate netAmount for each object in installmentStatus
                this.calculateNetAmount(newVal);
            },
            deep: true, // Watches for changes inside the array elements
        },
    },
    methods: {
        /**
         *  start get Data module && pagination
         */
        sumColumn(columnIndex) {
            return this.displayData.reduce((total, row) => {
                const value = this.getValueFromColumn(row, this.columns[columnIndex]);
                console.log("columns", columnIndex);
                return total + (value || 0);

            }, 0);
        },
        getValueFromColumn(row, columnName) {
            const columns = columnName.split('.');
            let value = row;
            for (let i = 0; i < columns.length; i++) {
                if (value !== null && value !== undefined) {
                    value = value[columns[i]];
                } else {
                    value = null;
                    break;
                }
            }
            console.log("columnName", columnName);
            console.log("row", row);
            console.log('value', value)
            return value;
        },
        calculateColumnSum(columnIndex) {
            let sum = 0;
            this.displayData.forEach((item) => {
                const columnKey = Object.keys(item)[columnIndex];
                const columnValue = item[columnKey];
                if (!isNaN(columnValue)) {
                    sum += parseFloat(columnValue);
                }
            });
            return sum;
        },
        processData() {
            const transformedData = [];

            this.installmentStatus.forEach((item) => {
                const newData = {
                    name: item.name,
                    name_e: item.name_e,
                    customer_id: item.customer_id,
                    instalment_date: item.instalment_date,
                    PastYears: null,
                    CurrentYear: null,
                    ExistingYears: null,
                };

                if (item.time_frame === 'previous') {
                    newData.PastYears = {
                        Dues: item.debit,
                        Paid: item.total_amount,
                        Remaining: item.remaining_amount,
                    };
                } else if (item.time_frame === 'current') {
                    newData.CurrentYear = {
                        Dues: item.debit,
                        Paid: item.total_amount,
                        Remaining: item.remaining_amount,
                    };
                } else if (item.time_frame === 'next') {
                    newData.ExistingYears = {
                        Dues: item.debit,
                        Paid: item.total_amount,
                        Remaining: item.remaining_amount,
                    };
                } else if (item.time_frame === 'future') {
                    newData.FutureYears = {
                        Dues: item.debit,
                        Paid: item.total_amount,
                        Remaining: item.remaining_amount,
                    };
                }

                transformedData.push(newData);
            });

            this.displayData = transformedData;
        },
        getData() {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                adminApi.get(`/recievable-payable/filter-follow-settlements?year=${this.create.financial_year}&client_type_id=${this.create.client_type_id}`)
                    .then((res) => {
                        this.installmentStatus = res.data;
                        this.processData();
                        console.log("dataNewW", this.displayData)
                        // this.installmentStatusPagination = l.pagination;
                        // this.current_page = l.pagination.current_page;
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
            }
        },
        // getDataCurrentPage(page = 1) {
        //     this.$v.create.$touch();
        //     if (this.$v.create.$invalid) {
        //         return;
        //     } else {
        //         if (this.current_page <= this.installmentStatusPagination.last_page && this.current_page != this.installmentStatusPagination.current_page && this.current_page) {
        //             this.isLoader = true;
        //             let _filterSetting = [...this.filterSetting];
        //             let index = this.filterSetting.indexOf("customer_id");
        //             if (index > -1) {
        //                 _filterSetting[index] =
        //                     this.$i18n.locale == "ar" ? "customer.name" : "customer.name_e";
        //             }
        //             index = this.filterSetting.indexOf("currency_id");
        //             if (index > -1) {
        //                 _filterSetting[index] =
        //                     this.$i18n.locale == "ar" ? "currency.name" : "currency.name_e";
        //             }
        //             index = this.filterSetting.indexOf("instalment_type_id");
        //             if (index > -1) {
        //                 _filterSetting[index] =
        //                     this.$i18n.locale == "ar" ? "installment_payment_type.name" : "installment_payment_type.name_e";
        //             }
        //             let filter = "";
        //             for (let i = 0; i < _filterSetting.length; ++i) {
        //                 filter += `columns[${i}]=${_filterSetting[i]}&`;
        //             }
        //
        //             adminApi.get(`/recievable-payable/filterBreak?start_date=${this.create.start_date}&end_date=${this.create.end_date}&customer_id=${this.create.customer_id ?? ''}&instalment_type_id=${this.create.instalment_type_id ?? ''}&amount_status=${this.create.amount_status ?? ''}&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
        //                 .then((res) => {
        //                     let l = res.data;
        //                     this.installmentStatus = l.data;
        //                     this.installmentStatusPagination = l.pagination;
        //                     this.current_page = l.pagination.current_page;
        //                 })
        //                 .catch((err) => {
        //                     Swal.fire({
        //                         icon: 'error',
        //                         title: `${this.$t('general.Error')}`,
        //                         text: `${this.$t('general.Thereisanerrorinthesystem')}`,
        //                     });
        //                 })
        //                 .finally(() => {
        //                     this.isLoader = false;
        //                 });
        //         }
        //     }
        // },
        /**
         *  end get Data module && pagination
         */
        /**
         *  get customer
         */
        // async getCustomers(opening_balance = 0) {
        //     this.isLoader = true;
        //     await adminApi
        //         .get(`/general-customer?opening_balance=${opening_balance}`)
        //         .then((res) => {
        //             let l = res.data.data;
        //             this.customers = l;
        //             this.isLoader = false;
        //         })
        //         .catch((err) => {
        //             Swal.fire({
        //                 icon: "error",
        //                 title: `${this.$t("general.Error")}`,
        //                 text: `${this.$t("general.Thereisanerrorinthesystem")}`,
        //             });
        //         });
        // },
        getBranches() {
            adminApi
                .get(`/branches`)
                .then((res) => {
                    this.branches = res.data.data;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })

        },
        getClientType() {
            this.isLoader = true;
            adminApi
                .get(`/client-types`)
                .then((res) => {
                    this.clients = res.data.data;

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
        /**
         *  get payment type
         */
        // async getInstallPaymentTypes() {
        //     this.isLoader = true;
        //     await adminApi
        //         .get(`/recievable-payable/rp_installment_payment_types`)
        //         .then((res) => {
        //             let l = res.data.data;
        //             this.payment_types = l;
        //         })
        //         .catch((err) => {
        //             Swal.fire({
        //                 icon: "error",
        //                 title: `${this.$t("general.Error")}`,
        //                 text: `${this.$t("general.Thereisanerrorinthesystem")}`,
        //             });
        //         })
        //         .finally(() => {
        //             this.isLoader = false;
        //         });
        // },
        /**
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
            this.$bvModal.hide(`create`);
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            // await this.getCustomers(1);
            // await this.getInstallPaymentTypes();
            await this.getClientType();
            await this.getBranches();
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
        },

        /**
         *  start  dynamicSortString
         */
        sortString(value) {
            return dynamicSortString(value);
        },
        moveInput(tag, c, index) {
            document.querySelector(`${tag}[data-${c}='${index}']`).focus()
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
                if (dl) {
                    XLSX.write(wb, {bookType: type, bookSST: true, type: 'base64'});
                } else {
                    XLSX.writeFile(wb, fn || (('Payment Report' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },

        dateStatus(date, status) {
            if (status == 'Unpaid') {
                let toDay = this.formatDate(new Date());
                let dateRow = this.formatDate(date);
                if (toDay >= dateRow) {
                    return 'due';
                } else if (toDay < dateRow) {
                    return 'NotDue';
                } else {
                    return 'completedPayment'
                }
            } else {
                return 'completedPayment'
            }
        }

    },
};
</script>

<template>
    <Layout>
        <PageHeader/>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title"> {{ $t('general.PaymentsReportTable') }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">

                                <div class="d-inline-block" style="width: 22.2%;">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <!--                                        <b-form-checkbox v-model="filterSetting"-->
                                        <!--                                                         :value="$i18n.locale == 'ar' ? 'customer.name' : 'customer.name_e'"-->
                                        <!--                                                         class="mb-1">{{ getCompanyKey("customer") }}-->
                                        <!--                                        </b-form-checkbox>-->

                                        <b-form-checkbox v-model="filterSetting" value="client_account" class="mb-1">
                                            {{ $t('general.ClientAccount') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="Before" class="mb-1">
                                            {{ $t('general.PastYears') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="During" class="mb-1">
                                            {{ $t('general.CurrentYear') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="After" class="mb-1">
                                            {{ $t('general.ExistingYears') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="paid_before" class="mb-1">
                                            {{ $t('general.PastYears') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="paid_during" class="mb-1">
                                            {{ $t('general.CurrentYear') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="paid_after" class="mb-1">
                                            {{ $t('general.ExistingYears') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="net_amount" class="mb-1">
                                            {{ $t('general.NetAmount') }}
                                        </b-form-checkbox>
                                    </b-dropdown>
                                    <!-- Basic dropdown -->
                                </div>

                                <div class="d-inline-block position-relative" style="width: 77%;">
                                    <span
                                        :class="['search-custom position-absolute',$i18n.locale == 'ar'?'search-custom-ar':'']"
                                    >
                                        <i class="fe-search"></i>
                                    </span>
                                    <input
                                        class="form-control"
                                        style="display:block;width:93%;padding-top: 3px;"
                                        type="text"
                                        v-model.trim="search"
                                        :placeholder="`${$t('general.Search')}...`"
                                    >
                                </div>
                            </div>
                        </div>
                        <!-- end search -->

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <!-- start create and printer -->
                                <div class="d-inline-flex">
                                    <button class="custom-btn-dowonload" @click="ExportExcel('xlsx')">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button class="custom-btn-dowonload" v-print="'#printCustom'">
                                        <i class="fe-printer"></i>
                                    </button>

                                </div>
                                <!-- end create and printer -->
                            </div>
                            <div
                                class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center  justify-content-end"
                            >
                                <div class="d-fex">
                                    <!-- start filter and setting -->
                                    <div class="d-inline-block">
                                        <b-button
                                            v-b-modal.create
                                            class="mx-1 custom-btn-background"
                                        >
                                            {{ $t('general.filter') }}
                                            <i class="fas fa-filter"></i>
                                        </b-button>
                                        <b-button
                                            class="mx-1 custom-btn-background"
                                        >
                                            {{ $t('general.group') }}
                                            <i class="fe-menu"></i>
                                        </b-button>
                                        <!-- Basic dropdown -->
                                        <b-dropdown variant="primary"
                                                    :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                                                    ref="dropdown" class="dropdown-custom-ali">
                                            <!--                                            <b-form-checkbox v-model="setting.customer_id" class="mb-1">-->
                                            <!--                                                {{ getCompanyKey("customer") }}-->
                                            <!--                                            </b-form-checkbox>-->
                                            <b-form-checkbox v-model="setting.client_account" class="mb-1">
                                                {{ $t('general.ClientAccount') }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.Before" class="mb-1">
                                                {{ $t('general.PastYears') }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.During" class="mb-1">
                                                {{ $t('general.CurrentYear') }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.After" class="mb-1">
                                                {{ $t('general.ExistingYears') }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.paid_before" class="mb-1">
                                                {{ $t('general.PastYears') }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.paid_during" class="mb-1">
                                                {{ $t('general.CurrentYear') }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.paid_after" class="mb-1">
                                                {{ $t('general.ExistingYears') }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.net_amount" class="mb-1">
                                                {{ $t('general.NetAmount') }}
                                            </b-form-checkbox>

                                            <div class="d-flex justify-content-end">
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm">Apply</a>
                                            </div>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </div>
                                    <!-- end filter and setting -->

                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom">
                                        <div class="d-inline-block" style="font-size:13px;">
                                            {{ installmentStatusPagination.from }}-{{ installmentStatusPagination.to }}
                                            /
                                            {{ installmentStatusPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{'pointer-events':installmentStatusPagination.current_page == 1 ? 'none': ''}"
                                                @click.prevent="getData(installmentStatusPagination.current_page - 1)"
                                            >
                                                <span>&lt;</span>
                                            </a>
                                            <input
                                                type="text"
                                                @keyup.enter="getDataCurrentPage()"
                                                v-model="current_page"
                                                class="pagination-current-page"
                                            />
                                            <a
                                                href="javascript:void(0)"
                                                :style="{'pointer-events':installmentStatusPagination.last_page == installmentStatusPagination.current_page ? 'none': ''}"
                                                @click.prevent="getData(installmentStatusPagination.current_page + 1)"
                                            >
                                                <span>&gt;</span>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>

                        <!--  create   -->
                        <b-modal
                            id="create"
                            :title="$t('general.Search')"
                            title-class="font-18"
                            body-class="p-4"
                            size="lg"
                            :hide-footer="true"
                            @show="resetModal"
                            @hidden="resetModalHidden"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <template v-if="!is_disabled">
                                        <b-button
                                            variant="success"
                                            type="button" class="mx-1"
                                            v-if="!isLoader"
                                            @click.prevent="getData"
                                        >
                                            {{ $t('general.Search') }}
                                        </b-button>

                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                        </b-button>
                                    </template>
                                    <!-- Emulate built in modal footer ok and cancel button actions -->

                                    <b-button variant="danger" type="button" @click.prevent="resetModalHidden">
                                        {{ $t('general.Cancel') }}
                                    </b-button>
                                </div>
                                <div class="row">
                                    <!--                                    <div class="col-md-6">-->
                                    <!--                                        <div class="form-group">-->
                                    <!--                                            <label class="control-label">-->
                                    <!--                                                {{ $t('general.branch') }}-->
                                    <!--                                                <span class="text-danger">*</span>-->
                                    <!--                                            </label>-->
                                    <!--                                            <multiselect-->
                                    <!--                                                v-model="create.branch_id"-->
                                    <!--                                                :options="branches.map((type) => type.id)"-->
                                    <!--                                                :custom-label="(opt) => $i18n.locale == 'ar' ?branches.find((x) => x.id == opt).name:branches.find((x) => x.id == opt).name_e"-->
                                    <!--                                                :class="{-->
                                    <!--                                                        'is-invalid': errors.branch_id,-->
                                    <!--                                                        'is-valid': !errors.branch_id,-->
                                    <!--                                                      }"-->
                                    <!--                                            >-->
                                    <!--                                            </multiselect>-->

                                    <!--                                            <template v-if="errors.branch_id">-->
                                    <!--                                                <ErrorMessage-->
                                    <!--                                                    v-for="(errorMessage, index) in errors.branch_id"-->
                                    <!--                                                    :key="index"-->
                                    <!--                                                >{{ errorMessage }}-->
                                    <!--                                                </ErrorMessage-->
                                    <!--                                                >-->
                                    <!--                                            </template>-->
                                    <!--                                        </div>-->
                                    <!--                                    </div>-->
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label
                                                class="control-label">{{ $t('general.ClientType') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                v-model="create.client_type_id"
                                                :options="clients.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ?clients.find((x) => x.id == opt).name:clients.find((x) => x.id == opt).name_e"
                                                :class="{
                                                        'is-invalid': errors.client_type_id,
                                                        'is-valid': !errors.client_type_id,
                                                      }"
                                            >
                                            </multiselect>

                                            <template v-if="errors.client_type_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.client_type_id"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.Year') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="year"
                                                v-model="$v.create.financial_year.$model"
                                                format="YYYY"
                                                valueType="format"
                                                :confirm="false"
                                                :class="{ 'is-invalid':
                                                    $v.create.financial_year.$error ||
                                                    errors.financial_year,
                                                'is-valid':
                                                    !$v.create.financial_year
                                                        .$invalid &&
                                                    !errors.financial_year,
                                             }"
                                            ></date-picker>

                                            <template v-if="errors.financial_year">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.financial_year"
                                                              :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </b-modal>
                        <!--  /create   -->
                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table"
                             id="printCustom">

                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader"/>
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0">
                                <thead>
                                <tr>
                                    <th v-if="setting.client_account" rowspan="2">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.ClientName') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="
                                                        installmentStatus.sort(
                                                            sortString($i18n.locale == 'ar' ? 'name' : 'name_e')
                                                        )
                                                    "></i>
                                                <i class="fas fa-arrow-down" @click="
                                                        installmentStatus.sort(
                                                            sortString($i18n.locale == 'ar' ? '-name' : '-name_e')
                                                        )
                                                    "></i>
                                            </div>
                                        </div>
                                    </th>

                                    <th rowspan="2">
                                        <div class="d-flex justify-content-center">
                                            <span>
                                                {{ $t('general.DueDate') }}
                                            </span>
                                        </div>
                                    </th>
                                    <th colspan="3">
                                        <div class="d-flex justify-content-center">
                                            <span>
                                            {{ $t('general.PastYears') }}
                                        </span>
                                        </div>
                                    </th>
                                    <th colspan="3" class="first-section">
                                        <div class="d-flex justify-content-center">
                                            <span>
                                            {{ $t('general.CurrentYear') }}
                                        </span>
                                        </div>
                                    </th>
                                    <th colspan="3">
                                        <div class="d-flex justify-content-center">
                                            <span>
                                                {{ $t('general.ExistingYears') }}
                                            </span>
                                        </div>
                                    </th>
                                    <th colspan="3" class="first-section">
                                        <div class="d-flex justify-content-center">
                                            <span>
                                                {{ $t('general.FutureYears') }}
                                            </span>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <th v-if="setting.dues">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Dues') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('dues'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-dues'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.paid">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Paid') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('paid'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-paid'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.remaining">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Remaining') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('remaining'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-remaining'))"></i>
                                            </div>
                                        </div>
                                    </th>

                                    <th v-if="setting.dues" class="first-section">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Dues') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('dues'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-dues'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.paid" class="first-section">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Paid') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('paid'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-paid'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.remaining" class="first-section">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Remaining') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('remaining'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-remaining'))"></i>
                                            </div>
                                        </div>
                                    </th>

                                    <th v-if="setting.dues">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Dues') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('dues'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-dues'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.paid">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Paid') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('paid'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-paid'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.remaining">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Remaining') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('remaining'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-remaining'))"></i>
                                            </div>
                                        </div>
                                    </th>

                                    <th v-if="setting.dues" class="first-section">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Dues') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('dues'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-dues'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.paid" class="first-section">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Paid') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('paid'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-paid'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.remaining" class="first-section">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Remaining') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('remaining'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-remaining'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody v-if="installmentStatus.length > 0">
                                <tr v-for="(item, index) in displayData" :key="index">
                                    <td>{{ item.name }}</td>
                                    <td>{{ item.instalment_date }}</td>
                                    <td v-if="item.PastYears">
                                        <span>{{ item.PastYears.Dues }}</span>
                                    </td>
                                    <td v-else>
                                        <span>0</span>
                                    </td>
                                    <td v-if="item.PastYears">
                                        <span>{{ item.PastYears.Paid }}</span>
                                    </td>
                                    <td v-else>
                                        <span>0</span>
                                    </td>
                                    <td v-if="item.PastYears">
                                        <span>{{ item.PastYears.Remaining }}</span>
                                    </td>
                                    <td v-else>
                                        <span>0</span>
                                    </td>
                                    <td v-if="item.CurrentYear">
                                        <span>{{ item.CurrentYear.Dues }}</span>
                                    </td>
                                    <td v-else>
                                        <span>0</span>
                                    </td>
                                    <td v-if="item.CurrentYear">
                                        <span>{{ item.CurrentYear.Paid }}</span>
                                    </td>
                                    <td v-else>
                                        <span>0</span>
                                    </td>
                                    <td v-if="item.CurrentYear">
                                        <span>{{ item.CurrentYear.Remaining }}</span>
                                    </td>
                                    <td v-else>
                                        <span>0</span>
                                    </td>
                                    <td v-if="item.ExistingYears">
                                        <span>{{ item.ExistingYears.Dues }}</span>
                                    </td>
                                    <td v-else>
                                        <span>0</span>
                                    </td>
                                    <td v-if="item.ExistingYears">
                                        <span>{{ item.ExistingYears.Paid }}</span>
                                    </td>
                                    <td v-else>
                                        <span>0</span>
                                    </td>
                                    <td v-if="item.ExistingYears">
                                        <span>{{ item.ExistingYears.Remaining }}</span>
                                    </td>
                                    <td v-else>
                                        <span>0</span>
                                    </td>
                                    <td v-if="item.FutureYears">
                                        <span>{{ item.FutureYears.Dues }}</span>
                                    </td>
                                    <td v-else>
                                        <span>0</span>
                                    </td>
                                    <td v-if="item.FutureYears">
                                        <span>{{ item.FutureYears.Paid }}</span>
                                    </td>
                                    <td v-else>
                                        <span>0</span>
                                    </td>
                                    <td v-if="item.FutureYears">
                                        <span>{{ item.FutureYears.Remaining }}</span>
                                    </td>
                                    <td v-else>
                                        <span>0</span>
                                    </td>

                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="12">{{ $t('general.notDataFound') }}</th>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td v-for="(column, columnIndex) in columns" :key="columnIndex">
                                        <template v-if="columnIndex > 1"> <!-- Starting from the third column -->
                                            {{ calculateColumnSum(columnIndex) }}
                                        </template>
                                        <template v-else>
                                            <!-- Add any specific footer content for the first columns if needed -->
                                        </template>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>

                        </div>
                        <!-- end .table-responsive-->

                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input[type=number] {
    -moz-appearance: textfield;
}

.multiselect__single {
    font-weight: 600 !important;
    color: black !important;
}

.td5 {
    font-size: 16px !important;
}

.first-section {
    background-color: #f1f5f7;
    color: black;
}


</style>



