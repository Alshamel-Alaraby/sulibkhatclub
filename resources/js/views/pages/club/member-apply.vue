<script>
import DatePicker from "vue2-datepicker";
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import { required, minLength, maxLength, integer, requiredIf } from "vuelidate/lib/validators";
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
            fields: [],
            per_page: 50,
            search: "",
            debounce: {},
            enabled3: true,
            membersPagination: {},
            members: [],
            isLoader: false,
            Tooltip: "",
            mouseEnter: "",
            applying_date: new Date(),
            birth_date: new Date(),
            create: {
                applying_date: this.formatDate(new Date()),
                applying_number: '',
                first_name: "",
                second_name: "",
                third_name: "",
                phone_code: '',
                last_name: "",
                family_name: "",
                birth_date: this.formatDate(new Date()),
                national_id: "",
                nationality_number: "",
                home_phone: "",
                work_phone: "",
                home_address: "",
                work_address: "",
                job: "",
                degree: "",
                acceptance: "0",
                gender: 1
            },
            edit: {
                applying_date: this.formatDate(new Date()),
                applying_number: '',
                first_name: "",
                second_name: "",
                phone_code: '',
                third_name: "",
                last_name: "",
                family_name: "",
                birth_date: this.formatDate(new Date()),
                national_id: "",
                nationality_number: "",
                home_phone: "",
                work_phone: "",
                home_address: "",
                work_address: "",
                job: "",
                degree: "",
                acceptance: "0",
                gender: 1
            },
            company_id: null,
            errors: {},
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            setting: {
                gender: true,
                first_name: true,
                second_name: true,
                third_name: true,
                last_name: true,
                family_name: true,
                status_id: true,
                birth_date: true,
                national_id: true,
                nationality_number: true,
                home_phone: true,
                work_phone: true,
                home_address: true,
                work_address: true,
                job: true,
                degree: true,
            },
            is_disabled: false,
            filterSetting: [
                "first_name",
                "second_name",
                "third_name",
                "last_name",
                "family_name",
                "birth_date",
                "national_id",
                "nationality_number",
                "home_phone",
                "work_phone",
                "home_address",
                "work_address",
                "job",
                "degree",
            ],
            codeCountry: '',
            printLoading: true,
            printObj: {
                id: "printData",
            }
        };
    },
    validations: {
        create: {
            first_name: { required },
            second_name: { required },
            gender: { required },
            third_name: { required },
            last_name: { required },
            family_name: { required },
            birth_date: { required },
            national_id: { required },
            nationality_number: { required },
            home_phone: { required },
            work_phone: { required },
            home_address: { required },
            work_address: { required },
            job: { required },
            degree: { required },
            applying_date: { required },
            applying_number: { required },
        },
        edit: {
            first_name: { required },
            second_name: { required },
            gender: { required },
            third_name: { required },
            last_name: { required },
            family_name: { required },
            birth_date: { required },
            national_id: { required },
            nationality_number: { required },
            home_phone: { required },
            work_phone: { required },
            home_address: { required },
            work_address: { required },
            job: { required },
            degree: { required },
            applying_date: { required },
            applying_number: { required },
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
        this.$store.dispatch('locationIp/getIp');
    },
    methods: {
        v_dateCreate(e, name) {
            if (e) {
                this.create[name] = formatDateOnly(e);
            } else {
                this.create[name] = null;
            }
        },
        v_dateEdit(e, name) {
            if (e) {
                this.edit[name] = formatDateOnly(e);
            } else {
                this.edit[name] = null;
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
                    `/club-members/members?page=${page}&per_page=${this.per_page}&company_id=${this.company_id}&search=${this.search}&${filter}`
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
                        `/club-members/members?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}&company_id=${this.company_id}`
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
                            .post(`/club-members/members/bulk-delete`, { ids: id })
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
                            .delete(`/club-members/members/${id}`)
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
            this.birth_date = new Date();
            this.applying_date = new Date();
            this.create = {
                applying_date: this.formatDate(new Date()),
                applying_number: '',
                first_name: "",
                phone_code: '',
                second_name: "",
                third_name: "",
                last_name: "",
                family_name: "",
                birth_date: this.formatDate(new Date()),
                national_id: "",
                nationality_number: "",
                home_phone: "",
                work_phone: "",
                home_address: "",
                work_address: "",
                job: "",
                degree: "",
                acceptance: "0",
                gender: 1
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.$bvModal.hide(`create`);
        },
        /**
         *  hidden Modal (create)
         */
        resetModal() {
            this.birth_date = new Date();
            this.applying_date = new Date();
            this.codeCountry =  this.$store.getters["locationIp/countryCode"];
            this.create = {
                applying_date: this.formatDate(new Date()),
                applying_number: '',
                first_name: "",
                phone_code: '',
                second_name: "",
                third_name: "",
                last_name: "",
                family_name: "",
                birth_date: this.formatDate(new Date()),
                national_id: "",
                nationality_number: "",
                home_phone: "",
                work_phone: "",
                home_address: "",
                work_address: "",
                job: "",
                degree: "",
                acceptance: "0",
                gender: 1
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  create countrie
         */
         resetForm() {
            this.birth_date = new Date();
            this.applying_date = new Date();
            this.codeCountry =  this.$store.getters["locationIp/countryCode"];
            this.create = {
                applying_date: this.formatDate(new Date()),
                applying_number: '',
                phone_code: '',
                first_name: "",
                second_name: "",
                third_name: "",
                last_name: "",
                family_name: "",
                birth_date: this.formatDate(new Date()),
                national_id: "",
                nationality_number: "",
                home_phone: "",
                work_phone: "",
                home_address: "",
                work_address: "",
                job: "",
                degree: "",
                acceptance: "0",
                gender: 1
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
        },

        AddSubmit() {
            this.$v.create.$touch();
            this.create.phone_code = this.codeCountry;

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .post(`/club-members/members`, this.create )
                    .then((res) => {
                        this.is_disabled = true;
                        this.getData();
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
                adminApi
                    .put(`/club-members/members/${id}`, this.edit)
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
        resetModalEdit(id) {
            let member = this.members.find((e) => id == e.id);
            this.birth_date = new Date(member.birth_date);
            this.applying_date = new Date(member.applying_date);
            this.edit.applying_number = member.applying_number;
            this.edit.first_name = member.first_name;
            this.edit.second_name = member.second_name;
            this.edit.phone_code = member.phone_code;
            this.edit.third_name = member.third_name;
            this.edit.last_name = member.last_name;
            this.edit.family_name = member.family_name;
            this.edit.birth_date = member.birth_date;
            this.edit.applying_date = member.applying_date;
            this.edit.national_id = member.national_id;
            this.edit.nationality_number = member.nationality_number;
            this.edit.home_phone = member.home_phone;
            this.edit.work_phone = member.work_phone;
            this.edit.home_address = member.home_address;
            this.edit.work_address = member.work_address;
            this.edit.job = member.job;
            this.edit.degree = member.degree;
            this.edit.gender = member.gender;
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.birth_date = new Date();
            this.applying_date = new Date();
            this.errors = {};
            this.edit = {
                applying_date: this.formatDate(new Date()),
                applying_number: '',
                phone_code: '',
                first_name: "",
                second_name: "",
                third_name: "",
                last_name: "",
                family_name: "",
                birth_date: this.formatDate(new Date()),
                national_id: "",
                nationality_number: "",
                home_phone: "",
                work_phone: "",
                home_address: "",
                work_address: "",
                job: "",
                degree: "",
                acceptance: "0",
                gender: 1
            };
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
        arabicValue(txt) {
            this.create.name = arabicValue(txt);
            this.edit.name = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.name_e = englishValue(txt);
            this.edit.name_e = englishValue(txt);
        },
        updatePhoneEdit(e) {
            this.codeCountry = e.countryCode;
            this.edit.phone_code = e.countryCode;
            this.create.phone_code = e.countryCode;
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
                            <h4 class="header-title">{{ $t("general.membersRequestTable") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                        class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting" value="first_name" class="mb-1">{{
                                            getCompanyKey("member_first_name") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="second_name" class="mb-1">{{
                                            getCompanyKey("member_second_name") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="third_name" class="mb-1">{{
                                            getCompanyKey("member_third_name") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="last_name" class="mb-1">{{
                                            getCompanyKey("member_last_name") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="family_name" class="mb-1">{{
                                            getCompanyKey("member_family_name") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="status_id" class="mb-1">{{
                                            getCompanyKey("status") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="national_id" class="mb-1">{{
                                            getCompanyKey("member_national_id") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="nationality_number" class="mb-1">{{
                                            getCompanyKey("member_nationality_number") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="home_phone" class="mb-1">{{
                                            getCompanyKey("member_home_phone") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="work_phone" class="mb-1">{{
                                            getCompanyKey("member_work_phone") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="home_address" class="mb-1">{{
                                            getCompanyKey("member_home_address") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="work_address" class="mb-1">{{
                                            getCompanyKey("member_work_address") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="job" class="mb-1">{{
                                            getCompanyKey("member_job") }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="degree" class="mb-1">{{
                                            getCompanyKey("member_degree") }}</b-form-checkbox>

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
                                <b-button v-b-modal.create variant="primary" class="btn-sm mx-1 font-weight-bold">
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
                                    <button class="custom-btn-dowonload" @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                                        v-if="checkAll.length == 1">
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length > 1"
                                        @click.prevent="deleteBranch(checkAll)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length == 1"
                                        @click.prevent="deleteBranch(checkAll[0])">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!--  end one delete  -->
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
                                            <b-form-checkbox v-model="setting.birth_date" class="mb-1">{{
                                                getCompanyKey("member_birth_date") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.gender" class="mb-1">{{
                                                getCompanyKey("member_type") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.national_id" class="mb-1">{{
                                                getCompanyKey("member_national_id") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.nationality_number" class="mb-1">{{
                                                getCompanyKey("member_nationality_number") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.home_phone" class="mb-1">{{
                                                getCompanyKey("member_home_phone") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.work_phone" class="mb-1">{{
                                                getCompanyKey("member_work_phone") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.home_address" class="mb-1">{{
                                                getCompanyKey("member_home_address") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.work_address" class="mb-1">{{
                                                getCompanyKey("member_work_address") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.job" class="mb-1">{{
                                                getCompanyKey("member_job") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.degree" class="mb-1">{{
                                                getCompanyKey("member_degree") }}
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

                        <!--  create   -->
                        <b-modal id="create" :title="getCompanyKey('member_request_create_form')" title-class="font-18"
                            dialog-class="modal-full-width" body-class="p-4 " :hide-footer="true" @show="resetModal"
                            @hidden="resetModalHidden">
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <b-button variant="success" :disabled="!is_disabled" @click.prevent="resetForm"
                                        type="button" :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                                        {{ $t("general.AddNewRecord") }}
                                    </b-button>
                                    <template v-if="!is_disabled">
                                        <b-button variant="success" type="submit" class="mx-1" v-if="!isLoader"
                                            @click.prevent="AddSubmit">
                                            {{ $t("general.Add") }}
                                        </b-button>
                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                        </b-button>

                                    </template>
                                    <b-button @click.prevent="$bvModal.hide(`create`)" variant="danger" type="button">
                                        {{ $t("general.Cancel") }}
                                    </b-button>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 position-relative">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("apply_membership_date") }}

                                            </label>
                                            <date-picker type="date" v-model="applying_date" defaultValue
                                                         @change="v_dateCreate($event, 'applying_date')" confirm></date-picker>
                                            <template v-if="errors.applying_date">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.applying_date" :key="index">{{
                                                        errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("apply_membership_number") }}</label>
                                            <input v-model="$v.create.applying_number.$model" class="form-control"
                                                   type="number" :class="{
                                                    'is-invalid':
                                                        $v.create.applying_number.$error || errors.applying_number,
                                                    'is-valid': !$v.create.applying_number.$invalid && !errors.applying_number,
                                                }" />
                                            <template v-if="errors.applying_number">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.applying_number"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <hr  style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("member_first_name") }}</label>
                                            <input v-model="$v.create.first_name.$model" class="form-control" type="text"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.first_name.$error || errors.first_name,
                                                    'is-valid': !$v.create.first_name.$invalid && !errors.first_name,
                                                }" />
                                            <template v-if="errors.first_name">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.first_name"
                                                    :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("member_second_name") }}</label>
                                            <input v-model="$v.create.second_name.$model" class="form-control" type="text"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.second_name.$error || errors.second_name,
                                                    'is-valid': !$v.create.second_name.$invalid && !errors.second_name,
                                                }" />
                                            <template v-if="errors.second_name">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.second_name"
                                                    :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("member_third_name") }}</label>
                                            <input v-model="$v.create.third_name.$model" class="form-control" type="text"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.third_name.$error || errors.third_name,
                                                    'is-valid': !$v.create.third_name.$invalid && !errors.third_name,
                                                }" />
                                            <template v-if="errors.third_name">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.third_name"
                                                    :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("member_last_name") }}</label>
                                            <input v-model="$v.create.last_name.$model" class="form-control" type="text"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.last_name.$error || errors.last_name,
                                                    'is-valid': !$v.create.last_name.$invalid && !errors.last_name,
                                                }" />
                                            <template v-if="errors.last_name">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.third_name"
                                                    :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("member_family_name") }}</label>
                                            <input v-model="$v.create.family_name.$model" class="form-control" type="text"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.family_name.$error || errors.family_name,
                                                    'is-valid': !$v.create.family_name.$invalid && !errors.family_name,
                                                }" />
                                            <template v-if="errors.family_name">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.family_name"
                                                    :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <hr  style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                <div class="row">
                                    <div class="col-md-3 position-relative">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("member_birth_date") }}

                                            </label>
                                            <date-picker type="date" v-model="birth_date" defaultValue
                                                @change="v_dateCreate($event, 'birth_date')" confirm></date-picker>
                                            <template v-if="errors.date">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.date" :key="index">
                                                    {{
                                                        errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="mr-2">
                                                {{ getCompanyKey("member_gender") }}

                                            </label>
                                            <b-form-group :class="{
                                                'is-invalid': $v.create.gender.$error || errors.gender,
                                                'is-valid': !$v.create.gender.$invalid && !errors.gender,
                                            }">
                                                <b-form-radio class="d-inline-block"
                                                              v-model="$v.create.gender.$model" name="create_gender"
                                                              value="1">{{ $t("general.male") }}
                                                </b-form-radio>
                                                <b-form-radio class="d-inline-block m-1"
                                                              v-model="$v.create.gender.$model" name="create_gender"
                                                              value="0">{{ $t("general.female") }}
                                                </b-form-radio>
                                            </b-form-group>
                                            <template v-if="errors.gender">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.gender"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("member_national_id") }}</label>
                                            <input v-model="$v.create.national_id.$model" class="form-control" type="number"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.national_id.$error || errors.national_id,
                                                    'is-valid': !$v.create.national_id.$invalid && !errors.national_id,
                                                }" />
                                            <template v-if="errors.national_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.national_id"
                                                    :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("member_nationality_number") }}</label>
                                            <input v-model="$v.create.nationality_number.$model" class="form-control"
                                                type="number" :class="{
                                                    'is-invalid':
                                                        $v.create.nationality_number.$error || errors.nationality_number,
                                                    'is-valid': !$v.create.nationality_number.$invalid && !errors.nationality_number,
                                                }" />
                                            <template v-if="errors.nationality_number">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.nationality_number"
                                                    :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <hr  style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("member_home_phone") }}</label>
                                            <VuePhoneNumberInput
                                                v-model="$v.create.home_phone.$model"
                                                :default-country-code="codeCountry"
                                                valid-color="#28a745"
                                                error-color="#dc3545"
                                                :preferred-countries="['FR', 'EG', 'DE']"
                                                @update="updatePhoneEdit"
                                            />
                                            <template v-if="errors.home_phone">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.home_phone"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("member_home_address") }}</label>
                                            <input v-model="$v.create.home_address.$model" class="form-control" type="text"
                                                   :class="{
                                                    'is-invalid':
                                                        $v.create.home_address.$error || errors.home_address,
                                                    'is-valid': !$v.create.home_address.$invalid && !errors.home_address,
                                                }" />
                                            <template v-if="errors.home_address">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.home_address"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("member_work_phone") }}</label>
                                            <VuePhoneNumberInput
                                                v-model="$v.create.work_phone.$model"
                                                :default-country-code="codeCountry"
                                                valid-color="#28a745"
                                                error-color="#dc3545"
                                                :preferred-countries="['FR', 'EG', 'DE']"
                                                @update="updatePhoneEdit"
                                            />
                                            <template v-if="errors.work_phone">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.work_phone"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("member_work_address") }}</label>
                                            <input v-model="$v.create.work_address.$model" class="form-control" type="text"
                                                   :class="{
                                                    'is-invalid':
                                                        $v.create.work_address.$error || errors.work_address,
                                                    'is-valid': !$v.create.work_address.$invalid && !errors.work_address,
                                                }" />
                                            <template v-if="errors.work_address">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.work_address"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <hr  style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("member_job") }}</label>
                                            <input v-model="$v.create.job.$model" class="form-control" type="text" :class="{
                                                'is-invalid':
                                                    $v.create.job.$error || errors.job,
                                                'is-valid': !$v.create.job.$invalid && !errors.job,
                                            }" />
                                            <template v-if="errors.job">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.job" :key="index">{{
                                                        errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("member_degree") }}</label>
                                            <input v-model="$v.create.degree.$model" class="form-control" type="text"
                                                   :class="{
                                                    'is-invalid':
                                                        $v.create.degree.$error || errors.degree,
                                                    'is-valid': !$v.create.degree.$invalid && !errors.degree,
                                                }" />
                                            <template v-if="errors.degree">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.degree" :key="index">{{
                                                        errorMessage }}
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
                                        <th v-if="setting.birth_date">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("member_birth_date") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up"
                                                        @click="members.sort(sortString('birth_date'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="members.sort(sortString('-birth_date'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.gender">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("member_gender") }}</span>
                                            </div>
                                        </th>
                                        <th v-if="setting.national_id">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("member_national_id") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up"
                                                        @click="members.sort(sortString('national_id'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="members.sort(sortString('-national_id'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.nationality_number">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("member_nationality_number") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up"
                                                        @click="members.sort(sortString('nationality_number'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="members.sort(sortString('-nationality_number'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.home_phone">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("member_home_phone") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up"
                                                        @click="members.sort(sortString('home_phone'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="members.sort(sortString('-home_phone'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.work_phone">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("member_work_phone") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up"
                                                        @click="members.sort(sortString('work_phone'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="members.sort(sortString('-work_phone'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.home_address">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("member_home_address") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up"
                                                        @click="members.sort(sortString('home_address'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="members.sort(sortString('-home_address'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.work_address">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("member_work_address") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up"
                                                        @click="members.sort(sortString('work_address'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="members.sort(sortString('-work_address'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.job">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("member_job") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up" @click="members.sort(sortString('job'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="members.sort(sortString('-job'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.degree">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("member_degree") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up"
                                                        @click="members.sort(sortString('degree'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="members.sort(sortString('-degree'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.acceptance">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("member_acceptance") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up"
                                                        @click="members.sort(sortString('acceptance'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="members.sort(sortString('-acceptance'))"></i>
                                                </div>
                                            </div>
                                        </th>

                                        <th v-if="enabled3" class="do-not-print">
                                            {{ $t("general.Action") }}
                                        </th>
                                        <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                    </tr>
                                </thead>
                                <tbody v-if="members.length > 0">
                                    <tr @click.capture="checkRow(data.id)"
                                        @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                                        v-for="(data, index) in members" :key="data.id" class="body-tr-custom">
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
                                        <td v-if="setting.birth_date">
                                            {{ data.birth_date }}
                                        </td>
                                        <td v-if="setting.gender">
                                            {{ data.gender == 1? $t('general.male') : $t('general.female')  }}
                                        </td>
                                        <td v-if="setting.national_id">
                                            {{ data.national_id }}
                                        </td>
                                        <td v-if="setting.nationality_number">
                                            {{ data.nationality_number }}
                                        </td>
                                        <td v-if="setting.home_phone">
                                            {{ data.home_phone }}
                                        </td>
                                        <td v-if="setting.work_phone">
                                            {{ data.work_phone }}
                                        </td>
                                        <td v-if="setting.home_address">
                                            {{ data.home_address }}
                                        </td>
                                        <td v-if="setting.work_address">
                                            {{ data.work_address }}
                                        </td>
                                        <td v-if="setting.job">
                                            {{ data.job }}
                                        </td>
                                        <td v-if="setting.degree">
                                            {{ data.degree }}
                                        </td>
                                        <td v-if="setting.acceptance">
                                            <span class="text-success">
                                                <template v-if="data.acceptance == '0'">
                                                    {{ $t("general.pending") }}
                                                </template>
                                                <template v-else-if="data.acceptance == '1'">
                                                    {{ $t("general.accepted") }}
                                                </template>
                                                <template v-else="data.acceptance=='2'">
                                                    {{ $t("general.declined") }}
                                                </template>
                                            </span>
                                        </td>
                                        <td v-if="enabled3" class="do-not-print">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                    {{ $t("general.commands") }}
                                                    <i class="fas fa-angle-down"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-custom">
                                                    <a class="dropdown-item" href="#"
                                                        @click="$bvModal.show(`modal-edit-${data.id}`)">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center text-black">
                                                            <span>{{ $t("general.edit") }}</span>
                                                            <i class="mdi mdi-square-edit-outline text-info"></i>
                                                        </div>
                                                    </a>
                                                    <a class="dropdown-item text-black" href="#"
                                                        @click.prevent="deleteBranch(data.id)">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center text-black">
                                                            <span>{{ $t("general.delete") }}</span>
                                                            <i class="fas fa-times text-danger"></i>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>

                                            <!--  edit   -->
                                            <b-modal dialog-class="modal-full-width" :id="`modal-edit-${data.id}`"
                                                :title="getCompanyKey('member_request_edit_form')" title-class="font-18"
                                                body-class="p-4" :ref="`edit-${data.id}`" :hide-footer="true"
                                                @show="resetModalEdit(data.id)" @hidden="resetModalHiddenEdit(data.id)">
                                                <form>
                                                    <div class="mb-3 d-flex justify-content-end">
                                                        <!-- Emulate built in modal footer ok and cancel button actions -->
                                                        <b-button variant="success" type="submit" class="mx-1"
                                                            v-if="!isLoader" @click.prevent="editSubmit(data.id)">
                                                            {{ $t("general.Edit") }}
                                                        </b-button>

                                                        <b-button variant="success" class="mx-1" disabled v-else>
                                                            <b-spinner small></b-spinner>
                                                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                        </b-button>

                                                        <b-button variant="danger" type="button"
                                                            @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)">
                                                            {{ $t("general.Cancel") }}
                                                        </b-button>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3 position-relative">
                                                            <div class="form-group">
                                                                <label class="control-label">
                                                                    {{ getCompanyKey("apply_membership_date") }}

                                                                </label>
                                                                <date-picker type="date" v-model="applying_date" defaultValue
                                                                             @change="v_dateEdit($event, 'applying_date')" confirm></date-picker>
                                                                <template v-if="errors.applying_date">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.applying_date" :key="index">{{
                                                                            errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ getCompanyKey("apply_membership_number") }}</label>
                                                                <input v-model="$v.edit.applying_number.$model" class="form-control"
                                                                       type="number" :class="{
                                                                                'is-invalid':
                                                                                    $v.edit.applying_number.$error || errors.applying_number,
                                                                                'is-valid': !$v.edit.applying_number.$invalid && !errors.applying_number,
                                                                            }" />
                                                                <template v-if="errors.applying_number">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.applying_number"
                                                                                  :key="index">{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr  style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>{{ getCompanyKey("member_first_name") }}</label>
                                                                <input v-model="$v.edit.first_name.$model" class="form-control" type="text"
                                                                       :class="{
                                                                            'is-invalid':
                                                                                $v.edit.first_name.$error || errors.first_name,
                                                                            'is-valid': !$v.edit.first_name.$invalid && !errors.first_name,
                                                                        }" />
                                                                <template v-if="errors.first_name">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.first_name"
                                                                                  :key="index">{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>{{ getCompanyKey("member_second_name") }}</label>
                                                                <input v-model="$v.edit.second_name.$model" class="form-control" type="text"
                                                                       :class="{
                                                    'is-invalid':
                                                        $v.edit.second_name.$error || errors.second_name,
                                                    'is-valid': !$v.edit.second_name.$invalid && !errors.second_name,
                                                }" />
                                                                <template v-if="errors.second_name">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.second_name"
                                                                                  :key="index">{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>{{ getCompanyKey("member_third_name") }}</label>
                                                                <input v-model="$v.edit.third_name.$model" class="form-control" type="text"
                                                                       :class="{
                                                    'is-invalid':
                                                        $v.edit.third_name.$error || errors.third_name,
                                                    'is-valid': !$v.edit.third_name.$invalid && !errors.third_name,
                                                }" />
                                                                <template v-if="errors.third_name">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.third_name"
                                                                                  :key="index">{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>{{ getCompanyKey("member_last_name") }}</label>
                                                                <input v-model="$v.edit.last_name.$model" class="form-control" type="text"
                                                                       :class="{
                                                    'is-invalid':
                                                        $v.edit.last_name.$error || errors.last_name,
                                                    'is-valid': !$v.edit.last_name.$invalid && !errors.last_name,
                                                }" />
                                                                <template v-if="errors.last_name">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.third_name"
                                                                                  :key="index">{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>{{ getCompanyKey("member_family_name") }}</label>
                                                                <input v-model="$v.edit.family_name.$model" class="form-control" type="text"
                                                                       :class="{
                                                    'is-invalid':
                                                        $v.edit.family_name.$error || errors.family_name,
                                                    'is-valid': !$v.edit.family_name.$invalid && !errors.family_name,
                                                }" />
                                                                <template v-if="errors.family_name">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.family_name"
                                                                                  :key="index">{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr  style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                    <div class="row">
                                                        <div class="col-md-3 position-relative">
                                                            <div class="form-group">
                                                                <label class="control-label">
                                                                    {{ getCompanyKey("member_birth_date") }}

                                                                </label>
                                                                <date-picker type="date" v-model="birth_date" defaultValue
                                                                             @change="v_dateEdit($event, 'birth_date')" confirm></date-picker>
                                                                <template v-if="errors.date">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.date" :key="index">
                                                                        {{
                                                                            errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="mr-2">
                                                                    {{ getCompanyKey("member_gender") }}

                                                                </label>
                                                                <b-form-group :class="{
                                                'is-invalid': $v.edit.gender.$error || errors.gender,
                                                'is-valid': !$v.edit.gender.$invalid && !errors.gender,
                                            }">
                                                                    <b-form-radio class="d-inline-block"
                                                                                  v-model="$v.edit.gender.$model" name="edit_gender"
                                                                                  value="1">{{ $t("general.male") }}
                                                                    </b-form-radio>
                                                                    <b-form-radio class="d-inline-block m-1"
                                                                                  v-model="$v.edit.gender.$model" name="edit_gender"
                                                                                  value="0">{{ $t("general.female") }}
                                                                    </b-form-radio>
                                                                </b-form-group>
                                                                <template v-if="errors.gender">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.gender"
                                                                                  :key="index">{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ getCompanyKey("member_national_id") }}</label>
                                                                <input v-model="$v.edit.national_id.$model" class="form-control" type="number"
                                                                       :class="{
                                                    'is-invalid':
                                                        $v.edit.national_id.$error || errors.national_id,
                                                    'is-valid': !$v.edit.national_id.$invalid && !errors.national_id,
                                                }" />
                                                                <template v-if="errors.national_id">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.national_id"
                                                                                  :key="index">{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ getCompanyKey("member_nationality_number") }}</label>
                                                                <input v-model="$v.edit.nationality_number.$model" class="form-control"
                                                                       type="number" :class="{
                                                    'is-invalid':
                                                        $v.edit.nationality_number.$error || errors.nationality_number,
                                                    'is-valid': !$v.edit.nationality_number.$invalid && !errors.nationality_number,
                                                }" />
                                                                <template v-if="errors.nationality_number">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.nationality_number"
                                                                                  :key="index">{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr  style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ getCompanyKey("member_home_phone") }}</label>
                                                                <VuePhoneNumberInput
                                                                    v-model="$v.edit.home_phone.$model"
                                                                    :default-country-code="edit.phone_code"
                                                                    valid-color="#28a745"
                                                                    error-color="#dc3545"
                                                                    @update="updatePhoneEdit"
                                                                    :preferred-countries="['FR', 'EG', 'DE']"
                                                                />
                                                                <template v-if="errors.home_phone">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.home_phone"
                                                                                  :key="index">{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ getCompanyKey("member_home_address") }}</label>
                                                                <input v-model="$v.edit.home_address.$model" class="form-control" type="text"
                                                                       :class="{
                                                    'is-invalid':
                                                        $v.edit.home_address.$error || errors.home_address,
                                                    'is-valid': !$v.edit.home_address.$invalid && !errors.home_address,
                                                }" />
                                                                <template v-if="errors.home_address">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.home_address"
                                                                                  :key="index">{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ getCompanyKey("member_work_phone") }}</label>
                                                                <VuePhoneNumberInput
                                                                    v-model="$v.edit.work_phone.$model"
                                                                    :default-country-code="edit.phone_code"
                                                                    valid-color="#28a745"
                                                                    error-color="#dc3545"
                                                                    @update="updatePhoneEdit"
                                                                    :preferred-countries="['FR', 'EG', 'DE']"
                                                                />
                                                                <template v-if="errors.work_phone">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.work_phone"
                                                                                  :key="index">{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ getCompanyKey("member_work_address") }}</label>
                                                                <input v-model="$v.edit.work_address.$model" class="form-control" type="text"
                                                                       :class="{
                                                    'is-invalid':
                                                        $v.edit.work_address.$error || errors.work_address,
                                                    'is-valid': !$v.edit.work_address.$invalid && !errors.work_address,
                                                }" />
                                                                <template v-if="errors.work_address">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.work_address"
                                                                                  :key="index">{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr  style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ getCompanyKey("member_job") }}</label>
                                                                <input v-model="$v.edit.job.$model" class="form-control" type="text" :class="{
                                                'is-invalid':
                                                    $v.edit.job.$error || errors.job,
                                                'is-valid': !$v.edit.job.$invalid && !errors.job,
                                            }" />
                                                                <template v-if="errors.job">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.job" :key="index">{{
                                                                            errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ getCompanyKey("member_degree") }}</label>
                                                                <input v-model="$v.edit.degree.$model" class="form-control" type="text"
                                                                       :class="{
                                                    'is-invalid':
                                                        $v.edit.degree.$error || errors.degree,
                                                    'is-valid': !$v.edit.degree.$invalid && !errors.degree,
                                                }" />
                                                                <template v-if="errors.degree">
                                                                    <ErrorMessage v-for="(errorMessage, index) in errors.degree" :key="index">{{
                                                                            errorMessage }}
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
                                            <button @mouseover="log(data.id)" @mousemove="log(data.id)" type="button"
                                                class="btn" data-toggle="tooltip"
                                                :data-placement="$i18n.locale == 'en' ? 'left' : 'right'" :title="Tooltip">
                                                <i class="fe-info" style="font-size: 22px"></i>
                                            </button>
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
