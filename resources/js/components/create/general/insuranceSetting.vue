<template>
   <div>
       <!--  create   -->
       <b-modal
           :id="id"
           :title="type != 'edit' ?getCompanyKey('car_insurance_settings_create_form'):getCompanyKey('car_insurance_settings_edit_form')"
           title-class="font-18"
           body-class="p-4 "
           :hide-footer="true"
           @show="resetModal"
           @hidden="resetModalHidden"
       >
           <form>
               <loader size="large" v-if="isCustom && !isPage" />
               <div class="mb-3 d-flex justify-content-end">
                   <b-button
                       v-if="type != 'edit'"
                       variant="success"
                       :disabled="!is_disabled"
                       @click.prevent="resetForm"
                       type="button"
                       :class="[
                      'font-weight-bold px-2',
                      is_disabled ? 'mx-2' : '',
                    ]"
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
                           {{ type != 'edit'? $t("general.Add"): $t("general.edit") }}
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
                   <div class="col-md-12" v-if="isVisible('title')">
                       <div class="form-group">
                           <label for="field-1" class="control-label">
                               {{ getCompanyKey('car_insurance_settings_title_ar') }}
                               <span v-if="isRequired('title')" class="text-danger">*</span>
                           </label>
                           <div dir="rtl">
                               <input
                                   type="text"
                                   class="form-control arabicInput"
                                   @keyup="arabicValueName(create.title)"
                                   v-model="$v.create.title.$model"
                                   :class="{
                                        'is-invalid': $v.create.title.$error || errors.title,
                                        'is-valid': !$v.create.title.$invalid && !errors.title,
                                      }"
                                   id="field-1"
                               />
                           </div>
                           <div v-if="!$v.create.title.minLength" class="invalid-feedback">
                               {{ $t("general.Itmustbeatleast") }}
                               {{ $v.create.title.$params.minLength.min }}
                               {{ $t("general.letters") }}
                           </div>
                           <div v-if="!$v.create.title.maxLength" class="invalid-feedback">
                               {{ $t("general.Itmustbeatmost") }}
                               {{ $v.create.title.$params.maxLength.max }}
                               {{ $t("general.letters") }}
                           </div>
                           <template v-if="errors.title">
                               <ErrorMessage
                                   v-for="(errorMessage, index) in errors.title"
                                   :key="index"
                               >{{ errorMessage }}</ErrorMessage
                               >
                           </template>
                       </div>
                   </div>
                   <div class="col-md-12" v-if="isVisible('title_e')">
                       <div class="form-group">
                           <label for="field-2" class="control-label">
                               {{ getCompanyKey('car_insurance_settings_title_en') }}
                               <span v-if="isRequired('title_e')" class="text-danger">*</span>
                           </label>
                           <div dir="ltr">
                               <input
                                   type="text"
                                   class="form-control englishInput"
                                   data-create="2"
                                   @keyup="englishValueName(create.title_e)"
                                   v-model="$v.create.title_e.$model"
                                   :class="{
                                        'is-invalid': $v.create.title_e.$error || errors.title_e,
                                        'is-valid': !$v.create.title_e.$invalid && !errors.title_e,
                                      }"
                                   id="field-2"
                               />
                           </div>
                           <div v-if="!$v.create.title_e.minLength" class="invalid-feedback">
                               {{ $t("general.Itmustbeatleast") }}
                               {{ $v.create.title_e.$params.minLength.min }}
                               {{ $t("general.letters") }}
                           </div>
                           <div v-if="!$v.create.title_e.maxLength" class="invalid-feedback">
                               {{ $t("general.Itmustbeatmost") }}
                               {{ $v.create.title_e.$params.maxLength.max }}
                               {{ $t("general.letters") }}
                           </div>
                           <template v-if="errors.title_e">
                               <ErrorMessage
                                   v-for="(errorMessage, index) in errors.title_e"
                                   :key="index"
                               >{{ errorMessage }}</ErrorMessage
                               >
                           </template>
                       </div>
                   </div>
                   <div class="col-md-12" v-if="isVisible('db_table')">
                       <div class="form-group position-relative">
                           <label class="control-label">
                               {{ getCompanyKey("car_insurance_settings_db_table") }}
                               <span class="text-danger">*</span>
                           </label>
                           <multiselect
                               @input="getColumns(create.db_table)"
                               v-model="$v.create.db_table.$model"
                               :options="tables"
                               :custom-label="(opt) => opt"
                           >
                           </multiselect>
                           <div
                               v-if="$v.create.db_table.$error || errors.db_table"
                               class="text-danger"
                           >
                               {{ $t("general.fieldIsRequired") }}
                           </div>
                           <template v-if="errors.db_table">
                               <ErrorMessage
                                   v-for="(errorMessage, index) in errors.db_table"
                                   :key="index"
                               >{{ errorMessage }}
                               </ErrorMessage>
                           </template>
                       </div>
                   </div>
                   <div class="col-md-12" v-if="create.db_table && isVisible('db_field')">
                       <div class="form-group position-relative">
                           <label class="control-label">
                               {{ getCompanyKey("document-field-car_insurance_settings_db_field") }}
                               <span class="text-danger">*</span>
                           </label>
                           <multiselect
                               v-model="$v.create.db_field.$model"
                               :options="columns.length > 0 ? columns: []"
                               :custom-label="(opt) => opt"
                           >
                           </multiselect>
                           <div
                               v-if="
                                  $v.create.db_field.$error ||
                                  errors.db_field
                                "
                               class="text-danger"
                           >
                               {{ $t("general.fieldIsRequired") }}
                           </div>
                           <template v-if="errors.db_field">
                               <ErrorMessage
                                   v-for="(errorMessage, index) in errors.db_field"
                                   :key="index"
                               >{{ errorMessage }}
                               </ErrorMessage>
                           </template>
                       </div>
                   </div>
               </div>
           </form>
       </b-modal>
       <!--  /create   -->
   </div>
