<script>
import ErrorMessage from "../widgets/errorMessage";
import loader from "../loader";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";
import translation from "../../helper/translation-mixin";
import {minValue, required, requiredIf} from "vuelidate/lib/validators";
import {formatDateOnly} from "../../helper/startDate";
import adminApi from "../../api/adminAxios";
import Swal from "sweetalert2";
import {dynamicSortString} from "../../helper/tableSort";


/**
 * Advanced Table component
 */
export default {
    name: "documentWithItem",
    props: {
        document_id: {
            default: null,
        },
    },
    mixins: [translation],
    components: {
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
    },
    data() {
        return {
            phone:'',
            per_page: 50,
            search: "",
            debounce: {},
            reservationsPagination: {},
            reservations: [],
            customers: [],
            salesmen: [],
            statuses: [],
            tasks: [],
            serial_number:"",
            document:null,
            relatedDocumentNumbers: [],
            externalSalesman: [],
            relatedDocuments: [],
            sellMethods: [],
            categories: [],
            countries: [],
            packages: [],
            governorates: [],
            enabled3: true,
            isLoader: false,
            create: {
                branch_id: null,
                document_statuse_id: 1,
                related_document_id: null,
                external_salesman_id: null,
                task_id: null,
                related_document_number: null,
                reason:'',
                sell_method_id: null,
                document_id: 9,
                date: this.formatDate(new Date()),
                salesman_id: null,
                customer_id: null,
                total: 0,
                is_stripe: 0,
                is_quotation: 1,
                details: [{
                    category_id: null,
                    governorate_id: null,
                    package_id: null,
                    quantity: 0,
                    price: 0,
                    total: 0,
                    unit_type: "Banners",
                    from: this.formatDate(new Date()),
                    to: this.formatDate(new Date()),
                    panels: []
                }]
            },
            edit: {
                branch_id: null,
                document_statuse_id: null,
                sell_method_id: null,
                document_id: 9,
                date: this.formatDate(new Date()),
                salesman_id: null,
                customer_id: null,
                total: 0,
                is_stripe: 0,
                is_quotation: 1,
                details: [],
                serial_number: ''
            },
            setting: {
                date: true,
                customer_id: true,
                salesman_id: true,
                branch_id: true,
                serial_id: true,
            },
            codeCountry: '',
            filterSetting: [
                'salesman_id',
                'customer_id',
                'branch_id'
            ],
            errors: {},
            branches: [],
            isCheckAll: false,
            checkAll: [],
            is_disabled: false,
            current_page: 1,
            total: 0,
            company_id: null,
            Tooltip: "",
            mouseEnter: null,
            printLoading: true,
            date: new Date(),
            multDate: [{to: new Date(),from: new Date()}],
            multDateEdit: [],
            printObj: {
                id: "printReservation",
            },
            faces: ['A','B','Multi','One-Face'],
            cities: [],
            avenues: [],
            streets: [],
            pans: [],
            locations: [],
        };
    },
    validations: {
        create: {
            media: {},
            date: { required },
            customer_id: { required },
            branch_id: { required },
            salesman_id: { required },
            document_statuse_id: {  },
            reason: {},
            related_document_id: {},
            external_salesman_id: {},
            task_id: {},
            related_document_number: {},
            sell_method_id: { required },
            document_id: { required },
            total: { required },
            is_stripe: { required },
            details: {
                required,
                $each: {
                    category_id: { required: requiredIf(function (model) {
                            return this.create.is_stripe == 0;
                        }) },
                    governorate_id: { required: requiredIf(function (model) {
                            return this.create.is_stripe == 0;
                        }) },
                    package_id: { required: requiredIf(function (model) {
                            return this.create.is_stripe == 1;
                        }) },
                    quantity: { required: requiredIf(function (model) {
                            return this.create.is_stripe == 0;
                        }), minValue: minValue(0) },
                    from: { required },
                    to: { required },
                    unit_type: { required},
                    price: { required, minValue: minValue(0)},
                    total: { required},
                    panels: {
                        required,
                        $each: {
                            panel_id: {required}
                        }
                    }
                }
            }
        },
        edit: {
            date: { required },
            customer_id: { required },
            branch_id: { required },
            salesman_id: { required },
            document_statuse_id: {  },
            sell_method_id: { required },
            document_id: { required },
            total: { required },
            is_stripe: { required },
            details: {
                required,
                $each: {
                    category_id: { required: requiredIf(function (model) {
                            return this.edit.is_stripe == 0;
                        }) },
                    governorate_id: { required: requiredIf(function (model) {
                            return this.edit.is_stripe == 0;
                        }) },
                    package_id: { required: requiredIf(function (model) {
                            return this.edit.is_stripe == 1;
                        }) },
                    quantity: { required: requiredIf(function (model) {
                            return this.edit.is_stripe == 0;
                        }), minValue: minValue(0) },
                    from: { required },
                    to: { required },
                    price: { required, minValue: minValue(0)},
                    panels: {
                        required,
                        $each: {
                            panel_id: {required}
                        }
                    }
                }
            },
            serial_number: {required}
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
                this.reservations.forEach((el) => {
                    if (!this.checkAll.includes(el.id)) {
                        this.checkAll.push(el.id);
                    }
                });
            } else {
                this.checkAll = [];
            }
        },
        'create.is_stripe': {
            handler: function (after, before) {
                // Changes detected. Do work...
                if(this.create.is_stripe == 0){
                    this.cities= [{cities: []}];
                    this.avenues= [{avenues: []}];
                    this.streets = [{streets: []}];
                    this.pans = [{pans: []}];
                    this.create.details = [{
                        category_id: null,
                        governorate_id: null,
                        quantity: 0,
                        price: 0,
                        from: this.formatDate(new Date()),
                        to: this.formatDate(new Date()),
                        total: 0,
                        unit_type: "Banners",
                        panels: []
                    }];
                }else {
                    this.create.details = [{
                        package_id: null,
                        price: 0,
                        from: this.formatDate(new Date()),
                        to: this.formatDate(new Date()),
                        panels: []
                    }];
                    if(this.packages.length == 0) this.getPackage();
                }
                this.multDate = [{to: new Date(),from: new Date()}];
                this.create.total = 0;
            },
            deep: true
        },
        'edit.is_stripe': {
            handler: function (after, before) {
                // Changes detected. Do work...
                if(this.edit.is_stripe == 0){
                    this.cities= [{cities: []}];
                    this.avenues= [{avenues: []}];
                    this.streets = [{streets: []}];
                    this.pans = [{pans: []}];
                    this.edit.details = [{
                        category_id: null,
                        governorate_id: null,
                        quantity: 0,
                        price: 0,
                        from: this.formatDate(new Date()),
                        to: this.formatDate(new Date()),
                        panels: []
                    }];
                }else {
                    this.edit.details = [{
                        package_id: null,
                        price: 0,
                        from: this.formatDate(new Date()),
                        to: this.formatDate(new Date()),
                        panels: []
                    }];
                    if(this.packages.length == 0) this.getPackage();
                }
                this.multDate = [{to: new Date(),from: new Date()}];
                this.edit.total = 0;
            },
            deep: true
        }
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.getDocumentData();
        this.getData();
        this.$store.dispatch('locationIp/getIp');
    },
    methods: {
        addNewField(index){
            if(this.create.is_stripe == 0){
                this.cities.push({cities: []});
                this.avenues.push({avenues: []});
                this.streets.push({streets: []});
                this.pans.push({pans: []});
                this.create.details.push({
                    category_id: null,
                    governorate_id: null,
                    quantity: 0,
                    price: 0,
                    from: this.formatDate(new Date()),
                    to: this.formatDate(new Date()),
                    panels: []
                });
            }else {
                this.create.details.push({
                    package_id: null,
                    price: 0,
                    from: this.formatDate(new Date()),
                    to: this.formatDate(new Date()),
                    panels: []
                });
            }
            this.multDate.push({to: new Date(),from: new Date()});
            this.changeValue();
        },
        removeNewField(index){
            if(this.create.details.length > 1){
                this.create.details.splice(index,1);
                this.multDate.splice(index,1);
                if(this.create.is_stripe == 0){
                    this.cities.splice(index,1);
                    this.avenues.splice(index,1);
                    this.streets.splice(index,1);
                    this.pans.splice(index,1);
                }
            }
        },
        addNewFieldEdit(index){
            if(this.edit.is_stripe == 0){
                this.cities.push({cities: []});
                this.avenues.push({avenues: []});
                this.streets.push({streets: []});
                this.pans.push({pans: []});
                this.edit.details.push({
                    category_id: null,
                    governorate_id: null,
                    quantity: 0,
                    price: 0,
                    from: this.formatDate(new Date()),
                    to: this.formatDate(new Date()),
                });
            }else {
                this.edit.details.push({
                    package_id: null,
                    price: 0,
                    from: this.formatDate(new Date()),
                    to: this.formatDate(new Date()),
                });
            }
            this.changeValueEdit();
            this.multDateEdit.push({to: new Date(),from: new Date()});
        },
        removeNewFieldEdit(index){
            if(this.edit.details.length > 1){
                this.edit.details.splice(index,1);
                this.multDateEdit.splice(index,1);
                if(this.create.is_stripe == 0){
                    this.cities.splice(index,1);
                    this.avenues.splice(index,1);
                    this.streets.splice(index,1);
                    this.pans.splice(index,1);
                }
            }
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
            index = this.filterSetting.indexOf("salesman_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "salesman.name" : "salesman.name_e";
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
            adminApi
                .get(
                    `boards-rent/orders?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}&is_quotation=1`
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
                index = this.filterSetting.indexOf("salesman_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "salesman.name" : "salesman.name_e";
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

                adminApi
                    .get(
                        `boards-rent/orders?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}&is_quotation=1`
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
        /**
         *  delete screen button
         */
        deleteScreenButton(id, index) {
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
                            .post(`boards-rent/orders/bulk-delete`, { ids: id })
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
                            .delete(`boards-rent/orders/${id}`)
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
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.create = {
                branch_id: null,
                document_statuse_id: null,
                reason:'',
                related_document_id: null,
                external_salesman_id: null,
                task_id: null,
                related_document_number: null,
                sell_method_id: null,
                document_id: 9,
                date: this.formatDate(new Date()),
                salesman_id: null,
                customer_id: null,
                total: 0,
                is_stripe: 0,
                is_quotation: 1,
                details: [{
                    category_id: null,
                    governorate_id: null,
                    quantity: 0,
                    price: 0,
                    total: 0,
                    unit_type: "Banners",
                    from: this.formatDate(new Date()),
                    to: this.formatDate(new Date()),
                    panels: []
                }]
            };
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            this.date = new Date();
            this.multDate = [{to: new Date(),from: new Date()}];
            this.cities= [{cities: []}];
            this.avenues= [{avenues: []}];
            this.streets = [{streets: []}];
            this.pans = [{pans: []}];
            this.locations = [];
            this.create = {
                branch_id: null,
                document_statuse_id: null,
                reason:'',
                related_document_id: null,
                external_salesman_id: null,
                task_id: null,
                related_document_number: null,
                sell_method_id: null,
                document_id: 9,
                date: this.formatDate(new Date()),
                salesman_id: null,
                customer_id: null,
                is_quotation: 1,
                total: 0,
                is_stripe: 0,
                details: [{
                    category_id: null,
                    governorate_id: null,
                    quantity: 0,
                    price: 0,
                    total: 0,
                    unit_type: "Banners",
                    from: this.formatDate(new Date()),
                    to: this.formatDate(new Date()),
                    panels: []
                }]
            };
            this.phone = '';
            this.codeCountry = this.$store.getters["locationIp/countryCode"];
            await this.getBranches();
            await this.getSalesmen();
            await this.getStatus('create');
            await this.getExternalSalesmen();
            await this.getSellmethod();
            await this.getCategory();
            await this.getGovernorate();

            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  create screen
         */
        resetForm() {
            this.multDate = [{to: new Date(),from: new Date()}];
            this.locations = [];
            this.cities= [{cities: []}];
            this.avenues= [{avenues: []}];
            this.streets = [{streets: []}];
            this.pans = [{pans: []}];
            this.create =  {
                branch_id: null,
                document_statuse_id: null,
                reason:'',
                related_document_id: null,
                external_salesman_id: null,
                related_document_number: null,
                task_id: null,
                sell_method_id: null,
                document_id: 9,
                date: this.formatDate(new Date()),
                salesman_id: null,
                customer_id: null,
                total: 0,
                is_stripe: 0,
                is_quotation: 1,
                details: [{
                    category_id: null,
                    governorate_id: null,
                    quantity: 0,
                    price: 0,
                    total: 0,
                    unit_type: "Banners",
                    from: this.formatDate(new Date()),
                    to: this.formatDate(new Date()),
                    panels: []
                }]
            };
            this.date = new Date();
            this.start_date = new Date();
            this.codeCountry = this.$store.getters["locationIp/countryCode"];
            this.end_date = new Date();
            this.is_disabled = false;
            this.phone = '';
            this.$nextTick(() => {
                this.$v.$reset();
            });
        },
        AddSubmit() {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                adminApi
                    .post(`boards-rent/orders`, {
                        ...this.create
                    })
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
         *  edit screen
         */
        editSubmit(id) {
            this.$v.edit.$touch();

            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .put(`boards-rent/orders/${id}`, {
                        ...this.edit
                    })
                    .then((res) => {
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
         *  get workflows
         */
        async getDocumentData() {
            this.isLoader = true;
            await adminApi
                .get(`/document/${this.document_id}`)
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
        async getStatus(type='create'){
            this.isLoader = true;

            await adminApi
                .get(`/document-statuses`)
                .then((res) => {
                    let l = res.data.data;
                    this.statuses = l;
                    if (type=='create')
                    {
                        this.create.document_statuse_id=1;
                    }else {
                        this.edit.document_statuse_id=1;
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
        async getBranches() {
            this.isLoader = true;
            await adminApi
                .get(`/branches?document_id=${this.document_id}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.branches = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        getSerialNumber(e)
        {
            let shortYear =new Date().getFullYear();
            let twoDigitYear = shortYear.toString().substr(-2);
            let branch = this.branches.find((row) => e == row.id);
            this.serial_number = `${twoDigitYear}-${branch.id}-${this.document_id}-${branch.serials[0].perfix}`;
        },

        async getSellmethod() {
            this.isLoader = true;
            await adminApi
                .get(`/boards-rent/sell-methods?company_id=${this.company_id}`)
                .then((res) => {
                    let l = res.data.data;
                    this.sellMethods = l;
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
        async getSalesmen() {
            this.isLoader = true;
            await adminApi
                .get(`/employees?is_salesman=1&customer_handel=1`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.salesmen = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getCustomerSalesman(e)
        {
            let employee_id = e;
            if (employee_id)
            {
                await this.getCustomers(employee_id);
                await this.getTaskNumber();
            }
        },

        async getCustomers(employee_id=null) {
            this.isLoader = true;
            await adminApi
                .get(`/general-customer?employee_id=${employee_id}`)
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
        async getTasksCreate()
        {
            if (this.create.customer_id&&this.create.employee_id)
            {
                await this.getTaskNumber(this.create.customer_id,this.create.employee_id);
            }
        },
        async getTaskNumber(customer_id,employee_id) {
            this.isLoader = true;
            await adminApi
                .get(`/tasks?customer_id=${customer_id}&employee_id=${employee_id}`)
                .then((res) => {
                    let l = res.data.data;
                    this.tasks = l;
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
        async getExternalSalesmen() {
            this.isLoader = true;
            await adminApi
                .get(`/external-salesmen?company_id=${this.company_id}`)
                .then((res) => {
                    let l = res.data.data;
                    this.externalSalesman = l;
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




        async getCategory(index = null) {
            this.isLoader = true;
            await adminApi
                .get(`/categories`)
                .then((res) => {
                    let l = res.data.data;
                    this.categories = l;
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
        async getPackage() {
            this.isLoader = true;
            await adminApi
                .get(`/boards-rent/packages`)
                .then((res) => {
                    let l = res.data.data;
                    this.packages = l;
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
        async getGovernorate() {
            this.isLoader = true;
            await adminApi
                .get(`/governorates`)
                .then((res) => {
                    let l = res.data.data;
                    this.governorates = l;
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
        async getCity(index,ind = null) {
            this.isLoader = true;
            let governorate = this.create.details[index].governorate_id;
            if(ind){
                this.locations[index].locations[ind].city_id = null;
                this.locations[index].locations[ind].avenue_id = null;
                this.locations[index].locations[ind].street_id = null;
                this.cities[index].cities[index].cities = [];
                this.avenues[index].avenues[index].avenues = [];
                this.streets[index].streets[index].streets = [];
            }

            await adminApi
                .get(`/cities?columns[0]=governorate.id&search=${governorate}`)
                .then((res) => {
                    let l = res.data.data;
                    if(!ind){
                        this.cities[index].cities.forEach((e,indexPen) => {
                            e.cities = l;
                        });
                    }else {
                        this.cities[index].cities[index].cities = l;
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
        async getAvnue(index,ind) {
            this.isLoader = true;
            let city = this.locations[index].locations[ind].city_id;
            this.locations[index].locations[ind].avenue_id = null;
            this.locations[index].locations[ind].street_id = null;
            this.avenues[index].avenues[ind].avenues = [];
            this.streets[index].streets[ind].streets = [];

            await adminApi
                .get(`/avenues?columns[0]=city.id&search=${city}`)
                .then((res) => {
                    let l = res.data.data;
                    this.avenues[index].avenues[ind].avenues = l;
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
        async getStreet(index,ind) {
            this.isLoader = true;
            let avenue = this.locations[index].locations[ind].avenue_id;
            this.locations[index].locations[ind].street_id = null;
            this.streets[index].streets[ind].streets = [];

            await adminApi
                .get(`/streets?columns[0]=avenue.id&search=${avenue}`)
                .then((res) => {
                    let l = res.data.data;
                    this.streets[index].streets[ind].streets = l;
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
        async getPanel(index,ind = null) {
            this.isLoader = true;
            let category_id = this.create.details[index].category_id;
            let governorate_id = this.create.details[index].governorate_id;
            let city_id =  ind ? this.locations[index].locations[ind].city_id : '';
            let avenue_id = ind ? this.locations[index].locations[ind].avenue_id : '';
            let face = ind ? this.locations[index].locations[ind].face : '';
            let street_id = ind ? this.locations[index].locations[ind].street_id : '';

            await adminApi
                .get(
                    `/boards-rent/panels?category_id=${category_id}&governorate_id=${governorate_id}&city_id=${city_id}&avenue_id=${avenue_id}&street_id=${street_id}&face=${face}`
                )
                .then((res) => {
                    let l = res.data.data;
                    if(!ind){
                        this.pans[index].pans.forEach((e,indexPen) => {
                            this.pans[index].pans[indexPen].pans = l;
                        });
                    }else {
                        this.pens[index].pens[ind].pans = l;
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
        /**
         *   show Modal (edit)
         */
        async resetModalEdit(id) {
            this.locations = [];
            this.cities = [];
            this.avenues = [];
            this.streets = [];
            this.pans = [];
            let reservation = this.reservations.find((e) => id == e.id);
            this.date = new Date(reservation.date);
            this.edit.serial_number = reservation.serial_number;
            this.edit.date = reservation.date;
            await this.getBranches();
            this.edit.branch_id = reservation.branch.id;
            await this.getSalesmen();
            this.edit.salesman_id = reservation.salesman.id;
            await this.getSellmethod();
            this.edit.sell_method_id = reservation.sell_method.id;
            await this.getStatus();
            await this.getExternalSalesmen();
            this.edit.document_statuse_id = reservation.customer.id;
            await this.getCustomers();
            this.edit.customer_id = reservation.status.id;
            this.edit.is_stripe = reservation.is_stripe;
            if(reservation.is_stripe == 1){
                await this.getPackage();
            }
            else {
                await this.getGovernorate();
                await this.getCategory();
            }
            reservation.order_details.forEach(  (e,index) => {
                this.multDateEdit.push({to: new Date(e.to),from: new Date(e.from)});
                if(reservation.is_stripe == 0){
                    this.cities.push({cities: []});
                    this.avenues.push({avenues: []});
                    this.streets.push({streets: []});
                    this.pans.push({pans: []});
                    this.locations.push({locations: []});
                    this.edit.details.push({
                        category_id: e.category_id,
                        governorate_id: e.governorate_id,
                        quantity: e.quantity,
                        price: e.price,
                        from: this.formatDate(e.from),
                        to: this.formatDate(e.to),
                        panels: []
                    });
                }else {
                    this.edit.details.push({
                        package_id: e.package_id,
                        price: e.price,
                        from: this.formatDate(e.from),
                        to: this.formatDate(e.to),
                        panels: []
                    });
                }
                this.edit.total += e.price;
            });
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.errors = {};
            this.edit = {
                branch_id: null,
                document_statuse_id: null,
                sell_method_id: null,
                document_id: 9,
                date: this.formatDate(new Date()),
                salesman_id: null,
                customer_id: null,
                total: 0,
                is_stripe: 0,
                is_quotation: 1,
                details: [],
                serial_number: ''
            };
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
                    .get(`boards-rent/orders/logs/${id}`)
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
                    XLSX.writeFile(wb, fn || (('Reservation' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },
        async showPanel(index){
            let panel = this.create.details[index].panels.some((e) => e.panel_id);
            this.$bvModal.show(`create-panel-quotation-${index}`);
            if(!panel){
                await this.getPanel(index);
            }
        },
        async showPanelEdit(index){
            let panel = this.edit.details[index].panels.some((e) => e.panel_id);
            this.$bvModal.show(`edit-panel-quotation-${index}`);
            if(!panel){
                await this.getPanel(index);
            }
        },
        totalCreate(index)
        {
            this.create.details[index].total = this.create.details[index].quantity * this.create.details[index].price;
        }
    }
};
</script>

<template>
    <div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title" v-if="document">{{  $i18n.locale == "ar" ? $t('general.table') + ' ' + document.name : document.name_e+ ' ' +$t('general.table')  }}</h4>
                            <h4 class="header-title" v-else>{{ $t('general.table') }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting"
                                                         value="salesman_id"
                                                         class="mb-1">{{ getCompanyKey("sale_man") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting"
                                                         value="customer_id"
                                                         class="mb-1">{{ getCompanyKey("customer") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting"
                                                         value="branch_id"
                                                         class="mb-1">{{ getCompanyKey("branch") }}
                                        </b-form-checkbox>
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
                                <b-button v-b-modal.create variant="primary" class="btn-sm mx-1 font-weight-bold">
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printReservation'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <button class="custom-btn-dowonload" @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                                            v-if="checkAll.length == 1">
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length > 1"
                                            @click.prevent="deleteScreenButton(checkAll)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length == 1"
                                            @click.prevent="deleteScreenButton(checkAll[0])">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!--  end one delete  -->
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
                                        <b-form-checkbox v-model="setting.date" class="mb-1">{{
                                            getCompanyKey("reservation_date") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="setting.customer_id" class="mb-1">{{
                                            getCompanyKey("customer") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="setting.salesman_id" class="mb-1">
                                            {{ getCompanyKey("sale_man") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="setting.branch_id" class="mb-1">
                                            {{ getCompanyKey("branch") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="setting.serial_id" class="mb-1">
                                            {{ getCompanyKey("reservation_serial") }}
                                        </b-form-checkbox>

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
                                            <input type="text" @keyup.enter="getDataCurrentPage()" v-model="current_page"
                                                   class="pagination-current-page" />
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

                        <!--  create   -->
                        <b-modal dialog-class="modal-full-width" id="create"
                                 :title="$t('general.addNew')"
                                 title-class="font-18" body-class="p-4 " :hide-footer="true" @show="resetModal"
                                 @hidden="resetModalHidden">
                            <form>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{$t('general.serial_number')}}
                                                    </label>
                                                    <input
                                                        :disabled="true"
                                                        type="text"
                                                        class="form-control"
                                                        v-model="serial_number"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6 mb-3 d-flex justify-content-end">
                                        <b-button variant="primary" type="button" class="font-weight-bold px-2 mx-1">
                                            {{ $t("general.print") }}
                                            <i class="fe-printer"></i>
                                        </b-button>
                                        <b-button variant="primary" type="button" class="font-weight-bold px-2 mx-1">
                                            {{ $t("general.printBannerGoogleMap") }}
                                            <i class="fe-printer"></i>
                                        </b-button>
                                        <b-button variant="success" :disabled="!is_disabled" @click.prevent="resetForm" type="button"
                                                  :class="['font-weight-bold px-2 mx-1', is_disabled ? 'mx-2' : '']">
                                            {{ $t("general.AddNewRecord") }}
                                        </b-button>
                                        <template v-if="!is_disabled">
                                            <b-button variant="success" type="submit" class="mx-1" v-if="!isLoader" @click.prevent="AddSubmit">
                                                {{ $t("general.Add") }}
                                            </b-button>
                                            <b-button variant="success" class="mx-1" disabled v-else>
                                                <b-spinner small></b-spinner>
                                                <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                            </b-button>

                                        </template>
                                        <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(`create`)">
                                            {{ $t("general.Cancel") }}
                                        </b-button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ $t('general.Status') }}
                                            </label>
                                            <multiselect v-model="create.document_statuse_id"
                                                         :options="statuses.map((type) => type.id)"
                                                         :custom-label="(opt) => $i18n.locale == 'ar'? statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e">
                                            </multiselect>
                                            <template v-if="errors.document_statuse_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.document_statuse_id" :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{$t('general.reason')}}
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control englishInput"
                                                v-model="$v.create.reason.$model"
                                                :class="{
                                                    'is-invalid': $v.create.reason.$error || errors.reason,
                                                    'is-valid': !$v.create.reason.$invalid && !errors.reason,
                                                  }"
                                            />
                                            <template v-if="errors.reason">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.reason" :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">{{ $t('general.Branch') }} <span class="text-danger">*</span></label>

                                            <multiselect
                                                @input="getSerialNumber"
                                                v-model="create.branch_id"
                                                :options="branches.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar'? branches.find((x) => x.id == opt).name: branches.find((x) => x.id == opt).name_e"
                                                :class="{'is-invalid': $v.create.branch_id.$error || errors.branch_id,}"
                                            >
                                            </multiselect>
                                            <div v-if="!$v.create.branch_id.required" class="invalid-feedback">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>

                                            <template v-if="errors.branch_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.branch_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.Date') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker type="date" v-model="date" defaultValue></date-picker>
                                            <template v-if="errors.date">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.date" :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{$t('general.RelatedDocument')}}</label>
                                            <multiselect
                                                v-model="create.related_document_id"
                                                :options="relatedDocuments.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? relatedDocuments.find((x) => x.id == opt).name : relatedDocuments.find((x) => x.id == opt).name_e"
                                                :class="{'is-invalid': $v.create.related_document_id.$error || errors.related_document_id, }">
                                            </multiselect>

                                            <template v-if="errors.related_document_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.related_document_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{$t('general.RelatedDocumentNumber')}}</label>
                                            <multiselect
                                                v-model="create.related_document_number"
                                                :options="relatedDocumentNumbers.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? relatedDocumentNumbers.find((x) => x.id == opt).name : relatedDocumentNumbers.find((x) => x.id == opt).name_e"
                                                :class="{'is-invalid': $v.create.related_document_number.$error || errors.related_document_number, }">
                                            </multiselect>

                                            <template v-if="errors.related_document_number">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.related_document_number"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">{{ $t('general.sellMethod') }} <span class="text-danger">*</span></label>
                                            <multiselect
                                                v-model="create.sell_method_id"
                                                :options="sellMethods.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? sellMethods.find((x) => x.id == opt).name: sellMethods.find((x) => x.id == opt).name_e"
                                                :class="{'is-invalid': $v.create.sell_method_id.$error || errors.sell_method_id,}"
                                            >
                                            </multiselect>
                                            <div v-if="!$v.create.sell_method_id.required" class="invalid-feedback">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.sell_method_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.sell_method_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">{{ $t('general.documentSalesmen') }} <span class="text-danger">*</span></label>

                                            <multiselect
                                                @input="getCustomerSalesman"
                                                v-model="create.salesman_id"
                                                :options="salesmen.map((type) => type.id)"
                                                :custom-label=" (opt) => $i18n.locale == 'ar' ? salesmen.find((x) => x.id == opt).name: salesmen.find((x) => x.id == opt).name_e"
                                                :class="{'is-invalid':$v.create.salesman_id.$error || errors.salesman_id,}"
                                            >
                                            </multiselect>
                                            <div v-if="!$v.create.salesman_id.required" class="invalid-feedback">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.salesman_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.button_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">{{ $t('general.documentCustomer') }} <span class="text-danger">*</span></label>
                                            <multiselect
                                                @input="getTasksCreate"
                                                v-model="create.customer_id"
                                                :options="customers.map((type) => type.id)"
                                                :custom-label="(opt) =>$i18n.locale == 'ar' ? customers.find((x) => x.id == opt).name: customers.find((x) => x.id == opt).name_e"
                                                :class="{'is-invalid':$v.create.customer_id.$error || errors.customer_id,}"
                                            >
                                            </multiselect>
                                            <div v-if="!$v.create.customer_id.required" class="invalid-feedback">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>

                                            <template v-if="errors.customer_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.customer_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{$t('general.taskNumber')}}</label>
                                            <multiselect
                                                v-model="create.task_id"
                                                :options="tasks.map((type) => type.id)"
                                                :custom-label="(opt) => tasks.find((x) => x.id == opt).task_title"
                                                :class="{'is-invalid': $v.create.task_id.$error || errors.task_id, }">
                                            </multiselect>

                                            <template v-if="errors.task_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.task_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{$t('general.externalSalesmen')}}</label>
                                            <multiselect
                                                v-model="create.external_salesman_id"
                                                :options="externalSalesman.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? externalSalesman.find((x) => x.id == opt).name : externalSalesman.find((x) => x.id == opt).name_e"
                                                :class="{'is-invalid': $v.create.external_salesman_id.$error || errors.external_salesman_id, }">
                                            </multiselect>

                                            <template v-if="errors.external_salesman_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.external_salesman_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="page-content">
                                            <div class="px-0">
                                                <div class="row mt-4">
                                                    <div class="col-12 col-lg-12">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="text-center text-150">
                                                                    <i style="font-size:20px"
                                                                       class="fa fa-book fa-2x text-success-m2 mr-1"></i>
                                                                    <span class="text-default-d3" v-if="document"> {{ $i18n.locale == 'ar' ?  document.name: document.name_e }}</span>
                                                                    <span class="text-default-d3" v-else> document</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- .row -->
                                                        <hr class="row brc-default-l1 mx-n1 mb-4" />
                                                        <div class="mt-4">
                                                            <div
                                                                class="row text-600 text-white bgc-default-tp1 py-25">
                                                                <div class="col-2">{{ $t('general.category') }}</div>
                                                                <div class="col-1">{{ $t('general.governorate') }}</div>
                                                                <div class="col-1">{{ $t('general.from_date') }}</div>
                                                                <div class="col-1">{{ $t('general.to_date') }}</div>
                                                                <div class="col-1">{{ $t('general.Unit_type') }}</div>
                                                                <div class="col-1">{{ $t('general.quantity') }}</div>
                                                                <div class="col-2">{{ $t('general.PricePerUnit') }}</div>
                                                                <div class="col-2">{{ $t('general.Total') }}</div>
                                                                <div class="col-1">{{ $t("general.Break") }}</div>
                                                            </div>
                                                            <div
                                                                v-for="(item, index) in create.details"
                                                                class="text-95  text-secondary-d3"
                                                            >
                                                                <!--  create panels   -->
                                                                <b-modal
                                                                    :id="`create-panel-quotation-${index}`"
                                                                    :title="getCompanyKey('boardRent_panel_create_form')"
                                                                    title-class="font-18"
                                                                    body-class="p-4 "
                                                                    dialog-class="modal-full-width"
                                                                    :hide-footer="true"
                                                                >
                                                                    <template v-for="(item, ind) in item.panels">
                                                                        <div class="row" :key="ind">
                                                                            <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <label class="control-label">
                                                                                        {{ getCompanyKey("boardRent_panel_city") }}
                                                                                        <span class="text-danger">*</span>
                                                                                    </label>
                                                                                    <multiselect
                                                                                        v-model="locations[index].locations[ind].city_id"
                                                                                        :options="cities[index].cities[ind].cities.map((type) => type.id)"
                                                                                        :custom-label="(opt) => $i18n.locale == 'ar' ? cities[index].cities[ind].cities.find((x) => x.id == opt).name : cities[index].cities[ind].cities.find((x) => x.id == opt).name_e"
                                                                                    >
                                                                                    </multiselect>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <label class="control-label">
                                                                                        {{ getCompanyKey("boardRent_panel_avenue") }}
                                                                                        <span class="text-danger">*</span>
                                                                                    </label>
                                                                                    <multiselect
                                                                                        v-model="locations[index].locations[ind].avenue_id"
                                                                                        :options="avenues[index].avenues[ind].avenues.map((type) => type.id)"
                                                                                        :custom-label="(opt) => $i18n.locale == 'ar' ? avenues[index].avenues[ind].avenues.find((x) => x.id == opt).name : avenues[index].avenues[ind].avenues.find((x) => x.id == opt).name_e"
                                                                                    >
                                                                                    </multiselect>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <label class="control-label">
                                                                                        {{ getCompanyKey("boardRent_panel_street") }}
                                                                                        <span class="text-danger">*</span>
                                                                                    </label>
                                                                                    <multiselect
                                                                                        v-model="locations[index].locations[ind].street_id"
                                                                                        :options="streets[index].streets[ind].streets.map((type) => type.id)"
                                                                                        :custom-label="(opt) => $i18n.locale == 'ar' ? streets[index].streets[ind].streets.find((x) => x.id == opt).name : streets[index].streets[ind].streets.find((x) => x.id == opt).name_e"
                                                                                    >
                                                                                    </multiselect>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <label class="control-label">
                                                                                        {{ getCompanyKey("boardRent_panel_face") }}
                                                                                        <span class="text-danger">*</span>
                                                                                    </label>
                                                                                    <multiselect
                                                                                        v-model="locations[index].locations[ind].face"
                                                                                        :options="faces"
                                                                                    >
                                                                                    </multiselect>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label>{{ $t('general.panel') }}</label>
                                                                                    <multiselect
                                                                                        v-model="$v.create.details.$each[index].panels.$each[ind].panel_id.$model"
                                                                                        :options="pans[index].pans[ind].pans.map((type) => type.id)" :custom-label="
                                                                                            (opt) =>
                                                                                              $i18n.locale == 'ar'
                                                                                                ? pans[index].pans[ind].pans.find((x) => x.id == opt).name
                                                                                                : pans[index].pans[ind].pans.find((x) => x.id == opt).name_e
                                                                                          ">
                                                                                    </multiselect>
                                                                                    <div v-if="$v.create.details.$each[index].panels.$each[ind].panel_id.$error" class="text-danger">
                                                                                        {{ $t("general.fieldIsRequired") }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </template>
                                                                </b-modal>
                                                                <!--  /create panels   -->

                                                                <div class="row mb-2 mb-sm-0 py-25" :class="index % 2 == 0 ? 'bgc-default-l4' : ''">
                                                                    <div class="col-2">
                                                                        <multiselect
                                                                            :id="`create-${index}-1`"
                                                                            v-model="$v.create.details.$each[index].category_id.$model"
                                                                            :options="categories.map((type) => type.id)"
                                                                            :custom-label="(opt) => $i18n.locale == 'ar' ? categories.find((x) => x.id == opt).name: categories.find((x) => x.id == opt).name_e"
                                                                            :class="{
                                                                                'is-invalid':
                                                                                    $v.create.details.$each[index].category_id.$error || errors.category_id,
                                                                            }">
                                                                            >
                                                                        </multiselect>
                                                                        <div v-if="!$v.create.details.$each[index].category_id.required"
                                                                             class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>
                                                                        <template v-if="errors.category_id">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.category_id"
                                                                                :key="index">{{ errorMessage
                                                                                }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                    <div class="col-1 p-0">
                                                                        <multiselect
                                                                            :id="`create-${index}-3`"
                                                                            v-model="$v.create.details.$each[index].governorate_id.$model"
                                                                            :options="governorates.map((type) => type.id)"
                                                                            :custom-label="(opt) => $i18n.locale == 'ar'? governorates.find((x) => x.id == opt).name : governorates.find((x) => x.id == opt).name_e"
                                                                            :class="{'is-invalid':$v.create.details.$each[index].governorate_id.$error || errors.governorate_id,}"
                                                                        >
                                                                        </multiselect>
                                                                        <div v-if="!$v.create.details.$each[index].governorate_id.required"
                                                                             class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>
                                                                        <template v-if="errors.governorate_id">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.governorate_id"
                                                                                :key="index">
                                                                                {{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                    <div class="col-1 p-0">
                                                                        <div class="form-group">
                                                                            <date-picker
                                                                                type="date" v-model="multDate[index].from" defaultValue
                                                                                confirm
                                                                                :class="{'is-invalid':$v.create.details.$each[index].from.$error || errors.from, }"
                                                                            ></date-picker>
                                                                            <div v-if="!$v.create.details.$each[index].from.required"
                                                                                 class="invalid-feedback">
                                                                                {{ $t("general.fieldIsRequired") }}
                                                                            </div>
                                                                            <template v-if="errors.from">
                                                                                <ErrorMessage v-for="(errorMessage, index) in errors.from"
                                                                                              :key="index">{{
                                                                                    errorMessage }}
                                                                                </ErrorMessage>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-1 p-0">
                                                                        <div class="form-group">
                                                                            <date-picker
                                                                                type="date"
                                                                                v-model="multDate[index].to"
                                                                                defaultValue
                                                                                :class="{ 'is-invalid': $v.create.details.$each[index].to.$error || errors.from,}"
                                                                            ></date-picker>
                                                                            <div v-if="!$v.create.details.$each[index].to.required"
                                                                                 class="invalid-feedback">
                                                                                {{ $t("general.fieldIsRequired") }}
                                                                            </div>
                                                                            <template v-if="errors.to">
                                                                                <ErrorMessage v-for="(errorMessage, index) in errors.to"
                                                                                              :key="index">
                                                                                    {{
                                                                                    errorMessage }}
                                                                                </ErrorMessage>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-1 p-0">
                                                                        <input
                                                                            :id="`create-${index}-4`"
                                                                            v-model="$v.create.details.$each[index].unit_type.$model"
                                                                            class="form-control"
                                                                            type="text"
                                                                            :class="{
                                                                                'is-invalid': $v.create.details.$each[index].unit_type.$error || errors.unit_type,
                                                                                'is-valid': !$v.create.details.$each[index].unit_type.$invalid && !errors.unit_type,
                                                                            }"
                                                                        />
                                                                        <div v-if="!$v.create.details.$each[index].unit_type.required"
                                                                             class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-1 p-0">
                                                                        <input
                                                                            @input="totalCreate(index)"
                                                                            :id="`create-${index}-4`"
                                                                            v-model.number="$v.create.details.$each[index].quantity.$model"
                                                                            class="form-control"
                                                                            type="number"
                                                                            :class="{'is-invalid': $v.create.details.$each[index].quantity.$error || errors.quantity,}"
                                                                        />
                                                                        <div v-if="!$v.create.details.$each[index].quantity.required"
                                                                             class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <input
                                                                            @input="totalCreate(index)"
                                                                            :id="`create-${index}-4`"
                                                                            v-model.number="$v.create.details.$each[index].price.$model"
                                                                            class="form-control" step="any"
                                                                            type="number"
                                                                            :class="{
                                                                                'is-invalid': $v.create.details.$each[index].price.$error || errors.price,
                                                                                'is-valid': !$v.create.details.$each[index].price.$invalid && !errors.price,
                                                                            }"
                                                                        />
                                                                        <div v-if="!$v.create.details.$each[index].price.required"
                                                                             class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <input
                                                                            :id="`create-${index}-4`"
                                                                            :disabled="true"
                                                                            v-model.number="$v.create.details.$each[index].total.$model"
                                                                            class="form-control" step="any"
                                                                            type="number"
                                                                            :class="{
                                                                                'is-invalid': $v.create.details.$each[index].total.$error || errors.total,
                                                                                'is-valid': !$v.create.details.$each[index].total.$invalid && !errors.total,
                                                                            }"
                                                                        />
                                                                        <div v-if="!$v.create.details.$each[index].total.required"
                                                                             class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-1">
                                                                        <button
                                                                            type="button"
                                                                            :disabled="!item.category_id && !item.governorate_id && !(item.quantity < 0)"
                                                                            @click.prevent="showPanel(index)"
                                                                            class="custom-panel-quotation"
                                                                        >
                                                                            {{ $t('general.Break') }}
                                                                        </button>
                                                                        <button
                                                                            v-if="create.details.length > 1"
                                                                            type="button"
                                                                            @click.prevent="removeNewField(index)"
                                                                            class="custom-btn-dowonload"
                                                                        >
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row border-b-2 brc-default-l2"></div>
                                                            <div class="row mt-3">
                                                                <div class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                                                    {{ $t("general.Extra_note") }}
                                                                </div>

                                                                <div class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                                                    <div class="row align-items-center bgc-primary-l3">
                                                                        <div class="col-7 text-right">
                                                                            {{ $t("general.TotalInvoice") }}
                                                                        </div>
                                                                        <div class="col-5">
                                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                                {{ !create.total ? '0.00' : create.total }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row align-items-center bgc-primary-l3">
                                                                        <div class="col-7 text-right">
                                                                            {{ $t("general.InvoiceDiscount") }}
                                                                        </div>
                                                                        <div class="col-5">
                                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                                {{ !create.total ? '0.00' : create.total }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row align-items-center bgc-primary-l3">
                                                                        <div class="col-7 text-right">
                                                                            {{ $t("general.NetInvoice") }}
                                                                        </div>
                                                                        <div class="col-5">
                                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                                {{ !create.total ? '0.00' : create.total }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <hr />
                                                            <div>
                                                                <span class="text-secondary-d1 text-105">{{$t("general.Thank_you") }}</span>
                                                                <a @click.prevent="addNewField" class="btn btn-info btn-bold px-4 float-right mt-3 mx-2 mt-lg-0">
                                                                    {{$t("general.Add") }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
                                            <span>{{ getCompanyKey("boardRent_order_date") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="
                                                        reservations.sort(
                                                            sortString(
                                                                $i18n.locale == 'ar' ? 'field_title' : 'field_title_e'
                                                            )
                                                        )
                                                    "></i>
                                                <i class="fas fa-arrow-down" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? '-name' : '-name_e')
                                                        )
                                                    "></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.customer_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("customer") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? 'name' : 'name_e')
                                                        )
                                                    "></i>
                                                <i class="fas fa-arrow-down" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? '-name' : '-name_e')
                                                        )
                                                    "></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.salesman_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("sale_man") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? 'name' : 'name_e')
                                                        )
                                                    "></i>
                                                <i class="fas fa-arrow-down" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? '-name' : '-name_e')
                                                        )
                                                    "></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.branch_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("branch") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? 'name' : 'name_e')
                                                        )
                                                    "></i>
                                                <i class="fas fa-arrow-down" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? '-name' : '-name_e')
                                                        )
                                                    "></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.serial_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("order_serial") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t("general.Action") }}
                                    </th>
                                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                </tr>
                                </thead>
                                <tbody v-if="reservations.length > 0">
                                <tr @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
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
                                            {{
                                            $i18n.locale == "ar" ? data.customer.name : data.customer.name_e
                                            }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.salesman_id">
                                        <h5 class="m-0 font-weight-normal">
                                            {{
                                            $i18n.locale == "ar" ? data.salesman.name : data.salesman.name_e
                                            }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.branch_id">
                                        {{
                                        data.branch? $i18n.locale == "ar" ? data.branch.name : data.branch.name_e : ''
                                        }}
                                    </td>
                                    <td v-if="setting.serial_id">
                                        {{ data.prefix }}
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
                                                   @click.prevent="deleteScreenButton(data.id)">
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
                                                 :title="getCompanyKey('boardRent_quotation_edit_form')" title-class="font-18"
                                                 body-class="p-4" :ref="`edit-${data.id}`" :hide-footer="true"
                                                 @show="resetModalEdit(data.id)" @hidden="resetModalHiddenEdit(data.id)">
                                            <form>
                                                <div class="mb-3 d-flex justify-content-end">

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

                                                    <b-button variant="danger" type="button"
                                                              @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)">
                                                        {{ $t("general.Cancel") }}
                                                    </b-button>
                                                </div>
                                            </form>
                                        </b-modal>
                                        <!--  /edit   -->
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <button @mousemove="log(data.id)" @mouseover="log(data.id)" type="button"
                                                class="btn" :id="`tooltip-${data.id}`"
                                                :data-placement="$i18n.locale == 'en' ? 'left' : 'right'" :title="Tooltip">
                                            <i class="fe-info" style="font-size: 22px"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="7">
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
</template>

<style scoped>
.custom-panel-quotation{
    background-color: #81afca !important;
    color: lemonchiffon;
    font-size: 16px;
    border: none;
}
.page-content {
    width: 100%;
}

.total {
    color: #343a40 !important;
    font-weight: bold;
}

.text-secondary-d1 {
    color: #728299 !important;
}

.page-header {
    margin: 0 0 1rem;
    padding-bottom: 1rem;
    padding-top: .5rem;
    border-bottom: 1px dotted #e2e2e2;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -ms-flex-align: center;
    align-items: center;
}

.page-title {
    padding: 0;
    margin: 0;
    font-size: 1.75rem;
    font-weight: 300;
}

.brc-default-l1 {
    border-color: #dce9f0 !important;
}

.ml-n1,
.mx-n1 {
    margin-left: -.25rem !important;
}

.mr-n1,
.mx-n1 {
    margin-right: -.25rem !important;
}

.mb-4,
.my-4 {
    margin-bottom: 1.5rem !important;
}

hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 0;
    border-top: 1px solid rgba(0, 0, 0, .1);
}

.text-grey-m2 {
    color: #888a8d !important;
}

.text-success-m2 {
    color: #86bd68 !important;
}

.font-bolder,
.text-600 {
    font-weight: 600 !important;
}

.text-110 {
    font-size: 110% !important;
}

.text-blue {
    color: #478fcc !important;
}

.pb-25,
.py-25 {
    padding-bottom: .75rem !important;
}

.pt-25,
.py-25 {
    padding-top: .75rem !important;
}

.bgc-default-tp1 {
    background-color: rgba(121, 169, 197, .92) !important;
}

.bgc-default-l4,
.bgc-h-default-l4:hover {
    background-color: #f3f8fa !important;
}

.page-header .page-tools {
    -ms-flex-item-align: end;
    align-self: flex-end;
}

.btn-light {
    color: #757984;
    background-color: #f5f6f9;
    border-color: #dddfe4;
}

.w-2 {
    width: 1rem;
}

.text-120 {
    font-size: 120% !important;
}

.text-primary-m1 {
    color: #4087d4 !important;
}

.text-danger-m1 {
    color: #dd4949 !important;
}

.text-blue-m2 {
    color: #68a3d5 !important;
}

.text-150 {
    font-size: 150% !important;
}

.text-60 {
    font-size: 60% !important;
}

.text-grey-m1 {
    color: #7b7d81 !important;
}

.align-bottom {
    vertical-align: bottom !important;
}
</style>
