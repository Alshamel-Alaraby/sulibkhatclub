<template>
    <div class="w-100">

        <div class="row">
            <div class="col">
                <button type="button" class="btn btn-primary btn-sm my-4 " data-toggle="modal" v-if="createMedicalFiles"
                    data-target="#MedicalFilesModel"><i class="fa fa-plus"></i> {{ $t('general.Add New') }}</button>
            </div>
        </div>


        <div class="row">
            <div class="col-md-4" v-for="file in medical_files" :key="file.id">
                <div class="card">
                    <img src="/images/pdf.jpg" class="card-img-top" v-if="file.file.mime_type.includes('pdf')" style="height: 209px;">
                    <img src="/images/docx.png" class="card-img-top" v-if="file.file.mime_type.includes('docx')" style="height: 209px;">
                    <a class="example-image-link" :href="file.file.url" data-lightbox="example-1"
                        v-if="file.file.mime_type.includes('image')">
                        <img :src="file.file.url" class="card-img-top" style="height: 209px;">
                    </a>
                    <div class="card-body bg-white text-center">
                        <h5 class="card-title">{{ $i18n.locale == 'ar' ? file.title : file.title_e }}</h5>
                        <p class="font-size-12">{{ file.note }}</p>
                        <p class="font-size-11"><label class="badge badge-dark text-white"> <i class="fas fa-calendar"></i> {{ file.created_at }}</label></p>
                        <a :href="file.file.url" class="btn btn-primary btn-sm" download><i class="fa fa-cloud-download-alt"></i></a>
                        <a @click.prevent="delete_medical_files(file.id)" class="btn btn-danger btn-sm" v-if="deleteMedicalFiles"><i
                                class="fa fa-trash"></i></a>
                    </div>
                </div>
            </div>
            <div class="col text-center" v-if="medical_files.length == 0">
                <img src="/images/not-found.svg" width="200" /> <br><br> <b class="text-muted">{{ $t('general.No medical files was found') }}</b>
            </div>

        </div>


        <div id="MedicalFilesModel" class="modal fade" role="dialog">
            <loader size="large" v-if="isLoader" style="z-index:9999;margin:0px -37px;" />

            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h5 class="modal-title text-white" id="exampleModalLabel">{{
                            getCompanyKey('hms_patients_new_medical_file') }}
                        </h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form>
                        <div class="modal-body bg-white">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="field-u-1" class="control-label">
                                            {{ getCompanyKey('hms_patients_new_medical_file_title') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div dir="rtl">
                                            <input type="text" class="form-control arabicInput"
                                                v-model="$v.create.title.$model" :class="{
                                                    'is-invalid':
                                                        $v.create.title.$error ||
                                                        errors.title,

                                                }" />
                                        </div>
                                        <div v-if="!$v.create.title.minLength" class="invalid-feedback">
                                            {{
                                                $t("general.Itmustbeatleast")
                                            }}
                                            {{
                                                $v.create.title.$params.minLength
                                                    .min
                                            }}
                                            {{ $t("general.letters") }}
                                        </div>
                                        <div v-if="!$v.create.title.maxLength" class="invalid-feedback">
                                            {{ $t("general.Itmustbeatmost") }}
                                            {{
                                                $v.create.title.$params.maxLength
                                                    .max
                                            }}
                                            {{ $t("general.letters") }}
                                        </div>
                                        <template v-if="errors.title">
                                            <ErrorMessage v-for="(
                                                  errorMessage, index
                                                ) in errors.title" :key="index">{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="field-u-2" class="control-label">
                                            <span class="text-danger">*</span>
                                            {{ getCompanyKey('hms_patients_new_medical_file_title_e') }}
                                        </label>
                                        <div dir="ltr">
                                            <input type="text" class="form-control englishInput"
                                                v-model="$v.create.title_e.$model" :class="{
                                                    'is-invalid':
                                                        $v.create.title_e.$error ||
                                                        errors.title_e,

                                                }" />
                                        </div>
                                        <div v-if="!$v.create.title_e.minLength" class="invalid-feedback">
                                            {{
                                                $t("general.Itmustbeatleast")
                                            }}
                                            {{
                                                $v.create.title_e.$params
                                                    .minLength.min
                                            }}
                                            {{ $t("general.letters") }}
                                        </div>
                                        <div v-if="!$v.create.title_e.maxLength" class="invalid-feedback">
                                            {{ $t("general.Itmustbeatmost") }}
                                            {{
                                                $v.create.title_e.$params
                                                    .maxLength.max
                                            }}
                                            {{ $t("general.letters") }}
                                        </div>
                                        <template v-if="errors.title_e">
                                            <ErrorMessage v-for="(
                                                  errorMessage, index
                                                ) in errors.title_e" :key="index">{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-6" v-if="isVisible('file')">
                                    <div class="form-group">
                                        <label for="file" class="control-label">
                                            {{ getCompanyKey('hms_patients_new_medical_file_file') }}
                                            <span v-if="isRequired('file')" class="text-danger">*</span>
                                        </label>
                                        <div>
                                            <input type="file" name="file" id="file" class="form-control-file"
                                                @change.prevent="onFileChanged" :class="{
                                                    'is-invalid':
                                                        $v.create.file.$error ||
                                                        errors.file,

                                                }">

                                        </div>

                                        <template v-if="errors.file">
                                            <ErrorMessage v-for="(errorMessage, index) in errors.file" :key="index">{{
                                                errorMessage }}</ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-6" v-if="isVisible('note')">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">
                                            {{ getCompanyKey('hms_patients_new_medical_file_note') }}
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
                                                errorMessage }}</ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer bg-dark">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">{{ $t('general.Cancel')
                            }}</button>
                            <button class="btn btn-primary text-white" type="submit" @click.prevent="save_data">{{
                                $t('general.Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import adminApi from '../../../../api/adminAxios'
import loader from "../../../../components/general/loader";
import { maxLength, minLength, requiredIf } from "vuelidate/lib/validators";
import successError from "../../../../helper/mixin/success&error";
import ErrorMessage from "../../../../components/widgets/errorMessage.vue";
import transMixinComp from "../../../../helper/mixin/translation-comp-mixin";
import Swal from "sweetalert2";

export default {
    name: 'MedicalFiles',
    props: ['patient_id', 'medical_files','createMedicalFiles','deleteMedicalFiles'],
    components: {
        loader, ErrorMessage
    },
    mixins: [successError, transMixinComp],
    data() {
        return {
            fields: [],
            isLoader: false,
            create: {
                title: "",
                title_e: "",
                file: "",
                note: "",
            },
            errors: {},
        };
    },
    validations: {
        create: {
            title: {
                required: requiredIf(function (model) {
                    return this.isRequired("title");
                }), minLength: minLength(2), maxLength: maxLength(100)
            },
            title_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("title_e");
                }), minLength: minLength(2), maxLength: maxLength(100)
            },
            file: {
                required: requiredIf(function (model) {
                    return this.isRequired("file");
                })
            },

            note: {},

        },
    },
    methods: {
        isVisible(fieldName) {
            // let res = this.fields.filter((field) => {
            //     return field.column_name == fieldName;
            // });
            // return res.length > 0 && res[0].is_visible == 1 ? true : false;
            return true;

        },
        isRequired(fieldName) {
            let res = this.fields.filter((field) => {
                return field.column_name == fieldName;
            });
            return res.length > 0 && res[0].is_required == 1 ? true : false;
        },
        async save_data() {
            this.$v.create.$touch();
            this.isLoader = true;
            this.errors = {};
            if (this.$v.create.$invalid) {
                return;
            } else {
                let data = new FormData();
                data.append('title', this.create.title)
                data.append('title_e', this.create.title_e)
                data.append('media', this.create.file)
                data.append('note', this.create.note)
                data.append('patient_id', this.patient_id)

                await adminApi
                    .post('h_m_s/patient_medical_files', data)
                    .then((res) => {
                        this.$emit("created");
                        $('#MedicalFilesModel').modal('hide')
                        this.create = {
                            title: "",
                            title_e: "",
                            file: "",
                            note: "",
                        }
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
            }
            this.isLoader = false;

        },
        async delete_medical_files(id) {
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
                        .delete('h_m_s/patient_medical_files/' + id)
                        .then((res) => {
                            this.$emit("created");
                            setTimeout(() => {
                                this.successFun('Yourrowhasbeendeleted', 'Deleted');
                            }, 500);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error', 'Thereisanerrorinthesystem');
                            }
                        }).finally(() => {
                            this.isLoader = false;
                        })
                }
            })

        },
        onFileChanged(e) {
            const file = e.target.files[0];
            this.create.file = file
        },
    }
}
</script>

<style>
.modal-backdrop.fade.show {
    z-index: 999;
}
.badge {
    padding: 11px;
    font-size: 14px;
    border-radius: 2rem;
}
</style>
