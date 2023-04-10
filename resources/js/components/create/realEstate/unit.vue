<script>
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import { required, minLength, maxLength, integer, numeric } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import { dynamicSortNumber, dynamicSortString } from "../../../helper/tableSort";
import { formatDateOnly, formatDateTime } from "../../../helper/startDate";
import Multiselect from "vue-multiselect";
import DatePicker from "vue2-datepicker";
import transMixinComp from "../../../helper/translation-comp-mixin";
import Building from "../../../components/create/building";
import unitStatus from "../../../components/create/realEstate/unitStatus";
import propertyTree from "../../../components/create/property-tree";
import { arabicValue, englishValue } from "../../../helper/langTransform";

/**
 * Advanced Table component
 */

export default {
    mixins:[transMixinComp],
    components: {
        Switches,
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
        Building,
        unitStatus,
        propertyTree
    },
    props: {
        id: {
            default: "create-unit",
        },
        companyKeys: {
            default: [],
        },
        defaultsKeys: {
            default: [],
        },
    },
    data() {
        return {
            images: [],
            media: {},
            unit_id: null,
            saveImageName: [],
            showPhoto: "./images/img-1.png",
            mime_type: '',
            units: [],
            buildings: [],
            unit_status: [],
            properties: [],
            modules: ['sell', 'buying'],
            isLoader: false,
            create: {
                name: '',
                name_e: '',
                description: '',
                description_e: '',
                code: '',
                unit_ty: 0,
                unit_area: 0,
                unit_net_area: 0,
                properties: [],
                module: 'sell',
                building_id: null,
                unit_status_id: null,
                rooms: 0,
                path: 0,
                view: 0,
                floor: 0,
                finishing: 0
            },
            errors: {},
            isCheckAll: false,
            enabled3: true,
            checkAll: [],
            image: '',

            is_disabled: false,
            idDelete: null,
            Tooltip: '',
            mouseEnter: null,
            printLoading: true,
            printObj: {
                id: "printUnit",
            }
        }
    },
    validations: {
        create: {
            name: { required, minLength: minLength(2), maxLength: maxLength(100), },
            name_e: { required, minLength: minLength(2), maxLength: maxLength(100), },
            description: { maxLength: maxLength(1000) },
            description_e: { maxLength: maxLength(1000) },
            code: { required, maxLength: maxLength(20), },
            unit_ty: { integer },
            unit_area: { numeric },
            module: { required },
            properties: { required },
            building_id: { required },
            unit_status_id: { required },
            rooms: { integer },
            path: { integer },
            view: { integer },
            floor: { integer },
            finishing: { integer },
            unit_net_area: { numeric },
        },
    },
    // updated(){
    //     $(function(){
    //         $(".englishInput").keypress(function(event){
    //             var ew = event.which;
    //             if(ew == 32)
    //                 return true;
    //             if(48 <= ew && ew <= 57)
    //                 return true;
    //             if(65 <= ew && ew <= 90)
    //                 return true;
    //             if(97 <= ew && ew <= 122)
    //                 return true;
    //             return false;
    //         });
    //         $(".arabicInput").keypress(function(event){
    //             var ew = event.which;
    //             if(ew == 32)
    //                 return true;
    //             if(48 <= ew && ew <= 57)
    //                 return true;
    //             if(65 <= ew && ew <= 90)
    //                 return false;
    //             if(97 <= ew && ew <= 122)
    //                 return false;
    //             return true;
    //         });
    //     });
    // },
    methods: {
        arabicValue(txt) {
            this.create.name = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.name_e = englishValue(txt);
        },
        arabicValueDescription(txt) {
            this.create.description = arabicValue(txt);
        },
        englishValueDescription(txt) {
            this.create.description_e = englishValue(txt);
        },

        /**
         *  end delete countrie
         */
        /**
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.create = {
                name: '',
                name_e: '',
                description: '',
                description_e: '',
                code: '',
                properties: [],
                unit_ty: 0,
                unit_area: 0,
                unit_net_area: 0,
                module: 'sell',
                building_id: null,
                unit_status_id: null,
                rooms: 0,
                path: 0,
                view: 0,
                floor: 0,
                finishing: 0
            };
            this.$nextTick(() => { this.$v.$reset() });
            this.errors = {};
            this.$bvModal.hide(`create-unit`);
            this.images = [];
            this.mime_type = '';
            this.unit_id = null;
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            await this.getBuildings();
            await this.getUnitStatus();
            await this.getProperty();
            this.create = {
                name: '',
                name_e: '',
                description: '',
                description_e: '',
                code: '',
                properties: [],
                unit_ty: 0,
                unit_area: 0,
                unit_net_area: 0,
                module: 'sell',
                building_id: null,
                unit_status_id: null,
                rooms: 0,
                path: 0,
                view: 0,
                floor: 0,
                finishing: 0
            };
            this.$nextTick(() => { this.$v.$reset() });
            this.showPhoto = "./images/img-1.png";
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.unit_id = null;
            this.media = {};
            this.mime_type = '';
            this.images = [];
            this.errors = {};
        },
        /**
         *  create countrie
         */
        resetForm() {
            this.create = {
                name: '',
                name_e: '',
                description: '',
                description_e: '',
                code: '',
                unit_ty: 0,
                unit_area: 0,
                properties: [],
                unit_net_area: 0,
                module: 'sell',
                building_id: null,
                unit_status_id: null,
                rooms: 0,
                path: 0,
                view: 0,
                floor: 0,
                finishing: 0
            };
            this.$nextTick(() => { this.$v.$reset() });
            this.errors = {};
            this.unit_id = null;
            this.is_disabled = false;
            this.showPhoto = "./images/img-1.png";
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.media = {};
            this.images = [];
            this.mime_type = '';
        },
        AddSubmit() {

            if (!this.create.name) { this.create.name = this.create.name_e }
            if (!this.create.name_e) { this.create.name_e = this.create.name }
            if (!this.create.description) { this.create.description = this.create.description_e }
            if (!this.create.description_e) { this.create.description_e = this.create.description }

            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                adminApi.post(`/real-estate/units`, this.create)
                    .then((res) => {
                        this.is_disabled = true;
                        this.unit_id = res.data.data.id;
                        this.$emit("created");
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                text: `${this.$t('general.Addedsuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
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
         *  start  dynamicSortString
         */
        sortString(value) { return dynamicSortString(value); },
        SortNumber(value) { return dynamicSortNumber(value); },
        /**
         *  start  ckeckRow
         */
        /**
         *  end  ckeckRow
         */
        formatDate(value) {
            return formatDateOnly(value);
        },

        async getBuildings() {
            this.isLoader = true;
            await adminApi
                .get(`/real-estate/buildings`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضافة مبنى", name_e: "Add building" });
                    this.buildings = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getUnitStatus() {
            this.isLoader = true;

            await adminApi
                .get(
                    `real-estate/unit-statuses?is_active=active`
                )
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف حاله الوحده  ", name_e: "Add Unit Status" });
                    this.unit_status = l;
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
        async getProperty() {
            this.isLoader = true;

            await adminApi
                .get(
                    `/tree-properties`
                )
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف خصائص  ", name_e: "Add Property" });
                    this.properties = l;
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
        getCurrentYear() {
            return new Date().getFullYear();
        },
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
                if (dl) {
                    XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' });
                } else {
                    XLSX.writeFile(wb, fn || (('Unit' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },
        showBuildingModal() {
            if (this.create.building_id == 0) {
                this.$bvModal.show("building-create");
                this.create.building_id = null;
            }
        },
        showUnitStatusModal() {
            if (this.create.unit_status_id == 0) {
                this.$bvModal.show("unit-satatus-create");
                this.create.unit_status_id = null;
            }
        },
        showPropertyModal() {
            if (this.create.properties.includes(0)) {
                this.$bvModal.show("property-create");
                this.create.properties.pop();
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
                                .put(`/real-estate/units/${this.unit_id}`, {
                                    old_media,
                                    media: new_media,
                                })
                                .then((res) => {
                                    this.images = res.data.data.media ?? [];
                                    if (this.images && this.images.length > 0) {
                                        this.showPhoto = this.images[this.images.length - 1].url;
                                        this.mime_type = this.images[this.images.length - 1].mime_type;
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
                                        .put(`/real-estate/units/${this.unit_id}`, {
                                            old_media,
                                            media: new_media,
                                        })
                                        .then((res) => {
                                            this.images = res.data.data.media ?? [];
                                            if (this.images && this.images.length > 0) {
                                                this.showPhoto = this.images[this.images.length - 1].url;
                                                this.mime_type = this.images[this.images.length - 1].mime_type;
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
                .put(`/real-estate/units/${this.unit_id}`, { old_media })
                .then((res) => {
                    this.units[index] = res.data.data;
                    this.images = res.data.data.media ?? [];
                    if (this.images && this.images.length > 0) {
                        this.showPhoto = this.images[this.images.length - 1].url;
                        this.mime_type = this.images[this.images.length - 1].mime_type;
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
        }
    },
};
</script>

<template>
    <div>
        <Building :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getBuildings" />
        <unitStatus :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getUnitStatus" />
        <propertyTree :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getProperty" />

        <!--  create   -->
        <b-modal :id="id" :title="getCompanyKey('realEstate_unit_create_form')" title-class="font-18"
            dialog-class="modal-full-width" body-class="p-4 " :hide-footer="true" @show="resetModal"
            @hidden="resetModalHidden">
            <form>
                <div class="mb-3 d-flex justify-content-end">

                    <b-button variant="success" :disabled="!is_disabled" @click.prevent="resetForm" type="button"
                        :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                        {{ $t('general.AddNewRecord') }}
                    </b-button>
                    <template v-if="!is_disabled">
                        <b-button variant="success" type="button" class="mx-1" v-if="!isLoader" @click.prevent="AddSubmit">
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
                <b-tabs nav-class="nav-tabs nav-bordered">
                    <b-tab :title="$t('general.DataEntry')" active>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group position-relative">
                                    <label class="control-label">
                                        {{ getCompanyKey('realEstate_unit_building') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <multiselect @input="showBuildingModal" v-model="$v.create.building_id.$model"
                                        :options="buildings.map((type) => type.id)"
                                        :custom-label="(opt) => $i18n.locale == 'ar' ? buildings.find((x) => x.id == opt).name : buildings.find((x) => x.id == opt).name_e">
                                    </multiselect>
                                    <div v-if="$v.create.building_id.$error || errors.building_id" class="text-danger">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.building_id">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.building_id" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="field-4353" class="control-label">
                                        {{ getCompanyKey('realEstate_unit_code') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div dir="ltr">
                                        <input type="text" class="form-control" v-model="$v.create.code.$model" :class="{
                                            'is-invalid': $v.create.code.$error || errors.code,
                                            'is-valid': !$v.create.code.$invalid && !errors.code
                                        }" id="field-4353" />
                                    </div>
                                    <div v-if="!$v.create.code.maxLength" class="invalid-feedback">{{
                                        $t('general.Itmustbeatmost') }} {{ $v.create.code.$params.maxLength.max }} {{
        $t('general.letters') }}</div>
                                    <template v-if="errors.code">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.code" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey('realEstate_unit_unit_ty') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" v-model="$v.create.unit_ty.$model" :class="{
                                        'is-invalid': $v.create.unit_ty.$error || errors.unit_ty,
                                        'is-valid': !$v.create.unit_ty.$invalid && !errors.unit_ty
                                    }" />
                                    <template v-if="errors.unit_ty">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.unit_ty" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="field-1" class="control-label">
                                        {{ getCompanyKey('realEstate_unit_name_ar') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div dir="rtl">
                                        <input @keyup="arabicValue(create.name)" type="text" class="form-control"
                                            v-model="$v.create.name.$model" :class="{
                                                'is-invalid': $v.create.name.$error || errors.name,
                                                'is-valid': !$v.create.name.$invalid && !errors.name
                                            }" id="field-1" />
                                    </div>
                                    <div v-if="!$v.create.name.minLength" class="invalid-feedback">{{
                                        $t('general.Itmustbeatleast') }} {{ $v.create.name.$params.minLength.min }} {{
        $t('general.letters') }}</div>
                                    <div v-if="!$v.create.name.maxLength" class="invalid-feedback">{{
                                        $t('general.Itmustbeatmost') }} {{ $v.create.name.$params.maxLength.max }} {{
        $t('general.letters') }}</div>
                                    <template v-if="errors.name">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">
                                        {{ getCompanyKey('realEstate_unit_name_en') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div dir="ltr">
                                        <input @keyup="englishValue(create.name_e)" type="text" class="form-control"
                                            v-model="$v.create.name_e.$model" :class="{
                                                'is-invalid': $v.create.name_e.$error || errors.name_e,
                                                'is-valid': !$v.create.name_e.$invalid && !errors.name_e
                                            }" id="field-2" />
                                    </div>
                                    <div v-if="!$v.create.name_e.minLength" class="invalid-feedback">{{
                                        $t('general.Itmustbeatleast') }} {{ $v.create.name_e.$params.minLength.min }} {{
        $t('general.letters') }}</div>
                                    <div v-if="!$v.create.name_e.maxLength" class="invalid-feedback">{{
                                        $t('general.Itmustbeatmost') }} {{ $v.create.name_e.$params.maxLength.max }} {{
        $t('general.letters') }}</div>
                                    <template v-if="errors.name_e">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey('realEstate_unit_floor') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" v-model="$v.create.floor.$model" :class="{
                                        'is-invalid': $v.create.floor.$error || errors.floor,
                                        'is-valid': !$v.create.floor.$invalid && !errors.floor
                                    }" />
                                    <template v-if="errors.floor">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.floor" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group position-relative">
                                    <label class="control-label">
                                        {{ getCompanyKey('realEstate_unit_status') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <multiselect @input="showUnitStatusModal" v-model="$v.create.unit_status_id.$model"
                                        :options="unit_status.map((type) => type.id)"
                                        :custom-label="(opt) => $i18n.locale == 'ar' ? unit_status.find((x) => x.id == opt).name : unit_status.find((x) => x.id == opt).name_e">
                                    </multiselect>
                                    <div v-if="$v.create.unit_status_id.$error || errors.unit_status_id"
                                        class="text-danger">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.unit_status_id">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.unit_status_id" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey('realEstate_unit_unit_area') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" step="0.1"
                                        v-model="$v.create.unit_area.$model" :class="{
                                            'is-invalid': $v.create.unit_area.$error || errors.unit_area,
                                            'is-valid': !$v.create.unit_area.$invalid && !errors.unit_area
                                        }" />
                                    <template v-if="errors.unit_area">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.land_area" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey('realEstate_unit_unit_net_area') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" step="0.1"
                                        v-model="$v.create.unit_net_area.$model" :class="{
                                            'is-invalid': $v.create.unit_net_area.$error || errors.unit_net_area,
                                            'is-valid': !$v.create.unit_net_area.$invalid && !errors.unit_net_area
                                        }" />
                                    <template v-if="errors.unit_net_area">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.unit_net_area" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey('realEstate_unit_rooms') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" v-model="$v.create.rooms.$model" :class="{
                                        'is-invalid': $v.create.rooms.$error || errors.rooms,
                                        'is-valid': !$v.create.rooms.$invalid && !errors.rooms
                                    }" />
                                    <template v-if="errors.rooms">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.rooms" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey('realEstate_unit_path') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" v-model="$v.create.path.$model" :class="{
                                        'is-invalid': $v.create.path.$error || errors.path,
                                        'is-valid': !$v.create.path.$invalid && !errors.path
                                    }" />
                                    <template v-if="errors.path">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.path" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey('realEstate_unit_finishing') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" v-model="$v.create.finishing.$model" :class="{
                                        'is-invalid': $v.create.finishing.$error || errors.finishing,
                                        'is-valid': !$v.create.finishing.$invalid && !errors.finishing
                                    }" />
                                    <template v-if="errors.finishing">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.finishing" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey('realEstate_unit_view') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" v-model="$v.create.view.$model" :class="{
                                        'is-invalid': $v.create.view.$error || errors.view,
                                        'is-valid': !$v.create.view.$invalid && !errors.view
                                    }" />
                                    <template v-if="errors.view">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.view" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey('realEstate_unit_properties') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <multiselect :multiple="true" @input="showPropertyModal"
                                        v-model="$v.create.properties.$model" :options="properties.map((type) => type.id)"
                                        :custom-label="(opt) => $i18n.locale == 'ar' ? properties.find((x) => x.id == opt).name : properties.find((x) => x.id == opt).name_e">
                                    </multiselect>
                                    <template v-if="errors.properties">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.properties" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mr-2">
                                        {{ getCompanyKey('realEstate_unit_description_ar') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea @input="arabicValueDescription(create.description)"
                                        v-model="$v.create.description.$model" class="form-control" :maxlength="1000"
                                        rows="5"></textarea>
                                    <template v-if="errors.description">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.description" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mr-2">
                                        {{ getCompanyKey('realEstate_unit_description_en') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea @input="englishValueDescription(create.description_e)"
                                        v-model="$v.create.description_e.$model" class="form-control" :maxlength="1000"
                                        rows="5"></textarea>
                                    <template v-if="errors.description_e">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.description_e" :key="index">{{
                                            errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </b-tab>
                    <b-tab :disabled="!unit_id" :title="$t('general.ImageUploads')">
                        <div class="row">
                            <input accept="image/png, image/gif, image/jpeg, image/jpg" type="file" id="uploadImageCreate"
                                @change.prevent="onImageChanged" class="input-file-upload position-absolute"
                                :class="['d-none']" />
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
                                                            <div class="col-auto"
                                                                @click="showPhoto = photo.url; mime_type = photo.mime_type">
                                                                <template v-if="!photo.mime_type.includes('video')">
                                                                    <img data-dz-thumbnail :src="photo.url"
                                                                        class="avatar-sm rounded bg-light"
                                                                        @error="src = '../../../assets/images/video.jpg'" />
                                                                </template>
                                                                <template v-else>
                                                                    <img data-dz-thumbnail
                                                                        src="../../../assets/images/video.jpg"
                                                                        class="avatar-sm rounded bg-light"
                                                                        @error="src = '../../../assets/images/video.jpg'" />
                                                                </template>
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
                                <div class="show-dropzone" v-if="!mime_type.includes('video')">
                                    <img :src="showPhoto" class="img-thumbnail" @error="src = './images/img-1.png'" />
                                </div>
                                <div class="show-dropzone" v-else>
                                    <video width="320" height="240" controls autoplay>
                                        <source :src="showPhoto" :type="mime_type">
                                        <source :src="showPhoto" type="video/mp4">
                                        <source :src="showPhoto" type="video/ogg">
                                    </video>
                                </div>
                            </div>
                        </div>
                    </b-tab>
                </b-tabs>
            </form>
        </b-modal>
        <!--  /create   -->
    </div>
</template>
<style scoped>
.dropdown-menu-custom-company.dropdown .dropdown-menu {
    padding: 5px 10px !important;
    overflow-y: scroll;
    height: 300px;
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
</style>




