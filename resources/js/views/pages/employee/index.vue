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
  dynamicSortString,
  dynamicSortNumber,
} from "../../../helper/tableSort";
import senderHoverHelper from "../../../helper/senderHoverHelper";
import { formatDateOnly } from "../../../helper/startDate";
import translation from "../../../helper/translation-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import Department from "../../../components/create/department";
import Multiselect from "vue-multiselect";
import SalemanType from "../../../components/create/salesManType.vue";
import SalemanPlan from "../../../components/create/salesmanPlan.vue";
/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Employee",
    meta: [{ name: "description", content: "Employee" }],
  },
  mixins: [translation],
  components: {
    SalemanPlan,
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    SalemanType,
    Multiselect,
    Department,
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      if (
        vm.$store.state.auth.work_flow_trees.includes("employees") ||
        vm.$store.state.auth.user.type == "super_admin"
      ) {
        return true;
      } else {
        return vm.$router.push({ name: "home" });
      }
    });
  },
  data() {
    return {
      fields: [],
      enabled3: true,
      printLoading: true,
      printObj: {
        id: "printBuilding",
      },
      per_page: 50,
      search: "",
      debounce: {},
      employeesPagination: {},
      employees: [],
      departments: [],
      isLoader: false,
      Tooltip: "",
      mouseEnter: null,
      company_id: null,
      salesmenTypes: [],
      managers: [],
      plans: [],
      create: {
        name: "",
        name_e: "",
        is_salesman: "active",
        customer_handel: "non_customer",
        department_id: null,
        manger_id: null,
        salesman_type_id: null,
        plan_id: [],
        manage_others: 1,
      },
      edit: {
        name: "",
        name_e: "",
        is_salesman: "active",
        customer_handel: "non_customer",
        department_id: null,
        manger_id: null,
        salesman_type_id: null,
        plan_id: [],
        manage_others: 1,
      },
      errors: {},
      isCheckAll: false,
      checkAll: [],
      current_page: 1,
      setting: {
        name: true,
        name_e: true,
        department_id: true,
      },
      is_disabled: false,
      filterSetting: [
        "name",
        "name_e",
        this.$i18n.locale == "ar" ? "department.name" : "department.name_e",
      ],
    };
  },
  validations: {
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
      department_id: {
        required: requiredIf(function (model) {
          return this.isRequired("department_id");
        }),
      },
      is_salesman: {
        required: requiredIf(function (model) {
          return this.isRequired("is_salesman");
        }),
      },
      manage_others: {
        required: requiredIf(function (model) {
          return this.isRequired("manage_others");
        }),
      },

      customer_handel: {
        required: requiredIf(function (model) {
          return this.isRequired("customer_handel");
        }),
      },

      manger_id: {},
      salesman_type_id: {
        required: requiredIf(function (model) {
          return (
            this.isRequired("salesman_type_id") &&
            this.create.is_salesman == "active"
          );
        }),
      },
      manage_others: {
        required: requiredIf(function (model) {
          return this.isRequired("manage_others");
        }),
      },

      plan_id: {
        required: requiredIf(function (model) {
          return this.create.is_salesman == "active";
        }),
      },
    },
    edit: {
      name: {
        required: requiredIf(function (model) {
          return this.isRequired("name");
        }),
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      is_salesman: {
        required: requiredIf(function (model) {
          return this.isRequired("is_salesman");
        }),
      },
      manage_others: {
        required: requiredIf(function (model) {
          return this.isRequired("is_salesman");
        }),
      },

      customer_handel: {
        required: requiredIf(function (model) {
          return this.isRequired("customer_handel");
        }),
      },
      name_e: {
        required: requiredIf(function (model) {
          return this.isRequired("name_e");
        }),
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      department_id: {
        required: requiredIf(function (model) {
          return this.isRequired("department_id");
        }),
      },
      manger_id: {},
      salesman_type_id: {
        required: requiredIf(function (model) {
          return (
            this.isRequired("salesman_type_id") &&
            this.edit.is_salesman == "active"
          );
        }),
      },
      plan_id: {
        required: requiredIf(function (model) {
          return this.edit.is_salesman == "active";
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
        this.employees.forEach((el) => {
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
    this.getCustomTableFields();
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
  methods: {
    async getSaleMenType() {
      this.isLoader = true;
      await adminApi
        .get(`/salesmen-types?is_empolyee=1`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({
            id: 0,
            name: "اضف نوع رجل المبيعات",
            name_e: "Add saleman type",
          });
          this.salesmenTypes = l;
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
    getCustomTableFields() {
      adminApi
        .get(`/customTable/table-columns/general_employees`)
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
    /**
     *  start get Data countrie && pagination
     */
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      for (let i = 0; i > this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/employees?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.employees = l.data;
          this.employeesPagination = l.pagination;
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
        this.current_page <= this.employeesPagination.last_page &&
        this.current_page != this.employeesPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i > this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/employees?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.employees = l.data;
            this.employeesPagination = l.pagination;
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
    async getDepartnent() {
      this.isLoader = true;

      await adminApi
        .get(`/depertments`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضف قسم", name_e: "Add Department" });
          this.departments = l;
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
    async getPlans() {
      this.isLoader = true;

      await adminApi
        .get(`/salesmen-plans`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضف خطة", name_e: "Add plan" });
          this.plans = l;
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
    async getManagers() {
      this.isLoader = true;
      await adminApi
        .get(`/employees?search=true&columns[0]=is_salesman`)
        .then((res) => {
          let l = res.data.data;
          this.managers = l;
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
    showDepartmentModal() {
      if (this.create.department_id == 0) {
        this.$bvModal.show("department-create");
        this.create.department_id = null;
      }
    },
    showDepartmentModalEdit() {
      if (this.edit.department_id == 0) {
        this.$bvModal.show("department-create");
        this.edit.department_id = null;
      }
    },
    showSalesManTypeModal() {
      if (this.create.salesman_type_id == 0) {
        this.$bvModal.show("sales-man-type-create");
        this.create.salesman_type_id = null;
      }
    },
    showSalesManTypeEditModal() {
      if (this.edit.salesman_type_id == 0) {
        this.$bvModal.show("sales-man-type-create");
        this.edit.salesman_type_id = null;
      }
    },
    showPlanModal() {
      let index = this.create.plan_id.findIndex((id) => id == 0);
      if (index > -1) {
        this.$bvModal.show("create-salesman-plan");
        this.create.plan_id.splice(index, 1);
      }
    },
    showPlanModalEdit() {
      let index = this.edit.plan_id.findIndex((id) => id == 0);

      if (index > -1) {
        this.$bvModal.show("create-salesman-plan");
        this.edit.plan_id.splice(index, 1);
      }
    },

    /**
     *  end get Data countrie && pagination
     */
    /**
     *  start delete countrie
     */
    deleteEmployee(id, index) {
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
              .post(`/employees/bulk-delete`, { ids: id })
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
              .delete(`/employees/${id}`)
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
        name_e: "",
        manage_others: 1,

        is_salesman: "active",
        customer_handel: "non_customer",

        department_id: null,
        manger_id: null,
        salesman_type_id: null,
        plan_id: [],
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
      await this.getDepartnent();
      await this.getManagers();
      await this.getPlans();
      await this.getSaleMenType();
      this.create = {
        name: "",
        name_e: "",

        department_id: null,
        is_salesman: "active",
        manage_others: 1,
        customer_handel: "non_customer",

        manger_id: null,
        salesman_type_id: null,
        plan_id: [],
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
        name: "",
        name_e: "",
        manage_others: 1,
        department_id: null,
        is_salesman: "active",
        customer_handel: "non_customer",
        manger_id: null,
        salesman_type_id: null,
        plan_id: [],
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_disabled = false;
      this.errors = {};
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
        adminApi
          .post(`/employees`, {
            ...this.create,
            plans: this.create.plan_id,
            company_id: this.company_id,
            is_salesman: this.create.is_salesman == "active" ? "true" : "false",
          })
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
      if (!this.edit.name) {
        this.edit.name = this.edit.name_e;
      }
      if (!this.edit.name_e) {
        this.edit.name_e = this.edit.name;
      }
      this.$v.edit.$touch();

      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        let { name, name_e } = this.edit;
        adminApi
          .put(`/employees/${id}`, {
            ...this.edit,
            plans: this.edit.plan_id,
            is_salesman: this.edit.is_salesman == "active" ? "true" : "false",
          })
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
      await this.getDepartnent();
      await this.getManagers();
      await this.getPlans();
      await this.getSaleMenType();
      let employee = this.employees.find((e) => id == e.id);
      this.edit.name = employee.name;
      this.edit.name_e = employee.name_e;
      this.edit.customer_handel = employee.customer_handel;
      this.edit.department_id = employee.department.id;
      this.edit.salesman_type_id = employee.salesman_type.id;
      this.edit.is_salesman =
        employee.is_salesman == "true" ? "active" : "inactive";
      this.edit.manger_id = employee.manger ? employee.manger.id : null;
      this.edit.plan_id = employee.plans.map((plan) => plan.id);
      this.edit.manage_others = employee.manage_others;
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.errors = {};
      this.edit = {
        name: "",
        name_e: "",
        manage_others: 1,
        customer_handel: "non_customer",
        department_id: null,
        manger_id: null,
        salesman_type_id: null,
        plan_id: [],
        is_salesman: "active",
      };
    },
    /*
     *  start  dynamicSortString
     */
    sortString(value) {
      return dynamicSortString(value);
    },
    SortNumber(value) {
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
    moveInput(tag, c, index) {
      document.querySelector(`${tag}[data-${c}='${index}']`).focus();
    },
    formatDate(value) {
      return formatDateOnly(value);
    },
    log(id) {
      if (this.mouseEnter != id) {
        this.Tooltip = "";
        this.mouseEnter = id;
        adminApi
          .get(`/employees/logs/${id}`)
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
            fn || ("Employee" + "." || "SheetJSTableExport.") + (type || "xlsx")
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
    <Department
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getDepartnent"
    />
    <SalemanType
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getSaleMenType"
    />
    <SalemanPlan
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getPlans"
    />

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("employee.employeesTable") }}</h4>
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
                      v-if="isVisible('department_id')"
                      v-model="filterSetting"
                      :value="
                        $i18n.locale == 'ar'
                          ? 'department.name'
                          : 'department.name_e'
                      "
                      class="mb-1"
                    >
                      {{ getCompanyKey("employee_department") }}
                    </b-form-checkbox>
                    <b-form-checkbox
                      v-if="isVisible('name')"
                      v-model="filterSetting"
                      value="name"
                      class="mb-1"
                      >{{ getCompanyKey("employee_name_ar") }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-if="isVisible('name_e')"
                      v-model="filterSetting"
                      value="name_e"
                      class="mb-1"
                      >{{ getCompanyKey("employee_name_en") }}</b-form-checkbox
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
                    @click.prevent="deleteEmployee(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1"
                    @click.prevent="deleteEmployee(checkAll[0])"
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
                        v-model="setting.department_id"
                        class="mb-1"
                        >{{ getCompanyKey("employee_department") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-if="isVisible('name')"
                        v-model="setting.name"
                        class="mb-1"
                        >{{ getCompanyKey("employee_name_ar") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-if="isVisible('name_e')"
                        v-model="setting.name_e"
                        class="mb-1"
                      >
                        {{ getCompanyKey("employee_name_en") }}
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
                      {{ employeesPagination.from }}-{{
                        employeesPagination.to
                      }}
                      /
                      {{ employeesPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            employeesPagination.current_page == 1 ? 'none' : '',
                        }"
                        @click.prevent="
                          getData(employeesPagination.current_page - 1)
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
                            employeesPagination.last_page ==
                            employeesPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(employeesPagination.current_page + 1)
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
              :title="getCompanyKey('employee_create_form')"
              title-class="font-18"
              body-class="p-4 "
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
                  <div class="col-md-3" v-if="isVisible('name')">
                    <div class="form-group">
                      <label for="field-1" class="control-label">
                        {{ getCompanyKey("employee_name_ar") }}
                        <span v-if="isRequired('name')" class="text-danger"
                          >*</span
                        >
                      </label>
                      <div dir="rtl">
                        <input
                          type="text"
                          class="form-control arabicInput"
                          data-create="1"
                          @keyup="arabicValueName(create.name)"
                          v-model="$v.create.name.$model"
                          :class="{
                            'is-invalid': $v.create.name.$error || errors.name,
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
                  <div class="col-md-3" v-if="isVisible('name_e')">
                    <div class="form-group">
                      <label for="field-2" class="control-label">
                        {{ getCompanyKey("employee_name_en") }}
                        <span v-if="isRequired('name_e')" class="text-danger"
                          >*</span
                        >
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
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div class="col-md-3" v-if="isVisible('department_id')">
                    <div class="form-group position-relative">
                      <label class="control-label">
                        {{ getCompanyKey("employee_department") }}
                        <span
                          v-if="isRequired('department_id')"
                          class="text-danger"
                          >*</span
                        >
                      </label>
                      <multiselect
                        @input="showDepartmentModal"
                        v-model="create.department_id"
                        :options="departments.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            $i18n.locale == 'ar'
                              ? departments.find((x) => x.id == opt).name
                              : departments.find((x) => x.id == opt).name_e
                        "
                      >
                      </multiselect>
                      <div
                        v-if="
                          $v.create.department_id.$error || errors.department_id
                        "
                        class="text-danger"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.department_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.department_id"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-3" v-if="isVisible('manger_id')">
                    <div class="form-group">
                      <label class="mr-2">
                        {{ getCompanyKey("manager") }}
                      </label>
                      <multiselect
                        v-model="create.manger_id"
                        :options="managers.map((type) => type.id)"
                        :custom-label="
                          (opt) => managers.find((x) => x.id == opt).name
                        "
                      >
                      </multiselect>
                      <div
                        v-if="$v.create.manger_id.$error || errors.manger_id"
                        class="text-danger"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.manger_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.manger_id"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div v-if="isVisible('customer_handel')" class="col-md-3">
                    <div class="form-group">
                      <label class="mr-2">
                        {{ getCompanyKey("customer_handel") }}
                        <span
                          v-if="isRequired('customer_handel')"
                          class="text-danger"
                          >*</span
                        >
                      </label>

                      <select
                        class="custom-select"
                        v-model="$v.create.customer_handel.$model"
                      >
                        <option value="non_customer">
                          {{ $t("general.non_customer") }}
                        </option>
                        <option value="his_customer">
                          {{ $t("general.his_customer") }}
                        </option>
                        <option value="all_customer">
                          {{ $t("general.all_customer") }}
                        </option>
                      </select>

                      <template v-if="errors.customer_handel">
                        <ErrorMessage
                          v-for="(
                            errorMessage, index
                          ) in errors.customer_handel"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div v-if="isVisible('manage_others')" class="col-md-3">
                    <div class="form-group">
                      <label class="mr-2">
                        {{ getCompanyKey("manage_others") }}
                        <span
                          v-if="isRequired('manage_others')"
                          class="text-danger"
                          >*</span
                        >
                      </label>
                      <b-form-group
                        :class="{
                          'is-invalid':
                            $v.create.manage_others.$error ||
                            errors.manage_others,
                          'is-valid':
                            !$v.create.manage_others.$invalid &&
                            !errors.manage_others,
                        }"
                      >
                        <b-form-radio
                          class="d-inline-block"
                          v-model="$v.create.manage_others.$model"
                          name="manage_others_some-radios"
                          :value="1"
                          >{{ $t("general.Yes") }}</b-form-radio
                        >
                        <b-form-radio
                          class="d-inline-block m-1"
                          v-model="$v.create.manage_others.$model"
                          name="manage_others_some-radios"
                          :value="0"
                          >{{ $t("general.No") }}</b-form-radio
                        >
                      </b-form-group>
                      <template v-if="errors.manage_others">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.manage_others"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div v-if="isVisible('is_salesman')" class="col-md-3">
                    <div class="form-group">
                      <label class="mr-2">
                        {{ getCompanyKey("is_salesman") }}
                        <span
                          v-if="isRequired('is_salesman')"
                          class="text-danger"
                          >*</span
                        >
                      </label>
                      <b-form-group
                        :class="{
                          'is-invalid':
                            $v.create.is_salesman.$error || errors.is_salesman,
                          'is-valid':
                            !$v.create.is_salesman.$invalid &&
                            !errors.is_salesman,
                        }"
                      >
                        <b-form-radio
                          class="d-inline-block"
                          v-model="$v.create.is_salesman.$model"
                          name="some-radios"
                          value="active"
                          >{{ $t("general.Yes") }}</b-form-radio
                        >
                        <b-form-radio
                          class="d-inline-block m-1"
                          v-model="$v.create.is_salesman.$model"
                          name="some-radios"
                          value="inactive"
                          >{{ $t("general.No") }}</b-form-radio
                        >
                      </b-form-group>
                      <template v-if="errors.is_salesman">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.is_salesman"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div
                    v-if="
                      isVisible('salesman_type_id') &&
                      create.is_salesman == 'active'
                    "
                    class="col-md-3"
                  >
                    <div class="form-group">
                      <label class="mr-2">
                        {{ getCompanyKey("sale_man_type") }}
                        <span
                          v-if="isRequired('salesman_type_id')"
                          class="text-danger"
                          >*</span
                        >
                      </label>
                      <multiselect
                        @input="showSalesManTypeModal"
                        v-model="create.salesman_type_id"
                        :options="salesmenTypes.map((type) => type.id)"
                        :custom-label="
                          (opt) => salesmenTypes.find((x) => x.id == opt).name
                        "
                      >
                      </multiselect>
                      <div
                        v-if="
                          $v.create.salesman_type_id.$error ||
                          errors.salesman_type_id
                        "
                        class="text-danger"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.salesman_type_id">
                        <ErrorMessage
                          v-for="(
                            errorMessage, index
                          ) in errors.salesman_type_id"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div v-if="create.is_salesman == 'active'" class="col-md-3">
                    <div class="form-group">
                      <label class="mr-2">
                        {{ getCompanyKey("plan") }}
                        <span class="text-danger">*</span>
                      </label>
                      <multiselect
                        @input="showPlanModal"
                        :multiple="true"
                        v-model="create.plan_id"
                        :options="plans.map((type) => type.id)"
                        :custom-label="
                          (opt) => plans.find((x) => x.id == opt).name
                        "
                      >
                      </multiselect>
                      <div
                        v-if="$v.create.plan_id.$error || errors.plan_id"
                        class="text-danger"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.plan_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.plan_id"
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
                    <th
                      v-if="setting.department_id && isVisible('department_id')"
                    >
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("employee_department") }}</span>
                      </div>
                    </th>
                    <th v-if="setting.name && isVisible('name')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("employee_name_ar") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="employees.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="employees.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.name_e && isVisible('name_e')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("employee_name_en") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="employees.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="employees.sort(sortString('-name_e'))"
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
                <tbody v-if="employees.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in employees"
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
                    <td
                      v-if="setting.department_id && isVisible('department_id')"
                    >
                      <h5 class="m-0 font-weight-normal">
                        {{
                          $i18n.locale == "ar"
                            ? data.department.name
                            : data.department.name_e
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.name && isVisible('name')">
                      <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                    </td>
                    <td v-if="setting.name_e && isVisible('name_e')">
                      <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
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
                            @click.prevent="deleteEmployee(data.id)"
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
                        dialog-class="modal-full-width"
                        :id="`modal-edit-${data.id}`"
                        :title="getCompanyKey('employee_edit_form')"
                        title-class="font-18"
                        body-class="p-4"
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
                            <div class="col-md-3" v-if="isVisible('name')">
                              <div class="form-group">
                                <label for="field-1" class="control-label">
                                  {{ getCompanyKey("employee_name_ar") }}
                                  <span
                                    v-if="isRequired('name')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <div dir="rtl">
                                  <input
                                    type="text"
                                    class="form-control arabicInput"
                                    data-create="1"
                                    @keyup="arabicValueName(edit.name)"
                                    v-model="$v.edit.name.$model"
                                    :class="{
                                      'is-invalid':
                                        $v.edit.name.$error || errors.name,
                                      'is-valid':
                                        !$v.edit.name.$invalid && !errors.name,
                                    }"
                                    id="field-1"
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
                                    v-for="(errorMessage, index) in errors.name"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div class="col-md-3" v-if="isVisible('name_e')">
                              <div class="form-group">
                                <label for="field-2" class="control-label">
                                  {{ getCompanyKey("employee_name_en") }}
                                  <span
                                    v-if="isRequired('name_e')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <div dir="ltr">
                                  <input
                                    type="text"
                                    class="form-control englishInput"
                                    data-create="2"
                                    @keyup="englishValueName(edit.name_e)"
                                    v-model="$v.edit.name_e.$model"
                                    :class="{
                                      'is-invalid':
                                        $v.edit.name_e.$error || errors.name_e,
                                      'is-valid':
                                        !$v.edit.name_e.$invalid &&
                                        !errors.name_e,
                                    }"
                                    id="field-2"
                                  />
                                </div>
                                <div
                                  v-if="!$v.edit.name_e.minLength"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.Itmustbeatleast") }}
                                  {{ $v.edit.name_e.$params.minLength.min }}
                                  {{ $t("general.letters") }}
                                </div>
                                <div
                                  v-if="!$v.edit.name_e.maxLength"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.Itmustbeatmost") }}
                                  {{ $v.edit.name_e.$params.maxLength.max }}
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
                              class="col-md-3"
                              v-if="isVisible('department_id')"
                            >
                              <div class="form-group position-relative">
                                <label class="control-label">
                                  {{ getCompanyKey("employee_department") }}
                                  <span
                                    v-if="isRequired('department_id')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <multiselect
                                  @input="showDepartmentModalEdit"
                                  v-model="edit.department_id"
                                  :options="departments.map((type) => type.id)"
                                  :custom-label="
                                    (opt) =>
                                      $i18n.locale == 'ar'
                                        ? departments.find((x) => x.id == opt)
                                            .name
                                        : departments.find((x) => x.id == opt)
                                            .name_e
                                  "
                                >
                                </multiselect>
                                <div
                                  v-if="
                                    $v.edit.department_id.$error ||
                                    errors.department_id
                                  "
                                  class="text-danger"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.department_id">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.department_id"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-3" v-if="isVisible('manger_id')">
                              <div class="form-group">
                                <label class="mr-2">
                                  {{ getCompanyKey("manager") }}
                                </label>
                                <multiselect
                                  v-model="edit.manger_id"
                                  :options="managers.map((type) => type.id)"
                                  :custom-label="
                                    (opt) =>
                                      managers.find((x) => x.id == opt).name
                                  "
                                >
                                </multiselect>
                                <div
                                  v-if="
                                    $v.edit.manger_id.$error || errors.manger_id
                                  "
                                  class="text-danger"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.manger_id">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.manger_id"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div
                              v-if="isVisible('customer_handel')"
                              class="col-md-3"
                            >
                              <div class="form-group">
                                <label class="mr-2">
                                  {{ getCompanyKey("customer_handel") }}
                                  <span
                                    v-if="isRequired('customer_handel')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <select
                                  class="custom-select"
                                  v-model="$v.edit.customer_handel.$model"
                                >
                                  <option value="non_customer">
                                    {{ $t("general.non_customer") }}
                                  </option>
                                  <option value="his_customer">
                                    {{ $t("general.his_customer") }}
                                  </option>
                                  <option value="all_customer">
                                    {{ $t("general.all_customer") }}
                                  </option>
                                </select>
                                <template v-if="errors.customer_handel">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.customer_handel"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div
                              v-if="isVisible('manage_others')"
                              class="col-md-3"
                            >
                              <div class="form-group">
                                <label class="mr-2">
                                  {{ getCompanyKey("manage_others") }}
                                  <span
                                    v-if="isRequired('manage_others')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <b-form-group
                                  :class="{
                                    'is-invalid':
                                      $v.edit.manage_others.$error ||
                                      errors.manage_others,
                                    'is-valid':
                                      !$v.edit.manage_others.$invalid &&
                                      !errors.manage_others,
                                  }"
                                >
                                  <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.edit.manage_others.$model"
                                    name="manage_others_some-radios"
                                    :value="1"
                                    >{{ $t("general.Yes") }}</b-form-radio
                                  >
                                  <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.edit.manage_others.$model"
                                    name="manage_others_some-radios"
                                    :value="0"
                                    >{{ $t("general.No") }}</b-form-radio
                                  >
                                </b-form-group>
                                <template v-if="errors.manage_others">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.manage_others"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>

                            <div
                              v-if="isVisible('is_salesman')"
                              class="col-md-3"
                            >
                              <div class="form-group">
                                <label class="mr-2">
                                  {{ getCompanyKey("is_salesman") }}
                                  <span
                                    v-if="isRequired('is_salesman')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <b-form-group
                                  :class="{
                                    'is-invalid':
                                      $v.edit.is_salesman.$error ||
                                      errors.is_salesman,
                                    'is-valid':
                                      !$v.edit.is_salesman.$invalid &&
                                      !errors.is_salesman,
                                  }"
                                >
                                  <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.edit.is_salesman.$model"
                                    name="some-radios"
                                    value="active"
                                    >{{ $t("general.Yes") }}</b-form-radio
                                  >
                                  <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.edit.is_salesman.$model"
                                    name="some-radios"
                                    value="inactive"
                                    >{{ $t("general.No") }}</b-form-radio
                                  >
                                </b-form-group>
                                <template v-if="errors.is_salesman">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.is_salesman"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div
                              v-if="
                                isVisible('salesman_type_id') &&
                                edit.is_salesman == 'active'
                              "
                              class="col-md-3"
                            >
                              <div class="form-group">
                                <label class="mr-2">
                                  {{ getCompanyKey("sale_man_type") }}
                                  <span
                                    v-if="isRequired('salesman_type_id')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <multiselect
                                  @input="showSalesManTypeEditModal"
                                  v-model="edit.salesman_type_id"
                                  :options="
                                    salesmenTypes.map((type) => type.id)
                                  "
                                  :custom-label="
                                    (opt) =>
                                      salesmenTypes.find((x) => x.id == opt)
                                        .name
                                  "
                                >
                                </multiselect>
                                <div
                                  v-if="
                                    $v.edit.salesman_type_id.$error ||
                                    errors.salesman_type_id
                                  "
                                  class="text-danger"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.salesman_type_id">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.salesman_type_id"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div
                              v-if="edit.is_salesman == 'active'"
                              class="col-md-3"
                            >
                              <div class="form-group">
                                <label class="mr-2">
                                  {{ getCompanyKey("plan") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <multiselect
                                  @input="showPlanModalEdit"
                                  :multiple="true"
                                  v-model="edit.plan_id"
                                  :options="plans.map((type) => type.id)"
                                  :custom-label="
                                    (opt) => plans.find((x) => x.id == opt).name
                                  "
                                >
                                </multiselect>
                                <div
                                  v-if="
                                    $v.edit.plan_id.$error || errors.plan_id
                                  "
                                  class="text-danger"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.plan_id">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.plan_id"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
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
