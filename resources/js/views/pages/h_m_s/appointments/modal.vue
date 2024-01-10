<template>
    <div>
        <ModalDoctor
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            id="addFromDoctorAppointmentModal"
            :isPage="false"
            type="create"
            :isPermission="isPermission"
            @created="get_rooms_and_doctors_by_branch"
        />
        <ModalRoom
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            id="addRoomFromAppointment"
            :isPage="false"
            type="create"
            :isPermission="isPermission"
            @created="get_rooms_and_doctors_by_branch"
        />
        <!--  create   -->
        <b-modal :id="id"
            :title="type != 'edit' ? getCompanyKey('hms_appointments_create_form') : getCompanyKey('hms_appointments_edit_form')"
            title-class="font-18" size="xl" body-class="p-4 " :hide-footer="true" @show="resetModal"
            @hidden="resetModalHidden">
            <form>
                <loader size="large" v-if="isLoader" />
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
                    <div class="col-md-5 row">


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    {{ getCompanyKey("hms_appointments_branch") }}
                                </label>
                                <multiselect @input="showBranchModal" v-model="create.branch_id" placeholder="" selectLabel="" deselectLabel=""
                                    :options="branches.map((type) => type.id)" @select="get_rooms_and_doctors_by_branch()"
                                    :custom-label="(opt) => branches.find((x) => x.id == opt) ?
                                        $i18n.locale == 'ar'
                                            ? branches.find((x) => x.id == opt).name
                                            : branches.find((x) => x.id == opt).name_e : null">
                                </multiselect>

                                <div v-if="$v.create.branch_id.$error" class="invalid-feedback">
                                    {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors && errors.branch_id">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.branch_id" :key="index">{{
                                        errorMessage
                                    }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">

                                <label>
                                    {{ getCompanyKey("hms_appointments_room") }}
                                </label>
                                <input type="text" :value="$i18n.locale == 'ar' ? create.room.name : create.room.name_e"
                                    class="form-control"
                                    v-if="create.room_id && Object.keys(create.room ?? []).length && rooms.length == 0"
                                    disabled>
                                <template v-else>
                                    <multiselect @input="showRoomModal" v-model="create.room_id" placeholder="" selectLabel="" deselectLabel=""
                                        @select="get_available_times(); get_rooms_and_doctors_by_branch()"
                                        :options="rooms.map((type) => type.id)" :custom-label="(opt) => rooms.find((x) => x.id == opt) ?
                                            $i18n.locale == 'ar'
                                                ? rooms.find((x) => x.id == opt).name
                                                : rooms.find((x) => x.id == opt).name_e : null">
                                    </multiselect>
                                </template>

                                <div v-if="$v.create.room_id.$error" class="invalid-feedback">
                                    {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors && errors.room_id">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.room_id" :key="index">{{
                                        errorMessage
                                    }}
                                    </ErrorMessage>
                                </template>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    {{ getCompanyKey("hms_appointments_doctor") }}
                                </label>
                                <multiselect @input="showDoctorModal" v-model="create.doctor_id" :options="doctors.map((type) => type.id)"
                                    @select="get_available_times(); get_rooms_and_doctors_by_branch()"
                                    :disabled="type == 'edit'" :custom-label="(opt) => doctors.find((x) => x.id == opt) ?
                                        $i18n.locale == 'ar'
                                            ? doctors.find((x) => x.id == opt).name
                                            : doctors.find((x) => x.id == opt).name_e : null"
                                    :class="{ 'is-invalid': errors.doctor_id || $v.create.doctor_id.$error }">
                                </multiselect>
                                <div v-if="$v.create.doctor_id.$error" class="invalid-feedback">
                                    {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.doctor_id">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.doctor_id" :key="index">{{
                                        errorMessage
                                    }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="!patient_details_id">
                            <div class="form-group">
                                <label>
                                    {{ getCompanyKey("hms_appointments_patient") }}
                                </label>
                                <multiselect @input="showPatientModal" v-model="create.patient_id" :options="patients.map((type) => type.id)"
                                    :disabled="type == 'edit'" :custom-label="(opt) => patients.find((x) => x.id == opt) ?
                                        $i18n.locale == 'ar'
                                            ? patients.find((x) => x.id == opt).name
                                            : patients.find((x) => x.id == opt).name_e : null"
                                    :class="{ 'is-invalid': errors.patient_id || $v.create.patient_id.$error }">
                                </multiselect>
                                <div v-if="$v.create.patient_id.$error" class="invalid-feedback">
                                    {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.patient_id">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.patient_id" :key="index">{{
                                        errorMessage
                                    }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    {{ getCompanyKey("hms_appointments_date") }}
                                </label>
                                <date-picker v-model="create.date" type="date" :default-value="create.date"
                                    @change="get_available_times"
                                    :class="{ 'is-invalid': errors.date || $v.create.date.$error }"></date-picker>

                                <div v-if="$v.create.date.$error" class="invalid-feedback">
                                    {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.date">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.date" :key="index">{{
                                        errorMessage
                                    }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="isVisible('from_doctor_id')">
                            <div class="form-group">
                                <label>
                                    {{ getCompanyKey("hms_appointments_from_doctor") }}
                                </label>
                                <multiselect @input="showFromDoctorModal" v-model="create.from_doctor_id" :options="from_doctors.map((type) => type.id)"
                                    :disabled="type == 'edit'" :custom-label="(opt) => from_doctors.find((x) => x.id == opt) ?
                                        $i18n.locale == 'ar'
                                            ? from_doctors.find((x) => x.id == opt).name
                                            : from_doctors.find((x) => x.id == opt).name_e : null"
                                    :class="{ 'is-invalid': errors.from_doctor_id || $v.create.from_doctor_id.$error }">
                                </multiselect>
                                <div v-if="$v.create.from_doctor_id.$error" class="invalid-feedback">
                                    {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.from_doctor_id">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.from_doctor_id" :key="index">{{
                                        errorMessage
                                    }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>

                        <div class="col-md-12 mt-2" v-if="isVisible('reason')">
                            <div class="form-group">
                                <label for="field-3" class="control-label">
                                    {{ getCompanyKey('hms_rooms_reason') }}
                                    <span v-if="isRequired('reason')" class="text-danger">*</span>
                                </label>
                                <div>
                                    <textarea class="form-control " data-create="3" v-model="$v.create.reason.$model"
                                        :class="{
                                            'is-invalid': $v.create.reason.$error || errors.reason,
                                            'is-valid': !$v.create.reason.$invalid && !errors.reason,
                                        }" id="field-3" cols="30" rows="5">

                                    </textarea>

                                </div>

                                <template v-if="errors.reason">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.reason" :key="index">{{
                                        errorMessage }}</ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div>
                                <input class="" type="checkbox" id="sms" v-model="create.sms_notify">
                                <label class="form-check-label" for="sms">
                                    {{ $t('general.Send SMS') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div>
                                <input type="checkbox" id="whatsapp_notify" v-model="create.whatsapp_notify">
                                <label class="form-check-label" for="whatsapp_notify">
                                    {{ $t('general.Send Whatsapp') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div>
                                <input type="checkbox" id="email_notify" v-model="create.email_notify">
                                <label class="form-check-label" for="email_notify">
                                    {{ $t('general.Send email') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <label for="date">{{ $t('general.Available Times') }} </label>
                        <template v-if="errors.time">
                            <ErrorMessage v-for="(errorMessage, index) in errors.time" :key="index">{{
                                errorMessage
                            }}
                            </ErrorMessage>
                        </template>
                        <ErrorMessage v-if="errors.message">{{
                            errors.message
                        }}
                        </ErrorMessage>
                        <div class="row mb-2 myorders alert alert-warning  " v-if="available_times.length != 0">
                            <div class="my-2" style="width: 20%;" v-for="(time, index) in available_times"
                                :key="time.start">
                                <button class="btn btn-secondary btn-sm"
                                    :class="[time.available ? 'btn-danger' : '', time.selected ? 'btn-success' : '']"
                                    @click.prevent="select_time(time, index)">{{ time.start + ' - ' + time.end }}</button>
                            </div>

                        </div>

                        <div class="alert alert-danger text-center" role="alert" id="help-block" v-else>
                            <img src="/images/calendar.png"><br>

                            <b
                                v-if="this.create.doctor_id && this.create.room_id && this.create.date && available_times.length == 0">
                                {{ $t('general.There is no available time for this doctor in this day and this room') }}</b>
                            <b v-else class="text-primary">{{ $t('general.You have to select the doctor, branch, room and date to show available times')}}</b>
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
import { maxLength, minLength, requiredIf, required } from "vuelidate/lib/validators";
import adminApi from "../../../../api/adminAxios";
import { arabicValue, englishValue } from "../../../../helper/langTransform";
import transMixinComp from "../../../../helper/mixin/translation-comp-mixin";
import successError from "../../../../helper/mixin/success&error";
import Multiselect from "vue-multiselect";
import DatePicker from "vue2-datepicker";
import ModalRoom from "../rooms/modal.vue";
import ModalDoctor from "../doctors/modal.vue";

export default {
    name: "appointments_modal",
    components: {
        Multiselect,
        ModalRoom,
        ModalDoctor,
        DatePicker,
        ErrorMessage,
        loader,
    },
    mixins: [transMixinComp, successError],
    props: {
        id: { default: "create", }, companyKeys: { default: [], }, defaultsKeys: { default: [], },
        isPage: { default: true }, isVisiblePage: { default: null }, isRequiredPage: { default: null },
        type: { default: 'create' }, idObjEdit: { default: null }, isPermission: {}, url: { default: '/h_m_s/appointments' }, branches: { Array, default: [] }
        , patients: { Array, default: [] }, patient_details_id: { default: null }
    },
    data() {
        return {
            fields: [],
            available_times: [],
            from_doctors: [],
            doctors: [],
            rooms: [],
            isCustom: false,
            isLoader: false,
            company_id: null,
            create: {
                doctor_id: null,
                from_doctor_id: null,
                patient_id: null,
                room_id: null,
                branch_id: null,
                date: new Date(),
                reason: "",
                sms_notify: 0,
                whatsapp_notify: 0,
                email_notify: 0,
                time: []
            },
            errors: {},
            is_disabled: false,
        };
    },
    validations: {
        create: {
            doctor_id: { required },
            branch_id: { required },
            from_doctor_id: {},
            patient_id: { required },
            room_id: { required },
            date: { required },
            reason: {}
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.get_from_doctors()
    },
    methods: {
        get_from_doctors(){
            adminApi.get(`h_m_s/doctors?drop_down=1&company_id=${this.company_id}}`).then((res) => {
            this.from_doctors = res.data
        });
        },
        showBranchModal() {
            if (this.create.branch_id == 0) {
                this.$bvModal.show("addAppointmentBranch");
                this.create.branch_id = null;
            }
        },
        showDoctorModal() {
            if (this.create.doctor_id == 0) {
                this.$bvModal.show("addFromDoctorAppointmentModal");
                this.create.doctor_id = null;
            }
        },
        showFromDoctorModal() {
            if (this.create.from_doctor_id == 0) {
                this.$bvModal.show("addFromDoctorAppointmentModal");
                this.create.from_doctor_id = null;
            }
        },
        showPatientModal() {
            if (this.create.patient_id == 0) {
                this.$bvModal.show("addPatientFromAppointment");
                this.create.patient_id = null;
            }
        },
        showRoomModal(index) {
            if (this.create.room_id == 0) {
                this.$bvModal.show("addRoomFromAppointment");
                this.create.room_id = null;
            }
        },
        select_time(time, available_times_index) {
            let chech = 0, index;
            this.create.time.forEach((element, i) => {
                if (element.start == time.start && element.end == time.end) {
                    chech = 1
                    index = i
                }
            });
            if (chech) {
                this.create.time.splice(index, 1)
                this.available_times[available_times_index].selected = 0
            }
            else {
                this.create.time.push({ "start": time.start, "end": time.end })
                this.$set(this.available_times[available_times_index], 'selected', 1)
            }
        },
        get_available_times() {
            if (this.create.doctor_id && this.create.room_id && this.create.date) {
                this.isLoader = true;
                this.errors = {};
                this.available_times = []
                adminApi
                    .get(`/h_m_s/doctor_available_times?doctor_id=${this.create.doctor_id}&room_id=${this.create.room_id}&date=${this.formatDateTime(this.create.date)}`)
                    .then((res) => {
                        this.available_times = res.data
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
                this.available_times = []
            }
        },
        get_rooms_and_doctors_by_branch() {
            if(this.create.branch_id)
            adminApi.get(`/h_m_s/rooms?branch_id=${this.create.branch_id}&doctor_id=${this.create.doctor_id ??''}`).then((res) => {
                let data = res.data.data
                    if (this.isPermission("create Room")) {
                        data.unshift({ id: 0, name: "اضف غرفة", name_e: "Add Room" });
                }
                this.rooms = data
            })
            if(this.create.branch_id)
            adminApi.get(`h_m_s/doctors?drop_down=1&branch_id=${this.create.branch_id}&company_id=${this.company_id}&room_id=${this.create.room_id ??''}`).then((res) => {
                let data = res.data
                    if (this.isPermission("create Doctor")) {
                        data.unshift({ id: 0, name: "اضف طبيب", name_e: "Add Doctor" });
                }
                this.doctors = data
            });
        },
        getCustomTableFields() {
            this.isCustom = true;
            adminApi
                .get(`/customTable/table-columns/h_m_s_appointments`)
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
                doctor_id: null,
                from_doctor_id: null,
                patient_id: null,
                room_id: null,
                branch_id: null,
                date: new Date(),
                reason: "",
                sms_notify: 0,
                whatsapp_notify: 0,
                email_notify: 0,
                time: []
            };
            this.rooms = [];
            this.available_times = [];
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
            if (!this.isPage && this.fields.length == 0)
                this.getCustomTableFields();
            // if (this.type != 'edit') {
            // }
        },
        resetForm() {
            this.defaultData();
        },
        formatDateTime(item) {
            return new Date(item.getTime() - (item.getTimezoneOffset() * 60000)).toISOString().substring(0, 10)
        },
        AddSubmit() {
            if (this.patient_details_id)
                this.create.patient_id = this.patient_details_id

            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {

                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                if (this.type != 'edit') {
                    adminApi
                        .post(this.url, { ...this.create, company_id: this.company_id, date: this.formatDateTime(this.create.date) })
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
                }
                else {
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
    }
}
</script>

<style scoped>form {
    position: relative;
}</style>
