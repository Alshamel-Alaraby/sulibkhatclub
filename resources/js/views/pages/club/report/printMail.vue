<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import adminApi from "../../../../api/adminAxios";
import Switches from "vue-switches";
import Multiselect from "vue-multiselect";
import permissionGuard from "../../../../helper/permission";

import {required,} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../../components/widgets/errorMessage";
import loader from "../../../../components/general/loader";
import {dynamicSortNumber, dynamicSortString,} from "../../../../helper/tableSort";
import translation from "../../../../helper/mixin/translation-mixin";
import {formatDateOnly} from "../../../../helper/startDate";
import DatePicker from "vue2-datepicker";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Print Mail Report",
        meta: [{name: "description", content: "Print Mail Report"}],
    },
    mixins: [translation],
    components: {
        Multiselect,
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        DatePicker,
    },
    data() {
        return {
            permissions: [],
            create: {
                date: '',
                cm_permission_id: []
            },
            per_page: 16,
            search: "",
            debounce: {},
            enabled3: true,
            itemsPagination: {},
            progress: 0,
            items: [],
            isLoader: false,
            Tooltip: "",
            mouseEnter: "",
            fields: [],
            company_id: null,
            errors: {},
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            record_number: '',
            digits: [],
            lastDigit: null,
            rest: "",
            startingLastDigit: null,
            setting: {
                record_number: true,
                full_name: true,
                home_address: true,
                fils: true,
                'KD': true,
            },
            is_disabled: false,
            filterSetting: [
                "membership_number",
                "full_name",
                "birth_date",
                "gender",
                "membership_date",
                "financial_status_id",
                "member_status_id",
                "national_id",
                "home_phone",
                "home_address",
                "work_phone",
                "job",
                "degree",
            ],
            branch: {},
            printLoading: true,
            printObj: {
                id: "printData",
            },
            fils: 0,
            kd: 0
        };
    },
    validations: {
        create: {
            cm_permission_id: {required},
            date: {required},
        },
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Print Mail", "all Permission member club");
        });
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
                this.items.forEach((el) => {
                    if (!this.checkAll.includes(el.id)) {
                        this.checkAll.push(el.id);
                    }
                });
            } else {
                this.checkAll = [];
            }
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.getMemberPermissions();
        this.getBranches();
    },
    methods: {
        formatDate(value) {
            return formatDateOnly(value);
        },
        getData(page = 1) {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                let dateStartArray = this.create.date.split("-");
                let startDate = dateStartArray[2] + "-" + dateStartArray[1] + "-" + dateStartArray[0];

                adminApi
                    .get(`/club-members/reports/members-permissions?members_permissions_id=${this.create.cm_permission_id}&dateOfYear=${startDate}&page=${page}&per_page=16`, {
                        params: {
                            members_permissions_id: this.create.cm_permission_id,
                            dateOfYear: this.create.date
                        },
                    })
                    .then((res) => {
                        let l = res.data;
                        this.items = l.data;
                        this.itemsPagination = l.pagination;
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
        getDataCurrentPage(page = 1) {
            if (
                this.current_page <= this.itemsPagination.last_page &&
                this.current_page != this.itemsPagination.current_page &&
                this.current_page
            ) {
                this.$v.create.$touch();
                if (this.$v.create.$invalid) {
                    return;
                } else {
                    this.isLoader = true;
                    let dateStartArray = this.create.date.split("-");
                    let startDate = dateStartArray[2] + "-" + dateStartArray[1] + "-" + dateStartArray[0];

                    adminApi
                        .get(`/club-members/reports/members-permissions?members_permissions_id=${this.create.cm_permission_id}&dateOfYear=${startDate}&page=${this.current_page}&per_page=16&search=${this.search}&${filter}`, {
                            params: {
                                members_permissions_id: this.create.cm_permission_id,
                                dateOfYear: this.create.date
                            },
                        })
                        .then((res) => {
                            let l = res.data;
                            this.items = l.data;
                            this.itemsPagination = l.pagination;
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
            }
        },
        async getMemberPermissions() {
            this.isLoader = true;

            await adminApi
                .get(`/club-members/members-permissions`)
                .then((res) => {
                    let l = res.data.data;
                    this.permissions = l;
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
        sortString(value) {
            return dynamicSortString(value);
        },
        SortNumber(value) {
            return dynamicSortNumber(value);
        },
        checkRow(id) {
            if (!this.checkAll.includes(id)) {
                this.checkAll.push(id);
            } else {
                let index = this.checkAll.indexOf(id);
                this.checkAll.splice(index, 1);
            }
        },
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
                if (dl) {
                    XLSX.write(wb, {bookType: type, bookSST: true, type: "base64"});
                } else {
                    XLSX.writeFile(
                        wb,
                        fn || ("Permission Member Report" + "." || "SheetJSTableExport.") + (type || "xlsx")
                    );
                }
                this.enabled3 = true;
            }, 100);
        },
        getBranches() {
            adminApi
                .get(`/branches/2`)
                .then((res) => {
                    this.branch = res.data.data;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })
        },
        getRecord() {
            let recordNumber = this.record_number.toString();
            this.digits = [];

            if (recordNumber.length > 0) {
                const lastDigit = recordNumber.slice(-1);
                const rest = recordNumber.slice(0, -1);


                this.startingLastDigit = (this.current_page - 1) * this.per_page;
                this.lastDigit = lastDigit;
                this.rest = rest;

                console.log('lastDigit', this.lastDigit);
                console.log('rest', this.rest);
            }
            console.log('digits', this.digits);
        },
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
                            <h4 class="header-title">
                                {{ $t("general.printMail") }}
                            </h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">

                                </div>

                                <div
                                    class="d-inline-block position-relative"
                                    style="width: 77%"
                                >
                                    <div class="form-group position-relative"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end search -->

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-5 d-flex align-items-center mb-1 mt-2 mb-xl-0">
                                <b-button
                                    v-b-modal.create
                                    variant="primary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    {{ $t('general.Search') }}
                                    <i class="fe-search"></i>
                                </b-button>
                                <b-button
                                    v-b-modal.create_price
                                    variant="secondary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    ثمن الطابع
                                </b-button>
                                <!-- start create and printer -->
                                <div class="d-inline-flex">
                                    <button
                                        style="margin: 0 15px"
                                        @click="ExportExcel('xlsx')"
                                        class="custom-btn-dowonload"
                                    >
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printData'" class="custom-btn-dowonload" v-if="items.length > 0">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <b-button v-print="'#printEnvelope'" variant="info" v-if="items.length > 0">
                                        {{ $t("general.envelopePrinting") }}
                                        <i class="fe-printer"></i>
                                    </b-button>
                                </div>
                                <!-- end create and printer -->
                            </div>
                            <div class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end">
                                <div class="d-fex">
                                    <!-- start filter and setting -->
                                    <div class="d-inline-block">
                                        <b-button class="mx-1 custom-btn-background">
                                            {{ $t("general.filter") }}
                                            <i class="fas fa-filter"></i>
                                        </b-button>
                                        <b-button class="mx-1 custom-btn-background">
                                            {{ $t("general.group") }}
                                            <i class="fe-menu"></i>
                                        </b-button>
                                        <!-- Basic dropdown -->
                                        <b-dropdown
                                            variant="primary"
                                            :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                                            ref="dropdown"
                                            class="dropdown-custom-ali"
                                        >
                                            <b-form-checkbox v-model="setting.record_number" class="mb-1">
                                                {{ getCompanyKey("member_membership_number") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.full_name" class="mb-1">
                                                {{ $t("general.full_name") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.home_address" class="mb-1">
                                                {{ getCompanyKey("member_home_address") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.fils" class="mb-1">{{
                                                    $t("general.fils")
                                                }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.KD" class="mb-1">{{
                                                    $t('general.KD')
                                                }}
                                            </b-form-checkbox>
                                            <div class="d-flex justify-content-end">
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm">
                                                    Apply
                                                </a>
                                            </div>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </div>
                                    <!-- end filter and setting -->

                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom">
                                        <div class="d-inline-block" style="font-size: 13px">
                                            {{ itemsPagination.from }}-{{ itemsPagination.to }} /
                                            {{ itemsPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{ 'pointer-events': itemsPagination.current_page == 1 ? 'none' : '',}"
                                                @click.prevent="getData(itemsPagination.current_page - 1)"
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
                                                :style="{
                                                  'pointer-events': itemsPagination.last_page ==itemsPagination.current_page
                                                      ? 'none' : '',
                                                }"
                                                @click.prevent="getData(itemsPagination.current_page + 1)"
                                            >
                                                <span>&gt;</span>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>

                        <!--  create_price   -->
                        <b-modal
                            id="create_price"
                            :title="$t('general.execution')"
                            title-class="font-18"
                            body-class="p-4"
                            :hide-footer="true"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <!-- Emulate built in modal footer ok and cancel button actions -->
                                    <b-button
                                        variant="success"
                                        type="button" class="mx-1"
                                        v-if="!isLoader"
                                        @click.prevent="getRecord()"
                                    >
                                        {{ $t('general.execution') }}
                                    </b-button>
                                    <b-button variant="danger" type="button"
                                              @click.prevent="$bvModal.hide('create_price')">
                                        {{ $t('general.Cancel') }}
                                    </b-button>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ $t('general.KD') }}
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                step="0.01"
                                                v-model.number="kd"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.fils') }}
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                step="0.01"
                                                v-model.number="fils"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ $t('general.StartBarcode') }}
                                            </label>
                                            <input
                                                v-model="record_number"
                                                type="number"
                                                class="form-control"
                                                step="1"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /create_price   -->

                        <!--  create   -->
                        <b-modal
                            id="create"
                            :title="$t('general.execution')"
                            title-class="font-18"
                            body-class="p-4"
                            :hide-footer="true"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <template>
                                        <b-button
                                            variant="success"
                                            type="button" class="mx-1"
                                            v-if="!isLoader"
                                            @click.prevent="getData(1)"
                                        >
                                            {{ $t('general.execution') }}
                                        </b-button>

                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                        </b-button>
                                    </template>
                                    <!-- Emulate built in modal footer ok and cancel button actions -->

                                    <b-button variant="danger" type="button" @click.prevent="$bvModal.hide('create')">
                                        {{ $t('general.Cancel') }}
                                    </b-button>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-md-12">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ $t('general.permissionsMember') }}
                                            </label>
                                            <multiselect
                                                :multiple="true"
                                                v-model="$v.create.cm_permission_id.$model"
                                                :options="permissions.map((type) => type.id)"
                                                :custom-label="
                                                  (opt) => permissions.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'
                                                      ? permissions.find((x) => x.id == opt).name
                                                      : permissions.find((x) => x.id == opt).name_e:null
                                                "
                                            >
                                            </multiselect>
                                            <div
                                                v-if="$v.create.cm_permission_id.$error"
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.date') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="$v.create.date.$model"
                                                placeholder="DD-MM-YYYY"
                                                format="DD-MM-YYYY"
                                                valueType="format"
                                                :confirm="false"
                                            ></date-picker>
                                            <div
                                                v-if="
                                                  $v.create.date.$error
                                                "
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <div id="printEnvelope" class="head-branch"
                             :style="{ 'direction': $i18n.locale == 'ar' ? 'rtl!important' : '', 'text-align': $i18n.locale == 'ar' ? 'start' : '' }">
                            <div class="row justify-content-center align-items-center content border-0 text-center"
                                 v-for="(item, index) in items" :key="index">
                                <div class="col-md-12 d-flex justify-content-around mt-5">


                                    <div class="col-8 text-center">
                                        <div class="index-box float-right">{{ index + 1 }}</div>
                                        <div class="current-page-box float-left">{{ current_page }}</div>
                                        <div class="member-info">
                                            <p style="font-size: 40px;">{{ $t('general.memberName') }}:
                                                {{ item.full_name }}</p>
                                            <p style="font-size: 40px;">{{ $t('general.memberAddress') }}:
                                                {{ item.home_address }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table"
                             id="printData"
                             :style="{ 'direction': $i18n.locale == 'ar' ? 'rtl!important' : '', 'text-align': $i18n.locale == 'ar' ? 'start' : '' }">
                            <div class="head-branch overflow-hidden">
                                <div class="row">
                                    <div class="image-header px-0">
                                        <img src="/images/minstryOfComm.png" class="mr-5">
                                        <h4 class="d-inline-flex text-center" style="font-size: 20px;">
                                            {{ current_page }}</h4>
                                        <img src="/images/mailPrint.png"/>
                                    </div>
                                    <div class="col-2 mb-4 d-flex justify-content-start">
                                        <div class="border-custom"></div>
                                    </div>
                                    <div class="col-8 text-center"
                                         style="border-right: 2px solid;border-bottom: 2px solid;">
                                        <div class="d-flex justify-content-between mb-4">
                                            <div style="font-size: 30px;">قائمه بعاث المسجله المرسله:</div>
                                            <div style="font-size: 20px;">:List of registered items posted</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="col-1 font-weight-bold" style="font-size: 30px;">من:</div>
                                            <h3 class="text-center col-10 pt-4">
                                                <span style="font-size: 30px;">
                                                    {{ $t('general.SulaibikhatClub') }}
                                                </span>
                                            </h3>
                                            <div class="col-1 font-weight-bold" style="font-size: 30px;">:BY</div>
                                        </div>
                                    </div>
                                    <div class="col-2 pt-4">
                                        <div style="font-size: 30px;">قيمه اجور البريد</div>
                                        <div style="font-size: 30px;">بما فيها التسجيل</div>
                                        <div style="font-size: 20px;">Account of postage</div>
                                        <div style="font-size: 20px;">registrationFee</div>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <div style="font-size: 30px;">تاريخ خاتم المكتب المستلم</div>
                                        <div style="font-size: 20px;">Date stamo of receiving office</div>
                                    </div>
                                </div>
                            </div>

                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader"/>
                            <!--       end loader       -->
                            <table class="zizo-table table-borderless table-hover table-centered m-0 w-100">
                                <thead>
                                <tr>
                                    <th>{{ $t('general.series') }}</th>
                                    <th v-if="setting.record_number" colspan="2">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.RecordNumber") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="items.sort(sortString('record_number'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="items.sort(sortString('-record_number'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.full_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.full_name") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="items.sort(sortString('full_name'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="items.sort(sortString('-full_name'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.home_address">
                                        {{ $t('general.Address') }}
                                    </th>
                                    <th v-if="setting.fils">{{ $t('general.fils') }}</th>
                                    <th v-if="setting['KD']">{{ $t('general.KD') }}</th>
                                </tr>
                                </thead>
                                <tbody v-if="items.length > 0">
                                <tr
                                    v-for="(data, index) in items"
                                    :key="data.id"
                                    class="body-tr-custom"
                                >
                                    <td>
                                        {{ (current_page - 1) * per_page + index + 1 }}
                                    </td>
                                    <td v-if="setting.record_number && parseInt(lastDigit) >= 0">
                                        {{
                                            (parseInt(lastDigit) + (current_page - 1) * per_page + index + startingLastDigit) % 10
                                        }}
                                    </td>
                                    <td v-else>
                                        {{ lastDigit }}
                                    </td>
                                    <td v-if="setting.record_number">
                                        {{ rest }}
                                    </td>

                                    <td v-if="setting.full_name">
                                        {{ data.full_name }}
                                    </td>
                                    <td v-if="setting.home_address">
                                        {{ data.home_address }}
                                    </td>
                                    <td v-if="setting.fils">{{ fils ?? '0.00' }}</td>
                                    <td v-if="setting['KD']">{{ kd ?? '0.00' }}</td>
                                </tr>

                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="12">
                                        {{ $t("general.notDataFound") }}
                                    </th>
                                </tr>
                                </tbody>
                            </table>
                            <div class="head-branch overflow-hidden">
                                <div class="row justify-content-between mt-2">
                                    <div class="col-6">
                                        <h3 style="font-size: 25px;">استلمت ـــــــــــــ بعثيه مسجله حسب البيان
                                            اعلاه </h3>
                                    </div>
                                    <div class="col-6">
                                        <h4 style="font-size: 25px; direction: ltr !important;">Received ـــــــــــــ
                                            Registered items as entered
                                            above </h4>

                                    </div>
                                </div>
                                <br>
                                <br>
                                <div class="row justify-content-between" style="border-bottom: 2px solid;">
                                    <div class="col-3">
                                        <div class="text-center">
                                            <h3 style="font-size: 25px;">
                                                اسم الموظف المستلم بالكامل
                                            </h3>
                                        </div>
                                        <div class="text-center">
                                            <h3 style="font-size: 25px;">Name of receiving office in full</h3>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="text-center"><h3 style="font-size: 25px;">تدرج التفاصيل المطلوبه
                                            من</h3></div>
                                        <div class="text-center"><h3 style="font-size: 25px;">قبل المرسل</h3></div>
                                    </div>
                                </div>
                                <div class="row justify-content-between" style="border-bottom: 2px solid;">
                                    <div class="col-3">
                                        <div class="text-center"><h3 style="font-size: 25px;">التوقيعه:</h3></div>
                                        <div class="text-center"><h3 style="font-size: 25px;">signature</h3></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="text-center"><h3 style="font-size: 25px;">To be completed</h3></div>
                                        <div class="text-center"><h3 style="font-size: 25px;">By the poster</h3></div>
                                    </div>
                                </div>
                                <div class="finial-border">
                                    <h3 style="font-size: 30px;">
                                        ** تنص الطوابع الخاصه بالمواد اعلاء في خلف القائمه
                                    </h3>
                                </div>

                                <br>
                                <br>

                                <div class="main-container" v-if="items.length > 0">
                                    <div class="row col-12 border mx-0 px-0" v-for="i in Math.floor(items.length / 2)"
                                         :key="i">
                                        <div class="col-6 px-0">
                                            <div class="rectangle">
                                                {{ i }}
                                            </div>
                                        </div>
                                        <div class="col-6 px-0">
                                            <div class="rectangle">
                                                {{ i + Math.floor(items.length / 2) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row col-12 border" v-if=" items.length % 2 == 1">
                                        <div class="col-6 px-0">
                                            <div class="rectangle">
                                                {{ items.length }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end .table-responsive-->
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
.signature {
    display: none;
}

.main-container {
    margin: 110px 20px 0;
    border-radius: 20px;
    border-style: double;
    border-spacing: 10px;

}

.col-6 {
    padding: 0 15px;
}

.rectangle {
    border: 1px solid #ccc;
    height: 165px; /* Adjust the height as needed */
    width: 90px; /* Adjust the width as needed */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.head-zizo {
    display: none;
}

.head-branch {
    display: none;
}

.colPay {
    background-color: #3bafda;
    color: #fff;
    font-weight: 500;
}

.border-custom {
    width: 100px;
    height: 100px;
    border: 1px solid;
    border-radius: 50%;
}

.finial-border {
    margin-bottom: 5%;
}

@media print {
    .colPay {
        color: #000;
    }

    .head-branch {
        margin-top: 30px;
        display: block;
    }

    .head-branch h2 {
        text-decoration: underline;
    }

    .head-branch img {
        width: 100px;
        height: 100px;
    }

    .head-branch span {
        display: inline-block;
        position: relative;
        top: -49px;
    }

    .do-not-print {
        display: none;
    }

    .arrow-sort {
        display: none;
    }

    .zizo-table {
        min-height: 460px !important;
    }

    table thead tr th {
        color: #000;
        border: 1px solid #000;
    }

    table tbody tr td {
        color: #000;
        border: 1px solid #000;
    }

    hr {
        transform: rotate(90deg);
        background-color: #0000008a;
        border: 1px solid #4444449c;
    }

    .custom-table-theme thead {
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
    }

    .custom-table-theme tbody {
        border: 1px solid #000;
    }

    .signature {
        display: block;
    }

    .signature h4 {
        text-decoration: underline;
        margin: 3px;
    }

    .content {
        min-height: 1270px;
        padding: 20px;
        border: 2px solid;
        margin-bottom: 300px;
        position: relative;
    }

    .image-header {
        width: 100%;
        margin-bottom: 30px;
        padding-left: 0px;
        display: flex;
        justify-content: space-between;
    }

    .image-header img {
        display: inline-block;
        margin-top: 7px;
        height: 130px;
        width: 230px;
    }

    .underline {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 1px;
        background-color: black; /* You can set the color of the line */
    }

    .position-relative {
        position: relative;

    }

    .index-box {
        background-color: black;
        padding: 8px;
        font-size: 25px;
        border: 2px solid;
        margin-bottom: 10px; /* Add margin to create space between boxes */
    }

    .current-page-box {
        background-color: black;
        padding: 8px;
        font-size: 25px;
        border: 2px solid;
        margin-bottom: 10px; /* Add margin to create space between boxes */
    }
}
</style>
