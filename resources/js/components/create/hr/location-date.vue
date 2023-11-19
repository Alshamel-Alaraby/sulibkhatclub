<template>
    <div>
        <!--  create   -->
        <b-modal
            :id="id"
            :title="
                type != 'edit'
                    ? getCompanyKey('location_date_create_form')
                    : getCompanyKey('location_date_create_form')
                "
            title-class="font-18"
            body-class="p-4 "
            :hide-footer="true"
            @show="resetModal"
            @hidden="resetModalHidden"
        >
            <form>
                <loader size="large" v-if="isLoader" />
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
                            type="submit"
                            class="mx-1"
                            v-if="!isLoader"
                            @click.prevent="AddSubmit"
                        >
                            {{ type != "edit" ? $t("general.Add") : $t("general.edit") }}
                        </b-button>
                        <b-button variant="success" class="mx-1" disabled v-else>
                            <b-spinner small></b-spinner>
                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                        </b-button>
                    </template>
                    <b-button
                        @click.prevent="resetModalHidden"
                        variant="danger"
                        type="button"
                    >
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">
                                {{ $t('general.date') }}
                                <span class="text-danger">*</span>
                            </label>
                            <date-picker
                                v-model="create.vdate"
                                type="date"
                                format="YYYY-MM-DD"
                                valueType="format"
                            ></date-picker>
                            <template v-if="errors.vdate">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.vdate"
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
import Switches from "vue-switches";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import {maxLength, minLength, required} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import DatePicker from "vue2-datepicker";
import {formatDateOnly} from "../../../helper/startDate";
import successError from "../../../helper/mixin/success&error";

export default {
    name: "job",
    components: {
        Switches,
        ErrorMessage,
        loader,
        DatePicker,
    },
    mixins: [transMixinComp,successError],
    props: {
        id: { default: "create" },
        companyKeys: { default: [] },
        defaultsKeys: { default: [] },
        isPage: { default: true },
        isVisiblePage: { default: null },
        isRequiredPage: { default: null },
        type: { default: "create" },
        idObjEdit: { default: null },
        isPermission: {},
        url: { default: "/hr/location-dates" },
        location_id: {default: null}
    },
    data() {
        return {
            fields: [],
            isLoader: false,
            company_id:null,
            create: {
                location_id: null,
                public_holiday_header_id: null,
                vdate: ''
            },
            errors: {},
            is_disabled: false,
        };
    },
    validations: {
        create: {
            vdate: { required },
            location_id: {  },
            public_holiday_header_id: {  }
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        defaultData(edit = null){
            this.create = {
                location_id: null,
                public_holiday_header_id: null,
                vdate: this.formatDate(new Date()),
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
            setTimeout( () => {
                if(this.type != 'edit'){
                    if(!this.isPage)  return;
                }else {
                    if(this.idObjEdit.dataObj){
                        let financialYear = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.location_id = financialYear.location_id;
                        this.create.public_holiday_header_id =  financialYear.public_holiday_header_id;
                        this.create.vdate = financialYear.vdate;
                    }
                }
            },50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            this.create.location_id = this.location_id;
            this.create.public_holiday_header_id = this.location_id;
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                if(this.type != 'edit'){
                    adminApi
                        .post(this.url, { ...this.create, company_id: this.company_id })
                        .then((res) => {
                            this.is_disabled = true;
                            if(!this.isPage)
                                this.$emit("created");
                            else
                                this.$emit("getDataTable");
                            setTimeout(() => {
                                this.successFun('Addedsuccessfully');
                            }, 200);

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
                            this.$emit("created");
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
                                this.errorFun('Error','Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false
                        });
                }

            }
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
