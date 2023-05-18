<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {required, minLength, maxLength, integer, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import {dynamicSortString, dynamicSortNumber} from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import {formatDateOnly} from "../../../helper/startDate";
import translation from "../../../helper/translation-mixin";
import {arabicValue, englishValue} from "../../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Settings",
        meta: [{name: "description", content: "Settings"}],
    },
    mixins: [translation],
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            if (vm.$store.state.auth.work_flow_trees.includes('club') || vm.$store.state.auth.user.type == 'super_admin') {
                return true;
            } else {
                return vm.$router.push({name: "home"});
            }
        });

    },
    data() {
        return {
            per_page: 50,
            search: "",
            debounce: {},
            settingsPagination: {},
            settings: [],
            enabled3: true,
            is_disabled: false,
            isLoader: false,
            create: {
                day: null,
                month: null,
                cm_members_type_id: null,
                cm_permissions_id: null,
                cm_financial_status_id: null,
                membership_period: "",
                allowed_subscription_date: "",
            },
            company_id: null,
            edit: {
                day: null,
                month: null,
                cm_members_type_id: null,
                cm_permissions_id: null,
                cm_financial_status_id: null,
                membership_period: "",
                allowed_subscription_date: "",
            },
            setting: {
                cm_members_type_id: true,
                cm_permissions_id: true,
                cm_financial_status_id: true,
                membership_period: true,
                allowed_subscription_date: true,
            },
            typs: [],
            permissions: [],
            status: [],
            Tooltip: "",
            mouseEnter: null,
            errors: {},
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            filterSetting: [
                "cm_members_type_id",
                "cm_permissions_id",
                "cm_financial_status_id",
                "membership_period",
                "allowed_subscription_date",
                // this.$i18n.locale == "ar" ? "country.name" : "country.name_e",
            ],
            printLoading: true,
            printObj: {
                id: "printData",
            }
        };
    },
    validations: {
        create: {
            day: {required},
            month: {required},
            cm_members_type_id: {required},
            cm_permissions_id: {required},
            cm_financial_status_id: {required},
            membership_period: {required},
            allowed_subscription_date: {},
        },
        edit: {
            day: {required},
            month: {required},
            cm_members_type_id: {required},
            cm_permissions_id: {required},
            cm_financial_status_id: {required},
            membership_period: {required},
            allowed_subscription_date: {},
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
                this.settings.forEach((el) => {
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

        this.getData();
    },
    methods: {
        resetForm() {
            this.create = {
                day: null,
                month: null,
                cm_members_type_id: null,
                cm_permissions_id: null,
                cm_financial_status_id: null,
                membership_period: "",
                allowed_subscription_date: "",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
        },
        /**
         *  start get Data countrie && pagination
         */
        getData(page = 1) {
            this.isLoader = true;
            let filter = "";
            for (let i = 0; i < this.filterSetting.length; ++i) {
                filter += `columns[${i}]=${this.filterSetting[i]}&`;
            }
            adminApi
                .get(
                    `/club-members/type-permission?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.settings = l.data;
                    this.settingsPagination = l.pagination;
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
        getDataCurrentPage(page = 1) {
            if (
                this.current_page <= this.settingsPagination.last_page &&
                this.current_page != this.settingsPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let filter = "";
                for (let i = 0; i < this.filterSetting.length; ++i) {
                    filter += `columns[${i}]=${this.filterSetting[i]}&`;
                }
                adminApi
                    .get(
                        `/club-members/type-permission?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.settings = l.data;
                        this.settingsPagination = l.pagination;
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
        /**
         *  end get Data countrie && pagination
         */
        /**
         *  start delete countrie
         */
        deleteBranch(id, index) {
            if (Array.isArray(id)) {
                Swal.fire({
                    title: `${this.$t("general.Areyousure")}`,
                    text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
                    cancelButtonText: `${this.$t("general.Nocancel")}`,
                    confirmButtonClass: "btn btn-success mt-2",
                    cancelButtonClass: "btn btn-danger ml-2 mt-2",
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.value) {
                        this.isLoader = true;
                        adminApi
                            .post(`/club-members/type-permission/bulk-delete`, {ids: id})
                            .then((res) => {
                                this.checkAll = [];
                                this.getData();
                                Swal.fire({
                                    icon: "success",
                                    title: `${this.$t("general.Deleted")}`,
                                    text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                            })
                            .catch((err) => {
                                if (err.response.status == 400) {
                                    Swal.fire({
                                        icon: "error",
                                        title: `${this.$t("general.Error")}`,
                                        text: `${this.$t("general.CantDeleteRelation")}`,
                                    });
                                    this.getData();
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: `${this.$t("general.Error")}`,
                                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                                    });
                                }
                            })
                            .finally(() => {
                                this.isLoader = false;
                            });
                    }
                });
            } else {
                Swal.fire({
                    title: `${this.$t("general.Areyousure")}`,
                    text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
                    cancelButtonText: `${this.$t("general.Nocancel")}`,
                    confirmButtonClass: "btn btn-success mt-2",
                    cancelButtonClass: "btn btn-danger ml-2 mt-2",
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.value) {
                        this.isLoader = true;

                        adminApi
                            .delete(`/club-members/type-permission/${id}`)
                            .then((res) => {
                                this.checkAll = [];
                                this.getData();
                                Swal.fire({
                                    icon: "success",
                                    title: `${this.$t("general.Deleted")}`,
                                    text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                            })

                            .catch((err) => {
                                if (err.response.status == 400) {
                                    Swal.fire({
                                        icon: "error",
                                        title: `${this.$t("general.Error")}`,
                                        text: `${this.$t("general.CantDeleteRelation")}`,
                                    });
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: `${this.$t("general.Error")}`,
                                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                                    });
                                }
                            })
                            .finally(() => {
                                this.isLoader = false;
                            });
                    }
                });
            }
        },
        /**
         *  end delete countrie
         */
        /**
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.create = {
                day: null,
                month: null,
                cm_members_type_id: null,
                cm_permissions_id: null,
                cm_financial_status_id: null,
                membership_period: "",
                allowed_subscription_date: "",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabled = false;
            this.typs = [];
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            await this.getType();
            await this.getPermissions();
            await this.getStatus();
            this.create = {
                day: null,
                month: null,
                cm_members_type_id: null,
                cm_permissions_id: null,
                cm_financial_status_id: null,
                membership_period: "",
                allowed_subscription_date: "",
            };

            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
        },
        /**
         *  create countrie
         */
        AddSubmit() {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                this.create.allowed_subscription_date = `${this.create.month}-${this.create.day}`
                adminApi
                    .post(`/club-members/type-permission`, {...this.create, company_id: this.company_id})
                    .then((res) => {
                        this.getData();
                        this.is_disabled = true;
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Addedsuccessfully")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: `${this.$t("general.Error")}`,
                                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                            });
                        }
                    })
                    .finally(() => {
                        this.isLoader = false;
                    });
            }
        },
        /**
         *  edit countrie
         */
        editSubmit(id) {
            this.$v.edit.$touch();
            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.edit.allowed_subscription_date = `${this.edit.month}-${this.edit.day}`
                adminApi
                    .put(`/club-members/type-permission/${id}`, this.edit)
                    .then((res) => {
                        this.$bvModal.hide(`modal-edit-${id}`);
                        this.getData();
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Editsuccessfully")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: `${this.$t("general.Error")}`,
                                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                            });
                        }
                    })
                    .finally(() => {
                        this.isLoader = false;
                    });
            }
        },
        /**
         *   show Modal (edit)
         */
        async resetModalEdit(id) {
            await this.getType();
            await this.getPermissions();
            await this.getStatus();
            let setting = this.settings.find((e) => id == e.id);
            this.edit.day = setting.allowed_subscription_date.slice(3) ;
            this.edit.month = setting.allowed_subscription_date.slice(0,2);
            this.edit.cm_members_type_id = setting.type.id;
            this.edit.cm_permissions_id = setting.cm_permissions_id;
            this.edit.cm_financial_status_id = setting.financialStatus.id;
            this.edit.membership_period = setting.membership_period;
            this.edit.allowed_subscription_date = setting.allowed_subscription_date;
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.errors = {};
            this.edit = {
                day: null,
                month: null,
                cm_members_type_id: null,
                cm_permissions_id: null,
                cm_financial_status_id: null,
                membership_period: "",
                allowed_subscription_date: "",
            };
            this.typs = [];
        },
        /*
         *  start  dynamicSortString
         */
        sortString(value) {
            return dynamicSortString(value);
        },
        sortNumber(value) {
            return dynamicSortNumber(value);
        },
        /**
         *  start  ckeckRow
         */
        checkRow(id) {
            if (!this.checkAll.includes(id)) {
                this.checkAll.push(id);
            } else {
                let index = this.checkAll.indexOf(id);
                this.checkAll.splice(index, 1);
            }
        },
        /**
         *  end  ckeckRow
         */

        async getType() {
            this.isLoader = true;
            await adminApi
                .get(`/club-members/members-types`)
                .then((res) => {
                    let l = res.data.data;
                    this.typs = l;
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
        async getPermissions() {
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
        async getStatus() {
            this.isLoader = true;
            await adminApi
                .get(`/club-members/financial-status`)
                .then((res) => {
                    let l = res.data.data;
                    this.status = l;
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


        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if (this.mouseEnter != id) {
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`/club-members/type-permission/logs/${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.forEach((e) => {
                            this.Tooltip += `Created By: ${e.causer_type}; Event: ${
                                e.event
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

        /**
         *   Export Excel
         */
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
                if (dl) {
                    XLSX.write(wb, {bookType: type, bookSST: true, type: 'base64'});
                } else {
                    XLSX.writeFile(wb, fn || (('settings' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },
        getDay(){
            let days = [];
            for(let i = 1;i <= 31; ++i){
                i < 10? days.push(`0${i}`) : days.push(`${i}`);
            }
            return days;
        },
        getMonth(){
            let months = [];
            for(let i = 1;i <= 12; ++i){
                i < 10? months.push(`0${i}`) : months.push(`${i}`);
            }
            return months;
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
                            <h4 class="header-title">{{ $t("general.SettingTable") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown
                                        variant="primary"
                                        :text="$t('general.searchSetting')"
                                        ref="dropdown"
                                        class="btn-block setting-search"
                                    >
                                        <b-form-checkbox v-model="filterSetting" value="membership_period"
                                                         class="mb-1"
                                        >{{ getCompanyKey("membership_period") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting"
                                                         value="allowed_subscription_date" class="mb-1"
                                        >{{ getCompanyKey("allowed_subscription_date") }}
                                        </b-form-checkbox>
                                        <!-- Basic dropdown -->
                                    </b-dropdown>
                                </div>

                                <div class="d-inline-block position-relative" style="width: 77%">
                                      <span :class="[ 'search-custom position-absolute', $i18n.locale == 'ar' ? 'search-custom-ar' : '', ]">
                                        <i class="fe-search"></i>
                                      </span>
                                    <input
                                        class="form-control"
                                        style="display: block; width: 93%; padding-top: 3px"
                                        type="text"
                                        v-model.trim="search"
                                        :placeholder="`${$t('general.Search')}...`"
                                    />
                                </div>
                            </div>
                        </div>
                        <!-- end search -->

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <!-- start create and printer -->
                                <b-button
                                    v-b-modal.create
                                    variant="primary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printData'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <button
                                        class="custom-btn-dowonload"
                                        @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                                        v-if="checkAll.length == 1"
                                    >
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="checkAll.length > 1"
                                        @click.prevent="deleteBranch(checkAll)"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="checkAll.length == 1"
                                        @click.prevent="deleteBranch(checkAll[0])"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!--  end one delete  -->
                                </div>
                                <!-- end create and printer -->
                            </div>
                            <div
                                class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end"
                            >
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
                                            <b-form-checkbox v-model="setting.cm_members_type_id"
                                                             class="mb-1"
                                            >{{ getCompanyKey("member_type") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.cm_permissions_id"
                                                             class="mb-1">
                                                {{ getCompanyKey("member_permission") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.cm_financial_status_id"
                                                             class="mb-1">
                                                {{ getCompanyKey("financial_status") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.membership_period"
                                                             class="mb-1">
                                                {{ getCompanyKey("membership_period") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.allowed_subscription_date"
                                                             class="mb-1">
                                                {{ getCompanyKey("allowed_subscription_date") }}
                                            </b-form-checkbox>
                                            <div class="d-flex justify-content-end">
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm">{{
                                                        $t("general.Apply")
                                                    }}</a>
                                            </div>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </div>
                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom">
                                        <div class="d-inline-block" style="font-size: 13px">
                                            {{ settingsPagination.from }}-{{ settingsPagination.to }} /
                                            {{ settingsPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{
                          'pointer-events':
                            settingsPagination.current_page == 1 ? 'none' : '',
                        }"
                                                @click.prevent="getData(settingsPagination.current_page - 1)"
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
                          'pointer-events':
                            settingsPagination.last_page == settingsPagination.current_page
                              ? 'none'
                              : '',
                        }"
                                                @click.prevent="getData(settingsPagination.current_page + 1)"
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
                            :title="getCompanyKey('settings_create_form')"
                            title-class="font-18"
                            body-class="p-4 "
                            size="lg"
                            :hide-footer="true"
                            @show="resetModal"
                            @hidden="resetModalHidden"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <b-button
                                        variant="success"
                                        :disabled="!is_disabled"
                                        @click.prevent="resetForm"
                                        type="button"
                                        :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
                                    >
                                        {{ $t("general.AddNewRecord") }}
                                    </b-button>
                                    <!-- Emulate built in modal footer ok and cancel button actions -->
                                    <template v-if="!is_disabled">
                                        <b-button
                                            variant="success"
                                            type="submit"
                                            class="mx-1"
                                            v-if="!isLoader"
                                            @click.prevent="AddSubmit"
                                        >
                                            {{ $t("general.Add") }}
                                        </b-button>

                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                        </b-button>
                                    </template>
                                    <b-button
                                        variant="danger"
                                        type="button"
                                        @click.prevent="$bvModal.hide('create')"
                                    >
                                        {{ $t("general.Cancel") }}
                                    </b-button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey("member_type") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                v-model="create.cm_members_type_id"
                                                :options="typs.map((type) => type.id)"
                                                :custom-label="
                                                  (opt) =>
                                                    $i18n.locale == 'ar'
                                                      ? typs.find((x) => x.id == opt).name
                                                      : typs.find((x) => x.id == opt).name_e
                                                "
                                            >
                                            </multiselect>
                                            <div
                                                v-if="$v.create.cm_members_type_id.$error || errors.cm_members_type_id"
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.cm_members_type_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.cm_members_type_id"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey("member_permission") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                multiple="true"
                                                v-model="create.cm_permissions_id"
                                                :options="permissions.map((type) => type.id)"
                                                :custom-label="
                                                  (opt) =>
                                                    $i18n.locale == 'ar'
                                                      ? permissions.find((x) => x.id == opt).name
                                                      : permissions.find((x) => x.id == opt).name_e
                                                "
                                            >
                                            </multiselect>
                                            <div
                                                v-if="$v.create.cm_permissions_id.$error || errors.cm_permissions_id"
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.cm_permissions_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.cm_permissions_id"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey("financial_status") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                v-model="create.cm_financial_status_id"
                                                :options="status.map((type) => type.id)"
                                                :custom-label="
                                                  (opt) =>
                                                    $i18n.locale == 'ar'
                                                      ? status.find((x) => x.id == opt).name
                                                      : status.find((x) => x.id == opt).name_e
                                                "
                                            >
                                            </multiselect>
                                            <div
                                                v-if="$v.create.cm_financial_status_id.$error || errors.cm_financial_status_id"
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.cm_financial_status_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.cm_financial_status_id"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("membership_period") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                v-model="$v.create.membership_period.$model"
                                                :class="{
                                                  'is-invalid': $v.create.membership_period.$error || errors.membership_period,
                                                  'is-valid':
                                                    !$v.create.membership_period.$invalid && !errors.membership_period,
                                                }"
                                            />
                                            <template v-if="errors.membership_period">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.membership_period"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>
                                                {{ getCompanyKey("allowed_subscription_date") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="d-flex">
                                                <div class="form-group col-6">
                                                    <label>{{ $t("general.day") }}</label>
                                                    <select
                                                        v-model="$v.create.day.$model"
                                                        class="custom-select"
                                                        :class="{
                                                        'is-invalid': $v.create.day.$error || errors.day,
                                                        'is-valid':
                                                          !$v.create.day.$invalid && !errors.day,
                                                      }"
                                                    >
                                                        <option selected disabled value="">Choose...</option>
                                                        <option :value="day" :key="day" v-for="day in getDay()">{{ day }}</option>
                                                    </select>
                                                    <template v-if="errors.day">
                                                        <ErrorMessage v-for="(errorMessage, index) in errors.day" :key="index">{{ errorMessage }}
                                                        </ErrorMessage>
                                                    </template>
                                                </div>
                                                <div class="form-group col-6">
                                                    <label>{{ $t("general.month") }}</label>
                                                    <select
                                                        v-model="$v.create.month.$model"
                                                        class="custom-select"
                                                        :class="{
                                                        'is-invalid': $v.create.month.$error || errors.month,
                                                        'is-valid':
                                                          !$v.create.month.$invalid && !errors.month,
                                                      }"
                                                    >
                                                        <option selected disabled value="">Choose...</option>
                                                        <option :value="month" :key="month" v-for="month in getMonth()">{{ month }}</option>
                                                    </select>
                                                    <template v-if="errors.month">
                                                        <ErrorMessage v-for="(errorMessage, index) in errors.month" :key="index">{{ errorMessage }}
                                                        </ErrorMessage>
                                                    </template>
                                                </div>
                                            </div>

                                            <template v-if="errors.allowed_subscription_date">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.allowed_subscription_date"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative">
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader"/>
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                                   id="printData">
                                <thead>
                                <tr>
                                    <th v-if="enabled3" class="do-not-print" scope="col" style="width: 0">
                                        <div class="form-check custom-control">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                v-model="isCheckAll"
                                                style="width: 17px; height: 17px"
                                            />
                                        </div>
                                    </th>
                                    <th v-if="setting.cm_members_type_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_type") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="
                                                      settings.sort(
                                                        sortString(($i18n.locale = 'ar' ? 'name' : 'name_e'))
                                                      )
                                                    "
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="
                                                      settings.sort(
                                                        sortString(($i18n.locale = 'ar' ? '-name' : '-name_e'))
                                                      )
                                                    "
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.cm_permissions_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_permission") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="
                                                      settings.sort(
                                                        sortString(($i18n.locale = 'ar' ? 'name' : 'name_e'))
                                                      )
                                                    "
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="
                                                      settings.sort(
                                                        sortString(($i18n.locale = 'ar' ? '-name' : '-name_e'))
                                                      )
                                                    "
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.cm_financial_status_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("financial_status") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="
                                                      settings.sort(
                                                        sortString(($i18n.locale = 'ar' ? 'name' : 'name_e'))
                                                      )
                                                    "
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="
                                                      settings.sort(
                                                        sortString(($i18n.locale = 'ar' ? '-name' : '-name_e'))
                                                      )
                                                    "
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.membership_period">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("membership_period") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="settings.sort(sortString('membership_period'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="settings.sort(sortString('-membership_period'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.allowed_subscription_date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("allowed_subscription_date") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="settings.sort(sortString('allowed_subscription_date'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="settings.sort(sortString('-allowed_subscription_date'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t("general.Action") }}
                                    </th>
                                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                </tr>
                                </thead>
                                <tbody v-if="settings.length > 0">
                                <tr
                                    @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                                    v-for="(data, index) in settings"
                                    :key="data.id"
                                    class="body-tr-custom"
                                >
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control" style="min-height: 1.9em">
                                            <input
                                                style="width: 17px; height: 17px"
                                                class="form-check-input"
                                                type="checkbox"
                                                :value="data.id"
                                                v-model="checkAll"
                                            />
                                        </div>
                                    </td>
                                    <td v-if="setting.cm_members_type_id">
                                        <h5 class="m-0 font-weight-normal">
                                            {{
                                                data.type ? $i18n.locale == "ar" ? data.type.name : data.type.name_e : ' - '
                                            }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.cm_permissions_id">
                                        <h5 class="m-0 font-weight-normal" v-if="data.permission.length > 0">
                                            <span  v-for="(per, index) in data.permission"
                                                   :key="per.id">
                                                 {{  $i18n.locale == "ar" ? per.name : per.name_e }} -
                                            </span>

                                        </h5>
                                    </td>
                                    <td v-if="setting.cm_financial_status_id">
                                        <h5 class="m-0 font-weight-normal">
                                            {{
                                                data.financialStatus ? $i18n.locale == "ar" ? data.financialStatus.name : data.financialStatus.name_e : ' - '
                                            }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.membership_period">
                                        <h5 class="m-0 font-weight-normal">{{ data.membership_period }}</h5>
                                    </td>
                                    <td v-if="setting.allowed_subscription_date">
                                        <h5 class="m-0 font-weight-normal">{{ data.allowed_subscription_date.slice(3) + '-' + data.allowed_subscription_date.slice(0,2) }}</h5>
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="btn-group">
                                            <button
                                                type="button"
                                                class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                data-toggle="dropdown"
                                                aria-expanded="false"
                                            >
                                                {{ $t("general.commands") }}
                                                <i class="fas fa-angle-down"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-custom">
                                                <a
                                                    class="dropdown-item"
                                                    href="#"
                                                    @click="$bvModal.show(`modal-edit-${data.id}`)"
                                                >
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black"
                                                    >
                                                        <span>{{ $t("general.edit") }}</span>
                                                        <i class="mdi mdi-square-edit-outline text-info"></i>
                                                    </div>
                                                </a>
                                                <a
                                                    class="dropdown-item text-black"
                                                    href="#"
                                                    @click.prevent="deleteBranch(data.id)"
                                                >
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black"
                                                    >
                                                        <span>{{ $t("general.delete") }}</span>
                                                        <i class="fas fa-times text-danger"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>

                                        <!--  edit   -->
                                        <b-modal
                                            :id="`modal-edit-${data.id}`"
                                            :title="getCompanyKey('settings_edit_form')"
                                            title-class="font-18"
                                            body-class="p-4"
                                            size="lg"
                                            :ref="`edit-${data.id}`"
                                            :hide-footer="true"
                                            @show="resetModalEdit(data.id)"
                                            @hidden="resetModalHiddenEdit(data.id)"
                                        >
                                            <form>
                                                <div class="mb-3 d-flex justify-content-end">
                                                    <!-- Emulate built in modal footer ok and cancel button actions -->
                                                    <b-button
                                                        variant="success"
                                                        @click.prevent="editSubmit(data.id)"
                                                        type="button"
                                                        class="mx-1 font-weight-bold px-3"
                                                        v-if="!isLoader"
                                                    >
                                                        {{ $t("general.Edit") }}
                                                    </b-button>

                                                    <b-button variant="success" class="mx-1" disabled v-else>
                                                        <b-spinner small></b-spinner>
                                                        <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                    </b-button>

                                                    <b-button
                                                        variant="danger"
                                                        class="font-weight-bold"
                                                        type="button"
                                                        @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)"
                                                    >
                                                        {{ $t("general.Cancel") }}
                                                    </b-button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group position-relative">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("member_type") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                v-model="edit.cm_members_type_id"
                                                                :options="typs.map((type) => type.id)"
                                                                :custom-label="
                                                                      (opt) =>
                                                                        $i18n.locale == 'ar'
                                                                          ? typs.find((x) => x.id == opt).name
                                                                          : typs.find((x) => x.id == opt).name_e
                                                                    "
                                                            >
                                                            </multiselect>
                                                            <div
                                                                v-if="$v.edit.cm_members_type_id.$error || errors.cm_members_type_id"
                                                                class="text-danger"
                                                            >
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.cm_members_type_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.cm_members_type_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group position-relative">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("member_permission") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                multiple="true"
                                                                v-model="edit.cm_permissions_id"
                                                                :options="permissions.map((type) => type.id)"
                                                                :custom-label="
                                                                      (opt) =>
                                                                        $i18n.locale == 'ar'
                                                                          ? permissions.find((x) => x.id == opt).name
                                                                          : permissions.find((x) => x.id == opt).name_e
                                                                    "
                                                            >
                                                            </multiselect>
                                                            <div
                                                                v-if="$v.edit.cm_permissions_id.$error || errors.cm_permissions_id"
                                                                class="text-danger"
                                                            >
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.cm_permissions_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.cm_permissions_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group position-relative">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("financial_status") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                v-model="edit.cm_financial_status_id"
                                                                :options="status.map((type) => type.id)"
                                                                :custom-label="
                                                                  (opt) =>
                                                                    $i18n.locale == 'ar'
                                                                      ? status.find((x) => x.id == opt).name
                                                                      : status.find((x) => x.id == opt).name_e
                                                                "
                                                            >
                                                            </multiselect>
                                                            <div
                                                                v-if="$v.edit.cm_financial_status_id.$error || errors.cm_financial_status_id"
                                                                class="text-danger"
                                                            >
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.cm_financial_status_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.cm_financial_status_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("membership_period") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="number"
                                                                class="form-control"
                                                                v-model="$v.edit.membership_period.$model"
                                                                :class="{
                                                                      'is-invalid': $v.edit.membership_period.$error || errors.membership_period,
                                                                      'is-valid':
                                                                        !$v.edit.membership_period.$invalid && !errors.membership_period,
                                                                    }"
                                                            />
                                                            <template v-if="errors.membership_period">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.membership_period"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>
                                                                {{ getCompanyKey("allowed_subscription_date") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="d-flex">
                                                                <div class="form-group col-6">
                                                                    <label>{{ $t("general.day") }}</label>
                                                                    <select
                                                                        v-model="$v.edit.day.$model"
                                                                        class="custom-select"
                                                                        :class="{
                                                                            'is-invalid': $v.edit.day.$error || errors.day,
                                                                            'is-valid':
                                                                              !$v.edit.day.$invalid && !errors.day,
                                                                          }"
                                                                    >
                                                                        <option selected disabled value="">Choose...</option>
                                                                        <option :value="day" :key="day" v-for="day in getDay()">{{ day }}</option>
                                                                    </select>
                                                                    <template v-if="errors.day">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors.day" :key="index">{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                                <div class="form-group col-6">
                                                                    <label>{{ $t("general.month") }}</label>
                                                                    <select
                                                                        v-model="$v.edit.month.$model"
                                                                        class="custom-select"
                                                                        :class="{
                                                                            'is-invalid': $v.edit.month.$error || errors.month,
                                                                            'is-valid':
                                                                              !$v.edit.month.$invalid && !errors.month,
                                                                          }"
                                                                    >
                                                                        <option selected disabled value="">Choose...</option>
                                                                        <option :value="month" :key="month" v-for="month in getMonth()">{{ month }}</option>
                                                                    </select>
                                                                    <template v-if="errors.month">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors.month" :key="index">{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>

                                                            <template v-if="errors.allowed_subscription_date">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.allowed_subscription_date"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </b-modal>
                                        <!--  /edit   -->
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <button
                                            @mousemove="log(data.id)"
                                            @mouseover="log(data.id)"
                                            type="button"
                                            class="btn"
                                            :id="`tooltip-${data.id}`"
                                            :data-placement="$i18n.locale == 'en' ? 'left' : 'right'"
                                            :title="Tooltip"
                                        >
                                            <i class="fe-info" style="font-size: 22px"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="11">
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
