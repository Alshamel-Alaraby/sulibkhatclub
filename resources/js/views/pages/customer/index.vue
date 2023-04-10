<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {required, minLength, maxLength, integer, numeric, email, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import {dynamicSortNumber, dynamicSortString} from "../../../helper/tableSort";
import {formatDateOnly} from "../../../helper/startDate";
import Country from "../../../components/country";
import City from "../../../components/city";
import bankAccount from "../../../components/create/bankAccount";
import Multiselect from "vue-multiselect";
// require styles
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
import translation from "../../../helper/translation-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import Avenue from "../../../components/create/avenue";
import Street from "../../../components/create/street";

/**
 * Advanced Table component
 */

export default {
    page: {
        title: "General Customers",
        meta: [{ name: "description", content: 'Customers' }],
    },
    mixins: [translation],
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            if (vm.$store.state.auth.work_flow_trees.includes('general customer') || vm.$store.state.auth.user.type == 'super_admin') {
                return true;
            } else {
                return vm.$router.push({ name: "home" });
            }
        });
    },
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Country,
        Avenue,
        City,
        Multiselect,
        bankAccount,
        Street
    },
    data() {
        return {
            fields: [],
            per_page: 50,
            search: '',
            debounce: {},
            customersPagination: {},
            customers: [],
            cities: [],
            countries: [],
            bank_accounts: [],
            branchCities: [],
            branchCountries: [],
            streets: [],
            avenues: [],
            isLoader: false,
            isVaildPhone: false,
            create: {
                name: '',
                name_e: '',
                phone: '',
                email: '',
                rp_code: '',
                contact_person: '',
                contact_phone:'',
                national_id: null,
                nationality: null,
                bank_account_id: null,
                country_id: null,
                city_id: null ,
                whatsapp: '',
                passport_no: null,
                Note1: '',
                Note2: '',
                Note3: '',
                Note4: '',
                media: [],
                code_country: ''
            },
            edit: {
                name: '',
                name_e: '',
                phone: '',
                email: '',
                rp_code: '',
                contact_person: '',
                contact_phone:'',
                national_id: null,
                nationality: null,
                bank_account_id: null,
                code_country: '',
                country_id: null,
                city_id: null ,
                whatsapp: '',
                passport_no: null,
                Note1: '',
                Note2: '',
                Note3: '',
                Note4: '',
                old_media: [],
            },
            codeCountry: '',
            branchCreate: {
                name: '',
                name_e: '',
                country_id: null,
                city_id: null,
                governorate_id: null,
                avenue_id: null,
                street_id: null,
                latitude: '',
                longitude: ''
            },
            branchEdit: {
                name: '',
                name_e: '',
                country_id: null,
                city_id: null,
                governorate_id: null,
                avenue_id: null,
                street_id: null,
                latitude: '',
                longitude: ''
            },
            customer_id: null,
            errors: {},
            isCheckAll: false,
            checkAll: [],
            images: [],
            media: {},
            titleFile:"",
            saveImageName: [],
            showPhoto: "../../../../../images/img-1.png",
            current_page: 1,
            enabled3: true,
            image: '',
            setting: {
                name: true,
                name_e: true,
                phone : true,
                email : true,
                rp_code: true,
                nationality: true,
                contact_person: true,
                contact_phone: true,
                national_id:true,
                bank_account_id: true,
                country_id: true ,
                city_id: true,
                whatsapp: true,
                passport_no: true
            },
            is_disabled: false,
            filterSetting: [
                'name',
                'name_e',
                'phone',
                'email',
                'rp_code',
                'nationality',
                'contact_person',
                'contact_phone',
                'national_id',
                this.$i18n.locale == 'ar'? 'city.name': 'city.name_e',
                this.$i18n.locale == 'ar'? 'country.name': 'country.name_e',
                this.$i18n.locale == 'ar'? 'bankAccount.account_number': 'bankAccount.account_number',
                'whatsapp',
                'passport_no'
            ],
            Tooltip: '',
            idDelete: null,
            mouseEnter: null,
            printLoading: true,
            printObj: {
                id: "printCustomer",
            }
        }
    },
    validations: {
        create: {
            name: {required: requiredIf(function (model) {
                    return this.isRequired("name");
                }),minLength: minLength(2),maxLength: maxLength(100),},
            name_e: {required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }),minLength: minLength(2),maxLength: maxLength(100),},
            phone: {required: requiredIf(function (model) {
                    return this.isRequired("phone");
                }),maxLength: maxLength(20)},
            email: {required: requiredIf(function (model) {
                    return this.isRequired("email");
                }),maxLength: maxLength(100),email},
            rp_code: {required: requiredIf(function (model) {
                    return this.isRequired("rp_code");
                }),maxLength: maxLength(9),},
            nationality: {required: requiredIf(function (model) {
                    return this.isRequired("nationality");
                })},
            contact_person: {required: requiredIf(function (model) {
                    return this.isRequired("contact_person");
                }),maxLength: maxLength(100)},
            contact_phone: {required: requiredIf(function (model) {
                    return this.isRequired("contact_phone");
                }),maxLength: maxLength(100)},
            national_id: {required: requiredIf(function (model) {
                    return this.isRequired("national_id");
                }),integer,maxLength: maxLength(20)},
            country_id: {required: requiredIf(function (model) {
                    return this.isRequired("country_id");
                })},
            city_id: {required: requiredIf(function (model) {
                    return this.isRequired("city_id");
                })},
            bank_account_id: {required: requiredIf(function (model) {
                    return this.isRequired("bank_account_id");
                })},
            whatsapp: {required: requiredIf(function (model) {
                    return this.isRequired("whatsapp");
                }),maxLength: maxLength(20)},
            passport_no: {required: requiredIf(function (model) {
                    return this.isRequired("passport_no");
                }),integer,maxLength: maxLength(20)},
                media: {},
        },
        edit: {
            name: {required: requiredIf(function (model) {
                    return this.isRequired("name");
                }),minLength: minLength(2),maxLength: maxLength(100),},
            name_e: {required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }),minLength: minLength(2),maxLength: maxLength(100),},
            phone: {required: requiredIf(function (model) {
                    return this.isRequired("phone");
                }),maxLength: maxLength(20)},
            email: {required: requiredIf(function (model) {
                    return this.isRequired("email");
                }),maxLength: maxLength(100),email},
            rp_code: {required: requiredIf(function (model) {
                    return this.isRequired("rp_code");
                }),maxLength: maxLength(9),},
            nationality: {required: requiredIf(function (model) {
                    return this.isRequired("nationality");
                })},
            contact_person: {required: requiredIf(function (model) {
                    return this.isRequired("contact_person");
                }),maxLength: maxLength(100)},
            contact_phone: {required: requiredIf(function (model) {
                    return this.isRequired("contact_phone");
                }),maxLength: maxLength(100)},
            national_id: {required: requiredIf(function (model) {
                    return this.isRequired("national_id");
                }),integer,maxLength: maxLength(20)},
            country_id: {required: requiredIf(function (model) {
                    return this.isRequired("country_id");
                })},
            city_id: {required: requiredIf(function (model) {
                    return this.isRequired("city_id");
                })},
            bank_account_id: {required: requiredIf(function (model) {
                    return this.isRequired("bank_account_id");
                })},
            whatsapp: {required: requiredIf(function (model) {
                    return this.isRequired("whatsapp");
                }),maxLength: maxLength(20)},
            passport_no: {required: requiredIf(function (model) {
                    return this.isRequired("passport_no");
                }),integer,maxLength: maxLength(20)},
                media: {},
        },
        branchCreate: {
            name: {required,minLength: minLength(2),maxLength: maxLength(100),},
            name_e: {required,minLength: minLength(2),maxLength: maxLength(100),},
            country_id: {required},
            city_id: {required},
            governorate_id: {required},
            avenue_id: {required},
            street_id: {required},
            latitude: {required},
            longitude: {required}
        },
        branchEdit: {
            name: {required,minLength: minLength(2),maxLength: maxLength(100),},
            name_e: {required,minLength: minLength(2),maxLength: maxLength(100),},
            country_id: {required},
            city_id: {required},
            governorate_id: {required},
            avenue_id: {required},
            street_id: {required},
            latitude: {required},
            longitude: {required}
        },
        titleFile:{required,minLength: minLength(2),maxLength: maxLength(100),}
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
        this.getCustomTableFields();
        this.getData();
        this.$store.dispatch('locationIp/getIp');
    },
    methods: {
        getCustomTableFields() {
                adminApi
                    .get(`/customTable/table-columns/general_customers`)
                    .then((res) => {
                        this.fields = res.data;
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
        isVisible(fieldName) {
                let res = this.fields.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_visible == 1 ? true : false;
            },
        isRequired(fieldName) {
                let res = this.fields.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_required == 1 ? true : false;
            },
        arabicValue(txt) {
          this.create.name = arabicValue(txt);
          this.edit.name = arabicValue(txt);
        },
        englishValue(txt) {
          this.create.name_e = englishValue(txt);
          this.edit.name_e = englishValue(txt);
        },
        arabicValueBranch(txt) {
            this.branchCreate.name = arabicValue(txt);
            this.branchEdit.name = arabicValue(txt);
        },
        englishValueBranch(txt) {
            this.branchCreate.name_e = arabicValue(txt);
            this.branchEdit.name_e = arabicValue(txt);
        },
        /**
         *  start get Data customers && pagination
         */
        getData(page = 1){
            this.isLoader = true;
            let filter = '';
            for (let i = 0; i < this.filterSetting.length; ++i) {
                filter += `columns[${i}]=${this.filterSetting[i]}&`;
            }

            adminApi.get(`/general-customer?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
                .then((res) => {
                    let l = res.data;
                    this.customers = l.data;
                    this.customersPagination = l.pagination;
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
            if(this.current_page <= this.customersPagination.last_page && this.current_page != this.customersPagination.current_page && this.current_page){
                this.isLoader = true;
                let filter = '';
                for (let i = 0; i < this.filterSetting.length; ++i) {
                    filter += `columns[${i}]=${this.filterSetting[i]}&`;
                }

                adminApi.get(`/general-customer?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
                    .then((res) => {
                        let l = res.data;
                        this.customers = l.data;
                        this.customersPagination = l.pagination;
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
                            .post(`/general-customer/bulk-delete`, { ids: id })
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
                            .delete(`/general-customer/${id}`)
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
                rp_code: '',
                code_country: '',
                nationality: null,
                contact_person: '',
                contact_phone:'',
                national_id: null,
                passport_no:'',
                bank_account_id: null,
                country_id: null,
                city_id: null ,
                whatsapp: '',
                media: null,
            };
            this.countries = [];
            this.cities = [];
            this.bank_accounts = [];
            this.$nextTick(() => { this.$v.$reset() });
            this.errors = {};
            this.images = [];
            this.$bvModal.hide(`create`);
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal(){
            this.countries = [];
            this.cities = [];
            this.bank_accounts = [];
            if(this.isVisible('country_id')) await this.getCategory();
            if(this.isVisible('bank_account_id'))await this.getBankAcount();
            this.create = {
                name: '',
                name_e: '',
                phone: '',
                email: '',
                code_country: '',
                rp_code: '',
                nationality: null,
                contact_person: '',
                contact_phone:'',
                national_id: null,
                bank_account_id: null,
                passport_no:'',
                country_id: null,
                city_id: null ,
                whatsapp: ''
            };
            this.codeCountry = this.$store.getters["locationIp/countryCode"];
            this.$nextTick(() => { this.$v.$reset() });
            this.errors = {};
            this.showPhoto = "../../../../../images/img-1.png";
            this.media = {};
            this.images = [];
        },
        /**
         *  create countrie
         */
        resetForm(){
            this.countries = [];
            this.cities = [];
            this.bank_accounts = [];
            this.create = {
                name: '',
                name_e: '',
                phone: '',
                email: '',
                code_country: '',
                rp_code: '',
                nationality: null,
                contact_person: '',
                contact_phone:'',
                national_id: null,
                passport_no:'',
                bank_account_id: null,
                country_id: null,
                city_id: null ,
                whatsapp: ''
            };
            this.codeCountry = this.$store.getters["locationIp/countryCode"];
            this.$nextTick(() => { this.$v.$reset() });
            this.errors = {};
            this.is_disabled = false;
            this.media = {};
            this.images = [];
        },
        AddSubmit(){
            this.create.code_country = this.codeCountry;
            if(!this.create.name){ this.create.name = this.create.name_e}
            if(!this.create.name_e){ this.create.name_e = this.create.name}
            this.create.company_id = JSON.parse(localStorage.getItem("company_id"));
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                adminApi.post(`/general-customer`,this.create)
                    .then((res) => {
                        this.is_disabled = true;
                        this.customer_id = res.data.data.id;
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
            this.edit.company_id = JSON.parse(localStorage.getItem("company_id"));
            this.$v.edit.$touch();
            this.images.forEach((e) => {
                this.edit.old_media.push(e.id);
            });

            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi.put(`/general-customer/${id}`,this.edit)
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
            this.countries = [];
            this.cities = [];
            this.customer_id = id;
            this.bank_accounts = [];
            if(this.isVisible('country_id'))await this.getCategory();
            if(this.isVisible('bank_account_id'))await this.getBankAcount();
            let build = this.customers.find(e => id == e.id );
            if(this.isVisible('city_id'))await this.getCity(build.country.id);
            this.edit.name = build.name;
            this.edit.name_e = build.name_e;
            this.edit.bank_account_id = build.bank_account_id ?? null;
            this.edit.city_id = build.city ? build.city.id : null;
            this.edit.contact_person = build.contact_person;
            this.edit.contact_phone = build.contact_phone;
            this.edit.country_id = build.country ?build.country.id: null;
            this.edit.national_id = build.national_id;
            this.edit.email = build.email;
            this.edit.code_country = build.code_country;
            this.edit.nationality = build.nationality ?? null;
            this.edit.passport_no = build.passport_no;
            this.edit.phone = build.phone;
            this.edit.rp_code = build.rp_code;
            this.edit.whatsapp = build.whatsapp;
            this.images = build.media ?? [];
            if (this.images && this.images.length > 0) {
                this.showPhoto = this.images[this.images.length - 1].webp;
            } else {
                this.showPhoto = "../../../../../images/img-1.png";
            }

            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id){
            this.errors = {};
            this.edit = {
                name: '',
                name_e: '',
                phone: '',
                email: '',
                rp_code: '',
                nationality: null,
                contact_person: '',
                contact_phone:'',
                national_id: null,
                code_country: '',
                passport_no:'',
                bank_account_id: null,
                country_id: null,
                city_id: null ,
                whatsapp: '',
                old_media: [],
            };
            this.countries = [];
            this.cities = [];
            this.bank_accounts = [];
            this.images = [];
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
       /**
         *  start Image ceate
         */
        uploadPhotoTitle () {
           this.titleFile="";
           this.$bvModal.show(`uploadPhotoTitleCreate`);
           this.errors = {};
        },
        uploadPhotoTitleHidden(){
            this.titleFile="";
            this.$bvModal.hide(`uploadPhotoTitleCreate`);
            this.errors = {};
        },
        changePhoto() {
        document.getElementById("uploadImageCreate").click();
        },
        changePhotoEdit() {
        document.getElementById("uploadImageEdit").click();
        },
        onImageChanged(e) {
        const file = e.target.files[0];
        this.addImage(file);
        },
        addImage(file) {
        this.media = file; //upload
        if (file) {
            this.idDelete = null;
            let is_media = this.images.find(
            (e) => e.name == file.name.slice(0, file.name.indexOf("."))
            );
            this.idDelete = is_media ? is_media.id : null;
            if (!this.idDelete) {
            this.isLoader = true;
            let formDate = new FormData();
            formDate.append("media[][media]", this.media);
            formDate.append("media[][title]", this.titleFile);
            adminApi
                .post(`/media-name`, formDate)
                .then((res) => {
                let old_media = [];
                this.images.forEach((e) => old_media.push(e.id));
                let new_media = [];
                res.data.data.forEach((e) => new_media.push(e.id));

                adminApi
                    .put(`/general-customer/${this.customer_id}`, { old_media, media: new_media })
                    .then((res) => {
                    this.images = res.data.data.media ?? [];
                    if (this.images && this.images.length > 0) {
                        this.showPhoto = this.images[this.images.length - 1].webp;
                    } else {
                        this.showPhoto = "../../../../../images/img-1.png";
                    }
                    this.getData();
                        this.uploadPhotoTitleHidden();
                    })
                    .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                    });
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
            } else {
            Swal.fire({
                title: `${this.$t("general.Thisfilehasalreadybeenuploaded")}`,
                type: "warning",
                showCancelButton: true,
                confirmButtonText: `${this.$t("general.Replace")}`,
                cancelButtonText: `${this.$t("general.Nocancel")}`,
                confirmButtonClass: "btn btn-success mt-2",
                cancelButtonClass: "btn btn-danger ml-2 mt-2",
                buttonsStyling: false,
            }).then((result) => {
                if (result.value) {
                this.isLoader = true;
                let formDate = new FormData();
                formDate.append("media[0]", this.media);
                adminApi
                    .post(`/media`, formDate)
                    .then((res) => {
                    let old_media = [];
                    this.images.forEach((e) => old_media.push(e.id));
                    old_media.splice(old_media.indexOf(this.idDelete), 1);
                    let new_media = [];
                    res.data.data.forEach((e) => new_media.push(e.id));

                    adminApi
                        .put(`/general-customer/${this.country_id}`, { old_media, media: new_media })
                        .then((res) => {
                        this.images = res.data.data.media ?? [];
                        if (this.images && this.images.length > 0) {
                            this.showPhoto = this.images[this.images.length - 1].webp;
                        } else {
                            this.showPhoto = "../../../../../images/img-1.png";
                        }
                        this.getData();
                        })
                        .catch((err) => {
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                        });
                        });
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
            });
            }
        }
        },
        deleteImageCreate(id, index) {
        let old_media = [];
        this.images.forEach((e) => {
            if (e.id != id) {
            old_media.push(e.id);
            }
        });
        adminApi
            .put(`/general-customer/${this.customer_id}`, { old_media })
            .then((res) => {
            this.customers[index] = res.data.data;
            this.images = res.data.data.media ?? [];
            if (this.images && this.images.length > 0) {
                this.showPhoto = this.images[this.images.length - 1].webp;
            } else {
                this.showPhoto = "../../../../../images/img-1.png";
            }
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
         *  end Image ceate
         *
         */
        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if(this.mouseEnter != id){
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`/general-customer/logs/${id}`)
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
        getCurrentYear() {
            return new Date().getFullYear();
        },
        async getCategory() {
            this.create.city_id = null;
            this.edit.city_id = null;
            await adminApi
                .get(`/countries?is_active=active`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضافة دولة", name_e: "Add Country" });
                    this.countries = l;
                    this.branchCountries = l;
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
            this.bank_accounts = [];
            await adminApi.get(`/bank-accounts?`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضافة حساب بنكي", name_e: "Add Bank Account",account_number: "Add Bank Account" });
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
                this.cities = [];
                this.create.city_id = null;
                this.edit.city_id = null;
                if(this.isVisible('country_id')){
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
            }
        },
        async getCitybranch(id = null){
            if (id) {
                this.branchCities = [];
                this.branchCreate.city_id = null;
                this.branchEdit.city_id = null;
                await adminApi
                    .get(`/cities?country_id=${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.unshift({ id: 0, name: "اضافة مدينة", name_e: "Add City" });
                        this.branchCities = l;
                    })
                    .catch((err) => {
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                        });
                    });
            }

            if(this.branchCreate.city_id  == 0 || this.create.branchCreate == 0){
                this.$bvModal.show("city-create");
                this.branchCreate.city_id = null;
                this.branchCreate.city_id = null;
            }else if(this.branchCreate.city_id  > 0 || this.create.branchCreate > 0){
                this.branchCreate.avenue_id = null;
                this.branchCreate.avenue_id = null;
                let country = this.branchCreate.country_id ? this.branchCreate.country_id : this.branchCreate.country_id;
                let city =    this.branchCreate.city_id ? this.branchCreate.city_id : this.branchCreate.city_id;
                await adminApi
                    .get(`/avenues?country_id=${country}&city_id=${city}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.unshift({ id: 0, name: "اضافة منطقه", name_e: "Add Avenue" });
                        this.avenues = l;
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
        async getAvenue(){
            this.branchEdit.street_id = null;
            this.branchCreate.street_id = null;
            let country = this.branchCreate.country_id ? this.branchCreate.country_id : this.branchEdit.country_id;
            let city =    this.branchCreate.city_id ? this.branchCreate.city_id : this.branchEdit.city_id;
            await adminApi
                .get(`/avenues?country_id=${country}&city_id=${city}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضافة منطقه", name_e: "Add Avenue" });
                    this.avenues = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async showAvenueModal(id = null, id2 = null) {
            if (this.branchCreate.avenue_id == 0 || this.branchEdit.avenue_id == 0) {
                this.$bvModal.show("avenue-create");
                this.branchCreate.avenue_id = null;
                this.branchEdit.avenue_id = null;
            }else if(this.branchCreate.avenue_id > 0 || this.branchEdit.avenue_id > 0){
                let avenue = this.branchCreate.avenue_id ? this.branchCreate.avenue_id : this.branchEdit.avenue_id;
                this.branchCreate.street_id = null;
                this.branchEdit.street_id = null;
                await
                    adminApi
                        .get(
                            `/streets?avenue_id=${avenue}`
                        )
                        .then((res) => {
                            let l = res.data.data;
                            l.unshift({ id: 0, name: "اضافة شارع", name_e: "Add Street" });
                            this.streets = l;
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
        async getStreet(){
            let avenue = this.branchCreate.avenue_id ? this.branchCreate.avenue_id : this.branchEdit.avenue_id;
            this.branchCreate.street_id = null;
            this.branchEdit.street_id = null;
            await
                adminApi
                    .get(
                        `/streets?avenue_id=${avenue}`
                    )
                    .then((res) => {
                        let l = res.data.data;
                        l.unshift({ id: 0, name: "اضافة شارع", name_e: "Add Street" });
                        this.streets = l;
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
        showCountryModal() {
            if (this.branchCreate.country_id == 0) {
                this.$bvModal.show("country-create");
                this.branchCreate.country_id = null;
            }else if(this.branchCreate.country_id > 0){
                this.getCity(this.branchCreate.country_id);
            }else if (this.create.country_id == 0) {
                this.$bvModal.show("country-create");
                this.create.country_id = null;
            }else if(this.create.country_id > 0){
                this.getCity(this.create.country_id);
            }
        },
        showCountryBranchModal() {
            if (this.branchCreate.country_id == 0) {
                this.$bvModal.show("country-create");
                this.branchCreate.country_id = null;
            }else if(this.branchCreate.country_id > 0){
                this.getCitybranch(this.branchCreate.country_id);
            }
        },
        showCountryModalEdit() {
            if (this.edit.country_id == 0) {
                this.$bvModal.show("country-create");
                this.edit.country_id = null;
            }else if(this.edit.country_id > 0){
                this.getCity(this.edit.country_id);
            }
        },
        showCountryBranchModalEdit() {
            if (this.branchEdit.country_id == 0) {
                this.$bvModal.show("country-create");
                this.branchEdit.country_id = null;
            }else if(this.branchEdit.country_id > 0){
                this.getCitybranch(this.branchEdit.country_id);
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
        showStreetModal(){
            if (this.branchCreate.street_id == 0 || this.branchEdit.street_id == 0) {
                this.$bvModal.show("create-street");
                this.branchCreate.street_id = null;
                this.branchEdit.street_id = null;
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
                    XLSX.writeFile(wb, fn || (('Customer' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },
        async getLocation() {
            if (navigator.geolocation) {
                return await navigator.geolocation.getCurrentPosition(this.showPosition);
            }
        },
        showPosition(position) {
            this.branchCreate.latitude = position.coords.latitude;
            this.branchCreate.longitude = position.coords.longitude;
            this.branchEdit.latitude = position.coords.latitude;
            this.branchEdit.longitude = position.coords.longitude;
        },
        updatePhone(e) {
            this.create.phone = e.phoneNumber;
            // this.create.phone_code = e.countryCallingCode;
            // this.isVaildPhone = e.isValid;
        },
        updateWhatsapp(e){
            this.create.whatsapp = e.phoneNumber;
        },
        updateContract(e){
            this.create.contact_phone = e.phoneNumber;
        },
        updateWhatsappEdit(e){
            this.edit.whatsapp = e.phoneNumber;
        },
        updatePhoneEdit(e) {
            this.edit.phone = e.phoneNumber;
            // this.edit.phone_code = e.countryCallingCode;
            // this.isVaildPhone = e.isValid;
        },
        updateContractEdit(e){
            this.edit.contact_phone = e.phoneNumber;
        },
    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <bankAccount :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"  @created="getBankAcount" />
        <Country :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'country-create'" @created="getCategory" />
        <City :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCity(create.country_id ? create.country_id: edit.country_id)" />
        <Avenue :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getAvenue"/>
        <Street :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getStreet" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title"> {{ $t('general.customersTable') }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%;">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown" class="btn-block setting-search dropdown-menu-custom-company">
                                        <b-form-checkbox v-if="isVisible('name')" v-model="filterSetting" value="name" class="mb-1">{{ getCompanyKey('general_customer_name_ar') }}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('name_e')" v-model="filterSetting" value="name_e" class="mb-1">{{ getCompanyKey('general_customer_name_en') }}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('phone')" v-model="filterSetting" value="phone" class="mb-1">{{ getCompanyKey('general_customer_phone') }}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('email')" v-model="filterSetting" value="email" class="mb-1">{{ getCompanyKey('general_customer_email') }}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('rp_code')" v-model="filterSetting" value="rp_code" class="mb-1">{{ getCompanyKey('general_customer_code') }}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('nationality')" v-model="filterSetting" :value="$i18n.locale == 'ar'? 'nationality': 'nationality'" class="mb-1">{{ getCompanyKey('general_customer_nationality') }}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('contact_person')" v-model="filterSetting" value="contact_person" class="mb-1">{{ getCompanyKey('general_customer_contact_person') }}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('contact_phone')" v-model="filterSetting" value="contact_phone" class="mb-1">{{ getCompanyKey('general_customer_contact_phones') }}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('country_id')" v-model="filterSetting" :value="$i18n.locale == 'ar'? 'country.name': 'country.name_e'" class="mb-1">{{ getCompanyKey('general_customer_country') }}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('city_id')" v-model="filterSetting" :value="$i18n.locale == 'ar'? 'city.name': 'city.name_e'" class="mb-1">{{ getCompanyKey('general_customer_city') }}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('national_id')" v-model="filterSetting" value="national_id" class="mb-1">{{ getCompanyKey('customer_national_id') }}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('bank_account_id')" v-model="filterSetting" :value="$i18n.locale == 'ar'? 'bankAccount.name': 'bankAccount.name_e'" class="mb-1">{{ getCompanyKey('bank_account') }}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('whatsapp')" v-model="filterSetting" value="whatsapp" class="mb-1">{{ getCompanyKey('general_customer_whatsapp') }}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('passport_no')" v-model="filterSetting" value="passport_no" class="mb-1">{{ getCompanyKey('general_customer_passport_number') }}</b-form-checkbox>
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
                                    <button v-print="'#printCustomer'" class="custom-btn-dowonload">
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
                                            <b-form-checkbox v-if="isVisible('name')" v-model="setting.name" class="mb-1">{{getCompanyKey('general_customer_name_ar') }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('name_e')" v-model="setting.name_e" class="mb-1">{{ getCompanyKey('general_customer_name_en') }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('phone')" v-model="setting.phone" class="mb-1">{{getCompanyKey('general_customer_phone') }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('email')" v-model="setting.email" class="mb-1">{{ getCompanyKey('general_customer_email') }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('rp_code')" v-model="setting.rp_code" class="mb-1">{{getCompanyKey('general_customer_code') }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('contact_person')" v-model="setting.contact_person" class="mb-1">{{ getCompanyKey('general_customer_contact_person') }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('contact_phone')" v-model="setting.contact_phone" class="mb-1">{{getCompanyKey('general_customer_contact_phones') }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('nationality')" v-model="setting.nationality" class="mb-1">{{ getCompanyKey('general_customer_nationality') }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('national_id')" v-model="setting.national_id" class="mb-1">{{ getCompanyKey('general_customer_national_id') }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('bank_account_id')" v-model="setting.bank_account_id" class="mb-1">{{getCompanyKey('bank_account') }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('country_id')" v-model="setting.country_id" class="mb-1">{{getCompanyKey('general_customer_country') }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('city_id')" v-model="setting.city_id" class="mb-1">{{ getCompanyKey('general_customer_city') }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('whatsapp')" v-model="setting.whatsapp" class="mb-1">{{ getCompanyKey('general_customer_whatsapp') }}</b-form-checkbox>
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
                                            {{ customersPagination.from }}-{{ customersPagination.to }} / {{ customersPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{'pointer-events':customersPagination.current_page == 1 ? 'none': ''}"
                                                @click.prevent="getData(customersPagination.current_page - 1)"
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
                                                :style="{'pointer-events':customersPagination.last_page == customersPagination.current_page ? 'none': ''}"
                                                @click.prevent="getData(customersPagination.current_page + 1)"
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
                            :title="getCompanyKey('general_customer_create_form')"
                            title-class="font-18"
                            dialog-class="modal-full-width"
                            body-class="p-4 "
                            :hide-footer="true"
                            @show="resetModal"
                            @hidden="resetModalHidden"
                        >
                            <form>
                                <div class="card">
                                    <div class="card-body">
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
                                    </div>

                                    <b-tabs nav-class="nav-tabs nav-bordered">
                                        <b-tab :title="$t('general.DataEntry')" active>
                                            <div class="row">
                                                <div class="col-md-4" v-if="isVisible('name')">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">
                                                            {{ getCompanyKey('general_customer_name_ar') }}
                                                            <span v-if="isRequired('name')" class="text-danger">*</span>
                                                        </label>
                                                        <div dir="rtl">
                                                            <input
                                                                @keyup="arabicValue(create.name)"
                                                                type="text"
                                                                class="form-control"
                                                                data-create="1"
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
                                                <div class="col-md-4" v-if="isVisible('name_e')">
                                                    <div class="form-group">
                                                        <label for="field-2" class="control-label">
                                                            {{ getCompanyKey('general_customer_name_en') }}
                                                            <span  v-if="isRequired('name_e')" class="text-danger">*</span>
                                                        </label>
                                                        <div dir="ltr">
                                                            <input
                                                                @keyup="englishValue(create.name_e)"
                                                                type="text"
                                                                class="form-control"
                                                                data-create="2"
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
                                            <hr v-if="isVisible('nationality') || isVisible('country_id') || isVisible('city_id') || isVisible('national_id') || isVisible('passport_no')"  style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                            <div class="row">
                                                <div class="col-md-4" v-if="isVisible('nationality')">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey('general_customer_nationality') }}
                                                            <span v-if="isRequired('nationality')" class="text-danger">*</span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            data-create="9"
                                                            v-model="$v.create.nationality.$model"
                                                            :class="{
                                                'is-invalid':$v.create.nationality.$error || errors.nationality,
                                                'is-valid':!$v.create.nationality.$invalid && !errors.nationality
                                            }"
                                                        />
                                                        <template v-if="errors.nationality">
                                                            <ErrorMessage v-for="(errorMessage,index) in errors.nationality" :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-4" v-if="isVisible('country_id')">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey('general_customer_country') }}
                                                            <span v-if="isRequired('country_id')" class="text-danger">*</span>
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
                                                <div class="col-md-4" v-if="isVisible('city_id')">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey('general_customer_city') }}
                                                            <span v-if="isRequired('city_id')" class="text-danger">*</span>
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
                                                <div class="col-md-4" v-if="isVisible('national_id')">
                                                    <div class="form-group">
                                                        <label  class="control-label">
                                                            {{ getCompanyKey('general_customer_national_id') }}
                                                            <span v-if="isRequired('national_id')" class="text-danger">*</span>
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
                                                <div class="col-md-4" v-if="isVisible('passport_no')">
                                                    <div class="form-group">
                                                        <label  class="control-label">
                                                            {{ getCompanyKey('general_customer_passport_number') }}
                                                            <span v-if="isRequired('passport_no')" class="text-danger">*</span>
                                                        </label>
                                                        <input
                                                            type="number"
                                                            class="form-control"
                                                            step="0.1"
                                                            v-model="$v.create.passport_no.$model"
                                                            :class="{
                                                                'is-invalid':$v.create.passport_no.$error || errors.passport_no,
                                                                'is-valid':!$v.create.passport_no.$invalid && !errors.passport_no
                                                            }"
                                                        />
                                                        <template v-if="errors.passport_no">
                                                            <ErrorMessage v-for="(errorMessage,index) in errors.passport_no" :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr v-if="isVisible('bank_account_id') || isVisible('rp_code') || isVisible('phone') || isVisible('whatsapp') || isVisible('email')" style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                            <div class="row">
                                                <div class="col-md-4" v-if="isVisible('bank_account_id')">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey('bank_account') }}
                                                            <span  v-if="isRequired('bank_account_id')" class="text-danger">*</span>
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
                                                <div class="col-md-4" v-if="isVisible('rp_code')">
                                                    <div class="form-group">
                                                        <label  class="control-label">
                                                            {{ getCompanyKey('general_customer_code') }}
                                                            <span v-if="isRequired('rp_code')" class="text-danger">*</span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            data-create="9"
                                                            v-model="$v.create.rp_code.$model"
                                                            :class="{
                                                'is-invalid':$v.create.rp_code.$error || errors.rp_code,
                                                'is-valid':!$v.create.rp_code.$invalid && !errors.rp_code
                                            }"
                                                        />
                                                        <template v-if="errors.rp_code">
                                                            <ErrorMessage v-for="(errorMessage,index) in errors.rp_code" :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-4" v-if="isVisible('phone')">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey('general_customer_phone') }}
                                                            <span v-if="isRequired('phone')" class="text-danger">*</span>
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
                                                <div class="col-md-4" v-if="isVisible('whatsapp')">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey('general_customer_whatsapp') }}
                                                            <span v-if="isRequired('whatsapp')" class="text-danger">*</span>
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
                                                <div class="col-md-4" v-if="isVisible('email')">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey('general_customer_email') }}
                                                            <span v-if="isRequired('email')" class="text-danger">*</span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            data-create="9"
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
                                            <hr v-if="isVisible('contact_person') || isVisible('contact_phone')" style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                            <div class="row">
                                                <div class="col-md-4" v-if="isVisible('contact_person')">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey('general_customer_contact_person') }}
                                                            <span v-if="isRequired('contact_person')" class="text-danger">*</span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            data-create="9"
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
                                                <div class="col-md-4" v-if="isVisible('contact_phone')">
                                                    <div class="form-group">
                                                        <label  class="control-label">
                                                            {{ getCompanyKey('general_customer_contact_phones') }}
                                                            <span v-if="isRequired('contact_phone')" class="text-danger">*</span>
                                                        </label>
                                                        <VuePhoneNumberInput
                                                            v-model="$v.create.contact_phone.$model"
                                                            :default-country-code="codeCountry"
                                                            valid-color="#28a745"
                                                            error-color="#dc3545"
                                                            :preferred-countries="['FR', 'EG', 'DE']"
                                                            @update="updateContract"
                                                        />
                                                        <template v-if="errors.contact_phone">
                                                            <ErrorMessage v-for="(errorMessage,index) in errors.contact_phone" :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </b-tab>
                                        <!-- <b-tab
                                            :title="$t('general.customerBranch')"
                                        >
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="field-16" class="control-label">
                                                            {{ getCompanyKey('general_customer_name_ar') }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div dir="rtl">
                                                            <input
                                                                @keyup="arabicValueBranch(branchCreate.name)"
                                                                type="text"
                                                                class="form-control"
                                                                data-create="1"
                                                                v-model="$v.branchCreate.name.$model"
                                                                :class="{
                                                                'is-invalid':$v.branchCreate.name.$error || errors.name,
                                                                'is-valid':!$v.branchCreate.name.$invalid && !errors.name
                                                            }"
                                                                id="field-16"
                                                            />
                                                        </div>
                                                        <div v-if="!$v.branchCreate.name.minLength" class="invalid-feedback">{{ $t('general.Itmustbeatleast') }} {{ $v.branchCreate.name.$params.minLength.min }} {{ $t('general.letters') }}</div>
                                                        <div v-if="!$v.branchCreate.name.maxLength" class="invalid-feedback">{{ $t('general.Itmustbeatmost') }}  {{ $v.branchCreate.name.$params.maxLength.max }} {{ $t('general.letters') }}</div>
                                                        <template v-if="errors.name">
                                                            <ErrorMessage v-for="(errorMessage,index) in errors.name" :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-3" >
                                                    <div class="form-group">
                                                        <label for="field-21" class="control-label">
                                                            {{ getCompanyKey('general_customer_name_en') }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div dir="ltr">
                                                            <input
                                                                @keyup="englishValueBranch(branchCreate.name_e)"
                                                                type="text"
                                                                class="form-control"
                                                                data-create="2"
                                                                v-model="$v.branchCreate.name_e.$model"
                                                                :class="{
                                                                'is-invalid':$v.branchCreate.name_e.$error || errors.name_e,
                                                                'is-valid':!$v.branchCreate.name_e.$invalid && !errors.name_e
                                                            }"
                                                                id="field-21"
                                                            />
                                                        </div>
                                                        <div v-if="!$v.branchCreate.name_e.minLength" class="invalid-feedback">{{ $t('general.Itmustbeatleast') }} {{ $v.branchCreate.name_e.$params.minLength.min }} {{ $t('general.letters') }}</div>
                                                        <div v-if="!$v.branchCreate.name_e.maxLength" class="invalid-feedback">{{ $t('general.Itmustbeatmost') }}  {{ $v.branchCreate.name_e.$params.maxLength.max }} {{ $t('general.letters') }}</div>
                                                        <template v-if="errors.name_e">
                                                            <ErrorMessage v-for="(errorMessage,index) in errors.name_e" :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-6"></div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("building_longitude") }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input
                                                            type="number"
                                                            class="form-control"
                                                            step="0.00000000000001"
                                                            v-model="$v.branchCreate.longitude.$model"
                                                            :class="{
                                                                'is-invalid': $v.branchCreate.longitude.$error || errors.longitude,
                                                                'is-valid': !$v.branchCreate.longitude.$invalid && !errors.longitude,
                                                              }"
                                                        />
                                                        <template v-if="errors.longitude">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.longitude"
                                                                :key="index"
                                                            >{{ errorMessage }}</ErrorMessage
                                                            >
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("building_latitude") }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input
                                                            type="number"
                                                            class="form-control"
                                                            data-create="9"
                                                            step="0.00000000000001"
                                                            v-model="$v.branchCreate.latitude.$model"
                                                            :class="{
                                                            'is-invalid': $v.branchCreate.latitude.$error || errors.latitude,
                                                            'is-valid': !$v.branchCreate.latitude.$invalid && !errors.latitude,
                                                          }"
                                                        />
                                                        <template v-if="errors.lat">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.latitude"
                                                                :key="index"
                                                            >{{ errorMessage }}</ErrorMessage
                                                            >
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 d-flex align-items-center mt-3">
                                                    <button
                                                        class="btn btn-primary"
                                                        type="button"
                                                        @click="getLocation"
                                                    >
                                                        {{ $t('general.location') }}
                                                    </button>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey('general_customer_country') }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <multiselect
                                                            @input="showCountryBranchModal"
                                                            v-model="$v.branchCreate.country_id.$model"
                                                            :options="branchCountries.map((type) => type.id)"
                                                            :custom-label="(opt) => branchCountries.find((x) => x.id == opt).name"
                                                        >
                                                        </multiselect>
                                                        <div
                                                            v-if="$v.branchCreate.country_id.$error || errors.country_id"
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
                                                <div class="col-md-3" >
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey('general_customer_city') }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <multiselect
                                                            @input="getCitybranch()"
                                                            v-model="$v.branchCreate.city_id.$model"
                                                            :options="branchCities.map((type) => type.id)"
                                                            :custom-label="(opt) => branchCities.find((x) => x.id == opt).name"
                                                        >
                                                        </multiselect>
                                                        <div
                                                            v-if="$v.branchCreate.city_id.$error || errors.city_id"
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
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("avenue") }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <multiselect
                                                            @input="showAvenueModal"
                                                            v-model="$v.branchCreate.avenue_id.$model"
                                                            :options="avenues.map((type) => type.id)"
                                                            :custom-label="
                                                                (opt) => avenues.find((x) => x.id == opt).name
                                                            "
                                                        >
                                                        </multiselect>
                                                        <div
                                                            v-if="$v.branchCreate.avenue_id.$error || errors.avenue_id"
                                                            class="text-danger"
                                                        >
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>
                                                        <template v-if="errors.avenue_id">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.avenue_id"
                                                                :key="index"
                                                            >{{ errorMessage }}</ErrorMessage
                                                            >
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("street") }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <multiselect
                                                            @input="showStreetModal"
                                                            v-model="$v.branchCreate.street_id.$model"
                                                            :options="streets.map((type) => type.id)"
                                                            :custom-label="
                                                                (opt) => streets.find((x) => x.id == opt).name
                                                            "
                                                        >
                                                        </multiselect>
                                                        <div
                                                            v-if="$v.branchCreate.street_id.$error || errors.street_id"
                                                            class="text-danger"
                                                        >
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>
                                                        <template v-if="errors.street_id">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.street_id"
                                                                :key="index"
                                                            >{{ errorMessage }}</ErrorMessage
                                                            >
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </b-tab> -->
                                        <b-tab :disabled="!customer_id" :title="$t('general.ImageUploads')">
                                            <div class="row">
                                                <b-modal
                                                    id="uploadPhotoTitleCreate"
                                                    :title="$t('general.ImageUploads')"
                                                    title-class="font-18"
                                                    body-class="p-4 "
                                                    :hide-footer="true"
                                                    @show="uploadPhotoTitle"
                                                    @hidden="uploadPhotoTitleHidden"
                                                >
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ $t("general.titleFile") }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div dir="rtl">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                data-create="1"
                                                                v-model="$v.titleFile.$model"
                                                                :class="{
                                                                        'is-invalid':$v.titleFile.$error || errors.title,
                                                                        'is-valid':!$v.titleFile.$invalid && !errors.title
                                                                    }"
                                                            />
                                                        </div>
                                                        <div v-if="!$v.titleFile.minLength" class="invalid-feedback">{{ $t('general.Itmustbeatleast') }} {{ $v.titleFile.$params.minLength.min }} {{ $t('general.letters') }}</div>
                                                        <div v-if="!$v.titleFile.maxLength" class="invalid-feedback">{{ $t('general.Itmustbeatmost') }}  {{ $v.titleFile.$params.maxLength.max }} {{ $t('general.letters') }}</div>
                                                        <template v-if="errors.title">
                                                            <ErrorMessage v-for="(errorMessage,index) in errors.title" :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>

                                                    <input accept="image/png, image/gif, image/jpeg, image/jpg" type="file" id="uploadImageCreate"
                                                      @change.prevent="onImageChanged" class="input-file-upload position-absolute" :class="[
                                                        'd-none',
                                                        {
                                                          'is-invalid': $v.create.media.$error || errors.media,
                                                          'is-valid': !$v.create.media.$invalid && !errors.media,
                                                        },
                                                      ]" />

                                                    <b-button :disabled="!titleFile && $v.titleFile.$error" @click="changePhoto" variant="success" type="button"
                                                              class="mx-1 font-weight-bold px-3" v-if="!isLoader">
                                                        {{ $t("general.Add") }}
                                                    </b-button>
                                                    <b-button variant="success" class="mx-1" disabled v-else>
                                                        <b-spinner small></b-spinner>
                                                        <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                    </b-button>

                                                </b-modal>
                                                <div class="col-md-8 my-1">
                                                  <!-- file upload -->
                                                  <div class="row align-content-between" style="width: 100%; height: 100%">
                                                    <div class="col-12">
                                                      <div class="d-flex flex-wrap">
                                                        <div :class="[
                                                          'dropzone-previews col-4 position-relative mb-2',
                                                        ]" v-for="(photo, index) in images" :key="photo.id">
                                                          <div :class="[
                                                            'card mb-0 shadow-none border',
                                                            images.length - 1 == index ? 'bg-primary' : '',
                                                          ]">
                                                            <div class="p-2">
                                                              <div class="row align-items-center">
                                                                <div class="col-auto" @click="showPhoto = photo.webp">
                                                                  <img data-dz-thumbnail :src="photo.webp" class="avatar-sm rounded bg-light"
                                                                    @error="src = '../../../../../images/img-1.png'" />
                                                                </div>
                                                                <div class="col pl-0">
                                                                  <a href="javascript:void(0);" :class="[
                                                                    'font-weight-bold',
                                                                    images.length - 1 == index
                                                                      ? 'text-white'
                                                                      : 'text-muted',
                                                                  ]" data-dz-name>
                                                                    {{ photo.title }}
                                                                  </a>
                                                                </div>
                                                                <!-- Button -->
                                                                <a href="javascript:void(0);" :class="[
                                                                  'btn-danger dropzone-close',
                                                                  $i18n.locale == 'ar'
                                                                    ? 'dropzone-close-rtl'
                                                                    : '',
                                                                ]" data-dz-remove @click.prevent="
                                                                    deleteImageCreate(photo.id, index)
                                                                    ">
                                                                  <i class="fe-x"></i>
                                                                </a>
                                                              </div>
                                                            </div>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                    <div class="footer-image col-12">
                                                      <b-button @click="uploadPhotoTitle" variant="success" type="button"
                                                        class="mx-1 font-weight-bold px-3" v-if="!isLoader">
                                                        {{ $t("general.Add") }}
                                                      </b-button>
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                  <div class="show-dropzone">
                                                    <img :src="showPhoto" class="img-thumbnail" @error="src = '../../../../../images/img-1.png'" />
                                                  </div>
                                                </div>
                                              </div>
                                        </b-tab>
                                    </b-tabs>
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
                                   id="printCustomer">
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
                                    <th v-if="setting.name && isVisible('name')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('general_customer_name_ar') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="customers.sort(sortString('name'))"></i>
                                                <i class="fas fa-arrow-down" @click="customers.sort(sortString('-name'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.name_e && isVisible('name_e')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('general_customer_name_en') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="customers.sort(sortString('name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="customers.sort(sortString('-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.phone && isVisible('phone')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('customer_phone') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="customers.sort(sortNumber('phone'))"></i>
                                                <i class="fas fa-arrow-down" @click="customers.sort(sortNumber('-phone'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.email && isVisible('email')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('general_customer_email') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="customers.sort(sortString('email'))"></i>
                                                <i class="fas fa-arrow-down" @click="customers.sort(sortString('-email'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.nationality && isVisible('nationality')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('general_customer_nationality') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="customers.sort(SortNumber('nationality'))"></i>
                                                <i class="fas fa-arrow-down" @click="customers.sort(SortNumber('-nationality'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.national_id && isVisible('national_id')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('general_customer_national_id') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="customers.sort(sortNumber('national_id'))"></i>
                                                <i class="fas fa-arrow-down" @click="customers.sort(sortNumber('-national_id'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.contact_person && isVisible('contact_person')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('general_customer_contact_person') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="customers.sort(sortString('contact_person'))"></i>
                                                <i class="fas fa-arrow-down" @click="customers.sort(sortString('-contact_person'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.contact_phone && isVisible('contact_phone')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('general_customer_contact_phones') }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.country_id && isVisible('country_id')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('general_customer_country') }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.city_id && isVisible('city_id')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('general_customer_city') }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.bank_account_id && isVisible('bank_account_id')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('bank_account') }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.whatsapp && isVisible('whatsapp')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('general_customer_whatsapp') }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.rp_code && isVisible('rp_code')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('general_customer_code') }}</span>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t('general.Action') }}
                                    </th>
                                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                </tr>
                                </thead>
                                <tbody v-if="customers.length > 0">
                                <tr
                                    @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                                    v-for="(data,index) in customers"
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
                                    <td v-if="setting.name && isVisible('name')">
                                        <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                                    </td>
                                    <td v-if="setting.name_e && isVisible('name_e')">
                                        <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
                                    </td>
                                    <td v-if="setting.phone && isVisible('phone')">{{ data.phone }}</td>
                                    <td v-if="setting.email && isVisible('email')">{{ data.email }}</td>
                                    <td v-if="setting.nationality && isVisible('nationality')">{{ data.nationality }}</td>
                                    <td v-if="setting.national_id && isVisible('national_id')">{{ data.national_id }}</td>
                                    <td v-if="setting.contact_person && isVisible('contact_person')">{{ data.contact_person }}</td>
                                    <td v-if="setting.contact_phone && isVisible('contact_phone')">{{ data.contact_phone }}</td>
                                    <td v-if="setting.country_id && isVisible('country_id')">
                                        {{data.country ? $i18n.locale == 'ar'? data.country.name : data.country.name_e : ' - '}}
                                    </td>
                                    <td v-if="setting.city_id && isVisible('city_id')">
                                        {{data.city ? $i18n.locale == 'ar'? data.city.name : data.city.name_e : ' - '}}
                                    </td>
                                    <td v-if="setting.bank_account_id && isVisible('bank_account_id')">
                                        {{ data.bankAccount ?  data.bankAccount.account_number : ' - '}}
                                    </td>
                                    <td v-if="setting.whatsapp && isVisible('whatsapp')">{{ data.whatsapp }}</td>
                                    <td v-if="setting.rp_code && isVisible('rp_code')">{{ data.rp_code }}</td>
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
                                            :title="getCompanyKey('general_customer_edit_form')"
                                            title-class="font-18"
                                            body-class="p-4"
                                            dialog-class="modal-full-width"
                                            :ref="`edit-${data.id}`"
                                            :hide-footer="true"
                                            @show="resetModalEdit(data.id)"
                                            @hidden="resetModalHiddenEdit(data.id)"
                                        >
                                            <form>
                                                <div class="card">
                                                    <div class="card-body">
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
                                                    </div>

                                                    <b-tabs nav-class="nav-tabs nav-bordered">
                                                        <b-tab :title="$t('general.DataEntry')" active>

                                                            <div class="row">
                                                                <div class="col-md-4" v-if="isVisible('name')">
                                                                    <div class="form-group">
                                                                        <label for="field-1" class="control-label">
                                                                            {{ getCompanyKey('general_customer_name_ar') }}
                                                                            <span v-if="isRequired('name')" class="text-danger">*</span>
                                                                        </label>
                                                                        <div dir="rtl">
                                                                            <input
                                                                                @keyup="arabicValue(edit.name)"
                                                                                type="text"
                                                                                class="form-control"
                                                                                data-edit="1"
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
                                                                <div class="col-md-4" v-if="isVisible('name_e')">
                                                                    <div class="form-group">
                                                                        <label for="field-2" class="control-label">
                                                                            {{ getCompanyKey('general_customer_name_en') }}
                                                                            <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                                                        </label>
                                                                        <div dir="ltr">
                                                                            <input
                                                                                @keyup="englishValue(edit.name_e)"
                                                                                type="text"
                                                                                class="form-control"
                                                                                data-edit="2"
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
                                                            <hr v-if="isVisible('nationality') || isVisible('country_id') || isVisible('city_id') || isVisible('national_id') || isVisible('passport_no')"  style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                            <div class="row">
                                                                <div class="col-md-4"  v-if="isVisible('nationality')">
                                                                    <div class="form-group">
                                                                        <label class="control-label">
                                                                            {{ getCompanyKey('general_customer_nationality') }}
                                                                            <span  v-if="isRequired('nationality')" class="text-danger">*</span>
                                                                        </label>
                                                                        <input
                                                                            type="text"
                                                                            class="form-control"
                                                                            data-edit="9"
                                                                            v-model="$v.edit.nationality.$model"
                                                                            :class="{
                                                                    'is-invalid':$v.edit.nationality.$error || errors.nationality,
                                                                    'is-valid':!$v.edit.nationality.$invalid && !errors.nationality
                                                                }"
                                                                        />
                                                                        <template v-if="errors.nationality">
                                                                            <ErrorMessage v-for="(errorMessage,index) in errors.nationality" :key="index">{{ errorMessage }}</ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4" v-if="isVisible('country_id')">
                                                                    <div class="form-group position-relative">
                                                                        <label class="control-label">
                                                                            {{ getCompanyKey('general_customer_country') }}
                                                                            <span v-if="isRequired('country_id')" class="text-danger">*</span>
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
                                                                <div class="col-md-4" v-if="isVisible('city_id')">
                                                                    <div class="form-group position-relative">
                                                                        <label class="control-label">
                                                                            {{ getCompanyKey('general_customer_city') }}
                                                                            <span v-if="isRequired('city_id')" class="text-danger">*</span>
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
                                                                <div class="col-md-4" v-if="isVisible('national_id')">
                                                                    <div class="form-group">
                                                                        <label  class="control-label">
                                                                            {{ getCompanyKey('general_customer_national_id') }}
                                                                            <span v-if="isRequired('national_id')" class="text-danger">*</span>
                                                                        </label>
                                                                        <input
                                                                            type="number"
                                                                            class="form-control"
                                                                            data-edit="9"
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
                                                                <div class="col-md-4" v-if="isVisible('passport_no')">
                                                                    <div class="form-group">
                                                                        <label  class="control-label">
                                                                            {{ getCompanyKey('general_customer_passport_number') }}
                                                                            <span v-if="isRequired('passport_no')" class="text-danger">*</span>
                                                                        </label>
                                                                        <input
                                                                            type="number"
                                                                            class="form-control"
                                                                            data-edit="9"
                                                                            step="0.1"
                                                                            v-model="$v.edit.passport_no.$model"
                                                                            :class="{
                                                                    'is-invalid':$v.edit.passport_no.$error || errors.passport_no,
                                                                    'is-valid':!$v.edit.passport_no.$invalid && !errors.passport_no
                                                                }"
                                                                        />
                                                                        <template v-if="errors.passport_no">
                                                                            <ErrorMessage v-for="(errorMessage,index) in errors.passport_no" :key="index">{{ errorMessage }}</ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr v-if="isVisible('bank_account_id') || isVisible('rp_code') || isVisible('phone') || isVisible('whatsapp') || isVisible('email')" style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                            <div class="row">
                                                                <div class="col-md-4" v-if="isVisible('bank_account_id')">
                                                                    <div class="form-group position-relative">
                                                                        <label class="control-label">
                                                                            {{ getCompanyKey('bank_account') }}
                                                                            <span v-if="isRequired('bank_account_id')" class="text-danger">*</span>
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
                                                                <div class="col-md-4" v-if="isVisible('rp_code')">
                                                                    <div class="form-group">
                                                                        <label  class="control-label">
                                                                            {{ getCompanyKey('general_customer_code') }}
                                                                            <span v-if="isRequired('rp_code')" class="text-danger">*</span>
                                                                        </label>
                                                                        <input
                                                                            type="text"
                                                                            class="form-control"
                                                                            data-edit="9"
                                                                            v-model="$v.edit.rp_code.$model"
                                                                            :class="{
                                                                    'is-invalid':$v.edit.rp_code.$error || errors.rp_code,
                                                                    'is-valid':!$v.edit.rp_code.$invalid && !errors.rp_code
                                                                }"
                                                                        />
                                                                        <template v-if="errors.rp_code">
                                                                            <ErrorMessage v-for="(errorMessage,index) in errors.rp_code" :key="index">{{ errorMessage }}</ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4" v-if="isVisible('phone')">
                                                                    <div class="form-group">
                                                                        <label class="control-label">
                                                                            {{ getCompanyKey('general_customer_phone') }}
                                                                            <span v-if="isRequired('phone')" class="text-danger">*</span>
                                                                        </label>
                                                                        <VuePhoneNumberInput
                                                                            v-model="$v.edit.phone.$model"
                                                                            :default-country-code="edit.code_country"
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
                                                                <div class="col-md-4" v-if="isVisible('whatsapp')">
                                                                    <div class="form-group">
                                                                        <label class="control-label">
                                                                            {{ getCompanyKey('general_customer_whatsapp') }}
                                                                            <span v-if="isRequired('whatsapp')" class="text-danger">*</span>
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
                                                                <div class="col-md-4" v-if="isVisible('email')">
                                                                    <div class="form-group">
                                                                        <label class="control-label">
                                                                            {{ getCompanyKey('general_customer_email') }}
                                                                            <span v-if="isRequired('email')" class="text-danger">*</span>
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
                                                            <hr v-if="isVisible('contact_person') || isVisible('contact_phone')" style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                            <div class="row">
                                                                <div class="col-md-4" v-if="isVisible('contact_person')">
                                                                    <div class="form-group">
                                                                        <label class="control-label">
                                                                            {{ getCompanyKey('general_customer_contact_person') }}
                                                                            <span v-if="isRequired('contact_person')" class="text-danger">*</span>
                                                                        </label>
                                                                        <input
                                                                            type="text"
                                                                            class="form-control"
                                                                            data-edit="9"
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
                                                                <div class="col-md-4" v-if="isVisible('contact_phone')">
                                                                    <div class="form-group">
                                                                        <label  class="control-label">
                                                                            {{ getCompanyKey('general_customer_contact_phones') }}
                                                                            <span v-if="isRequired('contact_phone')" class="text-danger">*</span>
                                                                        </label>
                                                                        <VuePhoneNumberInput
                                                                            v-model="$v.edit.contact_phone.$model"
                                                                            :default-country-code="codeCountry"
                                                                            valid-color="#28a745"
                                                                            error-color="#dc3545"
                                                                            :preferred-countries="['FR', 'EG', 'DE']"
                                                                            @update="updateContractEdit"
                                                                        />
                                                                        <template v-if="errors.contact_phone">
                                                                            <ErrorMessage v-for="(errorMessage,index) in errors.contact_phone" :key="index">{{ errorMessage }}</ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </b-tab>
                                                        <b-tab :title="$t('general.ImageUploads')">
                                                            <div class="row">
                                                                <b-modal
                                                                    id="uploadPhotoTitleCreate"
                                                                    :title="$t('general.ImageUploads')"
                                                                    title-class="font-18"
                                                                    body-class="p-4 "
                                                                    :hide-footer="true"
                                                                    @show="uploadPhotoTitle"
                                                                    @hidden="uploadPhotoTitleHidden"
                                                                >
                                                                    <div class="form-group">
                                                                        <label class="control-label">
                                                                            {{ $t("general.titleFile") }}
                                                                            <span class="text-danger">*</span>
                                                                        </label>
                                                                        <div dir="rtl">
                                                                            <input
                                                                                type="text"
                                                                                class="form-control"
                                                                                data-create="1"
                                                                                v-model="$v.titleFile.$model"
                                                                                :class="{
                                                                        'is-invalid':$v.titleFile.$error || errors.title,
                                                                        'is-valid':!$v.titleFile.$invalid && !errors.title
                                                                    }"
                                                                            />
                                                                        </div>
                                                                        <div v-if="!$v.titleFile.minLength" class="invalid-feedback">{{ $t('general.Itmustbeatleast') }} {{ $v.titleFile.$params.minLength.min }} {{ $t('general.letters') }}</div>
                                                                        <div v-if="!$v.titleFile.maxLength" class="invalid-feedback">{{ $t('general.Itmustbeatmost') }}  {{ $v.titleFile.$params.maxLength.max }} {{ $t('general.letters') }}</div>
                                                                        <template v-if="errors.title">
                                                                            <ErrorMessage v-for="(errorMessage,index) in errors.title" :key="index">{{ errorMessage }}</ErrorMessage>
                                                                        </template>
                                                                    </div>

                                                                    <input accept="image/png, image/gif, image/jpeg, image/jpg" type="file"
                                                                           id="uploadImageEdit" @change.prevent="onImageChanged"
                                                                           class="input-file-upload position-absolute" :class="[
                                                                        'd-none',
                                                                        {
                                                                          'is-invalid': $v.edit.media.$error || errors.media,
                                                                          'is-valid':
                                                                            !$v.edit.media.$invalid && !errors.media,
                                                                        },
                                                                      ]" />

                                                                    <b-button :disabled="!titleFile && $v.titleFile.$error" @click="changePhotoEdit" variant="success" type="button"
                                                                              class="mx-1 font-weight-bold px-3" v-if="!isLoader">
                                                                        {{ $t("general.Add") }}
                                                                    </b-button>
                                                                    <b-button variant="success" class="mx-1" disabled v-else>
                                                                        <b-spinner small></b-spinner>
                                                                        <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                                    </b-button>

                                                                </b-modal>

                                                                <div class="col-md-8 my-1">
                                                                    <!-- file upload -->
                                                                    <div class="row align-content-between" style="width: 100%; height: 100%">
                                                                        <div class="col-12">
                                                                            <div class="d-flex flex-wrap">
                                                                                <div class="dropzone-previews col-4 position-relative mb-2"
                                                                                     v-for="(photo, index) in images">
                                                                                    <div :class="[
                                                                                        'card mb-0 shadow-none border',
                                                                                        images.length - 1 == index
                                                                                          ? 'bg-primary'
                                                                                          : '',
                                                                                      ]">
                                                                                        <div class="p-2">
                                                                                            <div class="row align-items-center">
                                                                                                <div class="col-auto" @click="showPhoto = photo.webp">
                                                                                                    <img data-dz-thumbnail :src="photo.webp"
                                                                                                         class="avatar-sm rounded bg-light"
                                                                                                         @error="src = '../../../../../images/img-1.png'" />
                                                                                                </div>
                                                                                                <div class="col pl-0">
                                                                                                    <a href="javascript:void(0);" :class="[
                                                                                                        'font-weight-bold',
                                                                                                        images.length - 1 == index
                                                                                                          ? 'text-white'
                                                                                                          : 'text-muted',
                                                                                                      ]" data-dz-name>
                                                                                                        {{ photo.title }}
                                                                                                    </a>
                                                                                                </div>
                                                                                                <!-- Button -->
                                                                                                <a href="javascript:void(0);" :class="[
                                                                                                  'btn-danger text-muted dropzone-close',
                                                                                                  $i18n.locale == 'ar'
                                                                                                    ? 'dropzone-close-rtl'
                                                                                                    : '',
                                                                                                ]" data-dz-remove @click.prevent="
                                                                                                          deleteImageCreate(photo.id, index)
                                                                                                        ">
                                                                                                    <i class="fe-x"></i>
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="footer-image col-12">
                                                                            <b-button @click="uploadPhotoTitle" variant="success" type="button"
                                                                                      class="mx-1 font-weight-bold px-3" v-if="!isLoader">
                                                                                {{ $t("general.Add") }}
                                                                            </b-button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="show-dropzone">
                                                                        <img :src="showPhoto" class="img-thumbnail" @error="src = '../../../../../images/img-1.png'" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </b-tab>
                                                    </b-tabs>
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
                                    <th class="text-center" colspan="16">{{ $t('general.notDataFound') }}</th>
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
.dropdown-menu-custom-company.dropdown .dropdown-menu {
    padding: 5px 10px !important;
    overflow-y: scroll;
    height: 300px;
}
.modal-body .card .tabs .tab-content {
    padding: 20px 60px 40px !important;
dding: 5px 10px !important;
    overflow-y: scroll;
    height: 300px;
