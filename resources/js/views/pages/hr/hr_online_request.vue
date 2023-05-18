<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {
  required,
  minLength,
  maxLength,
  integer,
  requiredIf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import alphaArabic from "../../../helper/alphaArabic";
import alphaEnglish from "../../../helper/alphaEnglish";
import {
  formatDateTime,
  formatDateOnly,
  formatTime,
} from "../../../helper/startDate";
import DatePicker from "vue2-datepicker";
import { dynamicSortString, dynamicSortDate } from "../../../helper/tableSort";
import translation from "../../../helper/translation-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import Multiselect from "vue-multiselect";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Online request",
    meta: [{ name: "description", content: "Online request" }],
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    Multiselect,
    Switches,
    ErrorMessage,
    loader,
    DatePicker,
  },
  data() {
    return {
      fields: [],
      per_page: 50,
      search: "",
      debounce: {},
      onlineRequestsPagination: {},
      onlineRequests: [],
      isLoader: false,
      requestTypes: [],
      employees: [],
      statuses: [],
      create: {
        request_type_id: null,
        last_status_id: null,
        employee_id: null,
        start_from: formatDateOnly(new Date()),
        date: formatDateOnly(new Date()),
        end_date: formatDateOnly(new Date()),
        descriptions: "",
        amount: 0,
        from_hour: formatTime(new Date()),
        to_hour: formatTime(new Date()),
        custom_start_from: new Date(),
        custom_date: new Date(),
        custom_end_date: new Date(),
        custom_from_hour: new Date(),
        custom_to_hour: new Date(),
        approved_date: formatDateOnly(new Date()),
        approved_start_from: formatDateOnly(new Date()),
        approved_end_date: formatDateOnly(new Date()),
        approved_from_hour: formatTime(new Date()),
        approved_to_hour: formatTime(new Date()),
        approved_amount: 0,
        custom_approved_date: new Date(),
        custom_approved_start_from: new Date(),
        custom_approved_end_date: new Date(),
        custom_approved_from_hour: new Date(),
        custom_approved_to_hour: new Date(),
      },
      edit: {
        request_type_id: null,
        employee_id: null,
        last_status_id: null,

        start_from: formatDateOnly(new Date()),
        end_date: formatDateOnly(new Date()),
        date: formatDateOnly(new Date()),
        descriptions: "",
        amount: 0,
        from_hour: formatTime(new Date()),
        to_hour: formatTime(new Date()),
        custom_start_from: new Date(),
        custom_date: new Date(),
        custom_end_date: new Date(),
        custom_from_hour: new Date(),
        custom_to_hour: new Date(),
        approved_date: formatDateOnly(new Date()),
        approved_start_from: formatDateOnly(new Date()),
        approved_end_date: formatDateOnly(new Date()),
        approved_from_hour: formatTime(new Date()),
        approved_to_hour: formatTime(new Date()),
        approved_amount: 0,
        custom_approved_date: new Date(),
        custom_approved_start_from: new Date(),
        custom_approved_end_date: new Date(),
        custom_approved_from_hour: new Date(),
        custom_approved_to_hour: new Date(),
      },
      errors: {},
      isCheckAll: false,
      checkAll: [],
      current_page: 1,
      setting: {
        request_type_id: true,
        employee_id: true,
        start_from: true,
        end_date: true,
        descriptions: true,
        amount: true,
        from_hour: true,
        to_hour: true,
      },
      is_disabled: false,
      filterSetting: ["request_type_id", "employee_id"],
      Tooltip: "",
      company_id: null,
      mouseEnter: null,
      enabled3: true,
      printLoading: true,
      printObj: {
        id: "printBuilding",
      },
    };
  },
  validations: {
    create: {
      request_type_id: { required },
      last_status_id: { required },
      employee_id: { required },
      start_from: {
        required: requiredIf(function (model) {
          return (
            !this.create.request_type_id ||
            (this.create.request_type_id &&
              this.getRequestType(this.create.request_type_id).start_from)
          );
        }),
      },
      approved_start_from: {
        required: requiredIf(function (model) {
          return (
            !this.create.request_type_id ||
            (this.create.request_type_id &&
              this.getRequestType(this.create.request_type_id).start_from)
          );
        }),
      },
      end_date: {
        required: requiredIf(function (model) {
          return (
            !this.create.request_type_id ||
            (this.create.request_type_id &&
              this.getRequestType(this.create.request_type_id).end_date)
          );
        }),
      },
      approved_end_date: {
        required: requiredIf(function (model) {
          return (
            !this.create.request_type_id ||
            (this.create.request_type_id &&
              this.getRequestType(this.create.request_type_id).end_date)
          );
        }),
      },
      date: {},
      approved_date: { required },
      descriptions: { required },
      amount: {
        required: requiredIf(function (model) {
          return (
            !this.create.request_type_id ||
            (this.create.request_type_id &&
              this.getRequestType(this.create.request_type_id).amount)
          );
        }),
      },
      approved_amount: {
        required: requiredIf(function (model) {
          return (
            !this.create.request_type_id ||
            (this.create.request_type_id &&
              this.getRequestType(this.create.request_type_id).amount)
          );
        }),
      },
      from_hour: {
        required: requiredIf(function (model) {
          return (
            !this.create.request_type_id ||
            (this.create.request_type_id &&
              this.getRequestType(this.create.request_type_id).from_hour)
          );
        }),
      },
      approved_from_hour: {
        required: requiredIf(function (model) {
          return (
            !this.create.request_type_id ||
            (this.create.request_type_id &&
              this.getRequestType(this.create.request_type_id).from_hour)
          );
        }),
      },
      to_hour: {
        required: requiredIf(function (model) {
          return (
            !this.create.request_type_id ||
            (this.create.request_type_id &&
              this.getRequestType(this.create.request_type_id).to_hour)
          );
        }),
      },
      approved_to_hour: {
        required: requiredIf(function (model) {
          return (
            !this.create.request_type_id ||
            (this.create.request_type_id &&
              this.getRequestType(this.create.request_type_id).to_hour)
          );
        }),
      },
    },
    edit: {
      last_status_id: { required },
      request_type_id: { required },
      employee_id: { required },
      date: {},
      approved_date: { required },
      start_from: {
        required: requiredIf(function (model) {
          return (
            !this.edit.request_type_id ||
            (this.edit.request_type_id &&
              this.getRequestType(this.edit.request_type_id).start_from)
          );
        }),
      },
      approved_start_from: {
        required: requiredIf(function (model) {
          return (
            !this.edit.request_type_id ||
            (this.edit.request_type_id &&
              this.getRequestType(this.edit.request_type_id).start_from)
          );
        }),
      },
      end_date: {
        required: requiredIf(function (model) {
          return (
            !this.edit.request_type_id ||
            (this.edit.request_type_id &&
              this.getRequestType(this.edit.request_type_id).end_date)
          );
        }),
      },
      approved_end_date: {
        required: requiredIf(function (model) {
          return (
            !this.edit.request_type_id ||
            (this.edit.request_type_id &&
              this.getRequestType(this.edit.request_type_id).end_date)
          );
        }),
      },
      descriptions: { required },
      amount: {
        required: requiredIf(function (model) {
          return (
            !this.edit.request_type_id ||
            (this.edit.request_type_id &&
              this.getRequestType(this.edit.request_type_id).amount)
          );
        }),
      },
      approved_amount: {
        required: requiredIf(function (model) {
          return (
            !this.edit.request_type_id ||
            (this.edit.request_type_id &&
              this.getRequestType(this.edit.request_type_id).amount)
          );
        }),
      },
      from_hour: {
        required: requiredIf(function (model) {
          return (
            !this.edit.request_type_id ||
            (this.edit.request_type_id &&
              this.getRequestType(this.edit.request_type_id).from_hour)
          );
        }),
      },
      approved_from_hour: {
        required: requiredIf(function (model) {
          return (
            !this.edit.request_type_id ||
            (this.edit.request_type_id &&
              this.getRequestType(this.edit.request_type_id).from_hour)
          );
        }),
      },
      to_hour: {
        required: requiredIf(function (model) {
          return (
            !this.edit.request_type_id ||
            (this.edit.request_type_id &&
              this.getRequestType(this.edit.request_type_id).to_hour)
          );
        }),
      },
      approved_to_hour: {
        required: requiredIf(function (model) {
          return (
            !this.edit.request_type_id ||
            (this.edit.request_type_id &&
              this.getRequestType(this.edit.request_type_id).to_hour)
          );
        }),
      },
    },
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
        this.onlineRequests.forEach((el) => {
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
    this.company_id = this.$store.getters["auth/company_id"];
    this.getData();
  },
  // updated() {
  //   $(function () {
  //     $(".englishInput").keypress(function (event) {
  //       var ew = event.which;
  //       if (ew == 32) return true;
  //       if (48 <= ew && ew <= 57) return true;
  //       if (65 <= ew && ew <= 90) return true;
  //       if (97 <= ew && ew <= 122) return true;
  //       return false;
  //     });
  //     $(".arabicInput").keypress(function (event) {
  //       var ew = event.which;
  //       if (ew == 32) return true;
  //       if (48 <= ew && ew <= 57) return true;
  //       if (65 <= ew && ew <= 90) return false;
  //       if (97 <= ew && ew <= 122) return false;
  //       return true;
  //     });
  //   });
  // },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      if (
        vm.$store.state.auth.work_flow_trees.includes("hr") ||
        vm.$store.state.auth.user.type == "super_admin"
      ) {
        return true;
      } else {
        return vm.$router.push({ name: "home" });
      }
    });
  },
  methods: {
    getRequestType(id) {
      let rt = this.requestTypes.filter((t) => t.id == id);
      return rt.length > 0 ? rt[0] : null;
    },
    async getEmployees() {
      await adminApi
        .get(`/employees`)
        .then((res) => {
          this.employees = res.data.data;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    async getStatuses() {
      await adminApi
        .get(`/employees`)
        .then((res) => {
          this.statuses = res.data.data;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    async getRequestTypes() {
      await adminApi
        .get(`/hr/request-types`)
        .then((res) => {
          console.log(res.data.data);
          this.requestTypes = res.data.data;
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
     *  start get Data countrie && pagination
     */
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      let _filterSetting = [];
      let index = this.filterSetting.indexOf("employee_id");
      if (index > -1) {
        _filterSetting[index] =
          this.$i18n.locale == "ar" ? "employee.name" : "employee.name_e";
      }
      index = this.filterSetting.indexOf("request_type_id");
      if (index > -1) {
        _filterSetting[index] =
          this.$i18n.locale == "ar" ? "requestType.name" : "requestType.name_e";
      }
      for (let i = 0; i < _filterSetting.length; ++i) {
        filter += `columns[${i}]=${_filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/hr/online-requests?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.onlineRequests = l.data;
          this.onlineRequestsPagination = l.pagination;
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
        this.current_page <= this.onlineRequestsPagination.last_page &&
        this.current_page != this.onlineRequestsPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/hr/online-requests?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.onlineRequests = l.data;
            this.onlineRequestsPagination = l.pagination;
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
    deleteFinancialYear(id, index) {
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
              .post(`/hr/online-requests/bulk-delete`, { ids: id })
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
              .delete(`/hr/online-requests/${id}`)
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
        request_type_id: null,
        employee_id: null,
        last_status_id: null,
        start_from: formatDateOnly(new Date()),
        end_date: formatDateOnly(new Date()),
        date: formatDateOnly(new Date()),
        descriptions: "",
        amount: 0,
        from_hour: formatTime(new Date()),
        to_hour: formatTime(new Date()),
        custom_start_from: new Date(),
        custom_end_date: new Date(),
        custom_date: new Date(),
        custom_from_hour: new Date(),
        custom_to_hour: new Date(),
        approved_date: formatDateOnly(new Date()),
        approved_start_from: formatDateOnly(new Date()),
        approved_end_date: formatDateOnly(new Date()),
        approved_from_hour: formatTime(new Date()),
        approved_to_hour: formatTime(new Date()),
        approved_amount: 0,
        custom_approved_date: new Date(),
        custom_approved_start_from: new Date(),
        custom_approved_end_date: new Date(),
        custom_approved_from_hour: new Date(),
        custom_approved_to_hour: new Date(),
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.is_disabled = false;
      this.$bvModal.hide(`create`);
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      await this.getEmployees();
      await this.getRequestTypes();
      await this.getStatuses();
      this.create = {
        request_type_id: null,
        last_status_id: null,
        employee_id: null,
        start_from: formatDateOnly(new Date()),
        end_date: formatDateOnly(new Date()),
        date: formatDateOnly(new Date()),
        descriptions: "",
        amount: 0,
        from_hour: formatTime(new Date()),
        to_hour: formatTime(new Date()),
        custom_start_from: new Date(),
        custom_end_date: new Date(),
        custom_date: new Date(),
        custom_from_hour: new Date(),
        custom_to_hour: new Date(),
        approved_date: formatDateOnly(new Date()),
        approved_start_from: formatDateOnly(new Date()),
        approved_end_date: formatDateOnly(new Date()),
        approved_from_hour: formatTime(new Date()),
        approved_to_hour: formatTime(new Date()),
        approved_amount: 0,
        custom_approved_date: new Date(),
        custom_approved_start_from: new Date(),
        custom_approved_end_date: new Date(),
        custom_approved_from_hour: new Date(),
        custom_approved_to_hour: new Date(),
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create countrie
     */
    resetForm() {
      this.create = {
        request_type_id: null,
        last_status_id: null,

        employee_id: null,
        start_from: formatDateOnly(new Date()),
        end_date: formatDateOnly(new Date()),
        descriptions: "",
        amount: 0,
        from_hour: formatTime(new Date()),
        to_hour: formatTime(new Date()),
        custom_start_from: new Date(),
        custom_end_date: new Date(),
        custom_from_hour: new Date(),
        custom_to_hour: new Date(),
        approved_date: formatDateOnly(new Date()),
        approved_start_from: formatDateOnly(new Date()),
        approved_end_date: formatDateOnly(new Date()),
        approved_from_hour: formatTime(new Date()),
        approved_to_hour: formatTime(new Date()),
        approved_amount: 0,
        custom_approved_date: new Date(),
        custom_approved_start_from: new Date(),
        custom_approved_end_date: new Date(),
        custom_approved_from_hour: new Date(),
        custom_approved_to_hour: new Date(),
      };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },

    AddSubmit() {
      this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        adminApi
          .post(`/hr/online-requests`, this.create)
          .then((res) => {
            this.is_disabled = true;
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
      this.$v.edit.$touch();

      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        adminApi
          .put(`/hr/online-requests/${id}`, this.edit)
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
      await this.getEmployees();
      await this.getRequestTypes();
      let financialYear = this.onlineRequests.find((e) => id == e.id);
      this.edit.request_type_id = financialYear.request_type_id;
      this.edit.last_status_id = financialYear.last_status_id;
      this.edit.employee_id = financialYear.employee_id;
      this.edit.descriptions = financialYear.descriptions;
      this.edit.amount = financialYear.amount;
      this.edit.approved_amount = financialYear.approved_amount;
      this.edit.start_from = this.formatDate(financialYear.start_from);
      this.edit.end_date = this.formatDate(financialYear.end_date);
      this.edit.date = this.formatDate(financialYear.date);
      this.edit.from_hour = financialYear.from_hour;
      this.edit.to_hour = financialYear.to_hour;
      this.edit.approved_date = this.formatDate(financialYear.approved_date);
      this.edit.approved_start_from = this.formatDate(
        financialYear.approved_start_from
      );
      this.edit.approved_end_date = this.formatDate(
        financialYear.approved_end_date
      );
      this.edit.approved_from_hour = financialYear.approved_from_hour;
      this.edit.approved_to_hour = financialYear.approved_to_hour;
      this.edit.custom_start_from = new Date(financialYear.start_from);
      this.edit.custom_end_date = new Date(financialYear.end_date);
      this.edit.custom_date = new Date(financialYear.date);
      this.edit.custom_approved_date = new Date(financialYear.approved_date);
      this.edit.custom_approved_start_from = new Date(
        financialYear.approved_start_from
      );
      this.edit.custom_approved_end_date = new Date(
        financialYear.approved_end_date
      );
      let currDate = new Date();
      currDate.setHours(
        financialYear.from_hour.split(":")[0],
        financialYear.from_hour.split(":")[1],
        financialYear.from_hour.split(":")[2]
      );
      this.edit.custom_from_hour = currDate;
      this.edit.custom_to_hour = currDate;
      this.edit.custom_approved_from_hour = currDate;
      this.edit.custom_approved_to_hour = currDate;
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.errors = {};
      this.edit = {
        request_type_id: null,
        employee_id: null,
        last_status_id: null,
        start_from: formatDateOnly(new Date()),
        end_date: formatDateOnly(new Date()),
        date: formatDateOnly(new Date()),
        descriptions: "",
        amount: 0,
        from_hour: formatTime(new Date()),
        to_hour: formatTime(new Date()),
        custom_start_from: new Date(),
        custom_end_date: new Date(),
        custom_date: new Date(),
        custom_from_hour: new Date(),
        custom_to_hour: new Date(),
        approved_date: formatDateOnly(new Date()),
        approved_start_from: formatDateOnly(new Date()),
        approved_end_date: formatDateOnly(new Date()),
        approved_from_hour: formatTime(new Date()),
        approved_to_hour: formatTime(new Date()),
        approved_amount: 0,
        custom_approved_date: new Date(),
        custom_approved_start_from: new Date(),
        custom_approved_end_date: new Date(),
        custom_approved_from_hour: new Date(),
        custom_approved_to_hour: new Date(),
      };
    },
    /*
     *  start  dynamicSortString
     */
    sortString(value) {
      return dynamicSortString(value);
    },
    SortDate(value) {
      return dynamicSortDate(value);
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
     * watch start_date end date
     */
    start_from(e) {
      if (e) {
        this.create.start_from = formatDateOnly(e);
        this.edit.start_from = formatDateOnly(e);
      } else {
        this.create.start_from = null;
        this.edit.start_from = null;
      }
    },
    end_date(e) {
      if (e) {
        this.create.end_date = formatDateOnly(e);
        this.edit.end_date = formatDateOnly(e);
      } else {
        this.create.end_date = null;
        this.edit.end_date = null;
      }
    },
    date(e) {
      if (e) {
        this.create.date = formatDateOnly(e);
        this.edit.date = formatDateOnly(e);
      } else {
        this.create.date = null;
        this.edit.date = null;
      }
    },
    approved_date(e) {
      if (e) {
        this.create.approved_date = formatDateOnly(e);
        this.edit.approved_date = formatDateOnly(e);
      } else {
        this.create.approved_date = null;
        this.edit.approved_date = null;
      }
    },
    approved_start_from(e) {
      if (e) {
        this.create.approved_start_from = formatDateOnly(e);
        this.edit.approved_start_from = formatDateOnly(e);
      } else {
        this.create.approved_start_from = null;
        this.edit.approved_start_from = null;
      }
    },
    approved_end_date(e) {
      if (e) {
        this.create.approved_end_date = formatDateOnly(e);
        this.edit.approved_end_date = formatDateOnly(e);
      } else {
        this.create.approved_end_date = null;
        this.edit.approved_end_date = null;
      }
    },
    to_hour(e) {
      if (e) {
        this.create.to_hour = formatTime(e);
        this.edit.to_hour = formatTime(e);
      } else {
        this.create.to_hour = null;
        this.edit.to_hour = null;
      }
    },
    from_hour(e) {
      if (e) {
        this.create.from_hour = formatTime(e);
        this.edit.from_hour = formatTime(e);
      } else {
        this.create.from_hour = null;
        this.edit.from_hour = null;
      }
    },
    approved_to_hour(e) {
      if (e) {
        this.create.approved_to_hour = formatTime(e);
        this.edit.approved_to_hour = formatTime(e);
      } else {
        this.create.approved_to_hour = null;
        this.edit.approved_to_hour = null;
      }
    },
    approved_from_hour(e) {
      if (e) {
        this.create.approved_from_hour = formatTime(e);
        this.edit.approved_from_hour = formatTime(e);
      } else {
        this.create.approved_from_hour = null;
        this.edit.approved_from_hour = null;
      }
    },

    formatDate(value) {
      return formatDateOnly(value);
    },
    formatTime(value) {
      return formatTime(value);
    },

    log(id) {
      if (this.mouseEnter != id) {
        this.Tooltip = "";
        this.mouseEnter = id;
        adminApi
          .get(`/hr/online-requests/logs/${id}`)
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
            fn ||
              ("FinanicalYear" + "." || "SheetJSTableExport.") +
                (type || "xlsx")
          );
        }
        this.enabled3 = true;
      }, 100);
    },
    arabicValueName(txt) {
      this.create.name = arabicValue(txt);
      this.edit.name = arabicValue(txt);
    },
    englishValueName(txt) {
      this.create.name_e = englishValue(txt);
      this.edit.name_e = englishValue(txt);
    },
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">
                {{ $t("general.onlineRequestsTable") }}
              </h4>
              <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                <div class="d-inline-block" style="width: 22.2%">
                  <!-- Basic dropdown -->
                  <b-dropdown
                    variant="primary"
                    :text="$t('general.searchSetting')"
                    ref="dropdown"
                    class="btn-block setting-search"
                  >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="request_type_id"
                      class="mb-1"
                      >{{ getCompanyKey("request_type") }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="employee_id"
                      class="mb-1"
                      >{{ getCompanyKey("employee") }}</b-form-checkbox
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
                  v-b-modal.create
                  variant="primary"
                  class="btn-sm mx-1 font-weight-bold"
                >
                  {{ $t("general.Create") }}
                  <i class="fas fa-plus"></i>
                </b-button>
                <div class="d-inline-flex">
                  <button
                    class="custom-btn-dowonload"
                    @click="ExportExcel('xlsx')"
                  >
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button class="custom-btn-dowonload" v-print="'#printCustom'">
                    <i class="fe-printer"></i>
                  </button>
                  <button
                    class="custom-btn-dowonload"
                    @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                    v-if="checkAll.length == 1"
                  >
                    <i class="mdi mdi-square-edit-outline"></i>
                  </button>
                  <!-- start mult delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length > 1"
                    @click.prevent="deleteFinancialYear(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1"
                    @click.prevent="deleteFinancialYear(checkAll[0])"
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
                      class="dropdown-custom-ali"
                    >
                      <b-form-checkbox
                        v-model="setting.request_type_id"
                        class="mb-1"
                        >{{ getCompanyKey("request_type") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.employee_id"
                        class="mb-1"
                      >
                        {{ getCompanyKey("employee") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.start_from"
                        class="mb-1"
                      >
                        {{ getCompanyKey("online_request_start_from") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.end_date" class="mb-1">
                        {{ getCompanyKey("online_request_end_date") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.descriptions"
                        class="mb-1"
                      >
                        {{ getCompanyKey("online_request_descriptions") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.amount" class="mb-1">
                        {{ getCompanyKey("online_request_amount") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.from_hour" class="mb-1">
                        {{ getCompanyKey("online_request_from_hour") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.to_hour" class="mb-1">
                        {{ getCompanyKey("online_request_to_hour") }}
                      </b-form-checkbox>
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
                      {{ onlineRequestsPagination.from }}-{{
                        onlineRequestsPagination.to
                      }}
                      / {{ onlineRequestsPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            onlineRequestsPagination.current_page == 1
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(onlineRequestsPagination.current_page - 1)
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
                            onlineRequestsPagination.last_page ==
                            onlineRequestsPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(onlineRequestsPagination.current_page + 1)
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
              dialog-class="modal-full-width"
              id="create"
              :title="getCompanyKey('online_request_create_form')"
              title-class="font-18"
              body-class="p-4"
              :hide-footer="true"
              @show="resetModal"
              @hidden="resetModalHidden"
            >
              <form>
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
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">
                        {{ getCompanyKey("online_request_date") }}
                        <span class="text-danger">*</span>
                      </label>
                      <date-picker
                        :disabled="true"
                        v-model="create.custom_date"
                        type="date"
                        :default-value="create.custom_date"
                        @change="date"
                        confirm
                      ></date-picker>
                      <div
                        v-if="!$v.create.date.required"
                        class="invalid-feedback"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.date">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.date"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group position-relative">
                      <label class="control-label">
                        {{ getCompanyKey("request_type") }}
                        <span class="text-danger">*</span>
                      </label>
                      <multiselect
                        v-model="create.request_type_id"
                        :options="requestTypes.map((type) => type.id)"
                        :custom-label="
                          (opt) => requestTypes.find((x) => x.id == opt).name
                        "
                      >
                      </multiselect>
                      <div
                        v-if="
                          $v.create.request_type_id.$error ||
                          errors.request_type_id
                        "
                        class="text-danger"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.request_type_id">
                        <ErrorMessage
                          v-for="(
                            errorMessage, index
                          ) in errors.request_type_id"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                   <div class="col-md-4">
                    <div class="form-group position-relative">
                      <label class="control-label">
                        {{ getCompanyKey("online_request_last_status") }}
                        <span class="text-danger">*</span>
                      </label>
                      <multiselect
                        :disabled="true"
                        v-model="create.last_status_id"
                        :options="statuses.map((type) => type.id)"
                        :custom-label="
                          (opt) => statuses.find((x) => x.id == opt).name
                        "
                      >
                      </multiselect>
                      <div
                        v-if="
                          $v.create.last_status_id.$error ||
                          errors.last_status_id
                        "
                        class="text-danger"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.last_status_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.last_status_id"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group position-relative">
                      <label class="control-label">
                        {{ getCompanyKey("employee") }}
                        <span class="text-danger">*</span>
                      </label>
                      <multiselect
                        v-model="create.employee_id"
                        :options="employees.map((type) => type.id)"
                        :custom-label="
                          (opt) => employees.find((x) => x.id == opt).name
                        "
                      >
                      </multiselect>
                      <div
                        v-if="
                          $v.create.employee_id.$error || errors.employee_id
                        "
                        class="text-danger"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.employee_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.employee_id"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div
                    v-if="
                      !create.request_type_id ||
                      (create.request_type_id &&
                        getRequestType(create.request_type_id).start_from)
                    "
                    class="col-md-4"
                  >
                    <div class="form-group">
                      <label class="control-label">
                        {{ getCompanyKey("online_request_start_from") }}
                        <span class="text-danger">*</span>
                      </label>
                      <date-picker
                        v-model="create.custom_start_from"
                        type="date"
                        :default-value="create.custom_start_from"
                        @change="start_from"
                        confirm
                      ></date-picker>
                      <div
                        v-if="!$v.create.start_from.required"
                        class="invalid-feedback"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.start_from">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.start_from"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div
                    v-if="
                      !create.request_type_id ||
                      (create.request_type_id &&
                        getRequestType(create.request_type_id).end_date)
                    "
                    class="col-md-4"
                  >
                    <div class="form-group">
                      <label class="control-label">
                        {{ getCompanyKey("online_request_end_date") }}
                        <span class="text-danger">*</span>
                      </label>
                      <date-picker
                        v-model="create.custom_end_date"
                        type="date"
                        @change="end_date"
                        confirm
                      ></date-picker>
                      <div
                        v-if="!$v.create.end_date.required"
                        class="invalid-feedback"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.end_date">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.end_date"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div
                    v-if="
                      !create.request_type_id ||
                      (create.request_type_id &&
                        getRequestType(create.request_type_id).from_hour)
                    "
                    class="col-md-4"
                  >
                    <div class="form-group">
                      <label class="control-label">
                        {{ getCompanyKey("online_request_from_hour") }}
                        <span class="text-danger">*</span>
                      </label>
                      <date-picker
                        v-model="create.custom_from_hour"
                        type="time"
                        :default-value="create.custom_from_hour"
                        @change="from_hour"
                        confirm
                      ></date-picker>
                      <div
                        v-if="!$v.create.from_hour.required"
                        class="invalid-feedback"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.from_hour">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.from_hour"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div
                    v-if="
                      !create.request_type_id ||
                      (create.request_type_id &&
                        getRequestType(create.request_type_id).to_hour)
                    "
                    class="col-md-4"
                  >
                    <div class="form-group">
                      <label class="control-label">
                        {{ getCompanyKey("online_request_to_hour") }}
                        <span class="text-danger">*</span>
                      </label>
                      <date-picker
                        v-model="create.custom_to_hour"
                        type="time"
                        :default-value="create.custom_to_hour"
                        @change="to_hour"
                        confirm
                      ></date-picker>
                      <div
                        v-if="!$v.create.to_hour.required"
                        class="invalid-feedback"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.to_hour">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.to_hour"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div
                    v-if="
                      !create.request_type_id ||
                      (create.request_type_id &&
                        getRequestType(create.request_type_id).amount)
                    "
                    class="col-md-4"
                  >
                    <div class="form-group">
                      <label for="create-20" class="control-label">
                        {{ getCompanyKey("online_request_amount") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input
                        type="number"
                        class="form-control"
                        v-model="$v.create.amount.$model"
                        :class="{
                          'is-invalid':
                            $v.create.amount.$error || errors.amount,
                          'is-valid': !$v.create.amount.$invalid,
                        }"
                        id="create-20"
                      />
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="create-20" class="control-label">
                        {{ getCompanyKey("online_request_descriptions") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input
                        type="text"
                        class="form-control"
                        v-model="$v.create.descriptions.$model"
                        :class="{
                          'is-invalid':
                            $v.create.descriptions.$error ||
                            errors.descriptions,
                          'is-valid': !$v.create.descriptions.$invalid,
                        }"
                        id="create-20"
                      />
                    </div>
                  </div>
                </div>
                <hr class="line" />
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">
                        {{ getCompanyKey("online_request_approved_date") }}
                        <span class="text-danger">*</span>
                      </label>
                      <date-picker
                        :disabled="true"
                        v-model="create.custom_approved_date"
                        type="date"
                        :default-value="create.custom_approved_date"
                        @change="approved_date"
                        confirm
                      ></date-picker>
                      <div
                        v-if="!$v.create.approved_date.required"
                        class="invalid-feedback"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.approved_date">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.approved_date"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div
                    v-if="
                      !create.request_type_id ||
                      (create.request_type_id &&
                        getRequestType(create.request_type_id).start_from)
                    "
                    class="col-md-4"
                  >
                    <div class="form-group">
                      <label class="control-label">
                        {{
                          getCompanyKey("online_request_approved_start_from")
                        }}
                        <span class="text-danger">*</span>
                      </label>
                      <date-picker
                        :disabled="true"
                        v-model="create.custom_approved_start_from"
                        type="date"
                        :default-value="create.custom_approved_start_from"
                        @change="approved_start_from"
                        confirm
                      ></date-picker>
                      <div
                        v-if="!$v.create.approved_start_from.required"
                        class="invalid-feedback"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.approved_start_from">
                        <ErrorMessage
                          v-for="(
                            errorMessage, index
                          ) in errors.approved_start_from"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div
                    v-if="
                      !create.request_type_id ||
                      (create.request_type_id &&
                        getRequestType(create.request_type_id).end_date)
                    "
                    class="col-md-4"
                  >
                    <div class="form-group">
                      <label class="control-label">
                        {{ getCompanyKey("online_request_approved_end_date") }}
                        <span class="text-danger">*</span>
                      </label>
                      <date-picker
                        :disabled="true"
                        v-model="create.custom_approved_end_date"
                        type="date"
                        :default-value="create.custom_approved_end_date"
                        @change="approved_end_date"
                        confirm
                      ></date-picker>
                      <div
                        v-if="!$v.create.approved_end_date.required"
                        class="invalid-feedback"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.approved_end_date">
                        <ErrorMessage
                          v-for="(
                            errorMessage, index
                          ) in errors.approved_end_date"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div
                    v-if="
                      !create.request_type_id ||
                      (create.request_type_id &&
                        getRequestType(create.request_type_id).from_hour)
                    "
                    class="col-md-4"
                  >
                    <div class="form-group">
                      <label class="control-label">
                        {{ getCompanyKey("online_request_approved_from_hour") }}
                        <span class="text-danger">*</span>
                      </label>
                      <date-picker
                        v-model="create.custom_approved_from_hour"
                        type="time"
                        :default-value="create.custom_approved_from_hour"
                        @change="approved_from_hour"
                        confirm
                      ></date-picker>
                      <div
                        v-if="!$v.create.approved_from_hour.required"
                        class="invalid-feedback"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.approved_from_hour">
                        <ErrorMessage
                          v-for="(
                            errorMessage, index
                          ) in errors.approved_from_hour"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div
                    v-if="
                      !create.request_type_id ||
                      (create.request_type_id &&
                        getRequestType(create.request_type_id).to_hour)
                    "
                    class="col-md-4"
                  >
                    <div class="form-group">
                      <label class="control-label">
                        {{ getCompanyKey("online_request_approved_to_hour") }}
                        <span class="text-danger">*</span>
                      </label>
                      <date-picker
                        v-model="create.custom_approved_to_hour"
                        type="time"
                        :default-value="create.custom_approved_to_hour"
                        @change="approved_to_hour"
                        confirm
                      ></date-picker>
                      <div
                        v-if="!$v.create.approved_to_hour.required"
                        class="invalid-feedback"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.approved_to_hour">
                        <ErrorMessage
                          v-for="(
                            errorMessage, index
                          ) in errors.approved_to_hour"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div
                    v-if="
                      !create.request_type_id ||
                      (create.request_type_id &&
                        getRequestType(create.request_type_id).amount)
                    "
                    class="col-md-4"
                  >
                    <div class="form-group">
                      <label for="create-20" class="control-label">
                        {{ getCompanyKey("online_request_approved_amount") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input
                        type="number"
                        class="form-control"
                        v-model="$v.create.approved_amount.$model"
                        :class="{
                          'is-invalid':
                            $v.create.approved_amount.$error ||
                            errors.approved_amount,
                          'is-valid': !$v.create.approved_amount.$invalid,
                        }"
                        id="create-20"
                      />
                    </div>
                  </div>
                </div>
              </form>
            </b-modal>
            <!--  /create   -->

            <!-- start .table-responsive-->
            <div
              class="table-responsive mb-3 custom-table-theme position-relative"
              ref="exportable_table"
              id="printCustom"
            >
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->

              <table
                class="table table-borderless table-hover table-centered m-0"
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
                    <th v-if="setting.request_type_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("request_type") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.employee_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("employee") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(sortString('-name_e'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.start_from">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_start_from")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(SortDate('start_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              onlineRequests.sort(SortDate('-start_date'))
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.end_date">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_end_date")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(SortDate('end_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(SortDate('-end_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.descriptions">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_descriptions")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(SortDate('end_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(SortDate('-end_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.amount">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_amount")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(SortDate('end_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(SortDate('-end_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.from_hour">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_from_hour")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(SortDate('end_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(SortDate('-end_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.to_hour">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_to_hour")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(SortDate('end_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(SortDate('-end_date'))"
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
                <tbody v-if="onlineRequests.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in onlineRequests"
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
                          :value="data.id"
                          v-model="checkAll"
                        />
                      </div>
                    </td>
                    <td v-if="setting.request_type_id">
                      <h5
                        v-if="data.request_type"
                        class="m-0 font-weight-normal"
                      >
                        {{
                          $i18n.locale == "ar"
                            ? data.request_type.name
                            : data.request_type.name_e
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.employee_id">
                      <h5 v-if="data.employee" class="m-0 font-weight-normal">
                        {{
                          $i18n.locale == "ar"
                            ? data.employee.name
                            : data.employee.name_e
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.start_from">
                      <h5 class="m-0 font-weight-normal">
                        {{ formatDate(data.start_from) }}
                      </h5>
                    </td>
                    <td v-if="setting.end_date">
                      <h5 class="m-0 font-weight-normal">
                        {{ formatDate(data.end_date) }}
                      </h5>
                    </td>
                    <td v-if="setting.descriptions">
                      <h5 class="m-0 font-weight-normal">
                        {{ data.descriptions }}
                      </h5>
                    </td>
                    <td v-if="setting.amount">
                      <h5 class="m-0 font-weight-normal">{{ data.amount }}</h5>
                    </td>
                    <td v-if="setting.from_hour">
                      <h5 class="m-0 font-weight-normal">
                        {{ data.from_hour }}
                      </h5>
                    </td>
                    <td v-if="setting.to_hour">
                      <h5 class="m-0 font-weight-normal">
                        {{ data.to_hour }}
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
                            class="dropdown-item text-black"
                            href="#"
                            @click.prevent="deleteFinancialYear(data.id)"
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
                        :title="getCompanyKey('online_request_edit_form')"
                        title-class="font-18"
                        body-class="p-4"
                        dialog-class="modal-full-width"
                        :ref="`edit-${data.id}`"
                        :hide-footer="true"
                        @show="resetModalEdit(data.id)"
                        @hidden="resetModalHiddenEdit(data.id)"
                      >
                        <form>
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
                          <div class="row">
                            <div class="col-md-4">
                              <div class="form-group">
                                <label class="control-label">
                                  {{ getCompanyKey("online_request_date") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <date-picker
                                  :disabled="true"
                                  v-model="edit.custom_date"
                                  type="date"
                                  :default-value="edit.custom_date"
                                  @change="date"
                                  confirm
                                ></date-picker>
                                <div
                                  v-if="!$v.edit.date.required"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.date">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.date"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group position-relative">
                                <label class="control-label">
                                  {{ getCompanyKey("request_type") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <multiselect
                                  v-model="edit.request_type_id"
                                  :options="requestTypes.map((type) => type.id)"
                                  :custom-label="
                                    (opt) =>
                                      requestTypes.find((x) => x.id == opt).name
                                  "
                                >
                                </multiselect>
                                <div
                                  v-if="
                                    $v.edit.request_type_id.$error ||
                                    errors.request_type_id
                                  "
                                  class="text-danger"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.request_type_id">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.request_type_id"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group position-relative">
                                <label class="control-label">
                                  {{
                                    getCompanyKey("online_request_last_status")
                                  }}
                                  <span class="text-danger">*</span>
                                </label>
                                <multiselect
                                :disabled="true"
                                  v-model="edit.last_status_id"
                                  :options="statuses.map((type) => type.id)"
                                  :custom-label="
                                    (opt) =>
                                      statuses.find((x) => x.id == opt).name
                                  "
                                >
                                </multiselect>
                                <div
                                  v-if="
                                    $v.edit.last_status_id.$error ||
                                    errors.last_status_id
                                  "
                                  class="text-danger"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.last_status_id">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.last_status_id"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group position-relative">
                                <label class="control-label">
                                  {{ getCompanyKey("employee") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <multiselect
                                  v-model="edit.employee_id"
                                  :options="employees.map((type) => type.id)"
                                  :custom-label="
                                    (opt) =>
                                      employees.find((x) => x.id == opt).name
                                  "
                                >
                                </multiselect>
                                <div
                                  v-if="
                                    $v.edit.request_type_id.$error ||
                                    errors.request_type_id
                                  "
                                  class="text-danger"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.request_type_id">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.request_type_id"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            
                            <div
                              v-if="
                                !edit.request_type_id ||
                                (edit.request_type_id &&
                                  getRequestType(edit.request_type_id)
                                    .start_from)
                              "
                              class="col-md-4"
                            >
                              <div class="form-group">
                                <label class="control-label">
                                  {{
                                    getCompanyKey("online_request_start_from")
                                  }}
                                  <span class="text-danger">*</span>
                                </label>
                                <date-picker
                                  v-model="edit.custom_start_from"
                                  type="date"
                                  :default-value="edit.custom_start_from"
                                  @change="start_from"
                                  confirm
                                ></date-picker>
                                <div
                                  v-if="!$v.edit.start_from.required"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.start_from">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.start_from"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div
                              v-if="
                                !edit.request_type_id ||
                                (edit.request_type_id &&
                                  getRequestType(edit.request_type_id).end_date)
                              "
                              class="col-md-4"
                            >
                              <div class="form-group">
                                <label class="control-label">
                                  {{ getCompanyKey("online_request_end_date") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <date-picker
                                  v-model="edit.custom_end_date"
                                  type="date"
                                  @change="end_date"
                                  confirm
                                ></date-picker>
                                <div
                                  v-if="!$v.edit.end_date.required"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.end_date">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.end_date"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div
                              v-if="
                                !edit.request_type_id ||
                                (edit.request_type_id &&
                                  getRequestType(edit.request_type_id)
                                    .from_hour)
                              "
                              class="col-md-4"
                            >
                              <div class="form-group">
                                <label class="control-label">
                                  {{
                                    getCompanyKey("online_request_from_hour")
                                  }}
                                  <span class="text-danger">*</span>
                                </label>
                                <date-picker
                                  v-model="edit.custom_from_hour"
                                  type="time"
                                  :default-value="edit.custom_from_hour"
                                  @change="from_hour"
                                  confirm
                                ></date-picker>
                                <div
                                  v-if="!$v.edit.from_hour.required"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.from_hour">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.from_hour"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div
                              v-if="
                                !edit.request_type_id ||
                                (edit.request_type_id &&
                                  getRequestType(edit.request_type_id).to_hour)
                              "
                              class="col-md-4"
                            >
                              <div class="form-group">
                                <label class="control-label">
                                  {{ getCompanyKey("online_request_to_hour") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <date-picker
                                  v-model="edit.custom_to_hour"
                                  type="time"
                                  :default-value="edit.custom_to_hour"
                                  @change="to_hour"
                                  confirm
                                ></date-picker>
                                <div
                                  v-if="!$v.edit.to_hour.required"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.to_hour">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.to_hour"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div
                              v-if="
                                !edit.request_type_id ||
                                (edit.request_type_id &&
                                  getRequestType(edit.request_type_id).amount)
                              "
                              class="col-md-4"
                            >
                              <div class="form-group">
                                <label for="create-20" class="control-label">
                                  {{ getCompanyKey("online_request_amount") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <input
                                  type="number"
                                  class="form-control"
                                  v-model="$v.edit.amount.$model"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.amount.$error || errors.amount,
                                    'is-valid': !$v.edit.amount.$invalid,
                                  }"
                                  id="create-20"
                                />
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label for="create-20" class="control-label">
                                  {{
                                    getCompanyKey("online_request_descriptions")
                                  }}
                                  <span class="text-danger">*</span>
                                </label>
                                <input
                                  type="text"
                                  class="form-control"
                                  v-model="$v.edit.descriptions.$model"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.descriptions.$error ||
                                      errors.descriptions,
                                    'is-valid': !$v.edit.descriptions.$invalid,
                                  }"
                                  id="create-20"
                                />
                              </div>
                            </div>
                          </div>
                          <hr class="line" />
                          <div class="row">
                            <div class="col-md-4">
                              <div class="form-group">
                                <label class="control-label">
                                  {{
                                    getCompanyKey(
                                      "online_request_approved_date"
                                    )
                                  }}
                                  <span class="text-danger">*</span>
                                </label>
                                <date-picker
                                  :disabled="true"
                                  v-model="edit.custom_approved_date"
                                  type="date"
                                  :default-value="edit.custom_approved_date"
                                  @change="approved_date"
                                  confirm
                                ></date-picker>
                                <div
                                  v-if="!$v.edit.approved_date.required"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.approved_date">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.approved_date"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div
                              v-if="
                                !edit.request_type_id ||
                                (edit.request_type_id &&
                                  getRequestType(edit.request_type_id)
                                    .start_from)
                              "
                              class="col-md-4"
                            >
                              <div class="form-group">
                                <label class="control-label">
                                  {{
                                    getCompanyKey(
                                      "online_request_approved_start_from"
                                    )
                                  }}
                                  <span class="text-danger">*</span>
                                </label>
                                <date-picker
                                  :disabled="true"
                                  v-model="edit.custom_approved_start_from"
                                  type="date"
                                  :default-value="
                                    edit.custom_approved_start_from
                                  "
                                  @change="approved_start_from"
                                  confirm
                                ></date-picker>
                                <div
                                  v-if="!$v.edit.approved_start_from.required"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.approved_start_from">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.approved_start_from"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div
                              v-if="
                                !edit.request_type_id ||
                                (edit.request_type_id &&
                                  getRequestType(edit.request_type_id)
                                    .end_date)
                              "
                              class="col-md-4"
                            >
                              <div class="form-group">
                                <label class="control-label">
                                  {{
                                    getCompanyKey(
                                      "online_request_approved_end_date"
                                    )
                                  }}
                                  <span class="text-danger">*</span>
                                </label>
                                <date-picker
                                  :disabled="true"
                                  v-model="edit.custom_approved_end_date"
                                  type="date"
                                  :default-value="
                                    edit.custom_approved_end_date
                                  "
                                  @change="approved_end_date"
                                  confirm
                                ></date-picker>
                                <div
                                  v-if="!$v.edit.approved_end_date.required"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.approved_end_date">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.approved_end_date"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div
                              v-if="
                                !edit.request_type_id ||
                                (edit.request_type_id &&
                                  getRequestType(edit.request_type_id)
                                    .from_hour)
                              "
                              class="col-md-4"
                            >
                              <div class="form-group">
                                <label class="control-label">
                                  {{
                                    getCompanyKey(
                                      "online_request_approved_from_hour"
                                    )
                                  }}
                                  <span class="text-danger">*</span>
                                </label>
                                <date-picker
                                  v-model="edit.custom_approved_from_hour"
                                  type="time"
                                  :default-value="
                                    edit.custom_approved_from_hour
                                  "
                                  @change="approved_from_hour"
                                  confirm
                                ></date-picker>
                                <div
                                  v-if="!$v.edit.approved_from_hour.required"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.approved_from_hour">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.approved_from_hour"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div
                              v-if="
                                !edit.request_type_id ||
                                (edit.request_type_id &&
                                  getRequestType(edit.request_type_id)
                                    .to_hour)
                              "
                              class="col-md-4"
                            >
                              <div class="form-group">
                                <label class="control-label">
                                  {{
                                    getCompanyKey(
                                      "online_request_approved_to_hour"
                                    )
                                  }}
                                  <span class="text-danger">*</span>
                                </label>
                                <date-picker
                                  v-model="edit.custom_approved_to_hour"
                                  type="time"
                                  :default-value="
                                    edit.custom_approved_to_hour
                                  "
                                  @change="approved_to_hour"
                                  confirm
                                ></date-picker>
                                <div
                                  v-if="!$v.edit.approved_to_hour.required"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.approved_to_hour">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.approved_to_hour"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div
                              v-if="
                                !edit.request_type_id ||
                                (edit.request_type_id &&
                                  getRequestType(edit.request_type_id).amount)
                              "
                              class="col-md-4"
                            >
                              <div class="form-group">
                                <label for="create-20" class="control-label">
                                  {{
                                    getCompanyKey(
                                      "online_request_approved_amount"
                                    )
                                  }}
                                  <span class="text-danger">*</span>
                                </label>
                                <input
                                  type="number"
                                  class="form-control"
                                  v-model="$v.edit.approved_amount.$model"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.approved_amount.$error ||
                                      errors.approved_amount,
                                    'is-valid':
                                      !$v.edit.approved_amount.$invalid,
                                  }"
                                  id="create-20"
                                />
                              </div>
                            </div>
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
                    <th class="text-center" colspan="11">
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
<style scoped>
.line {
  border-bottom: 1px solid #fff !important;
  margin-top:.5rem !important;
  margin-bottom:.5rem !important;
}
</style>