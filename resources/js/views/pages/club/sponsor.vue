<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {required, minLength, maxLength, integer, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import { dynamicSortString } from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../../helper/startDate";
import TreeBrowser from "../../../components/create/realEstate/tree.vue";
import {arabicValue,englishValue} from "../../../helper/langTransform";
import translation from "../../../helper/mixin/translation-mixin";
import permissionGuard from "../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Sponsor",
    meta: [{ name: "description", content: "Sponsor" }],
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
    TreeBrowser
  },
  beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Sponsor", "all sponsor club");
    });

    },
  data() {
    return {
      fields: [],
      per_page: 50,
      search: "",
      debounce: {},
      sponsorsPagination: {},
      sponsors: [],
      enabled3: true,
      isLoader: false,
      rootNodes: [],
      childNodes: [],
      groups: [],
      sponserGroups: [],
      Tooltip: "",
      mouseEnter: "",
      current_id: null,
      create: {
        name: "",
        name_e: "",
        parent_id: null,
        group_id : null
      },
      edit: {
        name: "",
        name_e: "",
        parent_id: null,
          group_id : null
      },
      setting: {
        name: true,
        name_e: true,
        parent_id: true,
        group_id: true,
      },
      filterSetting: ["name", "name_e",this.$i18n.locale == 'ar'?'sponsorGroup.name':'sponsorGroup.name_e'],
      errors: {},
      isCheckAll: false,
      checkAll: [],
      is_disabled: false,
      current_page: 1,
        printLoading: true,
        printObj: {
            id: "printMe",
        }
    };
  },
  validations: {
    create: {
      name: { required: requiredIf(function (model) {
              return this.isRequired("name");
          }), minLength: minLength(3), maxLength: maxLength(100) },
      name_e: {
          required: requiredIf(function (model) {
              return this.isRequired("name_e");
          }),
        minLength: minLength(3),
        maxLength: maxLength(100),
      },
      group_id: {required: requiredIf(function (model) {
              return this.isRequired("group_id");
          })
      },
    },
    edit: {
      name: { required: requiredIf(function (model) {
              return this.isRequired("name");
          }), minLength: minLength(3), maxLength: maxLength(100) },
      name_e: {
          required: requiredIf(function (model) {
              return this.isRequired("name_e");
          }),
        minLength: minLength(3),
        maxLength: maxLength(100),
      },
        group_id: {required: requiredIf(function (model) {
                return this.isRequired("group_id");
            })
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
        this.sponsors.forEach((el) => {
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
  },
  methods: {
    getCustomTableFields() {
          adminApi
              .get(`/customTable/table-columns/cm_sponsers`)
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
    isPermission(item) {
          if (this.$store.state.auth.type == 'user'){
              return this.$store.state.auth.permissions.includes(item)
          }
          return true;
      },
    getGroup() {
          this.isLoader = true;
          adminApi
              .get(`/club-members/sponsor-group`)
              .then((res) => {
                  this.groups = res.data.data;
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
    formatDate(value) {
      return formatDateOnly(value);
    },
    log(id) {
      if (this.mouseEnter != id) {
        this.Tooltip = "";
        this.mouseEnter = id;
        adminApi
          .get(`club-members/sponsers/logs/${id}`)
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
    /**
     *  get Data module
     */
    getData(page = 1) {
      this.isLoader = true;

      let filter = "";
      for (let i = 0; i < this.filterSetting.length; i++) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      adminApi
        .get(
          `club-members/sponsers?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.sponsors = l.data;
          this.sponsorsPagination = l.pagination;
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
    getDataCurrentPage() {
      if (
        this.current_page <= this.sponsorsPagination.last_page &&
        this.current_page != this.sponsorsPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; i++) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `club-members/sponsers?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.sponsors = l.data;
            this.sponsorsPagination = l.pagination;
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
    getSponserGroup(id) {
          this.isLoader = true;

          adminApi
              .get(
                  `club-members/sponsers/get-sponsors/${id}`
              )
              .then((res) => {
                  let l = res.data.data;
                  this.sponserGroups = l;
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
    /**
     *  delete module
     */
    deleteModule(id, tree = false) {
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
              .post(`club-members/sponsers/bulk-delete`, { ids: id })
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
              .delete(`club-members/sponsers/${id}`)
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
     *  reset Modal (create)
     */
    resetModalHidden() {
      this.create = { name: "", name_e: "", parent_id: null,group_id : null };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
        this.is_disabled = false;
      this.rootNodes = [];
    },
    /**
     *  hidden Modal (create)
     */
    resetModal() {
      this.getGroup();
      this.create = { name: "", name_e: "", parent_id: null,group_id : null  };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create module
     */
    resetForm() {
      this.create = { name: "", name_e: "", parent_id: null ,group_id : null  };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
    },
    AddSubmit() {
      if (this.create.name || this.create.name_e) {
        this.create.name = this.create.name ? this.create.name : this.create.name_e;
        this.create.name_e = this.create.name_e ? this.create.name_e : this.create.name;
      }
      this.$v.create.$touch();
      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;
        if (this.create.parent_id == null) {
          this.create.parent_id = 0;
        }
        adminApi
          .post(`club-members/sponsers`, {...this.create,company_id: this.$store.getters["auth/company_id"],})
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
     *  edit module
     */
    editSubmit(id) {
      if (this.edit.name || this.edit.name_e) {
        this.edit.name = this.edit.name ? this.edit.name : this.edit.name_e;
        this.edit.name_e = this.edit.name_e ? this.edit.name_e : this.edit.name;
      }
      this.$v.edit.$touch();
      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        if (!this.edit.parent_id) {
          this.edit.parent_id = 0;
        }
      adminApi
          .put(`club-members/sponsers/${id}`, {...this.edit,company_id: this.$store.getters["auth/company_id"],})
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
      this.getGroup();
      let module = this.sponsors.find((e) => id == e.id);
      this.edit.name = module.name;
      this.edit.name_e = module.name_e;
      this.edit.parent_id = module.parent_id;
      this.edit.group_id = module.group_id;
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
        parent_id: null,
          group_id : null
      };
      this.rootNodes = [];
    },
    sortString(value) {
      return dynamicSortString(value);
    },
    checkRow(id) {
      if (!this.checkAll.includes(id)) {
        this.checkAll.push(id);
      } else {
        let index = this.checkAll.indexOf(id);
        this.checkAll.splice(index, 1);
      }
    },
      ExportExcel(type, fn, dl) {
          this.enabled3 = false;
          setTimeout(()=>{
              let elt = this.$refs.exportable_table;
              let wb = XLSX.utils.table_to_book(elt, {sheet:"Sheet JS"});
              if (dl){
                  XLSX.write(wb, {bookType:type, bookSST:true, type: 'base64'});
              }else {
                  XLSX.writeFile(wb, fn || (('Module' + '.'|| 'SheetJSTableExport.') + (type || 'xlsx')));
              }
              this.enabled3 = true;
          },100);
      },
      arabicValue(txt){
          this.create.name = arabicValue(txt);
          this.edit.name = arabicValue(txt);
      } ,
      englishValue(txt){
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
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("general.sponsorTable") }}</h4>
              <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                <div class="d-inline-block" style="width: 22.2%">
                  <!-- Basic dropdown -->
                  <b-dropdown
                    variant="primary"
                    :text="$t('general.searchSetting')"
                    ref="dropdown"
                    class="btn-block setting-search"
                  >
                    <b-form-checkbox v-if="isVisible('name')" v-model="filterSetting" value="name" class="mb-1">{{
                      getCompanyKey("sponsor_name_ar")
                    }}</b-form-checkbox>sponsorGroup
                    <b-form-checkbox
                      v-if="isVisible('name_e')"
                      v-model="filterSetting"
                      value="name_e"
                      class="mb-1"
                      >
                        {{ getCompanyKey("sponsor_name_en") }}
                    </b-form-checkbox>
                      <b-form-checkbox
                          v-model="filterSetting"
                          :value="$i18n.locale == 'ar'?'sponsorGroup.name':'sponsorGroup.name_e'"
                          class="mb-1"
                      >
                          {{ getCompanyKey("sponsor_group") }}
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

            <div class="row justify-content-between align-items-center mb-2 px-1">
              <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                <b-button
                    v-if="isPermission('create sponsor club')"
                  v-b-modal.create
                  variant="primary"
                  class="btn-sm mx-1 font-weight-bold"
                >
                  {{ $t("general.Create") }}
                  <i class="fas fa-plus"></i>
                </b-button>
                <div class="d-inline-flex">
                  <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button v-print="'#printMe'" class="custom-btn-dowonload">
                    <i class="fe-printer"></i>
                  </button>
                  <button
                    class="custom-btn-dowonload"
                    @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                    v-if="checkAll.length == 1 && isPermission('update sponsor club')"
                  >
                    <i class="mdi mdi-square-edit-outline"></i>
                  </button>
                  <!-- start mult delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length > 1 && isPermission('delete sponsor club')"
                    @click.prevent="deleteModule(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1 && isPermission('delete sponsor club')"
                    @click.prevent="deleteModule(checkAll[0])"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!--  end one delete  -->
                </div>
              </div>
              <div
                class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end"
              >
                <div>
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
                    <b-form-checkbox v-if="isVisible('name')"    v-model="setting.name" class="mb-1"
                      >{{ getCompanyKey("sponsor_name_ar") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-if="isVisible('name_e')"    v-model="setting.name_e" class="mb-1">
                      {{ getCompanyKey("sponsor_name_en") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-if="isVisible('parent_id')"    v-model="setting.parent_id" class="mb-1">
                      {{ getCompanyKey("parent") }}
                    </b-form-checkbox>
                    <div class="d-flex justify-content-end">
                      <a href="javascript:void(0)" class="btn btn-primary btn-sm">{{
                        $t("general.Apply")
                      }}</a>
                    </div>
                  </b-dropdown>
                  <!-- Basic dropdown -->
                  <!-- start Pagination -->
                  <div class="d-inline-flex align-items-center pagination-custom">
                    <div class="d-inline-block" style="font-size: 15px">
                      {{ sponsorsPagination.from }}-{{ sponsorsPagination.to }} /
                      {{ sponsorsPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            sponsorsPagination.current_page == 1 ? 'none' : '',
                        }"
                        @click.prevent="getData(sponsorsPagination.current_page - 1)"
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
                            sponsorsPagination.last_page == sponsorsPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="getData(sponsorsPagination.current_page + 1)"
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
              :title="getCompanyKey('sponsor_create_form')"
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
                    @click.prevent="$bvModal.hide(`create`)"
                    variant="danger"
                    type="button"
                  >
                    {{ $t("general.Cancel") }}
                  </b-button>
                </div>
                <div class="row">
                      <div class="col-12 direction" v-if="isVisible('name')" dir="rtl">
                          <div class="form-group">
                              <label for="field-1" class="control-label">
                                  {{ getCompanyKey("sponsor_name_ar") }}
                                  <span v-if="isRequired('name')"  class="text-danger">*</span>
                              </label>
                              <input
                                  type="text"
                                  class="form-control arabicInput"
                                  v-model="$v.create.name.$model"
                                  :class="{
                              'is-invalid': $v.create.name.$error || errors.name,
                              'is-valid': !$v.create.name.$invalid && !errors.name,
                            }"
                                  @keyup="arabicValue(create.name)"
                                  id="field-1"
                              />
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
                                  <ErrorMessage
                                      v-for="(errorMessage, index) in errors.name"
                                      :key="index"
                                  >{{ $t(errorMessage) }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-12 direction-ltr" v-if="isVisible('name_e')" dir="ltr">
                          <div class="form-group">
                              <label for="field-2" class="control-label">
                                  {{ getCompanyKey("sponsor_name_en") }}
                                  <span v-if="isRequired('name_e')" class="text-danger">*</span>
                              </label>
                              <input
                                  type="text"
                                  class="form-control englishInput"
                                  v-model="$v.create.name_e.$model"
                                  :class="{
                              'is-invalid': $v.create.name_e.$error || errors.name_e,
                              'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                            }"
                                  @keyup="englishValue(create.name_e)"
                                  id="field-2"
                              />
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
                                  >{{ $t(errorMessage) }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-12" v-if="isVisible('group_id')">
                          <div class="form-group position-relative">
                              <label class="control-label">
                                  {{ getCompanyKey("sponsor_group") }}
                                  <span v-if="isRequired('group_id')" class="text-danger">*</span>
                              </label>
                              <multiselect
                                  v-model="create.group_id"
                                  :options="groups.map((type) => type.id)"
                                  :custom-label="
                                  (opt) => groups.find((x) => x.id == opt)?
                                    groups.locale == 'ar'
                                      ? groups.find((x) => x.id == opt)
                                          .name
                                      : groups.find((x) => x.id == opt)
                                          .name_e: null
                                "
                              >
                              </multiselect>
                              <div
                                  v-if="
                                  $v.edit.group_id.$error ||
                                  errors.group_id
                                "
                                  class="text-danger"
                              >
                                  {{ $t("general.fieldIsRequired") }}
                              </div>
                              <template v-if="errors.group_id">
                                  <ErrorMessage
                                      v-for="(
                                    errorMessage, index
                                  ) in errors.group_id"
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
            <div class="table-responsive mb-3 custom-table-theme position-relative">
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->

              <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table" id="printMe">
                <thead>
                  <tr>
                    <th v-if="enabled3" class="do-not-print" scope="col" style="width: 0">
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
                        <span>{{ getCompanyKey("sponsor_name_ar") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="sponsors.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="sponsors.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.name_e && isVisible('name_e')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("sponsor_name_en") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="sponsors.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="sponsors.sort(sortString('-name_e'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.group_id && isVisible('group_id')">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("sponsor_group") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="sponsors.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="sponsors.sort(sortString('-name_e'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="enabled3" class="do-not-print">
                      {{ $t("general.Action") }}
                    </th>
                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                  </tr>
                </thead>
                <tbody v-if="sponsors.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="isPermission('update sponsor club')?
                    $bvModal.show(`modal-edit-${data.id}`):false"
                    v-for="(data, index) in sponsors"
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
                    <td v-if="setting.name && isVisible('name')">
                      <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                    </td>
                    <td v-if="setting.name_e && isVisible('name_e')">
                      <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
                    </td>
                    <td v-if="setting.group_id && isVisible('group_id')">
                      <template v-if="data.group">
                        {{ $i18n.locale == "ar" ? data.group.name : data.group.name_e }}
                      </template>
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
                              v-if="isPermission('update sponsor club')"
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
                            v-if="isPermission('delete sponsor club')"
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
                        :title="getCompanyKey('sponsor_edit_form')"
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
                                <div class="col-12 direction" v-if="isVisible('name')" dir="rtl">
                                    <div class="form-group">
                                        <label for="field-u-1" class="control-label">
                                            {{ getCompanyKey("sponsor_name_ar") }}
                                            <span  v-if="isRequired('name')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control arabicInput"
                                            v-model="$v.edit.name.$model"
                                            :class="{
                                        'is-invalid': $v.edit.name.$error || errors.name,
                                        'is-valid':
                                          !$v.edit.name.$invalid && !errors.name,
                                      }"
                                            :placeholder="$t('general.Name')"
                                            @keyup="arabicValue(edit.name)"
                                            id="field-u-1"
                                        />
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
                                            >{{ $t(errorMessage) }}</ErrorMessage
                                            >
                                        </template>
                                    </div>
                                </div>
                                <div class="col-12 direction-ltr" v-if="isVisible('name_e')" dir="ltr">
                                    <div class="form-group">
                                        <label for="field-u-2" class="control-label">
                                            {{ getCompanyKey("sponsor_name_en") }}
                                            <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control englishInput"
                                            v-model="$v.edit.name_e.$model"
                                            :class="{
                                        'is-invalid':
                                          $v.edit.name_e.$error || errors.name_e,
                                        'is-valid':
                                          !$v.edit.name_e.$invalid && !errors.name_e,
                                      }"
                                            :placeholder="$t('general.Name_en')"
                                            @keyup="englishValue(edit.name_e)"
                                            id="field-u-2"
                                        />
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
                                                v-for="(errorMessage, index) in errors.name_e"
                                                :key="index"
                                            >{{ $t(errorMessage) }}</ErrorMessage
                                            >
                                        </template>
                                    </div>
                                </div>
                                <div class="col-12" v-if="isVisible('group_id')">
                                    <div class="form-group position-relative">
                                        <label class="control-label">
                                            {{ getCompanyKey("sponsor_group") }}
                                            <span v-if="isRequired('group_id')" class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            v-model="edit.group_id"
                                            :options="groups.map((type) => type.id)"
                                            :custom-label="
                                                  (opt) => groups.find((x) => x.id == opt)?
                                                    groups.locale == 'ar'
                                                      ? groups.find((x) => x.id == opt)
                                                          .name
                                                      : groups.find((x) => x.id == opt)
                                                          .name_e: null
                                                "
                                        >
                                        </multiselect>
                                        <div
                                            v-if="
                                  $v.edit.group_id.$error ||
                                  errors.group_id
                                "
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.group_id">
                                            <ErrorMessage
                                                v-for="(
                                    errorMessage, index
                                  ) in errors.group_id"
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
                        <b-button class="btn"   @mouseover="log(data.id)" @mousemove="log(data.id)" :id="'tooltip-button-'+data.id" :variant="$i18n.locale == 'en' ? 'left' : 'right'"
                        > <i class="fe-info" style="font-size: 22px"></i></b-button
                        >
                        <b-tooltip :target="'tooltip-button-'+data.id" :placement="$i18n.locale == 'en' ? 'left' : 'right'"
                        >{{Tooltip}}</b-tooltip>
<!--                      <button-->
<!--                        @mouseover="log(data.id)"-->
<!--                        @mousemove="log(data.id)"-->
<!--                        type="button"-->
<!--                        class="btn"-->
<!--                        data-toggle="tooltip"-->
<!--                        :data-placement="$i18n.locale == 'en' ? 'left' : 'right'"-->
<!--                        :title="Tooltip"-->
<!--                      >-->
<!--                        <i class="fe-info" style="font-size: 22px"></i>-->
<!--                      </button>-->
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
<style scoped lang="scss">
ul,
#myUL {
  list-style-type: none;
  .delete-node {
    i {
      font-size: 18px;
      margin: 0 5px;
    }
  }
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
.rtl {
  #myUL {
    .without-children {
      padding-right: 10px;
    }
    .nested {
      padding-right: 40px;
    }
  }
}
.ltr {
  #myUL {
    .without-children {
      padding-left: 10px;
    }
  }
}

@media print {
    .do-not-print{
        display: none;
    }
    .arrow-sort{
        display: none;
    }
    .bg-soft-success{
        background-color:unset;
        color: #000000 !important;
        border: unset;
    }
    .bg-soft-danger{
        background-color:unset;
        color: #000000 !important;
        border: unset;
    }
}
.tooltip-inner {
    max-width: 750px !important;
    background-color: #eed900;
    color:black;
}
</style>
