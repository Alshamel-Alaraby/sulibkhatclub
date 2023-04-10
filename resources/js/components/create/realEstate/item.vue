<script>
import PageHeader from "../../../components/Page-header";
import ErrorMessage from "../../../components/widgets/errorMessage";
import adminApi from "../../../api/adminAxios";
import transMixinComp from "../../../helper/translation-comp-mixin";
import ItemCategory from "../../../components/create/realEstate/itemCategory.vue"
import {
    required,
    minLength,
    maxLength,
    integer,
    email,
    sameAs,
    url,
    minValue,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import loader from "../../../components/loader";
import { dynamicSortNumber, dynamicSortString } from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";

/**
 * Advanced Table component
 */

// start validation image/png

export default {
    mixins: [transMixinComp],
    props: {
        companyKeys: {
            default: []
        },
        defaultsKeys: {
            default: []
        },
    },
    components: {
        ItemCategory,
        loader,
        ErrorMessage,
        Multiselect,
    },
    data() {
        return {
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
            errors: {},
            isCheckAll: false,
            checkAll: [],
            images: [],
            media: {},
            item_id: null,
            isVaildPhone: false,
            saveImageName: [],
            showPhoto: "./images/img-1.png",
            idDelete: null,
            printLoading: true,
            printObj: {
                id: "printMe",
            }
        };
    },
    validations: {
        create: {
            name: { required, minLength: minLength(3), maxLength: maxLength(100) },
            name_e: { required, minLength: minLength(3), maxLength: maxLength(100) },
            code_number: { required },
            price: { required, minValue: minValue(0) },
            unit_id: { required, minValue: minValue(0) },
            type: { required },
            media: {}
        },
    },
    updated() {
        // $(function () {
        //   $(".englishInput").keypress(function (event) {
        //     var ew = event.which;
        //     if (ew == 32) return true;
        //     if (48 <= ew && ew <= 57) return true;
        //     if (65 <= ew && ew <= 90) return true;
        //     if (97 <= ew && ew <= 122) return true;
        //     return false;
        //   });
        //   $(".arabicInput").keypress(function (event) {
        //     var ew = event.which;
        //     if (ew == 32) return true;
        //     if (48 <= ew && ew <= 57) return false;
        //     if (65 <= ew && ew <= 90) return false;
        //     if (97 <= ew && ew <= 122) return false;
        //     return true;
        //   });
        // });
    },
    methods: {
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
                    l.unshift({ id: 0, name: "اضف فئة", name_e: "Add category" });
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


        /**
         *  get Data company
         */
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
            this.$bvModal.hide(`item-create`);
            this.item_id = null;
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            await this.getUnits();
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
                    .post(`/real-estate/item`, this.create)
                    .then((res) => {
                        this.item_id = res.data.data.id;
                        this.$emit("created");
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
        async getUnits() {
            await adminApi
                .get(`/units`)
                .then((res) => {
                    let l = res.data;
                    this.units = l.data;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
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
        },

        englishValue(txt) {
            this.create.name_e = englishValue(txt);
        }
    },
};
</script>

<template>
    <div>
        <ItemCategory :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCategories"
            @deleted="getCategories" />

        <!--  create   -->
        <b-modal id="item-create" :title="getCompanyKey('item_create_form')" title-class="font-18"
            dialog-class="modal-full-width" body-class="paddingUnset" :hide-footer="true" @show="resetModal"
            @hidden="resetModalHidden">
            <form @submit.stop.prevent="AddSubmit">
                <div class="card">
                    <div class="card-body">
                        <div class="mt-1 d-flex justify-content-end">
                            <!-- Emulate built in modal footer ok and cancel button actions -->
                            <b-button variant="success" :disabled="!item_id" @click.prevent="resetForm" type="button"
                                :class="['font-weight-bold px-2', item_id ? 'mx-2' : '']">
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="field-5" class="control-label">
                                            {{ getCompanyKey("code_number") }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input class="form-control" v-model.number="$v.create.code_number.$model" :class="{
                                            'is-invalid':
                                                $v.create.code_number.$error || errors.code_number,
                                            'is-valid':
                                                !$v.create.code_number.$invalid && !errors.code_number,
                                        }" id="field-5" />
                                        <template v-if="errors.code_number">
                                            <ErrorMessage v-for="(errorMessage, index) in errors.code_number" :key="index">
                                                {{ errorMessage }}</ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-6 direction" dir="rtl">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">
                                            {{ getCompanyKey("item_name_ar") }}
                                            <span class="text-danger">*</span>
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
                                            <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index">{{
                                                errorMessage }}</ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-6 direction-ltr" dir="ltr">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">
                                            {{ getCompanyKey("item_name_en") }}
                                            <span class="text-danger">*</span>
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
                                            <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index">{{
                                                errorMessage }}</ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group position-relative">
                                        <label class="control-label">
                                            {{ getCompanyKey("item_unit") }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <multiselect v-model="create.unit_id" :options="units.map((type) => type.id)"
                                            :custom-label="
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
                                            <ErrorMessage v-for="(errorMessage, index) in errors.partner_id" :key="index">{{
                                                errorMessage }}</ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="field-5" class="control-label">
                                            {{ getCompanyKey("item_type") }}
                                            <span class="text-danger">*</span>
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
                                            <ErrorMessage v-for="(errorMessage, index) in errors.type" :key="index">{{
                                                errorMessage }}</ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="field-5" class="control-label">
                                            {{ getCompanyKey("item_price") }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" v-model.number="$v.create.price.$model"
                                            :class="{
                                                'is-invalid':
                                                    $v.create.price.$error || errors.price,
                                                'is-valid':
                                                    !$v.create.price.$invalid && !errors.price,
                                            }" id="field-5" />
                                        <template v-if="errors.price">
                                            <ErrorMessage v-for="(errorMessage, index) in errors.price" :key="index">{{
                                                errorMessage }}</ErrorMessage>
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
                                        <img :src="showPhoto" class="img-thumbnail" @error="src = './images/img-1.png'" />
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
                                    <multiselect v-model="category_item_id" @select="addCategoryToItem"
                                        :options="categories.map((type) => type.id)" :custom-label="
                                            (opt) =>
                                                $i18n.locale == 'ar'
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
                                        <tr v-for="(data, index) in itemCategories" :key="data.id" class="body-tr-custom">
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

    </div>
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
}
</style>
