<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import { required, minLength, maxLength, decimal, minValue, requiredIf } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import { dynamicSortString, dynamicSortNumber } from "../../../helper/tableSort";
import translation from "../../../helper/translation-mixin";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import Multiselect from "vue-multiselect";
import Governate from "../../../components/governate";
import City from "../../../components/city";
import Avnue from "../../../components/create/avenue";
import Street from "../../../components/create/street";
import Category from "../../../components/create/category";
/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Packages",
        meta: [{ name: "description", content: "Packages" }],
    },
    mixins: [translation],
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
        Governate,
        Avnue,
        Street,
        City,
        Category
    },
    data() {
        return {
            per_page: 50,
            search: "",
            debounce: {},
            enabled3: true,
            packagesPagination: {},
            packages: [],
            isLoader: false,
            Tooltip: "",
            mouseEnter: "",
            package_id: null,
            location: {city_id: null,governorate_id: null,avenue_id: null,category_id: null,face: null,street_id: null},
            panels: [],
            create: {
                name: "",
                name_e: "",
                code: "",
                price: "",
            },
            edit: {
                name: "",
                name_e: "",
                code: "",
                price: "",
            },
            faces: ['A','B','Multi','One-Face'],
            faceNumber: {'A': 0,'B': 0,'Multi': 0,'One-Face': 0},
            company_id: null,
            governorates: [],
            cities: [],
            avenues: [],
            streets: [],
            categories: [],
            pans: [],
            pansPagination: {},
            current_page_pans: 1,
            errors: {},
            isCheckAll: false,
            checkAll: [],
            allPanelPackages: [],
            panelPackages: [],
            panelPackagesPaginatation: {},
            current_page: 1,
            current_page_pans_pack: 1,
            setting: {
                name: true,
                name_e: true,
                code: true,
                price: true,
            },
            is_disabled: false,
            filterSetting: ["name", "name_e",'code','price'],
            printLoading: true,
            printObj: {
                id: "printUnitStatus",
            },
            is_panel: false,
            CheckAllPanel: [],
        };
    },
    validations: {
        create: {
            name: { required, minLength: minLength(2), maxLength: maxLength(255) },
            name_e: { required, minLength: minLength(2), maxLength: maxLength(255) },
            code: { required, decimal , minValue: minValue(0.01)},
            price: { required, decimal , minValue: minValue(0.01) },
        },
        edit: {
            name: { required, minLength: minLength(2), maxLength: maxLength(255) },
            name_e: { required, minLength: minLength(2), maxLength: maxLength(255) },
            code: { required, decimal , minValue: minValue(0.01)},
            price: { required, decimal , minValue: minValue(0.01) },
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
                this.packages.forEach((el) => {
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

            if (vm.$store.state.auth.work_flow_trees.includes('realEstate unit status')  || vm.$store.state.auth.work_flow_trees.includes('board rent') || vm.$store.state.auth.user.type == 'super_admin') {
                return true;
            } else {
                return vm.$router.push({ name: "home" });
            }
        });
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
        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if (this.mouseEnter != id) {
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`/boards-rent/packages/logs/${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.forEach((e) => {
                            this.Tooltip += `Created By: ${e.causer_type}; Event: ${
                                e.event
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
                    `/boards-rent/packages?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.packages = l.data;
                    this.packagesPagination = l.pagination;
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
                this.current_page <= this.packagesPagination.last_page &&
                this.current_page != this.packagesPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let filter = "";
                for (let i = 0; i < this.filterSetting.length; ++i) {
                    filter += `columns[${i}]=${this.filterSetting[i]}&`;
                }

                adminApi
                    .get(
                        `/boards-rent/packages?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}&company_id=${this.company_id}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.packages = l.data;
                        this.packagesPagination = l.pagination;
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
                            .delete(`/boards-rent/packages/bulk-delete`, { ids: id })
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
                            .delete(`/boards-rent/packages/${id}`)
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
                name: "",
                name_e: "",
                code: "",
                price: "",
            };
            this.package_id = null;
            this.location = {city_id: null,governorate_id: null,avenue_id: null,category_id: null,face: null,street_id: null};
            this.faceNumber = {'A': 0,'B': 0,'Multi': 0,'One-Face': 0};
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_panel = false;
            this.is_disabled = false;
            this.$bvModal.hide(`create`);
            this.current_page_pans_pack = 1;
            this.allPanelPackages = [];
            this.panelPackages = [];
            this.panelPackagesPaginatation = {};
            this.pans = [];
            this.pansPagination = {};
            this.current_page_pans = 1;
            this.cities = [];this.avenues = [];this.streets = [];this.pans = [];
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            this.create = {
                name: "",
                name_e: "",
                code: "",
                price: "",
            };
            this.package_id = null;
            await  this.getGovernorate();
            await  this.getCategory();
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        async getGovernorate() {

            this.governorates = [];this.cities = [];this.avenues = [];this.streets = [];

            await adminApi
                .get(`/governorates?columns[0]=country.id&search=1`)
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
                });
        },
        async getCity() {
            this.isLoader = true;
            let governorate = this.location.governorate_id;
            this.location.city_id = null;
            this.location.avenue_id = null;
            this.location.street_id = null;
            this.cities = [];this.avenues = [];this.streets = [];

            await adminApi
                .get(`/cities?columns[0]=governorate.id&search=${governorate}`)
                .then((res) => {
                    let l = res.data.data;
                    this.cities = l;
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
        async getAvnue() {
            this.isLoader = true;
            let city = this.location.city_id;
            this.location.avenue_id = null;
            this.location.street_id = null;
            this.avenues = [];this.streets = [];

            await adminApi
                .get(`/avenues?columns[0]=city.id&search=${city}`)
                .then((res) => {
                    let l = res.data.data;
                    this.avenues = l;
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
        async getStreet() {
            this.isLoader = true;
            let avenue = this.location.avenue_id;
            this.location.street_id = null;
            this.streets = [];

            await adminApi
                .get(`/streets?columns[0]=avenue.id&search=${avenue}`)
                .then((res) => {
                    let l = res.data.data;
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
        async getPanel() {
            this.isLoader = true;
            let category_id = this.location.category_id ?? null;
            let governorate_id = this.location.governorate_id ?? null;
            let city_id = this.location.city_id ?? null;
            let avenue_id = this.location.avenue_id ?? null;
            let face = this.location.face ?? null;
            let street_id = this.location.street_id ?? null;

            await adminApi
                .get(
                    `/boards-rent/panels?page=${1}&per_page=${7}&packages=1&category_id=${category_id}&governorate_id=${governorate_id}&city_id=${city_id}&avenue_id=${avenue_id}&street_id=${street_id}&face=${face}`
                )
                .then((res) => {
                    let l = res.data;
                    this.pans = l.data;
                    this.pansPagination = l.pagination;
                    this.current_page_pans = l.pagination.current_page;
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
        async getPanelPagination() {

            if (
                this.current_page_pans <= this.pansPagination.last_page &&
                this.current_page_pans != this.pansPagination.current_page &&
                this.current_page_pans
            ) {
                this.isLoader = true;
                let category_id = this.location.category_id ?? null;
                let governorate_id = this.location.governorate_id ?? null;
                let city_id = this.location.city_id ?? null;
                let avenue_id = this.location.avenue_id ?? null;
                let face = this.location.face ?? null;
                let street_id = this.location.street_id ?? null;

                await adminApi
                    .get(
                        `/boards-rent/panels?page=${1}&per_page=${7}&packages=1&category_id=${category_id}&governorate_id=${governorate_id}&city_id=${city_id}&avenue_id=${avenue_id}&street_id=${street_id}&face=${face}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.pans = l.data;
                        this.pansPagination = l.pagination;
                        this.current_page_pans = l.pagination.current_page;
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
        async getCategory() {
            this.isLoader = true;

            await adminApi
                .get(
                    `/categories`
                )
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
        /**
         *  create countrie
         */
        resetForm() {
            this.create = {
                name: "",
                name_e: "",
                code: "",
                price: "",
            };
            this.location = {city_id: null,governorate_id: null,avenue_id: null,category_id: null,face: null,street_id: null};
            this.faceNumber = {'A': 0,'B': 0,'Multi': 0,'One-Face': 0};
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.package_id = null;
            this.is_panel = false;
            this.is_disabled = false;
            this.current_page_pans_pack = 1;
            this.allPanelPackages = [];
            this.panelPackages = [];
            this.panelPackagesPaginatation = {};
            this.pans = [];
            this.pansPagination = {};
            this.current_page_pans = 1;
            this.cities = [];this.avenues = [];this.streets = [];this.pans = [];
        },
        AddSubmit() {
            if (!this.create.name) {
                this.create.name = this.create.name_e;
            }
            if (!this.create.name_e) {
                this.create.name_e = this.create.name;
            }
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                adminApi
                    .post(`/boards-rent/packages`, {...this.create,company_id: this.company_id,panels: this.CheckAllPanel})
                    .then((res) => {
                        this.is_disabled = true;
                        this.package_id = res.data.data.id;
                        this.edit = this.create;
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
        editSubmit(id = null) {
            if (!this.edit.name) {
                this.edit.name = this.edit.name_e;
            }
            if (!this.edit.name_e) {
                this.edit.name_e = this.edit.name;
            }
            this.$v.edit.$touch();
            this.$v.panels.$touch();
            if (this.$v.edit.$invalid || this.$v.panels.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .put(`/boards-rent/packages/${this.package_id}`, {...this.edit,panels: this.CheckAllPanel})
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
            this.cities = [];this.avenues = [];this.streets = [];this.pans = [];
            this.location = {city_id: null,governorate_id: null,avenue_id: null,category_id: null,face: null,street_id: null};
            this.is_panel = false;
            this.current_page_pans_pack = 1;
            let packages = this.packages.find((e) => id == e.id);
            this.package_id = packages.id;
            this.edit.name = packages.name;
            this.edit.name_e = packages.name_e;
            this.edit.code = packages.code;
            this.edit.price = packages.price;
            await  this.getGovernorate();
            await  this.getCategory();
            if(packages.panels && packages.panels.length > 0){
                this.allPanelPackages = packages.panels;
                this.allPanelPackages.forEach((el,index) => {
                    this.CheckAllPanel.push(el.id);
                });
                this.paginate();
                this.changeValuePanel();
            }else{
                this.cities = [];
                this.avenues = [];
                this.streets = [];
                this.pans = [];
                this.location= {category_id: null,governorate_id: null,city_id: null, avenue_id: null, street_id: null,face: ''};
                this.CheckAllPanel = [];
            }
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.errors = {};
            this.package_id = null;
            this.location = {city_id: null,governorate_id: null,avenue_id: null,category_id: null,face: null,street_id: null};
            this.cities = [];this.avenues = [];this.streets = [];this.pans = [];
            this.faceNumber = {'A': 0,'B': 0,'Multi': 0,'One-Face': 0};
            this.edit = {
                name: "",
                name_e: "",
                code: "",
                price: ""
            };
            this.current_page_pans_pack = 1;
            this.allPanelPackages = [];
            this.panelPackages = [];
            this.is_panel = false;
            this.panelPackagesPaginatation = {};
            this.pans = [];
            this.CheckAllPanel = [];
            this.pansPagination = {};
            this.current_page_pans = 1;
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
        checkRowPanel(data) {
            let panel = this.allPanelPackages.find((el) => el.id == data.id);
            if (!panel) {
                this.allPanelPackages.push(data);
            } else {
                let index = this.allPanelPackages.findIndex((el) => el.id == data.id);
                this.allPanelPackages.splice(index, 1);
            }
            this.paginate(this.current_page_pans_pack ? this.current_page_pans_pack : 1);
            this.changeValuePanel();
        },
        /**
         *  end  ckeckRow
         */
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
                if (dl) {
                    XLSX.write(wb, {bookType: type, bookSST: true, type: 'base64'});
                } else {
                    XLSX.writeFile(wb, fn || (('Package' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },
        async showGovernorateModal() {
            if (this.location.governorate_id == 0) {
                this.$bvModal.show("governorate-create");
                this.location.governorate_id = null;
            }else {
                await this.getCity();
            }
        },
        async showCityModal() {
            if (this.location.city_id == 0) {
                this.$bvModal.show("city-create");
                this.location.city_id = null;
            }else {
                await this.getAvnue();
            }
        },
        async showAvenueModal() {
            if (this.location.avenue_id == 0) {
                this.$bvModal.show("avenue-create");
                this.location.avenue_id = null;
            }else {
                await this.getStreet();
            }
        },
        async showStreetModal() {
            if (this.location.street_id == 0) {
                this.$bvModal.show("street-create");
                this.location.street_id = null;
            }
        },
        async showCategoryModal(){
            if (this.location.category_id == 0) {
                this.$bvModal.show("category-create");
                this.location.category_id = null;
            }
        },
        changeValuePanel (){
            this.faceNumber = {'A': 0,'B': 0,'Multi': 0,'One-Face': 0};
            this.allPanelPackages.forEach((e,index) => {
                if(e.face == 'A'){
                    this.faceNumber.A = this.faceNumber.A + 1
                }else if(e.face == 'B'){
                    this.faceNumber.B = this.faceNumber.B + 1
                }else if(e.face == 'Multi'){
                    this.faceNumber.Multi = this.faceNumber.Multi + 1
                }else if(e.face == 'One-Face'){
                    this.faceNumber['One-Face'] = this.faceNumber['One-Face'] + 1
                }
            });
        },
        // paginate
        paginate(p = 1){

            const page = p;
            this.current_page_pans_pack = page;
            const limit = 7;
            const skip = (page - 1) * limit;
            const endIndex = page * limit;
            const total = this.allPanelPackages.length;

            // Pagination result
            this.panelPackagesPaginatation.total = total;
            this.panelPackagesPaginatation.limit = limit;
            this.panelPackagesPaginatation.last_page = Math.ceil(total / limit);
            this.panelPackagesPaginatation.to = skip? (skip + limit) : limit;
            this.panelPackagesPaginatation.from = skip ? skip : 1;
            this.panelPackages = [];
            this.panelPackages = this.allPanelPackages.slice(skip,skip+limit);

        }
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
                            <h4 class="header-title">{{ $t("general.packagesTable") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown
                                        variant="primary"
                                        :text="$t('general.searchSetting')"
                                        ref="dropdown"
                                        class="btn-block setting-search"
                                    >
                                        <b-form-checkbox v-model="filterSetting" value="name" class="mb-1">{{
                                                getCompanyKey("boardRent_package_name_ar") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="name_e" class="mb-1">
                                            {{ getCompanyKey("boardRent_package_name_en") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="code" class="mb-1">{{
                                                getCompanyKey("boardRent_package_code") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="price" class="mb-1">
                                            {{ getCompanyKey("boardRent_package_price") }}
                                        </b-form-checkbox>
                                    </b-dropdown>
                                    <!-- Basic dropdown -->
                                </div>

                                <div class="d-inline-block position-relative" style="width: 77%">
                  <span
                      :class="[
                      'search-custom position-absolute',
                      $i18n.locale == 'ar' ? 'search-custom-ar' : '',
                    ]"
                  >
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
                                    <button v-print="'#printUnitStatus'" class="custom-btn-dowonload">
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
                                            <b-form-checkbox v-model="setting.name" class="mb-1"
                                            >{{ getCompanyKey("boardRent_package_name_ar") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.name_e" class="mb-1">
                                                {{ getCompanyKey("boardRent_package_name_en") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.code" class="mb-1"
                                            >{{ getCompanyKey("boardRent_package_code") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.price" class="mb-1">
                                                {{ getCompanyKey("boardRent_package_price") }}
                                            </b-form-checkbox>
                                            <div class="d-flex justify-content-end">
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm"
                                                >Apply</a
                                                >
                                            </div>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </div>
                                    <!-- end filter and setting -->

                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom">
                                        <div class="d-inline-block" style="font-size: 13px">
                                            {{ packagesPagination.from }}-{{ packagesPagination.to }} /
                                            {{ packagesPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{
                                                  'pointer-events':
                                                    packagesPagination.current_page == 1 ? 'none' : '',
                                                }"
                                                @click.prevent="getData(packagesPagination.current_page - 1)"
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
                                                    packagesPagination.last_page ==
                                                    packagesPagination.current_page
                                                      ? 'none'
                                                      : '',
                                                }"
                                                @click.prevent="getData(packagesPagination.current_page + 1)"
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
                            :title="getCompanyKey('boardRent_package_create_form')"
                            title-class="font-18"
                            :body-class="is_panel?'p-1':'p-4'"
                            :hide-footer="true"
                            @show="resetModal"
                            @hidden="resetModalHidden"
                            dialog-class="modal-full-width"
                        >
                            <form>
                                <div :class="{'position-relative': is_panel}">
                                    <div
                                        :class="['row justify-content-center']"
                                        v-if="package_id && is_panel"
                                    >
                                        <div :class="['col-5',{'position-absolute': is_panel}]">
                                            <div class="face">
                                                <span class="face-name">A : {{ faceNumber.A }}</span>
                                            </div>
                                            <div class="face">
                                                <span class="face-name">B : {{ faceNumber.B }}</span>
                                            </div>
                                            <div class="face">
                                                <span class="face-name">Multi : {{ faceNumber.Multi }}</span>
                                            </div>
                                            <div class="face">
                                                <span class="face-name">One-Face : {{ faceNumber["One-Face"] }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div :class="['d-flex justify-content-end']">
                                        <div :class="{'position-absolute': is_panel}">
                                            <b-button
                                                variant="success"
                                                :disabled="!is_disabled"
                                                @click.prevent="resetForm"
                                                type="button"
                                                :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
                                            >
                                                {{ $t("general.AddNewRecord") }}
                                            </b-button>
                                            <template v-if="!is_disabled">
                                                <b-button
                                                    variant="success"
                                                    type="button"
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
                                            <!-- Emulate built in modal footer ok and cancel button actions -->

                                            <b-button
                                                variant="danger"
                                                type="button"
                                                @click.prevent="resetModalHidden"
                                            >
                                                {{ $t("general.Cancel") }}
                                            </b-button>
                                        </div>
                                    </div>

                                    <b-tabs :content-class="is_panel?'tab-content-custom':''" nav-class="nav-tabs nav-bordered">
                                        <b-tab :title="$t('general.DataEntry')" active @click="is_panel = false">
                                            <div class="row">
                                                <div class="row col-7">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="field-1" class="control-label">
                                                                {{ getCompanyKey("boardRent_package_name_ar") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <div dir="rtl">
                                                                <input
                                                                    @keyup="arabicValue(create.name)"
                                                                    type="text"
                                                                    class="form-control"
                                                                    data-create="1"
                                                                    v-model="$v.create.name.$model"
                                                                    :class="{
                                                        'is-invalid': $v.create.name.$error || errors.name,
                                                        'is-valid': !$v.create.name.$invalid && !errors.name,
                                                      }"
                                                                    id="field-1"
                                                                />
                                                            </div>
                                                            <div v-if="!$v.create.name.minLength" class="invalid-feedback">
                                                                {{ $t("general.Itmustbeatleast") }}
                                                                {{ $v.create.name.$params.minLength.min }}
                                                                {{ $t("general.letters") }}
                                                            </div>
                                                            <div v-if="!$v.create.name.maxLength" class="invalid-feedback">
                                                                {{ $t("general.Itmustbeatmost") }}
                                                                {{ $v.create.name.$params.maxLength.max }}
                                                                {{ $t("general.letters") }}
                                                            </div>
                                                            <template v-if="errors.name">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.name"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="field-2" class="control-label">
                                                                {{ getCompanyKey("boardRent_package_name_en") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <div dir="ltr">
                                                                <input
                                                                    @keyup="englishValue(create.name_e)"
                                                                    type="text"
                                                                    class="form-control englishInput"
                                                                    data-create="2"
                                                                    v-model="$v.create.name_e.$model"
                                                                    :class="{
                                                        'is-invalid': $v.create.name_e.$error || errors.name_e,
                                                        'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                                                      }"
                                                                    id="field-2"
                                                                />
                                                            </div>
                                                            <div v-if="!$v.create.name_e.minLength" class="invalid-feedback">
                                                                {{ $t("general.Itmustbeatleast") }}
                                                                {{ $v.create.name_e.$params.minLength.min }}
                                                                {{ $t("general.letters") }}
                                                            </div>
                                                            <div v-if="!$v.create.name_e.maxLength" class="invalid-feedback">
                                                                {{ $t("general.Itmustbeatmost") }}
                                                                {{ $v.create.name_e.$params.maxLength.max }}
                                                                {{ $t("general.letters") }}
                                                            </div>
                                                            <template v-if="errors.name_e">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.name_e"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="field-3" class="control-label">
                                                                {{ getCompanyKey("boardRent_package_code") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <div >
                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    v-model="$v.create.code.$model"
                                                                    :class="{
                                                    'is-invalid': $v.create.code.$error || errors.code,
                                                    'is-valid': !$v.create.code.$invalid && !errors.code,
                                                  }"
                                                                    id="field-3"
                                                                />
                                                            </div>
                                                            <template v-if="errors.code">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.code"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="field-4" class="control-label">
                                                                {{ getCompanyKey("boardRent_package_price") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <div>
                                                                <input
                                                                    step=".01"
                                                                    type="number"
                                                                    class="form-control"
                                                                    v-model="$v.create.price.$model"
                                                                    :class="{
                                                    'is-invalid': $v.create.price.$error || errors.price,
                                                    'is-valid': !$v.create.price.$invalid && !errors.price,
                                                  }"
                                                                    id="field-4"
                                                                />
                                                            </div>
                                                            <template v-if="errors.price">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.price"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </b-tab>
                                        <b-tab
                                            :title="$t('general.panel')"
                                            :disabled="!package_id"
                                            @click="is_panel = !is_panel"
                                        >
                                            <div class="d-flex justify-content-end position-relative">
                                                <div>
                                                    <b-button
                                                        v-if="package_id && is_panel"
                                                        @click.prevent="$bvModal.show(`search`)"
                                                        variant="primary"
                                                        class="mx-1 font-weight-bold"
                                                    >
                                                        {{ $t('general.Search') }}
                                                        <i class="fe-search"></i>
                                                    </b-button>
                                                    <b-button
                                                        variant="success"
                                                        type="button"
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
                                                </div>
                                            </div>
                                            <div class="row">
                                                <!-- selet panals -->
                                                <div class="col-md-6">
                                                    <!-- start Pagination -->
                                                    <div class="d-inline-flex align-items-center pagination-custom position-relative">
                                                        <div>
                                                            <div class="d-inline-block" style="font-size: 13px">
                                                                {{ pansPagination.from }}-{{ pansPagination.to }} /
                                                                {{ pansPagination.total }}
                                                            </div>
                                                            <div class="d-inline-block">
                                                                <a
                                                                    href="javascript:void(0)"
                                                                    :style="{
                                                          'pointer-events':
                                                            pansPagination.current_page > 1 ? '' : 'none',
                                                        }"
                                                                    @click.prevent="getPanel(pansPagination.current_page - 1)"
                                                                >
                                                                    <span>&lt;</span>
                                                                </a>
                                                                <input
                                                                    type="text"
                                                                    @keyup.enter="getPanelPagination()"
                                                                    v-model="current_page_pans"
                                                                    class="pagination-current-page"
                                                                />
                                                                <a
                                                                    href="javascript:void(0)"
                                                                    :style="{
                                                          'pointer-events':
                                                            (pansPagination.last_page ==
                                                            pansPagination.current_page) || !pansPagination
                                                              ? 'none'
                                                              : '',
                                                        }"
                                                                    @click.prevent="getPanel(pansPagination.current_page + 1)"
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
                                                        <tbody v-if="pans.length > 0">
                                                        <tr
                                                            v-for="(data, index) in pans"
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
                                                                        @change="checkRowPanel(data)"
                                                                        v-model="CheckAllPanel"
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
                                                                {{ panelPackagesPaginatation.from }}-{{ panelPackagesPaginatation.to }} /
                                                                {{ panelPackagesPaginatation.total }}
                                                            </div>
                                                            <div class="d-inline-block">
                                                                <a
                                                                    href="javascript:void(0)"
                                                                    :style="{
                                                          'pointer-events':
                                                            current_page_pans_pack > 1 ? '' : 'none',
                                                        }"
                                                                    @click.prevent="paginate(current_page_pans_pack - 1)"
                                                                >
                                                                    <span>&lt;</span>
                                                                </a>
                                                                <input
                                                                    type="text"
                                                                    @keyup.enter="paginate(current_page_pans_pack)"
                                                                    v-model="current_page_pans_pack"
                                                                    class="pagination-current-page"
                                                                />
                                                                <a
                                                                    href="javascript:void(0)"
                                                                    :style="{
                                                          'pointer-events':
                                                            (panelPackagesPaginatation.last_page ==
                                                            current_page_pans_pack) || !panelPackagesPaginatation
                                                              ? 'none'
                                                              : '',
                                                        }"
                                                                    @click.prevent="paginate(current_page_pans_pack + 1)"
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
                                                        <tbody v-if="panelPackages.length > 0">
                                                        <tr
                                                            v-for="(data, index) in panelPackages"
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
                                                                        @change="checkRowPanel(data)"
                                                                        v-model="CheckAllPanel"
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

                                        </b-tab>
                                    </b-tabs>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <!--  search   -->
                        <b-modal
                            id="search"
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

                                    <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(`search`)">
                                        {{ $t('general.Cancel') }}
                                    </b-button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_category") }}
                                            </label>
                                            <multiselect
                                                @input="showCategoryModal"
                                                v-model="location.category_id"
                                                :options="categories.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? categories.find((x) => x.id == opt).name : categories.find((x) => x.id == opt).name_e"
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_governorate") }}
                                            </label>
                                            <multiselect
                                                @input="showGovernorateModal"
                                                v-model="location.governorate_id"
                                                :options="governorates.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? governorates.find((x) => x.id == opt).name : governorates.find((x) => x.id == opt).name_e"
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_city") }}
                                            </label>
                                            <multiselect
                                                @input="showCityModal"
                                                v-model="location.city_id"
                                                :options="cities.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? cities.find((x) => x.id == opt).name : cities.find((x) => x.id == opt).name_e"
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
                                                @input="showAvenueModal"
                                                v-model="location.avenue_id"
                                                :options="avenues.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? avenues.find((x) => x.id == opt).name : avenues.find((x) => x.id == opt).name_e"
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
                                                @input="showStreetModal"
                                                v-model="location.street_id"
                                                :options="streets.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? streets.find((x) => x.id == opt).name : streets.find((x) => x.id == opt).name_e"
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
                                                v-model="location.face"
                                                :options="faces"
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /search   -->

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative">
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->
                            <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                                   id="printUnitStatus">
                                <thead>
                                <tr>
                                    <th scope="col" style="width: 0" v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                v-model="isCheckAll"
                                                style="width: 17px; height: 17px"
                                            />
                                        </div>
                                    </th>
                                    <th v-if="setting.name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_package_name_ar") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="packages.sort(sortString('name'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="packages.sort(sortString('-name'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.name_e">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_package_name_en") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="packages.sort(sortString('name_e'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="packages.sort(sortString('-name_e'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.code">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_package_code") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.price">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_package_price") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t("general.Action") }}
                                    </th>
                                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                </tr>
                                </thead>
                                <tbody v-if="packages.length > 0">
                                <tr
                                    @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                                    v-for="(data, index) in packages"
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
                                    <td v-if="setting.name">
                                        <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                                    </td>
                                    <td v-if="setting.name_e">
                                        <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
                                    </td>
                                    <td v-if="setting.code">
                                        <h5 class="m-0 font-weight-normal">{{ data.code }}</h5>
                                    </td>
                                    <td v-if="setting.price">
                                        <h5 class="m-0 font-weight-normal">{{ data.price }}</h5>
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
                                            :title="getCompanyKey('boardRent_package_edit_form')"
                                            title-class="font-18"
                                            dialog-class="modal-full-width"
                                            :body-class="is_panel?'p-1':'p-4'"
                                            :ref="`edit-${data.id}`"
                                            :hide-footer="true"
                                            @show="resetModalEdit(data.id)"
                                            @hidden="resetModalHiddenEdit(data.id)"
                                        >
                                            <form>
                                                <div :class="{'position-relative': is_panel}">
                                                    <div :class="['row justify-content-center']"
                                                         v-if="is_panel"
                                                    >
                                                        <div :class="['col-5',{'position-absolute': is_panel}]">
                                                            <div class="face">
                                                                <span class="face-name">A : {{ faceNumber.A }}</span>
                                                            </div>
                                                            <div class="face">
                                                                <span class="face-name">B : {{ faceNumber.B }}</span>
                                                            </div>
                                                            <div class="face">
                                                                <span class="face-name">Multi : {{ faceNumber.Multi }}</span>
                                                            </div>
                                                            <div class="face">
                                                                <span class="face-name">One-Face : {{ faceNumber["One-Face"] }}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div :class="['d-flex justify-content-end']">
                                                        <div :class="{'position-absolute': is_panel}">
                                                            <!-- Emulate built in modal footer ok and cancel button actions -->
                                                            <b-button
                                                                variant="success"
                                                                type="submit"
                                                                class="mx-1"
                                                                v-if="!isLoader"
                                                                @click.prevent="editSubmit(data.id)"
                                                            >
                                                                {{ $t("general.Edit") }}
                                                            </b-button>

                                                            <b-button variant="success" class="mx-1" disabled v-else>
                                                                <b-spinner small></b-spinner>
                                                                <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                            </b-button>

                                                            <b-button
                                                                variant="danger"
                                                                type="button"
                                                                @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)"
                                                            >
                                                                {{ $t("general.Cancel") }}
                                                            </b-button>
                                                        </div>
                                                    </div>

                                                    <b-tabs :content-class="is_panel?'tab-content-custom':''" nav-class="nav-tabs nav-bordered">
                                                        <b-tab :title="$t('general.DataEntry')" @click="is_panel = false" active>
                                                            <div class="row">
                                                                <div class="row col-7">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="field-edit-1" class="control-label">
                                                                                {{ getCompanyKey("boardRent_package_name_ar") }}
                                                                                <span class="text-danger">*</span>
                                                                            </label>
                                                                            <div dir="rtl">
                                                                                <input
                                                                                    @keyup="arabicValue(edit.name)"
                                                                                    type="text"
                                                                                    class="form-control"
                                                                                    data-create="1"
                                                                                    v-model="$v.edit.name.$model"
                                                                                    :class="{
                                                                                    'is-invalid': $v.edit.name.$error || errors.name,
                                                                                    'is-valid': !$v.edit.name.$invalid && !errors.name,
                                                                                  }"
                                                                                    id="field-edit-1"
                                                                                />
                                                                            </div>
                                                                            <div v-if="!$v.edit.name.minLength" class="invalid-feedback">
                                                                                {{ $t("general.Itmustbeatleast") }}
                                                                                {{ $v.edit.name.$params.minLength.min }}
                                                                                {{ $t("general.letters") }}
                                                                            </div>
                                                                            <div v-if="!$v.edit.name.maxLength" class="invalid-feedback">
                                                                                {{ $t("general.Itmustbeatmost") }}
                                                                                {{ $v.edit.name.$params.maxLength.max }}
                                                                                {{ $t("general.letters") }}
                                                                            </div>
                                                                            <template v-if="errors.name">
                                                                                <ErrorMessage
                                                                                    v-for="(errorMessage, index) in errors.name"
                                                                                    :key="index"
                                                                                >{{ errorMessage }}</ErrorMessage
                                                                                >
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="field-edit-2" class="control-label">
                                                                                {{ getCompanyKey("boardRent_package_name_en") }}
                                                                                <span class="text-danger">*</span>
                                                                            </label>
                                                                            <div dir="ltr">
                                                                                <input
                                                                                    @keyup="englishValue(edit.name_e)"
                                                                                    type="text"
                                                                                    class="form-control englishInput"
                                                                                    data-create="2"
                                                                                    v-model="$v.edit.name_e.$model"
                                                                                    :class="{
                                                        'is-invalid': $v.edit.name_e.$error || errors.name_e,
                                                        'is-valid': !$v.edit.name_e.$invalid && !errors.name_e,
                                                      }"
                                                                                    id="field-edit-2"
                                                                                />
                                                                            </div>
                                                                            <div v-if="!$v.edit.name_e.minLength" class="invalid-feedback">
                                                                                {{ $t("general.Itmustbeatleast") }}
                                                                                {{ $v.edit.name_e.$params.minLength.min }}
                                                                                {{ $t("general.letters") }}
                                                                            </div>
                                                                            <div v-if="!$v.edit.name_e.maxLength" class="invalid-feedback">
                                                                                {{ $t("general.Itmustbeatmost") }}
                                                                                {{ $v.edit.name_e.$params.maxLength.max }}
                                                                                {{ $t("general.letters") }}
                                                                            </div>
                                                                            <template v-if="errors.name_e">
                                                                                <ErrorMessage
                                                                                    v-for="(errorMessage, index) in errors.name_e"
                                                                                    :key="index"
                                                                                >{{ errorMessage }}</ErrorMessage
                                                                                >
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="field-edit-3" class="control-label">
                                                                                {{ getCompanyKey("boardRent_package_code") }}
                                                                                <span class="text-danger">*</span>
                                                                            </label>
                                                                            <div >
                                                                                <input
                                                                                    type="text"
                                                                                    class="form-control"
                                                                                    v-model="$v.edit.code.$model"
                                                                                    :class="{
                                                    'is-invalid': $v.edit.code.$error || errors.code,
                                                    'is-valid': !$v.edit.code.$invalid && !errors.code,
                                                  }"
                                                                                    id="field-edit-3"
                                                                                />
                                                                            </div>
                                                                            <template v-if="errors.code">
                                                                                <ErrorMessage
                                                                                    v-for="(errorMessage, index) in errors.code"
                                                                                    :key="index"
                                                                                >{{ errorMessage }}</ErrorMessage
                                                                                >
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="field-edit-4" class="control-label">
                                                                                {{ getCompanyKey("boardRent_package_price") }}
                                                                                <span class="text-danger">*</span>
                                                                            </label>
                                                                            <div>
                                                                                <input
                                                                                    step=".01"
                                                                                    type="number"
                                                                                    class="form-control"
                                                                                    v-model="$v.edit.price.$model"
                                                                                    :class="{
                                                                                    'is-invalid': $v.edit.price.$error || errors.price,
                                                                                    'is-valid': !$v.edit.price.$invalid && !errors.price,
                                                                                  }"
                                                                                    id="field-edit-4"
                                                                                />
                                                                            </div>
                                                                            <template v-if="errors.price">
                                                                                <ErrorMessage
                                                                                    v-for="(errorMessage, index) in errors.price"
                                                                                    :key="index"
                                                                                >{{ errorMessage }}</ErrorMessage
                                                                                >
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </b-tab>
                                                        <b-tab
                                                            :title="$t('general.panel')"
                                                            :disabled="!package_id"
                                                            @click="is_panel = !is_panel"
                                                        >
                                                            <div class="d-flex justify-content-end position-relative">
                                                                <div>
                                                                    <b-button
                                                                        v-if="package_id && is_panel"
                                                                        @click.prevent="$bvModal.show(`search`)"
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
                                                                                {{ pansPagination.from }}-{{ pansPagination.to }} /
                                                                                {{ pansPagination.total }}
                                                                            </div>
                                                                            <div class="d-inline-block">
                                                                                <a
                                                                                    href="javascript:void(0)"
                                                                                    :style="{
                                                                              'pointer-events':
                                                                                pansPagination.current_page > 1 ? '' : 'none',
                                                                            }"
                                                                                    @click.prevent="getPanel(pansPagination.current_page - 1)"
                                                                                >
                                                                                    <span>&lt;</span>
                                                                                </a>
                                                                                <input
                                                                                    type="text"
                                                                                    @keyup.enter="getPanelPagination()"
                                                                                    v-model="current_page_pans"
                                                                                    class="pagination-current-page"
                                                                                />
                                                                                <a
                                                                                    href="javascript:void(0)"
                                                                                    :style="{
                                                          'pointer-events':
                                                            (pansPagination.last_page ==
                                                            pansPagination.current_page) || !pansPagination
                                                              ? 'none'
                                                              : '',
                                                        }"
                                                                                    @click.prevent="getPanel(pansPagination.current_page + 1)"
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
                                                                        <tbody v-if="pans.length > 0">
                                                                        <tr
                                                                            v-for="(data, index) in pans"
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
                                                                                        @change="checkRowPanel(data)"
                                                                                        v-model="CheckAllPanel"
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
                                                                                {{ panelPackagesPaginatation.from }}-{{ panelPackagesPaginatation.to }} /
                                                                                {{ panelPackagesPaginatation.total }}
                                                                            </div>
                                                                            <div class="d-inline-block">
                                                                                <a
                                                                                    href="javascript:void(0)"
                                                                                    :style="{
                                                                              'pointer-events':
                                                                                current_page_pans_pack > 1 ? '' : 'none',
                                                                            }"
                                                                                    @click.prevent="paginate(current_page_pans_pack - 1)"
                                                                                >
                                                                                    <span>&lt;</span>
                                                                                </a>
                                                                                <input
                                                                                    type="text"
                                                                                    @keyup.enter="paginate(current_page_pans_pack)"
                                                                                    v-model="current_page_pans_pack"
                                                                                    class="pagination-current-page"
                                                                                />
                                                                                <a
                                                                                    href="javascript:void(0)"
                                                                                    :style="{
                                                          'pointer-events':
                                                            (panelPackagesPaginatation.last_page ==
                                                            current_page_pans_pack) || !panelPackagesPaginatation
                                                              ? 'none'
                                                              : '',
                                                        }"
                                                                                    @click.prevent="paginate(current_page_pans_pack + 1)"
                                                                                >
                                                                                    <span>&gt;</span>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        <div style="position: absolute;transform: translate(250px, 0px)">
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
                                                                        <tbody v-if="panelPackages.length > 0">
                                                                        <tr
                                                                            v-for="(data, index) in panelPackages"
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
                                                                                        @change="checkRowPanel(data)"
                                                                                        v-model="CheckAllPanel"
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

                                                        </b-tab>
                                                    </b-tabs>
                                                </div>
                                            </form>
                                        </b-modal>
                                        <!--  /edit   -->
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <button
                                            @mouseover="log(data.id)"
                                            @mousemove="log(data.id)"
                                            type="button"
                                            class="btn"
                                            data-toggle="tooltip"
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

<style scoped>
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
