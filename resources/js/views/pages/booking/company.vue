<script>
import Layout from "../../layouts/main";
import employee from "../../../components/create/general/employee";

import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {
  required,
  minLength,
  maxLength,
  integer,
  url,
  email,
  requiredIf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import {
  dynamicSortNumber,
  dynamicSortString,
} from "../../../helper/tableSort";
import { formatDateOnly } from "../../../helper/startDate";
import Multiselect from "vue-multiselect";
import permissionGuard from "../../../helper/permission";

// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
import translation from "../../../helper/mixin/translation-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";

/**
 * Advanced Table component
 */

export default {
  page: {
    title: "Booking Company",
    meta: [{ name: "Booking Company", content: "Booking Company" }],
  },
  mixins: [translation],
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Booking Company", "all Booking Company");
    });
  },
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
    employee,
  },
  data() {
    return {
      fields: [],
      per_page: 50,
      search: "",
      debounce: {},
      customersPagination: {},
      customers: [],
      isLoader: false,
      isVaildPhone: false,
      employees: [],
      watsApp: { phone: "" },
      create: {
        name: "",
        email: "",
        employee_id: null,
        name_e: "",
        phone: "",
        contact_person: "",
        contact_phone: "",
        tax_record: "",
        address: "",
        media: [],
        code_country: "",
        type: 0,
        is_supplier: 0,
      },
      edit: {
        name: "",
        email: "",
        name_e: "",
        phone: "",
        contact_person: "",
        contact_phone: "",
        employee_id: null,

        tax_record: "",
        address: "",
        code_country: "",
        type: 0,
        is_supplier: 0,
        old_media: [],
      },
      countryCode: "",
      customer_id: null,
      errors: {},
      isCheckAll: false,
      checkAll: [],
      images: [],
      media: {},
      titleFile: "",
      saveImageName: [],
      showPhoto: "../../../../../images/img-1.png",
      current_page: 1,
      enabled3: true,
      image: "",
      setting: {
        name: true,
        name_e: true,
        phone: true,
        email: true,
        tax_record: true,
        address: true,
        employee: true,
        contact_person: true,
        contact_phone: true,
      },
      is_disabled: false,
      filterSetting: [
        "name",
        "name_e",
        "phone",
        "tax_record",
        "address",
        "contact_person",
        "contact_phone",
      ],
      Tooltip: "",
      idDelete: null,
      mouseEnter: null,
      printLoading: true,
      printObj: {
        id: "printCustomer",
      },
    };
  },
  validations: {
    watsApp: {
      phone: { required },
    },
    create: {
      name: {
        required: requiredIf(function (model) {
          return this.isRequired("name");
        }),
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      name_e: {
        required: requiredIf(function (model) {
          return this.isRequired("name_e");
        }),
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      phone: {
        required: requiredIf(function (model) {
          return this.isRequired("phone");
        }),
        maxLength: maxLength(20),
      },
      employee_id: {
        required: requiredIf(function (model) {
          return this.isRequired("employee_id");
        }),
      },

      email: {
        required: requiredIf(function (model) {
          return this.isRequired("email");
        }),
        email,
      },

      tax_record: {
        required: requiredIf(function (model) {
          return this.isRequired("tax_record");
        }),
        integer,
      },
      address: {
        required: requiredIf(function (model) {
          return this.isRequired("address");
        }),
        maxLength: maxLength(1000),
      },
      contact_person: {
        required: requiredIf(function (model) {
          return this.isRequired("contact_person");
        }),
        maxLength: maxLength(100),
      },
      contact_phone: {
        required: requiredIf(function (model) {
          return this.isRequired("contact_phone");
        }),
        maxLength: maxLength(100),
      },
      media: {},
    },
    edit: {
      name: {
        required: requiredIf(function (model) {
          return this.isRequired("name");
        }),
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      employee_id: {
        required: requiredIf(function (model) {
          return this.isRequired("employee_id");
        }),
      },

      name_e: {
        required: requiredIf(function (model) {
          return this.isRequired("name_e");
        }),
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      email: {
        required: requiredIf(function (model) {
          return this.isRequired("email");
        }),
        email,
      },

      phone: {
        required: requiredIf(function (model) {
          return this.isRequired("phone");
        }),
        maxLength: maxLength(20),
      },
      tax_record: {
        required: requiredIf(function (model) {
          return this.isRequired("tax_record");
        }),
        integer,
      },
      address: {
        required: requiredIf(function (model) {
          return this.isRequired("address");
        }),
        maxLength: maxLength(1000),
      },
      contact_person: {
        required: requiredIf(function (model) {
          return this.isRequired("contact_person");
        }),
        maxLength: maxLength(100),
      },
      contact_phone: {
        required: requiredIf(function (model) {
          return this.isRequired("contact_phone");
        }),
        maxLength: maxLength(100),
      },
      media: {},
    },
    titleFile: { required, minLength: minLength(2), maxLength: maxLength(100) },
  },
  watch: {
    /**
     * watch per_page
     */
    per_page(after, befour) {
      this.getData();
    },
    /**
     * watch search
     */
    search(after, befour) {
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.getData();
      }, 400);
    },
    /**
     * watch check All table
     */
    isCheckAll(after, befour) {
      if (after) {
        this.customers.forEach((el) => {
          if (!this.checkAll.includes(el.id)) {
            this.checkAll.push(el.id);
          }
        });
      } else {
        this.checkAll = [];
      }
    },
  },
  mounted() {
    this.getCustomTableFields();
    this.getData();
    this.$store.dispatch("locationIp/getIp");
  },
  methods: {
    // start whats app
    resetWhatsApp(index) {
      this.watsApp = { phone: "" };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.$bvModal.hide("show-whatup-" + index);
    },
    getLinkWhastapp(photo) {
      this.$v.watsApp.$touch();
      if (this.$v.watsApp.$invalid) {
        return;
      } else {
        var url =
          "https://api.whatsapp.com/send?phone=" +
          this.watsApp.phone +
          "&text=" +
          encodeURIComponent(photo);

        window.open(url);
      }
    },
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
    async getEmployees() {
      await adminApi
        .get(`/employees?customer_handel=1`)
        .then((res) => {
          let l = res.data.data;
          if (this.isPermission("create Employee")) {
            l.unshift({ id: 0, name: "اضاف موظف", name_e: "Add Employee" });
          }
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
    isPermission(item) {
      if (this.$store.state.auth.type == "user") {
        return this.$store.state.auth.permissions.includes(item);
      }
      return true;
    },
    getCustomTableFields() {
      adminApi
        .get(`/customTable/table-columns/general_customers`)
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
    arabicValue(txt) {
      this.create.name = arabicValue(txt);
      this.edit.name = arabicValue(txt);
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
      this.edit.name_e = englishValue(txt);
    },
    /**
     *  start get Data customers && pagination
     */
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      for (let i = 0; i < this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/general-customer?page=${page}&type=0&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.customers = l.data;
          this.customersPagination = l.pagination;
          this.current_page = l.pagination.current_page;
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
    getDataCurrentPage(page = 1) {
      if (
        this.current_page <= this.customersPagination.last_page &&
        this.current_page != this.customersPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/general-customer?page=${page}&type=0&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.customers = l.data;
            this.customersPagination = l.pagination;
            this.current_page = l.pagination.current_page;
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
      }
    },
    /**
     *  end get Data countrie && pagination
     */
    /**
     *  start delete countrie
     */
    deleteCountry(id, index) {
      if (Array.isArray(id)) {
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
              .post(`/general-customer/bulk-delete`, { ids: id })
              .then((res) => {
                this.checkAll = [];
                this.getData();
                Swal.fire({
                  icon: "success",
                  title: `${this.$t("general.Deleted")}`,
                  text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                  showConfirmButton: false,
                  timer: 1500,
                });
              })
              .catch((err) => {
                if (err.response.status == 400) {
                  Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.CantDeleteRelation")}`,
                  });
                  this.getData();
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
      } else {
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
              .delete(`/general-customer/${id}`)
              .then((res) => {
                this.checkAll = [];
                this.getData();
                Swal.fire({
                  icon: "success",
                  title: `${this.$t("general.Deleted")}`,
                  text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                  showConfirmButton: false,
                  timer: 1500,
                });
              })

              .catch((err) => {
                if (err.response.status == 400) {
                  Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.CantDeleteRelation")}`,
                  });
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
    },
    /**
     *  end delete countrie
     */
    /**
     *  reset Modal (create)
     */
    resetModalHidden() {
      this.create = {
        name: "",
        email: "",
        employee_id: null,
        name_e: "",
        phone: "",
        contact_person: "",
        contact_phone: "",
        tax_record: "",
        address: "",
        type: 0,
        is_supplier: 0,
        media: [],
        code_country: "",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.images = [];
      this.is_disabled = false;
      this.$bvModal.hide(`create`);
      this.customer_id = null;
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      this.create = {
        name: "",
        name_e: "",
        email: "",
        employee_id: null,

        phone: "",
        contact_person: "",
        contact_phone: "",
        tax_record: "",
        address: "",
        type: 0,
        is_supplier: 0,
        media: [],
        code_country: "",
      };
      if (this.isVisible("employee_id")) await this.getEmployees();

      this.countryCode = this.$store.getters["locationIp/countryCode"];
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.showPhoto = "../../../../../images/img-1.png";
      this.media = {};
      this.images = [];
      this.customer_id = null;
    },
    /**
     *  create countrie
     */
    resetForm() {
      this.create = {
        email: "",
        employee_id: null,

        name: "",
        name_e: "",
        phone: "",
        contact_person: "",
        contact_phone: "",
        tax_record: "",
        address: "",
        type: 0,
        is_supplier: 0,
        media: [],
        code_country: "",
      };
      this.countryCode = this.$store.getters["locationIp/countryCode"];
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.is_disabled = false;
      this.media = {};
      this.images = [];
      this.customer_id = null;
    },
    AddSubmit() {
      this.create.code_country = this.countryCode;
      if (!this.create.name) {
        this.create.name = this.create.name_e;
      }
      if (!this.create.name_e) {
        this.create.name_e = this.create.name;
      }
      this.create.company_id = JSON.parse(localStorage.getItem("company_id"));
      this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};

        adminApi
          .post(`/general-customer`, {
            ...this.create,
            company_id: this.$store.getters["auth/company_id"],
          })
          .then((res) => {
            this.is_disabled = true;
            this.customer_id = res.data.data.id;
            this.getData();
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
    /**
     *  edit countrie
     */
    editSubmit(id) {
      this.edit.code_country = this.countryCode;

      if (!this.edit.name) {
        this.edit.name = this.edit.name_e;
      }
      if (!this.edit.name_e) {
        this.edit.name_e = this.edit.name;
      }
      this.edit.company_id = JSON.parse(localStorage.getItem("company_id"));
      this.$v.edit.$touch();
      this.images.forEach((e) => {
        this.edit.old_media.push(e.id);
      });

      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        adminApi
          .put(`/general-customer/${id}`, this.edit)
          .then((res) => {
            this.$bvModal.hide(`modal-edit-${id}`);
            this.getData();
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
    /**
     *   show Modal (edit)
     */
    async resetModalEdit(id) {
      this.customer_id = id;
      let build = this.customers.find((e) => id == e.id);
      this.edit.name = build.name;
      this.edit.name_e = build.name_e;
      if (this.isVisible("employee_id")) await this.getEmployees();
      this.edit.employee_id = build.employee_id;
      this.edit.phone = build.phone;
      this.edit.contact_person = build.contact_person;
      this.edit.contact_phone = build.contact_phone;
      this.edit.email = build.email;
      this.edit.tax_record = build.tax_record;
      this.edit.address = build.address;
      this.edit.is_supplier = build.is_supplier;
      this.edit.type = build.type;
      this.countryCode = build.code_country;
      this.images = build.media ?? [];

      if (this.images && this.images.length > 0) {
        this.showPhoto = this.images[this.images.length - 1].webp;
      } else {
        this.showPhoto = "../../../../../images/img-1.png";
      }

      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.errors = {};
      this.edit = {
        name: "",
        email: "",
        employee_id: null,

        name_e: "",
        phone: "",
        contact_person: "",
        contact_phone: "",
        tax_record: "",
        address: "",
        is_supplier: 0,
        type: 0,
        code_country: "",
        old_media: [],
      };
      this.images = [];
      this.customer_id = null;
    },
    /**
     *  start  dynamicSortString
     */
    sortString(value) {
      return dynamicSortString(value);
    },
    sortNumber(value) {
      return dynamicSortNumber(value);
    },
    /**
     *  start  ckeckRow
     */
    checkRow(id) {
      if (!this.checkAll.includes(id)) {
        this.checkAll.push(id);
      } else {
        let index = this.checkAll.indexOf(id);
        this.checkAll.splice(index, 1);
      }
    },
    /**
     *  end  ckeckRow
     */
    /**
     *  start Image ceate
     */
    uploadPhotoTitle() {
      this.titleFile = "";
      this.$bvModal.show(`uploadPhotoTitleCreate`);
      this.errors = {};
    },
    uploadPhotoTitleHidden() {
      this.titleFile = "";
      this.$bvModal.hide(`uploadPhotoTitleCreate`);
      this.errors = {};
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
          formDate.append("media[][media]", this.media);
          formDate.append("media[][title]", this.titleFile);
          adminApi
            .post(`/media-name`, formDate)
            .then((res) => {
              let old_media = [];
              this.images.forEach((e) => old_media.push(e.id));
              let new_media = [];
              res.data.data.forEach((e) => new_media.push(e.id));

              adminApi
                .put(`/general-customer/${this.customer_id}`, {
                  old_media,
                  media: new_media,
                })
                .then((res) => {
                  this.images = res.data.data.media ?? [];
                  if (this.images && this.images.length > 0) {
                    this.showPhoto = this.images[this.images.length - 1].webp;
                  } else {
                    this.showPhoto = "../../../../../images/img-1.png";
                  }
                  this.getData();
                  this.uploadPhotoTitleHidden();
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
                    .put(`/general-customer/${this.country_id}`, {
                      old_media,
                      media: new_media,
                    })
                    .then((res) => {
                      this.images = res.data.data.media ?? [];
                      if (this.images && this.images.length > 0) {
                        this.showPhoto =
                          this.images[this.images.length - 1].webp;
                      } else {
                        this.showPhoto = "../../../../../images/img-1.png";
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
        .put(`/general-customer/${this.customer_id}`, { old_media })
        .then((res) => {
          this.customers[index] = res.data.data;
          this.images = res.data.data.media ?? [];
          if (this.images && this.images.length > 0) {
            this.showPhoto = this.images[this.images.length - 1].webp;
          } else {
            this.showPhoto = "../../../../../images/img-1.png";
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
     *
     */
    formatDate(value) {
      return formatDateOnly(value);
    },
    log(id) {
      if (this.mouseEnter != id) {
        this.Tooltip = "";
        this.mouseEnter = id;
        adminApi
          .get(`/general-customer/logs/${id}`)
          .then((res) => {
            let l = res.data.data;
            l.forEach((e) => {
              this.Tooltip += `Created By: ${e.causer_type}; Event: ${
                e.event
              }; Description: ${e.description} ;Created At: ${this.formatDate(
                e.created_at
              )} \n`;
            });
            $(`#tooltip-${id}`).tooltip();
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
    ExportExcel(type, fn, dl) {
      this.enabled3 = false;
      setTimeout(() => {
        let elt = this.$refs.exportable_table;
        let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
        if (dl) {
          XLSX.write(wb, { bookType: type, bookSST: true, type: "base64" });
        } else {
          XLSX.writeFile(
            wb,
            fn || ("guest" + "." || "SheetJSTableExport.") + (type || "xlsx")
          );
        }
        this.enabled3 = true;
      }, 100);
    },
    async getLocation() {
      if (navigator.geolocation) {
        return await navigator.geolocation.getCurrentPosition(
          this.showPosition
        );
      }
    },
    showPosition(position) {
      this.branchCreate.latitude = position.coords.latitude;
      this.branchCreate.longitude = position.coords.longitude;
      this.branchEdit.latitude = position.coords.latitude;
      this.branchEdit.longitude = position.coords.longitude;
    },
    updatePhone(e) {
      this.create.phone = e.phoneNumber;
      this.countryCode = e.countryCode;
      // this.isVaildPhone = e.isValid;
    },
    updatePhoneEdit(e) {
      this.edit.phone = e.phoneNumber;
      this.countryCode = e.countryCode;
    },
    updateContract(e) {
      this.create.contact_phone = e.phoneNumber;
    },
    updateContractEdit(e) {
      this.edit.contact_phone = e.phoneNumber;
    },
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <employee
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getEmployees"
    />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("general.companyTable") }}</h4>
              <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                <div class="d-inline-block" style="width: 22.2%">
                  <!-- Basic dropdown -->
                  <b-dropdown
                    variant="primary"
                    :text="$t('general.searchSetting')"
                    ref="dropdown"
                    class="btn-block setting-search dropdown-menu-custom-company"
                  >
                    <b-form-checkbox
                      v-if="isVisible('name')"
                      v-model="filterSetting"
                      value="name"
                      class="mb-1"
                      >{{ getCompanyKey("company_name_ar") }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-if="isVisible('name_e')"
                      v-model="filterSetting"
                      value="name_e"
                      class="mb-1"
                      >{{ getCompanyKey("company_name_en") }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-if="isVisible('phone')"
                      v-model="filterSetting"
                      value="phone"
                      class="mb-1"
                      >{{ getCompanyKey("company_phone") }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-if="isVisible('tax_record')"
                      v-model="filterSetting"
                      value="tax_record"
                      class="mb-1"
                      >{{
                        getCompanyKey("company_tax_record")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-if="isVisible('address')"
                      v-model="filterSetting"
                      value="address"
                      class="mb-1"
                      >{{ getCompanyKey("company_address") }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-if="isVisible('contact_person')"
                      v-model="filterSetting"
                      value="contact_person"
                      class="mb-1"
                      >{{
                        getCompanyKey("comapny_contact_person")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-if="isVisible('contact_phone')"
                      v-model="filterSetting"
                      value="contact_phone"
                      class="mb-1"
                      >{{
                        getCompanyKey("comapny_contact_phones")
                      }}</b-form-checkbox
                    >
                  </b-dropdown>
                  <!-- Basic dropdown -->
                </div>

                <div
                  class="d-inline-block position-relative"
                  style="width: 77%"
                >
                  <span
                    :class="[
                      'search-custom position-absolute',
                      $i18n.locale == 'ar' ? 'search-custom-ar' : '',
                    ]"
                  >
                    <i class="fe-search"></i>
                  </span>
                  <input
                    class="form-control"
                    style="display: block; width: 93%; padding-top: 3px"
                    type="text"
                    v-model.trim="search"
                    :placeholder="`${$t('general.Search')}...`"
                  />
                </div>
              </div>
            </div>
            <!-- end search -->

            <div
              class="row justify-content-between align-items-center mb-2 px-1"
            >
              <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                <!-- start create and printer -->
                <b-button
                  v-if="isPermission('create Customer')"
                  v-b-modal.create
                  variant="primary"
                  class="btn-sm mx-1 font-weight-bold"
                >
                  {{ $t("general.Create") }}
                  <i class="fas fa-plus"></i>
                </b-button>
                <div class="d-inline-flex">
                  <button
                    @click="ExportExcel('xlsx')"
                    class="custom-btn-dowonload"
                  >
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button
                    v-print="'#printCustomer'"
                    class="custom-btn-dowonload"
                  >
                    <i class="fe-printer"></i>
                  </button>
                  <button
                    class="custom-btn-dowonload"
                    @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                    v-if="
                      checkAll.length == 1 && isPermission('update Customer')
                    "
                  >
                    <i class="mdi mdi-square-edit-outline"></i>
                  </button>
                  <!-- start mult delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="
                      checkAll.length > 1 && isPermission('delete Customer')
                    "
                    @click.prevent="deleteCountry(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="
                      checkAll.length == 1 && isPermission('delete Customer')
                    "
                    @click.prevent="deleteCountry(checkAll[0])"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!--  end one delete  -->
                </div>
                <!-- end create and printer -->
              </div>
              <div
                class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end"
              >
                <div class="d-fex">
                  <!-- start filter and setting -->
                  <div class="d-inline-block">
                    <b-button class="mx-1 custom-btn-background">
                      {{ $t("general.filter") }}
                      <i class="fas fa-filter"></i>
                    </b-button>
                    <b-button class="mx-1 custom-btn-background">
                      {{ $t("general.group") }}
                      <i class="fe-menu"></i>
                    </b-button>
                    <!-- Basic dropdown -->
                    <b-dropdown
                      variant="primary"
                      :html="`${$t(
                        'general.setting'
                      )} <i class='fe-settings'></i>`"
                      ref="dropdown"
                      class="dropdown-custom-ali dropdown-menu-custom-company"
                    >
                      <b-form-checkbox
                        v-if="isVisible('name')"
                        v-model="setting.name"
                        class="mb-1"
                        >{{ getCompanyKey("company_name_ar") }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-if="isVisible('name_e')"
                        v-model="setting.name_e"
                        class="mb-1"
                        >{{ getCompanyKey("company_name_en") }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-if="isVisible('employee_id')"
                        v-model="setting.employee"
                        class="mb-1"
                        >{{
                          getCompanyKey("company_employee")
                        }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-if="isVisible('phone')"
                        v-model="setting.phone"
                        class="mb-1"
                        >{{ getCompanyKey("company_phone") }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-if="isVisible('email')"
                        v-model="setting.email"
                        class="mb-1"
                        >{{ getCompanyKey("company_email") }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-if="isVisible('tax_record')"
                        v-model="setting.tax_record"
                        class="mb-1"
                        >{{
                          getCompanyKey("company_tax_record")
                        }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-if="isVisible('address')"
                        v-model="setting.address"
                        class="mb-1"
                        >{{ getCompanyKey("company_address") }}</b-form-checkbox
                      >

                      <b-form-checkbox
                        v-if="isVisible('contact_person')"
                        v-model="setting.contact_person"
                        class="mb-1"
                        >{{
                          getCompanyKey("comapny_contact_person")
                        }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-if="isVisible('contact_phone')"
                        v-model="setting.contact_phone"
                        class="mb-1"
                        >{{
                          getCompanyKey("comapny_contact_phones")
                        }}</b-form-checkbox
                      >

                      <div class="d-flex justify-content-end">
                        <a
                          href="javascript:void(0)"
                          class="btn btn-primary btn-sm"
                          >Apply</a
                        >
                      </div>
                    </b-dropdown>
                    <!-- Basic dropdown -->
                  </div>
                  <!-- end filter and setting -->

                  <!-- start Pagination -->
                  <div
                    class="d-inline-flex align-items-center pagination-custom"
                  >
                    <div class="d-inline-block" style="font-size: 13px">
                      {{ customersPagination.from }}-{{
                        customersPagination.to
                      }}
                      / {{ customersPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            customersPagination.current_page == 1 ? 'none' : '',
                        }"
                        @click.prevent="
                          getData(customersPagination.current_page - 1)
                        "
                      >
                        <span>&lt;</span>
                      </a>
                      <input
                        type="text"
                        @keyup.enter="getDataCurrentPage()"
                        v-model="current_page"
                        class="pagination-current-page"
                      />
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            customersPagination.last_page ==
                            customersPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(customersPagination.current_page + 1)
                        "
                      >
                        <span>&gt;</span>
                      </a>
                    </div>
                  </div>
                  <!-- end Pagination -->
                </div>
              </div>
            </div>
            <!--  create   -->
            <b-modal
              id="create"
              :title="getCompanyKey('company_create_form')"
              title-class="font-18"
              dialog-class="modal-full-width"
              body-class="p-4 "
              :hide-footer="true"
              @show="resetModal"
              @hidden="resetModalHidden"
            >
              <form>
                <div class="card">
                  <div class="card-body">
                    <div class="mb-3 d-flex justify-content-end">
                      <b-button
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
                          {{ $t("general.Add") }}
                        </b-button>

                        <b-button
                          variant="success"
                          class="mx-1"
                          disabled
                          v-else
                        >
                          <b-spinner small></b-spinner>
                          <span class="sr-only"
                            >{{ $t("login.Loading") }}...</span
                          >
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
                  </div>

                  <b-tabs nav-class="nav-tabs nav-bordered">
                    <b-tab :title="$t('general.DataEntry')" active>
                      <div class="row">
                        <div class="col-md-4" v-if="isVisible('name')">
                          <div class="form-group">
                            <label for="field-1" class="control-label">
                              {{ getCompanyKey("company_name_ar") }}
                              <span
                                v-if="isRequired('name')"
                                class="text-danger"
                                >*</span
                              >
                            </label>
                            <div dir="rtl">
                              <input
                                @keyup="arabicValue(create.name)"
                                type="text"
                                class="form-control"
                                data-create="1"
                                v-model="$v.create.name.$model"
                                :class="{
                                  'is-invalid':
                                    $v.create.name.$error || errors.name,
                                  'is-valid':
                                    !$v.create.name.$invalid && !errors.name,
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
                                >{{ errorMessage }}</ErrorMessage
                              >
                            </template>
                          </div>
                        </div>
                        <div class="col-md-4" v-if="isVisible('name_e')">
                          <div class="form-group">
                            <label for="field-2" class="control-label">
                              {{ getCompanyKey("company_name_en") }}
                              <span
                                v-if="isRequired('name_e')"
                                class="text-danger"
                                >*</span
                              >
                            </label>
                            <div dir="ltr">
                              <input
                                @keyup="englishValue(create.name_e)"
                                type="text"
                                class="form-control"
                                data-create="2"
                                v-model="$v.create.name_e.$model"
                                :class="{
                                  'is-invalid':
                                    $v.create.name_e.$error || errors.name_e,
                                  'is-valid':
                                    !$v.create.name_e.$invalid &&
                                    !errors.name_e,
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
                                >{{ errorMessage }}</ErrorMessage
                              >
                            </template>
                          </div>
                        </div>
                        <div class="col-md-4" v-if="isVisible('employee_id')">
                          <div class="form-group">
                            <label
                              >{{ getCompanyKey("company_employee")
                              }}<span
                                v-if="isRequired('employee_id')"
                                class="text-danger"
                                >*</span
                              ></label
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
                              :class="{
                                'is-invalid':
                                  $v.create.employee_id.$error ||
                                  errors.employee_id,
                                'is-valid':
                                  !$v.create.employee_id.$invalid &&
                                  !errors.employee_id,
                              }"
                            >
                            </multiselect>
                            <template v-if="errors.employee_id">
                              <ErrorMessage
                                v-for="(
                                  errorMessage, index
                                ) in errors.employee_id"
                                :key="index"
                                >{{ errorMessage }}
                              </ErrorMessage>
                            </template>
                          </div>
                        </div>
                      </div>
                      <hr
                        v-if="
                          isVisible('phone') ||
                          isVisible('tax_record') ||
                          isVisible('address')
                        "
                        style="
                          margin: 10px 0 !important;
                          border-top: 1px solid rgb(141 163 159 / 42%);
                        "
                      />
                      <div class="row">
                        <div class="col-md-3" v-if="isVisible('phone')">
                          <div class="form-group">
                            <label class="control-label">
                              {{ getCompanyKey("company_phone") }}
                              <span
                                v-if="isRequired('phone')"
                                class="text-danger"
                                >*</span
                              >
                            </label>
                            <VuePhoneNumberInput
                              v-model="$v.create.phone.$model"
                              :default-country-code="countryCode"
                              valid-color="#28a745"
                              error-color="#dc3545"
                              :preferred-countries="['FR', 'EG', 'DE']"
                              @update="updatePhone"
                            />
                            <template v-if="errors.phone">
                              <ErrorMessage
                                v-for="(errorMessage, index) in errors.phone"
                                :key="index"
                                >{{ errorMessage }}</ErrorMessage
                              >
                            </template>
                          </div>
                        </div>
                        <div class="col-md-3" v-if="isVisible('email')">
                          <div class="form-group">
                            <label for="field-1" class="control-label">
                              {{ getCompanyKey("company_email") }}
                              <span
                                v-if="isRequired('email')"
                                class="text-danger"
                                >*</span
                              >
                            </label>
                            <input
                              type="text"
                              class="form-control"
                              data-create="9"
                              v-model="$v.create.email.$model"
                              :class="{
                                'is-invalid':
                                  $v.create.email.$error || errors.email,
                                'is-valid':
                                  !$v.create.email.$invalid && !errors.email,
                              }"
                            />
                            <template v-if="errors.email">
                              <ErrorMessage
                                v-for="(errorMessage, index) in errors.email"
                                :key="index"
                                >{{ errorMessage }}</ErrorMessage
                              >
                            </template>
                          </div>
                        </div>
                        <div class="col-md-3" v-if="isVisible('tax_record')">
                          <div class="form-group">
                            <label class="control-label">
                              {{ getCompanyKey("company_tax_record") }}
                              <span
                                v-if="isRequired('tax_record')"
                                class="text-danger"
                                >*</span
                              >
                            </label>
                            <input
                              type="text"
                              class="form-control"
                              data-create="9"
                              v-model="$v.create.tax_record.$model"
                              :class="{
                                'is-invalid':
                                  $v.create.tax_record.$error ||
                                  errors.tax_record,
                                'is-valid':
                                  !$v.create.tax_record.$invalid &&
                                  !errors.tax_record,
                              }"
                            />
                            <template v-if="errors.tax_record">
                              <ErrorMessage
                                v-for="(
                                  errorMessage, index
                                ) in errors.tax_record"
                                :key="index"
                                >{{ errorMessage }}</ErrorMessage
                              >
                            </template>
                          </div>
                        </div>
                        <div class="col-md-3" v-if="isVisible('address')">
                          <div class="form-group">
                            <label for="field-1" class="control-label">
                              {{ getCompanyKey("company_address") }}
                              <span
                                v-if="isRequired('address')"
                                class="text-danger"
                                >*</span
                              >
                            </label>
                            <input
                              type="text"
                              class="form-control"
                              data-create="9"
                              v-model="$v.create.address.$model"
                              :class="{
                                'is-invalid':
                                  $v.create.address.$error || errors.address,
                                'is-valid':
                                  !$v.create.address.$invalid &&
                                  !errors.address,
                              }"
                            />
                            <template v-if="errors.address">
                              <ErrorMessage
                                v-for="(errorMessage, index) in errors.address"
                                :key="index"
                                >{{ errorMessage }}</ErrorMessage
                              >
                            </template>
                          </div>
                        </div>
                      </div>
                      <hr
                        v-if="
                          isVisible('contact_person') ||
                          isVisible('contact_phone')
                        "
                        style="
                          margin: 10px 0 !important;
                          border-top: 1px solid rgb(141 163 159 / 42%);
                        "
                      />
                      <div class="row">
                        <div
                          class="col-md-4"
                          v-if="isVisible('contact_person')"
                        >
                          <div class="form-group">
                            <label class="control-label">
                              {{ getCompanyKey("comapny_contact_person") }}
                              <span
                                v-if="isRequired('contact_person')"
                                class="text-danger"
                                >*</span
                              >
                            </label>
                            <input
                              type="text"
                              class="form-control"
                              data-create="9"
                              v-model="$v.create.contact_person.$model"
                              :class="{
                                'is-invalid':
                                  $v.create.contact_person.$error ||
                                  errors.contact_person,
                                'is-valid':
                                  !$v.create.contact_person.$invalid &&
                                  !errors.contact_person,
                              }"
                            />
                            <template v-if="errors.contact_person">
                              <ErrorMessage
                                v-for="(
                                  errorMessage, index
                                ) in errors.contact_person"
                                :key="index"
                                >{{ errorMessage }}</ErrorMessage
                              >
                            </template>
                          </div>
                        </div>
                        <div class="col-md-4" v-if="isVisible('contact_phone')">
                          <div class="form-group">
                            <label class="control-label">
                              {{ getCompanyKey("comapny_contact_phones") }}
                              <span
                                v-if="isRequired('contact_phone')"
                                class="text-danger"
                                >*</span
                              >
                            </label>
                            <VuePhoneNumberInput
                              v-model="$v.create.contact_phone.$model"
                              :default-country-code="countryCode"
                              valid-color="#28a745"
                              error-color="#dc3545"
                              :preferred-countries="['FR', 'EG', 'DE']"
                              @update="updateContract"
                            />
                            <template v-if="errors.contact_phone">
                              <ErrorMessage
                                v-for="(
                                  errorMessage, index
                                ) in errors.contact_phone"
                                :key="index"
                                >{{ errorMessage }}</ErrorMessage
                              >
                            </template>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab
                      :disabled="!customer_id"
                      :title="$t('general.ImageUploads')"
                    >
                      <div class="row">
                        <b-modal
                          id="uploadPhotoTitleCreate"
                          :title="$t('general.ImageUploads')"
                          title-class="font-18"
                          body-class="p-4 "
                          :hide-footer="true"
                          @show="uploadPhotoTitle"
                          @hidden="uploadPhotoTitleHidden"
                        >
                          <div class="form-group">
                            <label class="control-label">
                              {{ $t("general.titleFile") }}
                              <span class="text-danger">*</span>
                            </label>
                            <div dir="rtl">
                              <input
                                type="text"
                                class="form-control"
                                data-create="1"
                                v-model="$v.titleFile.$model"
                                :class="{
                                  'is-invalid':
                                    $v.titleFile.$error || errors.title,
                                  'is-valid':
                                    !$v.titleFile.$invalid && !errors.title,
                                }"
                              />
                            </div>
                            <div
                              v-if="!$v.titleFile.minLength"
                              class="invalid-feedback"
                            >
                              {{ $t("general.Itmustbeatleast") }}
                              {{ $v.titleFile.$params.minLength.min }}
                              {{ $t("general.letters") }}
                            </div>
                            <div
                              v-if="!$v.titleFile.maxLength"
                              class="invalid-feedback"
                            >
                              {{ $t("general.Itmustbeatmost") }}
                              {{ $v.titleFile.$params.maxLength.max }}
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

                          <input
                            accept="image/png, image/gif, image/jpeg, image/jpg"
                            type="file"
                            id="uploadImageCreate"
                            @change.prevent="onImageChanged"
                            class="input-file-upload position-absolute"
                            :class="[
                              'd-none',
                              {
                                'is-invalid':
                                  $v.create.media.$error || errors.media,
                                'is-valid':
                                  !$v.create.media.$invalid && !errors.media,
                              },
                            ]"
                          />

                          <b-button
                            :disabled="!titleFile && $v.titleFile.$error"
                            @click="changePhoto"
                            variant="success"
                            type="button"
                            class="mx-1 font-weight-bold px-3"
                            v-if="!isLoader"
                          >
                            {{ $t("general.Add") }}
                          </b-button>
                          <b-button
                            variant="success"
                            class="mx-1"
                            disabled
                            v-else
                          >
                            <b-spinner small></b-spinner>
                            <span class="sr-only"
                              >{{ $t("login.Loading") }}...</span
                            >
                          </b-button>
                        </b-modal>
                        <div class="col-md-8 my-1">
                          <!-- file upload -->
                          <div
                            class="row align-content-between"
                            style="width: 100%; height: 100%"
                          >
                            <div class="col-12">
                              <div class="d-flex flex-wrap">
                                <div
                                  class="col-4"
                                  v-for="(photo, index) in images"
                                  :key="photo.id"
                                >
                                  <!-- whatup -->
                                  <b-modal
                                    :id="`show-whatup-${index}`"
                                    title="Send Whatsapp"
                                    title-class="font-18"
                                    body-class="p-4 "
                                    :hide-footer="true"
                                    @hidden="resetWhatsApp"
                                  >
                                    <form>
                                      <div class="d-flex justify-content-end">
                                        <button
                                          @click.prevent="
                                            getLinkWhastapp(photo.webp)
                                          "
                                          type="button"
                                          class="btn btn-info"
                                        >
                                          {{ $t("general.send") }}
                                        </button>
                                      </div>
                                      <div
                                        class="card"
                                        style="background: none !important"
                                      >
                                        <div class="row">
                                          <div class="col-md-12">
                                            <div class="form-group">
                                              <label class="control-label">
                                                {{ $t("general.phone") }}
                                              </label>
                                              <input
                                                v-model="
                                                  $v.watsApp.phone.$model
                                                "
                                                type="number"
                                                class="form-control"
                                                :class="{
                                                  'is-invalid':
                                                    $v.watsApp.phone.$error,
                                                  'is-valid':
                                                    !$v.watsApp.phone.$invalid,
                                                }"
                                                data-create="9"
                                              />
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </form>
                                  </b-modal>
                                  <!-- whatup -->
                                  <div
                                    :class="[
                                      'dropzone-previews position-relative mb-2',
                                    ]"
                                  >
                                    <div
                                      :class="[
                                        'card mb-0 shadow-none border',
                                        images.length - 1 == index
                                          ? 'bg-primary'
                                          : '',
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
                                              @error="
                                                src =
                                                  '../../../../../images/img-1.png'
                                              "
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
                                              {{ photo.title }}
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
                                  <div class="text-center">
                                    <a
                                      :href="photo.webp"
                                      download
                                      class="btn-sm mx-1 font-weight-bold btn-info"
                                    >
                                      {{ $t("general.download") }}
                                      <i class="fas fa-file-download"></i>
                                    </a>
                                    <a
                                      @click.prevent="
                                        $bvModal.show(`show-whatup-${index}`)
                                      "
                                      href="#"
                                      class="btn-sm mx-1 font-weight-bold btn-secondary"
                                    >
                                      {{ $t("general.whatsapp") }}
                                      <i class="fab fa-whatsapp"></i>
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="footer-image col-12">
                              <b-button
                                @click="uploadPhotoTitle"
                                variant="success"
                                type="button"
                                class="mx-1 font-weight-bold px-3"
                                v-if="!isLoader"
                              >
                                {{ $t("general.Add") }}
                              </b-button>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="show-dropzone">
                            <img
                              :src="showPhoto"
                              class="img-thumbnail"
                              @error="src = '../../../../../images/img-1.png'"
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

            <!-- start .table-responsive-->
            <div
              class="table-responsive mb-3 custom-table-theme position-relative"
            >
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->

              <table
                class="table table-borderless table-hover table-centered m-0"
                ref="exportable_table"
                id="printCustomer"
              >
                <thead>
                  <tr>
                    <th
                      scope="col"
                      style="width: 0"
                      v-if="enabled3"
                      class="do-not-print"
                    >
                      <div class="form-check custom-control">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          v-model="isCheckAll"
                          style="width: 17px; height: 17px"
                        />
                      </div>
                    </th>
                    <th v-if="setting.name && isVisible('name')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("company_name_ar") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="customers.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="customers.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.name_e && isVisible('name_e')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("company_name_en") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="customers.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="customers.sort(sortString('-name_e'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.employee && isVisible('employee_id')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("company_employee") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="customers.sort(sortNumber('phone'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="customers.sort(sortNumber('-phone'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.phone && isVisible('phone')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("company_phone") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="customers.sort(sortNumber('phone'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="customers.sort(sortNumber('-phone'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.email && isVisible('email')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("company_email") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="customers.sort(sortNumber('phone'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="customers.sort(sortNumber('-phone'))"
                          ></i>
                        </div>
                      </div>
                    </th>

                    <th v-if="setting.tax_record && isVisible('tax_record')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("company_tax_record") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="customers.sort(sortString('tax_record'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="customers.sort(sortString('-tax_record'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.address && isVisible('address')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("company_address") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="customers.sort(SortNumber('address'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="customers.sort(SortNumber('-address'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th
                      v-if="
                        setting.contact_person && isVisible('contact_person')
                      "
                    >
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("comapny_contact_person")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="
                              customers.sort(SortNumber('contact_person'))
                            "
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              customers.sort(SortNumber('-contact_person'))
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th
                      v-if="setting.contact_phone && isVisible('contact_phone')"
                    >
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("comapny_contact_phones")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="customers.sort(SortNumber('contact_phone'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              customers.sort(SortNumber('-contact_phone'))
                            "
                          ></i>
                        </div>
                      </div>
                    </th>

                    <th v-if="enabled3" class="do-not-print">
                      {{ $t("general.Action") }}
                    </th>
                    <th v-if="enabled3" class="do-not-print">
                      <i class="fas fa-ellipsis-v"></i>
                    </th>
                  </tr>
                </thead>
                <tbody v-if="customers.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="
                      isPermission('update Customer')
                        ? $bvModal.show(`modal-edit-${data.id}`)
                        : false
                    "
                    v-for="(data, index) in customers"
                    :key="data.id"
                    class="body-tr-custom"
                  >
                    <td v-if="enabled3" class="do-not-print">
                      <div
                        class="form-check custom-control"
                        style="min-height: 1.9em"
                      >
                        <input
                          style="width: 17px; height: 17px"
                          class="form-check-input"
                          type="checkbox"
                          v-model="checkAll"
                          :value="data.id"
                        />
                      </div>
                    </td>
                    <td v-if="setting.name && isVisible('name')">
                      <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                    </td>
                    <td v-if="setting.name_e && isVisible('name_e')">
                      <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
                    </td>
                    <td v-if="setting.employee && isVisible('employee_id')">
                      {{
                        data.employee
                          ? $i18n.locale == "ar"
                            ? data.employee.name
                            : data.employee.name_e
                          : " - "
                      }}
                    </td>

                    <td v-if="setting.phone && isVisible('phone')">
                      <h5 class="m-0 font-weight-normal">{{ data.phone }}</h5>
                    </td>
                    <td v-if="setting.email && isVisible('email')">
                      <h5 class="m-0 font-weight-normal">{{ data.email }}</h5>
                    </td>

                    <td v-if="setting.tax_record && isVisible('tax_record')">
                      <h5 class="m-0 font-weight-normal">
                        {{ data.tax_record }}
                      </h5>
                    </td>
                    <td v-if="setting.address && isVisible('address')">
                      <h5 class="m-0 font-weight-normal">{{ data.address }}</h5>
                    </td>
                    <td
                      v-if="
                        setting.contact_person && isVisible('contact_person')
                      "
                    >
                      <h5 class="m-0 font-weight-normal">
                        {{ data.contact_person }}
                      </h5>
                    </td>
                    <td
                      v-if="setting.contact_phone && isVisible('contact_phone')"
                    >
                      <h5 class="m-0 font-weight-normal">
                        {{ data.contact_phone }}
                      </h5>
                    </td>
                    <td v-if="enabled3" class="do-not-print">
                      <div class="btn-group">
                        <button
                          type="button"
                          class="btn btn-sm dropdown-toggle dropdown-coustom"
                          data-toggle="dropdown"
                          aria-expanded="false"
                        >
                          {{ $t("general.commands") }}
                          <i class="fas fa-angle-down"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-custom">
                          <a
                            v-if="isPermission('update Customer')"
                            class="dropdown-item"
                            href="#"
                            @click="$bvModal.show(`modal-edit-${data.id}`)"
                          >
                            <div
                              class="d-flex justify-content-between align-items-center text-black"
                            >
                              <span>{{ $t("general.edit") }}</span>
                              <i
                                class="mdi mdi-square-edit-outline text-info"
                              ></i>
                            </div>
                          </a>
                          <a
                            v-if="isPermission('delete Customer')"
                            class="dropdown-item text-black"
                            href="#"
                            @click.prevent="deleteCountry(data.id)"
                          >
                            <div
                              class="d-flex justify-content-between align-items-center text-black"
                            >
                              <span>{{ $t("general.delete") }}</span>
                              <i class="fas fa-times text-danger"></i>
                            </div>
                          </a>
                        </div>
                      </div>

                      <!--  edit   -->
                      <b-modal
                        :id="`modal-edit-${data.id}`"
                        :title="getCompanyKey('company_edit_form')"
                        title-class="font-18"
                        body-class="p-4"
                        dialog-class="modal-full-width"
                        :ref="`edit-${data.id}`"
                        :hide-footer="true"
                        @show="resetModalEdit(data.id)"
                        @hidden="resetModalHiddenEdit(data.id)"
                      >
                        <form>
                          <div class="card">
                            <div class="card-body">
                              <div class="mb-3 d-flex justify-content-end">
                                <!-- Emulate built in modal footer ok and cancel button actions -->
                                <b-button
                                  variant="success"
                                  type="submit"
                                  class="mx-1"
                                  v-if="!isLoader"
                                  @click.prevent="editSubmit(data.id)"
                                >
                                  {{ $t("general.Edit") }}
                                </b-button>

                                <b-button
                                  variant="success"
                                  class="mx-1"
                                  disabled
                                  v-else
                                >
                                  <b-spinner small></b-spinner>
                                  <span class="sr-only"
                                    >{{ $t("login.Loading") }}...</span
                                  >
                                </b-button>

                                <b-button
                                  variant="danger"
                                  type="button"
                                  @click.prevent="
                                    $bvModal.hide(`modal-edit-${data.id}`)
                                  "
                                >
                                  {{ $t("general.Cancel") }}
                                </b-button>
                              </div>
                            </div>

                            <b-tabs nav-class="nav-tabs nav-bordered">
                              <b-tab :title="$t('general.DataEntry')" active>
                                <div class="row">
                                  <div
                                    class="col-md-4"
                                    v-if="isVisible('name')"
                                  >
                                    <div class="form-group">
                                      <label class="control-label">
                                        {{ getCompanyKey("company_name_ar") }}
                                        <span
                                          v-if="isRequired('name')"
                                          class="text-danger"
                                          >*</span
                                        >
                                      </label>
                                      <div dir="rtl">
                                        <input
                                          @keyup="arabicValue(edit.name)"
                                          type="text"
                                          class="form-control"
                                          data-edit="1"
                                          v-model="$v.edit.name.$model"
                                          :class="{
                                            'is-invalid':
                                              $v.edit.name.$error ||
                                              errors.name,
                                            'is-valid':
                                              !$v.edit.name.$invalid &&
                                              !errors.name,
                                          }"
                                        />
                                      </div>
                                      <div
                                        v-if="!$v.edit.name.minLength"
                                        class="invalid-feedback"
                                      >
                                        {{ $t("general.Itmustbeatleast") }}
                                        {{ $v.edit.name.$params.minLength.min }}
                                        {{ $t("general.letters") }}
                                      </div>
                                      <div
                                        v-if="!$v.edit.name.maxLength"
                                        class="invalid-feedback"
                                      >
                                        {{ $t("general.Itmustbeatmost") }}
                                        {{ $v.edit.name.$params.maxLength.max }}
                                        {{ $t("general.letters") }}
                                      </div>
                                      <template v-if="errors.name">
                                        <ErrorMessage
                                          v-for="(
                                            errorMessage, index
                                          ) in errors.name"
                                          :key="index"
                                          >{{ errorMessage }}</ErrorMessage
                                        >
                                      </template>
                                    </div>
                                  </div>
                                  <div
                                    class="col-md-4"
                                    v-if="isVisible('name_e')"
                                  >
                                    <div class="form-group">
                                      <label class="control-label">
                                        {{ getCompanyKey("company_name_en") }}
                                        <span
                                          v-if="isRequired('name_e')"
                                          class="text-danger"
                                          >*</span
                                        >
                                      </label>
                                      <div dir="ltr">
                                        <input
                                          @keyup="englishValue(edit.name_e)"
                                          type="text"
                                          class="form-control"
                                          data-edit="2"
                                          v-model="$v.edit.name_e.$model"
                                          :class="{
                                            'is-invalid':
                                              $v.edit.name_e.$error ||
                                              errors.name_e,
                                            'is-valid':
                                              !$v.edit.name_e.$invalid &&
                                              !errors.name_e,
                                          }"
                                        />
                                      </div>
                                      <div
                                        v-if="!$v.edit.name_e.minLength"
                                        class="invalid-feedback"
                                      >
                                        {{ $t("general.Itmustbeatleast") }}
                                        {{
                                          $v.edit.name_e.$params.minLength.min
                                        }}
                                        {{ $t("general.letters") }}
                                      </div>
                                      <div
                                        v-if="!$v.edit.name_e.maxLength"
                                        class="invalid-feedback"
                                      >
                                        {{ $t("general.Itmustbeatmost") }}
                                        {{
                                          $v.edit.name_e.$params.maxLength.max
                                        }}
                                        {{ $t("general.letters") }}
                                      </div>
                                      <template v-if="errors.name_e">
                                        <ErrorMessage
                                          v-for="(
                                            errorMessage, index
                                          ) in errors.name_e"
                                          :key="index"
                                          >{{ errorMessage }}</ErrorMessage
                                        >
                                      </template>
                                    </div>
                                  </div>

                                  <div
                                    class="col-md-4"
                                    v-if="isVisible('employee_id')"
                                  >
                                    <div class="form-group">
                                      <label
                                        >{{ getCompanyKey("company_employee")
                                        }}<span
                                          v-if="isRequired('employee_id')"
                                          class="text-danger"
                                          >*</span
                                        ></label
                                      >
                                      <multiselect
                                        @input="showEmployeeModalEdit"
                                        v-model="edit.employee_id"
                                        :options="
                                          employees.map((type) => type.id)
                                        "
                                        :custom-label="
                                          (opt) =>
                                            $i18n.locale == 'ar'
                                              ? employees.find(
                                                  (x) => x.id == opt
                                                ).name
                                              : employees.find(
                                                  (x) => x.id == opt
                                                ).name_e
                                        "
                                        :class="{
                                          'is-invalid':
                                            $v.edit.employee_id.$error ||
                                            errors.employee_id,
                                          'is-valid':
                                            !$v.edit.employee_id.$invalid &&
                                            !errors.employee_id,
                                        }"
                                      >
                                      </multiselect>
                                      <template v-if="errors.employee_id">
                                        <ErrorMessage
                                          v-for="(
                                            errorMessage, index
                                          ) in errors.employee_id"
                                          :key="index"
                                          >{{ errorMessage }}
                                        </ErrorMessage>
                                      </template>
                                    </div>
                                  </div>
                                </div>
                                <hr
                                  v-if="
                                    isVisible('phone') ||
                                    isVisible('tax_record') ||
                                    isVisible('address')
                                  "
                                  style="
                                    margin: 10px 0 !important;
                                    border-top: 1px solid rgb(141 163 159 / 42%);
                                  "
                                />
                                <div class="row">
                                  <div
                                    class="col-md-3"
                                    v-if="isVisible('phone')"
                                  >
                                    <div class="form-group">
                                      <label class="control-label">
                                        {{ getCompanyKey("company_phone") }}
                                        <span
                                          v-if="isRequired('phone')"
                                          class="text-danger"
                                          >*</span
                                        >
                                      </label>
                                      <VuePhoneNumberInput
                                        v-model="$v.edit.phone.$model"
                                        :default-country-code="countryCode"
                                        valid-color="#28a745"
                                        error-color="#dc3545"
                                        :preferred-countries="[
                                          'FR',
                                          'EG',
                                          'DE',
                                        ]"
                                        @update="updatePhoneEdit"
                                      />
                                      <template v-if="errors.phone">
                                        <ErrorMessage
                                          v-for="(
                                            errorMessage, index
                                          ) in errors.phone"
                                          :key="index"
                                          >{{ errorMessage }}</ErrorMessage
                                        >
                                      </template>
                                    </div>
                                  </div>
                                  <div
                                    class="col-md-3"
                                    v-if="isVisible('email')"
                                  >
                                    <div class="form-group">
                                      <label
                                        for="field-1"
                                        class="control-label"
                                      >
                                        {{ getCompanyKey("company_email") }}
                                        <span
                                          v-if="isRequired('email')"
                                          class="text-danger"
                                          >*</span
                                        >
                                      </label>
                                      <input
                                        type="text"
                                        class="form-control"
                                        data-create="9"
                                        v-model="$v.edit.email.$model"
                                        :class="{
                                          'is-invalid':
                                            $v.edit.email.$error ||
                                            errors.email,
                                          'is-valid':
                                            !$v.edit.email.$invalid &&
                                            !errors.email,
                                        }"
                                      />
                                      <template v-if="errors.email">
                                        <ErrorMessage
                                          v-for="(
                                            errorMessage, index
                                          ) in errors.email"
                                          :key="index"
                                          >{{ errorMessage }}</ErrorMessage
                                        >
                                      </template>
                                    </div>
                                  </div>
                                  <div
                                    class="col-md-3"
                                    v-if="isVisible('tax_record')"
                                  >
                                    <div class="form-group">
                                      <label class="control-label">
                                        {{
                                          getCompanyKey("company_tax_record")
                                        }}
                                        <span
                                          v-if="isRequired('tax_record')"
                                          class="text-danger"
                                          >*</span
                                        >
                                      </label>
                                      <input
                                        type="text"
                                        class="form-control"
                                        data-create="9"
                                        v-model="$v.edit.tax_record.$model"
                                        :class="{
                                          'is-invalid':
                                            $v.edit.tax_record.$error ||
                                            errors.tax_record,
                                          'is-valid':
                                            !$v.edit.tax_record.$invalid &&
                                            !errors.tax_record,
                                        }"
                                      />
                                      <template v-if="errors.tax_record">
                                        <ErrorMessage
                                          v-for="(
                                            errorMessage, index
                                          ) in errors.tax_record"
                                          :key="index"
                                          >{{ errorMessage }}</ErrorMessage
                                        >
                                      </template>
                                    </div>
                                  </div>
                                  <div
                                    class="col-md-3"
                                    v-if="isVisible('address')"
                                  >
                                    <div class="form-group">
                                      <label
                                        for="field-1"
                                        class="control-label"
                                      >
                                        {{ getCompanyKey("company_address") }}
                                        <span
                                          v-if="isRequired('address')"
                                          class="text-danger"
                                          >*</span
                                        >
                                      </label>
                                      <input
                                        type="text"
                                        class="form-control"
                                        data-create="9"
                                        v-model="$v.edit.address.$model"
                                        :class="{
                                          'is-invalid':
                                            $v.edit.address.$error ||
                                            errors.address,
                                          'is-valid':
                                            !$v.edit.address.$invalid &&
                                            !errors.address,
                                        }"
                                      />
                                      <template v-if="errors.address">
                                        <ErrorMessage
                                          v-for="(
                                            errorMessage, index
                                          ) in errors.address"
                                          :key="index"
                                          >{{ errorMessage }}</ErrorMessage
                                        >
                                      </template>
                                    </div>
                                  </div>
                                </div>
                                <hr
                                  v-if="
                                    isVisible('contact_person') ||
                                    isVisible('contact_phone')
                                  "
                                  style="
                                    margin: 10px 0 !important;
                                    border-top: 1px solid rgb(141 163 159 / 42%);
                                  "
                                />
                                <div class="row">
                                  <div
                                    class="col-md-4"
                                    v-if="isVisible('contact_person')"
                                  >
                                    <div class="form-group">
                                      <label class="control-label">
                                        {{
                                          getCompanyKey(
                                            "comapny_contact_person"
                                          )
                                        }}
                                        <span
                                          v-if="isRequired('contact_person')"
                                          class="text-danger"
                                          >*</span
                                        >
                                      </label>
                                      <input
                                        type="text"
                                        class="form-control"
                                        data-create="9"
                                        v-model="$v.edit.contact_person.$model"
                                        :class="{
                                          'is-invalid':
                                            $v.edit.contact_person.$error ||
                                            errors.contact_person,
                                          'is-valid':
                                            !$v.edit.contact_person.$invalid &&
                                            !errors.contact_person,
                                        }"
                                      />
                                      <template v-if="errors.contact_person">
                                        <ErrorMessage
                                          v-for="(
                                            errorMessage, index
                                          ) in errors.contact_person"
                                          :key="index"
                                          >{{ errorMessage }}</ErrorMessage
                                        >
                                      </template>
                                    </div>
                                  </div>
                                  <div
                                    class="col-md-4"
                                    v-if="isVisible('contact_phone')"
                                  >
                                    <div class="form-group">
                                      <label class="control-label">
                                        {{
                                          getCompanyKey(
                                            "comapny_contact_phones"
                                          )
                                        }}
                                        <span
                                          v-if="isRequired('contact_phone')"
                                          class="text-danger"
                                          >*</span
                                        >
                                      </label>
                                      <VuePhoneNumberInput
                                        v-model="$v.edit.contact_phone.$model"
                                        :default-country-code="countryCode"
                                        valid-color="#28a745"
                                        error-color="#dc3545"
                                        :preferred-countries="[
                                          'FR',
                                          'EG',
                                          'DE',
                                        ]"
                                        @update="updateContract"
                                      />
                                      <template v-if="errors.contact_phone">
                                        <ErrorMessage
                                          v-for="(
                                            errorMessage, index
                                          ) in errors.contact_phone"
                                          :key="index"
                                          >{{ errorMessage }}</ErrorMessage
                                        >
                                      </template>
                                    </div>
                                  </div>
                                </div>
                              </b-tab>
                              <b-tab :title="$t('general.ImageUploads')">
                                <div class="row">
                                  <b-modal
                                    id="uploadPhotoTitleCreate"
                                    :title="$t('general.ImageUploads')"
                                    title-class="font-18"
                                    body-class="p-4 "
                                    :hide-footer="true"
                                    @show="uploadPhotoTitle"
                                    @hidden="uploadPhotoTitleHidden"
                                  >
                                    <div class="form-group">
                                      <label class="control-label">
                                        {{ $t("general.titleFile") }}
                                        <span class="text-danger">*</span>
                                      </label>
                                      <div dir="rtl">
                                        <input
                                          type="text"
                                          class="form-control"
                                          data-create="1"
                                          v-model="$v.titleFile.$model"
                                          :class="{
                                            'is-invalid':
                                              $v.titleFile.$error ||
                                              errors.title,
                                            'is-valid':
                                              !$v.titleFile.$invalid &&
                                              !errors.title,
                                          }"
                                        />
                                      </div>
                                      <div
                                        v-if="!$v.titleFile.minLength"
                                        class="invalid-feedback"
                                      >
                                        {{ $t("general.Itmustbeatleast") }}
                                        {{ $v.titleFile.$params.minLength.min }}
                                        {{ $t("general.letters") }}
                                      </div>
                                      <div
                                        v-if="!$v.titleFile.maxLength"
                                        class="invalid-feedback"
                                      >
                                        {{ $t("general.Itmustbeatmost") }}
                                        {{ $v.titleFile.$params.maxLength.max }}
                                        {{ $t("general.letters") }}
                                      </div>
                                      <template v-if="errors.title">
                                        <ErrorMessage
                                          v-for="(
                                            errorMessage, index
                                          ) in errors.title"
                                          :key="index"
                                          >{{ errorMessage }}</ErrorMessage
                                        >
                                      </template>
                                    </div>

                                    <input
                                      accept="image/png, image/gif, image/jpeg, image/jpg"
                                      type="file"
                                      id="uploadImageEdit"
                                      @change.prevent="onImageChanged"
                                      class="input-file-upload position-absolute"
                                      :class="[
                                        'd-none',
                                        {
                                          'is-invalid':
                                            $v.edit.media.$error ||
                                            errors.media,
                                          'is-valid':
                                            !$v.edit.media.$invalid &&
                                            !errors.media,
                                        },
                                      ]"
                                    />

                                    <b-button
                                      :disabled="
                                        !titleFile && $v.titleFile.$error
                                      "
                                      @click="changePhotoEdit"
                                      variant="success"
                                      type="button"
                                      class="mx-1 font-weight-bold px-3"
                                      v-if="!isLoader"
                                    >
                                      {{ $t("general.Add") }}
                                    </b-button>
                                    <b-button
                                      variant="success"
                                      class="mx-1"
                                      disabled
                                      v-else
                                    >
                                      <b-spinner small></b-spinner>
                                      <span class="sr-only"
                                        >{{ $t("login.Loading") }}...</span
                                      >
                                    </b-button>
                                  </b-modal>

                                  <div class="col-md-8 my-1">
                                    <!-- file upload -->
                                    <div
                                      class="row align-content-between"
                                      style="width: 100%; height: 100%"
                                    >
                                      <div class="col-12">
                                        <div class="d-flex flex-wrap">
                                          <div
                                            class="col-4"
                                            v-for="(photo, index) in images"
                                            :key="photo.id"
                                          >
                                            <!-- whatup -->
                                            <b-modal
                                              :id="`show-whatup-${index}`"
                                              title="Send Whatsapp"
                                              title-class="font-18"
                                              body-class="p-4 "
                                              :hide-footer="true"
                                              @hidden="resetWhatsApp"
                                            >
                                              <form>
                                                <div
                                                  class="d-flex justify-content-end"
                                                >
                                                  <button
                                                    @click.prevent="
                                                      getLinkWhastapp(
                                                        photo.webp
                                                      )
                                                    "
                                                    type="button"
                                                    class="btn btn-info"
                                                  >
                                                    {{ $t("general.send") }}
                                                  </button>
                                                </div>
                                                <div
                                                  class="card"
                                                  style="
                                                    background: none !important;
                                                  "
                                                >
                                                  <div class="row">
                                                    <div class="col-md-12">
                                                      <div class="form-group">
                                                        <label
                                                          class="control-label"
                                                        >
                                                          {{
                                                            $t("general.phone")
                                                          }}
                                                        </label>
                                                        <input
                                                          v-model="
                                                            $v.watsApp.phone
                                                              .$model
                                                          "
                                                          type="number"
                                                          class="form-control"
                                                          :class="{
                                                            'is-invalid':
                                                              $v.watsApp.phone
                                                                .$error,
                                                            'is-valid':
                                                              !$v.watsApp.phone
                                                                .$invalid,
                                                          }"
                                                          data-create="9"
                                                        />
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              </form>
                                            </b-modal>
                                            <!-- whatup -->
                                            <div
                                              :class="[
                                                'dropzone-previews position-relative mb-2',
                                              ]"
                                            >
                                              <div
                                                :class="[
                                                  'card mb-0 shadow-none border',
                                                  images.length - 1 == index
                                                    ? 'bg-primary'
                                                    : '',
                                                ]"
                                              >
                                                <div class="p-2">
                                                  <div
                                                    class="row align-items-center"
                                                  >
                                                    <div
                                                      class="col-auto"
                                                      @click="
                                                        showPhoto = photo.webp
                                                      "
                                                    >
                                                      <img
                                                        data-dz-thumbnail
                                                        :src="photo.webp"
                                                        class="avatar-sm rounded bg-light"
                                                        @error="
                                                          src =
                                                            '../../../../../images/img-1.png'
                                                        "
                                                      />
                                                    </div>
                                                    <div class="col pl-0">
                                                      <a
                                                        href="javascript:void(0);"
                                                        :class="[
                                                          'font-weight-bold',
                                                          images.length - 1 ==
                                                          index
                                                            ? 'text-white'
                                                            : 'text-muted',
                                                        ]"
                                                        data-dz-name
                                                      >
                                                        {{ photo.title }}
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
                                                        deleteImageCreate(
                                                          photo.id,
                                                          index
                                                        )
                                                      "
                                                    >
                                                      <i class="fe-x"></i>
                                                    </a>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="text-center">
                                              <a
                                                :href="photo.webp"
                                                download
                                                class="btn-sm mx-1 font-weight-bold btn-info"
                                              >
                                                {{ $t("general.download") }}
                                                <i
                                                  class="fas fa-file-download"
                                                ></i>
                                              </a>
                                              <a
                                                @click.prevent="
                                                  $bvModal.show(
                                                    `show-whatup-${index}`
                                                  )
                                                "
                                                href="#"
                                                class="btn-sm mx-1 font-weight-bold btn-secondary"
                                              >
                                                {{ $t("general.whatsapp") }}
                                                <i class="fab fa-whatsapp"></i>
                                              </a>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="footer-image col-12">
                                        <b-button
                                          @click="uploadPhotoTitle"
                                          variant="success"
                                          type="button"
                                          class="mx-1 font-weight-bold px-3"
                                          v-if="!isLoader"
                                        >
                                          {{ $t("general.Add") }}
                                        </b-button>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="show-dropzone">
                                      <img
                                        :src="showPhoto"
                                        class="img-thumbnail"
                                        @error="
                                          src =
                                            '../../../../../images/img-1.png'
                                        "
                                      />
                                    </div>
                                  </div>
                                </div>
                              </b-tab>
                            </b-tabs>
                          </div>
                        </form>
                      </b-modal>
                      <!--  /edit   -->
                    </td>
                    <td v-if="enabled3" class="do-not-print">
                      <button
                        @mousemove="log(data.id)"
                        @mouseover="log(data.id)"
                        type="button"
                        class="btn"
                        :id="`tooltip-${data.id}`"
                        :data-placement="
                          $i18n.locale == 'en' ? 'left' : 'right'
                        "
                        :title="Tooltip"
                      >
                        <i class="fe-info" style="font-size: 22px"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr>
                    <th class="text-center" colspan="12">
                      {{ $t("general.notDataFound") }}
                    </th>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- end .table-responsive-->
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>
<style>
.dropdown-menu-custom-company.dropdown .dropdown-menu {
  padding: 5px 10px !important;
  overflow-y: scroll;
  height: 300px;
}

.modal-body .card .tabs .tab-content {
  padding: 20px 60px 40px !important;
}
.modal-dialog .card {
  margin: 0 !important;
}

.country.modal-body {
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

.title_menu {
  display: inline-block;
  font-weight: bold;
  font-size: 18px;
}
</style>