</template>

<script>
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import {maxLength, minLength, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import Swal from "sweetalert2";
import Multiselect from "vue-multiselect";

export default {
    name: "group",
    components: {
        ErrorMessage,
        loader,
        Multiselect,
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/insurance-settings'}
    },
    data() {
        return {
            fields: [],
            isLoader: false,
            isCustom:false,
            company_id:null,
            create: {
                title: "",
                title_e: "",
                db_table: "",
                db_field: ""
            },
            errors: {},
            tables: [],
            columns: [],
            is_disabled: false,
        };
    },
    validations: {
        create: {
            title: { required: requiredIf(function (model) {
                    return this.isRequired("title");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            title_e: { required: requiredIf(function (model) {
                    return this.isRequired("title_e");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            db_table: { required: requiredIf(function (model) {
                    return this.isRequired("db_table");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            db_field: { required: requiredIf(function (model) {
                    return this.isRequired("db_field");
                }), minLength: minLength(2), maxLength: maxLength(100) },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        getCustomTableFields() {
            this.isCustom = true;
             adminApi
                .get(`/customTable/table-columns/general_insurance_settings`)
                .then((res) => {
                    this.fields = res.data;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isCustom = false;
                });
        },
        isVisible(fieldName) {
            if(!this.isPage){
                let res = this.fields.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_visible == 1 ? true : false;
            }else {
                return this.isVisiblePage(fieldName);
            }
        },
        isRequired(fieldName) {
            if(!this.isPage) {
                let res = this.fields.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_required == 1 ? true : false;
            }else {
                return this.isRequiredPage(fieldName);
            }
        },
        defaultData(edit = null){
            this.create = {
                title: "",
                title_e: "",
                db_table: "",
                db_field: ""
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabled = false;
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        resetModal() {
             this.defaultData();
             setTimeout( async () => {
                 if(this.type != 'edit'){
                     if(!this.isPage) await this.getCustomTableFields();
                     await this.getTables();
                 }else {
                     if(this.idObjEdit.dataObj){
                         let group = this.idObjEdit.dataObj;
                         this.errors = {};
                         this.create.title = group.title;
                         this.create.title_e = group.title_e;
                         this.create.db_table = group.db_table;
                         this.create.db_field = group.db_field;
                         await this.getTables();
                         await this.getColumns(group.db_table)
                     }
                 }
             },50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            if (!this.create.title) {
                this.create.title = this.create.title_e;
            }
            if (!this.create.title_e) {
                this.create.title_e = this.create.title;
            }
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                if(this.type != 'edit'){
                    adminApi
                        .post(this.url, {...this.create,company_id:this.company_id})
                        .then((res) => {
                            this.is_disabled = true;
                            setTimeout(() => {
                                this.successFun('Addedsuccessfully');
                            }, 500);
                            if(!this.isPage)
                                this.$emit("created");
                            else
                                this.$emit("getDataTable");
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error','Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }else {
                    adminApi
                        .put(`${this.url}/${this.idObjEdit.idEdit}`, {
                            ...this.create,
                        })
                        .then((res) => {
                            this.$bvModal.hide(this.id);
                            this.$emit("getDataTable");
                            setTimeout(() => {
                                this.successFun('Editsuccessfully');
                            }, 500);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error','Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }
            }
        },
        arabicValueName(txt){
            this.create.title = arabicValue(txt);
        },
        englishValueName(txt){
            this.create.title_e = englishValue(txt);
        },
        async getTables() {
            this.isLoader = true;

            await adminApi
                .get(`/insurance-settings/tables`)
                .then((res) => {
                    let l = res.data.data;
                    this.tables = l;
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
        async getColumns(table) {
            this.isLoader = true;

            await adminApi
                .get(`/insurance-settings/columns/${table}`)
                .then((res) => {
                    let l = res.data.data;
                    this.columns = l;
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
    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
