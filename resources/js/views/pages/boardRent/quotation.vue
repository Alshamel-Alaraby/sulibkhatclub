<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import Branch from "../../../components/create/branch";
import Saleman from "../../../components/create/saleman";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";
import customerGeneral from "../../../components/create/customerGeneral";
import translation from "../../../helper/translation-mixin";
import {minValue, required, requiredIf} from "vuelidate/lib/validators";
import {formatDateOnly} from "../../../helper/startDate";
import adminApi from "../../../api/adminAxios";
import Status from "../../../components/create/status.vue";
import Swal from "sweetalert2";
import {dynamicSortString} from "../../../helper/tableSort";
import Category from "../../../components/create/category";
import SellMethod from "../../../components/create/boardRent/sell-methods";
import Package from "../../../components/create/boardRent/package";
import TabGovernorate from "../../../components/tabGovernorate";
import Avnue from "../../../components/create/avenue";
import Street from "../../../components/create/street";
import City from "../../../components/city";
/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Quotation",
        meta: [{ name: "description", content: "Quotation" }],
    },
    mixins: [translation],
    beforeRouteEnter(to, from, next) {
        next((vm) => {

            if (vm.$store.state.auth.work_flow_trees.includes("board rent-e")) {
                Swal.fire({
                    icon: "error",
                    title: `${vm.$t("general.Error")}`,
                    text: `${vm.$t("general.ModuleExpired")}`,
                });
                return vm.$router.push({ name: "home" });
            }

            if (vm.$store.state.auth.work_flow_trees.includes('reservation') || vm.$store.state.auth.work_flow_trees.includes('board rent') || vm.$store.state.auth.user.type == 'super_admin') {
                return true;
            } else {
                return vm.$router.push({ name: "home" });
            }
        });
    },
    components: {
        Layout,
        PageHeader,
        Branch,
        Saleman,
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
        customerGeneral,
        Status,
        SellMethod,
        Category,
        Package,
        TabGovernorate,
        Avnue,
        Street,
        City
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
            sellMethods: [],
            categories: [],
            countries: [],
            packages: [],
            governorates: [],
            faceNumbers: [{'A': 0,'B': 0,'Multi': 0,'One-Face': 0}],
            enabled3: true,
            isLoader: false,
            create: {
                branch_id: null,
                status_id: null,
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
                    from: this.formatDate(new Date()),
                    to: this.formatDate(new Date()),
                    panels: []
                }]
            },
            edit: {
                branch_id: null,
                status_id: null,
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
            cities: [{cities: []}],
            avenues: [{avenues: []}],
            streets: [{streets: []}],
            pans: [{pans: []}],
            pansPaginations: [{pansPaginations: {}}],
            locations: [{city_id: null, avenue_id: null, street_id: null, face: ''}],
            allPanelPackages: [{allPanelPackages: []}],
            panelPackages: [{panelPackages: []}],
            panelPackagesPaginatations: [{panelPackagesPaginatations: {}}],
            current_page_pans: [1],
            current_page_pans_packs: [1],
            CheckAllPanels: [[]]
        };
    },
    validations: {
        create: {
            media: {},
            date: { required },
            customer_id: { required },
            branch_id: { required },
            salesman_id: { required },
            status_id: { required },
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
                    price: { required, minValue: minValue(0)},
                    panels: {
                        required,
                    }
                }
            }
        },
        edit: {
            date: { required },
            customer_id: { required },
            branch_id: { required },
            salesman_id: { required },
            status_id: { required },
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
        this.getData();
        this.$store.dispatch('locationIp/getIp');
    },
    methods: {
        async showCityModal(index) {
            if (this.locations[index].city_id == 0) {
                this.$bvModal.show("city-create");
                this.locations[index].city_id = null;
            }else {
                await this.getAvnue(index);
            }
        },
        async showAvenueModal(index) {
            if (this.locations[index].avenue_id == 0) {
                this.$bvModal.show("avenue-create");
                this.locations[index].avenue_id = null;
            }else {
                await this.getStreet(index);
            }
        },
        async showStreetModal(index) {
            if (this.locations[index].street_id == 0) {
                this.$bvModal.show("street-create");
                this.locations[index].street_id = null;
            }else {
                await this.getPanel(index);
            }
        },
        async showCityModalEdit(index) {
            if (this.locations[index].city_id == 0) {
                this.$bvModal.show("city-create");
                this.locations[index].city_id = null;
            }else {
                await this.getAvnue(index);
            }
        },
        async showAvenueModalEdit(index) {
            if (this.locations[index].avenue_id == 0) {
                this.$bvModal.show("avenue-create");
                this.locations[index].avenue_id = null;
            }else {
                await this.getStreet(index);
            }
        },
        async showStreetModalEdit(index) {
            if (this.locations[index].street_id == 0) {
                this.$bvModal.show("street-create");
                this.locations[index].street_id = null;
            }
        },
        v_startCreate(e,index) {
            if (e) {
                this.create.details[index].from = formatDateOnly(e);
            } else {
                this.create.details[index].from = null;
            }
        },
        v_startEdit(e,index){
            if (e) {
                this.edit.details[index].from = formatDateOnly(e);
            } else {
                this.edit.details[index].from = null;
            }
        },
        v_endCreate(e,index) {
            if (e) {
                this.create.details[index].to = formatDateOnly(e);
            } else {
                this.create.details[index].to = null;
            }
        },
        v_endEdit(e,index){
            if (e) {
                this.edit.details[index].to = formatDateOnly(e);
            } else {
                this.edit.details[index].to = null;
            }
        },
        v_dateCreate(e) {
            if (e) {
                this.create.date = formatDateOnly(e);
            } else {
                this.create.date = null;
            }
        },
        v_dateEdit(e){
            if (e) {
                this.edit.date = formatDateOnly(e);
            } else {
                this.edit.date = null;
            }
        },
        changeValue(index = null){
            let sum = 0;
            if(this.create.is_stripe == 0){
                this.create.details.forEach(e => {
                    sum += e.price * e.quantity;
                });
                this.create.total = sum;
            }else {
                this.create.details.forEach(e =>  sum += e.price );
                this.create.total = sum;
            }
        },
        changeValueEdit(index = null){
            let sum = 0;
            if(this.edit.is_stripe == 0){
                this.edit.details.forEach(e =>  sum += e.price * e.quantity );
                this.edit.total = sum;
            }else {
                this.edit.edit.details.forEach(e =>  sum += e.price );
                this.edit.total = sum;
            }
        },
        addNewField(index){
            if(this.create.is_stripe == 0){
                this.locations.push({city_id: null, avenue_id: null, street_id: null, face: ''});
                this.faceNumbers.push({'A': 0,'B': 0,'Multi': 0,'One-Face': 0});
                this.cities.push({cities: []});
                this.avenues.push({avenues: []});
                this.streets.push({streets: []});
                this.pans.push({pans: []});
                this.pansPaginations.push({pansPaginations: []});
                this.allPanelPackages.push({allPanelPackages: []});
                this.panelPackages.push({panelPackages: []});
                this.panelPackagesPaginatations.push({panelPackagesPaginatations: []});
                this.current_page_pans.push(1);
                this.current_page_pans_packs.push(1);
                this.CheckAllPanels.push([]);
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
                    this.locations.splice(index,1);
                    this.pansPaginations.splice(index,1);
                    this.allPanelPackages.splice(index,1);
                    this.panelPackages.splice(index,1);
                    this.panelPackagesPaginatations.splice(index,1);
                    this.current_page_pans.splice(index,1);
                    this.current_page_pans_packs.splice(index,1);
                    this.CheckAllPanels.splice(index,1);
                    this.faceNumbers.splice(index,1);
                }
            }
        },
        addNewFieldEdit(index){
            if(this.edit.is_stripe == 0){
                this.locations.push({city_id: null, avenue_id: null, street_id: null, face: ''});
                this.faceNumbers.push({'A': 0,'B': 0,'Multi': 0,'One-Face': 0});
                this.cities.push({cities: []});
                this.avenues.push({avenues: []});
                this.streets.push({streets: []});
                this.pans.push({pans: []});
                this.pansPaginations.push({pansPaginations: []});
                this.allPanelPackages.push({allPanelPackages: []});
                this.panelPackages.push({panelPackages: []});
                this.panelPackagesPaginatations.push({panelPackagesPaginatations: []});
                this.current_page_pans.push(1);
                this.current_page_pans_packs.push(1);
                this.CheckAllPanels.push([]);
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
                    this.locations.splice(index,1);
                    this.pansPaginations.splice(index,1);
                    this.allPanelPackages.splice(index,1);
                    this.panelPackages.splice(index,1);
                    this.panelPackagesPaginatations.splice(index,1);
                    this.current_page_pans.splice(index,1);
                    this.current_page_pans_packs.splice(index,1);
                    this.CheckAllPanels.splice(index,1);
                    this.faceNumbers.splice(index,1);
                }
            }
        },
        showSaleManModal() {
            if (this.create.salesman_id == 0) {
                this.$bvModal.show("saleman-create");
                this.create.salesman_id = null;
            }
        },
        showSaleManModalEdit() {
            if (this.edit.salesman_id == 0) {
                this.$bvModal.show("saleman-create");
                this.edit.salesman_id = null;
            }
        },
        showCustomerModal() {
            if (this.create.customer_id == 0) {
                this.$bvModal.show("customer-general-create");
                this.create.customer_id = null;
            }else if(this.create.customer_id > 0){
                this.phone = this.customers.find(el => el.id == this.create.customer_id).phone;
            }else {
                this.phone = '';
            }
        },
        showCustomerModalEdit() {
            if (this.edit.customer_id == 0) {
                this.$bvModal.show("customer-general-create");
                this.edit.customer_id = null;
            }else if(this.edit.customer_id > 0){
                this.phone = this.customers.find(el => el.id == this.edit.customer_id).phone;
            }else {
                this.phone = '';
            }
        },
        showBranchModal() {
            if (this.create.branch_id == 0) {
                this.$bvModal.show("create_branch");
                this.create.branch_id = null;
            }
        },
        showBranchModalEdit() {
            if (this.edit.branch_id == 0) {
                this.$bvModal.show("create_branch");
                this.edit.branch_id = null;
            }
        },
        showStatusModal() {
            if (this.create.status_id == 0) {
                this.$bvModal.show("status-create");
                this.create.status_id = null;
            }
        },
        showStatusModalEdit() {
            if (this.edit.status_id == 0) {
                this.$bvModal.show("status-create");
                this.edit.status_id = null;
            }
        },
        showSellMethodModal() {
            if (this.create.sell_method_id == 0) {
                this.$bvModal.show("create-sell-method");
                this.create.sell_method_id = null;
            }
        },
        showSellMethodModalEdit() {
            if (this.edit.sell_method_id == 0) {
                this.$bvModal.show("create-sell-method");
                this.edit.sell_method_id = null;
            }
        },
        async showGovernorateModal(index) {
            if (this.create.details[index].governorate_id == 0) {
                this.$bvModal.show(`tab-governorate-create`);
                this.create.details[index].governorate_id = null;
            }else {
                await this.getCity(index);
            }
        },
        async showGovernorateModalEdit(index) {
            if (this.edit.details[index].governorate_id == 0) {
                this.$bvModal.show(`tab-governorate-create`);
                this.edit.details[index].governorate_id = null;
            }else {
                await this.getCity(index);
            }
        },
        showeCategoryModal(index) {
            if (this.create.details[index].category_id == 0) {
                this.$bvModal.show("category-create");
                this.create.details[index].category_id = null;
            }
        },
        showCategoryModalEdit(index) {
            if (this.edit.details[index].category_id == 0) {
                this.$bvModal.show("category-create");
                this.edit.details[index].category_id = null;
            }
        },
        showPackageModal(index) {
            if (this.create.details[index].package_id == 0) {
                this.$bvModal.show("package-create");
                this.create.details[index].package_id = null;
            }else  if (this.create.details[index].package_id > 0) {
                this.create.details[index].price =
                    this.packages.find(el => el.id == this.create.details[index].package_id).price;
                this.changeValue();
            }
        },
        showPackageModalEdit(index) {
            if (this.edit.details[index].package_id == 0) {
                this.$bvModal.show("package-create");
                this.edit.details[index].package_id = null;
            }else  if (this.edit.details[index].package_id > 0) {
                this.edit.details[index].price =
                    this.packages.find(el => el.id == this.edit.details[index].package_id).price;
                this.changeValueEdit();
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
                status_id: null,
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
            this.locations = [{city_id: null, avenue_id: null, street_id: null, face: ''}];
            this.cities= [{cities: []}];
            this.avenues= [{streets: []}];
            this.streets = [{streets: []}];
            this.faceNumbers = [{'A': 0,'B': 0,'Multi': 0,'One-Face': 0}];
            this.pans = [{pans: []}];
            this.pansPaginations = [{pansPaginations: []}];
            this.allPanelPackages = [{allPanelPackages: []}];
            this.panelPackages = [{panelPackages: []}];
            this.panelPackagesPaginatations= [{panelPackagesPaginatations: []}];
            this.current_page_pans = [1];
            this.current_page_pans_packs = [1];
            this.CheckAllPanels = [[]];
            this.create = {
                branch_id: null,
                status_id: null,
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
                    from: this.formatDate(new Date()),
                    to: this.formatDate(new Date()),
                    panels: []
                }]
            };
            this.phone = '';
            this.codeCountry = this.$store.getters["locationIp/countryCode"];
            await this.getCustomers();
            await this.getBranches();
            await this.getSalesmen();
            await this.getStatus();
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
            this.locations = [{city_id: null, avenue_id: null, street_id: null, face: ''}];
            this.cities= [{cities: []}];
            this.avenues= [{streets: []}];
            this.streets = [{streets: []}];
            this.faceNumbers = [{'A': 0,'B': 0,'Multi': 0,'One-Face': 0}];
            this.pans = [{pans: []}];
            this.pansPaginations = [{pansPaginations: []}];
            this.allPanelPackages = [{allPanelPackages: []}];
            this.panelPackages = [{panelPackages: []}];
            this.panelPackagesPaginatations= [{panelPackagesPaginatations: []}];
            this.current_page_pans = [1];
            this.current_page_pans_packs = [1];
            this.CheckAllPanels = [[]];
            this.create =  {
                branch_id: null,
                status_id: null,
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
        async getCustomers() {
            this.isLoader = true;
            await adminApi
                .get(`/general-customer`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضافة زبون", name_e: "Add customer" });
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
        async getBranches() {
            this.isLoader = true;
            await adminApi
                .get(`/branches?document_id=9`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف فرع", name_e: "Add branch" });
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
        async getSalesmen() {
            this.isLoader = true;
            await adminApi
                .get(`/salesmen`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضافة رجل مبيعات", name_e: "Add sale man" });
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
        async getStatus(){
            this.isLoader = true;

            await adminApi
                .get(
                    `/statuses`
                )
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف حاله", name_e: "Add Status" });
                    this.statuses = l;
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
        async getSellmethod() {
            this.isLoader = true;
            await adminApi
                .get(`/boards-rent/sell-methods?company_id=${this.company_id}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف طريه البيع", name_e: "Add sell methods" });
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
        async getCategory(index = null) {
            this.isLoader = true;

            await adminApi
                .get(
                    `/categories`
                )
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف الفئه", name_e: "Add Category" });
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
                .get(
                    `/boards-rent/packages`
                )
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف الباقه", name_e: "Add Package" });
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
                .get(
                    `/governorates`
                )
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف المحافظه", name_e: "Add Governorates" });
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
        async getCity(index) {
            this.isLoader = true;
            let governorate = this.create.details[index].governorate_id;
            this.locations[index].city_id = null;
            this.locations[index].avenue_id = null;
            this.locations[index].street_id = null;
            this.cities[index].cities = [];
            this.avenues[index].avenues = [];
            this.streets[index].streets = [];

            await adminApi
                .get(`/cities?columns[0]=governorate.id&search=${governorate}`)
                .then((res) => {
                    let l = res.data.data;
                    this.cities[index].cities = l;
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
        async getAvnue(index) {
            this.isLoader = true;
            let city = this.locations[index].city_id;
            this.locations[index].avenue_id = null;
            this.locations[index].street_id = null;
            this.avenues[index].avenues = [];
            this.streets[index].streets = [];

            await adminApi
                .get(`/avenues?columns[0]=city.id&search=${city}`)
                .then((res) => {
                    let l = res.data.data;
                    this.avenues[index].avenues = l;
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
        async getStreet(index) {
            this.isLoader = true;
            let avenue = this.locations[index].avenue_id;
            this.locations[index].street_id = null;
            this.streets[index].streets = [];

            await adminApi
                .get(`/streets?columns[0]=avenue.id&search=${avenue}`)
                .then((res) => {
                    let l = res.data.data;
                    this.streets[index].streets = l;
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
        async getPanel(index) {
            this.isLoader = true;
            let category_id = this.create.details[index].category_id;
            let governorate_id = this.create.details[index].governorate_id;
            let city_id = this.locations[index].city_id;
            let avenue_id = this.locations[index].avenue_id;
            let face = this.locations[index].face;
            let street_id = this.locations[index].street_id;

            await adminApi
                .get(
                    `/boards-rent/panels?page=${1}&per_page=${7}&category_id=${category_id}&governorate_id=${governorate_id}&city_id=${city_id}&avenue_id=${avenue_id}&street_id=${street_id}&face=${face}`
                )
                .then((res) => {
                    let l = res.data;
                    this.pans[index].pans = l.data;
                    this.pansPaginations[index].pansPaginations = l.pagination;
                    this.current_page_pans[index] = l.pagination.current_page;
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
        async getPanelPagination(index) {

            if (
                this.current_page_pans[index] <= this.pansPaginations[index].pansPaginations.last_page &&
                this.current_page_pans[index] != this.pansPaginations[index].pansPaginations.current_page &&
                this.current_page_pans[index]
            ) {
                this.isLoader = true;
                let category_id = this.location[index].category_id ?? null;
                let governorate_id = this.location[index].governorate_id ?? null;
                let city_id = this.location[index].city_id ?? null;
                let avenue_id = this.location[index].avenue_id ?? null;
                let face = this.location[index].face ?? null;
                let street_id = this.location[index].street_id ?? null;

                await adminApi
                    .get(
                        `/boards-rent/panels?page=${1}&per_page=${7}&packages=1&category_id=${category_id}&governorate_id=${governorate_id}&city_id=${city_id}&avenue_id=${avenue_id}&street_id=${street_id}&face=${face}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.pans[index].pans = l.data;
                        this.pansPaginations[index].pansPaginations = l.pagination;
                        this.current_page_pans[index] = l.pagination.current_page;
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
         *   show Modal (edit)
         */
        async resetModalEdit(id) {
            this.locations = [];
            this.cities = [];
            this.avenues = [];
            this.streets = [];
            this.pans = [];this.pansPaginations = [];
            this.allPanelPackages = [];
            this.panelPackages = [];this.faceNumbers = [];this.panelPackagesPaginatations= [];
            this.current_page_pans = [];this.current_page_pans_packs = [];this.CheckAllPanels = [];
            let reservation = this.reservations.find((e) => id == e.id);this.date = new Date(reservation.date);
            this.edit.serial_number = reservation.serial_number;this.edit.date = reservation.date;
            await this.getBranches();this.edit.branch_id = reservation.branch.id;
            await this.getSalesmen();this.edit.salesman_id = reservation.salesman.id;
            await this.getSellmethod();this.edit.sell_method_id = reservation.sell_method.id;
            await this.getStatus();this.edit.status_id = reservation.customer.id;
            await this.getCustomers();this.edit.customer_id = reservation.status.id;
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
                    this.locations.push({city_id: null, avenue_id: null, street_id: null, face: ''});
                    this.faceNumbers.push({'A': 0,'B': 0,'Multi': 0,'One-Face': 0});
                    this.cities.push({cities: []});
                    this.avenues.push({avenues: []});
                    this.streets.push({streets: []});
                    this.pans.push({pans: []});
                    this.pansPaginations.push({pansPaginations: []});
                    this.allPanelPackages.push({allPanelPackages: []});
                    this.panelPackages.push({panelPackages: []});
                    this.panelPackagesPaginatations.push({panelPackagesPaginatations: []});
                    this.current_page_pans.push(1);
                    this.current_page_pans_packs.push(1);
                    this.CheckAllPanels.push([]);
                    this.allPanelPackages[index].allPanelPackages = e.panels;
                    e.panels.forEach((el,index) => {
                        this.CheckAllPanels[index].push(el.id);
                    });
                    this.paginate(index);
                    this.changeValuePanel(index);
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
            this.locations = [{city_id: null, avenue_id: null, street_id: null, face: ''}];
            this.cities= [{cities: []}];
            this.avenues= [{streets: []}];
            this.streets = [{streets: []}];
            this.pans = [{pans: []}];
            this.pansPaginations = [{pansPaginations: []}];
            this.allPanelPackages = [{allPanelPackages: []}];
            this.panelPackages = [{panelPackages: []}];
            this.panelPackagesPaginatations= [{panelPackagesPaginatations: []}];
            this.current_page_pans = [1];
            this.current_page_pans_packs = [1];
            this.CheckAllPanels = [[]];
            this.edit = {
                branch_id: null,
                status_id: null,
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
        showPanel(index){this.$bvModal.show(`create-panel-quotation-${index}`);},
        showPanelEdit(index){this.$bvModal.show(`edit-panel-quotation-${index}`);},
        changeValuePanel (index){
            this.faceNumbers[index] = {'A': 0,'B': 0,'Multi': 0,'One-Face': 0};
            this.allPanelPackages[index].allPanelPackages.forEach((e,index) => {
                if(e.face == 'A'){
                    this.faceNumbers[index].A = this.faceNumbers[index].A + 1;
                }else if(e.face == 'B'){
                    this.faceNumbers[index].B = this.faceNumbers[index].B + 1;
                }else if(e.face == 'Multi'){
                    this.faceNumbers[index].Multi = this.faceNumbers[index].Multi + 1;
                }else if(e.face == 'One-Face'){
                    this.faceNumbers[index]['One-Face'] = this.faceNumbers[index]['One-Face'] + 1;
                }
            });
        },
        checkRowPanel(index,data) {
            let panel = this.allPanelPackages[index].allPanelPackages.find((el) => el.id == data.id);
            if (!panel) {
                this.allPanelPackages[index].allPanelPackages.push(data);
            } else {
                let index = this.allPanelPackages[index].allPanelPackages.findIndex((el) => el.id == data.id);
                this.allPanelPackages[index].allPanelPackages.splice(index, 1);
            }
            this.paginate(
                this.panelPackagesPaginatations[index].panelPackagesPaginatations.currentPage ?
                    this.panelPackagesPaginatations[index].panelPackagesPaginatations.currentPage : 1
            );
            this.changeValuePanel(index);
        },
        // paginate
        paginate(p = 1,index){

            const page = p;
            this.current_page_pans_packs[index] = page;
            const limit = 7;
            const skip = (page - 1) * limit;
            const endIndex = page * limit;
            const total = this.allPanelPackages[index].allPanelPackages.length;

            // Pagination result
            this.panelPackagesPaginatations[index].panelPackagesPaginatations.total = total;
            this.panelPackagesPaginatations[index].panelPackagesPaginatations.limit = limit;
            this.panelPackagesPaginatations[index].panelPackagesPaginatations.last_page = Math.ceil(total / limit);

            if(
                this.current_page_pans_packs[index] <= this.panelPackagesPaginatations.last_page &&
                this.current_page_pans_packs[index] != this.packagesPagination.current_page
            ){
                this.panelPackagesPaginatations[index].panelPackagesPaginatations.to = skip? (skip + limit) : limit;
                this.panelPackagesPaginatations[index].panelPackagesPaginatations.from = skip ? skip : 1;
                this.panelPackagesPaginatations[index].panelPackagesPaginatations.currentPage = page;
                this.panelPackages[index].panelPackages = [];
                this.panelPackages = this.allPanelPackages[index].allPanelPackages.slice(skip,skip+limit);
            }

        }
    }
};
</script>

<template>
    <Layout>
        <PageHeader />
        <Saleman :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getSalesmen" />
        <customerGeneral :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCustomers" />
        <Branch :id="'create_branch'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getBranches" />
        <Status :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getStatus" />
        <SellMethod :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getSellmethod" />
        <Category :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCategory" />
        <Package :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"  @created="getPackage" />
        <TabGovernorate :country_id="1" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"  @created="getGovernorate" />

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ $t("general.quotationsTable") }}</h4>
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
                        <b-modal dialog-class="modal-full-width" id="create" :title="getCompanyKey('boardRent_quotation_create_form')"
                                 title-class="font-18" body-class="p-4 " :hide-footer="true" @show="resetModal"
                                 @hidden="resetModalHidden">
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <b-button variant="success" :disabled="!is_disabled" @click.prevent="resetForm" type="button"
                                              :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
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
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("branch") }}</label>
                                            <multiselect @input="showBranchModal" v-model="create.branch_id"
                                                         :options="branches.map((type) => type.id)" :custom-label="
                                                            (opt) =>
                                                                $i18n.locale == 'ar'
                                                                    ? branches.find((x) => x.id == opt).name
                                                                    : branches.find((x) => x.id == opt).name_e
                                                        " :class="{
                                                            'is-invalid':
                                                                $v.create.branch_id.$error || errors.branch_id,
                                                        }">
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
                                                {{ getCompanyKey("boardRent_order_date") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker type="date" v-model="date" defaultValue
                                                         @change="v_dateCreate" confirm></date-picker>
                                            <template v-if="errors.date">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.date"
                                                              :key="index">{{
                                                    errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("sale_man") }}</label>
                                            <multiselect @input="showSaleManModal" v-model="create.salesman_id"
                                                         :options="salesmen.map((type) => type.id)" :custom-label="
                                                            (opt) =>
                                                                $i18n.locale == 'ar'
                                                                    ? salesmen.find((x) => x.id == opt).name
                                                                    : salesmen.find((x) => x.id == opt).name_e
                                                        " :class="{
                                                            'is-invalid':
                                                                $v.create.salesman_id.$error || errors.salesman_id,
                                                        }">
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
                                            <label>{{ getCompanyKey("boardRent_order_sellMethod") }}</label>
                                            <multiselect @input="showSellMethodModal" v-model="create.sell_method_id"
                                                         :options="sellMethods.map((type) => type.id)" :custom-label="
                                                            (opt) =>
                                                                $i18n.locale == 'ar'
                                                                    ? sellMethods.find((x) => x.id == opt).name
                                                                    : sellMethods.find((x) => x.id == opt).name_e
                                                        " :class="{
                                                        'is-invalid':
                                                            $v.create.sell_method_id.$error || errors.sell_method_id,
                                                    }">
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
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_unitStatus_status") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect @input="showStatusModal" v-model="create.status_id"
                                                         :options="statuses.map((type) => type.id)"
                                                         :custom-label="(opt) => $i18n.locale == 'ar'? statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e">
                                            </multiselect>
                                            <div v-if="$v.create.status_id.$error || errors.status_id" class="text-danger">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.status_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.status_id" :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("customer") }}</label>
                                            <multiselect @input="showCustomerModal" v-model="create.customer_id"
                                                         :options="customers.map((type) => type.id)" :custom-label="
                                                            (opt) =>
                                                                $i18n.locale == 'ar'
                                                                    ? customers.find((x) => x.id == opt).name
                                                                    : customers.find((x) => x.id == opt).name_e
                                                        " :class="{
                                                            'is-invalid':
                                                                $v.create.customer_id.$error || errors.customer_id,
                                                        }">
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
                                            <label class="control-label">
                                                {{ getCompanyKey('general_customer_phone') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <VuePhoneNumberInput
                                                v-model="phone"
                                                :default-country-code="codeCountry"
                                                valid-color="#28a745"
                                                error-color="#dc3545"
                                                disabled
                                                :preferred-countries="['FR', 'EG', 'DE']"
                                            />
                                            <template v-if="errors.phone">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.phone" :key="index">{{
                                                    errorMessage
                                                    }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3" >
                                        <div class="form-group">
                                            <label class="mr-2">
                                                {{ getCompanyKey("boardRent_order_is_stripe") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <b-form-group :class="{
                                                    'is-invalid': $v.create.is_stripe.$error || errors.is_stripe,
                                                    'is-valid': !$v.create.is_stripe.$invalid && !errors.is_stripe,
                                                  }">
                                                <b-form-radio class="d-inline-block" v-model="$v.create.is_stripe.$model" name="some-radios"
                                                              value="1">{{ $t("general.Yes") }}</b-form-radio>
                                                <b-form-radio class="d-inline-block m-1" v-model="$v.create.is_stripe.$model" name="some-radios"
                                                              value="0">{{ $t("general.No") }}</b-form-radio>
                                            </b-form-group>
                                            <template v-if="errors.is_stripe">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.is_stripe" :key="index">{{ errorMessage }}
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
                                                                    <span class="text-default-d3">
                                                                        {{ $t("general.quotation") }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- .row -->
                                                        <hr class="row brc-default-l1 mx-n1 mb-4" />
                                                        <div class="mt-4" v-if="create.is_stripe == 0">
                                                            <div
                                                                class="row text-600 text-white bgc-default-tp1 py-25">
                                                                <div class="col-2">{{ getCompanyKey("boardRent_order_category") }}</div>
                                                                <div class="col-2">{{ getCompanyKey("boardRent_order_governorate") }}</div>
                                                                <div class="col-2">{{ getCompanyKey("boardRent_order_from") }}</div>
                                                                <div class="col-2">{{
                                                                    getCompanyKey("boardRent_order_to") }}
                                                                </div>
                                                                <div class="col-2"> {{ getCompanyKey("boardRent_order_price")
                                                                    }}</div>
                                                                <div class="col-1"> {{
                                                                    getCompanyKey("boardRent_order_quantity") }}</div>
                                                                <div class="col-1">{{ $t("general.Action") }}</div>
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
                                                                    body-class="p-1"
                                                                    dialog-class="modal-full-width"
                                                                    :hide-footer="true"
                                                                >
                                                                    <div :class="['row justify-content-center']">
                                                                        <div :class="['col-5']">
                                                                            <div class="face">
                                                                                <span class="face-name">A : {{ faceNumbers[index].A }}</span>
                                                                            </div>
                                                                            <div class="face">
                                                                                <span class="face-name">B : {{ faceNumbers[index].B }}</span>
                                                                            </div>
                                                                            <div class="face">
                                                                                <span class="face-name">Multi : {{ faceNumbers[index].Multi }}</span>
                                                                            </div>
                                                                            <div class="face">
                                                                                <span class="face-name">One-Face : {{ faceNumbers[index]["One-Face"] }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-end">
                                                                        <div>
                                                                            <b-button
                                                                                @click.prevent="$bvModal.show(`search-${index}`)"
                                                                                variant="primary"
                                                                                class="mx-1 font-weight-bold"
                                                                            >
                                                                                {{ $t('general.Search') }}
                                                                                <i class="fe-search"></i>
                                                                            </b-button>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <!-- selet panals -->
                                                                        <div class="col-md-6">
                                                                            <!-- start Pagination -->
                                                                            <div class="d-inline-flex align-items-center pagination-custom position-relative">
                                                                                <div>
                                                                                    <div class="d-inline-block" style="font-size: 13px">
                                                                                        {{ pansPaginations[index].pansPaginations.from }}-{{ pansPaginations[index].pansPaginations.to }} /
                                                                                        {{ pansPaginations[index].pansPaginations.total }}
                                                                                    </div>
                                                                                    <div class="d-inline-block">
                                                                                        <a
                                                                                            href="javascript:void(0)"
                                                                                            :style="{
                                                                                              'pointer-events':
                                                                                                pansPaginations[index].pansPaginations.current_page > 1 ? '' : 'none',
                                                                                            }"
                                                                                            @click.prevent="getPanel(pansPaginations[index].pansPaginations.current_page - 1,index)"
                                                                                        >
                                                                                            <span>&lt;</span>
                                                                                        </a>
                                                                                        <input
                                                                                            type="text"
                                                                                            @keyup.enter="getPanelPagination(index)"
                                                                                            v-model="current_page_pans[index]"
                                                                                            class="pagination-current-page"
                                                                                        />
                                                                                        <a
                                                                                            href="javascript:void(0)"
                                                                                            :style="{
                                                                                              'pointer-events':
                                                                                                (pansPaginations[index].pansPaginations.last_page ==
                                                                                                pansPaginations[index].pansPaginations.current_page) || !pansPaginations[index].pansPaginations
                                                                                                  ? 'none'
                                                                                                  : '',
                                                                                            }"
                                                                                            @click.prevent="getPanel(pansPaginations[index].pansPaginations.current_page + 1)"
                                                                                        >
                                                                                            <span>&gt;</span>
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                                <div style="position: absolute;transform: translate(230px, 0px)">
                                                                                    <h3>{{ $t('general.SelectPanel') }}</h3>
                                                                                </div>
                                                                            </div>
                                                                            <!-- end Pagination -->
                                                                            <table class="table table-borderless table-hover table-centered m-0">
                                                                                <thead>
                                                                                <tr>
                                                                                    <th>
                                                                                        <div class="d-flex justify-content-center">
                                                                                            <span>{{ getCompanyKey("boardRent_panel_code") }}</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th>
                                                                                        <div class="d-flex justify-content-center">
                                                                                            <span>{{ getCompanyKey("boardRent_panel_category") }}</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th >
                                                                                        <div class="d-flex justify-content-center">
                                                                                            <span>{{ getCompanyKey("boardRent_panel_governorate") }}</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th >
                                                                                        <div class="d-flex justify-content-center">
                                                                                            <span>{{ getCompanyKey("boardRent_panel_city") }}</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th >
                                                                                        <div class="d-flex justify-content-center">
                                                                                            <span>{{ getCompanyKey("boardRent_panel_avenue") }}</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th >
                                                                                        <div class="d-flex justify-content-center">
                                                                                            <span>{{ getCompanyKey("boardRent_panel_street") }}</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th >
                                                                                        <div class="d-flex justify-content-center">
                                                                                            <span>{{ getCompanyKey("boardRent_panel_face") }}</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th>
                                                                                        {{ $t("general.Action") }}
                                                                                    </th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody v-if="pans[index].pans.length > 0">
                                                                                <tr
                                                                                    v-for="(data, index) in pans[index].pans"
                                                                                    :key="data.id"
                                                                                    class="body-tr-custom"
                                                                                >
                                                                                    <td scope="col">
                                                                                        {{ data.code }}
                                                                                    </td>
                                                                                    <td scope="col">
                                                                                        {{ $i18n.locale == 'ar' ? data.category.name : data.category.name_e }}
                                                                                    </td>
                                                                                    <td scope="col">
                                                                                        {{  data.governorate ? $i18n.locale == 'ar' ? data.governorate.name : data.governorate.name_e : '-' }}
                                                                                    </td>
                                                                                    <td scope="col">
                                                                                        {{ data.city ? $i18n.locale == 'ar' ? data.city.name : data.city.name_e : '-' }}
                                                                                    </td>
                                                                                    <td scope="col">
                                                                                        {{ data.avenue ? $i18n.locale == 'ar' ? data.avenue.name : data.avenue.name_e : '-' }}
                                                                                    </td>
                                                                                    <td scope="col">
                                                                                        {{ data.street ? $i18n.locale == 'ar' ? data.street.name : data.street.name_e : '-' }}
                                                                                    </td>
                                                                                    <td scope="col">
                                                                                        {{ data.face }}
                                                                                    </td>
                                                                                    <td scope="col" style="width: 0">
                                                                                        <div class="form-check custom-control">
                                                                                            <input
                                                                                                class="form-check-input"
                                                                                                type="checkbox"
                                                                                                :value="data.id"
                                                                                                @change="checkRowPanel(index,data)"
                                                                                                v-model="CheckAllPanels[index]"
                                                                                                style="width: 17px; height: 17px"
                                                                                            />
                                                                                        </div>
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

                                                                        <!-- panals package -->
                                                                        <div class="col-md-6">
                                                                            <!-- start Pagination -->
                                                                            <div class="d-inline-flex align-items-center pagination-custom position-relative">
                                                                                <div>
                                                                                    <div class="d-inline-block" style="font-size: 13px">
                                                                                        {{ panelPackagesPaginatations[index].panelPackagesPaginatations.from }}-{{ panelPackagesPaginatations[index].panelPackagesPaginatations.to }} /
                                                                                        {{ panelPackagesPaginatations[index].panelPackagesPaginatations.total }}
                                                                                    </div>
                                                                                    <div class="d-inline-block">
                                                                                        <a
                                                                                            href="javascript:void(0)"
                                                                                            :style="{
                                                                                              'pointer-events':
                                                                                                panelPackagesPaginatations[index].panelPackagesPaginatations.current_page > 1 ? '' : 'none',
                                                                                            }"
                                                                                            @click.prevent="paginate(panelPackagesPaginatations[index].panelPackagesPaginatations.current_page - 1,index)"
                                                                                        >
                                                                                            <span>&lt;</span>
                                                                                        </a>
                                                                                        <input
                                                                                            type="text"
                                                                                            @keyup.enter="paginate(current_page_pans_packs[index],index)"
                                                                                            v-model="current_page_pans_packs[index]"
                                                                                            class="pagination-current-page"
                                                                                        />
                                                                                        <a
                                                                                            href="javascript:void(0)"
                                                                                            :style="{
                                                                                              'pointer-events':
                                                                                                (panelPackagesPaginatations[index].panelPackagesPaginatations.last_page ==
                                                                                                panelPackagesPaginatations[index].panelPackagesPaginatations.current_page) || !panelPackagesPaginatations[index].panelPackagesPaginatations
                                                                                                  ? 'none'
                                                                                                  : '',
                                                                                            }"
                                                                                            @click.prevent="paginate(panelPackagesPaginatations[index].panelPackagesPaginatations.current_page + 1)"
                                                                                        >
                                                                                            <span>&gt;</span>
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                                <div style="position: absolute;transform: translate(230px, 0px)">
                                                                                    <h3>{{ $t('general.Selected') }}</h3>
                                                                                </div>
                                                                            </div>
                                                                            <!-- end Pagination -->

                                                                            <table class="table table-borderless table-hover table-centered">
                                                                                <thead>
                                                                                <tr>
                                                                                    <th>
                                                                                        <div class="d-flex justify-content-center">
                                                                                            <span>{{ getCompanyKey("boardRent_panel_code") }}</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th>
                                                                                        <div class="d-flex justify-content-center">
                                                                                            <span>{{ getCompanyKey("boardRent_panel_category") }}</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th >
                                                                                        <div class="d-flex justify-content-center">
                                                                                            <span>{{ getCompanyKey("boardRent_panel_governorate") }}</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th >
                                                                                        <div class="d-flex justify-content-center">
                                                                                            <span>{{ getCompanyKey("boardRent_panel_city") }}</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th >
                                                                                        <div class="d-flex justify-content-center">
                                                                                            <span>{{ getCompanyKey("boardRent_panel_avenue") }}</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th >
                                                                                        <div class="d-flex justify-content-center">
                                                                                            <span>{{ getCompanyKey("boardRent_panel_street") }}</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th >
                                                                                        <div class="d-flex justify-content-center">
                                                                                            <span>{{ getCompanyKey("boardRent_panel_face") }}</span>
                                                                                        </div>
                                                                                    </th>
                                                                                    <th>
                                                                                        {{ $t("general.Action") }}
                                                                                    </th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody v-if="panelPackages[index].panelPackages.length > 0">
                                                                                <tr
                                                                                    v-for="(data, index) in panelPackages[index].panelPackages"
                                                                                    :key="data.id"
                                                                                    class="body-tr-custom"
                                                                                >
                                                                                    <td scope="col">
                                                                                        {{ data.code }}
                                                                                    </td>
                                                                                    <td scope="col">
                                                                                        {{ $i18n.locale == 'ar' ? data.category.name : data.category.name_e }}
                                                                                    </td>
                                                                                    <td scope="col">
                                                                                        {{  data.governorate ? $i18n.locale == 'ar' ? data.governorate.name : data.governorate.name_e : '-' }}
                                                                                    </td>
                                                                                    <td scope="col">
                                                                                        {{ data.city ? $i18n.locale == 'ar' ? data.city.name : data.city.name_e : '-' }}
                                                                                    </td>
                                                                                    <td scope="col">
                                                                                        {{ data.avenue ? $i18n.locale == 'ar' ? data.avenue.name : data.avenue.name_e : '-' }}
                                                                                    </td>
                                                                                    <td scope="col">
                                                                                        {{ data.street ? $i18n.locale == 'ar' ? data.street.name : data.street.name_e : '-' }}
                                                                                    </td>
                                                                                    <td scope="col">
                                                                                        {{ data.face }}
                                                                                    </td>
                                                                                    <td scope="col" style="width: 0">
                                                                                        <div class="form-check custom-control">
                                                                                            <input
                                                                                                class="form-check-input"
                                                                                                type="checkbox"
                                                                                                :value="data.id"
                                                                                                @change="checkRowPanel(index,data)"
                                                                                                v-model="CheckAllPanels[index]"
                                                                                                style="width: 17px; height: 17px"
                                                                                            />
                                                                                        </div>
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
                                                                    </div>
                                                                </b-modal>
                                                                <!--  /create panels   -->

                                                                <!--  search   -->
                                                                <b-modal
                                                                    :id="`search-${index}`"
                                                                    :title="$t('general.Search')"
                                                                    title-class="font-18"
                                                                    body-class="p-4"
                                                                    size="lg"
                                                                    :hide-footer="true"
                                                                >
                                                                    <form>
                                                                        <div class="mb-3 d-flex justify-content-end">
                                                                            <b-button
                                                                                variant="success"
                                                                                type="button" class="mx-1"
                                                                                v-if="!isLoader"
                                                                                @click.prevent="getPanel(index)"
                                                                            >
                                                                                {{ $t('general.Search') }}
                                                                            </b-button>

                                                                            <b-button variant="success" class="mx-1" disabled v-else>
                                                                                <b-spinner small></b-spinner>
                                                                                <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                                                            </b-button>
                                                                            <!-- Emulate built in modal footer ok and cancel button actions -->

                                                                            <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(`search-${index}`)">
                                                                                {{ $t('general.Cancel') }}
                                                                            </b-button>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label class="control-label">
                                                                                        {{ getCompanyKey("boardRent_panel_city") }}
                                                                                    </label>
                                                                                    <multiselect
                                                                                        @input="showCityModal(index)"
                                                                                        v-model="locations[index].city_id"
                                                                                        :options="cities[index].cities.map((type) => type.id)"
                                                                                        :custom-label="(opt) => $i18n.locale == 'ar' ? cities[index].cities.find((x) => x.id == opt).name : cities[index].cities.find((x) => x.id == opt).name_e"
                                                                                    >
                                                                                    </multiselect>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label class="control-label">
                                                                                        {{ getCompanyKey("boardRent_panel_avenue") }}
                                                                                    </label>
                                                                                    <multiselect
                                                                                        @input="showAvenueModal(index)"
                                                                                        v-model="locations[index].avenue_id"
                                                                                        :options="avenues[index].avenues.map((type) => type.id)"
                                                                                        :custom-label="(opt) => $i18n.locale == 'ar' ? avenues[index].avenues.find((x) => x.id == opt).name : avenues[index].avenues.find((x) => x.id == opt).name_e"
                                                                                    >
                                                                                    </multiselect>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label class="control-label">
                                                                                        {{ getCompanyKey("boardRent_panel_street") }}
                                                                                    </label>
                                                                                    <multiselect
                                                                                        @input="showStreetModal(index)"
                                                                                        v-model="locations[index].street_id"
                                                                                        :options="streets[index].streets.map((type) => type.id)"
                                                                                        :custom-label="(opt) => $i18n.locale == 'ar' ? streets[index].streets.find((x) => x.id == opt).name : streets[index].streets.find((x) => x.id == opt).name_e"
                                                                                    >
                                                                                    </multiselect>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label class="control-label">
                                                                                        {{ getCompanyKey("boardRent_panel_face") }}
                                                                                    </label>
                                                                                    <multiselect
                                                                                        v-model="locations[index].face"
                                                                                        :options="faces"
                                                                                    >
                                                                                    </multiselect>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </b-modal>
                                                                <!--  /search   -->

                                                                <div class="row mb-2 mb-sm-0 py-25" :class="index % 2 == 0 ? 'bgc-default-l4' : ''">
                                                                    <div class="col-2">
                                                                        <multiselect
                                                                            :id="`create-${index}-1`"
                                                                            @input="showeCategoryModal(index)"
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
                                                                    <div class="col-2">
                                                                        <multiselect :id="`create-${index}-3`"
                                                                                     @input="showGovernorateModal(index)"
                                                                                     v-model="$v.create.details.$each[index].governorate_id.$model"
                                                                                     :options="governorates.map((type) => type.id)"
                                                                                     :custom-label="
                                                                                        (opt) =>
                                                                                            $i18n.locale == 'ar'
                                                                                                ? governorates.find((x) => x.id == opt).name
                                                                                                : governorates.find((x) => x.id == opt).name_e
                                                                                    " :class="{
                                                                                        'is-invalid':
                                                                                            $v.create.details.$each[index].governorate_id.$error || errors.governorate_id,
                                                                                    }">
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
                                                                    <div class="col-2">
                                                                        <div class="form-group">
                                                                            <date-picker
                                                                                type="date" v-model="multDate[index].from" defaultValue
                                                                                @change="v_startCreate($event,index)"
                                                                                confirm
                                                                                :class="{
                                                                                        'is-invalid':
                                                                                            $v.create.details.$each[index].from.$error || errors.from,
                                                                                    }"
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
                                                                    <div class="col-2">
                                                                        <div class="form-group">
                                                                            <date-picker type="date" v-model="multDate[index].to" defaultValue
                                                                                         @change="v_endCreate($event,index)" confirm
                                                                                         :class="{
                                                                                        'is-invalid':
                                                                                            $v.create.details.$each[index].to.$error || errors.from,
                                                                                    }"
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
                                                                    <div class="col-2">
                                                                        <input
                                                                            :id="`create-${index}-4`"
                                                                            v-model.number="$v.create.details.$each[index].price.$model"
                                                                            @input="changeValue"
                                                                            class="form-control" step=".01"
                                                                            type="number" :class="{
                                                                                        'is-invalid':
                                                                                            $v.create.details.$each[index].price.$error || errors.price,
                                                                                        'is-valid': !$v.create.details.$each[index].price.$invalid && !errors.price,
                                                                                    }" />
                                                                        <div v-if="!$v.create.details.$each[index].price.required"
                                                                             class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-1">
                                                                        <input
                                                                            :id="`create-${index}-4`"
                                                                            v-model.number="$v.create.details.$each[index].quantity.$model"
                                                                            @input="changeValue(index,'panel')"
                                                                            class="form-control"
                                                                            type="number" :class="{
                                                                                        'is-invalid':
                                                                                            $v.create.details.$each[index].quantity.$error || errors.quantity,
                                                                                    }" />
                                                                        <div v-if="!$v.create.details.$each[index].quantity.required"
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
                                                                            {{ $t('general.panel') }}
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
                                                                    <div
                                                                        class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                                                        {{ $t("general.Extra_note") }}
                                                                    </div>

                                                                    <div
                                                                        class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                                                        <div
                                                                            class="row my-2 align-items-center bgc-primary-l3 p-2">
                                                                            <div class="col-7 text-right">
                                                                                {{ $t("general.Total_Amount") }}
                                                                            </div>
                                                                            <div class="col-5">
                                                                                        <span
                                                                                            class="text-150 text-success-d3 opacity-2">
                                                                                            {{ !create.total ? '0.00' : create.total }}
                                                                                        </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            <hr />
                                                            <div>
                                                                <span class="text-secondary-d1 text-105">{{
                                                                        $t("general.Thank_you") }}
                                                                </span>
                                                                <a @click.prevent="addNewField" href=""
                                                                   class="btn btn-info btn-bold px-4 float-right mt-3 mx-2 mt-lg-0">{{
                                                                    $t("general.Add") }}</a>
                                                            </div>
                                                        </div>

                                                        <div class="mt-4" v-else>
                                                            <div
                                                                class="row text-600 text-white bgc-default-tp1 py-25">
                                                                <div class="col-1">#</div>
                                                                <div class="col-2">{{ getCompanyKey("boardRent_order_package") }}
                                                                </div>
                                                                <div class="col-2">{{ getCompanyKey("boardRent_order_from") }}</div>
                                                                <div class="col-2">{{
                                                                    getCompanyKey("boardRent_order_to") }}
                                                                </div>
                                                                <div class="col-2"> {{ getCompanyKey("boardRent_order_price")
                                                                    }}</div>
                                                                <div class="col-1">{{ $t("general.Action") }}</div>
                                                            </div>
                                                            <div v-for="(item, index) in create.details"
                                                                 class="text-95  text-secondary-d3"
                                                            >
                                                                <div class="row mb-2 mb-sm-0 py-25"
                                                                     :class="index % 2 == 0 ? 'bgc-default-l4' : ''">
                                                                    <div class="col-1">
                                                                        {{ index + 1 }}
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <multiselect
                                                                            :id="`create-${index}-1`"
                                                                            @input="showPackageModal(index)"
                                                                            v-model="$v.create.details.$each[index].package_id.$model"
                                                                            :options="packages.map((type) => type.id)"
                                                                            :custom-label="(opt) => $i18n.locale == 'ar' ? packages.find((x) => x.id == opt).name: packages.find((x) => x.id == opt).name_e"
                                                                            :class="{
                                                                                'is-invalid':
                                                                                    $v.create.details.$each[index].category_id.$error || errors.category_id,
                                                                            }"
                                                                        >
                                                                        </multiselect>
                                                                        <div v-if="!$v.create.details.$each[index].package_id.required"
                                                                             class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>
                                                                        <template v-if="errors.package_id">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.package_id"
                                                                                :key="index">{{ errorMessage
                                                                                }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <div class="form-group">
                                                                            <date-picker type="date" v-model="multDate[index].from" defaultValue
                                                                                         @change="v_startCreate($event,index)" confirm></date-picker>
                                                                            <template v-if="errors.from">
                                                                                <ErrorMessage v-for="(errorMessage, index) in errors.from"
                                                                                              :key="index">{{
                                                                                    errorMessage }}
                                                                                </ErrorMessage>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <div class="form-group">
                                                                            <date-picker type="date" v-model="multDate[index].to" defaultValue
                                                                                         @change="v_endCreate($event,index)" confirm></date-picker>
                                                                            <template v-if="errors.to">
                                                                                <ErrorMessage v-for="(errorMessage, index) in errors.to"
                                                                                              :key="index">
                                                                                    {{
                                                                                    errorMessage }}
                                                                                </ErrorMessage>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <input
                                                                            :id="`create-${index}-4`"
                                                                            v-model.number="$v.create.details.$each[index].price.$model"
                                                                            @input="changeValue"
                                                                            disabled
                                                                            class="form-control" step=".01"
                                                                            type="number" :class="{
                                                                                        'is-invalid':
                                                                                            $v.create.details.$each[index].price.$error || errors.price,
                                                                                        'is-valid': !$v.create.details.$each[index].price.$invalid && !errors.price,
                                                                                    }" />
                                                                        <div v-if="!$v.create.details.$each[index].price.required"
                                                                             class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-1">
                                                                        <button v-if="create.details.length > 1"
                                                                                type="button"
                                                                                @click.prevent="removeNewField(index)"
                                                                                class="custom-btn-dowonload">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row border-b-2 brc-default-l2"></div>
                                                            <div class="row mt-3">
                                                                <div
                                                                    class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                                                    {{ $t("general.Extra_note") }}
                                                                </div>

                                                                <div
                                                                    class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                                                    <div
                                                                        class="row my-2 align-items-center bgc-primary-l3 p-2">
                                                                        <div class="col-7 text-right">
                                                                            {{ $t("general.Total_Amount") }}
                                                                        </div>
                                                                        <div class="col-5">
                                                                                    <span
                                                                                        class="text-150 text-success-d3 opacity-2">
                                                                                        {{ !create.total ? '0.00' : create.total }}
                                                                                    </span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <hr />
                                                            <div>
                                                                <span class="text-secondary-d1 text-105">{{
                                                                        $t("general.Thank_you") }}
                                                                </span>
                                                                <a @click.prevent="addNewField" href=""
                                                                   class="btn btn-info btn-bold px-4 float-right mt-3 mx-2 mt-lg-0">{{
                                                                    $t("general.Add") }}</a>
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

                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>{{ getCompanyKey("branch") }}</label>
                                                            <multiselect @input="showBranchModalEdit" v-model="edit.branch_id"
                                                                         :options="branches.map((type) => type.id)" :custom-label="
                                                            (opt) =>
                                                                $i18n.locale == 'ar'
                                                                    ? branches.find((x) => x.id == opt).name
                                                                    : branches.find((x) => x.id == opt).name_e
                                                        " :class="{
                                                            'is-invalid':
                                                                $v.edit.branch_id.$error || errors.branch_id,
                                                        }">
                                                            </multiselect>
                                                            <div v-if="!$v.edit.branch_id.required" class="invalid-feedback">
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
                                                            <label>{{ getCompanyKey("order_serial") }}</label>
                                                            <input
                                                                v-model="$v.edit.serial_number.$model"
                                                                class="form-control"
                                                                type="text"
                                                                :class="{
                                                                'is-invalid':
                                                                    $v.edit.serial_number.$error || errors.serial_number,
                                                                'is-valid': !$v.edit.serial_number.$invalid && !errors.serial_number,
                                                            }" />
                                                            <template v-if="errors.serial_number">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.serial_number"
                                                                    :key="index">{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_order_date") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <date-picker type="date" v-model="date" defaultValue
                                                                         @change="v_dateEdit" confirm></date-picker>
                                                            <template v-if="errors.date">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.date"
                                                                              :key="index">{{
                                                                    errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>{{ getCompanyKey("sale_man") }}</label>
                                                            <multiselect @input="showSaleManModalEdit" v-model="edit.salesman_id"
                                                                         :options="salesmen.map((type) => type.id)" :custom-label="
                                                            (opt) =>
                                                                $i18n.locale == 'ar'
                                                                    ? salesmen.find((x) => x.id == opt).name
                                                                    : salesmen.find((x) => x.id == opt).name_e
                                                        " :class="{
                                                                'is-invalid':
                                                                    $v.edit.salesman_id.$error || errors.salesman_id,
                                                            }">
                                                            </multiselect>
                                                            <div v-if="!$v.edit.salesman_id.required" class="invalid-feedback">
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
                                                            <label>{{ getCompanyKey("boardRent_order_sellMethod") }}</label>
                                                            <multiselect @input="showSellMethodModalEdit" v-model="edit.sell_method_id"
                                                                         :options="sellMethods.map((type) => type.id)" :custom-label="
                                                            (opt) =>
                                                                $i18n.locale == 'ar'
                                                                    ? sellMethods.find((x) => x.id == opt).name
                                                                    : sellMethods.find((x) => x.id == opt).name_e
                                                        " :class="{
                                                        'is-invalid':
                                                            $v.edit.sell_method_id.$error || errors.sell_method_id,
                                                    }">
                                                            </multiselect>
                                                            <div v-if="!$v.edit.sell_method_id.required" class="invalid-feedback">
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.sell_method_id">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.sell_method_id"
                                                                              :key="index">{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3" >
                                                        <div class="form-group position-relative">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_unitStatus_status") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect @input="showStatusModalEdit" v-model="edit.status_id"
                                                                         :options="statuses.map((type) => type.id)"
                                                                         :custom-label="(opt) => $i18n.locale == 'ar'? statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e">
                                                            </multiselect>
                                                            <div v-if="$v.edit.status_id.$error || errors.status_id" class="text-danger">
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.status_id">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.status_id" :key="index">{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>{{ getCompanyKey("customer") }}</label>
                                                            <multiselect @input="showCustomerModalEdit" v-model="edit.customer_id"
                                                                         :options="customers.map((type) => type.id)" :custom-label="
                                                            (opt) =>
                                                                $i18n.locale == 'ar'
                                                                    ? customers.find((x) => x.id == opt).name
                                                                    : customers.find((x) => x.id == opt).name_e
                                                        " :class="{
                                                            'is-invalid':
                                                                $v.edit.customer_id.$error || errors.customer_id,
                                                        }">
                                                            </multiselect>
                                                            <div v-if="!$v.edit.customer_id.required" class="invalid-feedback">
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
                                                            <label class="control-label">
                                                                {{ getCompanyKey('general_customer_phone') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <VuePhoneNumberInput
                                                                v-model="phone"
                                                                :default-country-code="codeCountry"
                                                                valid-color="#28a745"
                                                                error-color="#dc3545"
                                                                disabled
                                                                :preferred-countries="['FR', 'EG', 'DE']"
                                                            />
                                                            <template v-if="errors.phone">
                                                                <ErrorMessage v-for="(errorMessage,index) in errors.phone" :key="index">{{
                                                                    errorMessage
                                                                    }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3" >
                                                        <div class="form-group">
                                                            <label class="mr-2">
                                                                {{ getCompanyKey("boardRent_order_is_stripe") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <b-form-group :class="{
                                                    'is-invalid': $v.edit.is_stripe.$error || errors.is_stripe,
                                                    'is-valid': !$v.edit.is_stripe.$invalid && !errors.is_stripe,
                                                  }">
                                                                <b-form-radio class="d-inline-block" v-model="$v.edit.is_stripe.$model" name="some-radios"
                                                                              value="1">{{ $t("general.Yes") }}</b-form-radio>
                                                                <b-form-radio class="d-inline-block m-1" v-model="$v.edit.is_stripe.$model" name="some-radios"
                                                                              value="0">{{ $t("general.No") }}</b-form-radio>
                                                            </b-form-group>
                                                            <template v-if="errors.is_stripe">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.is_stripe" :key="index">{{ errorMessage }}
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
                                                                                    <span class="text-default-d3">
                                                                        {{ $t("general.quotation") }}</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- .row -->
                                                                        <hr class="row brc-default-l1 mx-n1 mb-4" />

                                                                        <div class="mt-4" v-if="edit.is_stripe == 0">
                                                                            <div
                                                                                class="row text-600 text-white bgc-default-tp1 py-25">
                                                                                <div class="col-2">{{ getCompanyKey("boardRent_order_category") }}</div>
                                                                                <div class="col-2">{{ getCompanyKey("boardRent_order_governorate") }}</div>
                                                                                <div class="col-2">{{ getCompanyKey("boardRent_order_from") }}</div>
                                                                                <div class="col-2">{{
                                                                                    getCompanyKey("boardRent_order_to") }}
                                                                                </div>
                                                                                <div class="col-2"> {{ getCompanyKey("boardRent_order_price")
                                                                                    }}</div>
                                                                                <div class="col-1"> {{
                                                                                    getCompanyKey("boardRent_order_quantity") }}</div>
                                                                                <!--                                                                <div class="col-1">{{ $t("general.Action") }}</div>-->
                                                                            </div>
                                                                            <div v-for="(item, index) in edit.details"
                                                                                 class="text-95  text-secondary-d3">

                                                                                <!--  edit panels   -->
                                                                                <b-modal
                                                                                    :id="`edit-panel-quotation-${index}`"
                                                                                    :title="getCompanyKey('boardRent_panel_create_form')"
                                                                                    title-class="font-18"
                                                                                    body-class="p-1"
                                                                                    dialog-class="modal-full-width"
                                                                                    :hide-footer="true"
                                                                                >
                                                                                    <div :class="['row justify-content-center']">
                                                                                        <div :class="['col-5']">
                                                                                            <div class="face">
                                                                                                <span class="face-name">A : {{ faceNumbers[index].A }}</span>
                                                                                            </div>
                                                                                            <div class="face">
                                                                                                <span class="face-name">B : {{ faceNumbers[index].B }}</span>
                                                                                            </div>
                                                                                            <div class="face">
                                                                                                <span class="face-name">Multi : {{ faceNumbers[index].Multi }}</span>
                                                                                            </div>
                                                                                            <div class="face">
                                                                                                <span class="face-name">One-Face : {{ faceNumbers[index]["One-Face"] }}</span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="d-flex justify-content-end">
                                                                                        <div>
                                                                                            <b-button
                                                                                                @click.prevent="$bvModal.show(`search-${index}`)"
                                                                                                variant="primary"
                                                                                                class="mx-1 font-weight-bold"
                                                                                            >
                                                                                                {{ $t('general.Search') }}
                                                                                                <i class="fe-search"></i>
                                                                                            </b-button>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="row">
                                                                                        <!-- selet panals -->
                                                                                        <div class="col-md-6">
                                                                                            <!-- start Pagination -->
                                                                                            <div class="d-inline-flex align-items-center pagination-custom position-relative">
                                                                                                <div>
                                                                                                    <div class="d-inline-block" style="font-size: 13px">
                                                                                                        {{ pansPaginations[index].pansPaginations.from }}-{{ pansPaginations[index].pansPaginations.to }} /
                                                                                                        {{ pansPaginations[index].pansPaginations.total }}
                                                                                                    </div>
                                                                                                    <div class="d-inline-block">
                                                                                                        <a
                                                                                                            href="javascript:void(0)"
                                                                                                            :style="{
                                                                                              'pointer-events':
                                                                                                pansPaginations[index].pansPaginations.current_page > 1 ? '' : 'none',
                                                                                            }"
                                                                                                            @click.prevent="getPanel(pansPaginations[index].pansPaginations.current_page - 1,index)"
                                                                                                        >
                                                                                                            <span>&lt;</span>
                                                                                                        </a>
                                                                                                        <input
                                                                                                            type="text"
                                                                                                            @keyup.enter="getPanelPagination(index)"
                                                                                                            v-model="current_page_pans[index]"
                                                                                                            class="pagination-current-page"
                                                                                                        />
                                                                                                        <a
                                                                                                            href="javascript:void(0)"
                                                                                                            :style="{
                                                                                              'pointer-events':
                                                                                                (pansPaginations[index].pansPaginations.last_page ==
                                                                                                pansPaginations[index].pansPaginations.current_page) || !pansPaginations[index].pansPaginations
                                                                                                  ? 'none'
                                                                                                  : '',
                                                                                            }"
                                                                                                            @click.prevent="getPanel(pansPaginations[index].pansPaginations.current_page + 1)"
                                                                                                        >
                                                                                                            <span>&gt;</span>
                                                                                                        </a>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div style="position: absolute;transform: translate(230px, 0px)">
                                                                                                    <h3>{{ $t('general.SelectPanel') }}</h3>
                                                                                                </div>
                                                                                            </div>
                                                                                            <!-- end Pagination -->
                                                                                            <table class="table table-borderless table-hover table-centered m-0">
                                                                                                <thead>
                                                                                                <tr>
                                                                                                    <th>
                                                                                                        <div class="d-flex justify-content-center">
                                                                                                            <span>{{ getCompanyKey("boardRent_panel_code") }}</span>
                                                                                                        </div>
                                                                                                    </th>
                                                                                                    <th>
                                                                                                        <div class="d-flex justify-content-center">
                                                                                                            <span>{{ getCompanyKey("boardRent_panel_category") }}</span>
                                                                                                        </div>
                                                                                                    </th>
                                                                                                    <th >
                                                                                                        <div class="d-flex justify-content-center">
                                                                                                            <span>{{ getCompanyKey("boardRent_panel_governorate") }}</span>
                                                                                                        </div>
                                                                                                    </th>
                                                                                                    <th >
                                                                                                        <div class="d-flex justify-content-center">
                                                                                                            <span>{{ getCompanyKey("boardRent_panel_city") }}</span>
                                                                                                        </div>
                                                                                                    </th>
                                                                                                    <th >
                                                                                                        <div class="d-flex justify-content-center">
                                                                                                            <span>{{ getCompanyKey("boardRent_panel_avenue") }}</span>
                                                                                                        </div>
                                                                                                    </th>
                                                                                                    <th >
                                                                                                        <div class="d-flex justify-content-center">
                                                                                                            <span>{{ getCompanyKey("boardRent_panel_street") }}</span>
                                                                                                        </div>
                                                                                                    </th>
                                                                                                    <th >
                                                                                                        <div class="d-flex justify-content-center">
                                                                                                            <span>{{ getCompanyKey("boardRent_panel_face") }}</span>
                                                                                                        </div>
                                                                                                    </th>
                                                                                                    <th>
                                                                                                        {{ $t("general.Action") }}
                                                                                                    </th>
                                                                                                </tr>
                                                                                                </thead>
                                                                                                <tbody v-if="pans[index].pans.length > 0">
                                                                                                <tr
                                                                                                    v-for="(data, index) in pans[index].pans"
                                                                                                    :key="data.id"
                                                                                                    class="body-tr-custom"
                                                                                                >
                                                                                                    <td scope="col">
                                                                                                        {{ data.code }}
                                                                                                    </td>
                                                                                                    <td scope="col">
                                                                                                        {{ $i18n.locale == 'ar' ? data.category.name : data.category.name_e }}
                                                                                                    </td>
                                                                                                    <td scope="col">
                                                                                                        {{  data.governorate ? $i18n.locale == 'ar' ? data.governorate.name : data.governorate.name_e : '-' }}
                                                                                                    </td>
                                                                                                    <td scope="col">
                                                                                                        {{ data.city ? $i18n.locale == 'ar' ? data.city.name : data.city.name_e : '-' }}
                                                                                                    </td>
                                                                                                    <td scope="col">
                                                                                                        {{ data.avenue ? $i18n.locale == 'ar' ? data.avenue.name : data.avenue.name_e : '-' }}
                                                                                                    </td>
                                                                                                    <td scope="col">
                                                                                                        {{ data.street ? $i18n.locale == 'ar' ? data.street.name : data.street.name_e : '-' }}
                                                                                                    </td>
                                                                                                    <td scope="col">
                                                                                                        {{ data.face }}
                                                                                                    </td>
                                                                                                    <td scope="col" style="width: 0">
                                                                                                        <div class="form-check custom-control">
                                                                                                            <input
                                                                                                                class="form-check-input"
                                                                                                                type="checkbox"
                                                                                                                :value="data.id"
                                                                                                                @change="checkRowPanel(index,data)"
                                                                                                                v-model="CheckAllPanels[index]"
                                                                                                                style="width: 17px; height: 17px"
                                                                                                            />
                                                                                                        </div>
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

                                                                                        <!-- panals package -->
                                                                                        <div class="col-md-6">
                                                                                            <!-- start Pagination -->
                                                                                            <div class="d-inline-flex align-items-center pagination-custom position-relative">
                                                                                                <div>
                                                                                                    <div class="d-inline-block" style="font-size: 13px">
                                                                                                        {{ panelPackagesPaginatations[index].panelPackagesPaginatations.from }}-{{ panelPackagesPaginatations[index].panelPackagesPaginatations.to }} /
                                                                                                        {{ panelPackagesPaginatations[index].panelPackagesPaginatations.total }}
                                                                                                    </div>
                                                                                                    <div class="d-inline-block">
                                                                                                        <a
                                                                                                            href="javascript:void(0)"
                                                                                                            :style="{
                                                                                              'pointer-events':
                                                                                                panelPackagesPaginatations[index].panelPackagesPaginatations.current_page > 1 ? '' : 'none',
                                                                                            }"
                                                                                                            @click.prevent="paginate(panelPackagesPaginatations[index].panelPackagesPaginatations.current_page - 1,index)"
                                                                                                        >
                                                                                                            <span>&lt;</span>
                                                                                                        </a>
                                                                                                        <input
                                                                                                            type="text"
                                                                                                            @keyup.enter="paginate(current_page_pans_packs[index],index)"
                                                                                                            v-model="current_page_pans_packs[index]"
                                                                                                            class="pagination-current-page"
                                                                                                        />
                                                                                                        <a
                                                                                                            href="javascript:void(0)"
                                                                                                            :style="{
                                                                                              'pointer-events':
                                                                                                (panelPackagesPaginatations[index].panelPackagesPaginatations.last_page ==
                                                                                                panelPackagesPaginatations[index].panelPackagesPaginatations.current_page) || !panelPackagesPaginatations[index].panelPackagesPaginatations
                                                                                                  ? 'none'
                                                                                                  : '',
                                                                                            }"
                                                                                                            @click.prevent="paginate(panelPackagesPaginatations[index].panelPackagesPaginatations.current_page + 1)"
                                                                                                        >
                                                                                                            <span>&gt;</span>
                                                                                                        </a>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div style="position: absolute;transform: translate(230px, 0px)">
                                                                                                    <h3>{{ $t('general.Selected') }}</h3>
                                                                                                </div>
                                                                                            </div>
                                                                                            <!-- end Pagination -->

                                                                                            <table class="table table-borderless table-hover table-centered">
                                                                                                <thead>
                                                                                                <tr>
                                                                                                    <th>
                                                                                                        <div class="d-flex justify-content-center">
                                                                                                            <span>{{ getCompanyKey("boardRent_panel_code") }}</span>
                                                                                                        </div>
                                                                                                    </th>
                                                                                                    <th>
                                                                                                        <div class="d-flex justify-content-center">
                                                                                                            <span>{{ getCompanyKey("boardRent_panel_category") }}</span>
                                                                                                        </div>
                                                                                                    </th>
                                                                                                    <th >
                                                                                                        <div class="d-flex justify-content-center">
                                                                                                            <span>{{ getCompanyKey("boardRent_panel_governorate") }}</span>
                                                                                                        </div>
                                                                                                    </th>
                                                                                                    <th >
                                                                                                        <div class="d-flex justify-content-center">
                                                                                                            <span>{{ getCompanyKey("boardRent_panel_city") }}</span>
                                                                                                        </div>
                                                                                                    </th>
                                                                                                    <th >
                                                                                                        <div class="d-flex justify-content-center">
                                                                                                            <span>{{ getCompanyKey("boardRent_panel_avenue") }}</span>
                                                                                                        </div>
                                                                                                    </th>
                                                                                                    <th >
                                                                                                        <div class="d-flex justify-content-center">
                                                                                                            <span>{{ getCompanyKey("boardRent_panel_street") }}</span>
                                                                                                        </div>
                                                                                                    </th>
                                                                                                    <th >
                                                                                                        <div class="d-flex justify-content-center">
                                                                                                            <span>{{ getCompanyKey("boardRent_panel_face") }}</span>
                                                                                                        </div>
                                                                                                    </th>
                                                                                                    <th>
                                                                                                        {{ $t("general.Action") }}
                                                                                                    </th>
                                                                                                </tr>
                                                                                                </thead>
                                                                                                <tbody v-if="panelPackages[index].panelPackages.length > 0">
                                                                                                <tr
                                                                                                    v-for="(data, index) in panelPackages[index].panelPackages"
                                                                                                    :key="data.id"
                                                                                                    class="body-tr-custom"
                                                                                                >
                                                                                                    <td scope="col">
                                                                                                        {{ data.code }}
                                                                                                    </td>
                                                                                                    <td scope="col">
                                                                                                        {{ $i18n.locale == 'ar' ? data.category.name : data.category.name_e }}
                                                                                                    </td>
                                                                                                    <td scope="col">
                                                                                                        {{  data.governorate ? $i18n.locale == 'ar' ? data.governorate.name : data.governorate.name_e : '-' }}
                                                                                                    </td>
                                                                                                    <td scope="col">
                                                                                                        {{ data.city ? $i18n.locale == 'ar' ? data.city.name : data.city.name_e : '-' }}
                                                                                                    </td>
                                                                                                    <td scope="col">
                                                                                                        {{ data.avenue ? $i18n.locale == 'ar' ? data.avenue.name : data.avenue.name_e : '-' }}
                                                                                                    </td>
                                                                                                    <td scope="col">
                                                                                                        {{ data.street ? $i18n.locale == 'ar' ? data.street.name : data.street.name_e : '-' }}
                                                                                                    </td>
                                                                                                    <td scope="col">
                                                                                                        {{ data.face }}
                                                                                                    </td>
                                                                                                    <td scope="col" style="width: 0">
                                                                                                        <div class="form-check custom-control">
                                                                                                            <input
                                                                                                                class="form-check-input"
                                                                                                                type="checkbox"
                                                                                                                :value="data.id"
                                                                                                                @change="checkRowPanel(index,data)"
                                                                                                                v-model="CheckAllPanels[index]"
                                                                                                                style="width: 17px; height: 17px"
                                                                                                            />
                                                                                                        </div>
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
                                                                                    </div>
                                                                                </b-modal>
                                                                                <!--  /edit panels   -->

                                                                                <!--  search   -->
                                                                                <b-modal
                                                                                    :id="`search-edit-${index}`"
                                                                                    :title="$t('general.Search')"
                                                                                    title-class="font-18"
                                                                                    body-class="p-4"
                                                                                    size="lg"
                                                                                    :hide-footer="true"
                                                                                >
                                                                                    <form>
                                                                                        <div class="mb-3 d-flex justify-content-end">
                                                                                            <b-button
                                                                                                variant="success"
                                                                                                type="button" class="mx-1"
                                                                                                v-if="!isLoader"
                                                                                                @click.prevent="getPanel"
                                                                                            >
                                                                                                {{ $t('general.Search') }}
                                                                                            </b-button>

                                                                                            <b-button variant="success" class="mx-1" disabled v-else>
                                                                                                <b-spinner small></b-spinner>
                                                                                                <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                                                                            </b-button>
                                                                                            <!-- Emulate built in modal footer ok and cancel button actions -->

                                                                                            <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(`search-edit-${index}`)">
                                                                                                {{ $t('general.Cancel') }}
                                                                                            </b-button>
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label class="control-label">
                                                                                                        {{ getCompanyKey("boardRent_panel_city") }}
                                                                                                    </label>
                                                                                                    <multiselect
                                                                                                        @input="showCityModal(index)"
                                                                                                        v-model="locations[index].city_id"
                                                                                                        :options="cities[index].cities.map((type) => type.id)"
                                                                                                        :custom-label="(opt) => $i18n.locale == 'ar' ? cities[index].cities.find((x) => x.id == opt).name : cities[index].cities.find((x) => x.id == opt).name_e"
                                                                                                    >
                                                                                                    </multiselect>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label class="control-label">
                                                                                                        {{ getCompanyKey("boardRent_panel_avenue") }}
                                                                                                    </label>
                                                                                                    <multiselect
                                                                                                        @input="showAvenueModal(index)"
                                                                                                        v-model="locations[index].avenue_id"
                                                                                                        :options="avenues[index].avenues.map((type) => type.id)"
                                                                                                        :custom-label="(opt) => $i18n.locale == 'ar' ? avenues[index].avenues.find((x) => x.id == opt).name : avenues[index].avenues.find((x) => x.id == opt).name_e"
                                                                                                    >
                                                                                                    </multiselect>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label class="control-label">
                                                                                                        {{ getCompanyKey("boardRent_panel_street") }}
                                                                                                    </label>
                                                                                                    <multiselect
                                                                                                        @input="showStreetModal(index)"
                                                                                                        v-model="locations[index].street_id"
                                                                                                        :options="streets[index].streets.map((type) => type.id)"
                                                                                                        :custom-label="(opt) => $i18n.locale == 'ar' ? streets[index].streets.find((x) => x.id == opt).name : streets[index].streets.find((x) => x.id == opt).name_e"
                                                                                                    >
                                                                                                    </multiselect>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label class="control-label">
                                                                                                        {{ getCompanyKey("boardRent_panel_face") }}
                                                                                                    </label>
                                                                                                    <multiselect
                                                                                                        v-model="locations[index].face"
                                                                                                        :options="faces"
                                                                                                    >
                                                                                                    </multiselect>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                </b-modal>
                                                                                <!--  /search   -->

                                                                                <div class="row mb-2 mb-sm-0 py-25" :class="index % 2 == 0 ? 'bgc-default-l4' : ''">
                                                                                    <div class="col-2">
                                                                                        <multiselect
                                                                                            :id="`edit-${index}-1`"
                                                                                            @input="showCategoryModalEdit(index)"
                                                                                            v-model="$v.edit.details.$each[index].category_id.$model"
                                                                                            :options="categories.map((type) => type.id)"
                                                                                            :custom-label="(opt) => $i18n.locale == 'ar' ? categories.find((x) => x.id == opt).name: categories.find((x) => x.id == opt).name_e"
                                                                                            :class="{
                                                                                                'is-invalid':
                                                                                                    $v.edit.details.$each[index].category_id.$error || errors.category_id,
                                                                                            }">
                                                                                        </multiselect>
                                                                                        <div v-if="!$v.edit.details.$each[index].category_id.required"
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
                                                                                    <div class="col-2">
                                                                                        <multiselect :id="`edit-${index}-3`"
                                                                                                     @input="showGovernorateModalEdit(index)"
                                                                                                     v-model="$v.edit.details.$each[index].governorate_id.$model"
                                                                                                     :options="governorates.map((type) => type.id)"
                                                                                                     :custom-label="
                                                                                        (opt) =>
                                                                                            $i18n.locale == 'ar'
                                                                                                ? governorates.find((x) => x.id == opt).name
                                                                                                : governorates.find((x) => x.id == opt).name_e
                                                                                    " :class="{
                                                                                        'is-invalid':
                                                                                            $v.edit.details.$each[index].governorate_id.$error || errors.governorate_id,
                                                                                    }">
                                                                                        </multiselect>
                                                                                        <div v-if="!$v.edit.details.$each[index].governorate_id.required"
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
                                                                                    <div class="col-2">
                                                                                        <div class="form-group">
                                                                                            <date-picker
                                                                                                type="date" v-model="multDateEdit[index].from" defaultValue
                                                                                                @change="v_startEdit($event,index)"
                                                                                                confirm
                                                                                                :class="{
                                                                                        'is-invalid':
                                                                                            $v.edit.details.$each[index].from.$error || errors.from,
                                                                                    }"
                                                                                            ></date-picker>
                                                                                            <div v-if="!$v.edit.details.$each[index].from.required"
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
                                                                                    <div class="col-2">
                                                                                        <div class="form-group">
                                                                                            <date-picker type="date" v-model="multDateEdit[index].to" defaultValue
                                                                                                         @change="v_endEdit($event,index)" confirm
                                                                                                         :class="{
                                                                                        'is-invalid':
                                                                                            $v.edit.details.$each[index].to.$error || errors.from,
                                                                                    }"
                                                                                            ></date-picker>
                                                                                            <div v-if="!$v.edit.details.$each[index].to.required"
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
                                                                                    <div class="col-2">
                                                                                        <input
                                                                                            :id="`edit-${index}-4`"
                                                                                            v-model.number="$v.edit.details.$each[index].price.$model"
                                                                                            @input="changeValueEdit"
                                                                                            class="form-control" step=".01"
                                                                                            type="number" :class="{
                                                                                        'is-invalid':
                                                                                            $v.edit.details.$each[index].price.$error || errors.price,
                                                                                        'is-valid': !$v.edit.details.$each[index].price.$invalid && !errors.price,
                                                                                    }" />
                                                                                        <div v-if="!$v.edit.details.$each[index].price.required"
                                                                                             class="invalid-feedback">
                                                                                            {{ $t("general.fieldIsRequired") }}
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-1">
                                                                                        <input
                                                                                            :id="`edit-${index}-4`"
                                                                                            v-model.number="$v.edit.details.$each[index].quantity.$model"
                                                                                            @input="changeValueEdit"
                                                                                            class="form-control"
                                                                                            type="number" :class="{
                                                                                            'is-invalid':
                                                                                                $v.edit.details.$each[index].quantity.$error || errors.quantity,
                                                                                        }" />
                                                                                        <div v-if="!$v.edit.details.$each[index].quantity.required"
                                                                                             class="invalid-feedback">
                                                                                            {{ $t("general.fieldIsRequired") }}
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-1">
                                                                                        <button
                                                                                            type="button"
                                                                                            :disabled="!item.category_id && !item.governorate_id && !(item.quantity < 0)"
                                                                                            @click.prevent="showPanelEdit(index)"
                                                                                            class="custom-panel-quotation"
                                                                                        >
                                                                                            {{ $t('general.panel') }}
                                                                                        </button>
                                                                                        <button v-if="edit.details.length > 1"
                                                                                                type="button"
                                                                                                @click.prevent="removeNewFieldEdit(index)"
                                                                                                class="custom-btn-dowonload">
                                                                                            <i class="fas fa-trash-alt"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row border-b-2 brc-default-l2"></div>
                                                                            <div class="row mt-3">
                                                                                <div
                                                                                    class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                                                                    {{ $t("general.Extra_note") }}
                                                                                </div>

                                                                                <div
                                                                                    class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                                                                    <div
                                                                                        class="row my-2 align-items-center bgc-primary-l3 p-2">
                                                                                        <div class="col-7 text-right">
                                                                                            {{ $t("general.Total_Amount") }}
                                                                                        </div>
                                                                                        <div class="col-5">
                                                                                    <span
                                                                                        class="text-150 text-success-d3 opacity-2">
                                                                                        {{ !edit.total ? '0.00' : edit.total }}
                                                                                    </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                            <hr />
                                                                            <div>
                                                                <span class="text-secondary-d1 text-105">{{
                                                                        $t("general.Thank_you") }}
                                                                </span>
                                                                                <a @click.prevent="addNewField" href=""
                                                                                   class="btn btn-info btn-bold px-4 float-right mt-3 mx-2 mt-lg-0">{{
                                                                                    $t("general.Add") }}</a>
                                                                            </div>
                                                                        </div>

                                                                        <div class="mt-4" v-else>
                                                                            <div
                                                                                class="row text-600 text-white bgc-default-tp1 py-25">
                                                                                <div class="col-1">#</div>
                                                                                <div class="col-2">{{ getCompanyKey("boardRent_order_package") }}
                                                                                </div>
                                                                                <div class="col-2">{{ getCompanyKey("boardRent_order_from") }}</div>
                                                                                <div class="col-2">{{
                                                                                    getCompanyKey("boardRent_order_to") }}
                                                                                </div>
                                                                                <div class="col-2"> {{ getCompanyKey("boardRent_order_price")
                                                                                    }}</div>
                                                                                <div class="col-1">{{ $t("general.Action") }}</div>
                                                                            </div>
                                                                            <div v-for="(item, index) in edit.details"
                                                                                 class="text-95  text-secondary-d3">

                                                                                <div class="row mb-2 mb-sm-0 py-25"
                                                                                     :class="index % 2 == 0 ? 'bgc-default-l4' : ''">
                                                                                    <div class="col-1">
                                                                                        {{ index + 1 }}
                                                                                    </div>
                                                                                    <div class="col-2">
                                                                                        <multiselect
                                                                                            :id="`edit-${index}-1`"
                                                                                            @input="showPackageModalEdit(index)"
                                                                                            v-model="$v.edit.details.$each[index].package_id.$model"
                                                                                            :options="packages.map((type) => type.id)"
                                                                                            :custom-label="(opt) => $i18n.locale == 'ar' ? packages.find((x) => x.id == opt).name: packages.find((x) => x.id == opt).name_e"
                                                                                            :class="{
                                                                                                'is-invalid':
                                                                                                    $v.edit.details.$each[index].category_id.$error || errors.category_id,
                                                                                            }"
                                                                                        >
                                                                                        </multiselect>
                                                                                        <div v-if="!$v.edit.details.$each[index].package_id.required"
                                                                                             class="invalid-feedback">
                                                                                            {{ $t("general.fieldIsRequired") }}
                                                                                        </div>
                                                                                        <template v-if="errors.package_id">
                                                                                            <ErrorMessage
                                                                                                v-for="(errorMessage, index) in errors.package_id"
                                                                                                :key="index">{{ errorMessage
                                                                                                }}
                                                                                            </ErrorMessage>
                                                                                        </template>
                                                                                    </div>
                                                                                    <div class="col-2">
                                                                                        <div class="form-group">
                                                                                            <date-picker type="date" v-model="multDateEdit[index].from" defaultValue
                                                                                                         @change="v_startEdit($event,index)" confirm></date-picker>
                                                                                            <template v-if="errors.from">
                                                                                                <ErrorMessage v-for="(errorMessage, index) in errors.from"
                                                                                                              :key="index">{{
                                                                                                    errorMessage }}
                                                                                                </ErrorMessage>
                                                                                            </template>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-2">
                                                                                        <div class="form-group">
                                                                                            <date-picker type="date" v-model="multDateEdit[index].to" defaultValue
                                                                                                         @change="v_endEdit($event,index)" confirm></date-picker>
                                                                                            <template v-if="errors.to">
                                                                                                <ErrorMessage v-for="(errorMessage, index) in errors.to"
                                                                                                              :key="index">
                                                                                                    {{
                                                                                                    errorMessage }}
                                                                                                </ErrorMessage>
                                                                                            </template>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-2">
                                                                                        <input
                                                                                            :id="`edit-${index}-4`"
                                                                                            v-model.number="$v.edit.details.$each[index].price.$model"
                                                                                            @input="changeValueEdit"
                                                                                            disabled
                                                                                            class="form-control" step=".01"
                                                                                            type="number" :class="{
                                                                                        'is-invalid':
                                                                                            $v.edit.details.$each[index].price.$error || errors.price,
                                                                                        'is-valid': !$v.edit.details.$each[index].price.$invalid && !errors.price,
                                                                                    }" />
                                                                                        <div v-if="!$v.edit.details.$each[index].price.required"
                                                                                             class="invalid-feedback">
                                                                                            {{ $t("general.fieldIsRequired") }}
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-1">
                                                                                        <button v-if="edit.details.length > 1"
                                                                                                type="button"
                                                                                                @click.prevent="removeNewFieldEdit(index)"
                                                                                                class="custom-btn-dowonload">
                                                                                            <i class="fas fa-trash-alt"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row border-b-2 brc-default-l2"></div>
                                                                            <div class="row mt-3">
                                                                                <div
                                                                                    class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                                                                    {{ $t("general.Extra_note") }}
                                                                                </div>

                                                                                <div
                                                                                    class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                                                                    <div
                                                                                        class="row my-2 align-items-center bgc-primary-l3 p-2">
                                                                                        <div class="col-7 text-right">
                                                                                            {{ $t("general.Total_Amount") }}
                                                                                        </div>
                                                                                        <div class="col-5">
                                                                                    <span
                                                                                        class="text-150 text-success-d3 opacity-2">
                                                                                        {{ !edit.total ? '0.00' : edit.total }}
                                                                                    </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                            <hr />
                                                                            <div>
                                                                <span class="text-secondary-d1 text-105">{{
                                                                        $t("general.Thank_you") }}
                                                                </span>
                                                                                <a @click.prevent="addNewField" href=""
                                                                                   class="btn btn-info btn-bold px-4 float-right mt-3 mx-2 mt-lg-0">{{
                                                                                    $t("general.Add") }}</a>
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
    </Layout>
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

.face {
    display: inline-block;
    text-align: center;
    margin: 0 5px;
}

.face .face-name {
    background-color: #6dc6f5;
    padding: 0px 8px;
    font-size: 16px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 7px;
    display: block;
}
</style>
