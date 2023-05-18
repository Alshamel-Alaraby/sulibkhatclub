<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import { requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import { dynamicSortString, dynamicSortNumber } from "../../../helper/tableSort";
import translation from "../../../helper/translation-mixin";
import senderHoverHelper from "../../../helper/senderHoverHelper";
import { formatDateOnly } from "../../../helper/startDate";
import employee from "../../../components/create/employee";
import documentStatus from "../../../components/create/document-status";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "document Reason",
    meta: [{ name: "description", content: "document Reason" }],
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
      employee,
      documentStatus,
      DatePicker,
      Multiselect
  },
  data() {
    return {
      fields: [],
      per_page: 50,
      search: "",
      debounce: {},
      enabled3: true,
      reasonsPagination: {},
      reasons: [],
      employees: [],
      status: [],
      isLoader: false,
      Tooltip: "",
      mouseEnter: "",
      create: {
          document_serial_number: "",
          notes: "",
          decision_date: this.formatDate(new Date()),
          approval_time: "",
          employee_id: null,
          decision_id: null,
      },
      edit: {
          document_serial_number: "",
          notes: "",
          decision_date: this.formatDate(new Date()),
          approval_time: "",
          employee_id: null,
          decision_id: null,
      },
      company_id: null,
      errors: {},
      isCheckAll: false,
      checkAll: [],
      current_page: 1,
      setting: {
          document_serial_number: true,
          notes: true,
          decision_date: true,
          approval_time: true,
          employee_id: true,
          decision_id: true,
      },
      is_disabled: false,
      filterSetting: [
          "document_serial_number",
          "notes",
          "decision_date",
          "approval_time",
          this.$i18n.locale == 'ar'? 'employee.name': 'employee.name_e',
          this.$i18n.locale == 'ar'? 'documentStatuse.name': 'documentStatuse.name_e',
      ],
      printLoading: true,
      printObj: {
        id: "printUnitStatus",
      }
    };
  },
  validations: {
    create: {
        document_serial_number: { required: requiredIf(function (model) {
                return this.isRequired("document_serial_number");
            }) },
        notes: { required: requiredIf(function (model) {
                return this.isRequired("notes");
            }) },
        decision_date: { required: requiredIf(function (model) {
              return this.isRequired("decision_date");
          }) },
        approval_time: { required: requiredIf(function (model) {
              return this.isRequired("approval_time");
          }) },
        employee_id: { required: requiredIf(function (model) {
              return this.isRequired("employee_id");
          }) },
        decision_id: { required: requiredIf(function (model) {
              return this.isRequired("decision_id");
          }) },
    },
    edit: {
        document_serial_number: { required: requiredIf(function (model) {
                return this.isRequired("document_serial_number");
            }) },
        notes: { required: requiredIf(function (model) {
                return this.isRequired("notes");
            }) },
        decision_date: { required: requiredIf(function (model) {
                return this.isRequired("decision_date");
            }) },
        approval_time: { required: requiredIf(function (model) {
                return this.isRequired("approval_time");
            }) },
        employee_id: { required: requiredIf(function (model) {
                return this.isRequired("employee_id");
            }) },
        decision_id: { required: requiredIf(function (model) {
                return this.isRequired("decision_id");
            }) },
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
        this.reasons.forEach((el) => {
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
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            if (
                vm.$store.state.auth.work_flow_trees.includes("document") ||
                vm.$store.state.auth.user.type == "super_admin"
            ) {
                return true;
            } else {
                return vm.$router.push({ name: "home" });
            }
        });
    },
  methods: {
      getCustomTableFields() {
          adminApi
              .get(`/customTable/table-columns/general_document_approval_details`)
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
    formatDate(value) {
      return formatDateOnly(value);
    },
    log(id) {
      if (this.mouseEnter != id) {
        this.Tooltip = "";
        this.mouseEnter = id;
        adminApi
          .get(`document-approval-details/logs/${id}`)
          .then((res) => {
            let l = res.data.data;
            l.forEach((e) => {
              this.Tooltip += `Created By: ${e.causer_type}; Event: ${e.event
                }; Description: ${e.description} ;Created At: ${this.formatDate(
                  e.created_at
                )} \n`;
            });
          })
          .catch((err) => {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.Thereisanerrorinthesystem")}`,
            });
          });
      } else {
      }
    },
    /**
     *  start get Data countrie && pagination
     */
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      for (let i = 0; i < this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      adminApi
        .get(
          `document-approval-details?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.reasons = l.data;
          this.reasonsPagination = l.pagination;
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
        this.current_page <= this.reasonsPagination.last_page &&
        this.current_page != this.reasonsPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `document-approval-details?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}&company_id=${this.company_id}`
          )
          .then((res) => {
            let l = res.data;
            this.reasons = l.data;
            this.reasonsPagination = l.pagination;
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
    deleteBranch(id, index) {
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
              .post(`document-approval-details/bulk-delete`, { ids: id })
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
              .delete(`document-approval-details/${id}`)
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
    resetDataCreate()
    {
        this.create = {
            document_serial_number: "",
            notes: "",
            decision_date: this.formatDate(new Date()),
            approval_time: "",
            employee_id: null,
            decision_id: null,
        };
        this.$nextTick(() => {
            this.$v.$reset();
        });
        this.errors = {};
    },
    resetModalHidden() {
      this.resetDataCreate();
      this.$bvModal.hide(`create`);
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
        if(this.isVisible('employee_id'))await this.getEmployees();
        if(this.isVisible('decision_id'))await this.getDocumentStatus();
        this.resetDataCreate();
    },
    /**
     *  create countrie
     */
   resetForm() {
        this.resetDataCreate();
      this.is_disabled = false;
    },
    AddSubmit() {
      this.$v.create.$touch();
      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        let data = {
            document_serial_number: this.create.document_serial_number,
            notes: this.create.notes,
            decision_date: this.create.decision_date,
            approval_time: this.create.approval_time,
            employee_id: this.create.employee_id,
            decision_id: this.create.decision_id,
        };
        adminApi
          .post(`document-approval-details`, data)
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
        let data = {
            document_serial_number: this.edit.document_serial_number,
            notes: this.edit.notes,
            decision_date: this.edit.decision_date,
            approval_time: this.edit.approval_time,
            employee_id: this.edit.employee_id,
            decision_id: this.edit.decision_id,
        };
        adminApi
          .put(`document-approval-details/${id}`, data)
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
       if(this.isVisible('employee_id'))await this.getEmployees();
        if(this.isVisible('decision_id'))await this.getDocumentStatus();
      let unitStatus = this.reasons.find((e) => id == e.id);
      this.edit.document_serial_number = unitStatus.document_serial_number;
      this.edit.notes = unitStatus.notes;
      this.edit.decision_date = unitStatus.decision_date;
      this.edit.approval_time = unitStatus.approval_time;
      this.edit.employee_id = unitStatus.employee_id;
      this.edit.decision_id = unitStatus.decision_id;
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.errors = {};
      this.edit = {
          document_serial_number: "",
          notes: "",
          decision_date: this.formatDate(new Date()),
          approval_time: "",
          employee_id: null,
          decision_id: null,
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

      async getEmployees() {
          await adminApi
              .get(`/employees`)
              .then((res) => {
                  let l = res.data.data;
                  l.unshift({ id: 0, name: "اضاف موظف", name_e: "Add Employee" });
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
      showEmployeeModal() {
          if (this.create.employee_id == 0) {
              this.$bvModal.show("employee-create");
              this.create.employee_id = null;
          }
      },
      showEmployeeModalEdit() {
          if(this.edit.employee_id == 0) {
              this.$bvModal.show("employee-create");
              this.edit.employee_id = null;
          }
      },
      async getDocumentStatus() {
          await adminApi
              .get(`/document-statuses`)
              .then((res) => {
                  let l = res.data.data;
                  l.unshift({ id: 0, name: "اضاف قرار", name_e: "Add Decision" });
                  this.status = l;
              })
              .catch((err) => {
                  Swal.fire({
                      icon: "error",
                      title: `${this.$t("general.Error")}`,
                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
              });
      },
      showStatusModal() {
          if (this.create.decision_id == 0) {
              this.$bvModal.show("document-status-create");
              this.create.decision_id = null;
          }
      },
      showStatusModalEdit() {
          if(this.edit.decision_id == 0) {
              this.$bvModal.show("document-status-create");
              this.edit.decision_id = null;
          }
      },
    ExportExcel(type, fn, dl) {
      this.enabled3 = false;
      setTimeout(() => {
        let elt = this.$refs.exportable_table;
        let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
        if (dl) {
          XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' });
        } else {
          XLSX.writeFile(wb, fn || (('Unit-Status' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
        }
        this.enabled3 = true;
      }, 100);
    }
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
      <employee :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getEmployees" />
      <documentStatus :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getDocumentStatus" />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("general.documentReasonTable") }}</h4>
              <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                <div class="d-inline-block" style="width: 22.2%">
                  <!-- Basic dropdown -->
                  <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                    class="btn-block setting-search">
                    <b-form-checkbox v-if="isVisible('document_serial_number')" v-model="filterSetting" value="name" class="mb-1">{{ getCompanyKey("document_serial_number")}}</b-form-checkbox>
                    <b-form-checkbox v-if="isVisible('notes')" v-model="filterSetting" value="name_e" class="mb-1">{{ getCompanyKey("document_reason_notes") }}</b-form-checkbox>
                    <b-form-checkbox v-if="isVisible('decision_date')" v-model="filterSetting" value="name_e" class="mb-1">{{ getCompanyKey("document_decision_date") }}</b-form-checkbox>
                    <b-form-checkbox v-if="isVisible('approval_time')" v-model="filterSetting" value="name_e" class="mb-1">{{ getCompanyKey("document_approval_time") }}</b-form-checkbox>
                    <b-form-checkbox v-if="isVisible('employee_id')" v-model="filterSetting" :value="$i18n.locale == 'ar'? 'employee.name': 'employee.name_e'" class="mb-1">{{ getCompanyKey('employee') }}</b-form-checkbox>
                    <b-form-checkbox v-if="isVisible('decision_id')" v-model="filterSetting" :value="$i18n.locale == 'ar'? 'documentStatuse.name': 'documentStatuse.name_e'" class="mb-1">{{ getCompanyKey('document_decision') }}</b-form-checkbox>
                  </b-dropdown>
                  <!-- Basic dropdown -->
                </div>

                <div class="d-inline-block position-relative" style="width: 77%">
                  <span :class="[
                    'search-custom position-absolute',
                    $i18n.locale == 'ar' ? 'search-custom-ar' : '',
                  ]">
                    <i class="fe-search"></i>
                  </span>
                  <input class="form-control" style="display: block; width: 93%; padding-top: 3px" type="text"
                    v-model.trim="search" :placeholder="`${$t('general.Search')}...`" />
                </div>
              </div>
            </div>
            <!-- end search -->

            <div class="row justify-content-between align-items-center mb-2 px-1">
              <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                <!-- start create and printer -->
                <b-button v-b-modal.create variant="primary" class="btn-sm mx-1 font-weight-bold">
                  {{ $t("general.Create") }}
                  <i class="fas fa-plus"></i>
                </b-button>
                <div class="d-inline-flex">
                  <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button v-print="'#printUnitStatus'" class="custom-btn-dowonload">
                    <i class="fe-printer"></i>
                  </button>
                  <button class="custom-btn-dowonload" @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                    v-if="checkAll.length == 1">
                    <i class="mdi mdi-square-edit-outline"></i>
                  </button>
                  <!-- start mult delete  -->
                  <button class="custom-btn-dowonload" v-if="checkAll.length > 1" @click.prevent="deleteBranch(checkAll)">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button class="custom-btn-dowonload" v-if="checkAll.length == 1"
                    @click.prevent="deleteBranch(checkAll[0])">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!--  end one delete  -->
                </div>
                <!-- end create and printer -->
              </div>
              <div class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end">
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
                    <b-dropdown variant="primary" :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                      ref="dropdown" class="dropdown-custom-ali">
                      <b-form-checkbox v-if="isVisible('document_serial_number')" v-model="setting.document_serial_number" class="mb-1">{{ getCompanyKey("document_serial_number") }}</b-form-checkbox>
                      <b-form-checkbox v-if="isVisible('notes')" v-model="setting.notes" class="mb-1">{{ getCompanyKey("document_reason_notes") }}</b-form-checkbox>
                      <b-form-checkbox v-if="isVisible('decision_date')" v-model="setting.decision_date" class="mb-1">{{ getCompanyKey("document_decision_date") }}</b-form-checkbox>
                      <b-form-checkbox v-if="isVisible('approval_time')" v-model="setting.approval_time" class="mb-1">{{ getCompanyKey("document_approval_time") }}</b-form-checkbox>
                      <b-form-checkbox v-if="isVisible('employee_id')" v-model="setting.employee_id" class="mb-1">{{getCompanyKey('employee') }}</b-form-checkbox>
                      <b-form-checkbox v-if="isVisible('decision_id')" v-model="setting.decision_id" class="mb-1">{{getCompanyKey('document_decision') }}</b-form-checkbox>
                        <div class="d-flex justify-content-end">
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm">Apply</a>
                      </div>
                    </b-dropdown>
                    <!-- Basic dropdown -->
                  </div>
                  <!-- end filter and setting -->

                  <!-- start Pagination -->
                  <div class="d-inline-flex align-items-center pagination-custom">
                    <div class="d-inline-block" style="font-size: 13px">
                      {{ reasonsPagination.from }}-{{ reasonsPagination.to }} /
                      {{ reasonsPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a href="javascript:void(0)" :style="{
                        'pointer-events':
                          reasonsPagination.current_page == 1 ? 'none' : '',
                      }" @click.prevent="getData(reasonsPagination.current_page - 1)">
                        <span>&lt;</span>
                      </a>
                      <input type="text" @keyup.enter="getDataCurrentPage()" v-model="current_page"
                        class="pagination-current-page" />
                      <a href="javascript:void(0)" :style="{
                          'pointer-events':
                            reasonsPagination.last_page ==
                              reasonsPagination.current_page
                              ? 'none'
                              : '',
                        }" @click.prevent="getData(reasonsPagination.current_page + 1)">
                        <span>&gt;</span>
                      </a>
                    </div>
                  </div>
                  <!-- end Pagination -->
                </div>
              </div>
            </div>

            <!--  create   -->
            <b-modal id="create" size="lg" :title="getCompanyKey('document_reason_create_form')" title-class="font-18" body-class="p-4 "
              :hide-footer="true" @show="resetModal" @hidden="resetModalHidden">
              <form>
                <div class="mb-3 d-flex justify-content-end">
                  <b-button variant="success" :disabled="!is_disabled" @click.prevent="resetForm" type="button"
                    :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                    {{ $t("general.AddNewRecord") }}
                  </b-button>
                  <template v-if="!is_disabled">
                    <b-button variant="success" type="button" class="mx-1" v-if="!isLoader" @click.prevent="AddSubmit">
                      {{ $t("general.Add") }}
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
                    <div class="col-md-6" v-if="isVisible('employee_id')">
                        <div class="form-group">
                            <label>{{ getCompanyKey('employee') }}<span class="text-danger" v-if="isRequired('employee_id')">*</span></label>
                            <multiselect
                                @input="showEmployeeModal"
                                v-model="create.employee_id"
                                :options="employees.map((type) => type.id)"
                                :custom-label=" (opt) => $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e "
                                :class="{'is-invalid': $v.create.employee_id.$error || errors.employee_id,'is-valid': !$v.create.employee_id.$invalid && !errors.employee_id,}"
                            >
                            </multiselect>
                            <template v-if="errors.employee_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.employee_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('decision_id')">
                        <div class="form-group">
                            <label>{{ getCompanyKey('document_decision') }}<span class="text-danger" v-if="isRequired('decision_id')">*</span></label>
                            <multiselect
                                @input="showStatusModal"
                                v-model="create.decision_id"
                                :options="status.map((type) => type.id)"
                                :custom-label=" (opt) => $i18n.locale == 'ar' ? status.find((x) => x.id == opt).name : status.find((x) => x.id == opt).name_e "
                                :class="{'is-invalid': $v.create.decision_id.$error || errors.decision_id,'is-valid': !$v.create.decision_id.$invalid && !errors.decision_id,}"
                            >
                            </multiselect>
                            <template v-if="errors.decision_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.decision_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('decision_date')">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("document_decision_date") }}
                                <span v-if="isRequired('decision_date')" class="text-danger">*</span>
                            </label>
                            <date-picker
                                type="date"
                                v-model="$v.create.decision_date.$model"
                                format="YYYY-MM-DD"
                                valueType="format"
                                :confirm="false"
                                :class="{
                                'is-invalid': $v.create.decision_date.$error || errors.decision_date,
                                'is-valid': !$v.create.decision_date.$invalid && !errors.decision_date,
                            }">
                            </date-picker>
                            <template v-if="errors.decision_date">
                                <ErrorMessage v-for="(errorMessage, index) in errors.decision_date" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('approval_time')">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("document_approval_time") }}
                                <span v-if="isRequired('approval_time')" class="text-danger">*</span>
                            </label>
                            <date-picker
                                type="time"
                                v-model="$v.create.approval_time.$model"
                                format="HH:mm:ss"
                                valueType="format"
                                :confirm="false"
                                :class="{
                                'is-invalid': $v.create.approval_time.$error || errors.approval_time,
                                'is-valid': !$v.create.approval_time.$invalid && !errors.approval_time,
                            }">
                            </date-picker>
                            <template v-if="errors.approval_time">
                                <ErrorMessage v-for="(errorMessage, index) in errors.approval_time" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                  <div class="col-md-6" v-if="isVisible('document_serial_number')">
                    <div class="form-group">
                      <label for="field-1" class="control-label">
                        {{ getCompanyKey("document_serial_number") }}
                        <span v-if="isRequired('document_serial_number')" class="text-danger">*</span>
                      </label>
                      <div dir="rtl">
                        <input
                            type="text"
                            class="form-control"
                            data-create="1"
                            v-model="$v.create.document_serial_number.$model"
                            :class="{
                              'is-invalid': $v.create.document_serial_number.$error || errors.document_serial_number,
                              'is-valid': !$v.create.document_serial_number.$invalid && !errors.document_serial_number,
                            }"
                            id="field-1"
                        />
                      </div>
                      <template v-if="errors.document_serial_number">
                        <ErrorMessage v-for="(errorMessage, index) in errors.document_serial_number" :key="index">{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6" v-if="isVisible('notes')">
                    <div class="form-group">
                      <label class="control-label">
                        {{ getCompanyKey("document_reason_notes") }}
                        <span v-if="isRequired('notes')" class="text-danger">*</span>
                      </label>
                        <textarea
                            v-model="$v.create.notes.$model"
                            class="form-control"
                            :maxlength="1000"
                            rows="5"
                            :class="{
                                'is-invalid': $v.create.notes.$error || errors.notes,
                                'is-valid': !$v.create.notes.$invalid && !errors.notes,
                            }"
                        ></textarea>
                      <template v-if="errors.notes">
                        <ErrorMessage v-for="(errorMessage, index) in errors.notes" :key="index">{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                </div>
              </form>
            </b-modal>
            <!--  /create   -->

            <!-- start .table-responsive-->
            <div class="table-responsive mb-3 custom-table-theme position-relative">
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->
              <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                id="printUnitStatus">
                <thead>
                  <tr>
                    <th scope="col" style="width: 0" v-if="enabled3" class="do-not-print">
                      <div class="form-check custom-control">
                        <input class="form-check-input" type="checkbox" v-model="isCheckAll"
                          style="width: 17px; height: 17px" />
                      </div>
                    </th>
                    <th v-if="setting.document_serial_number && isVisible('document_serial_number')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("document_serial_number") }}</span>
                        <div class="arrow-sort">
                          <i class="fas fa-arrow-up" @click="reasons.sort(sortString('document_serial_number'))"></i>
                          <i class="fas fa-arrow-down" @click="reasons.sort(sortString('-document_serial_number'))"></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.notes && isVisible('notes')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("document_reason_notes") }}</span>
                        <div class="arrow-sort">
                          <i class="fas fa-arrow-up" @click="reasons.sort(sortString('notes'))"></i>
                          <i class="fas fa-arrow-down" @click="reasons.sort(sortString('-notes'))"></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.decision_date && isVisible('decision_date')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("document_decision_date") }}</span>
                        <div class="arrow-sort">
                          <i class="fas fa-arrow-up" @click="reasons.sort(sortString('decision_date'))"></i>
                          <i class="fas fa-arrow-down" @click="reasons.sort(sortString('-decision_date'))"></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.approval_time && isVisible('approval_time')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("document_approval_time") }}</span>
                        <div class="arrow-sort">
                          <i class="fas fa-arrow-up" @click="reasons.sort(sortString('approval_time'))"></i>
                          <i class="fas fa-arrow-down" @click="reasons.sort(sortString('-approval_time'))"></i>
                        </div>
                      </div>
                    </th>
                      <th v-if="setting.employee_id && isVisible('employee_id')">
                          <div class="d-flex justify-content-center">
                              <span>{{ getCompanyKey('employee') }}</span>
                              <div class="arrow-sort">
                                  <i class="fas fa-arrow-up" @click="reasons.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                  <i class="fas fa-arrow-down" @click="reasons.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                              </div>
                          </div>
                      </th>
                      <th v-if="setting.decision_id && isVisible('decision_id')">
                          <div class="d-flex justify-content-center">
                              <span>{{ getCompanyKey('document_decision') }}</span>
                              <div class="arrow-sort">
                                  <i class="fas fa-arrow-up" @click="reasons.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                  <i class="fas fa-arrow-down" @click="reasons.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                              </div>
                          </div>
                      </th>
                    <th v-if="enabled3" class="do-not-print">
                      {{ $t("general.Action") }}
                    </th>
                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                  </tr>
                </thead>
                <tbody v-if="reasons.length > 0">
                  <tr @click.capture="checkRow(data.id)" @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in reasons" :key="data.id" class="body-tr-custom">
                    <td v-if="enabled3" class="do-not-print">
                      <div class="form-check custom-control" style="min-height: 1.9em">
                        <input style="width: 17px; height: 17px" class="form-check-input" type="checkbox" :value="data.id"
                          v-model="checkAll" />
                      </div>
                    </td>
                    <td v-if="setting.document_serial_number && isVisible('document_serial_number')">
                      <h5 class="m-0 font-weight-normal">{{ data.document_serial_number }}</h5>
                    </td>
                    <td v-if="setting.notes && isVisible('notes')">
                      <h5 class="m-0 font-weight-normal">{{ data.notes }}</h5>
                    </td>
                    <td v-if="setting.decision_date && isVisible('decision_date')">
                      <h5 class="m-0 font-weight-normal">{{ data.decision_date }}</h5>
                    </td>
                    <td v-if="setting.approval_time && isVisible('approval_time')">
                      <h5 class="m-0 font-weight-normal">{{ data.approval_time }}</h5>
                    </td>
                      <td v-if="setting.employee_id && isVisible('employee_id')">
                          <h5 v-if="data.employee" class="m-0 font-weight-normal">
                              {{ data.employee? $i18n.locale == "ar" ? data.employee.name : data.employee.name_e : ' - '}}
                          </h5>
                      </td>
                      <td v-if="setting.decision_id && isVisible('decision_id')">
                          <h5 v-if="data.documentStatuse" class="m-0 font-weight-normal">
                              {{ data.documentStatuse? $i18n.locale == "ar" ? data.documentStatuse.name : data.documentStatuse.name_e : ' - '}}
                          </h5>
                      </td>

                    <td v-if="enabled3" class="do-not-print">
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm dropdown-toggle dropdown-coustom" data-toggle="dropdown"
                          aria-expanded="false">
                          {{ $t("general.commands") }}
                          <i class="fas fa-angle-down"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-custom">
                          <a class="dropdown-item" href="#" @click="$bvModal.show(`modal-edit-${data.id}`)">
                            <div class="d-flex justify-content-between align-items-center text-black">
                              <span>{{ $t("general.edit") }}</span>
                              <i class="mdi mdi-square-edit-outline text-info"></i>
                            </div>
                          </a>
                          <a class="dropdown-item text-black" href="#" @click.prevent="deleteBranch(data.id)">
                            <div class="d-flex justify-content-between align-items-center text-black">
                              <span>{{ $t("general.delete") }}</span>
                              <i class="fas fa-times text-danger"></i>
                            </div>
                          </a>
                        </div>
                      </div>

                      <!--  edit   -->
                      <b-modal :id="`modal-edit-${data.id}`" size="lg" :title="getCompanyKey('document_reason_edit_form')"
                        title-class="font-18" body-class="p-4" :ref="`edit-${data.id}`" :hide-footer="true"
                        @show="resetModalEdit(data.id)" @hidden="resetModalHiddenEdit(data.id)">
                        <form>
                          <div class="mb-3 d-flex justify-content-end">
                            <!-- Emulate built in modal footer ok and cancel button actions -->
                            <b-button variant="success" type="submit" class="mx-1" v-if="!isLoader"
                              @click.prevent="editSubmit(data.id)">
                              {{ $t("general.Edit") }}
                            </b-button>

                            <b-button variant="success" class="mx-1" disabled v-else>
                              <b-spinner small></b-spinner>
                              <span class="sr-only">{{ $t("login.Loading") }}...</span>
                            </b-button>

                            <b-button variant="danger" type="button"
                              @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)">
                              {{ $t("general.Cancel") }}
                            </b-button>
                          </div>
                          <div class="row">
                              <div class="col-md-6" v-if="isVisible('employee_id')">
                                  <div class="form-group">
                                      <label>{{ getCompanyKey('employee') }}<span class="text-danger" v-if="isRequired('employee_id')">*</span></label>
                                      <multiselect
                                          @input="showEmployeeModalEdit"
                                          v-model="edit.employee_id"
                                          :options="employees.map((type) => type.id)"
                                          :custom-label=" (opt) => $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e "
                                          :class="{'is-invalid': $v.edit.employee_id.$error || errors.employee_id,'is-valid': !$v.edit.employee_id.$invalid && !errors.employee_id,}"
                                      >
                                      </multiselect>
                                      <template v-if="errors.employee_id">
                                          <ErrorMessage v-for="(errorMessage, index) in errors.employee_id"
                                                        :key="index">{{ errorMessage }}
                                          </ErrorMessage>
                                      </template>
                                  </div>
                              </div>
                              <div class="col-md-6" v-if="isVisible('decision_id')">
                                  <div class="form-group">
                                      <label>{{ getCompanyKey('document_decision') }}<span class="text-danger" v-if="isRequired('decision_id')">*</span></label>
                                      <multiselect
                                          @input="showStatusModalEdit"
                                          v-model="edit.decision_id"
                                          :options="status.map((type) => type.id)"
                                          :custom-label=" (opt) => $i18n.locale == 'ar' ? status.find((x) => x.id == opt).name : status.find((x) => x.id == opt).name_e "
                                          :class="{'is-invalid': $v.edit.decision_id.$error || errors.decision_id,'is-valid': !$v.edit.decision_id.$invalid && !errors.decision_id,}"
                                      >
                                      </multiselect>
                                      <template v-if="errors.decision_id">
                                          <ErrorMessage v-for="(errorMessage, index) in errors.decision_id"
                                                        :key="index">{{ errorMessage }}
                                          </ErrorMessage>
                                      </template>
                                  </div>
                              </div>
                              <div class="col-md-6" v-if="isVisible('decision_date')">
                                  <div class="form-group">
                                      <label class="mr-2">
                                          {{ getCompanyKey("document_decision_date") }}
                                          <span v-if="isRequired('decision_date')" class="text-danger">*</span>
                                      </label>
                                      <date-picker
                                          type="date"
                                          v-model="$v.edit.decision_date.$model"
                                          format="YYYY-MM-DD"
                                          valueType="format"
                                          :confirm="false"
                                          :class="{
                                                'is-invalid': $v.edit.decision_date.$error || errors.decision_date,
                                                'is-valid': !$v.edit.decision_date.$invalid && !errors.decision_date,
                                            }">
                                      </date-picker>
                                      <template v-if="errors.decision_date">
                                          <ErrorMessage v-for="(errorMessage, index) in errors.decision_date" :key="index">{{ errorMessage }}
                                          </ErrorMessage>
                                      </template>
                                  </div>
                              </div>
                              <div class="col-md-6" v-if="isVisible('approval_time')">
                                  <div class="form-group">
                                      <label class="mr-2">
                                          {{ getCompanyKey("document_approval_time") }}
                                          <span v-if="isRequired('approval_time')" class="text-danger">*</span>
                                      </label>
                                      <date-picker
                                          type="time"
                                          v-model="$v.edit.approval_time.$model"
                                          format="HH:mm:ss"
                                          valueType="format"
                                          :confirm="false"
                                          :class="{
                                                'is-invalid': $v.edit.approval_time.$error || errors.approval_time,
                                                'is-valid': !$v.edit.approval_time.$invalid && !errors.approval_time,
                                            }">
                                      </date-picker>
                                      <template v-if="errors.approval_time">
                                          <ErrorMessage v-for="(errorMessage, index) in errors.approval_time" :key="index">{{ errorMessage }}
                                          </ErrorMessage>
                                      </template>
                                  </div>
                              </div>
                              <div class="col-md-6" v-if="isVisible('document_serial_number')">
                                  <div class="form-group">
                                      <label class="control-label">
                                          {{ getCompanyKey("document_serial_number") }}
                                          <span v-if="isRequired('document_serial_number')" class="text-danger">*</span>
                                      </label>
                                      <div dir="rtl">
                                          <input
                                              type="text"
                                              class="form-control"
                                              data-create="1"
                                              v-model="$v.edit.document_serial_number.$model"
                                              :class="{
                                                  'is-invalid': $v.edit.document_serial_number.$error || errors.document_serial_number,
                                                  'is-valid': !$v.edit.document_serial_number.$invalid && !errors.document_serial_number,
                                                }"
                                          />
                                      </div>
                                      <template v-if="errors.document_serial_number">
                                          <ErrorMessage v-for="(errorMessage, index) in errors.document_serial_number" :key="index">{{ errorMessage }}
                                          </ErrorMessage>
                                      </template>
                                  </div>
                              </div>
                              <div class="col-md-6" v-if="isVisible('notes')">
                                  <div class="form-group">
                                      <label class="control-label">
                                          {{ getCompanyKey("document_reason_notes") }}
                                          <span v-if="isRequired('notes')" class="text-danger">*</span>
                                      </label>
                                      <textarea
                                          v-model="$v.edit.notes.$model"
                                          class="form-control"
                                          :class="{
                                            'is-invalid': $v.edit.notes.$error || errors.notes,
                                            'is-valid': !$v.edit.notes.$invalid && !errors.notes,
                                          }"
                                          :maxlength="1000"
                                          rows="5"
                                      ></textarea>
                                      <template v-if="errors.notes">
                                          <ErrorMessage v-for="(errorMessage, index) in errors.notes" :key="index">{{ errorMessage }}
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
                      <button @mouseover="log(data.id)" @mousemove="log(data.id)" type="button" class="btn"
                        data-toggle="tooltip" :data-placement="$i18n.locale == 'en' ? 'left' : 'right'" :title="Tooltip">
                        <i class="fe-info" style="font-size: 22px"></i>
                      </button>
                    </td v-if="enabled3" class="do-not-print">
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
