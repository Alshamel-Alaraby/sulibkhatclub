<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import { required, minLength, maxLength, integer, requiredIf } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import alphaArabic from "../../../helper/alphaArabic";
import alphaEnglish from "../../../helper/alphaEnglish";
import { dynamicSortString } from "../../../helper/tableSort";
import senderHoverHelper from "../../../helper/senderHoverHelper";
import { formatDateOnly } from "../../../helper/startDate";
import translation from "../../../helper/translation-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Statuses",
    meta: [{ name: "Statuses", content: "Statuses" }],
  },
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
  },
  mixins: [translation],
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      if (
        vm.$store.state.auth.work_flow_trees.includes("units") ||
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
      per_page: 50,
      search: "",
      debounce: {},
      statusesPagination: {},
      statuses: [],
      parents: [],
      isLoader: false,
      create: {
        name: "",
        name_e: "",
        color: "",
        icon: "",
        module_type:""
      },
      company_id: null,
      edit: {
        name: "",
        name_e: "",
        color: "",
        icon: "",
        module_type:""
      },
      errors: {},
      dropDownSenders: [],
      isButton: true,
      isCheckAll: false,
      checkAll: [],
      is_disabled: false,
      current_page: 1,
      setting: {
        name: true,
        name_e: true,
        icon: true,
        color: true,
        module_type:true
      },
      filterSetting: ["name", "name_e", 'color', 'icon',"module_type"],
      Tooltip: "",
      mouseEnter: null,
      enabled3: true,
      printLoading: true,
      printObj: {
        id: "printBuilding",
      }
    };
  },
  validations: {
    create: {
      name: {
        required: requiredIf(function (model) {
          return this.isRequired("name");
        }), minLength: minLength(3), maxLength: maxLength(100)
      },
      name_e: {
        required: requiredIf(function (model) {
          return this.isRequired("name_e");
        }), minLength: minLength(3), maxLength: maxLength(100)
      },
      color: {
        required: requiredIf(function (model) {
          return this.isRequired("color");
        }), minLength: minLength(3), maxLength: maxLength(100)
      },
      icon: {
        required: requiredIf(function (model) {
          return this.isRequired("icon");
        }), minLength: minLength(3), maxLength: maxLength(100)
      },
      module_type: {
        required: requiredIf(function (model) {
          return this.isRequired("module_type");
        }), minLength: minLength(3), maxLength: maxLength(100)
      },
    },
    edit: {
      name: {
        required: requiredIf(function (model) {
          return this.isRequired("name");
        }), minLength: minLength(3), maxLength: maxLength(100)
      },
      name_e: {
        required: requiredIf(function (model) {
          return this.isRequired("name_e");
        }), minLength: minLength(3), maxLength: maxLength(100)
      },
      color: {
        required: requiredIf(function (model) {
          return this.isRequired("color");
        }), minLength: minLength(3), maxLength: maxLength(100)
      },
      icon: {
        required: requiredIf(function (model) {
          return this.isRequired("icon");
        }), minLength: minLength(3), maxLength: maxLength(100)
      },
      module_type: {
        required: requiredIf(function (model) {
          return this.isRequired("module_type");
        }), minLength: minLength(3), maxLength: maxLength(100)
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
        this.statuses.forEach((el) => {
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
    getCustomTableFields() {
      adminApi
        .get(`/customTable/table-columns/general_statuses`)
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
     *  start get Data module && pagination
     */
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      for (let i = 0; i < this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/statuses?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.statuses = l.data;
          this.statusesPagination = l.pagination;
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
        this.current_page <= this.statusesPagination.last_page &&
        this.current_page != this.statusesPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/statuses?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.statuses = l.data;
            this.statusesPagination = l.pagination;
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
              .post(`/statuses/bulk-delete`, { ids: id })
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
              .delete(`/statuses/${id}`)
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
        name: "",
        name_e: "",
        color: "",
        icon: "",
        module_type: "",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_disabled = false;
      this.errors = {};
      this.$bvModal.hide(`create`);
    },
    /**
     *  hidden Modal (create)
     */
    resetModal() {
      this.create = {
        name: "",
        name_e: "",
        color: "",
        icon: "",
        module_type:""
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    resetForm() {
      this.create = {
        name: "",
        name_e: "",
        color: "",
        icon: "",
        module_type: "",

      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_disabled = false;
      this.errors = {};
    },
    /**
     *  create module
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
        this.isLoader = true;
        this.errors = {};
        adminApi
          .post(`/statuses`, { ...this.create, company_id: this.company_id })
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
          .put(`/statuses/${id}`, this.edit)
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
    resetModalEdit(id) {
      let module = this.statuses.find((e) => id == e.id);
      this.edit.name = module.name;
      this.edit.name_e = module.name_e;
      this.edit.color = module.color;
      this.edit.icon = module.icon;
      this.edit.module_type = module.module_type;
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
        color: "",
        icon: "",
        module_type: "",
      };
    },

    /**
     *  start  dynamicSortString
     */
    sortString(value) {
      return dynamicSortString(value);
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
    formatDate(value) {
      return formatDateOnly(value);
    },
    log(id) {
      if (this.mouseEnter != id) {
        this.Tooltip = "";
        this.mouseEnter = id;
        adminApi
          .get(`/statuses/logs/${id}`)
          .then((res) => {
            let l = res.data.data;
            l.forEach((e) => {
              this.Tooltip += `Created By: ${e.causer_type}; Event: ${e.event
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
          XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' });
        } else {
          XLSX.writeFile(wb, fn || (('Building' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
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
    }
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
              <h4 class="header-title">{{ $t("general.statusesTable") }}</h4>
              <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                <div class="d-inline-block" style="width: 22.2%">
                  <!-- Basic dropdown -->
                  <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                    class="btn-block setting-search">
                    <b-form-checkbox v-if="isVisible('name')" v-model="filterSetting" value="name" class="mb-1">
                      {{ getCompanyKey("statuses_name_ar") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-if="isVisible('name_e')" v-model="filterSetting" value="name_e" class="mb-1">
                      {{ getCompanyKey("statuses_name_en") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-if="isVisible('icon')" v-model="filterSetting" value="icon" class="mb-1">
                      {{ getCompanyKey("statuses_icon") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-if="isVisible('color')" v-model="filterSetting" value="color" class="mb-1">
                      {{ getCompanyKey("statuses_color") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-if="isVisible('module_type')" v-model="filterSetting" value="module_type" class="mb-1">
                      {{ getCompanyKey("statuses_module_type") }}
                    </b-form-checkbox>
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
                  <button class="custom-btn-dowonload" v-print="'#printCustom'">
                    <i class="fe-printer"></i>
                  </button>
                  <button class="custom-btn-dowonload" @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                    v-if="checkAll.length == 1">
                    <i class="mdi mdi-square-edit-outline"></i>
                  </button>
                  <!-- start mult delete  -->
                  <button class="custom-btn-dowonload" v-if="checkAll.length > 1" @click.prevent="deleteModule(checkAll)">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button class="custom-btn-dowonload" v-if="checkAll.length == 1"
                    @click.prevent="deleteModule(checkAll[0])">
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
                      <b-form-checkbox v-if="isVisible('name')" v-model="setting.name" class="mb-1">{{
                        getCompanyKey("statuses_name_ar") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-if="isVisible('name_e')" v-model="setting.name_e" class="mb-1">
                        {{ getCompanyKey("statuses_name_en") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-if="isVisible('color')" v-model="setting.color" class="mb-1">
                        {{ getCompanyKey("statuses_color") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-if="isVisible('icon')" v-model="setting.icon" class="mb-1">
                        {{ getCompanyKey("statuses_icon") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-if="isVisible('module_type')" v-model="setting.module_type" class="mb-1">
                        {{ getCompanyKey("statuses_module_type") }}
                      </b-form-checkbox>
                      
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
                      {{ statusesPagination.from }}-{{ statusesPagination.to }} /
                      {{ statusesPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a href="javascript:void(0)" :style="{
                        'pointer-events':
                          statusesPagination.current_page == 1 ? 'none' : '',
                      }" @click.prevent="getData(statusesPagination.current_page - 1)">
                        <span>&lt;</span>
                      </a>
                      <input type="text" @keyup.enter="getDataCurrentPage()" v-model="current_page"
                        class="pagination-current-page" />
                      <a href="javascript:void(0)" :style="{
                        'pointer-events':
                          statusesPagination.last_page == statusesPagination.current_page
                            ? 'none'
                            : '',
                      }" @click.prevent="getData(statusesPagination.current_page + 1)">
                        <span>&gt;</span>
                      </a>
                    </div>
                  </div>
                  <!-- end Pagination -->
                </div>
              </div>
            </div>

            <!--  create   -->
            <b-modal id="create" :title="getCompanyKey('statuses_create_form')" title-class="font-18" body-class="p-4 "
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
                  <div class="col-md-12" v-if="isVisible('name')">
                    <div class="form-group">
                      <label for="field-1" class="control-label">
                        {{ getCompanyKey("statuses_name_ar") }}
                        <span v-if="isRequired('name')" class="text-danger">*</span>
                      </label>
                      <div dir="rtl">
                        <input type="text" class="form-control" data-create="1" @keyup="arabicValueName(create.name)"
                          v-model="$v.create.name.$model" :class="{
                            'is-invalid': $v.create.name.$error || errors.name,
                            'is-valid': !$v.create.name.$invalid && !errors.name,
                          }" id="field-1" />
                      </div>
                      <div v-if="!$v.create.name.minLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatleast") }}
                        {{ $v.create.name.$params.minLength.min }}
                        {{ $t("general.letters") }}
                      </div>
                      <div v-if="!$v.create.name.maxLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatmost") }}
                        {{ $v.create.name.$params.maxLength.max }}
                        {{ $t("general.letters") }}
                      </div>
                      <template v-if="errors.name">
                        <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index">
                          {{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-12" v-if="isVisible('name_e')">
                    <div class="form-group">
                      <label for="field-2" class="control-label">
                        {{ getCompanyKey("statuses_name_en") }}
                        <span v-if="isRequired('name_e')" class="text-danger">*</span>
                      </label>
                      <div dir="ltr">
                        <input type="text" class="form-control" data-create="2" @keyup="englishValueName(create.name_e)"
                          v-model="$v.create.name_e.$model" :class="{
                            'is-invalid': $v.create.name_e.$error || errors.name_e,
                            'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                          }" id="field-2" />
                      </div>
                      <div v-if="!$v.create.name_e.minLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatleast") }}
                        {{ $v.create.name_e.$params.minLength.min }}
                        {{ $t("general.letters") }}
                      </div>
                      <div v-if="!$v.create.name_e.maxLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatmost") }}
                        {{ $v.create.name_e.$params.maxLength.max }}
                        {{ $t("general.letters") }}
                      </div>
                      <template v-if="errors.name_e">
                        <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index">{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-12" v-if="isVisible('icon')">
                    <div class="form-group">
                      <label for="field-3" class="control-label">
                        {{ getCompanyKey("statuses_icon") }}
                        <span v-if="isRequired('icon')" class="text-danger">*</span>
                      </label>
                      <div>
                        <input type="text" class="form-control" v-model="$v.create.icon.$model" :class="{
                          'is-invalid': $v.create.icon.$error || errors.icon,
                          'is-valid': !$v.create.icon.$invalid && !errors.icon,
                        }" id="field-3" />
                      </div>
                      <template v-if="errors.icon">
                        <ErrorMessage v-for="(errorMessage, index) in errors.icon" :key="index">
                          {{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-12" v-if="isVisible('color')">
                    <div class="form-group">
                      <label for="field-4" class="control-label">
                        {{ getCompanyKey("statuses_color") }}
                        <span v-if="isRequired('color')" class="text-danger">*</span>
                      </label>
                      <div dir="ltr">
                        <input type="color" class="form-control" v-model="$v.create.color.$model" :class="{
                          'is-invalid': $v.create.color.$error || errors.color,
                          'is-valid': !$v.create.color.$invalid && !errors.color,
                        }" id="field-4" />
                      </div>
                      <template v-if="errors.color">
                        <ErrorMessage v-for="(errorMessage, index) in errors.color" :key="index">{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-12" v-if="isVisible('module_type')">
                    <div class="form-group">
                      <label for="field-4" class="control-label">
                        {{ getCompanyKey("statuses_module_type") }}
                        <span v-if="isRequired('module_type')" class="text-danger">*</span>
                      </label>
                      <div>
                        <input type="text" class="form-control" v-model="$v.create.module_type.$model" :class="{
                          'is-invalid': $v.create.module_type.$error || errors.module_type,
                          'is-valid': !$v.create.module_type.$invalid && !errors.module_type,
                        }" id="field-4" />
                      </div>
                      <template v-if="errors.module_type">
                        <ErrorMessage v-for="(errorMessage, index) in errors.module_type" :key="index">{{ errorMessage }}
                        </ErrorMessage>
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

              <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                id="printBuilding">
                <thead>
                  <tr>
                    <th scope="col" style="width: 0" v-if="enabled3" class="do-not-print">
                      <div class="form-check custom-control">
                        <input class="form-check-input" type="checkbox" v-model="isCheckAll"
                          style="width: 17px; height: 17px" />
                      </div>
                    </th>
                    <th v-if="setting.name && isVisible('name')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("statuses_name_ar") }}</span>
                        <div class="arrow-sort">
                          <i class="fas fa-arrow-up" @click="statuses.sort(sortString('name'))"></i>
                          <i class="fas fa-arrow-down" @click="statuses.sort(sortString('-name'))"></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.name_e && isVisible('name_e')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("statuses_name_en") }}</span>
                        <div class="arrow-sort">
                          <i class="fas fa-arrow-up" @click="statuses.sort(sortString('name_e'))"></i>
                          <i class="fas fa-arrow-down" @click="statuses.sort(sortString('-name_e'))"></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.name && isVisible('icon')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("statuses_icon") }}</span>
                      </div>
                    </th>
                    <th v-if="setting.name_e && isVisible('color')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("statuses_color") }}</span>
                      </div>
                    </th>
                    <th v-if="setting.module_type && isVisible('module_type')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("statuses_module_type") }}</span>
                      </div>
                    </th>
                    <th v-if="enabled3" class="do-not-print">
                      {{ $t("general.Action") }}
                    </th>
                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                  </tr>
                </thead>
                <tbody v-if="statuses.length > 0">
                  <tr @click.capture="checkRow(data.id)" @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in statuses" :key="data.id" class="body-tr-custom">
                    <td v-if="enabled3" class="do-not-print">
                      <div class="form-check custom-control" style="min-height: 1.9em">
                        <input style="width: 17px; height: 17px" class="form-check-input" type="checkbox" :value="data.id"
                          v-model="checkAll" />
                      </div>
                    </td>
                    <td v-if="setting.name && isVisible('name')">
                      <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                    </td>
                    <td v-if="setting.name_e && isVisible('name_e')">
                      <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
                    </td>
                    <td v-if="setting.name && isVisible('icon')">
                      <h5 class="m-0 font-weight-normal">{{ data.icon }}</h5>
                    </td>
                    <td v-if="setting.name_e && isVisible('color')">
                      <h5 class="m-0 font-weight-normal">{{ data.color }}</h5>
                    </td>
                    <td v-if="setting.module_type && isVisible('module_type')">
                      <h5 class="m-0 font-weight-normal">{{ data.module_type }}</h5>
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
                          <a class="dropdown-item text-black" href="#" @click.prevent="deleteModule(data.id)">
                            <div class="d-flex justify-content-between align-items-center text-black">
                              <span>{{ $t("general.delete") }}</span>
                              <i class="fas fa-times text-danger"></i>
                            </div>
                          </a>
                        </div>
                      </div>

                      <!--  edit   -->
                      <b-modal :id="`modal-edit-${data.id}`" :title="getCompanyKey('statuses_edit_form')"
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
                            <div class="col-md-12" v-if="isVisible('name')">
                              <div class="form-group">
                                <label for="field-u-1" class="control-label">
                                  {{ getCompanyKey("statuses_name_ar") }}
                                  <span v-if="isRequired('name')" class="text-danger">*</span>
                                </label>
                                <div dir="rtl">
                                  <input type="text" class="form-control" v-model="$v.edit.name.$model"
                                    @keyup="arabicValueName(edit.name)" :class="{
                                      'is-invalid': $v.edit.name.$error || errors.name,
                                      'is-valid': !$v.edit.name.$invalid && !errors.name,
                                    }" id="field-u-1" />
                                </div>
                                <div v-if="!$v.edit.name.minLength" class="invalid-feedback">
                                  {{ $t("general.Itmustbeatleast") }}
                                  {{ $v.edit.name.$params.minLength.min }}
                                  {{ $t("general.letters") }}
                                </div>
                                <div v-if="!$v.edit.name.maxLength" class="invalid-feedback">
                                  {{ $t("general.Itmustbeatmost") }}
                                  {{ $v.edit.name.$params.maxLength.max }}
                                  {{ $t("general.letters") }}
                                </div>
                                <template v-if="errors.name">
                                  <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index">{{ errorMessage
                                  }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-12" v-if="isVisible('name_e')">
                              <div class="form-group">
                                <label for="field-u-2" class="control-label">
                                  {{ getCompanyKey("statuses_name_en") }}
                                  <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                </label>
                                <div dir="ltr">
                                  <input type="text" class="form-control" @keyup="englishValueName(edit.name_e)"
                                    v-model="$v.edit.name_e.$model" :class="{
                                      'is-invalid':
                                        $v.edit.name_e.$error || errors.name_e,
                                      'is-valid':
                                        !$v.edit.name_e.$invalid && !errors.name_e,
                                    }" id="field-u-2" />
                                </div>
                                <div v-if="!$v.edit.name_e.minLength" class="invalid-feedback">
                                  {{ $t("general.Itmustbeatleast") }}
                                  {{ $v.edit.name_e.$params.minLength.min }}
                                  {{ $t("general.letters") }}
                                </div>
                                <div v-if="!$v.edit.name_e.maxLength" class="invalid-feedback">
                                  {{ $t("general.Itmustbeatmost") }}
                                  {{ $v.edit.name_e.$params.maxLength.max }}
                                  {{ $t("general.letters") }}
                                </div>
                                <template v-if="errors.name_e">
                                  <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index">{{
                                    errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-12" v-if="isVisible('icon')">
                              <div class="form-group">
                                <label for="field-3" class="control-label">
                                  {{ getCompanyKey("statuses_icon") }}
                                  <span v-if="isRequired('icon')" class="text-danger">*</span>
                                </label>
                                <div>
                                  <input type="text" class="form-control" v-model="$v.edit.icon.$model" :class="{
                                    'is-invalid': $v.edit.icon.$error || errors.icon,
                                    'is-valid': !$v.edit.icon.$invalid && !errors.icon,
                                  }" id="field-33" />
                                </div>
                                <template v-if="errors.icon">
                                  <ErrorMessage v-for="(errorMessage, index) in errors.icon" :key="index">
                                    {{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-12" v-if="isVisible('color')">
                              <div class="form-group">
                                <label for="field-44" class="control-label">
                                  {{ getCompanyKey("statuses_color") }}
                                  <span v-if="isRequired('color')" class="text-danger">*</span>
                                </label>
                                <div dir="ltr">
                                  <input type="color" class="form-control" v-model="$v.edit.color.$model" :class="{
                                    'is-invalid': $v.edit.color.$error || errors.color,
                                    'is-valid': !$v.edit.color.$invalid && !errors.color,
                                  }" id="field-44" />
                                </div>
                                <template v-if="errors.color">
                                  <ErrorMessage v-for="(errorMessage, index) in errors.color" :key="index">{{ errorMessage
                                  }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-12" v-if="isVisible('module_type')">
                              <div class="form-group">
                                <label for="field-4" class="control-label">
                                  {{ getCompanyKey("statuses_module_type") }}
                                  <span v-if="isRequired('module_type')" class="text-danger">*</span>
                                </label>
                                <div>
                                  <input type="text" class="form-control" v-model="$v.edit.module_type.$model" :class="{
                                    'is-invalid': $v.edit.module_type.$error || errors.module_type,
                                    'is-valid': !$v.edit.module_type.$invalid && !errors.module_type,
                                  }" id="field-4" />
                                </div>
                                <template v-if="errors.module_type">
                                  <ErrorMessage v-for="(errorMessage, index) in errors.module_type" :key="index">{{
                                    errorMessage }}
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
                      <button @mousemove="log(data.id)" @mouseover="log(data.id)" type="button" class="btn"
                        :id="`tooltip-${data.id}`" :data-placement="$i18n.locale == 'en' ? 'left' : 'right'"
                        :title="Tooltip">
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
            <!-- end .table-responsive-->
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>
