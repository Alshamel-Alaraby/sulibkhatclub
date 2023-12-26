<template>
    <div>
        <ModalBranch
            :tables="[]"
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            id="addBranchFromRoom"
            :isPage="false"
            type="create"
            :isPermission="isPermission"
            @created="getBranch"
        />
        <!--  create   -->
        <b-modal :id="id"
            :title="type != 'edit' ? getCompanyKey('hms_rooms_create_form') : getCompanyKey('hms_rooms_edit_form')"
            title-class="font-18" body-class="p-4 " size="lg" :hide-footer="true" @show="resetModal"
            @hidden="resetModalHidden">
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
                <div>
                    <b-tabs nav-class="nav-tabs nav-bordered w-100">

                        <b-tab :title="$t('general.DataEntry')"  active>
                            <div class="row">
                                <div class="col-md-6" v-if="isVisible('name')">
                                <div class="form-group">
                                    <label for="field-1" class="control-label">
                                        {{ getCompanyKey('hms_rooms_name_ar') }}
                                        <span v-if="isRequired('name')" class="text-danger">*</span>
                                    </label>
                                    <div dir="rtl">
                                        <input type="text" class="form-control arabicInput"
                                            @keyup="arabicValueName(create.name)" v-model="$v.create.name.$model" :class="{
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
                                        <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index">{{
                                            errorMessage
                                        }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-6" v-if="isVisible('name_e')">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">
                                        {{ getCompanyKey('hms_rooms_name_en') }}
                                        <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                    </label>
                                    <div dir="ltr">
                                        <input type="text" class="form-control englishInput" data-create="2"
                                            @keyup="englishValueName(create.name_e)" v-model="$v.create.name_e.$model"
                                            :class="{
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
                                        <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index">{{
                                            errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-6" v-if="isVisible('rooms_category_id')">
                                <div class="form-group">
                                    <label>
                                        {{ getCompanyKey("hms_rooms_category") }}
                                    </label>
                                    <multiselect v-model="create.rooms_category_id" @select="select_room_category"
                                        :options="rooms_categories.map((type) => type.id)" :custom-label="room_category_label"
                                        :class="{ 'is-invalid': errors.rooms_category_id || $v.create.rooms_category_id.$error }">
                                    </multiselect>
                                    <div v-if="$v.create.rooms_category_id.$error" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.rooms_category_id">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.rooms_category_id"
                                            :key="index">{{
                                                errorMessage
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
                                    <multiselect @input="showBranchModal"
                                    v-model="create.branch_id" :options="branches.map((type) => type.id)"
                                        :custom-label="(opt) => branches.find((x) => x.id == opt) ?
                                            $i18n.locale == 'ar'
                                                ? branches.find((x) => x.id == opt).name
                                                : branches.find((x) => x.id == opt).name_e : null"
                                        :class="{ 'is-invalid': errors.branch_id || $v.create.branch_id.$error }">
                                    </multiselect>
                                    <div v-if="$v.create.branch_id.$error" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.branch_id">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.branch_id" :key="index">{{
                                            errorMessage
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
                                    <div>
                                        <textarea class="form-control " data-create="3" v-model="$v.create.note.$model"
                                            :class="{
                                                'is-invalid': $v.create.note.$error || errors.note,
                                                'is-valid': !$v.create.note.$invalid && !errors.note,
                                            }" id="field-3" cols="30" rows="5">

                                    </textarea>

                                    </div>

                                    <template v-if="errors.note">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.note" :key="index">{{
                                            errorMessage
                                        }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            </div>


                        </b-tab>



                        <b-tab :title="$t('general.Time Schedule')" :disabled="!check_add_schedule">

                            <div class="card shadow mb-4 col-12 p-2">
                                <div class="card-header d-flex justify-content-between">
                                    <h4>{{ getCompanyKey('hms_doctors_schedule_times') }}</h4>
                                    <button @click.prevent="add_new_time" class="btn btn-primary me-2">
                                        <i class="fa fa-plus"></i>
                                        {{ $t('general.AddNewTime') }}
                                    </button>
                                </div>

                                <div class="card-body">

                                    <div class="row col-12">
                                        <div class="col-md-3">
                                            <label>
                                                {{ getCompanyKey("hms_doctors_day") }}
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>
                                                {{ getCompanyKey("hms_doctors_from") }}
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>
                                                {{ getCompanyKey("hms_doctors_to") }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row col-12" v-for="(time, index) in create.schedule_times" :key="index">


                                        <div class="col-md-3">
                                            <div class="form-group">

                                                <multiselect v-model="create.schedule_times[index].day_id" placeholder=""
                                                    selectLabel="" deselectLabel="" :options="days.map((type) => type.id)"
                                                    :custom-label="(opt) => days.find((x) => x.id == opt) ?
                                                        $i18n.locale == 'ar'
                                                            ? days.find((x) => x.id == opt).name
                                                            : days.find((x) => x.id == opt).name_e : null">
                                                </multiselect>
                                                <template v-if="errors && errors[`schedule_times.${index}.day_id`]">
                                                    <ErrorMessage
                                                        v-for="(errorMessage, index) in errors[`schedule_times.${index}.day_id`]"
                                                        :key="index">{{ errorMessage
                                                        }}
                                                    </ErrorMessage>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="col-md-3">

                                            <input type="time" class="form-control" v-model="create.schedule_times[index].from">
                                            <template v-if="errors && errors[`schedule_times.${index}.from`]">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors[`schedule_times.${index}.from`]"
                                                    :key="index">{{ errorMessage
                                                    }}
                                                </ErrorMessage>
                                            </template>
                                        </div>

                                        <div class="col-md-3">

                                            <input type="time" class="form-control" v-model="create.schedule_times[index].to">
                                            <template v-if="errors && errors[`schedule_times.${index}.to`]">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors[`schedule_times.${index}.to`]"
                                                    :key="index">{{ errorMessage
                                                    }}
                                                </ErrorMessage>
                                            </template>

                                        </div>

                                        <div class="col-md-2 d-flex  justify-content-center align-items-center">

                                            <button @click.prevent="delete_time(index)" class="btn btn-danger mx-2 col-6">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                        </div>

                                        <template v-if="errors && errors[`schedule_times.${index}`]">
                                            <div class="mx-s">
                                                <ErrorMessage v-for="(errorMessage, index) in errors[`schedule_times.${index}`]"
                                                    :key="index">{{
                                                        $t(`general.` + errorMessage) }}</ErrorMessage>
                                            </div>

                                        </template>


                                    </div>
                                </div>

                            </div>

                        </b-tab>
                    </b-tabs>
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
import ModalBranch from "../../../../components/create/general/branch.vue"

export default {
    name: "rooms_modal",
    components: {
        Multiselect,
        ModalBranch,
        ErrorMessage,
        loader,
    },
    mixins: [transMixinComp, successError],
    props: {
        id: { default: "create", }, companyKeys: { default: [], }, defaultsKeys: { default: [], },
        isPage: { default: true }, isVisiblePage: { default: null }, isRequiredPage: { default: null },
        type: { default: 'create' }, idObjEdit: { default: null }, isPermission: {}, url: { default: '/h_m_s/rooms' }
    },
    data() {
        return {
            fields: [],
            branches: [],
            days: [],
            rooms_categories: [],
            isCustom: false,
            isLoader: false,
            check_add_schedule: false,
            company_id: null,
            create: {
                name: "",
                name_e: "",
                rooms_category_id: null,
                branch_id: null,
                note: "",
                schedule_times: []
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
            branch_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("branch_id");
                })
            },
            rooms_category_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("rooms_category_id");
                })
            },
            schedule_times: [],
            note: {},

        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.get_rooms_categories();
        this.getBranch();
        this.get_days();
    },
    methods: {
        get_days() {
            adminApi.get(`h_m_s/days`).then((res) => {
                this.days = res.data.data
            });
        },
        getBranch() {
            this.isLoader = true;
            adminApi
                .get(`/branches/get-drop-down?company_id=${this.company_id}&notParent=1`)
                .then((res) => {
                    let data = res.data.data
                        if (this.isPermission("create Branch")) {
                            data.unshift({ id: 0, name: "اضف فرع", name_e: "Add Branch" });
                    }
                    this.branches = data
                })
                .catch((err) => {
                    this.errorFun('Error', 'Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        get_rooms_categories() {
            adminApi.get(`h_m_s/rooms_categories?company_id=${this.company_id}&type=children`).then((res) => {
                this.rooms_categories = res.data.data
            });
        },
        showBranchModal() {
            if (this.create.branch_id == 0) {
                this.$bvModal.show("addBranchFromRoom");
                this.create.branch_id = null;
            }
        },
        room_category_label(opt) {
            let label =''
            this.rooms_categories.forEach(element => {
                if(element.id == opt){
                    label=this.$i18n.locale == 'ar'? element.name : element.name_e
                    if(element.parent.does_it_have_schedule){
                        label += ` ( ${this.$i18n.locale == 'ar'? element.parent.name : element.parent.name_e} )`
                    }
                }

            });
            return label;
        },
        add_new_time() {
            this.create.schedule_times.push({
                day_id: null,
                from: '',
                to: '',
            })
        },
        delete_time(index) {
            this.create.schedule_times.splice(index, 1);
        },
        select_room_category() {
            this.rooms_categories.forEach((element) => {
                if (element.id == this.create.rooms_category_id) {
                    this.check_add_schedule = element.parent.does_it_have_schedule ? true : false
                }
            })
        },
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
                schedule_times: []

            };
            this.errors = {};
            this.check_add_schedule = false;
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
                        let schedule_times = []
                        room.schedule_times.forEach(element => {

                            schedule_times.push({
                                day_id: element.day_id,
                                from: element.from,
                                to: element.to,
                            })
                        });
                        this.check_add_schedule = room.rooms_category.parent.does_it_have_schedule ? true:false
                        this.create.schedule_times = schedule_times
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

<style scoped>form {
    position: relative;
}</style>
