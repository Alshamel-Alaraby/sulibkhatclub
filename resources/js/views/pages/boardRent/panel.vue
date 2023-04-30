<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import { required, minLength, maxLength, decimal, minValue } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import { dynamicSortString, dynamicSortNumber } from "../../../helper/tableSort";
import translation from "../../../helper/translation-mixin";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import Multiselect from "vue-multiselect";
import Category from "../../../components/create/category";
import UnitStatus from "../../../components/create/boardRent/unitStatus";
import Country from "../../../components/country";
import Governate from "../../../components/governate";
import City from "../../../components/city";
import Avnue from "../../../components/create/avenue";
import Street from "../../../components/create/street";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Panels",
        meta: [{ name: "description", content: "Panels" }],
    },
    mixins: [translation],
    components: {
        Multiselect,
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        Country,
        Governate,
        Avnue,
        Street,
        City,
        loader,
        Category,
        UnitStatus
    },
    data() {
        return {
            per_page: 50,
            search: "",
            debounce: {},
            enabled3: true,
            panelsPagination: {},
            panels: [],
            isLoader: false,
            Tooltip: "",
            mouseEnter: "",
            unit_statuses: [],
            departments: [],
            categories: [],
            countries: [],
            governorates: [],
            cities: [],
            avenues: [],
            streets: [],
            faces: ['A','B','Multi','One-Face'],
            create: {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                price: {
                    day: .00,
                    week: .00,
                    month: .00,
                    quarter_year: .00,
                    half_year: .00,
                    year: .00,
                },
                code: "",
                new_code: "",
                size: "",
                note: "",
                face: "A",
                unit_status_id: null,
                category_id: null,
                country_id: null,
                governorate_id: null,
                city_id: null,
                avenue_id: null,
                street_id: null,
                lat: 0,
                lng: 0,
            },
            edit: {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                price: {
                    day: .00,
                    week: .00,
                    month: .00,
                    quarter_year: .00,
                    half_year: .00,
                    year: .00,
                },
                code: "",
                new_code: "",
                size: "",
                note: "",
                face: "A",
                unit_status_id: null,
                category_id: null,
                country_id: null,
                governorate_id: null,
                city_id: null,
                avenue_id: null,
                street_id: null,
                lat: 0,
                lng: 0,
            },
            company_id: null,
            errors: {},
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            setting: {
                name: true,
                name_e: true,
                description: true,
                description_e: true,
                code: true,
                new_code: true,
                size: true,
                note: true,
                face: true,
                unit_status_id: true,
                category_id: true,
                country_id: true,
                governorate_id: true,
                city_id: true,
                avenue_id: true,
                street_id: true,
                lat: true,
                lng: true,
            },
            is_disabled: false,
            filterSetting: [
                "name", "name_e","description","description_e","code","new_code","size","note","face", 'unit_status_id',
                'category_id','country_id','governorate_id', 'city_id', 'avenue_id', 'street_id'
            ],
            printLoading: true,
            printObj: {
                id: "printUnitStatus",
            }
        };
    },
    validations: {
        create: {
            name: { required, minLength: minLength(2), maxLength: maxLength(255) },
            name_e: { required, minLength: minLength(2), maxLength: maxLength(255) },
            description: {  maxLength: maxLength(1000) },
            description_e: {  maxLength: maxLength(1000) },
            price: {
                required,
                day: { required , minValue: minValue(.01)},
                week: {  minValue: minValue(.00)},
                month: {  minValue: minValue(.00)},
                quarter_year: {   minValue: minValue(.00)},
                half_year: {  minValue: minValue(.00)},
                year: { decimal , minValue: minValue(.00)},
            },
            code: { required, minLength: minLength(1), maxLength: maxLength(255) },
            new_code: {  minLength: minLength(1), maxLength: maxLength(255) },
            size: { required ,maxLength: maxLength(255) },
            note: {  maxLength: maxLength(255) },
            face: { required },
            unit_status_id: { required },
            category_id: { required },
            country_id: {  },
            governorate_id: {  },
            city_id: {  },
            avenue_id: {  },
            street_id: {  },
            lat: { required , decimal },
            lng: { required , decimal },
        },
        edit: {
            name: { required, minLength: minLength(2), maxLength: maxLength(255) },
            name_e: { required, minLength: minLength(2), maxLength: maxLength(255) },
            description: {  maxLength: maxLength(1000) },
            description_e: {  maxLength: maxLength(1000) },
            price: {
                required,
                day: { required , minValue: minValue(.01)},
                week: {  minValue: minValue(.00)},
                month: {  minValue: minValue(.00)},
                quarter_year: {   minValue: minValue(.00)},
                half_year: {  minValue: minValue(.00)},
                year: { decimal , minValue: minValue(.00)},
            },
            code: { required, minLength: minLength(1), maxLength: maxLength(255) },
            new_code: {  minLength: minLength(1), maxLength: maxLength(255) },
            size: { required ,maxLength: maxLength(255) },
            note: {  maxLength: maxLength(255) },
            face: { required },
            unit_status_id: { required },
            category_id: { required },
            country_id: {  },
            governorate_id: {  },
            city_id: {  },
            avenue_id: {  },
            street_id: {  },
            lat: { required , decimal },
            lng: { required , decimal },
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
                this.panels.forEach((el) => {
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
    // updated() {
    //   $(function () {
    //     $(".englishInput").keypress(function (event) {
    //       var ew = event.which;
    //       if (ew == 32) return true;
    //       if (48 <= ew && ew <= 57) return true;
    //       if (65 <= ew && ew <= 90) return true;
    //       if (97 <= ew && ew <= 122) return true;
    //       return false;
    //     });
    //     $(".arabicInput").keypress(function (event) {
    //       var ew = event.which;
    //       if (ew == 32) return true;
    //       if (48 <= ew && ew <= 57) return false;
    //       if (65 <= ew && ew <= 90) return false;
    //       if (97 <= ew && ew <= 122) return false;
    //       return true;
    //     });
    //   });
    // },
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
        arabicValueDescription(txt){
            this.create.description = arabicValue(txt);
            this.edit.description = arabicValue(txt);
        },
        englishValueDescription(txt){
            this.create.description_e = englishValue(txt);
            this.edit.description_e = englishValue(txt);
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if (this.mouseEnter != id) {
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`/boards-rent/panels/logs/${id}`)
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
            let _filterSetting = [...this.filterSetting];
            let unit_status = this.filterSetting.indexOf("unit_status_id");
            let category = this.filterSetting.indexOf("category_id");
            let country = this.filterSetting.indexOf("country_id");
            let city = this.filterSetting.indexOf("city_id");
            let governorate = this.filterSetting.indexOf("governorate_id");
            let avenue = this.filterSetting.indexOf("avenue_id");
            let street = this.filterSetting.indexOf("street_id");
            if (unit_status > -1) {_filterSetting[unit_status] = this.$i18n.locale == "ar" ? "unitStatus.name" : "unitStatus.name_e";}
            if (category > -1) {_filterSetting[category] = this.$i18n.locale == "ar" ? "category.name" : "category.name_e";}
            if (country > -1) {_filterSetting[country] = this.$i18n.locale == "ar" ? "country.name" : "country.name_e";}
            if (city > -1) {_filterSetting[city] = this.$i18n.locale == "ar" ? "city.name" : "city.name_e";}
            if (avenue > -1) {_filterSetting[avenue] = this.$i18n.locale == "ar" ? "city.name" : "city.name_e";}
            if (street > -1) {_filterSetting[street] = this.$i18n.locale == "ar" ? "avenue.name" : "avenue.name_e";}
            if (governorate > -1) {_filterSetting[governorate] = this.$i18n.locale == "ar" ? "governorate.name" : "governorate.name_e";}

            let filter = "";
            for (let i = 0; i < _filterSetting.length; ++i) {
                filter += `columns[${i}]=${_filterSetting[i]}&`;
            }

            adminApi
                .get(
                    `/boards-rent/panels?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.panels = l.data;
                    this.panelsPagination = l.pagination;
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
                this.current_page <= this.panelsPagination.last_page &&
                this.current_page != this.panelsPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let _filterSetting = [...this.filterSetting];
                let unit_status = this.filterSetting.indexOf("unit_status_id");
                let category = this.filterSetting.indexOf("category_id");
                let country = this.filterSetting.indexOf("country_id");
                let city = this.filterSetting.indexOf("city_id");
                let governorate = this.filterSetting.indexOf("governorate_id");
                let avenue = this.filterSetting.indexOf("avenue_id");
                let street = this.filterSetting.indexOf("street_id");
                if (unit_status > -1) {_filterSetting[unit_status] = this.$i18n.locale == "ar" ? "unitStatus.name" : "unitStatus.name_e";}
                if (category > -1) {_filterSetting[category] = this.$i18n.locale == "ar" ? "category.name" : "category.name_e";}
                if (country > -1) {_filterSetting[country] = this.$i18n.locale == "ar" ? "country.name" : "country.name_e";}
                if (city > -1) {_filterSetting[city] = this.$i18n.locale == "ar" ? "city.name" : "city.name_e";}
                if (avenue > -1) {_filterSetting[avenue] = this.$i18n.locale == "ar" ? "city.name" : "city.name_e";}
                if (street > -1) {_filterSetting[street] = this.$i18n.locale == "ar" ? "avenue.name" : "avenue.name_e";}
                if (governorate > -1) {_filterSetting[governorate] = this.$i18n.locale == "ar" ? "governorate.name" : "governorate.name_e";}

                let filter = "";
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }

                adminApi
                    .get(
                        `/boards-rent/panels?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}&company_id=${this.company_id}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.panels = l.data;
                        this.panelsPagination = l.pagination;
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
                            .post(`/boards-rent/panels/bulk-delete`, { ids: id })
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
                            .delete(`/boards-rent/panels/${id}`)
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
                description: "",
                description_e: "",
                price: {
                    day: 0,
                    week: 0,
                    month: 0,
                    quarter_year: 0,
                    half_year: 0,
                    year: 0,
                },
                code: "",
                new_code: "",
                size: "",
                note: "",
                face: "A",
                unit_status_id: null,
                category_id: null,
                country_id: null,
                governorate_id: null,
                city_id: null,
                avenue_id: null,
                street_id: null,
                lat: 0,
                lng: 0,
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
        async resetModal() {
            await  this.getUnitStatus();
            await  this.getCategory();
            await  this.getCountry();
            this.create = {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                price: {
                    day: 0,
                    week: 0,
                    month: 0,
                    quarter_year: 0,
                    half_year: 0,
                    year: 0,
                },
                code: "",
                new_code: "",
                size: "",
                note: "",
                face: "A",
                unit_status_id: null,
                category_id: null,
                country_id: null,
                governorate_id: null,
                city_id: null,
                avenue_id: null,
                street_id: null,
                lat: 0,
                lng: 0,
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
            this.create = {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                price: {
                    day: 0,
                    week: 0,
                    month: 0,
                    quarter_year: 0,
                    half_year: 0,
                    year: 0,
                },
                code: "",
                new_code: "",
                size: "",
                note: "",
                face: "A",
                unit_status_id: null,
                category_id: null,
                country_id: null,
                governorate_id: null,
                city_id: null,
                avenue_id: null,
                street_id: null,
                lat: 0,
                lng: 0,
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabled = false;
        },
        AddSubmit() {
            this.create.name = `${this.create.code} - ${this.create.face} - ${this.categories.find( e => e.id == this.create.category_id).name} - ${this.governorates.find( e => e.id == this.create.governorate_id).name}`;
            this.create.name_e =  `${this.create.code} - ${this.create.face} - ${this.categories.find( e => e.id == this.create.category_id).name_e} - ${this.governorates.find( e => e.id == this.create.governorate_id).name_e}`;
            if (!this.create.description) {
                this.create.description = this.create.description_e;
            }
            if (!this.create.description_e) {
                this.create.description_e = this.create.description;
            }

            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                adminApi
                    .post(`/boards-rent/panels`, {...this.create, company_id: this.company_id})
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
            this.edit.name = `${this.edit.code} - ${this.edit.face} - ${this.categories.find( e => e.id == this.edit.category_id).name} - ${this.edit.find( e => e.id == this.edit.governorate_id).name}`;
            this.edit.name_e =  `${this.edit.code} - ${this.edit.face} - ${this.categories.find( e => e.id == this.edit.category_id).name_e} - ${this.edit.find( e => e.id == this.edit.governorate_id).name_e}`;
            if (!this.edit.description) {
                this.edit.description = this.edit.description_e;
            }
            if (!this.edit.description_e) {
                this.edit.description_e = this.edit.description;
            }
            this.$v.edit.$touch();

            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .put(`/boards-rent/panels/${id}`, this.edit)
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
            let panels = this.panels.find((e) => id == e.id);
            this.edit.name = panels.name;
            this.edit.name_e = panels.name_e;
            this.edit.description = panels.description;
            this.edit.description_e = panels.description_e;
            this.edit.lng = panels.lng;
            this.edit.lat = panels.lat;
            this.edit.code = panels.code;
            this.edit.new_code = panels.new_code;
            this.edit.face = panels.face;
            await this.getCategory();
            this.edit.category_id = panels.category_id;
            await this.getUnitStatus();
            this.edit.unit_status_id = panels.unit_status_id;
            await this.getCategory();
            this.edit.country_id = panels.country_id;
            await this.getGovernorate();
            this.edit.governorate_id = panels.governorate_id;
            await this.getCity();
            this.edit.city_id = panels.city_id;
            await this.getAvnue();
            this.edit.avenue_id = panels.avenue_id;
            await this.getStreet();
            this.edit.street_id = panels.street_id;
            this.edit.note = panels.note;
            this.edit.size = panels.size;
            if(panels.price){
                this.edit.price.day = panels.price.day;
                this.edit.price.week = panels.price.week;
                this.edit.price.month = panels.price.month;
                this.edit.price.quarter_year = panels.price.quarter_year;
                this.edit.price.half_year = panels.price.half_year;
                this.edit.price.year = panels.price.year;
            }
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.errors = {};
            this.edit = {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                price: {
                    day: 0,
                    week: 0,
                    month: 0,
                    quarter_year: 0,
                    half_year: 0,
                    year: 0,
                },
                code: "",
                new_code: "",
                size: "",
                note: "",
                face: "A",
                unit_status_id: null,
                category_id: null,
                country_id: null,
                governorate_id: null,
                city_id: null,
                avenue_id: null,
                street_id: null,
                lat: 0,
                lng: 0,
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
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
                if (dl) {
                    XLSX.write(wb, {bookType: type, bookSST: true, type: 'base64'});
                } else {
                    XLSX.writeFile(wb, fn || (('Unit-Status' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
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
            this.create.lat = position.coords.latitude;
            this.create.lng = position.coords.longitude;
            this.edit.lat = position.coords.latitude;
            this.edit.lng = position.coords.longitude;
        },
        showUnitStatusModal() {
            if (this.create.unit_status_id == 0) {
                this.$bvModal.show("unitStatus-create");
                this.create.unit_status_id = null;
            }
        },
        showUnitStatusModalEdit() {
            if (this.edit.unit_status_id == 0) {
                this.$bvModal.show("unitStatus-create");
                this.edit.unit_status_id = null;
            }
        },
        showeCategoryModal() {
            if (this.create.category_id == 0) {
                this.$bvModal.show("category-create");
                this.create.category_id = null;
            }
        },
        showCategoryModalEdit() {
            if (this.edit.category_id == 0) {
                this.$bvModal.show("category-create");
                this.edit.category_id = null;
            }
        },
        async showCountryModal() {
            if (this.create.country_id == 0) {
                this.$bvModal.show("country-create");
                this.create.country_id = null;
            }else {
                await this.getGovernorate();
            }
        },
        async showCountryModalEdit() {
            if (this.edit.country_id == 0) {
                this.$bvModal.show("country-create");
                this.edit.country_id = null;
            }else {
                await this.getGovernorate();
            }
        },
        async showGovernorateModal() {
            if (this.create.governorate_id == 0) {
                this.$bvModal.show("governorate-create");
                this.create.governorate_id = null;
            }else {
                await this.getCity();
            }
        },
        async showGovernorateModalEdit() {
            if (this.edit.governorate_id == 0) {
                this.$bvModal.show("governorate-create");
                this.edit.governorate_id = null;
            }else {
                await this.getCity();
            }
        },
        async showCityModal() {
            if (this.create.city_id == 0) {
                this.$bvModal.show("city-create");
                this.create.city_id = null;
            }else {
                await this.getAvnue();
            }
        },
        async showCityModalEdit() {
            if (this.edit.city_id == 0) {
                this.$bvModal.show("city-create");
                this.edit.city_id = null;
            }else {
                await this.getAvnue();
            }
        },
        async showAvenueModal() {
            if (this.create.avenue_id == 0) {
                this.$bvModal.show("avenue-create");
                this.create.avenue_id = null;
            }else {
                await this.getStreet();
            }
        },
        async showAvenueModalEdit() {
            if (this.edit.avenue_id == 0) {
                this.$bvModal.show("avenue-create");
                this.edit.avenue_id = null;
            }else {
                await this.getStreet();
            }
        },
        showStreetModal() {
            if (this.create.street_id == 0) {
                this.$bvModal.show("street-create");
                this.create.street_id = null;
            }
        },
        showStreetModalEdit() {
            if (this.edit.street_id == 0) {
                this.$bvModal.show("street-create");
                this.edit.street_id = null;
            }
        },
        async getUnitStatus(){
            this.isLoader = true;

            adminApi
                .get(
                    `/boards-rent/unit_statuses`
                )
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف حاله الوحده", name_e: "Add Unit Status" });
                    this.unit_statuses = l;
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
        async getCategory() {
            this.isLoader = true;

            adminApi
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
        async getCountry() {
            this.isLoader = true;

            adminApi
                .get(
                    `/countries`
                )
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف الدوله", name_e: "Add Country" });
                    this.countries = l;
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
            let country = this.create.country_id ?? this.edit.country_id;
            this.create.governorate_id = null;this.edit.governorate_id = null;
            this.create.city_id = null;this.edit.city_id = null;
            this.create.avenue_id = null;this.edit.avenue_id = null;
            this.create.street_id = null;this.edit.street_id = null;
            this.governorates = [];this.cities = [];this.avenues = [];
            this.streets = [];

            await adminApi
                .get(`/governorates?columns[0]=country_id&search=${country}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف المحافظه", name_e: "Add Governorate" });
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
            let governorate = this.create.governorate_id ?? this.edit.governorate_id;
            this.create.city_id = null;this.edit.city_id = null;
            this.create.avenue_id = null;this.edit.avenue_id = null;
            this.create.street_id = null;this.edit.street_id = null;
            this.cities = [];this.avenues = [];this.streets = [];

            await adminApi
                .get(`/cities?columns[0]=governorate_id&search=${governorate}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف المدينه", name_e: "Add City" });
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
            let city = this.create.city_id ?? this.edit.city_id;
            this.create.avenue_id = null;this.edit.avenue_id = null;
            this.create.street_id = null;this.edit.street_id = null;
            this.avenues = [];this.streets = [];

            await adminApi
                .get(`/avenues?columns[0]=city_id&search=${city}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف المنطقه", name_e: "Add Avenue" });
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
            let avenue = this.create.avenue_id ?? this.edit.avenue_id;
            this.create.street_id = null;this.edit.street_id = null;
            this.streets = [];

            await adminApi
                .get(`/streets?columns[0]=avenue_id&search=${avenue}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف الشارع", name_e: "Add Street" });
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
    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <UnitStatus :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getUnitStatus" />
        <Category :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCategory" />
        <Country :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCountry" />
        <Governate :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getGovernorate" />
        <City :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCity" />
        <Avnue :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getAvnue" />
        <Street :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getStreet" />

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ $t("general.panelsTable") }}</h4>
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
                                                getCompanyKey("boardRent_panel_name_ar") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="name_e" class="mb-1">
                                            {{ getCompanyKey("boardRent_panel_name_en") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="description" class="mb-1">
                                            {{ getCompanyKey("boardRent_panel_description") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="description_e" class="mb-1">
                                            {{ getCompanyKey("boardRent_panel_description_e") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="code" class="mb-1">
                                            {{ getCompanyKey("boardRent_panel_code") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="new_code" class="mb-1">
                                            {{ getCompanyKey("boardRent_panel_new_code") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="size" class="mb-1">
                                            {{ getCompanyKey("boardRent_panel_size") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="note" class="mb-1">
                                            {{ getCompanyKey("boardRent_panel_note") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="unit_status_id" class="mb-1">
                                            {{ getCompanyKey("boardRent_panel_unit_status") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="category_id" class="mb-1">
                                            {{ getCompanyKey("boardRent_panel_category") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="country_id" class="mb-1">
                                            {{ getCompanyKey("boardRent_panel_country") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="governorate_id" class="mb-1">
                                            {{ getCompanyKey("boardRent_panel_governorate") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="city_id" class="mb-1">
                                            {{ getCompanyKey("boardRent_panel_city") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="avenue_id" class="mb-1">
                                            {{ getCompanyKey("boardRent_panel_avenue") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="street_id" class="mb-1">
                                            {{ getCompanyKey("boardRent_panel_street") }}
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
                                            >{{ getCompanyKey("boardRent_panel_name_ar") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.name_e" class="mb-1">
                                                {{ getCompanyKey("boardRent_panel_name_en") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.description" class="mb-1"
                                            >{{ getCompanyKey("boardRent_panel_description") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.description_e" class="mb-1">
                                                {{ getCompanyKey("boardRent_panel_description_e") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.category_id" class="mb-1">
                                                {{ getCompanyKey("boardRent_panel_category") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.unit_status_id" class="mb-1"
                                            >{{ getCompanyKey("boardRent_panel_unit_status") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.note" class="mb-1">
                                                {{ getCompanyKey("boardRent_panel_note") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.lat" class="mb-1"
                                            >{{ getCompanyKey("boardRent_panel_lat") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.lng" class="mb-1">
                                                {{ getCompanyKey("boardRent_panel_lng") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.code" class="mb-1"
                                            >{{ getCompanyKey("boardRent_panel_code") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.new_code" class="mb-1">
                                                {{ getCompanyKey("boardRent_panel_new_code") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.face" class="mb-1"
                                            >{{ getCompanyKey("boardRent_panel_face") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.country_id" class="mb-1">
                                                {{ getCompanyKey("boardRent_panel_country") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.governorate_id" class="mb-1"
                                            >{{ getCompanyKey("boardRent_panel_governorate") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.city_id" class="mb-1">
                                                {{ getCompanyKey("boardRent_panel_city") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.avenue_id" class="mb-1">
                                                {{ getCompanyKey("boardRent_panel_avenue") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.street_id" class="mb-1">
                                                {{ getCompanyKey("boardRent_avenue_street") }}
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
                                            {{ panelsPagination.from }}-{{ panelsPagination.to }} /
                                            {{ panelsPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{
                          'pointer-events':
                            panelsPagination.current_page == 1 ? 'none' : '',
                        }"
                                                @click.prevent="getData(panelsPagination.current_page - 1)"
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
                            panelsPagination.last_page ==
                            panelsPagination.current_page
                              ? 'none'
                              : '',
                        }"
                                                @click.prevent="getData(panelsPagination.current_page + 1)"
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
                            :title="getCompanyKey('boardRent_panel_create_form')"
                            title-class="font-18"
                            body-class="p-4"
                            dialog-class="modal-full-width"
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">
                                                {{ getCompanyKey("boardRent_panel_name_ar") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div dir="rtl">
                                                <input
                                                    @keyup="arabicValue(create.name)"
                                                    type="text"
                                                    disabled
                                                    class="form-control"
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
                                                {{ getCompanyKey("boardRent_panel_name_en") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div dir="ltr">
                                                <input
                                                    @keyup="englishValue(create.name_e)"
                                                    type="text"
                                                    disabled
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
                                </div>
                                <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="field-3" class="control-label">
                                                {{ getCompanyKey("boardRent_panel_code") }}
                                                <span class="text-danger">*</span>
                                            </label>
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
                                            <template v-if="errors.code">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.code"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="field-4" class="control-label">
                                                {{ getCompanyKey("boardRent_panel_new_code") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="$v.create.new_code.$model"
                                                :class="{
                                                    'is-invalid': $v.create.new_code.$error || errors.new_code,
                                                    'is-valid': !$v.create.new_code.$invalid && !errors.new_code,
                                                  }"
                                                id="field-4"
                                            />
                                            <template v-if="errors.new_code">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.new_code"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_face") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                v-model="create.face"
                                                :options="faces"
                                                >
                                            </multiselect>
                                            <div v-if="$v.create.face.$error || errors.face" class="text-danger">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.face">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.face"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_category") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                @input="showeCategoryModal"
                                                v-model="$v.create.category_id.$model"
                                                :options="categories.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? categories.find((x) => x.id == opt).name : categories.find((x) => x.id == opt).name_e"
                                            >
                                            </multiselect>
                                            <div v-if="$v.create.category_id.$error || errors.category_id" class="text-danger">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.category_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.category_id"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_unit_status") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                @input="showUnitStatusModal"
                                                v-model="$v.create.unit_status_id.$model"
                                                :options="unit_statuses.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? unit_statuses.find((x) => x.id == opt).name : unit_statuses.find((x) => x.id == opt).name_e"
                                            >
                                            </multiselect>
                                            <div v-if="$v.create.unit_status_id.$error || errors.unit_status_id" class="text-danger">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.unit_status_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.unit_status_id"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_country") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                @input="showCountryModal"
                                                v-model="$v.create.country_id.$model"
                                                :options="countries.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? countries.find((x) => x.id == opt).name :  countries.find((x) => x.id == opt).name_e"
                                            >
                                            </multiselect>
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
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_governorate") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                @input="showGovernorateModal"
                                                v-model="$v.create.governorate_id.$model"
                                                :options="governorates.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? governorates.find((x) => x.id == opt).name : governorates.find((x) => x.id == opt).name_e"
                                            >
                                            </multiselect>
                                            <template v-if="errors.governorate_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.governorate_id"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_city") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                @input="showCityModal"
                                                v-model="$v.create.city_id.$model"
                                                :options="cities.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? cities.find((x) => x.id == opt).name : cities.find((x) => x.id == opt).name_e"
                                            >
                                            </multiselect>
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
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_avenue") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                @input="showAvenueModal"
                                                v-model="$v.create.avenue_id.$model"
                                                :options="avenues.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? avenues.find((x) => x.id == opt).name : avenues.find((x) => x.id == opt).name_e"
                                            >
                                            </multiselect>
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
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_street") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                @input="showStreetModal"
                                                v-model="$v.create.street_id.$model"
                                                :options="streets.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ? streets.find((x) => x.id == opt).name : streets.find((x) => x.id == opt).name_e"
                                            >
                                            </multiselect>
                                            <template v-if="errors.street_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.street_id"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_lng") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                step="0.00000000000001"
                                                v-model="$v.create.lng.$model"
                                                :class="{
                                                'is-invalid': $v.create.lng.$error || errors.lng,
                                                'is-valid': !$v.create.lng.$invalid && !errors.lng,
                                              }"
                                            />
                                            <template v-if="errors.lng">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.lng"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_lat") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                data-create="9"
                                                step="0.00000000000001"
                                                v-model="$v.create.lat.$model"
                                                :class="{
                                                'is-invalid': $v.create.lat.$error || errors.lat,
                                                'is-valid': !$v.create.lat.$invalid && !errors.lat,
                                              }"
                                            />
                                            <template v-if="errors.lat">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.lat"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-center mt-3">
                                        <button
                                            class="btn btn-primary"
                                            type="button"
                                            @click="getLocation"
                                        >
                                            {{ $t('general.location') }}
                                        </button>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_day") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                step="0.01"
                                                v-model="$v.create.price.day.$model"
                                                :class="{
                                                'is-invalid': $v.create.price.day.$error,
                                                'is-valid': !$v.create.price.day.$invalid,
                                              }"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_week") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                data-create="9"
                                                step="0.01"
                                                v-model="$v.create.price.week.$model"
                                                :class="{
                                                'is-invalid': $v.create.price.week.$error,
                                                'is-valid': !$v.create.price.week.$invalid
                                              }"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_month") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                step="0.01"
                                                v-model="$v.create.price.month.$model"
                                                :class="{
                                                'is-invalid': $v.create.price.month.$error,
                                                'is-valid': !$v.create.price.month.$invalid
                                              }"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_quarter_year") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                data-create="9"
                                                step="0.01"
                                                v-model="$v.create.price.quarter_year.$model"
                                                :class="{
                                                'is-invalid': $v.create.price.quarter_year.$error,
                                                'is-valid': !$v.create.price.quarter_year.$invalid,
                                              }"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_half_year") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                step="0.01"
                                                v-model="$v.create.price.half_year.$model"
                                                :class="{
                                                'is-invalid': $v.create.price.half_year.$error,
                                                'is-valid': !$v.create.price.half_year.$invalid
                                              }"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_year") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                step="0.01"
                                                v-model="$v.create.price.year.$model"
                                                :class="{
                                                'is-invalid': $v.create.price.year.$error,
                                                'is-valid': !$v.create.price.year.$invalid,
                                              }"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-6" class="control-label">
                                                {{ getCompanyKey("boardRent_panel_note") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="$v.create.note.$model"
                                                :class="{
                                                    'is-invalid': $v.create.note.$error || errors.note,
                                                    'is-valid': !$v.create.note.$invalid && !errors.note,
                                                  }"
                                                id="field-6"
                                            />
                                            <template v-if="errors.note">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.note"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6"></div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="mr-2">
                                                {{ getCompanyKey('boardRent_panel_description') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <textarea
                                                @input="arabicValueDescription(create.description)"
                                                v-model="$v.create.description.$model"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid': $v.create.description.$error || errors.description,
                                                    'is-valid': !$v.create.description.$invalid && !errors.description,
                                                  }"
                                                :maxlength="1000" rows="5"
                                            ></textarea>
                                            <template v-if="errors.description">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.description"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="mr-2">
                                                {{ getCompanyKey('boardRent_panel_description_e') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <textarea
                                                @input="englishValueDescription(create.description_e)"
                                                v-model="$v.create.description_e.$model" class="form-control"
                                                :maxlength="1000" rows="5"
                                                :class="{
                                                    'is-invalid': $v.create.description_e.$error || errors.description_e,
                                                    'is-valid': !$v.create.description_e.$invalid && !errors.description_e,
                                                  }"
                                            ></textarea>
                                            <template v-if="errors.description_e">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.description_e"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
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
                                            <span>{{ getCompanyKey("boardRent_panel_name_ar") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="panels.sort(sortString('name'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="panels.sort(sortString('-name'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.name_e">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_name_en") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="panels.sort(sortString('name_e'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="panels.sort(sortString('-name_e'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.description">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_description") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="panels.sort(sortString('description'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="panels.sort(sortString('-description'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.description_e">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_description_e") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="panels.sort(sortString('description_e'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="panels.sort(sortString('-description_e'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.face">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_face") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.code">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_code") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.new_code">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_new_code") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.note">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_note") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.category_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_category") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.unit_status_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_unit_status") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.country_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_country") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.governorate_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_governorate") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.city_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_city") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.avenue_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_avenue") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.street_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_street") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.lng">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_lng") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.lat">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("boardRent_panel_lat") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t("general.Action") }}
                                    </th>
                                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                </tr>
                                </thead>
                                <tbody v-if="panels.length > 0">
                                <tr
                                    @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                                    v-for="(data, index) in panels"
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
                                    <th v-if="setting.description">
                                        {{ data.description }}
                                    </th>
                                    <th v-if="setting.description_e">
                                        {{ data.description_e }}
                                    </th>
                                    <th v-if="setting.face">
                                        {{ data.face }}
                                    </th>
                                    <th v-if="setting.code">
                                        {{ data.code }}
                                    </th>
                                    <th v-if="setting.new_code">
                                        {{ data.new_code }}
                                    </th>
                                    <th v-if="setting.note">
                                        {{ data.note }}
                                    </th>
                                    <th v-if="setting.category_id">
                                        {{ $i18n.locale == 'ar' ? data.category.name : data.category.name_e }}
                                    </th>
                                    <th v-if="setting.unit_status_id">
                                        {{ $i18n.locale == 'ar' ? data.unit_status.name : data.unit_status.name_e }}
                                    </th>
                                    <th v-if="setting.country_id">
                                        {{
                                            data.country ? $i18n.locale == 'ar' ? data.country.name : data.country.name_e :
                                                '-'
                                        }}
                                    </th>
                                    <th v-if="setting.governorate_id">
                                        {{  data.governorate ? $i18n.locale == 'ar' ? data.governorate.name : data.governorate.name_e : '-' }}
                                    </th>
                                    <th v-if="setting.city_id">
                                        {{ data.city ? $i18n.locale == 'ar' ? data.city.name : data.city.name_e : '-' }}
                                    </th>
                                    <th v-if="setting.avenue_id">
                                        {{ data.avenue ? $i18n.locale == 'ar' ? data.avenue.name : data.avenue.name_e : '-' }}
                                    </th>
                                    <th v-if="setting.street_id">
                                        {{ data.street ? $i18n.locale == 'ar' ? data.street.name : data.street.name_e : '-' }}
                                    </th>
                                    <th v-if="setting.lng">
                                        {{ data.lng }}
                                    </th>
                                    <th v-if="setting.lat">
                                        {{ data.lat }}
                                    </th>
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
                                            :title="getCompanyKey('boardRent_panel_edit_form')"
                                            title-class="font-18"
                                            body-class="p-4"
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
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="edit-1" class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_name_ar") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <div dir="rtl">
                                                                <input
                                                                    @keyup="arabicValue(edit.name)"
                                                                    type="text"
                                                                    disabled
                                                                    class="form-control"
                                                                    v-model="$v.edit.name.$model"
                                                                    :class="{
                                                    'is-invalid': $v.edit.name.$error || errors.name,
                                                    'is-valid': !$v.edit.name.$invalid && !errors.name,
                                                  }"
                                                                    id="edit-1"
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
                                                            <label for="edit-2" class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_name_en") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <div dir="ltr">
                                                                <input
                                                                    @keyup="englishValue(edit.name_e)"
                                                                    type="text"
                                                                    disabled
                                                                    class="form-control englishInput"
                                                                    data-edit="2"
                                                                    v-model="$v.edit.name_e.$model"
                                                                    :class="{
                                                        'is-invalid': $v.edit.name_e.$error || errors.name_e,
                                                        'is-valid': !$v.edit.name_e.$invalid && !errors.name_e,
                                                      }"
                                                                    id="edit-2"
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
                                                </div>
                                                <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="edit-3" class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_code") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                v-model="$v.edit.code.$model"
                                                                :class="{
                                                    'is-invalid': $v.edit.code.$error || errors.code,
                                                    'is-valid': !$v.edit.code.$invalid && !errors.code,
                                                  }"
                                                                id="edit-3"
                                                            />
                                                            <template v-if="errors.code">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.code"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="edit-4" class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_new_code") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                v-model="$v.edit.new_code.$model"
                                                                :class="{
                                                    'is-invalid': $v.edit.new_code.$error || errors.new_code,
                                                    'is-valid': !$v.edit.new_code.$invalid && !errors.new_code,
                                                  }"
                                                                id="edit-4"
                                                            />
                                                            <template v-if="errors.new_code">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.new_code"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_face") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                v-model="edit.face"
                                                                :options="faces"
                                                            >
                                                            </multiselect>
                                                            <div v-if="$v.edit.face.$error || errors.face" class="text-danger">
                                                                {{ $t("general.editIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.face">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.face"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_category") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                @input="showeCategoryModalEdit"
                                                                v-model="$v.edit.category_id.$model"
                                                                :options="categories.map((type) => type.id)"
                                                                :custom-label="(opt) => $i18n.locale == 'ar' ? categories.find((x) => x.id == opt).name : categories.find((x) => x.id == opt).name_e"
                                                            >
                                                            </multiselect>
                                                            <div v-if="$v.edit.category_id.$error || errors.category_id" class="text-danger">
                                                                {{ $t("general.editIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.category_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.category_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_unit_status") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                @input="showUnitStatusModalEdit"
                                                                v-model="$v.edit.unit_status_id.$model"
                                                                :options="unit_statuses.map((type) => type.id)"
                                                                :custom-label="(opt) => $i18n.locale == 'ar' ? unit_statuses.find((x) => x.id == opt).name : unit_statuses.find((x) => x.id == opt).name_e"
                                                            >
                                                            </multiselect>
                                                            <div v-if="$v.edit.unit_status_id.$error || errors.unit_status_id" class="text-danger">
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.unit_status_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.unit_status_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_country") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                @input="showCountryModalEdit"
                                                                v-model="$v.edit.country_id.$model"
                                                                :options="countries.map((type) => type.id)"
                                                                :custom-label="(opt) => $i18n.locale == 'ar' ? countries.find((x) => x.id == opt).name :  countries.find((x) => x.id == opt).name_e"
                                                            >
                                                            </multiselect>
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
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_governorate") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                @input="showGovernorateModalEdit"
                                                                v-model="$v.edit.governorate_id.$model"
                                                                :options="governorates.map((type) => type.id)"
                                                                :custom-label="(opt) => $i18n.locale == 'ar' ? governorates.find((x) => x.id == opt).name : governorates.find((x) => x.id == opt).name_e"
                                                            >
                                                            </multiselect>
                                                            <template v-if="errors.governorate_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.governorate_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_city") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                @input="showCityModalEdit"
                                                                v-model="$v.edit.city_id.$model"
                                                                :options="cities.map((type) => type.id)"
                                                                :custom-label="(opt) => $i18n.locale == 'ar' ? cities.find((x) => x.id == opt).name : cities.find((x) => x.id == opt).name_e"
                                                            >
                                                            </multiselect>
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
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_avenue") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                @input="showAvenueModalEdit"
                                                                v-model="$v.edit.avenue_id.$model"
                                                                :options="avenues.map((type) => type.id)"
                                                                :custom-label="(opt) => $i18n.locale == 'ar' ? avenues.find((x) => x.id == opt).name : avenues.find((x) => x.id == opt).name_e"
                                                            >
                                                            </multiselect>
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
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_street") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                @input="showStreetModalEdit"
                                                                v-model="$v.edit.street_id.$model"
                                                                :options="streets.map((type) => type.id)"
                                                                :custom-label="(opt) => $i18n.locale == 'ar' ? streets.find((x) => x.id == opt).name : streets.find((x) => x.id == opt).name_e"
                                                            >
                                                            </multiselect>
                                                            <template v-if="errors.street_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.street_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_lng") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="number"
                                                                class="form-control"
                                                                step="0.00000000000001"
                                                                v-model="$v.edit.lng.$model"
                                                                :class="{
                                                'is-invalid': $v.edit.lng.$error || errors.lng,
                                                'is-valid': !$v.edit.lng.$invalid && !errors.lng,
                                              }"
                                                            />
                                                            <template v-if="errors.lng">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.lng"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_lat") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="number"
                                                                class="form-control"
                                                                data-edit="9"
                                                                step="0.00000000000001"
                                                                v-model="$v.edit.lat.$model"
                                                                :class="{
                                                'is-invalid': $v.edit.lat.$error || errors.lat,
                                                'is-valid': !$v.edit.lat.$invalid && !errors.lat,
                                              }"
                                                            />
                                                            <template v-if="errors.lat">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.lat"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 d-flex align-items-center mt-3">
                                                        <button
                                                            class="btn btn-primary"
                                                            type="button"
                                                            @click="getLocation"
                                                        >
                                                            {{ $t('general.location') }}
                                                        </button>
                                                    </div>
                                                </div>
                                                <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_day") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="number"
                                                                class="form-control"
                                                                step="0.01"
                                                                v-model="$v.edit.price.day.$model"
                                                                :class="{
                                                'is-invalid': $v.edit.price.day.$error,
                                                'is-valid': !$v.edit.price.day.$invalid,
                                              }"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_week") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="number"
                                                                class="form-control"
                                                                data-edit="9"
                                                                step="0.01"
                                                                v-model="$v.edit.price.week.$model"
                                                                :class="{
                                                'is-invalid': $v.edit.price.week.$error,
                                                'is-valid': !$v.edit.price.week.$invalid
                                              }"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_month") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="number"
                                                                class="form-control"
                                                                step="0.01"
                                                                v-model="$v.edit.price.month.$model"
                                                                :class="{
                                                'is-invalid': $v.edit.price.month.$error,
                                                'is-valid': !$v.edit.price.month.$invalid
                                              }"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_quarter_year") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="number"
                                                                class="form-control"
                                                                data-edit="9"
                                                                step="0.01"
                                                                v-model="$v.edit.price.quarter_year.$model"
                                                                :class="{
                                                'is-invalid': $v.edit.price.quarter_year.$error,
                                                'is-valid': !$v.edit.price.quarter_year.$invalid,
                                              }"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_half_year") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="number"
                                                                class="form-control"
                                                                step="0.01"
                                                                v-model="$v.edit.price.half_year.$model"
                                                                :class="{
                                                'is-invalid': $v.edit.price.half_year.$error,
                                                'is-valid': !$v.edit.price.half_year.$invalid
                                              }"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_year") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="number"
                                                                class="form-control"
                                                                step="0.01"
                                                                v-model="$v.edit.price.year.$model"
                                                                :class="{
                                                'is-invalid': $v.edit.price.year.$error,
                                                'is-valid': !$v.edit.price.year.$invalid,
                                              }"
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="edit-6" class="control-label">
                                                                {{ getCompanyKey("boardRent_panel_note") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                v-model="$v.edit.note.$model"
                                                                :class="{
                                                    'is-invalid': $v.edit.note.$error || errors.note,
                                                    'is-valid': !$v.edit.note.$invalid && !errors.note,
                                                  }"
                                                                id="edit-6"
                                                            />
                                                            <template v-if="errors.note">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.note"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6"></div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="mr-2">
                                                                {{ getCompanyKey('boardRent_panel_description') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <textarea
                                                                @input="arabicValueDescription(edit.description)"
                                                                v-model="$v.edit.description.$model"
                                                                class="form-control"
                                                                :class="{
                                                    'is-invalid': $v.edit.description.$error || errors.description,
                                                    'is-valid': !$v.edit.description.$invalid && !errors.description,
                                                  }"
                                                                :maxlength="1000" rows="5"
                                                            ></textarea>
                                                            <template v-if="errors.description">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.description"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="mr-2">
                                                                {{ getCompanyKey('boardRent_panel_description_e') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <textarea
                                                                @input="englishValueDescription(edit.description_e)"
                                                                v-model="$v.edit.description_e.$model" class="form-control"
                                                                :maxlength="1000" rows="5"
                                                                :class="{
                                                    'is-invalid': $v.edit.description_e.$error || errors.description_e,
                                                    'is-valid': !$v.edit.description_e.$invalid && !errors.description_e,
                                                  }"
                                                            ></textarea>
                                                            <template v-if="errors.description_e">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.description_e"
                                                                    :key="index"
                                                                >{{ errorMessage }}</ErrorMessage
                                                                >
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
                                    <th class="text-center" colspan="25">
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
