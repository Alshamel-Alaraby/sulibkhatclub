<template>
   <div>
       <!--  create   -->
       <b-modal
           :id="id"
           :title="type != 'edit'?getCompanyKey('hms_drugs_create_form'):getCompanyKey('hms_drugs_edit_form')"
           title-class="font-18"
           size="lg"
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
                   <div class="col-md-6" v-if="isVisible('trade_name')">
                       <div class="form-group">
                           <label for="field-1" class="control-label">
                               {{ getCompanyKey('hms_drugs_trade_name_ar') }}
                               <span v-if="isRequired('trade_name')" class="text-danger">*</span>
                           </label>
                           <div dir="rtl">
                               <input
                                   type="text"
                                   class="form-control arabicInput"
                                   @keyup="arabicValueName(create.trade_name)"
                                   v-model="$v.create.trade_name.$model"
                                   :class="{
                            'is-invalid': $v.create.trade_name.$error || errors.trade_name,
                            'is-valid': !$v.create.trade_name.$invalid && !errors.trade_name,
                          }"
                                   id="field-1"
                               />
                           </div>
                           <div v-if="!$v.create.trade_name.minLength" class="invalid-feedback">
                               {{ $t("general.Itmustbeatleast") }}
                               {{ $v.create.trade_name.$params.minLength.min }}
                               {{ $t("general.letters") }}
                           </div>
                           <div v-if="!$v.create.trade_name.maxLength" class="invalid-feedback">
                               {{ $t("general.Itmustbeatmost") }}
                               {{ $v.create.trade_name.$params.maxLength.max }}
                               {{ $t("general.letters") }}
                           </div>
                           <template v-if="errors.trade_name">
                               <ErrorMessage
                                   v-for="(errorMessage, index) in errors.trade_name"
                                   :key="index"
                               >{{ errorMessage }}</ErrorMessage
                               >
                           </template>
                       </div>
                   </div>
                   <div class="col-md-6" v-if="isVisible('trade_name_e')">
                       <div class="form-group">
                           <label for="field-2" class="control-label">
                               {{ getCompanyKey('hms_drugs_trade_name_en') }}
                               <span v-if="isRequired('trade_name_e')" class="text-danger">*</span>
                           </label>
                           <div dir="ltr">
                               <input
                                   type="text"
                                   class="form-control englishInput"
                                   data-create="2"
                                   @keyup="englishValueName(create.trade_name_e)"
                                   v-model="$v.create.trade_name_e.$model"
                                   :class="{
                                        'is-invalid': $v.create.trade_name_e.$error || errors.trade_name_e,
                                        'is-valid': !$v.create.trade_name_e.$invalid && !errors.trade_name_e,
                                    }"
                                   id="field-2"
                               />
                           </div>
                           <div v-if="!$v.create.trade_name_e.minLength" class="invalid-feedback">
                               {{ $t("general.Itmustbeatleast") }}
                               {{ $v.create.trade_name_e.$params.minLength.min }}
                               {{ $t("general.letters") }}
                           </div>
                           <div v-if="!$v.create.trade_name_e.maxLength" class="invalid-feedback">
                               {{ $t("general.Itmustbeatmost") }}
                               {{ $v.create.trade_name_e.$params.maxLength.max }}
                               {{ $t("general.letters") }}
                           </div>
                           <template v-if="errors.trade_name_e">
                               <ErrorMessage
                                   v-for="(errorMessage, index) in errors.trade_name_e"
                                   :key="index"
                               >{{ errorMessage }}</ErrorMessage
                               >
                           </template>
                       </div>
                   </div>

                   <div class="col-md-6" v-if="isVisible('generic_name')">
                       <div class="form-group">
                           <label for="field-11" class="control-label">
                               {{ getCompanyKey('hms_drugs_generic_name_ar') }}
                               <span v-if="isRequired('generic_name')" class="text-danger">*</span>
                           </label>
                           <div dir="rtl">
                               <input
                                   type="text"
                                   class="form-control arabicInput"
                                   @keyup="arabicValueName(create.generic_name)"
                                   v-model="$v.create.generic_name.$model"
                                   :class="{
                            'is-invalid': $v.create.generic_name.$error || errors.generic_name,
                            'is-valid': !$v.create.generic_name.$invalid && !errors.generic_name,
                          }"
                                   id="field-11"
                               />
                           </div>
                           <div v-if="!$v.create.generic_name.minLength" class="invalid-feedback">
                               {{ $t("general.Itmustbeatleast") }}
                               {{ $v.create.generic_name.$params.minLength.min }}
                               {{ $t("general.letters") }}
                           </div>
                           <div v-if="!$v.create.generic_name.maxLength" class="invalid-feedback">
                               {{ $t("general.Itmustbeatmost") }}
                               {{ $v.create.generic_name.$params.maxLength.max }}
                               {{ $t("general.letters") }}
                           </div>
                           <template v-if="errors.generic_name">
                               <ErrorMessage
                                   v-for="(errorMessage, index) in errors.generic_name"
                                   :key="index"
                               >{{ errorMessage }}</ErrorMessage
                               >
                           </template>
                       </div>
                   </div>
                   <div class="col-md-6" v-if="isVisible('generic_name_e')">
                       <div class="form-group">
                           <label for="field-22" class="control-label">
                               {{ getCompanyKey('hms_drugs_generic_name_en') }}
                               <span v-if="isRequired('generic_name_e')" class="text-danger">*</span>
                           </label>
                           <div dir="ltr">
                               <input
                                   type="text"
                                   class="form-control englishInput"
                                   data-create="2"
                                   @keyup="englishValueName(create.generic_name_e)"
                                   v-model="$v.create.generic_name_e.$model"
                                   :class="{
                                        'is-invalid': $v.create.generic_name_e.$error || errors.generic_name_e,
                                        'is-valid': !$v.create.generic_name_e.$invalid && !errors.generic_name_e,
                                    }"
                                   id="field-22"
                               />
                           </div>
                           <div v-if="!$v.create.generic_name_e.minLength" class="invalid-feedback">
                               {{ $t("general.Itmustbeatleast") }}
                               {{ $v.create.generic_name_e.$params.minLength.min }}
                               {{ $t("general.letters") }}
                           </div>
                           <div v-if="!$v.create.generic_name_e.maxLength" class="invalid-feedback">
                               {{ $t("general.Itmustbeatmost") }}
                               {{ $v.create.generic_name_e.$params.maxLength.max }}
                               {{ $t("general.letters") }}
                           </div>
                           <template v-if="errors.generic_name_e">
                               <ErrorMessage
                                   v-for="(errorMessage, index) in errors.generic_name_e"
                                   :key="index"
                               >{{ errorMessage }}</ErrorMessage
                               >
                           </template>
                       </div>
                   </div>

                   <div class="col-md-6" v-if="isVisible('sell_price')">
                       <div class="form-group">
                           <label for="field-sell_price" class="control-label">
                               {{ getCompanyKey('hms_drugs_sell_price') }}
                               <span v-if="isRequired('sell_price')" class="text-danger">*</span>
                           </label>
                           <div >
                                <input class="form-control"
                                   data-create="5"
                                    type="number"
                                    step="0.01"
                                    v-model.number="$v.create.sell_price.$model"
                                    :class="{
                                        'is-invalid': $v.create.sell_price.$error || errors.sell_price,
                                        'is-valid': !$v.create.sell_price.$invalid && !errors.sell_price,
                                    }"
                                   id="field-sell_price" />


                           </div>

                           <template v-if="errors.sell_price">
                               <ErrorMessage
                                   v-for="(errorMessage, index) in errors.sell_price"
                                   :key="index"
                               >{{ errorMessage }}</ErrorMessage
                               >
                           </template>
                       </div>
                   </div>



                   <div class="col-md-12" v-if="isVisible('note')">
                       <div class="form-group">
                           <label for="field-3" class="control-label">
                               {{ getCompanyKey('hms_drugs_note') }}
                               <span v-if="isRequired('note')" class="text-danger">*</span>
                           </label>
                           <div >
                                <textarea class="form-control "
                                   data-create="3"
                                   v-model="$v.create.note.$model"
                                   :class="{
                                        'is-invalid': $v.create.note.$error || errors.note,
                                        'is-valid': !$v.create.note.$invalid && !errors.note,
                                    }"
                                   id="field-3"
                                    cols="30" rows="5">

                                </textarea>

                           </div>

                           <template v-if="errors.note">
                               <ErrorMessage
                                   v-for="(errorMessage, index) in errors.note"
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
   </div>
