<template>
    <div>
        <!--  create   -->
        <b-modal :id="id"
            :title="type != 'edit' ? getCompanyKey('hms_rooms_categories_create_form') : getCompanyKey('hms_rooms_categories_edit_form')"
            title-class="font-18" body-class="p-4 " size="lg" :hide-footer="true" @show="resetModal" @hidden="resetModalHidden">
            <form>
                <loader size="large" v-if="isCustom && !isPage" />
                <div class="mb-3 d-flex justify-content-end">
                    <b-button v-if="type != 'edit'" variant="success" :disabled="!is_disabled" @click.prevent="resetForm"
                        type="button" :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                        {{ $t("general.AddNewRecord") }}
                    </b-button>
                    <template v-if="!is_disabled">
                        <b-button variant="success" type="button" class="mx-1" v-if="!isLoader" @click.prevent="AddSubmit">
                            {{ type != 'edit' ? $t("general.Add") : $t("general.edit") }}
                        </b-button>

                        <b-button variant="success" class="mx-1" disabled v-else>
                            <b-spinner small></b-spinner>
                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                        </b-button>
                    </template>
                    <!-- Emulate built in modal footer ok and cancel button actions -->

                    <b-button variant="danger" type="button" @click.prevent="resetModalHidden">
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-6" v-if="isVisible('name')">
                        <div class="form-group">
                            <label for="field-1" class="control-label">
                                {{ getCompanyKey('hms_rooms_categories_name_ar') }}
                                <span v-if="isRequired('name')" class="text-danger">*</span>
                            </label>
                            <div dir="rtl">
                                <input type="text" class="form-control arabicInput" @keyup="arabicValueName(create.name)"
                                    v-model="$v.create.name.$model" :class="{
                                        'is-invalid': $v.create.name.$error || errors.name,
                                        'is-valid': !$v.create.name.$invalid && !errors.name,
                                    }" id="field-1" />
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
                                <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('name_e')">
                        <div class="form-group">
                            <label for="field-2" class="control-label">
                                {{ getCompanyKey('hms_rooms_categories_name_en') }}
                                <span v-if="isRequired('name_e')" class="text-danger">*</span>
                            </label>
                            <div dir="ltr">
                                <input type="text" class="form-control englishInput" data-create="2"
                                    @keyup="englishValueName(create.name_e)" v-model="$v.create.name_e.$model" :class="{
                                        'is-invalid': $v.create.name_e.$error || errors.name_e,
                                        'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                                    }" id="field-2" />
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
                                <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('rooms_category_id')">
                        <div class="form-group">
                            <label>
                                {{ getCompanyKey("hms_rooms_category") }}
                            </label>
                            <multiselect v-model="create.rooms_category_id" :options="rooms_categories.map((type) => type.id)" :custom-label="(opt) => rooms_categories.find((x) => x.id == opt) ?
                                    $i18n.locale == 'ar'
                                        ? rooms_categories.find((x) => x.id == opt).name
                                        : rooms_categories.find((x) => x.id == opt).name_e : null"
                                :class="{ 'is-invalid': errors.rooms_category_id || $v.create.rooms_category_id.$error }">
                            </multiselect>
                            <div v-if="$v.create.rooms_category_id.$error" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.rooms_category_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.rooms_category_id" :key="index">{{ errorMessage
                                }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('branch_id')">
                        <div class="form-group">
                            <label>
                                {{ getCompanyKey("hms_rooms_branch") }}
                            </label>
                            <multiselect v-model="create.branch_id" :options="branches.map((type) => type.id)" :custom-label="(opt) => branches.find((x) => x.id == opt) ?
                                    $i18n.locale == 'ar'
                                        ? branches.find((x) => x.id == opt).name
                                        : branches.find((x) => x.id == opt).name_e : null"
                                :class="{ 'is-invalid': errors.branch_id || $v.create.branch_id.$error }">
                            </multiselect>
                            <div v-if="$v.create.branch_id.$error" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.branch_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.branch_id" :key="index">{{ errorMessage
                                }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('note')">
                       <div class="form-group">
                           <label for="field-3" class="control-label">
                               {{ getCompanyKey('hms_rooms_note') }}
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
    </div>
</template>

<script>
import ErrorMessage from "../../../../components/widgets/errorMessage.vue";
import loader from "../../../../components/general/loader";
import { maxLength, minLength, requiredIf } from "vuelidate/lib/validators";
import adminApi from "../../../../api/adminAxios";
import { arabicValue, englishValue } from "../../../../helper/langTransform";
import transMixinComp from "../../../../helper/mixin/translation-comp-mixin";
import successError from "../../../../helper/mixin/success&error";
import Multiselect from "vue-multiselect";
export default {
    name: "rooms_modal",
    components: {
        Multiselect,
        ErrorMessage,
        loader,
    },
    mixins: [transMixinComp, successError],
    props: {
        id: { default: "create", }, companyKeys: { default: [], }, defaultsKeys: { default: [], },
        isPage: { default: true }, isVisiblePage: { default: null }, isRequiredPage: { default: null },
        type: { default: 'create' }, idObjEdit: { default: null }, isPermission: {}, url: { default: '/h_m_s/rooms' },rooms_categories:{Array,default:[]},branches:{Array,default:[]}
    },
    data() {
        return {
            fields: [],
            isCustom: false,
            isLoader: false,
            company_id: null,
            create: {
                name: "",
                name_e: "",
                rooms_category_id: null,
                branch_id: null,
                note: "",
            },
            errors: {},
            is_disabled: false,
        };
    },
    validations: {
        create: {
            name: {
                required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(2), maxLength: maxLength(100)
            },
            name_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(2), maxLength: maxLength(100)
            },
            branch_id:{ required: requiredIf(function (model) {
                    return this.isRequired("branch_id");
            })},
            rooms_category_id:{ required: requiredIf(function (model) {
                    return this.isRequired("rooms_category_id");
            })},
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
                .get(`/customTable/table-columns/h_m_s_rooms`)
                .then((res) => {
                    this.fields = res.data;
                })
                .catch((err) => {
                    this.errorFun('Error', 'Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isCustom = false;
                });
        },
        isVisible(fieldName) {
            if (!this.isPage) {
                let res = this.fields.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_visible == 1 ? true : false;
            } else {
                return this.isVisiblePage(fieldName);
            }
        },
        isRequired(fieldName) {
            if (!this.isPage) {
                let res = this.fields.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_required == 1 ? true : false;
            } else {
                return this.isRequiredPage(fieldName);
            }
        },
        defaultData(edit = null) {
            this.create = {
                name: "",
                name_e: "",
                branch_id: null,
                rooms_category_id: null,
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
            setTimeout(() => {
                if (this.type != 'edit') {
                    if (!this.isPage) this.getCustomTableFields();
                } else {
                    if (this.idObjEdit.dataObj) {
                        let room = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.name = room.name;
                        this.create.name_e = room.name_e;
                        this.create.rooms_category_id = room.rooms_category_id;
                        this.create.branch_id = room.branch_id;
                        this.create.note = room.note;
                    }
                }
            }, 50);
        },
        resetForm() {
            this.defaultData();
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
                this.is_disabled = false;
                if (this.type != 'edit') {
                    adminApi
                        .post(this.url, { ...this.create, company_id: this.company_id })
                        .then((res) => {
                            this.is_disabled = true;
                            if (!this.isPage)
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
                                this.errorFun('Error', 'Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                } else {
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
                                this.errorFun('Error', 'Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }

            }
        },
        arabicValueName(txt) {
            this.create.name = arabicValue(txt);
        },
        englishValueName(txt) {
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
