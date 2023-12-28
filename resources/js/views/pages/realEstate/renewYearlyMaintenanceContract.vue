<template>
    <Layout>
        <PageHeader />
        <div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row justify-content-between align-items-center mb-2">
                                <h4 class="header-title" v-if="document">{{ page_title && page_title.url == $route.fullPath
                                    ? ($i18n.locale == 'ar' ? page_title.title : page_title.title_e) : ($i18n.locale == "ar"
                                        ? $t('general.table') + ' ' + document.name : document.name_e + ' ' + $t('general.table')
                                    ) }}</h4>
                                <h4 class="header-title" v-else>{{ page_title && page_title.url == $route.fullPath ?
                                    ($i18n.locale == 'ar' ? page_title.title : page_title.title_e) : $t('general.table') }}
                                </h4>
                                <div>
                                    <b-button v-b-modal.searchFilter variant="primary" class="btn-sm mx-1 font-weight-bold">
                                        {{ $t("general.searchFilter") }}
                                        <i class="fas fa-plus"></i>
                                    </b-button>
                                </div>
                                <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                    <div class="d-inline-block" style="width: 22.2%">
                                        <!-- Basic dropdown -->
                                        <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                            class="btn-block setting-search">
                                            <b-form-checkbox v-model="filterSetting" value="date" class="mb-1">{{
                                                $t('general.Date') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="filterSetting" value="customer_id" class="mb-1">{{
                                                $t('general.documentCustomer') }}</b-form-checkbox>


                                            <b-form-checkbox v-model="filterSetting" value="prefix" class="mb-1">{{
                                                $t('general.serial_number') }}</b-form-checkbox>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </div>

                                    <div class="d-inline-block position-relative" style="width: 77%">
                                        <span
                                            :class="['search-custom position-absolute', $i18n.locale == 'ar' ? 'search-custom-ar' : '',]">
                                            <i class="fe-search"></i>
                                        </span>
                                        <input class="form-control" style="display: block; width: 93%; padding-top: 3px"
                                            type="text" v-model.trim="search" :placeholder="`${$t('general.Search')}...`" />
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-between align-items-center mb-2 px-1">
                                <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">

                                    <div class="d-inline-flex">
                                        <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                            <i class="fas fa-file-download"></i>
                                        </button>
                                        <button v-print="'#printReservation'" class="custom-btn-dowonload">
                                            <i class="fe-printer"></i>
                                        </button>

                                        <b-button v-b-modal.renew variant="primary" class="btn-sm mx-1 font-weight-bold" v-if="checkAll.length >= 1">
                                        {{ $t("general.Renew") }}
                                            <i class="fas fa-renew"></i>
                                        </b-button>


                                    </div>
                                </div>
                                <div class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end">
                                    <div>
                                        <b-button class="mx-1 custom-btn-background">
                                            {{ $t("general.filter") }}
                                            <i class="fas fa-filter"></i>
                                        </b-button>
                                        <b-button class="mx-1 custom-btn-background">
                                            {{ $t("general.group") }}
                                            <i class="fe-menu"></i>
                                        </b-button>
                                        <!-- Basic dropdown -->
                                        <b-dropdown variant="primary"
                                            :html="`${$t('general.setting')} <i class='fe-settings'></i>`" ref="dropdown"
                                            class="dropdown-custom-ali">
                                            <b-form-checkbox v-model="setting.date" class="mb-1">{{ $t('general.Date')
                                            }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.customer_id" class="mb-1">{{
                                                $t('general.documentCustomer') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.salesman_id" class="mb-1">{{
                                                $t('general.documentSalesmen') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.branch_id" class="mb-1">{{
                                                $t('general.Branch') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.serial_id" class="mb-1"> {{
                                                $t('general.serial_number') }}</b-form-checkbox>
                                            <div class="d-flex justify-content-end">
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm">{{
                                                    $t("general.Apply")
                                                }}</a>
                                            </div>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                        <!-- start Pagination -->
                                        <div class="d-inline-flex align-items-center pagination-custom">
                                            <div class="d-inline-block" style="font-size: 15px">
                                                {{ reservationsPagination.from }}-{{ reservationsPagination.to }}
                                                /
                                                {{ reservationsPagination.total }}
                                            </div>
                                            <div class="d-inline-block">
                                                <a href="javascript:void(0)" :style="{
                                                    'pointer-events':
                                                        reservationsPagination.current_page == 1 ? 'none' : '',
                                                }" @click.prevent="getData(reservationsPagination.current_page - 1)">
                                                    <span>&lt;</span>
                                                </a>
                                                <input type="text" @keyup.enter="getDataCurrentPage()"
                                                    v-model="current_page" class="pagination-current-page" />
                                                <a href="javascript:void(0)" :style="{
                                                    'pointer-events':
                                                        reservationsPagination.last_page ==
                                                            reservationsPagination.current_page
                                                            ? 'none'
                                                            : '',
                                                }" @click.prevent="getData(reservationsPagination.current_page + 1)">
                                                    <span>&gt;</span>
                                                </a>
                                            </div>
                                        </div>
                                        <!-- end Pagination -->
                                    </div>
                                </div>
                            </div>

                            <b-modal id="renew" :title="$t('general.Renew')" title-class="font-18" body-class="p-4"
                                size="lg" :hide-footer="true" @hidden="$bvModal.hide(`renew`)">
                                <form>
                                    <div class="mb-3 d-flex justify-content-end">
                                        <template v-if="!is_disabled">
                                            <b-button variant="success" type="button" class="mx-1" v-if="!isLoader"
                                                @click.prevent="RenewContracts">
                                                {{ $t('general.Renew') }}
                                            </b-button>

                                            <b-button variant="success" class="mx-1" disabled v-else>
                                                <b-spinner small></b-spinner>
                                                <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                            </b-button>
                                        </template>
                                        <!-- Emulate built in modal footer ok and cancel button actions -->

                                        <b-button variant="danger" type="button"
                                            @click.prevent="$bvModal.hide(`renew`)">
                                            {{ $t('general.Cancel') }}
                                        </b-button>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ $t('general.fromDate') }}
                                                </label>
                                                <date-picker type="date" v-model="renew.date_from"
                                                    format="YYYY-MM-DD" valueType="format" :confirm="false"></date-picker>

                                                <template v-if="errors.date_from">
                                                    <ErrorMessage v-for="(errorMessage, index) in errors.date_from"
                                                        :key="index">
                                                        {{ errorMessage }}
                                                    </ErrorMessage>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ $t('general.toDate') }}
                                                </label>
                                                <date-picker type="date" v-model="renew.date_to" format="YYYY-MM-DD"
                                                    valueType="format" :confirm="false"></date-picker>

                                                <template v-if="errors.date_to">
                                                    <ErrorMessage v-for="(errorMessage, index) in errors.date_to"
                                                        :key="index">
                                                        {{ errorMessage }}
                                                    </ErrorMessage>
                                                </template>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </b-modal>

                            <!--  search Filter   -->
                            <b-modal id="searchFilter" :title="$t('general.Search')" title-class="font-18" body-class="p-4"
                                size="lg" :hide-footer="true" @show="resetModalSearchFilter"
                                @hidden="resetModalHiddenSearchFilter">
                                <form>
                                    <div class="mb-3 d-flex justify-content-end">
                                        <template v-if="!is_disabled">
                                            <b-button variant="success" type="button" class="mx-1" v-if="!isLoader"
                                                @click.prevent="getData">
                                                {{ $t('general.Search') }}
                                            </b-button>

                                            <b-button variant="success" class="mx-1" disabled v-else>
                                                <b-spinner small></b-spinner>
                                                <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                            </b-button>
                                        </template>
                                        <!-- Emulate built in modal footer ok and cancel button actions -->

                                        <b-button variant="danger" type="button"
                                            @click.prevent="resetModalHiddenSearchFilter">
                                            {{ $t('general.Cancel') }}
                                        </b-button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{$t('general.documentBuilding') }}
                                                </label>
                                                <multiselect
                                                    :show-labels="false"
                                                    v-model="searchFilter.building_id"
                                                    :options="buildings.map((type) => type.id)"
                                                    :custom-label="(opt) => buildings.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? buildings.find((x) => x.id == opt).name: buildings.find((x) => x.id == opt).name_e : ''"

                                                >
                                                </multiselect>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ $t('general.Date') }}
                                                </label>
                                                <input type="number" v-model="searchFilter.date" class="form-control">

                                                <template v-if="errors.date">
                                                    <ErrorMessage v-for="(errorMessage, index) in errors.date"
                                                        :key="index">
                                                        {{ errorMessage }}
                                                    </ErrorMessage>
                                                </template>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </b-modal>
                            <!--  /search Filter   -->

                            <!-- start .table-responsive-->
                            <div class="table-responsive mb-3 custom-table-theme position-relative">
                                <!--       start loader       -->
                                <loader size="large" v-if="isLoader" />
                                <!--       end loader       -->

                                <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                                    id="printReservation">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 0" v-if="enabled3" class="do-not-print">
                                                <div class="form-check custom-control">
                                                    <input class="form-check-input" type="checkbox" v-model="isCheckAll"
                                                        style="width: 17px; height: 17px" />
                                                </div>
                                            </th>
                                            <th v-if="setting.date">
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ $t('general.Date') }}</span>
                                                    <div class="arrow-sort">
                                                        <i class="fas fa-arrow-up"
                                                            @click="reservations.sort(sortString('date'))"></i>
                                                        <i class="fas fa-arrow-down"
                                                            @click="reservations.sort(sortString('-date'))"></i>
                                                    </div>
                                                </div>
                                            </th>
                                            <th v-if="setting.customer_id">
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ $t('general.documentCustomer') }}</span>
                                                    <div class="arrow-sort">
                                                        <i class="fas fa-arrow-up"
                                                            @click="reservations.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                        <i class="fas fa-arrow-down"
                                                            @click="reservations.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                                    </div>
                                                </div>
                                            </th>
                                            <th v-if="setting.building_id">
                                                <div class="d-flex justify-content-center">
                                                    <span>{{  $t('general.building') }}</span>

                                                </div>
                                            </th>
                                            <th v-if="setting.unit_id">
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ $t('general.Unit') }}</span>

                                                </div>
                                            </th>
                                            <th v-if="setting.date_from">
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ $t('general.from_date') }}</span>

                                                </div>
                                            </th>
                                            <th v-if="setting.date_to">
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ $t('general.to_date') }}</span>

                                                </div>
                                            </th>
                                            <th v-if="setting.amount">
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ $t('general.amount') }}</span>
                                                    <div class="arrow-sort">
                                                        <i class="fas fa-arrow-up"
                                                            @click="reservations.sort(sortString('net_invoice'))"></i>
                                                        <i class="fas fa-arrow-down"
                                                            @click="reservations.sort(sortString('-net_invoice'))"></i>
                                                    </div>
                                                </div>
                                            </th>

                                            <th v-if="setting.serial_id">
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ $t('general.serial_number') }}</span>
                                                    <div class="arrow-sort">
                                                        <i class="fas fa-arrow-up"
                                                            @click="reservations.sort(sortString('prefix'))"></i>
                                                        <i class="fas fa-arrow-down"
                                                            @click="reservations.sort(sortString('-prefix'))"></i>
                                                    </div>
                                                </div>
                                            </th>

                                        </tr>
                                    </thead>
                                    <tbody v-if="reservations.length > 0">
                                        <tr @click.capture="checkRow(data.id)"

                                            v-for="(data, index) in reservations" :key="data.id" class="body-tr-custom">
                                            <td v-if="enabled3" class="do-not-print">
                                                <div class="form-check custom-control" style="min-height: 1.9em">
                                                    <input style="width: 17px; height: 17px" class="form-check-input"
                                                        type="checkbox" :value="data.id" v-model="checkAll" />
                                                </div>
                                            </td>
                                            <td v-if="setting.date">
                                                <h5 class="m-0 font-weight-normal">
                                                    {{ data.date }}
                                                </h5>
                                            </td>
                                            <td v-if="setting.customer_id">
                                                <h5 class="m-0 font-weight-normal">
                                                    {{ $i18n.locale == "ar" ? data.customer.name : data.customer.name_e }}
                                                </h5>
                                            </td>
                                            <td v-if="setting.building_id">
                                                <h5 class="m-0 font-weight-normal">
                                                    {{ data.header_details[0] ? ($i18n.locale == "ar" ? data.header_details[0].building.name : data.header_details[0].building.name_e) :'' }}
                                                </h5>
                                            </td>
                                            <td v-if="setting.unit_id">
                                                <h5 class="m-0 font-weight-normal">
                                                    {{ data.header_details[0] ? ($i18n.locale == "ar" ? data.header_details[0].unit.name : data.header_details[0].unit.name_e) :'' }}
                                                </h5>
                                            </td>
                                            <td v-if="setting.date_from">
                                                <h5 class="m-0 font-weight-normal">
                                                    {{ data.header_details[0] ? data.header_details[0].date_from :'' }}
                                                </h5>
                                            </td>
                                            <td v-if="setting.date_to">
                                                <h5 class="m-0 font-weight-normal">
                                                    {{ data.header_details[0] ? data.header_details[0].date_to :'' }}
                                                </h5>
                                            </td>
                                            <td v-if="setting.amount">
                                                <h5 class="m-0 font-weight-normal">
                                                    {{ data.net_invoice }}
                                                </h5>
                                            </td>

                                            <td v-if="setting.serial_id">
                                                <h5 class="m-0 font-weight-normal">
                                                    {{ data.prefix }}
                                                </h5>
                                            </td>


                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <th class="text-center" colspan="8">
                                                {{ $t("general.notDataFound") }}
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- end .table-responsive-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>


