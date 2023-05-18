<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {required, minLength, maxLength, integer, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import Role from "../../../components/create/role.vue";
import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../../helper/startDate";
import translation from "../../../helper/translation-mixin";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Role Workflows",
    meta: [{ name: "Role Workflows", content: "Role Workflow" }],
  },
  mixins: [translation],
  components: {
    Role,
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
  },
  data() {
    return {
        fields: [],
        enabled3: true,
        printLoading: true,
        printObj: {
            id: "printCustom",
        },
      per_page: 50,
      search: "",
      Tooltip: "",
      mouseEnter: null,
      debounce: {},
      roleWorkflowsPagination: {},
      roleWorkflows: [],
      isLoader: false,
      usersCount: 0,
      create: {
        role_id: null,
        workflow_id: null,
        workflow_name: "",
      },
      edit: {
        role_id: null,
        workflow_id: null,
        workflow_name: "",
      },
      errors: {},
      isCheckAll: false,
      checkAll: [],
      roles: [],
      workflows: [],
      current_page: 1,
      setting: {
        role_id: true,
        workflow_id: true,
      },
      company_id: null,
      is_disabled: false,
      filterSetting: [
        this.$i18n.locale == "ar" ? "role.name" : "role.name_e",
        "workflow_id",
      ],
    };
  },
  validations: {
    create: {
      role_id: { required: requiredIf(function (model) {
              return this.isRequired("role_id");
          }) },
      workflow_id: { required: requiredIf(function (model) {
              return this.isRequired("role_id");
          }) },
    },
    edit: {
        role_id: { required: requiredIf(function (model) {
                return this.isRequired("role_id");
            }) },
        workflow_id: { required: requiredIf(function (model) {
                return this.isRequired("role_id");
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
        this.roleWorkflows.forEach((el) => {
          if (!this.checkAll.includes(el.id)) {
            this.checkAll.push(el.id);
          }
        });
      } else {
        this.checkAll = [];
      }
    },
  },
  async mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
    this.getCustomTableFields();
    await this.getWorkflow();
    await this.getData();
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      if (vm.$store.state.auth.work_flow_trees.includes("role-e")) {
        Swal.fire({
          icon: "error",
          title: `${vm.$t("general.Error")}`,
          text: `${vm.$t("general.ModuleExpired")}`,
        });
        return vm.$router.push({ name: "home" });
      }

    if (
        (vm.showScreen( "role","role workflow") &&
          vm.$store.state.auth.work_flow_trees.includes("role")) ||
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
              .get(`/customTable/table-columns/general_role_workflows`)
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
    showScreen(module, screen) {
      let filterRes = this.$store.state.auth.allWorkFlow.filter(
        (workflow) => workflow.name_e == module
      );
      let _module = filterRes.length ? filterRes[0] : null;
      if (!_module) return false;
      return _module.screen ? _module.screen.name_e == screen : true;
    },
    /**
     *  start get Data module && pagination
     */
    async getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      for (let i = 0; i < this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      await adminApi
        .get(
          `/role-workflows?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.roleWorkflows = l.data;
          this.roleWorkflowsPagination = l.pagination;
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
        this.current_page <= this.roleWorkflowsPagination.last_page &&
        this.current_page != this.roleWorkflowsPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/role-workflows?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.roleWorkflows = l.data;
            this.roleWorkflowsPagination = l.pagination;
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
     *  end get Data module && pagination
     */
    /**
     *  start delete module
     */
    deleteModule(id, index) {
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
              .post(`/role-workflows/bulk-delete`, { ids: id })
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
              .delete(`/role-workflows/${id}`)
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
     *  end delete module
     */
    /**
     *  reset Modal (create)
     */
    resetModalHidden() {
      this.create = {
        role_id: null,
        workflow_id: null,
        workflow_name: "",
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
      await this.getRole();
      await this.getWorkflow();
      this.create = {
        role_id: null,
        workflow_id: null,
        workflow_name: "",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create module
     */
    async resetForm() {
      await this.getRole();
      await this.getWorkflow();
      this.create = {
        role_id: null,
        workflow_id: null,
        workflow_name: "",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_disabled = false;
      this.errors = {};
    },

    async AddSubmit() {
      this.$v.create.$touch();
      if (this.$v.create.$invalid) {
        return;
      }
      await this.getRoleUsersCount(this.create.role_id);
      let self = this;
      let check =
        this.$store.state.auth.allWorkFlow.filter((workflow) => {
          return (
            workflow.id == self.create.workflow_id &&
            workflow.module_id &&
            self.usersCount > workflow.module_allowed_users_no
          );
        }).length > 0;
      if (check) {
        Swal.fire({
          icon: "error",
          text: `${this.$t("general.ExceedMaxUserNumber")}`,
          showConfirmButton: false,
          timer: 1500,
        });
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.create.role_id = this.create.role_id;
        this.create.workflow_id = this.create.workflow_id;
        let module = this.workflows.find((e) => this.create.workflow_id == e.id);
        this.create.workflow_name = module.name_e;
        adminApi
          .post(`/role-workflows`, { ...this.create, company_id: this.company_id })
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
     *  edit module
     */
    async editSubmit(id) {
      this.$v.edit.$touch();
      if (this.$v.edit.$invalid) {
        return;
      }
      await this.getRoleUsersCount(this.edit.role_id);

      let self = this;
      let check =
        this.$store.state.auth.allWorkFlow.filter((workflow) => {
          return (
            workflow.id == self.edit.workflow_id &&
            workflow.module_id &&
            self.usersCount > workflow.module_allowed_users_no
          );
        }).length > 0;
      if (check) {
        Swal.fire({
          icon: "error",
          text: `${this.$t("general.ExceedMaxUserNumber")}`,
          showConfirmButton: false,
          timer: 1500,
        });
        return;
      } else {
        this.isLoader = true;
        this.errors = {};

        adminApi
          .put(`/role-workflows/${id}`, this.edit)
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
      await this.getRole();
      await this.getWorkflow();
      let module = this.roleWorkflows.find((e) => id == e.id);
      this.edit.workflow_id = module.workflow_id;
      this.edit.workflow_name = module.name_e;
      this.edit.role_id = module.role.id;
      this.errors = {};
    },
    resetModalHiddenEdit(id) {
      this.create = {
        role_id: null,
        workflow_id: null,
        workflow_name: "",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_disabled = false;
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
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
    async getRole() {
      this.isLoader = true;

      await adminApi
        .get(`/roles`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضف  الدور", name_e: "Add Role" });
          this.roles = l;
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
    async getRoleUsersCount(id) {
      this.isLoader = true;
      await adminApi
        .get(`user-role/user-count/${id}`)
        .then((res) => {
          this.usersCount = res.data.user_count;
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
    async getWorkflow() {
      this.isLoader = true;

      await axios
        .get(
          `${process.env.MIX_APP_URL_OUTSIDE}api/everything_about_the_company/${this.company_id}`
        )
        .then((res) => {
          let l = res.data.data;
          this.workflows = l.work_flow_trees;
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
    showRoleModal() {
      if (this.create.role_id == 0) {
        this.$bvModal.show("role-create");
        this.create.role_id = null;
      }
    },
    showRoleModalEdit() {
      if (this.edit.role_id == 0) {
        this.$bvModal.show("role-create");
        this.edit.role_id = null;
      }
    },
    formatDate(value) {
      return formatDateOnly(value);
    },
    log(id) {
      if (this.mouseEnter != id) {
        this.Tooltip = "";
        this.mouseEnter = id;
        adminApi
          .get(`/role-workflows/logs/${id}`)
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
              let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
              if (dl) {
                  XLSX.write(wb, {bookType: type, bookSST: true, type: 'base64'});
              } else {
                  XLSX.writeFile(wb, fn || (('RoleWorkflow' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
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
    <Role :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getRole" />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("general.roleWorkflowsTable") }}</h4>
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
                      v-if="isVisible('role_id')"
                      v-model="filterSetting"
                      :value="$i18n.locale == 'ar' ? 'role.name' : 'role.name_e'"
                      class="mb-1"
                    >
                      {{ getCompanyKey("role") }}
                    </b-form-checkbox>
                    <b-form-checkbox
                      v-model="filterSetting"
                      v-if="isVisible('workflow_id')"
                      value="workflow_id"
                      class="mb-1"
                    >
                      {{ getCompanyKey("workflow") }}
                    </b-form-checkbox>
                  </b-dropdown>
                  <!-- Basic dropdown -->
                </div>

                <div class="d-inline-block position-relative" style="width: 77%">
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

            <div class="row justify-content-between align-items-center mb-2 px-1">
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
                  <button class="custom-btn-dowonload" @click="ExportExcel('xlsx')">
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button class="custom-btn-dowonload"  v-print="'#printCustom'">
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
                    @click.prevent="deleteModule(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1"
                    @click.prevent="deleteModule(checkAll[0])"
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
                      :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                      ref="dropdown"
                      class="dropdown-custom-ali"
                    >
                      <b-form-checkbox v-if="isVisible('role_id')" v-model="setting.role_id" class="mb-1">
                        {{ getCompanyKey("role") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-if="isVisible('workflow_id')" v-model="setting.workflow_id" class="mb-1">
                        {{ getCompanyKey("workflow") }}
                      </b-form-checkbox>
                      <div class="d-flex justify-content-end">
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm"
                          >Apply</a
                        >
                      </div>
                    </b-dropdown>
                    <!-- Basic dropdown -->
                  </div>
                  <!-- end filter and setting -->

                  <!-- start Pagination -->
                  <div class="d-inline-flex align-items-center pagination-custom">
                    <div class="d-inline-block" style="font-size: 13px">
                      {{ roleWorkflowsPagination.from }}-{{ roleWorkflowsPagination.to }}
                      /
                      {{ roleWorkflowsPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            roleWorkflowsPagination.current_page == 1 ? 'none' : '',
                        }"
                        @click.prevent="getData(roleWorkflowsPagination.current_page - 1)"
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
                            roleWorkflowsPagination.last_page ==
                            roleWorkflowsPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="getData(roleWorkflowsPagination.current_page + 1)"
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
              :title="getCompanyKey('role_workflow_create_form')"
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
                    :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
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
                  <div class="col-md-12" v-if="isVisible('role_id')">
                    <div class="form-group">
                      <label class="my-1 mr-2">
                          {{ getCompanyKey("role") }}
                          <span v-if="isRequired('role_id')" class="text-danger">*</span>
                      </label>
                      <multiselect
                        @input="showRoleModal"
                        v-model="create.role_id"
                        :options="roles.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            $i18n.locale == 'ar'
                              ? roles.find((x) => x.id == opt).name
                              : roles.find((x) => x.id == opt).name_e
                        "
                      >
                      </multiselect>
                      <template v-if="errors.role_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.role_id"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div class="col-md-12" v-if="isVisible('workflow_id')">
                    <div class="form-group">
                      <label class="my-1 mr-2">
                          {{ getCompanyKey("workflow") }}
                          <span v-if="isRequired('workflow_id')" class="text-danger">*</span>
                      </label>
                      <multiselect
                        v-model="create.workflow_id"
                        :options="workflows.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            $i18n.locale == 'ar'
                              ? workflows.find((x) => x.id == opt).name
                              : workflows.find((x) => x.id == opt).name_e
                        "
                      >
                      </multiselect>
                      <template v-if="errors.workflow_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.workflow_id"
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
            <div class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table"
                 id="printCustom">
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->

              <table class="table table-borderless table-hover table-centered m-0">
                <thead>
                  <tr>
                    <th scope="col" style="width: 0" v-if="enabled3" class="do-not-print">
                      <div class="form-check custom-control">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          v-model="isCheckAll"
                          style="width: 17px; height: 17px"
                        />
                      </div>
                    </th>
                    <th v-if="setting.role_id && isVisible('role_id')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("role") }}</span>
                      </div>
                    </th>
                    <th v-if="setting.workflow_id && isVisible('workflow_id')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("workflow") }}</span>
                      </div>
                    </th>
                    <th v-if="enabled3" class="do-not-print">
                      {{ $t("general.Action") }}
                    </th>
                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                  </tr>
                </thead>
                <tbody v-if="roleWorkflows.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in roleWorkflows"
                    :key="data.id"
                    class="body-tr-custom"
                  >
                    <td v-if="enabled3" class="do-not-print">
                      <div class="form-check custom-control" style="min-height: 1.9em">
                        <input
                          style="width: 17px; height: 17px"
                          class="form-check-input"
                          type="checkbox"
                          :value="data.id"
                          v-model="checkAll"
                        />
                      </div>
                    </td>
                    <td v-if="setting.role_id && isVisible('role_id')">
                      <h5 class="m-0 font-weight-normal">
                        {{ $i18n.locale == "ar" ? data.role.name : data.role.name_e }}
                      </h5>
                    </td>
                    <td v-if="setting.workflow_id && isVisible('workflow_id')">
                      <h5 class="m-0 font-weight-normal">
                        {{
                          workflows.length > 0
                            ? $i18n.locale == "ar"
                              ? workflows.find((x) => x.id == data.workflow_id).name
                              : workflows.find((x) => x.id == data.workflow_id).name_e
                            : ""
                        }}
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
                              <i class="mdi mdi-square-edit-outline text-info"></i>
                            </div>
                          </a>
                          <a
                            class="dropdown-item text-black"
                            href="#"
                            @click.prevent="deleteModule(data.id)"
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
                        :title="getCompanyKey('role_workflow_edit_form')"
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

                            <b-button variant="success" class="mx-1" disabled v-else>
                              <b-spinner small></b-spinner>
                              <span class="sr-only">{{ $t("login.Loading") }}...</span>
                            </b-button>

                            <b-button
                              variant="danger"
                              type="button"
                              @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)"
                            >
                              {{ $t("general.Cancel") }}
                            </b-button>
                          </div>
                          <div class="row">
                            <div class="col-md-12" v-if="isVisible('role_id')">
                              <div class="form-group">
                                <label class="my-1 mr-2">{{
                                  getCompanyKey("role")
                                }}
                                    <span v-if="isRequired('role_id')" class="text-danger">*</span>
                                </label>
                                <multiselect
                                  @input="showRoleModalEdit"
                                  v-model="edit.role_id"
                                  :options="roles.map((type) => type.id)"
                                  :custom-label="
                                    (opt) =>
                                      $i18n.locale == 'ar'
                                        ? roles.find((x) => x.id == opt).name
                                        : roles.find((x) => x.id == opt).name_e
                                  "
                                >
                                </multiselect>
                                <template v-if="errors.role_id">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.role_id"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div class="col-md-12" v-if="isVisible('workflow_id')">
                              <div class="form-group">
                                <label class="my-1 mr-2">{{
                                  getCompanyKey("workflow")
                                }}
                                    <span v-if="isRequired('workflow_id')" class="text-danger">*</span>
                                </label>
                                <multiselect
                                  v-model="edit.workflow_id"
                                  :options="workflows.map((type) => type.id)"
                                  :custom-label="
                                    (opt) =>
                                      $i18n.locale == 'ar'
                                        ? workflows.find((x) => x.id == opt).name
                                        : workflows.find((x) => x.id == opt).name_e
                                  "
                                >
                                </multiselect>
                                <template v-if="errors.workflow_id">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.workflow_id"
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
                        @mousemove="log(data.id)"
                        @mouseover="log(data.id)"
                        type="button"
                        class="btn"
                        :id="`tooltip-${data.id}`"
                        :data-placement="$i18n.locale == 'en' ? 'left' : 'right'"
                        :title="Tooltip"
                      >
                        <i class="fe-info" style="font-size: 22px"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr>
                    <th class="text-center" colspan="6">
                      {{ $t("general.notDataFound") }}
                    </th>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- end .table-responsive -->
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>
