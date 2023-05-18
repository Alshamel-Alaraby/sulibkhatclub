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
import {
  dynamicSortString,
  dynamicSortNumber,
} from "../../../helper/tableSort";
import senderHoverHelper from "../../../helper/senderHoverHelper";
import Document from "../../../components/create/document";
import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../../helper/startDate";
import translation from "../../../helper/translation-mixin";
import Branch from "../../../components/create/branch";
import { arabicValue, englishValue } from "../../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Serial",
    meta: [{ name: "description", content: "Serial" }],
  },
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
    Document,
    Branch,
  },
  mixins: [translation],
  data() {
    return {
      fields: [],
      per_page: 50,
      search: "",
      debounce: {},
      enabled3: true,
      serials: [],
      serialsPagination: {},
      documents: [],
      branches: [],
      is_disabled: false,
      isLoader: false,
      Tooltip: "",
      mouseEnter: "",
      types: [],
      create: {
        name: "",
        name_e: "",
        start_no: null,
        perfix: "",
        suffix: "",
        restart_period_id: null,
        is_default: 1,
        branch_id: null,
        document_id: null,
        type: "",
      },
      edit: {
        name: "",
        name_e: "",
        start_no: null,
        perfix: "",
        suffix: "",
        restart_period_id: null,
        document_id: null,
        branch_id: null,
        is_default: 1,
      },
      setting: {
        name: true,
        name_e: true,
        start_no: true,
        perfix: true,
        suffix: true,
        restart_period_id: true,
        branch_id: true,
        is_default: true,
        document_id: true,
      },
      company_id: null,
      errors: {},
      isCheckAll: false,
      checkAll: [],
      stores: [],
      current_page: 1,
      filterSetting: [
        "name",
        "name_e",
        "perfix",
        "suffix",
        "start_no",
        this.$i18n.locale == "ar" ? "document.name" : "document.name_e",
        this.$i18n.locale == "ar" ? "branch.name" : "branch.name_e",
      ],
      printLoading: true,
      printObj: {
        id: "printData",
      },
    };
  },
  validations: {
    create: {
      name: {
        required: requiredIf(function (model) {
          return this.isRequired("name");
        }),
        minLength: minLength(3),
        maxLength: maxLength(100),
      },
      name_e: {
        required: requiredIf(function (model) {
          return this.isRequired("name_e");
        }),
        minLength: minLength(3),
        maxLength: maxLength(100),
      },
      start_no: {
        required: requiredIf(function (model) {
          return this.isRequired("start_no");
        }),
        maxLength: maxLength(20),
      },
      suffix: {
        required: requiredIf(function (model) {
          return this.isRequired("suffix");
        }),
        maxLength: maxLength(200),
      },
      perfix: {
        required: requiredIf(function (model) {
          return this.isRequired("perfix");
        }),
        maxLength: maxLength(200),
      },
      restart_period_id: {
        required: requiredIf(function (model) {
          return this.isRequired("restart_period_id");
        }),
        minLength: minLength(1),
        maxLength: maxLength(200),
      },
      document_id: {
        required: requiredIf(function (model) {
          return this.isRequired("document_id");
        }),
      },
      branch_id: {
        required: requiredIf(function (model) {
          return this.isRequired("branch_id");
        }),
      },
      is_default: {
        required: requiredIf(function (model) {
          return this.isRequired("is_default");
        }),
      },
    },
    edit: {
      name: {
        required: requiredIf(function (model) {
          return this.isRequired("name");
        }),
        minLength: minLength(3),
        maxLength: maxLength(100),
      },
      name_e: {
        required: requiredIf(function (model) {
          return this.isRequired("name_e");
        }),
        minLength: minLength(3),
        maxLength: maxLength(100),
      },
      start_no: {
        required: requiredIf(function (model) {
          return this.isRequired("start_no");
        }),
        maxLength: maxLength(20),
      },
      suffix: {
        required: requiredIf(function (model) {
          return this.isRequired("suffix");
        }),
        maxLength: maxLength(200),
      },
      perfix: {
        required: requiredIf(function (model) {
          return this.isRequired("perfix");
        }),
        maxLength: maxLength(200),
      },
      restart_period_id: {
        required: requiredIf(function (model) {
          return this.isRequired("restart_period_id");
        }),
        minLength: minLength(1),
        maxLength: maxLength(200),
      },
      document_id: {
        required: requiredIf(function (model) {
          return this.isRequired("document_id");
        }),
      },
      branch_id: {
        required: requiredIf(function (model) {
          return this.isRequired("branch_id");
        }),
      },
      is_default: {
        required: requiredIf(function (model) {
          return this.isRequired("is_default");
        }),
      },
    },
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      if (
        vm.$store.state.auth.work_flow_trees.includes("serial") ||
        vm.$store.state.auth.user.type == "super_admin"
      ) {
        return true;
      } else {
        return vm.$router.push({ name: "home" });
      }
    });
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
        this.serials.forEach((el) => {
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
  methods: {
    arabicValueName(txt) {
      this.create.name = arabicValue(txt);
      this.edit.name = arabicValue(txt);
    },
    englishValueName(txt) {
      this.create.name_e = englishValue(txt);
      this.edit.name_e = englishValue(txt);
    },
    getCustomTableFields() {
      adminApi
        .get(`/customTable/table-columns/general_serials`)
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
          .get(`/serials/logs/${id}`)
          .then((res) => {
            let l = res.data.data;
            l.forEach((e) => {
              this.Tooltip += `Created By: ${e.causer_type}; Event: ${
                e.event
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
    resetForm() {
      this.create = {
        name: "",
        name_e: "",
        start_no: null,
        perfix: "",
        suffix: "",
        restart_period_id: null,
        is_default: 1,
        branch_id: null,
        document_id: null,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_disabled = false;
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
          `/serials?page=${page}&per_page=${this.per_page}&company_id=${this.company_id}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.serials = l.data;
          this.serialsPagination = l.pagination;
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
        this.current_page <= this.serialsPagination.last_page &&
        this.current_page != this.serialsPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }
        adminApi
          .get(
            `/serials?page=${this.current_page}&per_page=${this.per_page}&company_id=${this.company_id}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.serials = l.data;
            this.serialsPagination = l.pagination;
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
    deleteBranch(id) {
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
            .delete(`/serials/${id}`)
            .then((res) => {
              this.getData();
              this.checkAll = [];
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
    },
    /**
     *  end delete countrie
     */
    /**
     *  reset Modal (create)
     */
    resetModalHidden() {
      this.create = {
        start_no: null,
        perfix: "",
        suffix: "",
        restart_period_id: null,
        is_default: 1,
        branch_id: null,
        document_id: null,
        name: "",
        name_e: "",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.is_disabled = false;
      this.branches = [];
    },
    /**
     *  hidden Modal (create)
     */
    resetModal() {
      this.create = {
        name: "",
        name_e: "",
        start_no: null,
        perfix: "",
        suffix: "",
        restart_period_id: null,
        is_default: 1,
        branch_id: null,
        document_id: null,
      };
      if (this.isVisible("document_id")) this.getDocument(this.company_id);
      if (this.isVisible("branch_id")) this.getBranch();
      if (this.isVisible("restart_period_id")) this.getPeriod();
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_disabled = false;
      this.errors = {};
    },
    /**
     *  create countrie
     */

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
        if (!this.create.name) {
          this.create.name = this.create.name_e;
        }
        if (!this.create.name_e) {
          this.create.name_e = this.create.name;
        }

        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;
        adminApi
          .post(`/serials`, { ...this.create, company_id: this.company_id })
          .then((res) => {
            this.getData();
            this.is_disabled = true;

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
        adminApi
          .put(`/serials/${id}`, this.edit)
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
      if (this.isVisible("document_id"))
        await this.getDocument(this.company_id);
      if (this.isVisible("branch_id")) await this.getBranch();
      if (this.isVisible("restart_period_id")) this.getPeriod();
      let serial = this.serials.find((e) => id == e.id);
      this.edit.start_no = serial.start_no;
      this.edit.perfix = serial.perfix;
      this.edit.suffix = serial.suffix;
      this.edit.has_child = serial.has_child;
      this.edit.restart_period_id = serial.restart_period_id
        ? serial.restart_period_id
        : null;
      this.edit.document_id = serial.document ? serial.document.id : null;
      this.edit.branch_id = serial.branch ? serial.branch.id : null;
      this.edit.is_default = serial.is_default
        ? parseInt(serial.is_default)
        : 0;
      this.errors = {};
      this.edit.name = serial.name;
      this.edit.name_e = serial.name_e;
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.errors = {};
      this.edit = {
        name: "",
        name_e: "",
        start_no: null,
        perfix: "",
        suffix: "",
        restart_period_id: null,
        is_default: 1,
        branch_id: null,
        document_id: null,
      };
      this.branches = [];
      this.stores = [];
    },
    /*
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
    async getDocument(id) {
      this.isLoader = true;
      await adminApi
        .get(`/document?company_id=${this.company_id}&document=1`)
        .then((res) => {
          let l = res.data.data;
          // l.unshift({ id: 0, name: "اضف مستند", name_e: "Add Document" });
          this.documents = l;
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
    async getBranch() {
      this.isLoader = true;

      await adminApi
        .get(`/branches?company_id=${this.company_id}&notParent=1`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضف فرع", name_e: "Add Branch" });
          this.branches = l;
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
    async getPeriod() {
      this.isLoader = true;

      await adminApi
        .get(`/restart-period`)
        .then((res) => {
          let l = res.data.data;
          this.types = l;
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
    showBranchModal() {
      if (this.create.branch_id == 0) {
        this.$bvModal.show("branch-create");
        this.create.branch_id = null;
      }
    },
    showBranchModalEdit() {
      if (this.edit.branch_id == 0) {
        this.$bvModal.show("branch-create");
        this.edit.branch_id = null;
      }
    },
    showDocumentModal() {
      if (this.create.document_id == 0) {
        this.$bvModal.show("create-document");
        this.create.document_id = null;
      }
    },
    showDocumentModalEdit() {
      if (this.edit.document_id == 0) {
        this.$bvModal.show("create-document");
        this.edit.document_id = null;
      }
    },
    /**
     *   Export Excel
     */
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
            fn || ("Serials" + "." || "SheetJSTableExport.") + (type || "xlsx")
          );
        }
        this.enabled3 = true;
      }, 100);
    },
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <Document
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getDocument"
    />
    <Branch
      :id="'branch-create'"
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getBranch"
    />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("serial.serialsTable") }}</h4>
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
                      v-if="isVisible('name')"
                      v-model="filterSetting"
                      value="name"
                      class="mb-1"
                    >
                      {{ $t("general.Name") }}
                    </b-form-checkbox>
                    <b-form-checkbox
                      v-if="isVisible('name_e')"
                      v-model="filterSetting"
                      value="name_e"
                      class="mb-1"
                    >
                      {{ $t("general.Name_e") }}
                    </b-form-checkbox>
                    <b-form-checkbox
                      v-if="isVisible('perfix')"
                      v-model="filterSetting"
                      value="perfix"
                      class="mb-1"
                      >{{ $t("general.Perfix") }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      v-if="isVisible('suffix')"
                      value="suffix"
                      class="mb-1"
                      >{{ $t("general.Suffix") }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      v-if="isVisible('start_no')"
                      value="start_no"
                      class="mb-1"
                      >{{ $t("general.StartNo") }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-if="isVisible('document_id')"
                      v-model="filterSetting"
                      :value="
                        $i18n.locale == 'ar'
                          ? 'document.name'
                          : 'document.name_e'
                      "
                      class="mb-1"
                      >{{ $t("general.document") }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      :value="
                        $i18n.locale == 'ar' ? 'branch.name' : 'branch.name_e'
                      "
                      class="mb-1"
                    >
                      {{ $t("branch.branch") }}
                    </b-form-checkbox>
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
                    @click="ExportExcel('xlsx')"
                    class="custom-btn-dowonload"
                  >
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button v-print="'#printData'" class="custom-btn-dowonload">
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
                    @click.prevent="deleteBranch(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1"
                    @click.prevent="deleteBranch(checkAll)"
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
                        v-if="isVisible('name')"
                        v-model="setting.name"
                        class="mb-1"
                        >{{ $t("general.name") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-if="isVisible('name_e')"
                        v-model="setting.name_e"
                        class="mb-1"
                      >
                        {{ $t("general.name_e") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-if="isVisible('start_no')"
                        v-model="setting.start_no"
                        class="mb-1"
                        >{{ $t("general.StartNo") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-if="isVisible('perfix')"
                        v-model="setting.perfix"
                        class="mb-1"
                      >
                        {{ $t("general.Perfix") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-if="isVisible('suffix')"
                        v-model="setting.suffix"
                        class="mb-1"
                      >
                        {{ $t("general.Suffix") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-if="isVisible('restart_period_id')"
                        v-model="setting.restart_period_id"
                        class="mb-1"
                      >
                        {{ $t("general.RestartPeriod") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-if="isVisible('document_id')"
                        v-model="setting.document_id"
                        class="mb-1"
                      >
                        {{ $t("general.document") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-if="isVisible('branch_id')"
                        v-model="setting.branch_id"
                        class="mb-1"
                      >
                        {{ $t("branch.branch") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-if="isVisible('is_default')"
                        v-model="setting.is_default"
                        class="mb-1"
                      >
                        {{ $t("general.Default") }}
                      </b-form-checkbox>
                      <div class="d-flex justify-content-end">
                        <a
                          href="javascript:void(0)"
                          class="btn btn-primary btn-sm"
                          >{{ $t("general.Apply") }}</a
                        >
                      </div>
                    </b-dropdown>
                    <!-- Basic dropdown -->
                  </div>
                  <!-- start Pagination -->
                  <div
                    class="d-inline-flex align-items-center pagination-custom"
                  >
                    <div class="d-inline-block" style="font-size: 13px">
                      {{ serialsPagination.from }}-{{ serialsPagination.to }} /
                      {{ serialsPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            serialsPagination.current_page == 1 ? 'none' : '',
                        }"
                        @click.prevent="
                          getData(serialsPagination.current_page - 1)
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
                            serialsPagination.last_page ==
                            serialsPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(serialsPagination.current_page + 1)
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
              :title="$t('serial.addserial')"
              title-class="font-18"
              body-class="p-4 "
              size="lg"
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
                  <!-- Emulate built in modal footer ok and cancel button actions -->
                  <template v-if="!is_disabled">
                    <b-button
                      variant="success"
                      type="submit"
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
                  <b-button
                    variant="danger"
                    type="button"
                    @click.prevent="$bvModal.hide('create')"
                  >
                    {{ $t("general.Cancel") }}
                  </b-button>
                </div>
                <div class="row">
                  <div
                    class="col-md-6 position-relative"
                    v-if="isVisible('branch_id')"
                  >
                    <div class="form-group">
                      <label>
                        {{ getCompanyKey("branch") }}
                        <span v-if="isRequired('branch_id')" class="text-danger"
                          >*</span
                        >
                      </label>
                      <multiselect
                        @input="showBranchModal"
                        v-model="create.branch_id"
                        :options="branches.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            $i18n.locale == 'ar'
                              ? branches.find((x) => x.id == opt).name
                              : branches.find((x) => x.id == opt).name_e
                        "
                        :class="{
                          'is-invalid':
                            $v.create.branch_id.$error || errors.branch_id,
                        }"
                      >
                      </multiselect>
                      <div
                        v-if="!$v.create.branch_id.required"
                        class="invalid-feedback"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>

                      <template v-if="errors.branch_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.branch_id"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6" v-if="isVisible('document_id')">
                    <div class="form-group position-relative">
                      <label class="control-label">
                        {{ $t("general.document") }}
                        <span
                          v-if="isRequired('document_id')"
                          class="text-danger"
                          >*</span
                        >
                      </label>
                      <multiselect
                        v-model="create.document_id"
                        @select="showDocumentModal"
                        :options="documents.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            $i18n.locale == 'ar'
                              ? documents.find((x) => x.id == opt).name
                              : documents.find((x) => x.id == opt).name_e
                        "
                      >
                      </multiselect>
                      <div
                        v-if="
                          $v.create.document_id.$error || errors.document_id
                        "
                        class="text-danger"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.document_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.document_id"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6" v-if="isVisible('name')">
                    <div class="form-group">
                      <label for="field-8" class="control-label">
                        {{ $t("general.Name") }}
                        <span v-if="isRequired('name')" class="text-danger"
                          >*</span
                        >
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
                            'is-valid':
                              !$v.create.name.$invalid && !errors.name,
                          }"
                          id="field-8"
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
                        >
                          {{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6" v-if="isVisible('name_e')">
                    <div class="form-group">
                      <label for="field-9" class="control-label">
                        {{ $t("general.Name_en") }}
                        <span v-if="isRequired('name_e')" class="text-danger"
                          >*</span
                        >
                      </label>
                      <div dir="ltr">
                        <input
                          type="text"
                          class="form-control"
                          data-create="2"
                          @keyup="englishValueName(create.name_e)"
                          v-model="$v.create.name_e.$model"
                          :class="{
                            'is-invalid':
                              $v.create.name_e.$error || errors.name_e,
                            'is-valid':
                              !$v.create.name_e.$invalid && !errors.name_e,
                          }"
                          id="field-9"
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
                  <div class="col-md-6" v-if="isVisible('perfix')">
                    <div class="form-group">
                      <label for="field-2" class="control-label">
                        {{ $t("general.Perfix") }}
                        <span v-if="isRequired('perfix')" class="text-danger"
                          >*</span
                        >
                      </label>
                      <input
                        type="text"
                        class="form-control"
                        data-create="3"
                        v-model="$v.create.perfix.$model"
                        :class="{
                          'is-invalid':
                            $v.create.perfix.$error || errors.perfix,
                          'is-valid':
                            !$v.create.perfix.$invalid && !errors.perfix,
                        }"
                        id="field-2"
                      />
                      <div
                        v-if="!$v.create.perfix.maxLength"
                        class="invalid-feedback"
                      >
                        {{ $t("general.Itmustbeatmost") }}
                        {{ $v.create.perfix.$params.maxLength.max }}
                        {{ $t("general.letters") }}
                      </div>
                      <template v-if="errors.perfix">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.name_e"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6" v-if="isVisible('suffix')">
                    <div class="form-group">
                      <label for="field-1" class="control-label">
                        {{ $t("general.Suffix") }}

                        <span v-if="isRequired('suffix')" class="text-danger"
                          >*</span
                        >
                      </label>
                      <input
                        type="text"
                        class="form-control"
                        data-create="2"
                        v-model="$v.create.suffix.$model"
                        :class="{
                          'is-invalid':
                            $v.create.suffix.$error || errors.suffix,
                          'is-valid':
                            !$v.create.suffix.$invalid && !errors.suffix,
                        }"
                        id="field-1"
                      />
                      <div
                        v-if="!$v.create.suffix.maxLength"
                        class="invalid-feedback"
                      >
                        {{ $t("general.Itmustbeatmost") }}
                        {{ $v.create.suffix.$params.maxLength.max }}
                        {{ $t("general.letters") }}
                      </div>
                      <template v-if="errors.suffix">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.suffix"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6" v-if="isVisible('restart_period_id')">
                    <div class="form-group">
                      <label class="control-label">
                        {{ $t("general.RestartPeriod") }}
                        <span
                          v-if="isRequired('restart_period_id')"
                          class="text-danger"
                          >*</span
                        >
                      </label>
                      <multiselect
                        v-model="$v.create.restart_period_id.$model"
                        :options="types.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            $i18n.locale == 'ar'
                              ? types.find((x) => x.id == opt).name
                              : types.find((x) => x.id == opt).name_e
                        "
                        :class="{
                          'is-invalid':
                            $v.create.restart_period_id.$error ||
                            errors.restart_period_id,
                        }"
                      >
                      </multiselect>
                      <div
                        v-if="
                          $v.create.restart_period_id.$error ||
                          errors.restart_period_id
                        "
                        class="text-danger"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.restart_period_id">
                        <ErrorMessage
                          v-for="(
                            errorMessage, index
                          ) in errors.restart_period_id"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6" v-if="isVisible('start_no')">
                    <div class="form-group">
                      <label for="field-15" class="control-label">
                        {{ $t("general.StartNo") }}
                        <span v-if="isRequired('start_no')" class="text-danger"
                          >*</span
                        >
                      </label>
                      <input
                        type="number"
                        class="form-control"
                        data-create="1"
                        v-model="$v.create.start_no.$model"
                        :class="{
                          'is-invalid':
                            $v.create.start_no.$error || errors.start_no,
                          'is-valid':
                            !$v.create.start_no.$invalid && !errors.start_no,
                        }"
                        id="field-15"
                      />
                      <div
                        v-if="!$v.create.start_no.maxLength"
                        class="invalid-feedback"
                      >
                        {{ $t("general.Itmustbeatmost") }}
                        {{ $v.create.start_no.$params.maxLength.max }}
                        {{ $t("general.letters") }}
                      </div>
                      <template v-if="errors.start_no">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.start_no"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6" v-if="isVisible('is_default')">
                    <div class="form-group">
                      <label class="mr-2">
                        {{ $t("general.Default") }}
                        <span
                          v-if="isRequired('is_default')"
                          class="text-danger"
                          >*</span
                        >
                      </label>
                      <b-form-group
                        :class="{
                          'is-invalid':
                            $v.create.is_default.$error || errors.is_default,
                          'is-valid':
                            !$v.create.is_default.$invalid &&
                            !errors.is_default,
                        }"
                      >
                        <b-form-radio
                          class="d-inline-block"
                          v-model="$v.create.is_default.$model"
                          name="some-radios"
                          value="1"
                          >{{ $t("general.Yes") }}</b-form-radio
                        >
                        <b-form-radio
                          class="d-inline-block m-1"
                          v-model="$v.create.is_default.$model"
                          name="some-radios"
                          value="0"
                          >{{ $t("general.No") }}</b-form-radio
                        >
                      </b-form-group>
                      <template v-if="errors.is_default">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.is_default"
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
                id="printData"
              >
                <thead>
                  <tr>
                    <th
                      v-if="enabled3"
                      class="do-not-print"
                      scope="col"
                      style="width: 0"
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
                    <th v-if="setting.name">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.Name") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="serials.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="serials.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.name_e">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.Name_en") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="serials.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="serials.sort(sortString('-name_e'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.start_no && isVisible('start_no')">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.StartNo") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="serials.sort(sortNumber('start_no'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="serials.sort(sortNumber('-start_no'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.perfix && isVisible('perfix')">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.Perfix") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="serials.sort(sortString('perfix'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="serials.sort(sortString('-perfix'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.suffix && isVisible('suffix')">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.Suffix") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="serials.sort(sortString('suffix'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="serials.sort(sortString('-suffix'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th
                      v-if="
                        setting.restart_period_id &&
                        isVisible('restart_period_id')
                      "
                    >
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.RestartPeriod") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="
                              serials.sort(sortNumber('restart_period_id'))
                            "
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              serials.sort(sortNumber('-restart_period_id'))
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.document_id && isVisible('document_id')">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.document") }}</span>
                      </div>
                    </th>
                    <th v-if="setting.branch_id && isVisible('branch_id')">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("branch.branch") }}</span>
                      </div>
                    </th>
                    <th v-if="setting.is_default && isVisible('is_default')">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.Default") }}</span>
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
                <tbody v-if="serials.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in serials"
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
                    <td v-if="setting.name && isVisible('name')">
                      <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                    </td>
                    <td v-if="setting.name_e && isVisible('name_e')">
                      <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
                    </td>
                    <td v-if="setting.start_no && isVisible('start_no')">
                      <h5 class="m-0 font-weight-normal">
                        {{ data.start_no }}
                      </h5>
                    </td>
                    <td v-if="setting.perfix && isVisible('perfix')">
                      <h5 class="m-0 font-weight-normal">{{ data.perfix }}</h5>
                    </td>
                    <td v-if="setting.suffix && isVisible('suffix')">
                      <h5 class="m-0 font-weight-normal">{{ data.suffix }}</h5>
                    </td>
                    <td
                      v-if="
                        setting.restart_period_id &&
                        isVisible('restart_period_id')
                      "
                    >
                      <h5 class="m-0 font-weight-normal">
                        {{
                          data.restart_period
                            ? $i18n.locale == "ar"
                              ? data.restart_period.name
                              : data.restart_period.name_e
                            : " - "
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.document_id && isVisible('document_id')">
                      <h5 class="m-0 font-weight-normal">
                        {{
                          data.document
                            ? $i18n.locale == "ar"
                              ? data.document.name
                              : data.document.name_e
                            : " - "
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.branch_id && isVisible('branch_id')">
                      <h5 class="m-0 font-weight-normal">
                        {{
                          data.branch
                            ? $i18n.locale == "ar"
                              ? data.branch.name
                              : data.branch.name_e
                            : " - "
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.is_default && isVisible('is_default')">
                      <span
                        :class="[
                          parseInt(data.is_default) == 1
                            ? 'text-success'
                            : 'text-danger',
                          'badge',
                        ]"
                      >
                        {{
                          parseInt(data.is_default) == 1
                            ? `${$t("general.Yes")}`
                            : `${$t("general.No")}`
                        }}
                      </span>
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
                            @click.prevent="deleteBranch(data.id)"
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
                        :title="$t('serial.editserial')"
                        title-class="font-18"
                        body-class="p-4"
                        size="lg"
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
                              @click.prevent="editSubmit(data.id)"
                              type="button"
                              class="mx-1 font-weight-bold px-3"
                              v-if="!isLoader"
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
                              class="font-weight-bold"
                              type="button"
                              @click.prevent="
                                $bvModal.hide(`modal-edit-${data.id}`)
                              "
                            >
                              {{ $t("general.Cancel") }}
                            </b-button>
                          </div>
                          <div class="row">
                            <div v-if="isVisible('branch_id')" class="col-md-6">
                              <div class="form-group">
                                <label>
                                  {{ getCompanyKey("branch") }}
                                  <span
                                    v-if="isRequired('branch_id')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <multiselect
                                  :disabled="parseInt(data.has_child) == 1"
                                  @input="showBranchModalEdit"
                                  v-model="edit.branch_id"
                                  :options="branches.map((type) => type.id)"
                                  :custom-label="
                                    (opt) =>
                                      $i18n.locale == 'ar'
                                        ? branches.find((x) => x.id == opt).name
                                        : branches.find((x) => x.id == opt)
                                            .name_e
                                  "
                                  :class="{
                                    'is-invalid':
                                      $v.edit.branch_id.$error ||
                                      errors.branch_id,
                                  }"
                                >
                                </multiselect>
                                <div
                                  v-if="!$v.edit.branch_id.required"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>

                                <template v-if="errors.branch_id">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.branch_id"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div
                              class="col-md-6"
                              v-if="isVisible('document_id')"
                            >
                              <div class="form-group position-relative">
                                <label class="control-label">
                                  {{ $t("general.document") }}
                                  <span
                                    v-if="isRequired('document_id')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <multiselect
                                  :disabled="parseInt(data.has_child) == 1"
                                  v-model="$v.edit.document_id.$model"
                                  @select="showDocumentModalEdit"
                                  :options="documents.map((type) => type.id)"
                                  :custom-label="
                                    (opt) =>
                                      $i18n.locale == 'ar'
                                        ? documents.find((x) => x.id == opt)
                                            .name
                                        : documents.find((x) => x.id == opt)
                                            .name_e
                                  "
                                >
                                </multiselect>
                                <div
                                  v-if="
                                    $v.edit.document_id.$error ||
                                    errors.document_id
                                  "
                                  class="text-danger"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.document_id">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.document_id"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div class="col-md-6" v-if="isVisible('name')">
                              <div class="form-group">
                                <label for="field-u-1" class="control-label">
                                  {{ $t("general.Name") }}
                                  <span
                                    v-if="isRequired('name')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <div dir="rtl">
                                  <input
                                    type="text"
                                    class="form-control"
                                    v-model="$v.edit.name.$model"
                                    @keyup="arabicValueName(edit.name)"
                                    :class="{
                                      'is-invalid':
                                        $v.edit.name.$error || errors.name,
                                      'is-valid':
                                        !$v.edit.name.$invalid && !errors.name,
                                    }"
                                    id="field-u-1"
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
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-6" v-if="isVisible('name_e')">
                              <div class="form-group">
                                <label for="field-u-2" class="control-label">
                                  {{ $t("general.Name_en") }}
                                  <span
                                    v-if="isRequired('name_e')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <div dir="ltr">
                                  <input
                                    type="text"
                                    class="form-control"
                                    @keyup="englishValueName(edit.name_e)"
                                    v-model="$v.edit.name_e.$model"
                                    :class="{
                                      'is-invalid':
                                        $v.edit.name_e.$error || errors.name_e,
                                      'is-valid':
                                        !$v.edit.name_e.$invalid &&
                                        !errors.name_e,
                                    }"
                                    id="field-u-2"
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
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-6" v-if="isVisible('perfix')">
                              <div class="form-group">
                                <label for="field-2" class="control-label">
                                  {{ $t("general.Perfix") }}
                                  <span
                                    v-if="isRequired('perfix')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <input
                                  :disabled="parseInt(data.has_child) == 1"
                                  type="text"
                                  class="form-control"
                                  data-edit="3"
                                  v-model="$v.edit.perfix.$model"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.perfix.$error || errors.perfix,
                                    'is-valid':
                                      !$v.edit.perfix.$invalid &&
                                      !errors.perfix,
                                  }"
                                  id="edit-2"
                                />
                                <div
                                  v-if="!$v.edit.perfix.maxLength"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.Itmustbeatmost") }}
                                  {{ $v.edit.perfix.$params.maxLength.max }}
                                  {{ $t("general.letters") }}
                                </div>
                                <template v-if="errors.perfix">
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
                            <div class="col-md-6" v-if="isVisible('suffix')">
                              <div class="form-group">
                                <label for="field-1" class="control-label">
                                  {{ $t("general.Suffix") }}
                                  <span
                                    v-if="isRequired('suffix')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <input
                                  :disabled="parseInt(data.has_child) == 1"
                                  type="text"
                                  class="form-control"
                                  data-edit="2"
                                  v-model="$v.edit.suffix.$model"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.suffix.$error || errors.suffix,
                                    'is-valid':
                                      !$v.edit.suffix.$invalid &&
                                      !errors.suffix,
                                  }"
                                  id="edit-1"
                                />
                                <div
                                  v-if="!$v.edit.suffix.maxLength"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.Itmustbeatmost") }}
                                  {{ $v.edit.suffix.$params.maxLength.max }}
                                  {{ $t("general.letters") }}
                                </div>
                                <template v-if="errors.suffix">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.suffix"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div
                              class="col-md-6"
                              v-if="isVisible('restart_period_id')"
                            >
                              <div class="form-group">
                                <label class="control-label">
                                  {{ $t("general.RestartPeriod") }}
                                  <span
                                    v-if="isRequired('restart_period_id')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <multiselect
                                  v-model="$v.edit.restart_period_id.$model"
                                  :options="types.map((type) => type.id)"
                                  :custom-label="
                                    (opt) =>
                                      $i18n.locale == 'ar'
                                        ? types.find((x) => x.id == opt).name
                                        : types.find((x) => x.id == opt).name_e
                                  "
                                >
                                </multiselect>
                                <div
                                  v-if="
                                    $v.edit.restart_period_id.$error ||
                                    errors.restart_period_id
                                  "
                                  class="text-danger"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.restart_period_id">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.restart_period_id"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div class="col-md-6" v-if="isVisible('start_no')">
                              <div class="form-group">
                                <label for="field-15" class="control-label">
                                  {{ $t("general.StartNo") }}
                                  <span
                                    v-if="isRequired('start_no')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <input
                                  :disabled="parseInt(data.has_child) == 1"
                                  type="number"
                                  class="form-control"
                                  data-edit="1"
                                  v-model="$v.edit.start_no.$model"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.start_no.$error ||
                                      errors.start_no,
                                    'is-valid':
                                      !$v.edit.start_no.$invalid &&
                                      !errors.start_no,
                                  }"
                                  id="edit-15"
                                />
                                <div
                                  v-if="!$v.edit.start_no.maxLength"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.Itmustbeatmost") }}
                                  {{ $v.edit.start_no.$params.maxLength.max }}
                                  {{ $t("general.letters") }}
                                </div>
                                <template v-if="errors.start_no">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.start_no"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div
                              class="col-md-6"
                              v-if="isVisible('is_default')"
                            >
                              <div class="form-group">
                                <label class="mr-2">
                                  {{ $t("general.Default") }}
                                  <span
                                    v-if="isRequired('is_default')"
                                    class="text-danger"
                                    >*</span
                                  >
                                </label>
                                <b-form-group
                                  :class="{
                                    'is-invalid':
                                      $v.edit.is_default.$error ||
                                      errors.is_default,
                                    'is-valid':
                                      !$v.edit.is_default.$invalid &&
                                      !errors.is_default,
                                  }"
                                >
                                  <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.edit.is_default.$model"
                                    name="some-radios"
                                    value="1"
                                    >{{ $t("general.Yes") }}</b-form-radio
                                  >
                                  <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.edit.is_default.$model"
                                    name="some-radios"
                                    value="0"
                                    >{{ $t("general.No") }}</b-form-radio
                                  >
                                </b-form-group>
                                <template v-if="errors.is_default">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.is_default"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
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
                        @mouseover="log(data.id)"
                        @mousemove="log(data.id)"
                        type="button"
                        class="btn"
                        data-toggle="tooltip"
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

<style>
@media print {
  .do-not-print {
    display: none;
  }
  .arrow-sort {
    display: none;
  }
  .text-success {
    background-color: unset;
    color: #6c757d !important;
    border: unset;
  }
  .text-danger {
    background-color: unset;
    color: #6c757d !important;
    border: unset;
  }
}
</style>