</template>

<script>
import ErrorMessage from "../../../../components/widgets/errorMessage.vue";
import loader from "../../../../components/general/loader";
import {maxLength, minLength, requiredIf,minValue,maxValue} from "vuelidate/lib/validators";
import adminApi from "../../../../api/adminAxios";
import {arabicValue, englishValue} from "../../../../helper/langTransform";
import transMixinComp from "../../../../helper/mixin/translation-comp-mixin";
import successError from "../../../../helper/mixin/success&error";

export default {
    name: "category",
    components: {
        ErrorMessage,
        loader,
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/h_m_s/drugs'}
    },
    data() {
        return {
            fields: [],
            isCustom: false,
            isLoader: false,
            company_id:null,
            create: {
                trade_name: "",
                trade_name_e: "",
                generic_name: "",
                generic_name_e: "",
                sell_price: 0,
                note: "",
            },
            errors: {},
            is_disabled: false,
        };
    },
    validations: {
        create: {
            trade_name: { required: requiredIf(function (model) {
                    return this.isRequired("trade_name");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            trade_name_e: { required: requiredIf(function (model) {
                    return this.isRequired("trade_name_e");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            generic_name: { required: requiredIf(function (model) {
                    return this.isRequired("generic_name");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            generic_name_e: { required: requiredIf(function (model) {
                    return this.isRequired("generic_name_e");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            sell_price: { required: requiredIf(function (model) {
                    return this.isRequired("sell_price");
            }), minValue: minValue(.00)},
            note: {},
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        getCustomTableFields() {
            this.isCustom = true;
            adminApi
                .get(`/customTable/table-columns/h_m_s_drugs`)
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
                trade_name: "",
                trade_name_e: "",
                generic_name: "",
                generic_name_e: "",
                sell_price: 0,
                note: "",
            };
            this.errors = {};
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        resetModal() {
            this.defaultData();
            setTimeout( () => {
                if(this.type != 'edit'){
                    if(!this.isPage)  this.getCustomTableFields();
                }else {
                    if(this.idObjEdit.dataObj){
                        let drugs = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.trade_name = drugs.trade_name;
                        this.create.trade_name_e = drugs.trade_name_e;
                        this.create.generic_name = drugs.generic_name;
                        this.create.generic_name_e = drugs.generic_name_e;
                        this.create.sell_price = drugs.sell_price;
                        this.create.note = drugs.note;
                    }
                }
            },50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            if (!this.create.generic_name) {
                this.create.generic_name = this.create.generic_name;
            }
            if (!this.create.generic_name_e) {
                this.create.generic_name_e = this.create.generic_name;
            }
            if (!this.create.trade_name) {
                this.create.trade_name = this.create.trade_name;
            }
            if (!this.create.trade_name_e) {
                this.create.trade_name_e = this.create.trade_name;
            }
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                if(this.type != 'edit'){
                    adminApi
                        .post(this.url, {...this.create,company_id:this.company_id})
                        .then((res) => {
                            this.is_disabled = true;
                            if(!this.isPage)
                                this.$emit("created");
                            else
                                this.$emit("getDataTable");

                            setTimeout(() => {
                                this.successFun('Addedsuccessfully');
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
                }else {
                    adminApi
                        .put(`${this.url}/${this.idObjEdit.idEdit}`, {
                            ...this.create,
                            company_id: this.$store.getters["auth/company_id"],
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
            this.create.name = arabicValue(txt);
        },
        englishValueName(txt){
            this.create.name_e = englishValue(txt);
        }
    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
