<script>
import Layout from "../../layouts/main";
import permissionGuard from "../../../helper/permission";

import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {
  required,
  minLength,
  maxLength,
  integer,
  numeric,
  decimal,
  minValue,
  between,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import owner from "../../../components/create/realEstate/owner";
import wallet from "../../../components/create/realEstate/wallet";
import loader from "../../../components/general/loader";
import Multiselect from "vue-multiselect";
import { formatDateOnly, formatDateTime } from "../../../helper/startDate";
import translation from "../../../helper/mixin/translation-mixin";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Wallet Owner",
    meta: [{ name: "Wallet Owner", content: "Wallet Owner" }],
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
    owner,
    wallet,
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Wallet Owner", "all Store");
    });
  },
  data() {
    return {
      per_page: 50,
      search: "",
      Tooltip: "",
      mouseEnter: null,
      debounce: {},
      walletOwnersPagination: {},
      walletOwners: [],
      enabled3: true,
      isLoader: false,
      create: {
        wallet_id: null,
        wallet_owners: [
          {
            owner_id: null,
            percentage: 0,
          },
        ],
      },
      edit: {
        wallet_id: null,
        wallet_owners: [],
      },
      errors: {},
      isCheckAll: false,
      checkAll: [],
      wallets: [],
      owners: [],
      current_page: 1,
      is_persentage: true,
      setting: {
        wallet_id: true,
        owner_id: true,
      },
      is_disabled: false,
      filterSetting: [
        this.$i18n.locale == "ar" ? "owner.name" : "owner.name_e",
        this.$i18n.locale == "ar" ? "wallet.name" : "wallet.name_e",
      ],
      printLoading: true,
      printObj: {
        id: "printWalletOwner",
      },
    };
  },
  validations: {
    create: {
      wallet_id: { required },
      wallet_owners: {
        required,
        $each: {
          owner_id: { required },
          percentage: { required, decimal, minValue: minValue(0.01) },
        },
      },
    },
    edit: {
      wallet_id: { required },
      wallet_owners: {
        required,
        $each: {
          owner_id: { required },
          percentage: { required, decimal, minValue: minValue(0.01) },
        },
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
        this.walletOwners.forEach((el) => {
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
    await this.getData();
  },
  methods: {
    addNewField() {
      this.create.wallet_owners.push({
        owner_id: null,
        percentage: 0,
      });
    },
    removeNewField(index) {
      if (this.create.wallet_owners.length > 1) {
        this.create.wallet_owners.splice(index, 1);
        let totel = this.create.wallet_owners.reduce(
          (accumulator, curValue) =>
            parseFloat(accumulator) + parseFloat(curValue.percentage),
          0
        );
        this.is_persentage = totel == 100 ? true : false;
      }
    },
    addNewFieldEdit() {
      this.edit.wallet_owners.push({
        owner_id: null,
        percentage: 0,
      });
    },
    removeNewFieldEdit(index) {
      if (this.edit.wallet_owners.length > 1) {
        this.edit.wallet_owners.splice(index, 1);
        let totel = this.edit.wallet_owners.reduce(
          (accumulator, curValue) =>
            parseFloat(accumulator) + parseFloat(curValue.percentage),
          0
        );
        this.is_persentage = totel == 100 ? true : false;
      }
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
          `/real-estate/wallet-owner?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.walletOwners = l.data;
          this.walletOwnersPagination = l.pagination;
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
        this.current_page <= this.walletOwnersPagination.last_page &&
        this.current_page != this.walletOwnersPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/real-estate/wallet-owner?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.walletOwners = l.data;
            this.walletOwnersPagination = l.pagination;
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
              .post(`/real-estate/wallet-owner/bulk-delete`, { ids: id })
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
              .delete(`/real-estate/wallet-owner/${id}`)
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
        wallet_id: null,
        wallet_owners: [
          {
            owner_id: null,
            percentage: 0,
          },
        ],
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_disabled = false;
      this.errors = {};
      this.is_persentage = true;
      this.$bvModal.hide(`create`);
    },
    /**
     *  hidden Modal (create)
     */

    async resetModal() {
      await this.getWallet();
      await this.getOwner();
      this.create = {
        wallet_id: null,
        wallet_owners: [
          {
            owner_id: null,
            percentage: 0,
          },
        ],
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create module
     */
    resetForm() {
      this.create = {
        wallet_id: null,
        wallet_owners: [
          {
            owner_id: null,
            percentage: 0,
          },
        ],
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_disabled = false;
      this.is_persentage = true;
      this.errors = {};
    },

    AddSubmit() {
      this.$v.create.$touch();

      let totel = this.create.wallet_owners.reduce(
        (accumulator, curValue) =>
          parseFloat(accumulator) + parseFloat(curValue.percentage),
        0
      );
      this.is_persentage = totel == 100;

      if (this.$v.create.$invalid && !this.is_persentage) {
        return;
      } else {
        this.isLoader = true;
        this.create.wallet_owners.map(
          (e) => (e.wallet_id = this.create.wallet_id)
        );
        this.errors = {};
        adminApi
          .post(`/real-estate/wallet-owner`, {
            "wallet-owner": this.create.wallet_owners,
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
     *  edit module
     */
    editSubmit(id) {
      this.$v.edit.$touch();

      let totel = this.edit.wallet_owners.reduce(
        (accumulator, curValue) =>
          parseFloat(accumulator) + parseFloat(curValue.percentage),
        0
      );
      this.is_persentage = totel == 100;
      if (this.$v.edit.$invalid && !this.is_persentage) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.edit.wallet_owners.map((e) => (e.wallet_id = this.edit.wallet_id));
        adminApi
          .put(`/real-estate/wallet-owner/${this.edit.wallet_id}`, {
            "wallet-owner": this.edit.wallet_owners,
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
      await this.getWallet();
      await this.getOwner();
      let module = this.walletOwners.find((e) => id == e.id);
      this.edit.wallet_id = module.wallet.id;
      if (module.wallet.create_owners) {
        module.wallet.create_owners.forEach((e) => {
          adminApi
            .get(
              `/real-estate/owners/ownerWalletPercentage/${this.edit.wallet_id}/${e.id}`
            )
            .then((res) => {
              this.edit.wallet_owners.push({
                owner_id: e.id,
                percentage: res.data.data.percentage,
              });
            })
            .catch((err) => {
              Swal.fire({
                icon: "error",
                title: `${this.$t("general.Error")}`,
                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
              });
            })
            .finally(() => {});
        });
      } else {
        this.edit.wallet_owners.push({
          owner_id: null,
          percentage: 0,
        });
      }
      this.errors = {};
    },
    resetModalHiddenEdit(id) {
      this.edit = {
        wallet_id: null,
        wallet_owners: [],
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_persentage = true;
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
    async getWallet() {
      this.isLoader = true;

      await adminApi
        .get(`/real-estate/wallets`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضف  محفظه", name_e: "Add Wallet" });
          this.wallets = l;
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
    async getOwner() {
      this.isLoader = true;

      await adminApi
        .get(`/real-estate/owners`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضف مالك  ", name_e: "Add Owner" });
          this.owners = l;
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
    showWalletModal() {
      if (this.create.wallet_id == 0) {
        this.$bvModal.show("wallet-create");
        this.create.wallet_id = null;
      }
    },
    showWalleModalEdit() {
      if (this.edit.wallet_id == 0) {
        this.$bvModal.show("wallet-create");
        this.edit.wallet_id = null;
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
          .get(`/real-estate/wallet-owner/logs/${id}`)
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
              ("Wallet-Owner" + "." || "SheetJSTableExport.") + (type || "xlsx")
          );
        }
        this.enabled3 = true;
      }, 100);
    },
    showOwnerModal(index) {
      if (this.create.wallet_owners[index].owner_id == 0) {
        this.$bvModal.show("owner-create");
        this.create.wallet_owners[index].owner_id = null;
      }
    },
    showOwnerEditModal(index) {
      if (this.edit.wallet_owners[index].owner_id == 0) {
        this.$bvModal.show("owner-create");
        this.edit.wallet_owners[index].owner_id = null;
      }
    },
    changeNumber(add) {
      if (add == "add") {
        let totel = this.create.wallet_owners.reduce(
          (accumulator, curValue) =>
            parseFloat(accumulator) + parseFloat(curValue.percentage),
          0
        );
        this.is_persentage = totel == 100 ? true : false;
      } else {
        let totel = this.edit.wallet_owners.reduce(
          (accumulator, curValue) =>
            parseFloat(accumulator) + parseFloat(curValue.percentage),
          0
        );
        this.is_persentage = totel == 100 ? true : false;
      }
    },
    allOnwers(items) {
      let data = "";
      if (items) {
        items.forEach(
          (e) => (data += `${this.$i18n.locale == "ar" ? e.name : e.name_e} - `)
        );
      }
      return data;
    },
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <owner
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getOwner"
    />
    <wallet
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getWallet"
    />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("general.walletOwnerTable") }}</h4>
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
                      :value="
                        $i18n.locale == 'ar' ? 'owner.name' : 'owner.name_e'
                      "
                      class="mb-1"
                    >
                      {{ getCompanyKey("owner") }}
                    </b-form-checkbox>
                    <b-form-checkbox
                      v-model="filterSetting"
                      :value="
                        $i18n.locale == 'ar' ? 'wallet.name' : 'wallet.name_e'
                      "
                      class="mb-1"
                    >
                      {{ getCompanyKey("wallet") }}
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
                  <button
                    v-print="'#printWalletOwner'"
                    class="custom-btn-dowonload"
                  >
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
                      :html="`${$t(
                        'general.setting'
                      )} <i class='fe-settings'></i>`"
                      ref="dropdown"
                      class="dropdown-custom-ali"
                    >
                      <b-form-checkbox v-model="setting.owner_id" class="mb-1">
                        {{ getCompanyKey("owner") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.wallet_id" class="mb-1">
                        {{ getCompanyKey("wallet") }}
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
                      {{ walletOwnersPagination.from }}-{{
                        walletOwnersPagination.to
                      }}
                      /
                      {{ walletOwnersPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            walletOwnersPagination.current_page == 1
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(walletOwnersPagination.current_page - 1)
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
                            walletOwnersPagination.last_page ==
                            walletOwnersPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(walletOwnersPagination.current_page + 1)
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
              :title="getCompanyKey('wallet_owner_create_form')"
              title-class="font-18"
              body-class="p-4 "
              size="lg"
              :hide-footer="true"
              @show="resetModal"
              @hidden="resetModalHidden"
            >
              <form>
                <div class="d-flex justify-content-end">
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
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="my-1 mr-2">{{
                        getCompanyKey("wallet")
                      }}</label>
                      <multiselect
                        @input="showWalletModal"
                        v-model="$v.create.wallet_id.$model"
                        :options="wallets.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            $i18n.locale == 'ar'
                              ? wallets.find((x) => x.id == opt).name
                              : wallets.find((x) => x.id == opt).name_e
                        "
                      >
                      </multiselect>
                      <div
                        v-if="$v.create.wallet_id.$error"
                        class="text-danger"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.wallet_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.wallet_id"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                </div>
                <template v-for="(item, index) in create.wallet_owners">
                  <div class="row" :key="index">
                    <div class="col-md-5">
                      <div class="form-group">
                        <label>{{ getCompanyKey("owner") }}</label>
                        <multiselect
                          @input="showOwnerModal(index)"
                          v-model="
                            $v.create.wallet_owners.$each[index].owner_id.$model
                          "
                          :options="owners.map((type) => type.id)"
                          :custom-label="
                            (opt) =>
                              $i18n.locale == 'ar'
                                ? owners.find((x) => x.id == opt).name
                                : owners.find((x) => x.id == opt).name_e
                          "
                        >
                        </multiselect>
                        <div
                          v-if="
                            $v.create.wallet_owners.$each[index].owner_id.$error
                          "
                          class="text-danger"
                        >
                          {{ $t("general.fieldIsRequired") }}
                        </div>
                        <template v-if="errors.owner_id">
                          <ErrorMessage
                            v-for="(errorMessage, index) in errors.owner_id"
                            :key="index"
                            >{{ errorMessage }}
                          </ErrorMessage>
                        </template>
                      </div>
                    </div>
                    <div class="col-md-5">
                      <div class="form-group">
                        <label class="control-label">
                          {{ getCompanyKey("wallet_owner_percentage") }}
                          <span class="text-danger">*</span>
                        </label>
                        <input
                          type="number"
                          step="0.01"
                          class="form-control"
                          data-create="3"
                          @input="changeNumber('add')"
                          v-model="
                            $v.create.wallet_owners.$each[index].percentage
                              .$model
                          "
                          :class="{
                            'is-invalid':
                              $v.create.wallet_owners.$each[index].percentage
                                .$error ||
                              errors.percentage ||
                              !is_persentage,
                            'is-valid':
                              !$v.create.wallet_owners.$each[index].percentage
                                .$invalid &&
                              !errors.percentage &&
                              is_persentage,
                          }"
                        />
                        <template v-if="errors.percentage">
                          <ErrorMessage
                            v-for="(errorMessage, index) in errors.percentage"
                            :key="index"
                            >{{ errorMessage }}
                          </ErrorMessage>
                        </template>
                      </div>
                    </div>
                    <div class="col-md-2 p-0 pt-3">
                      <button
                        v-if="create.wallet_owners.length - 1 == index"
                        type="button"
                        @click.prevent="addNewField"
                        class="custom-btn-dowonload"
                      >
                        <i class="fas fa-plus"></i>
                      </button>
                      <button
                        v-if="create.wallet_owners.length > 1"
                        type="button"
                        @click.prevent="removeNewField(index)"
                        class="custom-btn-dowonload"
                      >
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </div>
                  </div>
                </template>
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
                id="printWalletOwner"
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
                    <th v-if="setting.wallet_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("wallet") }}</span>
                      </div>
                    </th>
                    <th v-if="setting.owner_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("owner") }}</span>
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
                <tbody v-if="walletOwners.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in walletOwners"
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
                    <td v-if="setting.wallet_id">
                      <h5 class="m-0 font-weight-normal">
                        {{
                          $i18n.locale == "ar"
                            ? data.wallet.name
                            : data.wallet.name_e
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.owner_id">
                      {{ allOnwers(data.wallet.create_owners) }}
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
                        :title="getCompanyKey('wallet_owner_edit_form')"
                        title-class="font-18"
                        body-class="p-4"
                        size="lg"
                        :ref="`edit-${data.id}`"
                        :hide-footer="true"
                        @show="resetModalEdit(data.id)"
                        @hidden="resetModalHiddenEdit(data.id)"
                      >
                        <form>
                          <div class="d-flex justify-content-end">
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
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="my-1 mr-2">{{
                                  getCompanyKey("wallet")
                                }}</label>
                                <multiselect
                                  :disabled="true"
                                  @input="showWalleModalEdit"
                                  v-model="$v.edit.wallet_id.$model"
                                  :options="wallets.map((type) => type.id)"
                                  :custom-label="
                                    (opt) =>
                                      $i18n.locale == 'ar'
                                        ? wallets.find((x) => x.id == opt).name
                                        : wallets.find((x) => x.id == opt)
                                            .name_e
                                  "
                                >
                                </multiselect>
                                <div
                                  v-if="$v.create.wallet_id.$error"
                                  class="text-danger"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.wallet_id">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.wallet_id"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                          </div>
                          <template v-for="(item, index) in edit.wallet_owners">
                            <div class="row" :key="index">
                              <div class="col-md-5">
                                <div class="form-group">
                                  <label>{{ getCompanyKey("owner") }}</label>
                                  <multiselect
                                    @input="showOwnerEditModal(index)"
                                    v-model="
                                      $v.edit.wallet_owners.$each[index]
                                        .owner_id.$model
                                    "
                                    :options="owners.map((type) => type.id)"
                                    :custom-label="
                                      (opt) =>
                                        $i18n.locale == 'ar'
                                          ? owners.find((x) => x.id == opt).name
                                          : owners.find((x) => x.id == opt)
                                              .name_e
                                    "
                                  >
                                  </multiselect>
                                  <div
                                    v-if="
                                      $v.edit.wallet_owners.$each[index]
                                        .owner_id.$error
                                    "
                                    class="text-danger"
                                  >
                                    {{ $t("general.fieldIsRequired") }}
                                  </div>
                                  <template v-if="errors.owner_id">
                                    <ErrorMessage
                                      v-for="(
                                        errorMessage, index
                                      ) in errors.owner_id"
                                      :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                    >
                                  </template>
                                </div>
                              </div>
                              <div class="col-md-5">
                                <div class="form-group">
                                  <label class="control-label">
                                    {{
                                      getCompanyKey("wallet_owner_percentage")
                                    }}
                                    <span class="text-danger">*</span>
                                  </label>
                                  <input
                                    type="number"
                                    step="0.01"
                                    class="form-control"
                                    @input="changeNumber('edit')"
                                    v-model="
                                      $v.edit.wallet_owners.$each[index]
                                        .percentage.$model
                                    "
                                    :class="{
                                      'is-invalid':
                                        $v.edit.wallet_owners.$each[index]
                                          .percentage.$error ||
                                        errors.percentage ||
                                        !is_persentage,
                                      'is-valid':
                                        !$v.edit.wallet_owners.$each[index]
                                          .percentage.$invalid &&
                                        !errors.percentage &&
                                        is_persentage,
                                    }"
                                  />
                                  <template v-if="errors.percentage">
                                    <ErrorMessage
                                      v-for="(
                                        errorMessage, index
                                      ) in errors.percentage"
                                      :key="index"
                                      >{{ errorMessage }}
                                    </ErrorMessage>
                                  </template>
                                </div>
                              </div>
                              <div class="col-md-2 p-0 pt-3">
                                <button
                                  v-if="edit.wallet_owners.length - 1 == index"
                                  type="button"
                                  @click.prevent="addNewFieldEdit"
                                  class="custom-btn-dowonload"
                                >
                                  <i class="fas fa-plus"></i>
                                </button>
                                <button
                                  v-if="edit.wallet_owners.length > 1"
                                  type="button"
                                  @click.prevent="removeNewFieldEdit(index)"
                                  class="custom-btn-dowonload"
                                >
                                  <i class="fas fa-trash-alt"></i>
                                </button>
                              </div>
                            </div>
                          </template>
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
