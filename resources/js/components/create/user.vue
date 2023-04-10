<script>
import adminApi from "../../api/adminAxios";
import Switches from "vue-switches";
import {required, minLength, maxLength, email, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../components/widgets/errorMessage";
import loader from "../../components/loader";
import Multiselect from "vue-multiselect";
import employee from "../../components/create/employee.vue";
import { formatDateOnly } from "../../helper/startDate";
import transMixinComp from "../../helper/translation-comp-mixin";
import {arabicValue, englishValue} from "../../helper/langTransform";

/**
 * Advanced Table component
 */

export default {
  components: {
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
    employee,
  },
  mixins: [transMixinComp],
  data() {
    return {
        fields: [],
      employees: [],
      isLoader: false,
      create: {
        name: "",
        name_e: "",
        email: "",
        password: "",
        employee_id: null,
        is_active: "active",
      },
      edit: {
        name: "",
        name_e: "",
        email: "",
        password: "",
        employee_id: null,
        is_active: "active",
        old_media: [],
      },
      errors: {},
      isCheckAll: false,
      checkAll: [],
      images: [],
      media: {},
      user_id: null,
      saveImageName: [],
      showPhoto: "./images/img-1.png",
      idDelete: null,
      Tooltip: "",
      mouseEnter: null,
    };
  },
  mounted() {
      this.getCustomTableFields();
    this.company_id = this.$store.getters["auth/company_id"];
  },
    props: {
        id: {
            default: "user-create",
        },
        companyKeys:{
            default:[]
        },
        defaultsKeys:{
            default:[]
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
    //     if (ew == 32) return false;
    //     if (48 <= ew && ew <= 57) return true;
    //     if (65 <= ew && ew <= 90) return false;
    //     if (97 <= ew && ew <= 122) return false;
    //     return true;
    //   });
    // });
  },
  validations() {
    return {
      create: {
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }) , minLength: minLength(2), maxLength: maxLength(100) },
            name_e: { required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }) , minLength: minLength(2), maxLength: maxLength(100) },
            email: { required: requiredIf(function (model) {
                    return this.isRequired("email");
                }) , email },
            password: { required: requiredIf(function (model) {
                    return this.isRequired("password");
                }) , minLength: minLength(8) },
            is_active: { required: requiredIf(function (model) {
                    return this.isRequired("is_active");
                })  },
            media: {},
        },
    };
  },
  methods: {
      getCustomTableFields() {
          adminApi
              .get(`/customTable/table-columns/general_users`)
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
    resetModalHidden() {
      this.create = {
        name: "",
        name_e: "",
        email: "",
        password: "",
        employee_id: null,
        is_active: "active",
        media: null,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.images = [];
      this.user_id = null;
      this.errors = {};
      this.employees = [];
      this.$bvModal.hide(this.id);
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      await this.getEmployees();
      this.create = {
        name: "",
        name_e: "",
        email: "",
        password: "",
        employee_id: null,
        is_active: "active",
      };
      this.showPhoto = "./images/img-1.png";
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.user_id = null;
      this.media = {};
      this.images = [];
      this.errors = {};
    },
    /**
     *  create workflow
     */
    resetForm() {
      this.create = {
        name: "",
        name_e: "",
        email: "",
        password: "",
        employee_id: null,
        is_active: "active",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.user_id = null;
      this.media = {};
      this.images = [];
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
      }
      if (true) {
        this.isLoader = true;
        this.errors = {};

        adminApi
          .post(`/users`, {...this.create,company_id:this.company_id})
          .then((res) => {
            this.user_id = res.data.data.id;
            this.$emit("created");
            setTimeout(() => {
              Swal.fire({
                icon: "success",
                text: `${this.$t("general.Addedsuccessfully")}`,
                showConfirmButton: false,
                timer: 1500,
              });
            }, 200);
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
    async getEmployees() {
      await adminApi
        .get(`/employees`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضف موظف", name_e: "Add Employee" });
          this.employees = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    moveInput(tag, c, index) {
      document.querySelector(`${tag}[data-${c}='${index}']`).focus();
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
                .put(`/users/${this.user_id}`, {
                  old_media,
                  media: new_media,
                })
                .then((res) => {
                  this.images = res.data.data.media ? res.data.data.media : [];
                  if (this.images && this.images.length > 0) {
                    this.showPhoto = this.images[this.images.length - 1].webp;
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
                    .put(`/users/${this.user_id}`, {
                      old_media,
                      media: new_media,
                    })
                    .then((res) => {
                      this.images = res.data.data.media ? res.data.data.media : [];
                      if (this.images && this.images.length > 0) {
                        this.showPhoto = this.images[this.images.length - 1].webp;
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
        .put(`/users/${this.user_id}`, { old_media })
        .then((res) => {
          this.images = res.data.data.media ? res.data.data.media : [];
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
    /**
     *  end Image ceate
     */
    showEmployeeModal() {
      if (this.create.employee_id == 0) {
        this.$bvModal.show("employee-create");
        this.create.employee_id = null;
      }
    },
    showEmployeeModalEdit() {
      if (this.edit.employee_id == 0) {
        this.$bvModal.show("employee-create");
        this.edit.employee_id = null;
      }
    },
    formatDate(value) {
      return formatDateOnly(value);
    },
    arabicValue(txt){
      this.create.name = arabicValue(txt);
    },

    englishValue(txt){
      this.create.name_e = englishValue(txt);
    }
  },
};
</script>

<template>
  <div>
    <employee :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getEmployees" />

    <!--  create   -->
    <b-modal
          :id="id"
          :title="getCompanyKey('user_create_form')"
          title-class="font-18"
          dialog-class="modal-full-width"
          body-class="workflow"
          :hide-footer="true"
          @show="resetModal"
          @hidden="resetModalHidden"
      >
          <form>
              <div class="card">
                  <div class="card-body">
                      <div class="mt-1 d-flex justify-content-end">
                          <!-- Emulate built in modal footer ok and cancel button actions -->
                          <b-button
                              variant="success"
                              :disabled="!user_id"
                              @click.prevent="resetForm"
                              type="button"
                              :class="['font-weight-bold px-2', user_id ? 'mx-2' : '']"
                          >
                              {{ $t("general.AddNewRecord") }}
                          </b-button>

                          <template v-if="!user_id">
                              <b-button
                                  variant="success"
                                  type="button"
                                  class="mx-1 font-weight-bold px-3"
                                  v-if="!isLoader"
                                  @click.prevent="AddSubmit"
                              >
                                  {{ $t("general.Save") }}
                              </b-button>

                              <b-button variant="success" class="mx-1" disabled v-else>
                                  <b-spinner small></b-spinner>
                                  <span class="sr-only">{{ $t("login.Loading") }}...</span>
                              </b-button>
                          </template>

                          <b-button
                              variant="danger"
                              class="font-weight-bold"
                              type="button"
                              @click.prevent="resetModalHidden"
                          >
                              {{ $t("general.Cancel") }}
                          </b-button>
                      </div>
                  </div>
                  <b-tabs nav-class="nav-tabs nav-bordered">
                      <b-tab :title="$t('general.DataEntry')" active>
                          <div class="col-lg-6">
                              <div class="row">
                                  <div class="col-md-12" v-if="isVisible('employee_id')">
                                      <div class="form-group">
                                          <label
                                          >{{ getCompanyKey('employee') }}
                                              <span v-if="isRequired('employee_id')" class="text-danger">*</span></label
                                          >

                                          <multiselect
                                              @input="showEmployeeModal"
                                              v-model="create.employee_id"
                                              :options="employees.map((type) => type.id)"
                                              :custom-label="
                                  (opt) =>
                                    $i18n.locale == 'ar'
                                      ? employees.find((x) => x.id == opt).name
                                      : employees.find((x) => x.id == opt).name_e
                                "
                                          >
                                          </multiselect>
                                      </div>
                                  </div>
                                  <div class="col-md-6" v-if="isVisible('email')">
                                      <div class="form-group">
                                          <label for="field-15" class="control-label">
                                              {{ getCompanyKey("user_email") }}
                                              <span v-if="isRequired('email')" class="text-danger">*</span>
                                          </label>
                                          <input
                                              type="text"
                                              class="form-control"
                                              data-create="1"
                                              @keypress.enter="moveInput('input', 'create', 2)"
                                              v-model="$v.create.email.$model"
                                              :class="{
                                  'is-invalid': $v.create.email.$error || errors.email,
                                  'is-valid': !$v.create.email.$invalid && !errors.email,
                                }"
                                              id="field-1s5"
                                          />
                                          <div v-if="!$v.create.email.email" class="invalid-feedback">
                                              {{ $t("general.notValidEmail") }}
                                          </div>
                                          <template v-if="errors.email">
                                              <ErrorMessage
                                                  v-for="(errorMessage, index) in errors.email"
                                                  :key="index"
                                              >{{ errorMessage }}</ErrorMessage
                                              >
                                          </template>
                                      </div>
                                  </div>
                                  <div class="col-md-6" v-if="isVisible('password')">
                                      <div class="form-group">
                                          <label for="field-15" class="control-label">
                                              {{ getCompanyKey('user_password') }}
                                              <span v-if="isRequired('password')" class="text-danger">*</span>
                                          </label>
                                          <input
                                              type="text"
                                              class="form-control"
                                              data-create="1"
                                              @keypress.enter="moveInput('input', 'create', 2)"
                                              v-model="$v.create.password.$model"
                                              :class="{
                                  'is-invalid':
                                    $v.create.password.$error || errors.password,
                                  'is-valid':
                                    !$v.create.password.$invalid && !errors.password,
                                }"
                                              id="field-15"
                                          />
                                          <div
                                              v-if="!$v.create.password.minLength"
                                              class="invalid-feedback"
                                          >
                                              {{ $t("general.Itmustbeatleast") }}
                                              {{ $v.create.password.$params.minLength.min }}
                                              {{ $t("general.letters") }}
                                          </div>
                                          <template v-if="errors.password">
                                              <ErrorMessage
                                                  v-for="(errorMessage, index) in errors.password"
                                                  :key="index"
                                              >{{ errorMessage }}</ErrorMessage
                                              >
                                          </template>
                                      </div>
                                  </div>
                                  <div class="col-md-6" v-if="isVisible('name')">
                                      <div class="form-group">
                                          <label for="field-1" class="control-label">
                                              {{ getCompanyKey('user_name_ar') }}
                                              <span v-if="isRequired('name')" class="text-danger">*</span>
                                          </label>
                                          <div dir="rtl">
                                              <input
                                                  type="text"
                                                  class="form-control"
                                                  data-create="1"
                                                  @keyup="arabicValueName(create.name)"
                                                  v-model="$v.create.name.$model"
                                                  :class="{
                                    'is-invalid': $v.create.name.$error || errors.name,
                                    'is-valid': !$v.create.name.$invalid && !errors.name,
                                  }"
                                                  id="field-1"
                                              />
                                          </div>
                                          <div
                                              v-if="!$v.create.name.minLength"
                                              class="invalid-feedback"
                                          >
                                              {{ $t("general.Itmustbeatleast") }}
                                              {{ $v.create.name.$params.minLength.min }}
                                              {{ $t("general.letters") }}
                                          </div>
                                          <div
                                              v-if="!$v.create.name.maxLength"
                                              class="invalid-feedback"
                                          >
                                              {{ $t("general.Itmustbeatmost") }}
                                              {{ $v.create.name.$params.maxLength.max }}
                                              {{ $t("general.letters") }}
                                          </div>

                                          <template v-if="errors.name">
                                              <ErrorMessage
                                                  v-for="(errorMessage, index) in errors.name"
                                                  :key="index"
                                              >{{ errorMessage }}
                                              </ErrorMessage>
                                          </template>
                                      </div>
                                  </div>
                                  <div class="col-md-6" v-if="isVisible('name_e')">
                                      <div class="form-group">
                                          <label for="field-2" class="control-label">
                                              {{ getCompanyKey('user_name_en') }}
                                              <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                          </label>
                                          <div dir="ltr">
                                              <input
                                                  type="text"
                                                  class="form-control englishInput"
                                                  data-create="2"
                                                  @keyup="englishValueName(create.name_e)"
                                                  v-model="$v.create.name_e.$model"
                                                  :class="{
                                    'is-invalid':
                                      $v.create.name_e.$error || errors.name_e,
                                    'is-valid':
                                      !$v.create.name_e.$invalid && !errors.name_e,
                                  }"
                                                  id="field-2"
                                              />
                                          </div>
                                          <div
                                              v-if="!$v.create.name_e.minLength"
                                              class="invalid-feedback"
                                          >
                                              {{ $t("general.Itmustbeatleast") }}
                                              {{ $v.create.name_e.$params.minLength.min }}
                                              {{ $t("general.letters") }}
                                          </div>
                                          <div
                                              v-if="!$v.create.name_e.maxLength"
                                              class="invalid-feedback"
                                          >
                                              {{ $t("general.Itmustbeatmost") }}
                                              {{ $v.create.name_e.$params.maxLength.max }}
                                              {{ $t("general.letters") }}
                                          </div>
                                          <template v-if="errors.name_e">
                                              <ErrorMessage
                                                  v-for="(errorMessage, index) in errors.name_e"
                                                  :key="index"
                                              >{{ errorMessage }}
                                              </ErrorMessage>
                                          </template>
                                      </div>
                                  </div>
                                  <div class="col-md-6" v-if="isVisible('is_active')">
                                      <div class="form-group">
                                          <label class="mr-2">
                                              {{ getCompanyKey('user_status') }}
                                              <span v-if="isRequired('is_active')" class="text-danger">*</span>
                                          </label>
                                          <b-form-group
                                              :class="{
                                  'is-invalid':
                                    $v.create.is_active.$error || errors.is_active,
                                  'is-valid':
                                    !$v.create.is_active.$invalid && !errors.is_active,
                                }"
                                          >
                                              <b-form-radio
                                                  class="d-inline-block"
                                                  v-model="$v.create.is_active.$model"
                                                  name="some-radios"
                                                  value="active"
                                              >{{ $t("general.Active") }}</b-form-radio
                                              >
                                              <b-form-radio
                                                  class="d-inline-block m-1"
                                                  v-model="$v.create.is_active.$model"
                                                  name="some-radios"
                                                  value="inactive"
                                              >{{ $t("general.Inactive") }}</b-form-radio
                                              >
                                          </b-form-group>
                                          <template v-if="errors.is_active">
                                              <ErrorMessage
                                                  v-for="(errorMessage, index) in errors.is_active"
                                                  :key="index"
                                              >{{ errorMessage }}
                                              </ErrorMessage>
                                          </template>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </b-tab>
                      <b-tab :disabled="!user_id" :title="$t('general.ImageUploads')">
                          <div class="row">
                              <input
                                  accept="image/png, image/gif, image/jpeg, image/jpg"
                                  type="file"
                                  id="uploadImageCreate"
                                  @change.prevent="onImageChanged"
                                  class="input-file-upload position-absolute"
                                  :class="[
                            'd-none',
                            {
                              'is-invalid': $v.create.media.$error || errors.media,
                              'is-valid': !$v.create.media.$invalid && !errors.media,
                            },
                          ]"
                              />
                              <div class="col-md-8 my-1">
                                  <!-- file upload -->
                                  <div
                                      class="row align-content-between"
                                      style="width: 100%; height: 100%"
                                  >
                                      <div class="col-12">
                                          <div class="d-flex flex-wrap">
                                              <div
                                                  :class="[
                                    'dropzone-previews col-4 position-relative mb-2',
                                  ]"
                                                  v-for="(photo, index) in images"
                                                  :key="photo.id"
                                              >
                                                  <div
                                                      :class="[
                                      'card mb-0 shadow-none border',
                                      images.length - 1 == index ? 'bg-primary' : '',
                                    ]"
                                                  >
                                                      <div class="p-2">
                                                          <div class="row align-items-center">
                                                              <div
                                                                  class="col-auto"
                                                                  @click="showPhoto = photo.webp"
                                                              >
                                                                  <img
                                                                      data-dz-thumbnail
                                                                      :src="photo.webp"
                                                                      class="avatar-sm rounded bg-light"
                                                                      @error="src = './images/img-1.png'"
                                                                  />
                                                              </div>
                                                              <div class="col pl-0">
                                                                  <a
                                                                      href="javascript:void(0);"
                                                                      :class="[
                                              'font-weight-bold',
                                              images.length - 1 == index
                                                ? 'text-white'
                                                : 'text-muted',
                                            ]"
                                                                      data-dz-name
                                                                  >
                                                                      {{ photo.name }}
                                                                  </a>
                                                              </div>
                                                              <!-- Button -->
                                                              <a
                                                                  href="javascript:void(0);"
                                                                  :class="[
                                            'btn-danger dropzone-close',
                                            $i18n.locale == 'ar'
                                              ? 'dropzone-close-rtl'
                                              : '',
                                          ]"
                                                                  data-dz-remove
                                                                  @click.prevent="
                                            deleteImageCreate(photo.id, index)
                                          "
                                                              >
                                                                  <i class="fe-x"></i>
                                                              </a>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="footer-image col-12">
                                          <b-button
                                              @click="changePhoto"
                                              variant="success"
                                              type="button"
                                              class="mx-1 font-weight-bold px-3"
                                              v-if="!isLoader"
                                          >
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
                                      <img
                                          :src="showPhoto"
                                          class="img-thumbnail"
                                          @error="src = './images/img-1.png'"
                                      />
                                  </div>
                              </div>
                          </div>
                      </b-tab>
                  </b-tabs>
              </div>
          </form>
      </b-modal>
    <!--  /create   -->
  </div>
</template>

<style lang="scss">
.myUl-workflow {
  ul,
  #myUL {
    list-style-type: none;
  }
  #myUL {
    margin: 0;
    padding: 0;
    span {
      i {
        font-size: 20px;
        position: relative;
        top: 3px;
        color: black;
      }
      span:hover,
      i:hover {
        cursor: pointer;
      }
    }
  }
  .nested {
    display: block;
  }
  .active {
    color: #1abc9c;
  }
  &.rtl {
    #myUL {
      .without-children {
        padding-right: 10px;
      }
      .nested {
        padding-right: 40px;
      }
    }
  }
  &.ltr {
    #myUL {
      .without-children {
        padding-left: 10px;
      }
    }
  }
}
.modal-dialog .card {
  margin: 0 !important;
}
.workflow.modal-body {
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
</style>
