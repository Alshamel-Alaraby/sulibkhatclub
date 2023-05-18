<script>
import DatePicker from "vue2-datepicker";
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {required, minLength, maxLength, integer, requiredIf, decimal, minValue} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import alphaArabic from "../../../helper/alphaArabic";
import alphaEnglish from "../../../helper/alphaEnglish";
import { dynamicSortString, dynamicSortNumber } from "../../../helper/tableSort";
import translation from "../../../helper/translation-mixin";
import senderHoverHelper from "../../../helper/senderHoverHelper";
import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Members",
        meta: [{ name: "description", content: "Members" }],
    },
    mixins: [translation],
    components: {
        DatePicker,
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
    },
    //   beforeRouteEnter(to, from, next) {
    //     next((vm) => {
    //       if (vm.$store.state.auth.work_flow_trees.includes('store') || vm.$store.state.auth.user.type == 'super_admin') {
    //         return true;
    //       } else {
    //         return vm.$router.push({ name: "home" });
    //       }
    //     });
    //   },
    data() {
        return {
            per_page: 50,
            search: "",
            debounce: {},
            enabled3: true,
            membersPagination: {},
            members: [],
            isLoader: false,
            Tooltip: "",
            mouseEnter: "",
            approve: {
                session_date: this.formatDate(new Date()),
                session_number: null,
                'accept-members': []
            },
            company_id: null,
            errors: {},
            date: [],
            isCheckAll: false,
            session_date: new Date(),
            checkAll: [],
            branches: [],
            current_page: 1,
            setting: {
                first_name: true,
                second_name: true,
                third_name: true,
                last_name: true,
                family_name: true,
            },
            is_disabled: false,
            filterSetting: [
                "first_name",
                "second_name",
                "third_name",
                "last_name",
                "family_name",
            ],
            reject: {notes: '',},
            printLoading: true,
            printObj: {
                id: "printData",
            }
        };
    },
    validations: {
        approve: {
            session_number: {required},
            session_date: {required},
            'accept-members':{
                required,
                $each:{
                        id: {required},
                        membership_date: {required},
                }
             }
         },
        reject: {
            notes: {required},
        }
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
                this.members.forEach((el) => {
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
                    `/club-members/members/pending?page=${page}&per_page=${this.per_page}&company_id=${this.company_id}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.members = l.data;
                    this.membersPagination = l.pagination;
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
                this.current_page <= this.membersPagination.last_page &&
                this.current_page != this.membersPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let filter = "";
                for (let i = 0; i < this.filterSetting.length; ++i) {
                    filter += `columns[${i}]=${this.filterSetting[i]}&`;
                }

                adminApi
                    .get(
                        `/club-members/members/pending?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}&company_id=${this.company_id}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.members = l.data;
                        this.membersPagination = l.pagination;
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
        acceptSubmit(id) {
            this.approve["accept-members"].forEach((el,index) => {
                this.approve["accept-members"][index]['session_date'] = this.approve.session_date;
                this.approve["accept-members"][index]['session_number'] = this.approve.session_number;
            });
            this.$v.approve.$touch();
            if (this.$v.approve.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .post(`/club-members/members/bulk-update`, {"accept-members":this.approve["accept-members"]})
                    .then((res) => {
                        this.$bvModal.hide(`modal_accept`);
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
        rejectSubmit() {
            this.$v.reject.$touch();
            if (this.$v.reject.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .put(`/club-members/members/decline-member/${this.checkAll[0]}`, {notes: this.reject.note})
                    .then((res) => {
                        this.$bvModal.hide(`modal_rejected`);
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
         *   show Modal (Accept)
         */
        resetModalAccept() {

            this.session_date = new Date();
            this.date = [];

            this.approve = {
                session_number: "",
                session_date: this.formatDate(new Date()),
                "accept-members": []
            };
            this.checkAll.forEach(id => {
                let member = this.members.find(el => el.id == id);
                this.approve["accept-members"].push({
                    id: id,
                    membership_date: this.formatDate(new Date()),
                });

                this.date.push({
                    membership_date: new Date(),
                    name: `${member.first_name ?? ''} ${member.second_name ?? ''} ${member.third_name ?? ''} ${member.last_name ?? ''} ${member.family_name ?? ''}`
                });
            });

            this.checkAll = [];
            this.errors = {};
        },
        resetModalReject(){
            this.reject = {
                notes: ''
            }
        },
        /**
         *  hidden Modal (Accept)
         */
        resetModalHiddenAccept() {
            this.session_date = new Date();
            this.date = [];

            this.approve = {
                session_number: "",
                session_date: this.formatDate(new Date()),
                "accept-members": []
            };

            this.checkAll = [];
            this.errors = {};
        },
        /*
         *  start  dynamicSortString
         */
        sortString(value) {
            return dynamicSortString(value);
        },
        SortNumber(value) {
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

        /**
         *   Export Excel
         */
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
                if (dl) {
                    XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' });
                } else {
                    XLSX.writeFile(wb, fn || (('Stores' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if (this.mouseEnter != id) {
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`/club-members/members/logs/${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.forEach((e) => {
                            this.Tooltip += `Created By: ${e.causer_type}; Event: ${e.event
                            }; Description: ${e.description} ;Created At: ${this.formatDate(
                                e.created_at
                            )} \n`;
                        });
                    })
                    .catch((err) => {
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                        });
                    });
            } else {
            }
        },
        v_dateEdit(e, name) {
            if (e) {
                this.approve[name] = formatDateOnly(e);
            } else {
                this.approve[name] = null;
            }
        },
        v_dateEditIndex(e, name,index) {
            if (e) {
                this.approve['accept-members'][index][name] = formatDateOnly(e);
            } else {
                this.approve['accept-members'][index][name] = null;
            }
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
                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ $t("general.accept-reject-member-table") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting" value="first_name" class="mb-1">{{
                                            getCompanyKey("member_first_name") }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="second_name" class="mb-1">{{
                                            getCompanyKey("member_second_name") }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="third_name" class="mb-1">{{
                                            getCompanyKey("member_third_name") }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="last_name" class="mb-1">{{
                                            getCompanyKey("member_last_name") }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="family_name" class="mb-1">{{
                                            getCompanyKey("member_family_name") }}</b-form-checkbox>
                                    </b-dropdown>
                                    <!-- Basic dropdown -->
                                </div>

                                <div class="d-inline-block position-relative" style="width: 77%">
                                    <span :class="[
                                        'search-custom position-absolute',
                                        $i18n.locale == 'ar' ? 'search-custom-ar' : '',
                                    ]">
                                        <i class="fe-search"></i>
                                    </span>
                                    <input class="form-control" style="display: block; width: 93%; padding-top: 3px"
                                           type="text" v-model.trim="search" :placeholder="`${$t('general.Search')}...`" />
                                </div>
                            </div>
                        </div>
                        <!-- end search -->

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <!-- start create and printer -->
                                <b-button v-b-modal.modal_accept variant="primary" class="btn-sm mx-1 font-11  font-weight-bold"
                                          v-if="checkAll.length > 0"
                                >
                                    {{ $t("general.Approve") }}
                                </b-button>
                                <b-button v-b-modal.modal_rejected variant="danger" class="btn-sm mx-1 font-11 font-weight-bold"
                                          v-if="checkAll.length == 1"
                                >
                                    {{ $t("general.non-Approve") }}
                                </b-button>
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printData'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
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
                                        <b-dropdown variant="primary"
                                                    :html="`${$t('general.setting')} <i class='fe-settings'></i>`" ref="dropdown"
                                                    class="dropdown-custom-ali">
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
                                            <div class="d-flex justify-content-end">
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm">Apply</a>
                                            </div>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </div>
                                    <!-- end filter and setting -->

                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom">
                                        <div class="d-inline-block" style="font-size: 13px">
                                            {{ membersPagination.from }}-{{ membersPagination.to }} /
                                            {{ membersPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a href="javascript:void(0)" :style="{
                                                'pointer-events':
                                                    membersPagination.current_page == 1 ? 'none' : '',
                                            }" @click.prevent="getData(membersPagination.current_page - 1)">
                                                <span>&lt;</span>
                                            </a>
                                            <input type="text" @keyup.enter="getDataCurrentPage()" v-model="current_page"
                                                   class="pagination-current-page" />
                                            <a href="javascript:void(0)" :style="{
                                                'pointer-events':
                                                    membersPagination.last_page == membersPagination.current_page
                                                        ? 'none'
                                                        : '',
                                            }" @click.prevent="getData(membersPagination.current_page + 1)">
                                                <span>&gt;</span>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>

                        <!--  accept   -->
                        <b-modal id="modal_accept" :title="getCompanyKey('member_request_create_accept')" title-class="font-18"
                                 size="lg" body-class="p-4 " :hide-footer="true" @show="resetModalAccept"
                                 @hidden="resetModalHiddenAccept">
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <template v-if="!is_disabled">
                                        <b-button variant="success" type="submit" class="mx-1" v-if="!isLoader"
                                                  @click.prevent="acceptSubmit">
                                            {{ $t("general.Add") }}
                                        </b-button>
                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                        </b-button>

                                    </template>
                                    <b-button @click.prevent="$bvModal.hide(`modal_accept`)" variant="danger" type="button">
                                        {{ $t("general.Cancel") }}
                                    </b-button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("member_session_date") }}

                                            </label>
                                            <date-picker type="date" v-model="session_date" defaultValue
                                                         @change="v_dateEdit($event, 'session_date')" confirm></date-picker>
                                            <template v-if="errors.session_date">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.session_date" :key="index">{{
                                                        errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("member_membership_number") }}</label>
                                            <input v-model="$v.approve.session_number.$model" class="form-control"
                                                   type="number" :class="{
                                                    'is-invalid':
                                                        $v.approve.session_number.$error || errors.session_number,
                                                    'is-valid': !$v.approve.session_number.$invalid && !errors.session_number,
                                                }" />
                                            <template v-if="errors.session_number">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.session_number"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <template v-for="(item, index) in approve['accept-members']">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ $t('general.name') }}</label>
                                                <input v-model="date[index].name" class="form-control"
                                                      disabled type="text"
                                                    />
                                            </div>
                                        </div>
                                        <div class="col-md-4 position-relative">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ getCompanyKey("member_membership_date") }}

                                                </label>
                                                <date-picker type="date" v-model="date[index].membership_date" defaultValue
                                                             @change="v_dateEditIndex($event, 'membership_date',index)" confirm></date-picker>
                                                <template v-if="errors.membership_date">
                                                    <ErrorMessage v-for="(errorMessage, index) in errors.membership_date" :key="index">{{
                                                            errorMessage }}
                                                    </ErrorMessage>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <!--  accept   -->
                        <b-modal id="modal_rejected" :title="getCompanyKey('member_request_create_reject')" title-class="font-18"
                                  body-class="p-4 " :hide-footer="true" @show="resetModalReject"
                                 >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <template v-if="!is_disabled">
                                        <b-button variant="success" type="submit" class="mx-1" v-if="!isLoader"
                                                  @click.prevent="rejectSubmit">
                                            {{ $t("general.Add") }}
                                        </b-button>
                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                        </b-button>

                                    </template>
                                    <b-button @click.prevent="$bvModal.hide(`modal_rejected`)" variant="danger" type="button">
                                        {{ $t("general.Cancel") }}
                                    </b-button>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("boardRent_panel_note") }}</label>
                                            <input v-model="$v.reject.notes.$model" class="form-control"
                                                   type="text" :class="{
                                                    'is-invalid':
                                                        $v.reject.notes.$error || errors.notes,
                                                    'is-valid': !$v.reject.notes.$invalid && !errors.notes,
                                                }" />
                                            <template v-if="errors.notes">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.notes"
                                                              :key="index">{{ errorMessage }}
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
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                                   id="printData">
                                <thead>
                                <tr>
                                    <th v-if="enabled3" class="do-not-print" scope="col" style="width: 0">
                                        <div class="form-check custom-control">
                                            <input class="form-check-input" type="checkbox" v-model="isCheckAll"
                                                   style="width: 17px; height: 17px" />
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
                                </tr>
                                </thead>
                                <tbody v-if="members.length > 0">
                                <tr @click.capture="checkRow(data.id)"
                                    v-for="(data, index) in members" :key="data.id"
                                    class="body-tr-custom"
                                >
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control" style="min-height: 1.9em">
                                            <input style="width: 17px; height: 17px" class="form-check-input"
                                                   type="checkbox" :value="data.id" v-model="checkAll" />
                                        </div>
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
                        </div>
                        <!-- end .table-responsive-->
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style>
@media print {
    .do-not-print {
        display: none;
    }

    .arrow-sort {
        display: none;
    }

    .text-success {
        background-color: unset;
        color: #6c757d !important;
        border: unset;
    }

    .text-danger {
        background-color: unset;
        color: #6c757d !important;
        border: unset;
    }
}
</style>