<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import Swal from "sweetalert2";
import permissionGuard from "../../../helper/permission";

import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import translation from "../../../helper/mixin/translation-mixin";
import { formatDateOnly } from "../../../helper/startDate";
import adminApi from "../../../api/adminAxios";
import { dynamicSortString } from "../../../helper/tableSort";
import page_title from "../../../helper/PageTitle";
import successError from "../../../helper/mixin/success&error";
import DatePicker from "vue2-datepicker";

import Multiselect from "vue-multiselect";

export default {
    page: {
        title: "RenewYearlyMaintenanceContract",
        meta: [{ name: "RenewYearlyMaintenanceContract", content: "RenewYearlyMaintenanceContract" }],
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Renew Yearly Maintenance Contract", "all RenewYearlyMaintenanceContract");
        });

    },
    components: {
        Layout,
        DatePicker,
        PageHeader,
        Multiselect,
        ErrorMessage,
        loader,
    },

    mixins: [translation, successError],

    computed: {
        pageTitle: function () {
            return page_title.value;
        },
    },
    data() {
        return {
            page_title: {},
            per_page: 50,
            search: "",
            debounce: {},
            reservationsPagination: {},
            reservations: [],
            document: null,
            enabled3: true,
            isLoader: false,
            setting: {
                date: true,
                customer_id: true,
                amount: true,
                building_id: true,
                unit_id: true,
                date_from: true,
                date_to: true,
                serial_id: true,
            },
            filterSetting: [
                'date',
                'customer_id',
                'prefix',
            ],
            errors: {},
            isCheckAll: false,
            checkAll: [],
            buildings: [],
            is_disabled: false,
            current_page: 1,
            company_id: null,
            Tooltip: "",
            mouseEnter: null,
            printLoading: true,
            printObj: {
                id: "printReservation",
            },
            searchFilter: {
                date: null,
                building_id: null,
            },
            renew: {
                date_from: null,
                date_to: null,
            },
        };
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
                this.reservations.forEach((el) => {
                    if (!this.checkAll.includes(el.id)) {
                        this.checkAll.push(el.id);
                    }
                });
            } else {
                this.checkAll = [];
            }
        },
        pageTitle: {
            handler(newV, old) {
                this.page_title = page_title.value;
            },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.getDocumentData();
        this.getData();
        this.getBuildings();
        this.$store.dispatch('locationIp/getIp');
    },
    methods: {
        getBuildings(){
            this.isLoader = true;
            adminApi
                .get(`/real-estate/buildings`)
                .then((res) => {
                    let l = res.data.data;
                    this.buildings = l;
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
         *  get Data reservations
         */
        getData(page = 1) {
            this.isLoader = true;
            let _filterSetting = [...this.filterSetting];
            let index = this.filterSetting.indexOf("customer_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "customer.name" : "customer.name_e";
            }

            let filter = "";
            for (let i = 0; i < _filterSetting.length; ++i) {
                filter += `columns[${i}]=${_filterSetting[i]}&`;
            }
            adminApi
                .get(
                    `document-headers/all_renew_contract_header?document_id=${44}&start_date=${this.searchFilter.date ?? ''}&building_id=${this.searchFilter.building_id ?? ''}&page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}&is_quotation=1`
                )
                .then((res) => {
                    let l = res.data;
                    this.reservations = l.data;
                    this.reservationsPagination = l.pagination;
                    this.current_page = l.pagination.current_page;
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
        getDataCurrentPage() {
            if (
                this.current_page <= this.reservationsPagination.last_page &&
                this.current_page != this.reservationsPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let _filterSetting = [...this.filterSetting];
                let index = this.filterSetting.indexOf("customer_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "customer.name" : "customer.name_e";
                }
                index = this.filterSetting.indexOf("employee_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "employee.name" : "employee.name_e";
                }

                index = this.filterSetting.indexOf("branch_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "branch.name" : "branch.name_e";
                }
                let filter = "";
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }

                adminApi.get(
                    `document-headers/all_renew_contract_header?document_id=${44}&date=${this.searchFilter.date ?? ''}&building_id=${this.searchFilter.building_id ?? ''}&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}&is_quotation=1`
                )
                    .then((res) => {
                        let l = res.data;
                        this.reservations = l.data;
                        this.reservationsPagination = l.pagination;
                        this.current_page = l.pagination.current_page;
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
            }
        },


        async RenewContracts() {
            this.isLoader = true;
            await adminApi
                .post(`document-headers/bulk-renew-contract`,{ids:this.checkAll,date_from:this.renew.date_from,date_to:this.renew.date_to})
                .then((res) => {
                    this.isLoader = false;
                    setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Renewed Successfully")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        }, 500);
                   this.getData()
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getDocumentData() {
            this.isLoader = true;
            await adminApi
                .get(`/document/${44}`)
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
                });
        },
        /**
         *  start  dynamicSortString
         */
        sortString(value) {
            return dynamicSortString(value);
        },
        checkRow(id) {
            if (!this.checkAll.includes(id)) {
                this.checkAll.push(id);
            } else {
                let index = this.checkAll.indexOf(id);
                this.checkAll.splice(index, 1);
            }
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if (this.mouseEnter != id) {
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`document-headers/${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.forEach((e) => {
                            this.Tooltip += `Created By: ${e.causer_type}; Event: ${e.event
                                }; Description: ${e.description} ;Created At: ${this.formatDate(
                                    e.created_at
                                )} \n`;
                        });
                        $(`#tooltip-${id}`).tooltip();
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
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
                if (dl) {
                    XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' });
                } else {
                    XLSX.writeFile(wb, fn || ((this.document.name + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },

        /**
         *  reset Modal (Search Filter)
         */
        resetModalHiddenSearchFilter() {
            this.is_disabled = false;

            this.errors = {};
            this.$bvModal.hide(`searchFilter`);
        },
        /**
         *  hidden Modal (Search Filter)
         */
        async resetModalSearchFilter() {
            this.is_disabled = false;

            this.errors = {};
        },
    }
}
</script>

<style scoped></style>
