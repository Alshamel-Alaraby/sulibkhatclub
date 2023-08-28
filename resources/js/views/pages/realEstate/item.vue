<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import ErrorMessage from "../../../components/widgets/errorMessage";
import adminApi from "../../../api/adminAxios";
import translation from "../../../helper/mixin/translation-mixin";
import ItemCategory from "../../../components/create/realEstate/itemCategory.vue"
import Unit from "../../../components/create/general/unit.vue"
import permissionGuard from "../../../helper/permission";

import {
    required,
    requiredIf,
    minLength,
    maxLength,
    integer,
    email,
    sameAs,
    url,
    minValue,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import loader from "../../../components/general/loader";
import { dynamicSortNumber, dynamicSortString } from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";

/**
 * Advanced Table component
 */

// start validation image/png

export default {
    page: {
        title: "Item",
        meta: [{ name: "description", content: "Item" }],
    },
    mixins: [translation],
    components: {
        Unit,
        ItemCategory,
        Layout,
        PageHeader,
        loader,
        ErrorMessage,
        Multiselect,
    },
//     beforeRouteEnter(to, from, next) {
//             next((vm) => {
//       return permissionGuard(vm, "Items RealState", "all items RealState");
//     });

//    },
    data() {
        return {
            fields: [],
            per_page: 50,
            search: "",
            debounce: {},
            itemsPagination: {},
            items: [],
            units: [],
            enabled3: true,
            isLoader: false,
            Tooltip: "",
            mouseEnter: "",
            categories: [],
            itemCategories: [],
            category_item_id: null,
            create: {
                code_number: "",
                name: "",
                name_e: "",
                unit_id: null,
                type: "item",
                price: 0,
                media: []
            },
            edit: {
                name: "",
                name_e: "",
                unit_id: null,
                type: "item",
                price: 0,
                old_media: [],
            },
            errors: {},
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            images: [],
            media: {},
            item_id: null,
            isVaildPhone: false,
            saveImageName: [],
            showPhoto: "./images/img-1.png",
            setting: {
                name: true,
                name_e: true,
                code_number: true,
                unit_id: true,
                type: true,
                price: true,
            },
            idDelete: null,
            filterSetting: [
                "name",
                "name_e",
            ],
            printLoading: true,
            printObj: {
                id: "printMe",
            }
        };
    },
    validations: {
        create: {
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(3), maxLength: maxLength(100) },
            name_e: { required: requiredIf(function (model) {
                return this.isRequired("name_e");
            }), minLength: minLength(3), maxLength: maxLength(100) },
            code_number: { required:requiredIf(function (model) {
                return this.isRequired("code_number");
            }) },
            price: { required:requiredIf(function (model) {
                return this.isRequired("price");
            }), minValue: minValue(0) },
            unit_id: { required:requiredIf(function (model) {
                return this.isRequired("unit_id");
            }), minValue: minValue(0) },
            type: { required:requiredIf(function (model) {
                return this.isRequired("type");
            }) },
            media: {}
        },
        edit: {
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(3), maxLength: maxLength(100) },
            name_e: { required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(3), maxLength: maxLength(100) },
            code_number: { required:requiredIf(function (model) {
                    return this.isRequired("code_number");
                }) },
            price: { required:requiredIf(function (model) {
                    return this.isRequired("price");
                }), minValue: minValue(0) },
            unit_id: { required:requiredIf(function (model) {
                    return this.isRequired("unit_id");
                }), minValue: minValue(0) },
            type: { required:requiredIf(function (model) {
                    return this.isRequired("type");
                }) },
            media: {}
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
                this.items.forEach((el) => {
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
        this.getCustomTableFields();
        this.getData();
    },
    methods: {
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
        getCustomTableFields() {
            adminApi
                .get(`/customTable/table-columns/rlst_items`)
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
        isPermission(item) {
            if (this.$store.state.auth.type == 'user'){
                return this.$store.state.auth.permissions.includes(item)
            }
            return true;
        },
        showUnitModal() {
            if (this.create.unit_id == 0) {
                this.$bvModal.show("create-unit");
                this.create.unit_id = null;
            }
        },
        showUnitModalEdit() {
            if (this.edit.unit_id == 0) {
                this.$bvModal.show("create-unit");
                this.edit.unit_id = null;
            }
        },
        addCategoryToItem(id) {
            if (id == 0) {
                this.$bvModal.show("category-create");
                setTimeout(() => {
                    this.category_item_id = null;
                }, 100);
                return;
            }
            this.isLoader = true;
            adminApi
                .post(`real-estate/Categories-item`, {
                    item_id: this.item_id,
                    category_item_id: id,
                })
                .then((res) => {
                    this.isLoader = false;
                    this.getItemCategories();
                    this.category_item_id = null;

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
                    this.category_item_id = null;

                    if (err.response.status == 422) {
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.ExistBefore")}`,
                        });
                        this.isLoader = false;
                        return;
                    }
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        deleteItemCategory(id) {
            this.isLoader = true;
            adminApi
                .delete(
                    `real-estate/Categories-item/${id}/${this.item_id}`
                )
                .then((res) => {
                    this.isLoader = false;
                    this.getItemCategories();
                    setTimeout(() => {
                        Swal.fire({
                            icon: "success",
                            text: `${this.$t("general.DeletedSuccessfully")}`,
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    }, 500);
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getItemCategories() {

            await adminApi
                .get(`/real-estate/Category-item?item_id=${this.item_id}`)
                .then((res) => {
                    this.itemCategories = res.data.data;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getCategories() {
            await adminApi
                .get(`real-estate/Category-item`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create category RealState')){
                        l.unshift({ id: 0, name: "اضف فئة", name_e: "Add category" });
                    }
                    this.categories = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
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
                    .get(`/real-estate/item/logs/${id}`)
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
         *  get Data company
         */
        getData(page = 1) {
            this.isLoader = true;
            let filter = "";
            for (let i = 0; i < this.filterSetting.length; i++) {
                filter += `columns[${i}]=${this.filterSetting[i]}&`;
            }

            adminApi
                .get(
                    `/real-estate/item?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.items = l.data;
                    this.itemsPagination = l.pagination;
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
                this.current_page <= this.itemsPagination.last_page &&
                this.current_page != this.itemsPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let filter = "";
                for (let i = 0; i < this.filterSetting.length; i++) {
                    filter += `columns[${i}]=${this.filterSetting[i]}&`;
                }

                adminApi
                    .get(
                        `/real-estate/item?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.items = l.data;
                        this.itemsPagination = l.pagination;
                        this.current_page = l.pagination.current_page;
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
         *  delete company
         */
        deletecompany(id, index) {
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
                            .post(`/real-estate/item/bulk-delete`, { ids: id })
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
                            .delete(`/real-estate/item/${id}`)
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
                code_number: "",
                name: "",
                name_e: "",
                unit_id: null,
                type: "item",
                price: 0,
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.isDrop = false;
            this.images = [];
            this.dropDownSenders = [];
            this.$bvModal.hide(`create`);
            this.item_id = null;
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            if(this.isVisible('unit_id')) await this.getUnits();
            await this.getCategories();
            this.create = {
                code_number: "",
                name: "",
                name_e: "",
                unit_id: null,
                type: "item",
                price: 0,
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.item_id = null;
            this.showPhoto = "./images/img-1.png";
            this.media = {};
            this.images = [];
        },
        /**
         *  create company
         */
        resetForm() {
            this.create = {
                code_number: "",
                name: "",
                name_e: "",
                unit_id: null,
                type: "item",
                price: 0,
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.media = {};
            this.images = [];

            this.item_id = null;
        },
        AddSubmit() {
            if (!this.create.name) {
                this.create.name = this.create.name_e;
            }
            if (!this.create.name_e) {
                this.create.name_e = this.create.name;
            }

            this.$v.create.$touch();

            if (this.$v.create.$invalid && !this.isVaildPhone) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                if (!this.create.name) {
                    this.create.name = this.create.name_e;
                }
                if (!this.create.name_e) {
                    this.create.name_e = this.create.name;
                }

                adminApi
                    .post(`/real-estate/item`, {...this.create,company_id: this.$store.getters["auth/company_id"],})
                    .then((res) => {
                        this.item_id = res.data.data.id;
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
         *  edit company
         */
        editSubmit(id, index) {
            if (!this.edit.name) {
                this.edit.name = this.edit.name_e;
            }
            if (!this.edit.name_e) {
                this.edit.name_e = this.edit.name;
            }

            this.$v.edit.$touch();
            if (this.images)
                this.images.forEach((e) => {
                    this.edit.old_media.push(e.id);
                });
            if (this.$v.edit.$invalid && !this.isVaildPhone) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                adminApi
                    .put(`/real-estate/item/${id}`, this.edit)
                    .then((res) => {
                        this.getData();
                        this.$bvModal.hide(`modal-edit-${id}`);
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
         *  get parent
         */
        async getUnits() {
            await adminApi
                .get(`/units`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create Unit')){
                        l.unshift({ id: 0, name: "اضف وحدة", name_e: "Add unit" });
                    }
                    this.units = l;
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
         *   show Modal (edit)
         */
        async resetModalEdit(id) {
            let item = this.items.find((e) => id == e.id);
            if(this.isVisible('unit_id')) await this.getUnits();
            await this.getCategories();
            this.item_id = item.id;
            await this.getItemCategories();
            this.edit.name = item.name;
            this.edit.name_e = item.name_e;
            this.edit.unit_id = item.unit_id;
            this.edit.code_number = item.code_number;
            this.edit.price = item.price;
            this.edit.type = item.type;
            this.errors = {};
            this.images = item.media ?? [];
            if (this.images && this.images.length > 0) {
                this.showPhoto = this.images[this.images.length - 1].webp;
            } else {
                this.showPhoto = "./images/img-1.png";
            }
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.errors = {};

            this.edit = {
                code_number: "",
                name: "",
                name_e: "",
                unit_id: null,
                type: "item",
                price: 0,
                old_media: [],
            };
            this.errors = {};
            this.item_id = null;
            this.images = [];
        },
        sortString(value) {
            return dynamicSortString(value);
        },
        sortNumber(value) {
            return dynamicSortNumber(value);
        },
        checkRow(id) {
            if (!this.checkAll.includes(id)) {
                this.checkAll.push(id);
            } else {
                let index = this.checkAll.indexOf(id);
                this.checkAll.splice(index, 1);
            }
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
                    formDate.append("media[0]", this.media);
                    adminApi
                        .post(`/media`, formDate)
                        .then((res) => {
                            let old_media = [];
                            this.images.forEach((e) => old_media.push(e.id));
                            let new_media = [];
                            res.data.data.forEach((e) => new_media.push(e.id));

                            adminApi
                                .put(`/real-estate/item/${this.item_id}`, { old_media, media: new_media })
                                .then((res) => {
                                    this.images = res.data.data.media ?? [];
                                    if (this.images && this.images.length > 0) {
                                        this.showPhoto = this.images[this.images.length - 1].webp;
                                    } else {
                                        this.showPhoto = "./images/img-1.png";
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
                                        .put(`/real-estate/${this.item_id}`, { old_media, media: new_media })
                                        .then((res) => {
                                            this.images = res.data.data.media ?? [];
                                            if (this.images && this.images.length > 0) {
                                                this.showPhoto = this.images[this.images.length - 1].webp;
                                            } else {
                                                this.showPhoto = "./images/img-1.png";
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
                .put(`/real-estate/item/${this.item_id}`, { old_media })
                .then((res) => {
                    this.items[index] = res.data.data;
                    this.images = res.data.data.media ?? [];
                    if (this.images && this.images.length > 0) {
                        this.showPhoto = this.images[this.images.length - 1].webp;
                    } else {
                        this.showPhoto = "./images/img-1.png";
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

        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
                if (dl) {
                    XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' });
                } else {
                    XLSX.writeFile(wb, fn || (('Companies' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
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
        }
    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <Unit :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getUnits" />
        <ItemCategory :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCategories" @deleted="getCategories" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ $t("general.itemsTable") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                        class="btn-block setting-search dropdown-menu-custom-company">
                                        <b-form-checkbox v-if="isVisible('name')" v-model="filterSetting" value="name" class="mb-1">
                                            {{ getCompanyKey("item_name_ar") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('name_e')" v-model="filterSetting" value="name_e" class="mb-1">
                                            {{ getCompanyKey("item_name_en") }}
                                        </b-form-checkbox>
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

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <b-button v-b-modal.create v-if="isPermission('create items RealState')" variant="primary" class="btn-sm mx-1 font-weight-bold">
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printMe'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <button class="custom-btn-dowonload" @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                                        v-if="checkAll.length == 1 && isPermission('update items RealState')">
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length > 1 && isPermission('delete items RealState')"
                                        @click.prevent="deletecompany(checkAll)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length == 1 && isPermission('delete items RealState')"
                                        @click.prevent="deletecompany(checkAll[0])">
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
                                    <b-dropdown variant="primary"
                                        :html="`${$t('general.setting')} <i class='fe-settings'></i>`" ref="dropdown"
                                        class="dropdown-custom-ali dropdown-menu-custom-company">
                                        <b-form-checkbox v-if="isVisible('code_number')" v-model="setting.code_number" class="mb-1">
                                            {{ getCompanyKey("code_number") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('name')" v-model="setting.name" class="mb-1">{{
                                            getCompanyKey("item_name_ar") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('name_e')" v-model="setting.name_e" class="mb-1">
                                            {{ getCompanyKey("item_name_en") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('unit_id')" v-model="setting.unit_id" class="mb-1">
                                            {{ getCompanyKey("item_unit") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('type')" v-model="setting.type" class="mb-1">
                                            {{ getCompanyKey("item_type") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('price')" v-model="setting.price" class="mb-1">
                                            {{ getCompanyKey("item_price") }}
                                        </b-form-checkbox>
                                        <div class="d-flex justify-content-end">
                                            <a href="javascript:void(0)" class="btn btn-primary btn-sm">Apply</a>
                                        </div>
                                    </b-dropdown>
                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom">
                                        <div class="d-inline-block" style="font-size: 15px">
                                            {{ itemsPagination.from }}-{{ itemsPagination.to }} /
                                            {{ itemsPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a href="javascript:void(0)" :style="{
                                                'pointer-events':
                                                    itemsPagination.current_page == 1 ? 'none' : '',
                                            }" @click.prevent="getData(itemsPagination.current_page - 1)">
                                                <span>&lt;</span>
                                            </a>
                                            <input type="text" @keyup.enter="getDataCurrentPage()" v-model="current_page"
                                                class="pagination-current-page" />
                                            <a href="javascript:void(0)" :style="{
                                                'pointer-events':
                                                    itemsPagination.last_page ==
                                                        itemsPagination.current_page
                                                        ? 'none'
                                                        : '',
                                            }" @click.prevent="getData(itemsPagination.current_page + 1)">
                                                <span>&gt;</span>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>

                        <!--  create   -->
                        <b-modal id="create" :title="getCompanyKey('item_create_form')" title-class="font-18"
                            dialog-class="modal-full-width" body-class="paddingUnset" :hide-footer="true" @show="resetModal"
                            @hidden="resetModalHidden">

                            <form @submit.stop.prevent="AddSubmit">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mt-1 d-flex justify-content-end">
                                            <!-- Emulate built in modal footer ok and cancel button actions -->
                                            <b-button variant="success" :disabled="!item_id" @click.prevent="resetForm"
                                                type="button" :class="['font-weight-bold px-2', item_id ? 'mx-2' : '']">
                                                {{ $t("general.AddNewRecord") }}
                                            </b-button>

                                            <template v-if="!item_id">
                                                <b-button variant="success" type="button" class="mx-1 font-weight-bold px-3"
                                                    v-if="!isLoader" @click.prevent="AddSubmit">
                                                    {{ $t("general.Save") }}
                                                </b-button>

                                                <b-button variant="success" class="mx-1" disabled v-else>
                                                    <b-spinner small></b-spinner>
                                                    <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                </b-button>
                                            </template>

                                            <b-button variant="danger" class="font-weight-bold" type="button"
                                                @click.prevent="resetModalHidden">
                                                {{ $t("general.Cancel") }}
                                            </b-button>
                                        </div>
                                    </div>
                                    <b-tabs nav-class="nav-tabs nav-bordered">
                                        <b-tab :title="$t('general.DataEntry')" active>
                                            <div class="row">
                                                <div class="col-md-6" v-if="isVisible('code_number')">
                                                    <div class="form-group">
                                                        <label for="field-5" class="control-label">
                                                            {{ getCompanyKey("code_number") }}
                                                            <span v-if="isRequired('code_number')" class="text-danger">*</span>
                                                        </label>
                                                        <input  class="form-control"
                                                            v-model.number="$v.create.code_number.$model" :class="{
                                                                'is-invalid':
                                                                    $v.create.code_number.$error || errors.code_number,
                                                                'is-valid':
                                                                    !$v.create.code_number.$invalid && !errors.code_number,
                                                            }" id="field-5" />
                                                        <template v-if="errors.code_number">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.code_number"
                                                                :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 direction" dir="rtl" v-if="isVisible('name')">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">
                                                            {{ getCompanyKey("item_name_ar") }}
                                                            <span v-if="isRequired('name')" class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control arabicInput"
                                                            v-model.trim="$v.create.name.$model" :class="{
                                                                'is-invalid': $v.create.name.$error || errors.name,
                                                                'is-valid': !$v.create.name.$invalid && !errors.name,
                                                            }" @keyup="arabicValue(create.name)" id="field-1" />
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
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.name"
                                                                :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 direction-ltr" dir="ltr" v-if="isVisible('name_e')">
                                                    <div class="form-group">
                                                        <label for="field-2" class="control-label">
                                                            {{ getCompanyKey("item_name_en") }}
                                                            <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control englishInput"
                                                            v-model.trim="$v.create.name_e.$model" :class="{
                                                                'is-invalid':
                                                                    $v.create.name_e.$error || errors.name_e,
                                                                'is-valid':
                                                                    !$v.create.name_e.$invalid && !errors.name_e,
                                                            }" @keyup="englishValue(create.name_e)" id="field-2" />
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
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.name_e"
                                                                :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" v-if="isVisible('unit_id')">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("item_unit") }}
                                                            <span v-if="isRequired('unit_id')" class="text-danger">*</span>
                                                        </label>
                                                        <multiselect @input="showUnitModal" v-model="create.unit_id"
                                                            :options="units.map((type) => type.id)" :custom-label="
                                                                (opt) =>
                                                                    $i18n.locale
                                                                        ? units.find((x) => x.id == opt).name
                                                                        : units.find((x) => x.id == opt).name_e
                                                            ">
                                                        </multiselect>

                                                        <div v-if="!$v.create.unit_id.integer" class="invalid-feedback">
                                                            {{ $t("general.fieldIsInteger") }}
                                                        </div>
                                                        <template v-if="errors.unit_id">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.partner_id"
                                                                :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" v-if="isVisible('type')">
                                                    <div class="form-group">
                                                        <label for="field-5" class="control-label">
                                                            {{ getCompanyKey("item_type") }}
                                                            <span v-if="isRequired('type')" class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-control" v-model="$v.create.type.$model" :class="{
                                                            'is-invalid':
                                                                $v.create.type.$error || errors.type,
                                                            'is-valid':
                                                                !$v.create.type.$invalid && !errors.type,
                                                        }">
                                                            <option value="service">{{ $t("general.Service") }}</option>
                                                            <option value="item">{{ $t("general.Item") }}</option>
                                                            <option value="product">{{ $t("general.Product") }}</option>
                                                        </select>
                                                        <template v-if="errors.type">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.type"
                                                                :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" v-if="isVisible('price')">
                                                    <div class="form-group">
                                                        <label for="field-5" class="control-label">
                                                            {{ getCompanyKey("item_price") }}
                                                            <span v-if="isRequired('price')" class="text-danger">*</span>
                                                        </label>
                                                        <input type="number" class="form-control"
                                                            v-model.number="$v.create.price.$model" :class="{
                                                                'is-invalid':
                                                                    $v.create.price.$error || errors.price,
                                                                'is-valid':
                                                                    !$v.create.price.$invalid && !errors.price,
                                                            }" id="field-5" />
                                                        <template v-if="errors.price">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.price"
                                                                :key="index">{{ errorMessage }}</ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </b-tab>
                                        <b-tab :disabled="!item_id" :title="$t('general.imagEUpload')">
                                            <div class="row">
                                                <input accept="image/png, image/gif, image/jpeg, image/jpg" type="file"
                                                    id="uploadImageCreate" @change.prevent="onImageChanged"
                                                    class="input-file-upload position-absolute d-none" />
                                                <div class="col-md-8 my-1">
                                                    <!-- file upload -->
                                                    <div class="row align-content-between"
                                                        style="width: 100%; height: 100%">
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
                                                                                <div class="col-auto"
                                                                                    @click="showPhoto = photo.webp">
                                                                                    <img data-dz-thumbnail :src="photo.webp"
                                                                                        class="avatar-sm rounded bg-light"
                                                                                        @error="src = './images/img-1.png'" />
                                                                                </div>
                                                                                <div class="col pl-0">
                                                                                    <a href="javascript:void(0);" :class="[
                                                                                        'font-weight-bold',
                                                                                        images.length - 1 == index
                                                                                            ? 'text-white'
                                                                                            : 'text-muted',
                                                                                    ]" data-dz-name>
                                                                                        {{ photo.name }}
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
                                                            <b-button @click="changePhoto" variant="success" type="button"
                                                                class="mx-1 font-weight-bold px-3" v-if="!isLoader">
                                                                {{ $t("general.Add") }}
                                                            </b-button>
                                                            <b-button variant="success" class="mx-1" disabled v-else>
                                                                <b-spinner small></b-spinner>
                                                                <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                            </b-button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="show-dropzone">
                                                        <img :src="showPhoto" class="img-thumbnail"
                                                            @error="src = './images/img-1.png'" />
                                                    </div>
                                                </div>
                                            </div>
                                        </b-tab>
                                        <b-tab :disabled="!item_id" :title="$t('general.category')">
                                            <!--                                                <div class="col-md-12 text-center">-->
                                            <!--                                                    <h3>{{$t('general.DocumentName')}} : {{ $i18n.locale == "ar" ? edit.name : edit.name_e }}</h3>-->
                                            <!--                                                </div>-->
                                            <div class="col-md-6 mb-4 p-0 position-relative">
                                                <div class="form-group">
                                                    <label class="my-1 mr-2">{{
                                                        $t("general.category")
                                                    }}</label>
                                                    <multiselect  v-model="category_item_id" @select="addCategoryToItem"
                                                        :options="categories.map((type) => type.id)" :custom-label="
                                                            (opt) =>
                                                                $i18n.locale=='ar'
                                                                    ? categories.find((x) => x.id == opt).name
                                                                    : categories.find((x) => x.id == opt).name_e
                                                        ">
                                                    </multiselect>
                                                </div>
                                            </div>
                                            <!-- start .table-responsive-->
                                            <div class="table-responsive mb-3 custom-table-theme position-relative">
                                                <!--       start loader       -->
                                                <loader size="large" v-if="isLoader" />
                                                <!--       end loader       -->

                                                <table class="table table-borderless table-hover table-centered m-0">
                                                    <thead>
                                                        <tr>
                                                            <th>
                                                                <div class="d-flex justify-content-center">
                                                                    <span>{{ $t("general.Name") }}</span>
                                                                </div>
                                                            </th>
                                                            <th>
                                                                <div class="d-flex justify-content-center">
                                                                    <span>{{ $t("general.Name_en") }}</span>
                                                                </div>
                                                            </th>
                                                            <th>{{ $t("general.Action") }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody v-if="itemCategories.length > 0">
                                                        <tr v-for="(data, index) in itemCategories" :key="data.id"
                                                            class="body-tr-custom">
                                                            <td>
                                                                {{ data.name }}
                                                            </td>
                                                            <td>
                                                                {{ data.name_e }}
                                                            </td>

                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button"
                                                                        class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                                        data-toggle="dropdown" aria-expanded="false">
                                                                        {{ $t("general.commands") }}
                                                                        <i class="fas fa-angle-down"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu dropdown-menu-custom">
                                                                        <a class="dropdown-item text-black" href="#"
                                                                            @click.prevent="deleteItemCategory(data.id)">
                                                                            <div
                                                                                class="d-flex justify-content-between align-items-center text-black">
                                                                                <span>{{ $t("general.delete") }}</span>
                                                                                <i class="fas fa-times text-danger"></i>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tbody v-else>
                                                        <tr>
                                                            <th class="text-center" colspan="15">
                                                                {{ $t("general.notDataFound") }}
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- end .table-responsive-->
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
                                id="printMe">
                                <thead>
                                    <tr>
                                        <th class="do-not-print" scope="col" style="width: 0" v-if="enabled3">
                                            <div class="form-check custom-control">
                                                <input class="form-check-input" type="checkbox" v-model="isCheckAll"
                                                    style="width: 17px; height: 17px" />
                                            </div>
                                        </th>
                                        <th v-if="setting.code_number && isVisible('code_number')">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("code_number") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up"
                                                        @click="items.sort(sortString('code_number'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="items.sort(sortString('-code_number'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.name && isVisible('name')">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("item_name_ar") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up" @click="items.sort(sortString('name'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="items.sort(sortString('-name'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.name_e && isVisible('name_e')">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("item_name_en") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up"
                                                        @click="items.sort(sortString('name_e'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="items.sort(sortString('-name_e'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.unit_id && isVisible('unit_id')">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("item_unit") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up" @click="items.sort(sortNumber('name'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="items.sort(sortNumber('-name'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.type && isVisible('type')">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("item_type") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up" @click="items.sort(sortNumber('type'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="items.sort(sortNumber('-type'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.price && isVisible('price')">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("item_price") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up" @click="items.sort(sortNumber('price'))"></i>
                                                    <i class="fas fa-arrow-down"
                                                        @click="items.sort(sortNumber('-price'))"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="enabled3" class="do-not-print">
                                            {{ $t("general.Action") }}
                                        </th>
                                        <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                    </tr>
                                </thead>
                                <tbody v-if="items.length > 0">
                                    <tr @click.capture="checkRow(data.id)"
                                        @dblclick.prevent="isPermission('update items RealState')?$bvModal.show(`modal-edit-${data.id}`):false"
                                        v-for="(data, index) in items" :key="data.id" class="body-tr-custom">
                                        <td class="do-not-print" v-if="enabled3">
                                            <div class="form-check custom-control" style="min-height: 1.9em">
                                                <input style="width: 17px; height: 17px" class="form-check-input"
                                                    type="checkbox" :value="data.id" v-model="checkAll" />
                                            </div>
                                        </td>
                                        <td v-if="setting.code_number && isVisible('code_number')">
                                            {{ data.code_number }}
                                        </td>
                                        <td v-if="setting.name && isVisible('name')">
                                            {{ data.name }}
                                        </td>
                                        <td v-if="setting.name_e && isVisible('name_e')">
                                            {{ data.name_e }}
                                        </td>
                                        <td v-if="setting.unit_id && isVisible('unit_id')">
                                            {{ data.unit? $i18n.locale == "ar" ? data.unit.name : data.unit.name_e : ''}}
                                        </td>
                                        <td v-if="setting.type && isVisible('type')">
                                            {{ data.type }}
                                        </td>
                                        <td v-if="setting.price && isVisible('price')">
                                            {{ data.price }}
                                        </td>
                                        <td class="do-not-print" v-if="enabled3">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                    {{ $t("general.commands") }}
                                                    <i class="fas fa-angle-down"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-custom">
                                                    <a class="dropdown-item" href="#" v-if="isPermission('update items RealState')"
                                                        @click="$bvModal.show(`modal-edit-${data.id}`)">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center text-black">
                                                            <span>{{ $t("general.edit") }}</span>
                                                            <i class="mdi mdi-square-edit-outline text-info"></i>
                                                        </div>
                                                    </a>
                                                    <a class="dropdown-item text-black" href="#" v-if="isPermission('delete items RealState')"
                                                        @click.prevent="deletecompany(data.id)">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center text-black">
                                                            <span>{{ $t("general.delete") }}</span>
                                                            <i class="fas fa-times text-danger"></i>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>

                                            <!--  edit   -->
                                            <b-modal :id="`modal-edit-${data.id}`" :title="getCompanyKey('item_edit_form')"
                                                title-class="font-18" :ref="`edit-${data.id}`"
                                                dialog-class="modal-full-width" body-class="paddingUnset"
                                                :hide-footer="true" @show="resetModalEdit(data.id)"
                                                @hidden="resetModalHiddenEdit(data.id)">
                                                <form>
                                                    <div class="card-body">
                                                        <div class="mt-1 d-flex justify-content-end">
                                                            <!-- Emulate built in modal footer ok and cancel button actions -->

                                                            <b-button variant="success" type="button"
                                                                class="mx-1 font-weight-bold px-3" v-if="!isLoader"
                                                                @click.prevent="editSubmit(data.id, index)">
                                                                {{ $t("general.Save") }}
                                                            </b-button>

                                                            <b-button variant="success" class="mx-1" disabled v-else>
                                                                <b-spinner small></b-spinner>
                                                                <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                            </b-button>

                                                            <b-button variant="danger" class="font-weight-bold"
                                                                type="button"
                                                                @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)">
                                                                {{ $t("general.Cancel") }}
                                                            </b-button>
                                                        </div>
                                                    </div>
                                                    <b-tabs nav-class="nav-tabs nav-bordered">
                                                        <b-tab :title="$t('general.DataEntry')" active>
                                                            <div class="row">
                                                                <div class="col-md-6" v-if="isVisible('code_number')">
                                                                    <div class="form-group">
                                                                        <label for="field-5" class="control-label">
                                                                            {{ getCompanyKey("code_number") }}
                                                                            <span v-if="isRequired('code_number')" class="text-danger">*</span>
                                                                        </label>
                                                                        <input class="form-control"
                                                                            v-model.number="$v.edit.code_number.$model"
                                                                            :class="{
                                                                                'is-invalid':
                                                                                    $v.edit.code_number.$error || errors.code_number,
                                                                                'is-valid':
                                                                                    !$v.edit.code_number.$invalid && !errors.code_number,
                                                                            }" id="field-5" />
                                                                        <template v-if="errors.code_number">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.code_number"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 direction" v-if="isVisible('name')" dir="rtl">
                                                                    <div class="form-group">
                                                                        <label for="field-1" class="control-label">
                                                                            {{ getCompanyKey("item_name_ar") }}
                                                                            <span v-if="isRequired('name')" class="text-danger">*</span>
                                                                        </label>
                                                                        <input type="text" class="form-control arabicInput"
                                                                            v-model.trim="$v.edit.name.$model" :class="{
                                                                                'is-invalid': $v.edit.name.$error || errors.name,
                                                                                'is-valid': !$v.edit.name.$invalid && !errors.name,
                                                                            }" @keyup="arabicValue(edit.name)"
                                                                            id="field-1" />
                                                                        <div v-if="!$v.edit.name.minLength"
                                                                            class="invalid-feedback">
                                                                            {{ $t("general.Itmustbeatleast") }}
                                                                            {{ $v.edit.name.$params.minLength.min }}
                                                                            {{ $t("general.letters") }}
                                                                        </div>
                                                                        <div v-if="!$v.edit.name.maxLength"
                                                                            class="invalid-feedback">
                                                                            {{ $t("general.Itmustbeatmost") }}
                                                                            {{ $v.edit.name.$params.maxLength.max }}
                                                                            {{ $t("general.letters") }}
                                                                        </div>
                                                                        <template v-if="errors.name">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.name"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 direction-ltr" v-if="isVisible('name_e')" dir="ltr">
                                                                    <div class="form-group">
                                                                        <label for="field-2" class="control-label">
                                                                            {{ getCompanyKey("item_name_en") }}
                                                                            <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                                                        </label>
                                                                        <input type="text" class="form-control englishInput"
                                                                            v-model.trim="$v.edit.name_e.$model" :class="{
                                                                                'is-invalid':
                                                                                    $v.edit.name_e.$error || errors.name_e,
                                                                                'is-valid':
                                                                                    !$v.edit.name_e.$invalid && !errors.name_e,
                                                                            }" @keyup="englishValue(edit.name_e)"
                                                                            id="field-2" />
                                                                        <div v-if="!$v.edit.name_e.minLength"
                                                                            class="invalid-feedback">
                                                                            {{ $t("general.Itmustbeatleast") }}
                                                                            {{ $v.edit.name_e.$params.minLength.min }}
                                                                            {{ $t("general.letters") }}
                                                                        </div>
                                                                        <div v-if="!$v.edit.name_e.maxLength"
                                                                            class="invalid-feedback">
                                                                            {{ $t("general.Itmustbeatmost") }}
                                                                            {{ $v.edit.name_e.$params.maxLength.max }}
                                                                            {{ $t("general.letters") }}
                                                                        </div>
                                                                        <template v-if="errors.name_e">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.name_e"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6" v-if="isVisible('unit_id')">
                                                                    <div class="form-group position-relative">
                                                                        <label class="control-label">
                                                                            {{ getCompanyKey("item_unit") }}
                                                                            <span v-if="isRequired('unit_id')" class="text-danger">*</span>
                                                                        </label>
                                                                        <multiselect @input="showUnitModalEdit" v-model="edit.unit_id"
                                                                            :options="units.map((type) => type.id)"
                                                                            :custom-label="
                                                                                (opt) =>
                                                                                    $i18n.locale
                                                                                        ? units.find((x) => x.id == opt).name
                                                                                        : units.find((x) => x.id == opt).name_e
                                                                            ">
                                                                        </multiselect>

                                                                        <div v-if="!$v.edit.unit_id.integer"
                                                                            class="invalid-feedback">
                                                                            {{ $t("general.fieldIsInteger") }}
                                                                        </div>
                                                                        <template v-if="errors.unit_id">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.partner_id"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6" v-if="isVisible('type')">
                                                                    <div class="form-group">
                                                                        <label for="field-5" class="control-label">
                                                                            {{ getCompanyKey("item_type") }}
                                                                            <span v-if="isRequired('type')"  class="text-danger">*</span>
                                                                        </label>
                                                                        <select class="form-control"
                                                                            v-model="$v.edit.type.$model" :class="{
                                                                                'is-invalid':
                                                                                    $v.edit.type.$error || errors.type,
                                                                                'is-valid':
                                                                                    !$v.edit.type.$invalid && !errors.type,
                                                                            }">
                                                                            <option value="service">{{ $t("general.Service")
                                                                            }}</option>
                                                                            <option value="item">{{ $t("general.Item") }}
                                                                            </option>
                                                                            <option value="product">{{ $t("general.Product")
                                                                            }}</option>
                                                                        </select>
                                                                        <template v-if="errors.type">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.type"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6" v-if="isVisible('price')">
                                                                    <div class="form-group">
                                                                        <label for="field-5" class="control-label">
                                                                            {{ getCompanyKey("item_price") }}
                                                                            <span v-if="isRequired('price')" class="text-danger">*</span>
                                                                        </label>
                                                                        <input type="number" class="form-control"
                                                                            v-model.number="$v.edit.price.$model" :class="{
                                                                                'is-invalid':
                                                                                    $v.edit.price.$error || errors.price,
                                                                                'is-valid':
                                                                                    !$v.edit.price.$invalid && !errors.price,
                                                                            }" id="field-5" />
                                                                        <template v-if="errors.price">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.price"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </b-tab>
                                                        <b-tab :title="$t('general.ImageUploads')">
                                                            <div class="row">
                                                                <input accept="image/png, image/gif, image/jpeg, image/jpg"
                                                                    type="file" id="uploadImageEdit"
                                                                    @change.prevent="onImageChanged"
                                                                    class="input-file-upload position-absolute" :class="[
                                                                        'd-none',
                                                                        {
                                                                            'is-invalid': $v.edit.media.$error || errors.media,
                                                                            'is-valid':
                                                                                !$v.edit.media.$invalid && !errors.media,
                                                                        },
                                                                    ]" />
                                                                <div class="col-md-8 my-1">
                                                                    <!-- file upload -->
                                                                    <div class="row align-content-between"
                                                                        style="width: 100%; height: 100%">
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
                                                                                            <div
                                                                                                class="row align-items-center">
                                                                                                <div class="col-auto"
                                                                                                    @click="showPhoto = photo.webp">
                                                                                                    <img data-dz-thumbnail
                                                                                                        :src="photo.webp"
                                                                                                        class="avatar-sm rounded bg-light"
                                                                                                        @error="src = './images/img-1.png'" />
                                                                                                </div>
                                                                                                <div class="col pl-0">
                                                                                                    <a href="javascript:void(0);"
                                                                                                        :class="[
                                                                                                            'font-weight-bold',
                                                                                                            images.length - 1 == index
                                                                                                                ? 'text-white'
                                                                                                                : 'text-muted',
                                                                                                        ]"
                                                                                                        data-dz-name>
                                                                                                        {{ photo.name }}
                                                                                                    </a>
                                                                                                </div>
                                                                                                <!-- Button -->
                                                                                                <a href="javascript:void(0);"
                                                                                                    :class="[
                                                                                                        'btn-danger text-muted dropzone-close',
                                                                                                        $i18n.locale == 'ar'
                                                                                                            ? 'dropzone-close-rtl'
                                                                                                            : '',
                                                                                                    ]" data-dz-remove
                                                                                                    @click.prevent="
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
                                                                            <b-button @click="changePhotoEdit"
                                                                                variant="success" type="button"
                                                                                class="mx-1 font-weight-bold px-3"
                                                                                v-if="!isLoader">
                                                                                {{ $t("general.Add") }}
                                                                            </b-button>
                                                                            <b-button variant="success" class="mx-1"
                                                                                disabled v-else>
                                                                                <b-spinner small></b-spinner>
                                                                                <span class="sr-only">{{ $t("login.Loading")
                                                                                }}...</span>
                                                                            </b-button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="show-dropzone">
                                                                        <img :src="showPhoto" class="img-thumbnail"
                                                                            @error="src = './images/img-1.png'" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </b-tab>
                                                        <b-tab :title="$t('general.category')">
                                                            <!--                                                <div class="col-md-12 text-center">-->
                                                            <!--                                                    <h3>{{$t('general.DocumentName')}} : {{ $i18n.locale == "ar" ? edit.name : edit.name_e }}</h3>-->
                                                            <!--                                                </div>-->
                                                            <div class="col-md-6 mb-4 p-0 position-relative">
                                                                <div class="form-group">
                                                                    <label class="my-1 mr-2">{{
                                                                        $t("general.category")
                                                                    }}</label>
                                                                    <multiselect v-model="category_item_id"
                                                                        @select="addCategoryToItem"
                                                                        :options="categories.map((type) => type.id)"
                                                                        :custom-label="
                                                                            (opt) =>
                                                                                $i18n.locale=='ar'
                                                                                    ? categories.find((x) => x.id == opt).name
                                                                                    : categories.find((x) => x.id == opt).name_e
                                                                        ">
                                                                    </multiselect>
                                                                </div>
                                                            </div>
                                                            <!-- start .table-responsive-->
                                                            <div
                                                                class="table-responsive mb-3 custom-table-theme position-relative">
                                                                <!--       start loader       -->
                                                                <loader size="large" v-if="isLoader" />
                                                                <!--       end loader       -->

                                                                <table
                                                                    class="table table-borderless table-hover table-centered m-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>
                                                                                <div class="d-flex justify-content-center">
                                                                                    <span>{{ $t("general.Name") }}</span>
                                                                                </div>
                                                                            </th>
                                                                            <th>
                                                                                <div class="d-flex justify-content-center">
                                                                                    <span>{{ $t("general.Name_en") }}</span>
                                                                                </div>
                                                                            </th>
                                                                            <th>{{ $t("general.Action") }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody v-if="itemCategories.length > 0">
                                                                        <tr v-for="(data, index) in itemCategories"
                                                                            :key="data.id" class="body-tr-custom">
                                                                            <td>
                                                                                {{ data.name }}
                                                                            </td>
                                                                            <td>
                                                                                {{ data.name_e }}
                                                                            </td>

                                                                            <td>
                                                                                <div class="btn-group">
                                                                                    <button type="button"
                                                                                        class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                                                        data-toggle="dropdown"
                                                                                        aria-expanded="false">
                                                                                        {{ $t("general.commands") }}
                                                                                        <i class="fas fa-angle-down"></i>
                                                                                    </button>
                                                                                    <div
                                                                                        class="dropdown-menu dropdown-menu-custom">
                                                                                        <a class="dropdown-item text-black"
                                                                                            href="#"
                                                                                            @click.prevent="deleteItemCategory(data.id)">
                                                                                            <div
                                                                                                class="d-flex justify-content-between align-items-center text-black">
                                                                                                <span>{{
                                                                                                    $t("general.delete")
                                                                                                }}</span>
                                                                                                <i
                                                                                                    class="fas fa-times text-danger"></i>
                                                                                            </div>
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                    <tbody v-else>
                                                                        <tr>
                                                                            <th class="text-center" colspan="15">
                                                                                {{ $t("general.notDataFound") }}
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <!-- end .table-responsive-->
                                                        </b-tab>
                                                    </b-tabs>
                                                </form>
                                            </b-modal>
                                            <!--  /edit   -->
                                        </td>
                                        <td class="do-not-print" v-if="enabled3">
                                            <b-button class="btn" @mouseover="log(data.id)" @mousemove="log(data.id)"
                                                :id="'tooltip-button-' + data.id"
                                                :variant="$i18n.locale == 'en' ? 'left' : 'right'"> <i class="fe-info"
                                                    style="font-size: 22px"></i></b-button>
                                            <b-tooltip :target="'tooltip-button-' + data.id"
                                                :placement="$i18n.locale == 'en' ? 'left' : 'right'">{{ Tooltip
                                                }}</b-tooltip>
                                            <!--                      <button-->
                                            <!--                        @mouseover="log(data.id)"-->
                                            <!--                        @mousemove="log(data.id)"-->
                                            <!--                        type="button"-->
                                            <!--                        class="btn"-->
                                            <!--                        data-toggle="tooltip"-->
                                            <!--                        :data-placement="$i18n.locale == 'en' ? 'left' : 'right'"-->
                                            <!--                        :title="Tooltip"-->
                                            <!--                      >-->
                                            <!--                        <i class="fe-info" style="font-size: 22px"></i>-->
                                            <!--                      </button>-->
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <th class="text-center" colspan="15">
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
.dropdown-menu-custom-company.dropdown .dropdown-menu {
    padding: 5px 10px !important;
    overflow-y: scroll;
    height: 400px;
}

.modal-dialog .card {
    margin: 0 !important;
}

.modal-body.paddingUnset {
    padding: 0 !important;
}

.modal-dialog .card-body {
    padding: 1.5rem 1.5rem 0 1.5rem !important;
}

.nav-bordered {
    border: unset !important;
}

.nav {
    background-color: #dff0fe;
}

.tab-content {
    padding: 70px 60px 40px;
    min-height: 300px;
    background-color: #f5f5f5;
    position: relative;
}

.nav-tabs .nav-link {
    border: 1px solid #b7b7b7 !important;
    background-color: #d7e5f2;
    border-bottom: 0 !important;
    margin-bottom: 1px;
}

.nav-tabs .nav-link.active,
.nav-tabs .nav-item.show .nav-link {
    color: #000;
    background-color: hsl(0deg 0% 96%);
    border-bottom: 0 !important;
}

.img-thumbnail {
    max-height: 400px !important;
}

@media print {
    .do-not-print {
        display: none;
    }

    .arrow-sort {
        display: none;
    }

    .bg-soft-success {
        background-color: unset;
        color: #000000 !important;
        border: unset;
    }

    .bg-soft-danger {
        background-color: unset;
        color: #000000 !important;
        border: unset;
    }
}

.tooltip-inner {
    max-width: 750px !important;
    background-color: #eed900;
    color: black;
}</style>
