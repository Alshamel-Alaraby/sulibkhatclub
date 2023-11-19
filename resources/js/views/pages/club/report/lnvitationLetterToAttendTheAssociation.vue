<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import adminApi from "../../../../api/adminAxios";
import Switches from "vue-switches";
import Multiselect from "vue-multiselect";
import permissionGuard from "../../../../helper/permission";

import {required, minLength, maxLength, integer, requiredIf,} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../../components/widgets/errorMessage";
import loader from "../../../../components/general/loader";
import {
    dynamicSortString,
    dynamicSortNumber,
} from "../../../../helper/tableSort";
import translation from "../../../../helper/mixin/translation-mixin";
import {formatDateOnly} from "../../../../helper/startDate";
import DatePicker from "vue2-datepicker";
import {arabicValue, englishValue} from "../../../../helper/langTransform";

// require styles
import 'quill/dist/quill.core.css';
import 'quill/dist/quill.snow.css';
import 'quill/dist/quill.bubble.css';
/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Members have the right to attend the assembly",
        meta: [{name: "description", content: "Members have the right to attend the assembly"}],
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
            per_page: 50,
            search: "",
            content: '',
            editorOption: {
                // Some Quill options...
                theme: "snow",
                modules: {
                    toolbar: [
                        [
                            {
                                font: [],
                            },
                            {
                                size: [],
                            },
                        ],
                        ["bold", "italic", "underline", "strike"],
                        [
                            {
                                color: [],
                            },
                            {
                                background: [],
                            },
                        ],
                        [
                            {
                                script: "super",
                            },
                            {
                                script: "sub",
                            },
                        ],
                        [
                            {
                                header: [false, 1, 2, 3, 4, 5, 6],
                            },
                            "blockquote",
                            "code-block",
                        ],
                        [
                            {
                                list: "ordered",
                            },
                            {
                                list: "bullet",
                            },
                            {
                                indent: "-1",
                            },
                            {
                                indent: "+1",
                            },
                        ],
                        [
                            "direction",
                            {
                                align: [],
                            },
                        ],
                        ["link", "image", "video"],
                        ["clean"],
                    ],
                },
            },
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
            setting: {
                membership_number: true,
                full_name: true,
                first_name: false,
                second_name: false,
                third_name: false,
                last_name: false,
                family_name: false,
                birth_date: true,
                gender: true,
                membership_date: true,
                financial_status_id: true,
                member_status_id: true,
                PaymentDate: true,
                document_no: true,
                ForAYear: true,
                national_id: true,
                home_phone: true,
                home_address: true,
                work_phone: true,
                job: true,
                degree: true,
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
            books: []
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
            return permissionGuard(vm, "invitation Letter To Attend The Association", "all Permission member club");
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
                    .get(`/club-members/reports/members-permissions?members_permissions_id=${this.create.cm_permission_id}&dateOfYear=${startDate}&page=${page}&per_page=50`, {
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
                        .get(`/club-members/reports/members-permissions?members_permissions_id=${this.create.cm_permission_id}&dateOfYear=${startDate}&page=${this.current_page}&per_page=50&search=${this.search}&${filter}`, {
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
                .get(`/branches/1`)
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
        onEditorChange({ quill, html, text }) {
            this.books = [];
            let i = '';
            let branch = `<p style="border-top:2px solid;position: absolute;bottom: 0;width: 97%;">تليفون: ${this.branch.code_country + this.branch.phone},
                ${this.branch.code_country + this.branch.second_phone} -
               فاكس: ${this.branch.fax} - ص.ب : ${this.branch.p_o_pox}
            </p>`;

            i = html + branch;
            this.items.forEach(el => {
                if(html.indexOf('{Name}') != -1 || html.indexOf('{Address}') != -1){
                    this.books.push(i.replace('{Name}',el.full_name).replace('{Address}',el.home_address));
                }else {
                    this.books.push(i)
                }
            });
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
                            <h4 class="header-title">
                                {{ $t("general.lnvitationLetterToAttendTheAssociation") }}
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

                        <div class="row justify-content-between align-items-center mb-2 px-1" >
                            <div class="col-md-3 d-flex align-items-center mb-1 mt-2 mb-xl-0">
                                <b-button
                                    v-b-modal.create
                                    variant="primary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    {{ $t('general.Search') }}
                                    <i class="fe-search"></i>
                                </b-button>
                                <b-button
                                    v-if="items.length > 0"
                                    v-b-modal.create_book
                                    variant="secondary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    {{ $t('general.bookPrint') }}
                                    <i class="fe-printer"></i>
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
                                            <b-form-checkbox v-model="setting.membership_number" class="mb-1">{{ getCompanyKey("member_membership_number") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.full_name" class="mb-1">{{ $t("general.full_name") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.first_name" class="mb-1">{{
                                                    getCompanyKey("member_first_name") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.second_name" class="mb-1">{{
                                                    getCompanyKey("member_second_name") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.third_name" class="mb-1">{{
                                                    getCompanyKey("member_third_name") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.last_name" class="mb-1">{{
                                                    getCompanyKey("member_last_name") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.family_name" class="mb-1">{{
                                                    getCompanyKey("member_family_name") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.birth_date" class="mb-1">{{ getCompanyKey("member_birth_date") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.gender" class="mb-1">{{ getCompanyKey("member_type") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.membership_date" class="mb-1">{{ getCompanyKey("member_membership_date") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.financial_status_id" class="mb-1">{{ getCompanyKey("financial_status") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.member_status_id" class="mb-1">{{ $t("general.status") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.PaymentDate" class="mb-1">{{ $t("general.PaymentDate") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.document_no" class="mb-1">{{ $t("general.ReceiptNumber") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.ForAYear" class="mb-1">{{ $t("general.ForAYear") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.national_id" class="mb-1">{{ getCompanyKey("member_national_id") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.home_phone" class="mb-1">{{ getCompanyKey("member_home_phone") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.home_address" class="mb-1">{{ getCompanyKey("member_home_address") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.work_phone" class="mb-1">{{ getCompanyKey("member_work_phone") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.job" class="mb-1">{{ getCompanyKey("member_job") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.degree" class="mb-1">{{ getCompanyKey("member_degree") }}</b-form-checkbox>

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

                        <div id="printData" class="head-branch">
                            <div class="text-center">
                                  <h2 class="d-inline-block" style="width:20%">
                                        <span v-if="branch">
                                            <template v-if="branch.media">
                                                <img :src="branch.media[0].url" width="100" />
                                            </template>
                                        </span>
                                        </h2>
                                  <div class="d-inline-block" style="width:70%">
                                        <h1 class="text-center">
                                            {{ branch.name }}
                                        </h1>
                                        <h1 class="text-center">
                                            {{ branch.name_e }}
                                         </h1>
                                  </div>
                            </div>
                            <template v-for="item in books">
                                <div v-html="item" class="content"></div>
                            </template>
                        </div>

                        <!--  book   -->
                        <b-modal
                            id="create_book"
                            :title="$t('general.execution')"
                            title-class="font-18"
                            body-class="p-4"
                            size="lg"
                            :hide-footer="true"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <template>
                                        <b-button
                                            variant="success"
                                            type="button" class="mx-1"
                                            v-if="!isLoader"
                                            v-print="'#printData'"
                                        >
                                            {{ $t('general.execution') }}
                                        </b-button>

                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                        </b-button>
                                    </template>
                                    <!-- Emulate built in modal footer ok and cancel button actions -->

                                    <b-button variant="danger" type="button" @click.prevent="$bvModal.hide('create_book')">
                                        {{ $t('general.Cancel') }}
                                    </b-button>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-md-3">
                                        <h4 class="text-center">{{ $t('general.memberName') }}</h4>
                                        <p>{{ $t('general.memberName') }}: {Name}</p>
                                        <p>{{ $t('general.memberAddress') }}: {Address}</p>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ $t('general.content') }}
                                            </label>
                                            <quill-editor
                                                @change="onEditorChange($event)"
                                                v-model="content"
                                                :options="editorOption"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /book   -->

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
                                                v-if="
                                  $v.create.cm_permission_id.$error
                                "
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

                        <!-- start .table-responsive-->
                        <div  class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table">
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader"/>
                            <!--       end loader       -->
                            <table class="table table-borderless table-hover table-centered m-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th v-if="setting.membership_number">
                                        <div class="d-flex justify-content-center">
                                            <span>{{getCompanyKey("member_membership_number")}}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString('membership_number'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-membership_number'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.full_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.full_name") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString('full_name'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-full_name'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.first_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_first_name") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="members.sort(sortString('name'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="members.sort(sortString('-name'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.second_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_second_name") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="members.sort(sortString('name_e'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="members.sort(sortString('-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.third_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_third_name") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="members.sort(sortString('name_e'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="members.sort(sortString('-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.last_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_last_name") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="members.sort(sortString('name_e'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="members.sort(sortString('-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.family_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_family_name") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="members.sort(sortString('name_e'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="members.sort(sortString('-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.birth_date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_birth_date") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString('birth_date'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-birth_date'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.national_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_national_id") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString('national_id'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-national_id'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.membership_date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_membership_date")}}</span>
                                            <div class="arrow-sort">
                                                <i  class="fas fa-arrow-up" @click="items.sort(sortString('membership_date'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-membership_date'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.PaymentDate">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.PaymentDate") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.document_no">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.ReceiptNumber") }}</span>
                                        </div>
                                    </th>
                                    <th>{{ $t('general.ManagerSignature') }}</th>
                                </tr>
                                </thead>
                                <tbody v-if="items.length > 0">
                                <tr
                                    v-for="(data, index) in items"
                                    :key="data.id"
                                    class="body-tr-custom"
                                >
                                    <td>
                                        {{ index + 1 }}
                                    </td>
                                    <td v-if="setting.membership_number">
                                        {{ data.membership_number }}
                                    </td>
                                    <td v-if="setting.full_name">
                                        {{ data.full_name }}
                                    </td>
                                    <td v-if="setting.first_name">
                                        {{ data.first_name }}
                                    </td>
                                    <td v-if="setting.second_name">
                                        {{ data.second_name }}
                                    </td>
                                    <td v-if="setting.third_name">
                                        {{ data.third_name }}
                                    </td>
                                    <td v-if="setting.last_name">
                                        {{ data.last_name }}
                                    </td>
                                    <td v-if="setting.family_name">
                                        {{ data.family_name }}
                                    </td>
                                    <td v-if="setting.birth_date">
                                        {{ data.birth_date?data.birth_date:'---' }}
                                    </td>
                                    <td v-if="setting.national_id">
                                        {{ data.national_id ? data.national_id : '---' }}
                                    </td>
                                    <td v-if="setting.membership_date">
                                        {{data.membership_date ? formatDate(data.membership_date) : '---' }}
                                    </td>
                                    <td v-if="setting.PaymentDate">
                                        {{data.transaction ? formatDate(data.transaction.date) : '---' }}
                                    </td>
                                    <td v-if="setting.document_no">
                                        {{ data.transaction ? data.transaction.document_no : '---' }}
                                    </td>
                                    <td></td>
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
.head-branch {
    display: none;
}
.colPay{
    background-color: #3bafda;
    color: #fff;
    font-weight: 500;
}
@media print {
    .colPay{
        color: #000;
    }
    .head-branch {
        margin-top: 50px;
        display: block;
    }
    .head-branch h2{
        text-decoration: underline;
    }
    .head-branch img{
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
}
.quill-editor {
    background-color: #fff;
}
.content {
    min-height: 1270px;
    padding: 20px;
    border: 2px solid;
    margin-bottom: 200px;
    position: relative;
}
</style>
