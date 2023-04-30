<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import { required, minLength, maxLength ,integer, numeric, email } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import {dynamicSortNumber, dynamicSortString} from "../../../helper/tableSort";
import {formatDateOnly} from "../../../helper/startDate";
import Country from "../../../components/country";
import City from "../../../components/city";
import bankAccount from "../../../components/create/bankAccount";
import Multiselect from "vue-multiselect";
import translation from "../../../helper/translation-mixin";
import {arabicValue,englishValue} from "../../../helper/langTransform";
import axios from "axios";
// require styles
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
/**
 * Advanced Table component
 */

export default {
    page: {
        title: "Owners",
        meta: [{ name: "description", content: 'Owners' }],
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {

                    if (vm.$store.state.auth.work_flow_trees.includes("real estate-e")) {
        Swal.fire({
          icon: "error",
          title: `${vm.$t("general.Error")}`,
          text: `${vm.$t("general.ModuleExpired")}`,
        });
        return vm.$router.push({ name: "home" });
      }

            if (vm.$store.state.auth.work_flow_trees.includes('owners')  || vm.$store.state.auth.work_flow_trees.includes('real estate') || vm.$store.state.auth.user.type == 'super_admin') {
                return true;
            } else {
                return vm.$router.push({ name: "home" });
            }
        });
    },
    mixins:[translation],
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Country,
        City,
        Multiselect,
        bankAccount
    },
    data() {
        return {
            per_page: 50,
            search: '',
            debounce: {},
            isVaildPhone: false,
            ownersPagination: {},
            owners: [],
            cities: [],
            countries: [],
            bank_accounts: [],
            nationalities: [],
            isLoader: false,
            codeCountry:"",
            create: {
                name: '',
                name_e: '',
                phone_code: '',
                phone: '',
                email: '',
                rb_code: null,
                nationality_id: null,
                contact_person: '',
                contact_phones:'',
                national_id: null,
                bank_account_id: null,
                country_id: null,
                city_id: null ,
                whatsapp: ''
            },
            edit: {
                name: '',
                name_e: '',
                phone: '',
                email: '',
                phone_code: '',
                rb_code: null,
                nationality_id: null,
                contact_person: '',
                contact_phones:'',
                national_id: null,
                bank_account_id: null,
                country_id: null,
                city_id: null ,
                whatsapp: ''
            },
            errors: {},
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            image: '',
            setting: {
                name: true,
                name_e: true,
                phone : true,
                email : true,
                rb_code: true,
                nationality_id: true,
                contact_person: true,
                contact_phones: true,
                national_id:true,
                bank_account_id: true,
                country_id: true ,
                city_id: true,
                whatsapp: true
            },
            is_disabled: false,
            filterSetting: [
                'name',
                'name_e',
                'phone',
                'email',
                'rb_code',
                'nationality_id',
                'contact_person',
                'contact_phones',
                'national_id',
                'country_id',
                'city_id',
                'bank_account_id',
                'whatsapp',
            ],
            Tooltip: '',
            mouseEnter: null,
            printLoading: true,
            enabled3: true,
            printObj: {
                id: "printOwner",
            }
        }
    },
    validations: {
        create: {
            name: {required,minLength: minLength(2),maxLength: maxLength(100),},
            name_e: {required,minLength: minLength(2),maxLength: maxLength(100),},
            phone: {required,maxLength: maxLength(100)},
            email: {maxLength: maxLength(100),email},
            rb_code: {required,integer,maxLength: maxLength(9),},
            nationality_id: {required,integer,maxLength: maxLength(40),},
            contact_person: {maxLength: maxLength(100)},
            contact_phones: {integer,maxLength: maxLength(100)},
            national_id: {required,integer},
            country_id: {required},
            city_id: {required},
            bank_account_id: {required},
            whatsapp: {integer}
        },
        edit: {
            name: {required,minLength: minLength(2),maxLength: maxLength(100),},
            name_e: {required,minLength: minLength(2),maxLength: maxLength(100),},
            phone: {required,maxLength: maxLength(100)},
            email: {maxLength: maxLength(100),email},
            rb_code: {required,integer,maxLength: maxLength(9),},
            nationality_id: {required,integer,maxLength: maxLength(40),},
            contact_person: {maxLength: maxLength(100)},
            contact_phones: {integer,maxLength: maxLength(100)},
            national_id: {required,integer},
            country_id: {required},
            city_id: {required},
            bank_account_id: {required},
            whatsapp: {integer}
        },
    },
    watch: {
        /**
         * watch per_page
         */
        per_page(after,befour){
            this.getData();
        },
        /**
         * watch search
         */
        search(after,befour){
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.getData();
            }, 400);
        },
        /**
         * watch check All table
         */
        isCheckAll(after,befour){
            if(after){
                this.builds.forEach(el => {
                    if(!this.checkAll.includes(el.id)){
                        this.checkAll.push(el.id);
                    }
                });
            }else{
                this.checkAll = [];
            }
        }
    },
    mounted() {
        this.getData();
        this.$store.dispatch('locationIp/getIp');

    },
    computed: {
    },
    methods: {
        arabicValue(txt) {
      this.create.name = arabicValue(txt);
      this.edit.name = arabicValue(txt);
    },
        englishValue(txt) {
      this.create.name_e = englishValue(txt);
      this.edit.name_e = englishValue(txt);
    },

        /**
         *  start get Data owners && pagination
         */
        getData(page = 1){
            this.isLoader = true;
            let filter = '';
            for (let i = 0; i < this.filterSetting.length; ++i) {
                filter += `columns[${i}]=${this.filterSetting[i]}&`;
            }

            adminApi.get(`/real-estate/owners?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
                .then((res) => {
                    let l = res.data;
                    this.owners = l.data;
                    this.ownersPagination = l.pagination;
                    this.current_page = l.pagination.current_page;
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
        },
        getDataCurrentPage(page = 1){
            if(this.current_page <= this.ownersPagination.last_page && this.current_page != this.ownersPagination.current_page && this.current_page){
                this.isLoader = true;
                let filter = '';
                for (let i = 0; i < this.filterSetting.length; ++i) {
                    filter += `columns[${i}]=${this.filterSetting[i]}&`;
                }

                adminApi.get(`/real-estate/owners?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
                    .then((res) => {
                        let l = res.data;
                        this.owners = l.data;
                        this.ownersPagination = l.pagination;
                        this.current_page = l.pagination.current_page;
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
        /**
         *  end get Data countrie && pagination
         */
        /**
         *  start delete countrie
         */
        deleteCountry(id, index) {
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
                            .post(`/real-estate/owners/bulk-delete`, { ids: id })
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
                            .delete(`/real-estate/owners/${id}`)
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
        resetModalHidden(){
            this.create =  {
                name: '',
                name_e: '',
                phone: '',
                email: '',
                phone_code: '',
                rb_code: null,
                nationality_id: null,
                contact_person: '',
                contact_phones:'',
                national_id: null,
                bank_account_id: null,
                country_id: null,
                city_id: null ,
                whatsapp: ''
            };
            this.$nextTick(() => { this.$v.$reset() });
            this.errors = {};
            this.$bvModal.hide(`create`);
            this.isVaildPhone = false;
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal(){
            await this.getCategory();
            await this.getBankAcount();
            this.codeCountry = this.$store.getters["locationIp/countryCode"];
            this.create = {
                name: '',
                name_e: '',
                phone: '',
                email: '',
                phone_code: '',
                rb_code: null,
                nationality_id: null,
                contact_person: '',
                contact_phones:'',
                national_id: null,
                bank_account_id: null,
                country_id: null,
                city_id: null ,
                whatsapp: ''
            };
            this.$nextTick(() => { this.$v.$reset() });
            this.errors = {};
            this.isVaildPhone = false;
        },
        /**
         *  create countrie
         */
        async resetForm(){
            await this.getCategory();
            await this.getBankAcount();
            this.isVaildPhone = false;
            this.create = {
                name: '',
                name_e: '',
                phone: '',
                email: '',
                rb_code: null,
                nationality_id: null,
                phone_code: '',
                contact_person: '',
                contact_phones:'',
                national_id: null,
                bank_account_id: null,
                country_id: null,
                city_id: null ,
                whatsapp: ''
            };
            this.codeCountry = this.$store.getters["locationIp/countryCode"];

            this.$nextTick(() => { this.$v.$reset() });
            this.errors = {};
            this.is_disabled = false;
        },

        AddSubmit(){

            if(!this.create.name){ this.create.name = this.create.name_e}
            if(!this.create.name_e){ this.create.name_e = this.create.name}

            this.$v.create.$touch();

            if (this.$v.create.$invalid && !this.isVaildPhone) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                adminApi.post(`/real-estate/owners`,this.create)
                    .then((res) => {
                        this.is_disabled = true;
                        this.getData();
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                text: `${this.$t('general.Addedsuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },500);
                    })
                    .catch((err) => {
                        if(err.response.data){
                            this.errors = err.response.data.errors;
                        }else {
                            Swal.fire({
                                icon: 'error',
                                title: `${this.$t('general.Error')}`,
                                text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                            });
                        }
                    }).finally(() => {
                    this.isLoader = false;
                });
            }

        },
        /**
         *  edit countrie
         */
        editSubmit(id){

            if(!this.edit.name){ this.edit.name = this.edit.name_e}
            if(!this.edit.name_e){ this.edit.name_e = this.edit.name}
            this.$v.edit.$touch();

            if (this.$v.edit.$invalid && !this.isVaildPhone) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                adminApi.put(`/real-estate/owners/${id}`,this.edit)
                    .then((res) => {
                        this.$bvModal.hide(`modal-edit-${id}`);
                        this.getData();
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                text: `${this.$t('general.Editsuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },500);
                    })
                    .catch((err) => {
                        if(err.response.data){
                            this.errors = err.response.data.errors;
                        }else {
                            Swal.fire({
                                icon: 'error',
                                title: `${this.$t('general.Error')}`,
                                text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                            });
                        }
                    }).finally(() => {
                    this.isLoader = false;
                });
            }
        },
        /**
         *   show Modal (edit)
         */
        async resetModalEdit(id){
            let build = this.owners.find(e => id == e.id );
            await this.getCategory();
            await this.getCity(build.country.id);
            await this.getBankAcount();
            this.edit.name = build.name;
            this.edit.name_e = build.name_e;
            this.edit.phone_code =  build.phone_code;
            this.edit.phone = build.phone;
            this.edit.city_id = build.city.id;
            this.edit.country_id = build.country.id;
            this.edit.bank_account_id = build.ban1k_account.id
            this.edit.nationality_id = build.nationality.id ;
            this.edit.email = build.email;
            this.edit.rb_code = build.rb_code;
            this.edit.contact_person = build.contact_person;
            this.edit.whatsapp = build.whatsapp;
            this.edit.contact_phones = build.contact_phones;
            this.edit.national_id = build.national_id;
            this.errors = {};
            this.isVaildPhone = false;
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id){
            this.errors = {};
            this.edit = {
                name: '',
                name_e: '',
                phone_code: '',
                phone: '',
                email: '',
                rb_code: null,
                nationality_id: null,
                contact_person: '',
                contact_phones:'',
                national_id: null,
                bank_account_id: null,
                country_id: null,
                city_id: null ,
                whatsapp: ''
            };
            this.isVaildPhone = false;
        },
        /**
         *  start  dynamicSortString
         */
        sortString(value){ return dynamicSortString(value);},
        sortNumber(value){return dynamicSortNumber(value);},
        /**
         *  start  ckeckRow
         */
        checkRow(id){
            if(!this.checkAll.includes(id)) {
                this.checkAll.push(id);
            }else {
                let index = this.checkAll.indexOf(id);
                this.checkAll.splice(index,1);
            }
        },
        /**
         *  end  ckeckRow
         */
        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if(this.mouseEnter != id){
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`/real-estate/owners/logs/${id}`)
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
        async getCategory() {
            this.create.city_id = null;
            this.edit.city_id = null;
            await adminApi
                .get(`/countries?is_active=active`)
                .then((res) => {
                    let l = res.data.data;
                    this.nationalities = l;
                    l.unshift({ id: 0, name: "اضافة دولة", name_e: "Add Country" });
                    this.countries = l;
                    this.nationalities = l.slice(1);
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getBankAcount() {
            await adminApi.get(`/bank-accounts`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, account_number: "Add Bank Account" });
                    this.bank_accounts = l;

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
        },
        async getCity(id = null) {
            if (this.edit.city_id  == 0 || this.create.city_id == 0) {
                this.$bvModal.show("city-create");
                this.create.city_id = null;
                this.edit.city_id = null;
            }
            if (id) {
                this.create.city_id = null;
                this.edit.city_id = null;
                await adminApi
                    .get(`/cities?country_id=${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.unshift({ id: 0, name: "اضافة مدينة", name_e: "Add City" });
                        this.cities = l;
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
        showCountryModal() {
            if (this.create.country_id == 0) {
                this.$bvModal.show("country-create");
                this.create.country_id = null;
            }else {
                this.getCity(this.create.country_id);
            }
        },
        showCountryModalEdit() {
            if (this.edit.country_id == 0) {
                this.$bvModal.show("country-create");
                this.edit.country_id = null;
            }else {
                this.getCity(this.edit.country_id);
            }
        },
        showBankAccountModal() {
            if (this.create.bank_account_id == 0) {
                this.$bvModal.show("bank-account-create");
                this.create.bank_account_id = null;
            }
        },
        showBankAccountEdit() {
            if (this.edit.bank_account_id == 0) {
                this.$bvModal.show("bank-account-create");
                this.edit.bank_account_id = null;
            }
        },
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
                if (dl) {
                    XLSX.write(wb, {bookType: type, bookSST: true, type: 'base64'});
                } else {
                    XLSX.writeFile(wb, fn || (('Document Department' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },
        updatePhone(e) {
            this.create.phone = e.phoneNumber;
            this.create.phone_code = e.countryCallingCode;
            this.isVaildPhone = e.isValid;
        },
        updateWhatsapp(e){
            this.create.whatsapp = e.phoneNumber;
        },
        updateWhatsappEdit(e){
            this.edit.whatsapp = e.phoneNumber;
        },
        updatePhoneEdit(e) {
            this.edit.phone = e.phoneNumber;
            this.edit.phone_code = e.countryCallingCode;
            this.isVaildPhone = e.isValid;
        },
        updateContract(e){
            this.create.contact_phones = e.phoneNumber;
        },
        updateContractEdit(e){
            this.edit.contact_phones = e.phoneNumber;
        },
    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <bankAccount :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getBankAcount" />
        <Country :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCategory" />
        <City :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCity(create.country_id ? create.country_id: edit.country_id)" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title"> {{ $t('general.ownersTable') }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%;">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown" class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting" value="name" class="mb-1">{{ getCompanyKey("owner_name_ar") }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="name_e" class="mb-1">{{ getCompanyKey('owner_name_en') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="phone" class="mb-1">{{ getCompanyKey('owner_phone') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="email" class="mb-1">{{ getCompanyKey('owner_email') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="rb_code" class="mb-1">{{ getCompanyKey('owner_code') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="nationality_id" class="mb-1">{{ getCompanyKey('owner_nationality') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="contact_person" class="mb-1">{{ getCompanyKey('owner_contact_person') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="contact_phones" class="mb-1">{{ getCompanyKey('owner_contact_phones') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="country_id" class="mb-1">{{ getCompanyKey('country') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="city_id" class="mb-1">{{ getCompanyKey('city') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="national_id" class="mb-1">{{ getCompanyKey('owner_national_id') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="bank_account_id" class="mb-1">{{ getCompanyKey('bank_account') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="whatsapp" class="mb-1">{{ getCompanyKey('owner_whatsapp') }}</b-form-checkbox>
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
                                <b-button
                                    v-b-modal.create
                                    variant="primary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    {{ $t('general.Create') }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printOwner'" class="custom-btn-dowonload">
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
                                        @click.prevent="deleteCountry(checkAll)"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="checkAll.length == 1"
                                        @click.prevent="deleteCountry(checkAll[0])"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!--  end one delete  -->
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
                                        <b-dropdown
                                            variant="primary"
                                            :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                                            ref="dropdown" class="dropdown-custom-ali dropdown-menu-custom-company"
                                        >
                                            <b-form-checkbox v-model="setting.name" class="mb-1">{{getCompanyKey("owner_name_ar") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.name_e" class="mb-1">{{ getCompanyKey('owner_name_en') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.phone" class="mb-1">{{getCompanyKey('owner_phone') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.email" class="mb-1">{{ getCompanyKey('owner_email') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.rb_code" class="mb-1">{{getCompanyKey('owner_code') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.contact_person" class="mb-1">{{ getCompanyKey('owner_contact_person') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.contact_phones" class="mb-1">{{getCompanyKey('owner_contact_phones') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.nationality_id" class="mb-1">{{ getCompanyKey('owner_nationality') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.national_id" class="mb-1">{{ getCompanyKey('owner_national_id') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.bank_account_id" class="mb-1">{{getCompanyKey('bank_account') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.country_id" class="mb-1">{{getCompanyKey('country') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.city_id" class="mb-1">{{ getCompanyKey('city') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.whatsapp" class="mb-1">{{ getCompanyKey('owner_whatsapp') }}</b-form-checkbox>
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
                                            {{ ownersPagination.from }}-{{ ownersPagination.to }} / {{ ownersPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{'pointer-events':ownersPagination.current_page == 1 ? 'none': ''}"
                                                @click.prevent="getData(ownersPagination.current_page - 1)"
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
                                                :style="{'pointer-events':ownersPagination.last_page == ownersPagination.current_page ? 'none': ''}"
                                                @click.prevent="getData(ownersPagination.current_page + 1)"
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
                            :title="getCompanyKey('owner_create_form')"
                            title-class="font-18"
                            dialog-class="modal-full-width"
                            body-class="p-4 "
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
                                        type="button" :class="['font-weight-bold px-2',is_disabled?'mx-2': '']"
                                    >
                                        {{ $t('general.AddNewRecord') }}
                                    </b-button>
                                    <template v-if="!is_disabled">
                                        <b-button
                                            variant="success"
                                            type="button" class="mx-1"
                                            v-if="!isLoader"
                                            @click.prevent="AddSubmit"
                                        >
                                            {{ $t('general.Add') }}
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">
                                                {{ getCompanyKey("owner_name_ar") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div dir="rtl">
                                                <input
                                                    @keyup="arabicValue(create.name)"
                                                    type="text"
                                                    class="form-control"
                                                    v-model="$v.create.name.$model"
                                                    :class="{
                                                    'is-invalid':$v.create.name.$error || errors.name,
                                                    'is-valid':!$v.create.name.$invalid && !errors.name
                                                }"
                                                    id="field-1"
                                                />
                                            </div>
                                            <div v-if="!$v.create.name.minLength" class="invalid-feedback">{{ $t('general.Itmustbeatleast') }} {{ $v.create.name.$params.minLength.min }} {{ $t('general.letters') }}</div>
                                            <div v-if="!$v.create.name.maxLength" class="invalid-feedback">{{ $t('general.Itmustbeatmost') }}  {{ $v.create.name.$params.maxLength.max }} {{ $t('general.letters') }}</div>
                                            <template v-if="errors.name">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.name" :key="index">{{ errorMessage }}</ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">
                                                {{ getCompanyKey('owner_name_en') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div dir="ltr">
                                                <input
                                                    @keyup="englishValue(create.name_e)"
                                                    type="text"
                                                    class="form-control"
                                                    v-model="$v.create.name_e.$model"
                                                    :class="{
                                                        'is-invalid':$v.create.name_e.$error || errors.name_e,
                                                        'is-valid':!$v.create.name_e.$invalid && !errors.name_e
                                                    }"
                                                    id="field-2"
                                                />
                                            </div>
                                            <div v-if="!$v.create.name_e.minLength" class="invalid-feedback">{{ $t('general.Itmustbeatleast') }} {{ $v.create.name_e.$params.minLength.min }} {{ $t('general.letters') }}</div>
                                            <div v-if="!$v.create.name_e.maxLength" class="invalid-feedback">{{ $t('general.Itmustbeatmost') }}  {{ $v.create.name_e.$params.maxLength.max }} {{ $t('general.letters') }}</div>
                                            <template v-if="errors.name_e">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.name_e" :key="index">{{ errorMessage }}</ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey('owner_nationality') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                v-model="$v.create.nationality_id.$model"
                                                :options="nationalities.map((type) => type.id)"
                                                :custom-label="(opt) => nationalities.find((x) => x.id == opt).name"
                                            >
                                            </multiselect>
                                            <div
                                                v-if="$v.create.nationality_id.$error || errors.nationality_id"
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.nationality_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.nationality_id"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey('country') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                @input="showCountryModal"
                                                v-model="$v.create.country_id.$model"
                                                :options="countries.map((type) => type.id)"
                                                :custom-label="(opt) => countries.find((x) => x.id == opt).name"
                                            >
                                            </multiselect>
                                            <div
                                                v-if="$v.create.country_id.$error || errors.country_id"
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.country_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.country_id"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey('city') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                @input="getCity()"
                                                v-model="$v.create.city_id.$model"
                                                :options="cities.map((type) => type.id)"
                                                :custom-label="(opt) => cities.find((x) => x.id == opt).name"
                                            >
                                            </multiselect>
                                            <div
                                                v-if="$v.create.city_id.$error || errors.city_id"
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.city_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.city_id"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label  class="control-label">
                                                {{ getCompanyKey('owner_national_id') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                step="0.1"
                                                v-model="$v.create.national_id.$model"
                                                :class="{
                                                'is-invalid':$v.create.national_id.$error || errors.national_id,
                                                'is-valid':!$v.create.national_id.$invalid && !errors.national_id
                                            }"
                                            />
                                            <template v-if="errors.national_id">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.national_id" :key="index">{{ errorMessage }}</ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey('bank_account') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                @input="showBankAccountModal"
                                                v-model="$v.create.bank_account_id.$model"
                                                :options="bank_accounts.map((type) => type.id)"
                                                :custom-label="(opt) => bank_accounts.find((x) => x.id == opt).account_number"
                                            >
                                            </multiselect>
                                            <div
                                                v-if="$v.create.bank_account_id.$error || errors.bank_account_id"
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.bank_account_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.bank_account_id"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label  class="control-label">
                                                {{ getCompanyKey('owner_code') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                v-model="$v.create.rb_code.$model"
                                                :class="{
                                                'is-invalid':$v.create.rb_code.$error || errors.rb_code,
                                                'is-valid':!$v.create.rb_code.$invalid && !errors.rb_code
                                            }"
                                            />
                                            <template v-if="errors.rb_code">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.rb_code" :key="index">{{ errorMessage }}</ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey('owner_phone') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <VuePhoneNumberInput
                                                v-model="$v.create.phone.$model"
                                                :default-country-code="codeCountry"
                                                valid-color="#28a745"
                                                error-color="#dc3545"
                                                :preferred-countries="['FR', 'EG', 'DE']"
                                                @update="updatePhone"
                                            />
                                            <template v-if="errors.phone">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.phone" :key="index">{{ errorMessage }}</ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey('owner_whatsapp') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <VuePhoneNumberInput
                                                v-model="$v.create.whatsapp.$model"
                                                :default-country-code="codeCountry"
                                                valid-color="#28a745"
                                                error-color="#dc3545"
                                                :preferred-countries="['FR', 'EG', 'DE']"
                                                @update="updateWhatsapp"
                                            />
                                            <template v-if="errors.whatsapp">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.whatsapp" :key="index">{{ errorMessage }}</ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey('owner_email') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="$v.create.email.$model"
                                                :class="{
                                                'is-invalid':$v.create.email.$error || errors.email,
                                                'is-valid':!$v.create.email.$invalid && !errors.email
                                            }"
                                            />
                                            <template v-if="errors.email">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.email" :key="index">{{ errorMessage }}</ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey('owner_contact_person') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="$v.create.contact_person.$model"
                                                :class="{
                                                'is-invalid':$v.create.contact_person.$error || errors.contact_person,
                                                'is-valid':!$v.create.contact_person.$invalid && !errors.contact_person
                                            }"
                                            />
                                            <template v-if="errors.contact_person">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.contact_person" :key="index">{{ errorMessage }}</ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label  class="control-label">
                                                {{ getCompanyKey('owner_contact_phones') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <VuePhoneNumberInput
                                                v-model="$v.create.contact_phones.$model"
                                                :default-country-code="codeCountry"
                                                valid-color="#28a745"
                                                error-color="#dc3545"
                                                :preferred-countries="['FR', 'EG', 'DE']"
                                                @update="updateContract"
                                            />
                                            <template v-if="errors.contact_phones">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.contact_phones" :key="index">{{ errorMessage }}</ErrorMessage>
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
                                   id="printOwner">
                                <thead>
                                <tr>
                                    <th scope="col" style="width: 0;" v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control">
                                            <input
                                                class="form-check-input"
                                                type="checkbox" v-model="isCheckAll"
                                                style="width: 17px;height: 17px;"
                                            >
                                        </div>
                                    </th>
                                    <th v-if="setting.name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("owner_name_ar") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="owners.sort(sortString('name'))"></i>
                                                <i class="fas fa-arrow-down" @click="owners.sort(sortString('-name'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.name_e">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('owner_name_en') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="owners.sort(sortString('name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="owners.sort(sortString('-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.phone">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('owner_phone') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="owners.sort(sortNumber('phone'))"></i>
                                                <i class="fas fa-arrow-down" @click="owners.sort(sortNumber('-phone'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.email">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('owner_email') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="owners.sort(sortString('email'))"></i>
                                                <i class="fas fa-arrow-down" @click="owners.sort(sortString('-email'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.nationality_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('owner_nationality') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="owners.sort(SortNumber('nationality'))"></i>
                                                <i class="fas fa-arrow-down" @click="owners.sort(SortNumber('-nationality'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.national_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('owner_national_id') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="owners.sort(sortNumber('national_id'))"></i>
                                                <i class="fas fa-arrow-down" @click="owners.sort(sortNumber('-national_id'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.contact_person">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('owner_contact_person') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="owners.sort(sortString('contact_person'))"></i>
                                                <i class="fas fa-arrow-down" @click="owners.sort(sortString('-contact_person'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.contact_phones">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('owner_contact_phones') }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.country_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('country') }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.city_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('city') }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.bank_account_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('bank_account') }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.whatsapp">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('owner_whatsapp') }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.rb_code">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('owner_code') }}</span>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t('general.Action') }}
                                    </th>
                                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                </tr>
                                </thead>
                                <tbody v-if="owners.length > 0">
                                <tr
                                    @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                                    v-for="(data,index) in owners"
                                    :key="data.id"
                                    class="body-tr-custom"
                                >
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control" style="min-height: 1.9em;">
                                            <input
                                                style="width: 17px;height: 17px;"
                                                class="form-check-input"
                                                type="checkbox"
                                                v-model="checkAll"
                                                :value="data.id"
                                            >
                                        </div>
                                    </td>
                                    <td v-if="setting.name">
                                        <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                                    </td>
                                    <td v-if="setting.name_e">
                                        <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
                                    </td>
                                    <td v-if="setting.phone">{{ data.phone ? '+'+ data.phone_code + data.phone : '-' }}</td>
                                    <td v-if="setting.email">{{ data.email }}</td>
                                    <td v-if="setting.nationality_id">{{ $i18n.locale == 'ar' ? data.nationality.name : data.nationality.name_e }}</td>
                                    <td v-if="setting.national_id">{{ data.national_id }}</td>
                                    <td v-if="setting.contact_person">{{ data.contact_person }}</td>
                                    <td v-if="setting.contact_phones">{{ data.contact_phones ? '+'+ data.phone_code + data.contact_phones : '-' }}</td>
                                    <td v-if="setting.country_id">{{ $i18n.locale == 'ar' ? data.country.name : data.country.name_e }}</td>
                                    <td v-if="setting.city_id">{{ $i18n.locale == 'ar' ? data.city.name : data.city.name_e }}</td>
                                    <td v-if="setting.bank_account_id">{{  data.ban1k_account.account_number }}</td>
                                    <td v-if="setting.whatsapp">{{ data.whatsapp ? '+'+data.phone_code + data.whatsapp : '-'}}</td>
                                    <td v-if="setting.rb_code">{{ data.rb_code }}</td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="btn-group">
                                            <button
                                                type="button"
                                                class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                data-toggle="dropdown"
                                                aria-expanded="false"
                                            >
                                                {{ $t('general.commands') }}
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
                                                        <span>{{ $t('general.edit') }}</span>
                                                        <i class="mdi mdi-square-edit-outline text-info"></i>
                                                    </div>
                                                </a>
                                                <a
                                                    class="dropdown-item text-black"
                                                    href="#"
                                                    @click.prevent="deleteCountry(data.id)"
                                                >
                                                    <div class="d-flex justify-content-between align-items-center text-black">
                                                        <span>{{ $t('general.delete') }}</span>
                                                        <i class="fas fa-times text-danger"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>

                                        <!--  edit   -->
                                        <b-modal
                                            :id="`modal-edit-${data.id}`"
                                            :title="getCompanyKey('owner_edit_form')"
                                            title-class="font-18"
                                            body-class="p-4"
                                            dialog-class="modal-full-width"
                                            :ref="`edit-${data.id}`"
                                            :hide-footer="true"
                                            @show="resetModalEdit(data.id)"
                                            @hidden="resetModalHiddenEdit(data.id)"
                                        >
                                            <form>
                                                <div class="mb-3 d-flex justify-content-end">
                                                    <!-- Emulate built in modal footer ok and cancel button actions -->
                                                    <b-button variant="success" type="submit" class="mx-1"
                                                              v-if="!isLoader"
                                                              @click.prevent="editSubmit(data.id)"
                                                    >
                                                        {{ $t('general.Edit') }}
                                                    </b-button>

                                                    <b-button variant="success" class="mx-1" disabled v-else>
                                                        <b-spinner small></b-spinner>
                                                        <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                                    </b-button>

                                                    <b-button
                                                        variant="danger"
                                                        type="button"
                                                        @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)"
                                                    >
                                                        {{ $t('general.Cancel') }}
                                                    </b-button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="field-1" class="control-label">
                                                                {{ getCompanyKey("owner_name_ar") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <div dir="rtl">
                                                                <input
                                                                    @keyup="arabicValue(edit.name)"
                                                                    type="text"
                                                                    class="form-control"
                                                                    v-model="$v.edit.name.$model"
                                                                    :class="{
                                                    'is-invalid':$v.edit.name.$error || errors.name,
                                                    'is-valid':!$v.edit.name.$invalid && !errors.name
                                                }"
                                                                />
                                                            </div>
                                                            <div v-if="!$v.edit.name.minLength" class="invalid-feedback">{{ $t('general.Itmustbeatleast') }} {{ $v.edit.name.$params.minLength.min }} {{ $t('general.letters') }}</div>
                                                            <div v-if="!$v.edit.name.maxLength" class="invalid-feedback">{{ $t('general.Itmustbeatmost') }}  {{ $v.edit.name.$params.maxLength.max }} {{ $t('general.letters') }}</div>
                                                            <template v-if="errors.name">
                                                                <ErrorMessage v-for="(errorMessage,index) in errors.name" :key="index">{{ errorMessage }}</ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="field-2" class="control-label">
                                                                {{ getCompanyKey('owner_name_en') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <div dir="ltr">
                                                                <input
                                                                    @keyup="englishValue(edit.name_e)"
                                                                    type="text"
                                                                    class="form-control"
                                                                    v-model="$v.edit.name_e.$model"
                                                                    :class="{
                                                        'is-invalid':$v.edit.name_e.$error || errors.name_e,
                                                        'is-valid':!$v.edit.name_e.$invalid && !errors.name_e
                                                    }"
                                                                />
                                                            </div>
                                                            <div v-if="!$v.edit.name_e.minLength" class="invalid-feedback">{{ $t('general.Itmustbeatleast') }} {{ $v.edit.name_e.$params.minLength.min }} {{ $t('general.letters') }}</div>
                                                            <div v-if="!$v.edit.name_e.maxLength" class="invalid-feedback">{{ $t('general.Itmustbeatmost') }}  {{ $v.edit.name_e.$params.maxLength.max }} {{ $t('general.letters') }}</div>
                                                            <template v-if="errors.name_e">
                                                                <ErrorMessage v-for="(errorMessage,index) in errors.name_e" :key="index">{{ errorMessage }}</ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group position-relative">
                                                            <label class="control-label">
                                                                {{ getCompanyKey('owner_nationality') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                v-model="$v.edit.nationality_id.$model"
                                                                :options="nationalities.map((type) => type.id)"
                                                                :custom-label="(opt) => nationalities.find((x) => x.id == opt).name"
                                                            >
                                                            </multiselect>
                                                            <div
                                                                v-if="$v.edit.nationality_id.$error || errors.nationality_id"
                                                                class="text-danger"
                                                            >
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.nationality_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.nationality_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group position-relative">
                                                            <label class="control-label">
                                                                {{ getCompanyKey('country') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                @input="showCountryModalEdit"
                                                                v-model="$v.edit.country_id.$model"
                                                                :options="countries.map((type) => type.id)"
                                                                :custom-label="(opt) => countries.find((x) => x.id == opt).name"
                                                            >
                                                            </multiselect>
                                                            <div
                                                                v-if="$v.edit.country_id.$error || errors.country_id"
                                                                class="text-danger"
                                                            >
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.country_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.country_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group position-relative">
                                                            <label class="control-label">
                                                                {{ getCompanyKey('city') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                @input="getCity()"
                                                                v-model="$v.edit.city_id.$model"
                                                                :options="cities.map((type) => type.id)"
                                                                :custom-label="(opt) => cities.find((x) => x.id == opt).name"
                                                            >
                                                            </multiselect>
                                                            <div
                                                                v-if="$v.edit.city_id.$error || errors.city_id"
                                                                class="text-danger"
                                                            >
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.city_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.city_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label  class="control-label">
                                                                {{ getCompanyKey('owner_national_id') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="number"
                                                                class="form-control"
                                                                step="0.1"
                                                                v-model="$v.edit.national_id.$model"
                                                                :class="{
                                                'is-invalid':$v.edit.national_id.$error || errors.national_id,
                                                'is-valid':!$v.edit.national_id.$invalid && !errors.national_id
                                            }"
                                                            />
                                                            <template v-if="errors.national_id">
                                                                <ErrorMessage v-for="(errorMessage,index) in errors.national_id" :key="index">{{ errorMessage }}</ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group position-relative">
                                                            <label class="control-label">
                                                                {{ getCompanyKey('bank_account') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                @input="showBankAccountEdit"
                                                                v-model="$v.edit.bank_account_id.$model"
                                                                :options="bank_accounts.map((type) => type.id)"
                                                                :custom-label="(opt) => bank_accounts.find((x) => x.id == opt).account_number"
                                                            >
                                                            </multiselect>
                                                            <div
                                                                v-if="$v.edit.bank_account_id.$error || errors.bank_account_id"
                                                                class="text-danger"
                                                            >
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.bank_account_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.bank_account_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label  class="control-label">
                                                                {{ getCompanyKey('owner_code') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="number"
                                                                class="form-control"
                                                                v-model="$v.edit.rb_code.$model"
                                                                :class="{
                                                'is-invalid':$v.edit.rb_code.$error || errors.rb_code,
                                                'is-valid':!$v.edit.rb_code.$invalid && !errors.rb_code
                                            }"
                                                            />
                                                            <template v-if="errors.rb_code">
                                                                <ErrorMessage v-for="(errorMessage,index) in errors.rb_code" :key="index">{{ errorMessage }}</ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey('owner_phone') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <VuePhoneNumberInput
                                                                v-model="$v.edit.phone.$model"
                                                                :default-country-code="codeCountry"
                                                                valid-color="#28a745"
                                                                error-color="#dc3545"
                                                                :preferred-countries="['FR', 'EG', 'DE']"
                                                                @update="updatePhoneEdit"
                                                            />
                                                            <template v-if="errors.phone">
                                                                <ErrorMessage v-for="(errorMessage,index) in errors.phone" :key="index">{{ errorMessage }}</ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey('owner_whatsapp') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <VuePhoneNumberInput
                                                                v-model="$v.edit.whatsapp.$model"
                                                                :default-country-code="codeCountry"
                                                                valid-color="#28a745"
                                                                error-color="#dc3545"
                                                                :preferred-countries="['FR', 'EG', 'DE']"
                                                                @update="updateWhatsappEdit"
                                                            />
                                                            <template v-if="errors.whatsapp">
                                                                <ErrorMessage v-for="(errorMessage,index) in errors.whatsapp" :key="index">{{ errorMessage }}</ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey('owner_email') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                v-model="$v.edit.email.$model"
                                                                :class="{
                                                'is-invalid':$v.edit.email.$error || errors.email,
                                                'is-valid':!$v.edit.email.$invalid && !errors.email
                                            }"
                                                            />
                                                            <template v-if="errors.email">
                                                                <ErrorMessage v-for="(errorMessage,index) in errors.email" :key="index">{{ errorMessage }}</ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey('owner_contact_person') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                v-model="$v.edit.contact_person.$model"
                                                                :class="{
                                                'is-invalid':$v.edit.contact_person.$error || errors.contact_person,
                                                'is-valid':!$v.edit.contact_person.$invalid && !errors.contact_person
                                            }"
                                                            />
                                                            <template v-if="errors.contact_person">
                                                                <ErrorMessage v-for="(errorMessage,index) in errors.contact_person" :key="index">{{ errorMessage }}</ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label  class="control-label">
                                                                {{ getCompanyKey('owner_contact_phones') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <VuePhoneNumberInput
                                                                v-model="$v.edit.contact_phones.$model"
                                                                :default-country-code="codeCountry"
                                                                valid-color="#28a745"
                                                                error-color="#dc3545"
                                                                :preferred-countries="['FR', 'EG', 'DE']"
                                                                @update="updateContractEdit"
                                                            />
                                                            <template v-if="errors.contact_phones">
                                                                <ErrorMessage v-for="(errorMessage,index) in errors.contact_phones" :key="index">{{ errorMessage }}</ErrorMessage>
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
                                    <th class="text-center" colspan="20">{{ $t('general.notDataFound') }}</th>
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
<style scope>
.dropdown-menu-custom-company.dropdown .dropdown-menu {
    padding: 5px 10px !important;
    overflow-y: scroll;
    height: 300px;
}
</style>
