<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {required, minLength, maxLength, integer, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import alphaArabic from "../../../helper/alphaArabic";
import alphaEnglish from "../../../helper/alphaEnglish";
import { dynamicSortString, dynamicSortNumber } from "../../../helper/tableSort";
import { formatDateOnly } from "../../../helper/startDate";
import translation from "../../../helper/mixin/translation-mixin";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import permissionGuard from "../../../helper/permission";
import Multiselect from "vue-multiselect";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "booking rooms",
        meta: [{ name: "booking rooms", content: "booking rooms" }],
    },
    mixins: [translation],
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Booking Rooms", "all Booking Rooms");
        });
    },
    data() {
        return {
            statuses: [],
            fields: [],
            enabled3: true,
            printLoading: true,
            printObj: {
                id: "printBuilding",
            },
            per_page: 50,
            search: "",
            debounce: {},
            categoriesPagination: {},
            categories: [],
            floors: [],
            isLoader: false,
            Tooltip: "",
            mouseEnter: null,
            company_id:null,
            create: {
                name: "",
                name_e: "",
                code: "",
                price: "",
                description: "",
                description_e: "",
                unit_status_id: 12,
                number_of_individuals:1,
                extra_guest_price:0,
                booking_floor_id:null,
            },
            edit: {
                name: "",
                name_e: "",
                code: "",
                price: "",
                description: "",
                description_e: "",
                unit_status_id: null,
                number_of_individuals:1,
                extra_guest_price:0,
                booking_floor_id:null,
            },
            errors: {},
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            setting: {
                name: true,
                name_e: true,
                code: true,
                price: true,
                description: true,
                description_e: true,
                unit_status_id: true,
                number_of_individuals:true,
                extra_guest_price:true,
                booking_floor_id:true,
            },
            is_disabled: false,
            filterSetting: [
                "name",
                "name_e",
                "code",
                "price",
                "description",
                "description_e",
                this.$i18n.locale == 'ar' ? 'unitStatus.name': 'unitStatus.name_e',
                "number_of_individuals",
                "extra_guest_price",
                "booking_floor_id",
            ],
        };
    },
    validations: {
        create: {
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            name_e: { required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            code: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            price: { required: requiredIf(function (model) {
                    return this.isRequired("price");
                }),
            },
            description: { required: requiredIf(function (model) {
                    return this.isRequired("description");
                }), maxLength: maxLength(1000),
            },
            description_e: { required: requiredIf(function (model) {
                    return this.isRequired("description_e");
                }), maxLength: maxLength(1000),
            },
            unit_status_id: { required: requiredIf(function (model) {
                    return this.isRequired("unit_status_id");
                }),
            },
            number_of_individuals: { required: requiredIf(function (model) {
                    return this.isRequired("number_of_individuals");
                }), maxLength: maxLength(1000),
            },
            extra_guest_price: { required: requiredIf(function (model) {
                    return this.isRequired("extra_guest_price");
                }), maxLength: maxLength(1000),
            },
            booking_floor_id: { required: requiredIf(function (model) {
                    return this.isRequired("extra_guest_price");
                }), maxLength: maxLength(1000),
            },

        },
        edit: {
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            name_e: { required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            code: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            price: { required: requiredIf(function (model) {
                    return this.isRequired("price");
                }),
            },
            description: { required: requiredIf(function (model) {
                    return this.isRequired("description");
                }), maxLength: maxLength(1000),
            },
            description_e: { required: requiredIf(function (model) {
                    return this.isRequired("description_e");
                }), maxLength: maxLength(1000),
            },
            unit_status_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("unit_status_id");
                }),
            },
            number_of_individuals: { required: requiredIf(function (model) {
                    return this.isRequired("number_of_individuals");
                }), maxLength: maxLength(1000),
            },
            extra_guest_price: { required: requiredIf(function (model) {
                    return this.isRequired("extra_guest_price");
                }), maxLength: maxLength(1000),
            },
            booking_floor_id: { required: requiredIf(function (model) {
                    return this.isRequired("extra_guest_price");
                }), maxLength: maxLength(1000),
            },
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
                this.categories.forEach((el) => {
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
        this.getCustomTableFields();
        this.getData();
    },
    methods: {
        isPermission(item) {
            if (this.$store.state.auth.type == 'user'){
                return this.$store.state.auth.permissions.includes(item)
            }
            return true;
        },
        getCustomTableFields() {
            adminApi
                .get(`/customTable/table-columns/booking_units`)
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
        /**
         *  start get Data countrie && pagination
         */
        getData(page = 1) {
            this.isLoader = true;
            let _filterSetting = [...this.filterSetting];
            let index = this.filterSetting.indexOf("booking_floor_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "floor.name" : "floor.name_e";
            }
            let filter = "";
            for (let i = 0; i < _filterSetting.length; ++i) {
                filter += `columns[${i}]=${_filterSetting[i]}&`;
            }
            adminApi
                .get(
                    `/booking/units?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.categories = l.data;
                    this.categoriesPagination = l.pagination;
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
                this.current_page <= this.categoriesPagination.last_page &&
                this.current_page != this.categoriesPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let _filterSetting = [...this.filterSetting];
                let index = this.filterSetting.indexOf("booking_floor_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "floor.name" : "floor.name_e";
                }
                let filter = "";
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }

                adminApi
                    .get(
                        `/booking/units?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.categories = l.data;
                        this.categoriesPagination = l.pagination;
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
        deletecategory(id, index) {
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
                            .post(`/booking/units/bulk-delete`, { ids: id })
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
                            .delete(`/booking/units/${id}`)
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
                description: "",
                description_e: "",
                unit_status_id: 12,
                number_of_individuals:1,
                extra_guest_price:0,
                booking_floor_id:null,
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
            if(this.isVisible('booking_floor_id')) await this.getFloors();
            if(this.isVisible('unit_status_id')) await this.getStatus();
            this.create = {
                name: "",
                name_e: "",
                code: "",
                price: "",
                description: "",
                description_e: "",
                unit_status_id: 12,
                number_of_individuals:1,
                extra_guest_price:0,
                booking_floor_id:null,
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
                code: "",
                price: "",
                description: "",
                description_e: "",
                unit_status_id: 12,
                number_of_individuals:1,
                extra_guest_price:0,
                booking_floor_id:null,
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
        },

        AddSubmit() {
            if (!this.create.name) {
                this.create.name = this.create.name_e;
            }
            if (!this.create.name_e) {
                this.create.name_e = this.create.name;
            }
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
                    .post(`/booking/units`, {...this.create,company_id:this.company_id})
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
            if (!this.edit.name) {
                this.edit.name = this.edit.name_e;
            }
            if (!this.edit.name_e) {
                this.edit.name_e = this.edit.name;
            }
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
                    .put(`/booking/units/${id}`, {...this.edit,company_id:this.company_id})
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
            if(this.isVisible('booking_floor_id')) await this.getFloors();
            if(this.isVisible('unit_status_id')) await this.getStatus();
            let category = this.categories.find((e) => id == e.id);
            this.edit.name = category.name;
            this.edit.name_e = category.name_e;
            this.edit.description = category.description;
            this.edit.description_e = category.description_e;
            this.edit.code = category.code;
            this.edit.price = category.price;
            this.edit.unit_status_id =  category.unit_status_id;
            this.edit.number_of_individuals = category.number_of_individuals;
            this.edit.extra_guest_price = category.extra_guest_price;
            this.edit.booking_floor_id = category.booking_floor_id;
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
                code: "",
                price: "",
                description: "",
                description_e: "",
                unit_status_id: null,
                number_of_individuals:1,
                extra_guest_price:0,
                booking_floor_id:null,
            };
        },
        async getFloors() {
            this.isLoader = true;
            await adminApi
                .get(`/booking/floors`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.floors = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
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
        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if (this.mouseEnter != id) {
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`/booking/units/logs/${id}`)
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
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
                if (dl) {
                    XLSX.write(wb, {bookType: type, bookSST: true, type: 'base64'});
                } else {
                    XLSX.writeFile(wb, fn || (('rooms' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },
        arabicValueName(txt){
            this.create.name = arabicValue(txt);
            this.edit.name = arabicValue(txt);
        },
        englishValueName(txt){
            this.create.name_e = englishValue(txt);
            this.edit.name_e = englishValue(txt);
        },
        arabicValueDescription(txt) {
            this.create.description = arabicValue(txt);
            this.edit.description = arabicValue(txt);
        },
        englishValueDescription(txt) {
            this.create.description_e = englishValue(txt);
            this.edit.description_e = englishValue(txt);
        },
        getStatus(){
            this.isLoader = true;
            adminApi
                .get(`/statuses/get-drop-down?module_type=booking`)
                .then((res) => {
                    let l = res.data.data;
                    this.statuses = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ $t("general.roomTable") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown
                                        variant="primary"
                                        :text="$t('general.searchSetting')"
                                        ref="dropdown"
                                        class="btn-block setting-search"
                                    >
                                        <b-form-checkbox v-if="isVisible('code')" v-model="filterSetting" value="code" class="mb-1">{{getCompanyKey('room_code')}}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('price')" v-model="filterSetting" value="price" class="mb-1">{{getCompanyKey('room_price')}}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('name')" v-model="filterSetting" value="name" class="mb-1">{{getCompanyKey('room_name_ar')}}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('name_e')" v-model="filterSetting" value="name_e" class="mb-1">{{getCompanyKey('room_name_en')}}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('number_of_individuals')" v-model="filterSetting" value="number_of_individuals" class="mb-1">{{getCompanyKey('room_number_of_individuals')}}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('extra_guest_price')" v-model="filterSetting" value="extra_guest_price" class="mb-1">{{getCompanyKey('room_extra_guest_price')}}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('booking_floor_id')" v-model="filterSetting" value="booking_floor_id" class="mb-1">{{getCompanyKey('room_floor')}}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('description')" v-model="filterSetting" value="description" class="mb-1">{{getCompanyKey('room_description_ar')}}</b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('description_e')" v-model="filterSetting" value="description_e" class="mb-1">{{getCompanyKey('room_description_en')}}</b-form-checkbox>
                                        <b-form-checkbox
                                            v-if="isVisible('unit_status_id')"
                                            v-model="filterSetting" :value="$i18n.locale == 'ar' ? 'unitStatus.name': 'unitStatus.name_e'"
                                            class="mb-1"
                                        >
                                            {{getCompanyKey('room_unit_status')}}
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
                                    v-if="isPermission('create Category')"
                                    v-b-modal.create
                                    variant="primary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button class="custom-btn-dowonload" @click="ExportExcel('xlsx')">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button class="custom-btn-dowonload" v-print="'#printCustom'">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <button
                                        class="custom-btn-dowonload"
                                        @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                                        v-if="checkAll.length == 1 && isPermission('update Category')"
                                    >
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="checkAll.length > 1  && isPermission('delete Category')"
                                        @click.prevent="deletecategory(checkAll)"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="checkAll.length == 1  && isPermission('delete Category')"
                                        @click.prevent="deletecategory(checkAll[0])"
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
                                            <b-form-checkbox v-if="isVisible('code')" v-model="setting.code" class="mb-1">{{getCompanyKey('room_code')}}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('price')" v-model="setting.price" class="mb-1">{{getCompanyKey('room_price')}}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('name')" v-model="setting.name" class="mb-1">{{getCompanyKey('room_name_ar')}}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('name_e')" v-model="setting.name_e" class="mb-1">{{getCompanyKey('room_name_en')}}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('number_of_individuals')" v-model="setting.number_of_individuals" class="mb-1">{{getCompanyKey('room_number_of_individuals')}}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('extra_guest_price')" v-model="setting.extra_guest_price" class="mb-1">{{getCompanyKey('room_extra_guest_price')}}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('booking_floor_id')" v-model="setting.booking_floor_id" class="mb-1">{{getCompanyKey('room_floor')}}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('description')" v-model="setting.description" class="mb-1">{{getCompanyKey('room_description_ar')}}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('description_e')" v-model="setting.description_e" class="mb-1">{{getCompanyKey('room_description_en')}}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('unit_status_id')" v-model="setting.unit_status_id" class="mb-1">{{getCompanyKey('room_unit_status')}}</b-form-checkbox>
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
                                            {{ categoriesPagination.from }}-{{ categoriesPagination.to }} /
                                            {{ categoriesPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{'pointer-events':categoriesPagination.current_page == 1 ? 'none' : '',}"
                                                @click.prevent="getData(categoriesPagination.current_page - 1)"
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
                                                :style="{'pointer-events':categoriesPagination.last_page == categoriesPagination.current_page? 'none': '',}"
                                                @click.prevent="getData(categoriesPagination.current_page + 1)"
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
                            :title="getCompanyKey('room_create_form')"
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
                                    <div v-if="isVisible('booking_floor_id')" class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey("room_floor") }}
                                                <span v-if="isRequired('booking_floor_id')" class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                v-model="create.booking_floor_id"
                                                :options="floors.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar'
                                                    ? floors.find((x) => x.id == opt).name
                                                    : floors.find((x) => x.id == opt).name_e
                                                "
                                            >
                                            </multiselect>
                                            <div
                                                v-if="$v.create.booking_floor_id.$error || errors.booking_floor_id"
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.booking_floor_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.booking_floor_id"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div v-if="isVisible('unit_status_id')" class="col-md-6">
                                        <div class="form-group">
                                            <label>
                                                {{ getCompanyKey('room_unit_status') }}
                                                <span v-if="isRequired('unit_status_id')" class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                v-model="create.unit_status_id"
                                                :options="statuses.map((type) => type.id)"
                                                :custom-label=" (opt) => statuses.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                                :class="{'is-invalid': $v.create.unit_status_id.$error || errors.unit_status_id,'is-valid': !$v.create.unit_status_id.$invalid && !errors.unit_status_id,}"
                                            >
                                            </multiselect>
                                            <div v-if="!$v.create.unit_status_id.required" class="invalid-feedback">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>

                                            <template v-if="errors.unit_status_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.unit_status_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div v-if="isVisible('code')" class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-4353" class="control-label">
                                                {{ getCompanyKey("room_code") }}
                                                <span v-if="isRequired('code')" class="text-danger">*</span>
                                            </label>
                                            <div dir="ltr">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    v-model="$v.create.code.$model"
                                                    :class="{
                                                        'is-invalid':
                                                          $v.create.code.$error || errors.code,
                                                        'is-valid':
                                                          !$v.create.code.$invalid && !errors.code,
                                                      }"
                                                    id="field-4353"
                                                />
                                            </div>
                                            <div v-if="!$v.create.code.maxLength" class="invalid-feedback">
                                                {{ $t("general.Itmustbeatmost") }}
                                                {{ $v.create.code.$params.maxLength.max }}
                                                {{ $t("general.letters") }}
                                            </div>
                                            <template v-if="errors.code">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.code" :key="index">{{ errorMessage }}</ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div v-if="isVisible('price')" class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("room_price") }}
                                                <span v-if="isRequired('price')" class="text-danger">*</span>
                                            </label>
                                            <div dir="ltr">
                                                <input
                                                    type="number"
                                                    step="any"
                                                    class="form-control"
                                                    v-model="$v.create.price.$model"
                                                    :class="{
                                                        'is-invalid':
                                                          $v.create.price.$error || errors.price,
                                                        'is-valid':
                                                          !$v.create.price.$invalid && !errors.price,
                                                      }"
                                                />
                                            </div>
                                            <template v-if="errors.price">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.price" :key="index">{{ errorMessage }}</ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6" v-if="isVisible('name')">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">
                                                {{ getCompanyKey('room_name_ar') }}
                                                <span v-if="isRequired('name')" class="text-danger">*</span>
                                            </label>
                                            <div dir="rtl">
                                                <input
                                                    type="text"
                                                    class="form-control arabicInput"
                                                    data-create="1"
                                                    @keyup="arabicValueName(create.name)"
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
                                    <div class="col-md-6" v-if="isVisible('name_e')">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">
                                                {{ getCompanyKey('room_name_en') }}
                                                <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                            </label>
                                            <div dir="ltr">
                                                <input
                                                    type="text"
                                                    class="form-control englishInput"
                                                    data-create="2"
                                                    @keyup="englishValueName(create.name_e)"
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
                                    <div v-if="isVisible('number_of_individuals')" class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("room_number_of_individuals") }}
                                                <span v-if="isRequired('number_of_individuals')" class="text-danger">*</span>
                                            </label>
                                            <div dir="ltr">
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    v-model="$v.create.number_of_individuals.$model"
                                                    :class="{
                                                        'is-invalid':
                                                          $v.create.number_of_individuals.$error || errors.number_of_individuals,
                                                        'is-valid':
                                                          !$v.create.number_of_individuals.$invalid && !errors.number_of_individuals,
                                                      }"
                                                />
                                            </div>
                                            <template v-if="errors.number_of_individuals">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.number_of_individuals" :key="index">{{ errorMessage }}</ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div v-if="isVisible('extra_guest_price')" class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("room_extra_guest_price") }}
                                                <span v-if="isRequired('extra_guest_price')" class="text-danger">*</span>
                                            </label>
                                            <div dir="ltr">
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    v-model="$v.create.extra_guest_price.$model"
                                                    :class="{
                                                        'is-invalid':
                                                          $v.create.extra_guest_price.$error || errors.extra_guest_price,
                                                        'is-valid':
                                                          !$v.create.extra_guest_price.$invalid && !errors.extra_guest_price,
                                                      }"
                                                />
                                            </div>
                                            <template v-if="errors.extra_guest_price">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.extra_guest_price" :key="index">{{ errorMessage }}</ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6" v-if="isVisible('description')">
                                        <div class="form-group">
                                            <label class="mr-2">
                                                {{getCompanyKey("room_description_ar")}}
                                                <span v-if="isVisible('description')" class="text-danger">*</span>
                                            </label>
                                            <textarea
                                                @input="arabicValueDescription(create.description)"
                                                v-model="$v.create.description.$model"
                                                class="form-control"
                                                :maxlength="1000"
                                                rows="5"
                                            ></textarea>
                                            <template v-if="errors.description">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.description" :key="index">{{ errorMessage }}</ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6" v-if="isVisible('description_e')">
                                        <div class="form-group">
                                            <label class="mr-2">
                                                {{ getCompanyKey("room_description_en")}}
                                                <span v-if="isRequired('description_e')" class="text-danger">*</span>
                                            </label>
                                            <textarea
                                                @input="englishValueDescription(create.description_e)"
                                                v-model="$v.create.description_e.$model"
                                                class="form-control"
                                                :maxlength="1000"
                                                rows="5"
                                            ></textarea>
                                            <template v-if="errors.description_e">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.description_e" :key="index">{{ errorMessage }}</ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table"
                             id="printCustom">
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0">
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
                                    <th v-if="setting.code && isVisible('code')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('room_code') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="categories.sort(sortString('code'))"></i>
                                                <i class="fas fa-arrow-down" @click="categories.sort(sortString('-code'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.price && isVisible('price')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('room_price') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="categories.sort(sortString('price'))"></i>
                                                <i class="fas fa-arrow-down" @click="categories.sort(sortString('-price'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.name && isVisible('name')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('room_name_ar') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="categories.sort(sortString('name'))"></i>
                                                <i class="fas fa-arrow-down" @click="categories.sort(sortString('-name'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.name_e && isVisible('name_e')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('room_name_en') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="categories.sort(sortString('name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="categories.sort(sortString('-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.unit_status_id && isVisible('unit_status_id')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('room_unit_status') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="categories.sort(sortString(($i18n.locale = 'ar' ? 'unit_status.name' : 'unit_status.name_e')))"></i>
                                                <i class="fas fa-arrow-down" @click="categories.sort(sortString(($i18n.locale = 'ar' ? '-unit_status.name' : '-unit_status.name_e')))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.number_of_individuals && isVisible('number_of_individuals')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('room_number_of_individuals') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="categories.sort(sortString('number_of_individuals'))"></i>
                                                <i class="fas fa-arrow-down" @click="categories.sort(sortString('-number_of_individuals'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.extra_guest_price && isVisible('extra_guest_price')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('room_extra_guest_price') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="categories.sort(sortString('extra_guest_price'))"></i>
                                                <i class="fas fa-arrow-down" @click="categories.sort(sortString('-extra_guest_price'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.booking_floor_id && isVisible('booking_floor_id')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('room_floor') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="categories.sort(sortString(($i18n.locale = 'ar' ? 'floor.name' : 'floor.name_e')))"></i>
                                                <i class="fas fa-arrow-down" @click="categories.sort(sortString(($i18n.locale = 'ar' ? '-floor.name' : '-floor.name_e')))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.description && isVisible('description')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('room_description_ar') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="categories.sort(sortString('description'))"></i>
                                                <i class="fas fa-arrow-down" @click="categories.sort(sortString('-description'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.description_e && isVisible('description_e')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('room_description_en') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="categories.sort(sortString('description_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="categories.sort(sortString('-description_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t("general.Action") }}
                                    </th>
                                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                </tr>
                                </thead>
                                <tbody v-if="categories.length > 0">
                                <tr
                                    @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="isPermission('update Category')?$bvModal.show(`modal-edit-${data.id}`):false"
                                    v-for="(data, index) in categories"
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
                                    <td v-if="setting.code  && isVisible('code')">
                                        <h5 class="m-0 font-weight-normal">{{ data.code }}</h5>
                                    </td>
                                    <td v-if="setting.price  && isVisible('price')">
                                        <h5 class="m-0 font-weight-normal">{{ data.price }}</h5>
                                    </td>
                                    <td v-if="setting.name && isVisible('name')">
                                        <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                                    </td>
                                    <td v-if="setting.name_e  && isVisible('name_e')">
                                        <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
                                    </td>
                                    <td v-if="setting.unit_status_id  && isVisible('unit_status_id')">
                                        <h5 class="m-0 font-weight-normal" v-if="data.unit_status">
                                            {{ $i18n.locale == 'ar' ? data.unit_status.name : data.unit_status.name_e  }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.number_of_individuals  && isVisible('number_of_individuals')">
                                        <h5 class="m-0 font-weight-normal">{{ data.number_of_individuals }}</h5>
                                    </td>
                                    <td v-if="setting.extra_guest_price  && isVisible('extra_guest_price')">
                                        <h5 class="m-0 font-weight-normal">{{ data.extra_guest_price }}</h5>
                                    </td>
                                    <td v-if="setting.booking_floor_id  && isVisible('booking_floor_id')">
                                        <h5 class="m-0 font-weight-normal">
                                            {{ data.floor ? $i18n.locale == 'ar' ? data.floor.name : data.floor.name : '---' }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.description  && isVisible('description')">
                                        <h5 class="m-0 font-weight-normal">{{ data.description }}</h5>
                                    </td>
                                    <td v-if="setting.description_e  && isVisible('description_e')">
                                        <h5 class="m-0 font-weight-normal">{{ data.description_e }}</h5>
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
                                                    v-if="isPermission('update Category')"
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
                                                    v-if="isPermission('delete Category')"
                                                    class="dropdown-item text-black"
                                                    href="#"
                                                    @click.prevent="deletecategory(data.id)"
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
                                            :title="getCompanyKey('room_edit_form')"
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
                                                    <div v-if="isVisible('booking_floor_id')" class="col-md-6">
                                                        <div class="form-group position-relative">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("room_floor") }}
                                                                <span v-if="isRequired('booking_floor_id')" class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                v-model="edit.booking_floor_id"
                                                                :options="floors.map((type) => type.id)"
                                                                :custom-label="(opt) => $i18n.locale == 'ar'
                                                                            ? floors.find((x) => x.id == opt).name
                                                                            : floors.find((x) => x.id == opt).name_e
                                                                        "
                                                            >
                                                            </multiselect>
                                                            <div
                                                                v-if="$v.edit.booking_floor_id.$error || errors.booking_floor_id"
                                                                class="text-danger"
                                                            >
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.booking_floor_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.booking_floor_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div v-if="isVisible('unit_status_id')" class="col-md-6">
                                                        <div class="form-group">
                                                            <label>
                                                                {{ getCompanyKey('room_unit_status') }}
                                                                <span v-if="isRequired('unit_status_id')" class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                v-model="edit.unit_status_id"
                                                                :options="statuses.map((type) => type.id)"
                                                                :custom-label=" (opt) => statuses.find((x) => x.id == opt)?
                                                                    $i18n.locale == 'ar' ? statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e
                                                                    : null
                                                                "
                                                                :class="{'is-invalid': $v.edit.unit_status_id.$error || errors.unit_status_id,'is-valid': !$v.edit.unit_status_id.$invalid && !errors.unit_status_id,}"
                                                            >
                                                            </multiselect>
                                                            <div v-if="!$v.edit.unit_status_id.required" class="invalid-feedback">
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>

                                                            <template v-if="errors.unit_status_id">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.unit_status_id"
                                                                              :key="index">{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div v-if="isVisible('code')" class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="field-435443" class="control-label">
                                                                {{ getCompanyKey("room_code") }}
                                                                <span v-if="isRequired('code')" class="text-danger">*</span>
                                                            </label>
                                                            <div dir="ltr">
                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    v-model="$v.edit.code.$model"
                                                                    :class="{
                                                                        'is-invalid':
                                                                          $v.edit.code.$error || errors.code,
                                                                        'is-valid':
                                                                          !$v.edit.code.$invalid && !errors.code,
                                                                      }"
                                                                    id="field-435443"
                                                                />
                                                            </div>
                                                            <div v-if="!$v.edit.code.maxLength" class="invalid-feedback">
                                                                {{ $t("general.Itmustbeatmost") }}
                                                                {{ $v.edit.code.$params.maxLength.max }}
                                                                {{ $t("general.letters") }}
                                                            </div>
                                                            <template v-if="errors.code">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.code" :key="index">{{ errorMessage }}</ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div v-if="isVisible('price')" class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("room_price") }}
                                                                <span v-if="isRequired('price')" class="text-danger">*</span>
                                                            </label>
                                                            <div dir="ltr">
                                                                <input
                                                                    type="number"
                                                                    step="any"
                                                                    class="form-control"
                                                                    v-model="$v.edit.price.$model"
                                                                    :class="{
                                                                        'is-invalid':
                                                                          $v.edit.price.$error || errors.price,
                                                                        'is-valid':
                                                                          !$v.edit.price.$invalid && !errors.price,
                                                                      }"
                                                                />
                                                            </div>
                                                            <template v-if="errors.price">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.price" :key="index">{{ errorMessage }}</ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" v-if="isVisible('name')">
                                                        <div class="form-group">
                                                            <label for="edit-1" class="control-label">
                                                                {{ getCompanyKey('room_name_ar') }}
                                                                <span v-if="isRequired('name')" class="text-danger">*</span>
                                                            </label>
                                                            <div dir="rtl">
                                                                <input
                                                                    type="text"
                                                                    class="form-control arabicInput"
                                                                    data-edit="1"
                                                                    @keyup="arabicValueName(edit.name)"
                                                                    v-model="$v.edit.name.$model"
                                                                    :class="{
                                                                          'is-invalid': $v.edit.name.$error || errors.name,
                                                                          'is-valid': !$v.edit.name.$invalid && !errors.name,
                                                                        }"
                                                                    id="edit-1"
                                                                />
                                                            </div>
                                                            <div
                                                                v-if="!$v.edit.name.minLength"
                                                                class="invalid-feedback"
                                                            >
                                                                {{ $t("general.Itmustbeatleast") }}
                                                                {{ $v.edit.name.$params.minLength.min }}
                                                                {{ $t("general.letters") }}
                                                            </div>
                                                            <div
                                                                v-if="!$v.edit.name.maxLength"
                                                                class="invalid-feedback"
                                                            >
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
                                                    <div class="col-md-6" dir="ltr" v-if="isVisible('name_e')">
                                                        <div class="form-group">
                                                            <label for="edit-2" class="control-label">
                                                                {{ getCompanyKey('room_name_en') }}
                                                                <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                                            </label>
                                                            <div>
                                                                <input
                                                                    type="text"
                                                                    class="form-control englishInput"
                                                                    data-edit="2"
                                                                    @keyup="englishValueName(edit.name_e)"
                                                                    v-model="$v.edit.name_e.$model"
                                                                    :class="{
                                                                          'is-invalid':
                                                                            $v.edit.name_e.$error || errors.name_e,
                                                                          'is-valid':
                                                                            !$v.edit.name_e.$invalid && !errors.name_e,
                                                                        }"
                                                                    id="edit-2"
                                                                />
                                                            </div>
                                                            <div
                                                                v-if="!$v.edit.name_e.minLength"
                                                                class="invalid-feedback"
                                                            >
                                                                {{ $t("general.Itmustbeatleast") }}
                                                                {{ $v.edit.name_e.$params.minLength.min }}
                                                                {{ $t("general.letters") }}
                                                            </div>
                                                            <div
                                                                v-if="!$v.edit.name_e.maxLength"
                                                                class="invalid-feedback"
                                                            >
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
                                                    <div v-if="isVisible('number_of_individuals')" class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("room_number_of_individuals") }}
                                                                <span v-if="isRequired('number_of_individuals')" class="text-danger">*</span>
                                                            </label>
                                                            <div dir="ltr">
                                                                <input
                                                                    type="number"
                                                                    class="form-control"
                                                                    v-model="$v.edit.number_of_individuals.$model"
                                                                    :class="{
                                                                        'is-invalid':
                                                                          $v.edit.number_of_individuals.$error || errors.number_of_individuals,
                                                                        'is-valid':
                                                                          !$v.edit.number_of_individuals.$invalid && !errors.number_of_individuals,
                                                                      }"
                                                                />
                                                            </div>
                                                            <template v-if="errors.number_of_individuals">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.number_of_individuals" :key="index">{{ errorMessage }}</ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div v-if="isVisible('extra_guest_price')" class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("room_extra_guest_price") }}
                                                                <span v-if="isRequired('extra_guest_price')" class="text-danger">*</span>
                                                            </label>
                                                            <div dir="ltr">
                                                                <input
                                                                    type="number"
                                                                    class="form-control"
                                                                    v-model="$v.edit.extra_guest_price.$model"
                                                                    :class="{
                                                                        'is-invalid':
                                                                          $v.edit.extra_guest_price.$error || errors.extra_guest_price,
                                                                        'is-valid':
                                                                          !$v.edit.extra_guest_price.$invalid && !errors.extra_guest_price,
                                                                      }"
                                                                />
                                                            </div>
                                                            <template v-if="errors.extra_guest_price">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.extra_guest_price" :key="index">{{ errorMessage }}</ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" v-if="isVisible('description')">
                                                        <div class="form-group">
                                                            <label class="mr-2">
                                                                {{getCompanyKey("room_description_ar")}}
                                                                <span v-if="isVisible('description')" class="text-danger">*</span>
                                                            </label>
                                                            <textarea
                                                                @input="arabicValueDescription(edit.description)"
                                                                v-model="$v.edit.description.$model"
                                                                class="form-control"
                                                                :maxlength="1000"
                                                                rows="5"
                                                            ></textarea>
                                                            <template v-if="errors.description">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.description" :key="index">{{ errorMessage }}</ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" v-if="isVisible('description_e')">
                                                        <div class="form-group">
                                                            <label class="mr-2">
                                                                {{ getCompanyKey("room_description_en")}}
                                                                <span v-if="isRequired('description_e')" class="text-danger">*</span>
                                                            </label>
                                                            <textarea
                                                                @input="englishValueDescription(edit.description_e)"
                                                                v-model="$v.edit.description_e.$model"
                                                                class="form-control"
                                                                :maxlength="1000"
                                                                rows="5"
                                                            ></textarea>
                                                            <template v-if="errors.description_e">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.description_e" :key="index">{{ errorMessage }}</ErrorMessage>
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
                                    <th class="text-center" colspan="13">
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
