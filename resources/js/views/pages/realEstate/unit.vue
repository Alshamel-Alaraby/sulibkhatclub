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
  numeric,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import {
  dynamicSortNumber,
  dynamicSortString,
} from "../../../helper/tableSort";
import { formatDateOnly, formatDateTime } from "../../../helper/startDate";
import Multiselect from "vue-multiselect";
import DatePicker from "vue2-datepicker";
import translation from "../../../helper/translation-mixin";
import Building from "../../../components/create/building";
import unitStatus from "../../../components/create/realEstate/unitStatus";
import propertyTree from "../../../components/create/property-tree";
import { arabicValue, englishValue } from "../../../helper/langTransform";

/**
 * Advanced Table component
 */

export default {
  page: {
    title: "units",
    meta: [{ name: "description", content: "units" }],
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      if (vm.$store.state.auth.work_flow_trees.includes("real estate-e")) {
        Swal.fire({
          icon: "error",
          title: `${vm.$t("general.Error")}`,
          text: `${vm.$t("general.ModuleExpired")}`,
        });
        return vm.$router.push({ name: "home" });
      }

      if (
        vm.$store.state.auth.work_flow_trees.includes("realEstate units") ||
        vm.$store.state.auth.work_flow_trees.includes("real estate") ||
        vm.$store.state.auth.user.type == "super_admin"
      ) {
        return true;
      } else {
        return vm.$router.push({ name: "home" });
      }
    });
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    DatePicker,
    Multiselect,
    Building,
    unitStatus,
    propertyTree,
  },
  data() {
    return {
      per_page: 50,
      search: "",
      debounce: {},
      images: [],
      media: {},
      unit_id: null,
      saveImageName: [],
      showPhoto: "./images/img-1.png",
      mime_type: "",
      unitsPagination: {},
      units: [],
      buildings: [],
      unit_status: [],
      properties: [],
      modules: ["sell", "buying"],
      isLoader: false,
      createVideo: "",
      frameUrl: "",
      prevUnitStatusId: null,
      create: {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        code: "",
        unit_ty: 0,
        unit_area: 0,
        unit_net_area: 0,
        properties: [],
        module: "sell",
        building_id: null,
        unit_status_id: null,
        rooms: 0,
        path: 0,
        view: 0,
        floor: 0,
        finishing: 0,
      },
      edit: {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        properties: [],
        code: "",
        unit_ty: 0,
        unit_area: 0,
        unit_net_area: 0,
        module: "sell",
        building_id: null,
        unit_status_id: null,
        rooms: 0,
        path: 0,
        view: 0,
        floor: 0,
        finishing: 0,
      },
      errors: {},
      isCheckAll: false,
      enabled3: true,
      checkAll: [],
      current_page: 1,
      image: "",
      setting: {
        name: true,
        name_e: true,
        description: true,
        description_e: true,
        code: true,
        unit_ty: true,
        unit_area: true,
        module_id: true,
        building_id: true,
        unit_status_id: true,
        rooms: true,
        path: true,
        view: true,
        floor: true,
        unit_net_area: true,
        finishing: true,
      },
      is_disabled: false,
      filterSetting: [
        "name",
        "name_e",
        "description",
        "description_e",
        "code",
        "unit_ty",
        "unit_area",
        this.$i18n.locale == "ar" ? "building.name" : "building.name_e",
        this.$i18n.locale == "ar" ? "unitStatus.name" : "unitStatus.name_e",
        "rooms",
        "path",
        "view",
        "floor",
        "unit_net_area",
        "finishing",
      ],
      idDelete: null,
      Tooltip: "",
      mouseEnter: null,
      printLoading: true,
      printObj: {
        id: "printUnit",
      },
    };
  },
  validations: {
    createVideo: { required },
    create: {
      name: { required, minLength: minLength(2), maxLength: maxLength(100) },
      name_e: { required, minLength: minLength(2), maxLength: maxLength(100) },
      description: { maxLength: maxLength(1000) },
      description_e: { maxLength: maxLength(1000) },
      code: { required, maxLength: maxLength(20) },
      unit_ty: { integer },
      unit_area: { numeric },
      module: { required },
      properties: { required },
      building_id: { required },
      unit_status_id: { required },
      rooms: { integer },
      path: { integer },
      view: { integer },
      floor: { integer },
      finishing: { integer },
      unit_net_area: { numeric },
    },
    edit: {
      name: { required, minLength: minLength(2), maxLength: maxLength(100) },
      name_e: { required, minLength: minLength(2), maxLength: maxLength(100) },
      description: { maxLength: maxLength(1000) },
      description_e: { maxLength: maxLength(1000) },
      code: { required, maxLength: maxLength(20) },
      unit_ty: { integer },
      properties: { required },
      unit_area: { numeric },
      module: { required },
      building_id: { required },
      unit_status_id: { required },
      rooms: { integer },
      path: { integer },
      view: { integer },
      floor: { integer },
      finishing: { integer },
      unit_net_area: { numeric },
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
        this.units.forEach((el) => {
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
    this.getData();
  },
  // updated(){
  //     $(function(){
  //         $(".englishInput").keypress(function(event){
  //             var ew = event.which;
  //             if(ew == 32)
  //                 return true;
  //             if(48 <= ew && ew <= 57)
  //                 return true;
  //             if(65 <= ew && ew <= 90)
  //                 return true;
  //             if(97 <= ew && ew <= 122)
  //                 return true;
  //             return false;
  //         });
  //         $(".arabicInput").keypress(function(event){
  //             var ew = event.which;
  //             if(ew == 32)
  //                 return true;
  //             if(48 <= ew && ew <= 57)
  //                 return true;
  //             if(65 <= ew && ew <= 90)
  //                 return false;
  //             if(97 <= ew && ew <= 122)
  //                 return false;
  //             return true;
  //         });
  //     });
  // },
  methods: {
    AddVideo(action) {
      let data = action == "create" ? this.create : this.edit;
      this.$v.createVideo.$touch();
      if (this.$v.createVideo.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        adminApi
          .put(`/real-estate/units/${this.unit_id}`, {
            ...data,
            video_link: this.createVideo,
          })
          .then((res) => {
            this.getData();
            this.frameUrl = res.data.data.video_link;
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

    arabicValue(txt) {
      this.create.name = arabicValue(txt);
      this.edit.name = arabicValue(txt);
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
      this.edit.name_e = englishValue(txt);
    },
    arabicValueDescription(txt) {
      this.create.description = arabicValue(txt);
      this.edit.description = arabicValue(txt);
    },
    englishValueDescription(txt) {
      this.create.description_e = englishValue(txt);
      this.edit.description_e = englishValue(txt);
    },
    /**
     *  start get Data units && pagination
     */
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      for (let i = 0; i < this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/real-estate/units?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.units = l.data;
          this.unitsPagination = l.pagination;
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
        this.current_page <= this.unitsPagination.last_page &&
        this.current_page != this.unitsPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/real-estate/units?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.units = l.data;
            this.unitsPagination = l.pagination;
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
              .post(`/real-estate/units/bulk-delete`, { ids: id })
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
              .delete(`/real-estate/units/${id}`)
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
      this.createVideo = "";
      this.frameUrl = "";

      this.create = {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        code: "",
        properties: [],
        unit_ty: 0,
        unit_area: 0,
        unit_net_area: 0,
        module: "sell",
        building_id: null,
        unit_status_id: null,
        rooms: 0,
        path: 0,
        view: 0,
        floor: 0,
        finishing: 0,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.$bvModal.hide(`create`);
      this.images = [];
      this.mime_type = "";
      this.unit_id = null;
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      this.frameUrl = "";

      await this.getBuildings();
      await this.getProperty();
      await this.getUnitStatus();

      this.createVideo = "";
      this.create = {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        code: "",
        properties: [],
        unit_ty: 0,
        unit_area: 0,
        unit_net_area: 0,
        module: "sell",
        building_id: null,
        unit_status_id: null,
        rooms: 0,
        path: 0,
        view: 0,
        floor: 0,
        finishing: 0,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.showPhoto = "./images/img-1.png";
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.unit_id = null;
      this.media = {};
      this.mime_type = "";
      this.images = [];
      this.errors = {};
      this.create.unit_status_id = 1;
    },
    /**
     *  create countrie
     */
    resetForm() {
      this.createVideo = "";
      this.frameUrl = "";

      this.create = {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        code: "",
        unit_ty: 0,
        unit_area: 0,
        properties: [],
        unit_net_area: 0,
        module: "sell",
        building_id: null,
        unit_status_id: null,
        rooms: 0,
        path: 0,
        view: 0,
        floor: 0,
        finishing: 0,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.unit_id = null;
      this.is_disabled = false;
      this.showPhoto = "./images/img-1.png";
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.media = {};
      this.images = [];
      this.mime_type = "";
      this.create.unit_status_id = 1;
    },
    AddSubmit() {
      if (!this.create.name) {
        this.create.name = this.create.name_e;
      }
      if (!this.create.name_e) {
        this.create.name_e = this.create.name;
      }
      if (!this.create.description) {
        this.create.description = this.create.description_e;
      }
      if (!this.create.description_e) {
        this.create.description_e = this.create.description;
      }

      this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};

        adminApi
          .post(`/real-estate/units`, this.create)
          .then((res) => {
            this.is_disabled = true;
            this.unit_id = res.data.data.id;
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
      if (!this.edit.description) {
        this.edit.description = this.edit.description_e;
      }
      if (!this.edit.description_e) {
        this.edit.description_e = this.edit.description;
      }
      this.$v.edit.$touch();

      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};

        adminApi
          .put(`/real-estate/units/${id}`, this.edit)
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
      this.createVideo = "";
      await this.getBuildings();
      await this.getUnitStatus();
      await this.getProperty();
      let unit = this.units.find((e) => id == e.id);
      this.frameUrl = unit.video_link;
      this.unit_id = id;
      this.edit.name = unit.name;
      this.edit.name_e = unit.name_e;
      this.edit.properties = unit.properties;
      this.edit.description = unit.description;
      this.edit.description_e = unit.description_e;
      this.edit.building_id = unit.building_id;
      this.edit.unit_status_id = unit.unit_status_id;
      this.prevUnitStatusId = unit.unit_status_id;
      this.edit.module = unit.name;
      this.edit.code = unit.code;
      this.edit.unit_ty = unit.unit_ty;
      this.edit.finishing = unit.finishing;
      this.edit.unit_area = unit.unit_area;
      this.edit.floor = unit.floor;
      this.edit.path = unit.path;
      this.edit.unit_net_area = unit.unit_net_area;
      this.edit.rooms = unit.rooms;
      this.edit.view = unit.view;
      this.images = unit.media ?? [];
      if (this.images && this.images.length > 0) {
        this.showPhoto = this.images[this.images.length - 1].url;
        this.mime_type = this.images[this.images.length - 1].mime_type;
      } else {
        this.showPhoto = "./images/img-1.png";
      }
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.createVideo = "";
      this.frameUrl = "";
      this.errors = {};
      this.edit = {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        properties: [],
        code: "",
        unit_ty: 0,
        unit_area: 0,
        unit_net_area: 0,
        module: "sell",
        building_id: null,
        unit_status_id: null,
        rooms: 0,
        path: 0,
        view: 0,
        floor: 0,
        finishing: 0,
      };
      this.unit_id = null;
      this.images = [];
      this.mime_type = "";
    },
    /**
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
    formatDate(value) {
      return formatDateOnly(value);
    },
    log(id) {
      if (this.mouseEnter != id) {
        this.Tooltip = "";
        this.mouseEnter = id;
        adminApi
          .get(`/real-estate/units/logs/${id}`)
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
    async getBuildings() {
      this.isLoader = true;
      await adminApi
        .get(`/real-estate/buildings`)
        .then((res) => {
          this.isLoader = false;
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضافة مبنى", name_e: "Add building" });
          this.buildings = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    async getUnitStatus() {
      this.isLoader = true;

      await adminApi
        .get(`real-estate/unit-statuses?is_active=active`)
        .then((res) => {
          let l = res.data.data;
          // l.unshift({
          //   id: 0,
          //   name: "اضف حاله الوحده  ",
          //   name_e: "Add Unit Status",
          // });
          this.unit_status = l;
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
    async getProperty() {
      this.isLoader = true;

      await adminApi
        .get(`/tree-properties`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضف خصائص  ", name_e: "Add Property" });
          this.properties = l;
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
    getCurrentYear() {
      return new Date().getFullYear();
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
            fn || ("Unit" + "." || "SheetJSTableExport.") + (type || "xlsx")
          );
        }
        this.enabled3 = true;
      }, 100);
    },
    showBuildingModal() {
      if (this.create.building_id == 0) {
        this.$bvModal.show("building-create");
        this.create.building_id = null;
      }
    },
    showBuildingModalEdit() {
      if (this.edit.building_id == 0) {
        this.$bvModal.show("building-create");
        this.edit.building_id = null;
      }
    },
    showUnitStatusModal() {
      if (this.create.unit_status_id == 0) {
        this.$bvModal.show("unit-satatus-create");
        this.create.unit_status_id = null;
      }
    },
    showUnitStatusEditModal() {
      if (this.prevUnitStatusId == 2 || this.prevUnitStatusId == 3) {
        this.edit.unit_status_id = this.prevUnitStatusId;
        adminApi.get(`/real-estate/contracts/${this.unit_id}/${this.prevUnitStatusId}`).then((res) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.ReservedOrSold", { serial_number: res.data.serial_number })}`,
          });
        });
        return;
      }
      if (this.edit.unit_status_id == 0) {
        this.$bvModal.show("unit-satatus-create");
        this.edit.unit_status_id = null;
      }
    },
    showPropertyModal() {
      if (this.create.properties.includes(0)) {
        this.$bvModal.show("property-create");
        this.create.properties.pop();
      }
    },
    showPropertyEditModal() {
      if (this.edit.properties.includes(0)) {
        this.$bvModal.show("property-create");
        this.edit.properties.pop();
      }
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
                .put(`/real-estate/units/${this.unit_id}`, {
                  old_media,
                  media: new_media,
                })
                .then((res) => {
                  this.images = res.data.data.media ?? [];
                  if (this.images && this.images.length > 0) {
                    this.showPhoto = this.images[this.images.length - 1].url;
                    this.mime_type =
                      this.images[this.images.length - 1].mime_type;
                  } else {
                    this.showPhoto = "./images/img-1.png";
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
                    .put(`/real-estate/units/${this.unit_id}`, {
                      old_media,
                      media: new_media,
                    })
                    .then((res) => {
                      this.images = res.data.data.media ?? [];
                      if (this.images && this.images.length > 0) {
                        this.showPhoto =
                          this.images[this.images.length - 1].url;
                        this.mime_type =
                          this.images[this.images.length - 1].mime_type;
                      } else {
                        this.showPhoto = "./images/img-1.png";
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
        .put(`/real-estate/units/${this.unit_id}`, { old_media })
        .then((res) => {
          this.units[index] = res.data.data;
          this.images = res.data.data.media ?? [];
          if (this.images && this.images.length > 0) {
            this.showPhoto = this.images[this.images.length - 1].url;
            this.mime_type = this.images[this.images.length - 1].mime_type;
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
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <Building
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getBuildings"
    />
    <unitStatus
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getUnitStatus"
    />
    <propertyTree
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getProperty"
    />

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">
                {{ $t("general.realEstateUnitTable") }}
              </h4>
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
                      v-model="filterSetting"
                      value="name"
                      class="mb-1"
                      >{{
                        getCompanyKey("realEstate_unit_name_ar")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="name_e"
                      class="mb-1"
                      >{{
                        getCompanyKey("realEstate_unit_name_en")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="description"
                      class="mb-1"
                      >{{
                        getCompanyKey("realEstate_unit_description_ar")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="description_e"
                      class="mb-1"
                      >{{
                        getCompanyKey("realEstate_unit_description_en")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="code"
                      class="mb-1"
                      >{{
                        getCompanyKey("realEstate_unit_code")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="unit_ty"
                      class="mb-1"
                      >{{
                        getCompanyKey("realEstate_unit_unit_ty")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="unit_area"
                      class="mb-1"
                      >{{
                        getCompanyKey("realEstate_unit_unit_area")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      :value="
                        $i18n.locale == 'ar'
                          ? 'building.name'
                          : 'building.name_e'
                      "
                      class="mb-1"
                      >{{
                        getCompanyKey("realEstate_unit_building")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      :value="
                        $i18n.locale == 'ar'
                          ? 'unitStatus.name'
                          : 'unitStatus.name_e'
                      "
                      class="mb-1"
                      >{{
                        getCompanyKey("realEstate_unit_unit_status")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="rooms"
                      class="mb-1"
                      >{{
                        getCompanyKey("realEstate_unit_rooms")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="path"
                      class="mb-1"
                      >{{
                        getCompanyKey("realEstate_unit_path")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="view"
                      class="mb-1"
                      >{{
                        getCompanyKey("realEstate_unit_view")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="floor"
                      class="mb-1"
                      >{{
                        getCompanyKey("realEstate_unit_floor")
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
                  <button v-print="'#printUnit'" class="custom-btn-dowonload">
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
                    @click.prevent="deleteCountry(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1"
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
                      <b-form-checkbox v-model="setting.name" class="mb-1">{{
                        getCompanyKey("realEstate_unit_name_ar")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.name_e" class="mb-1">{{
                        getCompanyKey("realEstate_unit_name_en")
                      }}</b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.description"
                        class="mb-1"
                        >{{
                          getCompanyKey("realEstate_unit_description_ar")
                        }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-model="setting.description_e"
                        class="mb-1"
                        >{{
                          getCompanyKey("realEstate_unit_description_en")
                        }}</b-form-checkbox
                      >
                      <b-form-checkbox v-model="setting.code" class="mb-1">{{
                        getCompanyKey("realEstate_unit_code")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.unit_ty" class="mb-1">{{
                        getCompanyKey("realEstate_unit_unit_ty")
                      }}</b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.unit_area"
                        class="mb-1"
                        >{{
                          getCompanyKey("realEstate_unit_unit_area")
                        }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-model="setting.building_id"
                        class="mb-1"
                        >{{
                          getCompanyKey("realEstate_unit_building")
                        }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-model="setting.unit_status_id"
                        class="mb-1"
                        >{{
                          getCompanyKey("realEstate_unit_status")
                        }}</b-form-checkbox
                      >
                      <b-form-checkbox v-model="setting.rooms" class="mb-1">{{
                        getCompanyKey("realEstate_unit_rooms")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.path" class="mb-1">{{
                        getCompanyKey("realEstate_unit_path")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.view" class="mb-1">{{
                        getCompanyKey("realEstate_unit_view")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.floor" class="mb-1">{{
                        getCompanyKey("realEstate_unit_floor")
                      }}</b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.module_id"
                        class="mb-1"
                        >{{
                          getCompanyKey("realEstate_unit_module")
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
                      {{ unitsPagination.from }}-{{ unitsPagination.to }} /
                      {{ unitsPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            unitsPagination.current_page == 1 ? 'none' : '',
                        }"
                        @click.prevent="
                          getData(unitsPagination.current_page - 1)
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
                            unitsPagination.last_page ==
                            unitsPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(unitsPagination.current_page + 1)
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
              :title="getCompanyKey('realEstate_unit_create_form')"
              title-class="font-18"
              dialog-class="modal-full-width"
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
                <b-tabs nav-class="nav-tabs nav-bordered">
                  <b-tab :title="$t('general.DataEntry')" active>
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group position-relative">
                          <label class="control-label">
                            {{ getCompanyKey("realEstate_unit_building") }}
                            <span class="text-danger">*</span>
                          </label>
                          <multiselect
                            @input="showBuildingModal"
                            v-model="$v.create.building_id.$model"
                            :options="buildings.map((type) => type.id)"
                            :custom-label="
                              (opt) =>
                                $i18n.locale == 'ar'
                                  ? buildings.find((x) => x.id == opt).name
                                  : buildings.find((x) => x.id == opt).name_e
                            "
                          >
                          </multiselect>
                          <div
                            v-if="
                              $v.create.building_id.$error || errors.building_id
                            "
                            class="text-danger"
                          >
                            {{ $t("general.fieldIsRequired") }}
                          </div>
                          <template v-if="errors.building_id">
                            <ErrorMessage
                              v-for="(
                                errorMessage, index
                              ) in errors.building_id"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="field-4353" class="control-label">
                            {{ getCompanyKey("realEstate_unit_code") }}
                            <span class="text-danger">*</span>
                          </label>
                          <div dir="ltr">
                            <input
                              type="text"
                              class="form-control"
                              v-model="$v.create.code.$model"
                              :class="{
                                'is-invalid':
                                  $v.create.code.$error || errors.code,
                                'is-valid':
                                  !$v.create.code.$invalid && !errors.code,
                              }"
                              id="field-4353"
                            />
                          </div>
                          <div
                            v-if="!$v.create.code.maxLength"
                            class="invalid-feedback"
                          >
                            {{ $t("general.Itmustbeatmost") }}
                            {{ $v.create.code.$params.maxLength.max }}
                            {{ $t("general.letters") }}
                          </div>
                          <template v-if="errors.code">
                            <ErrorMessage
                              v-for="(errorMessage, index) in errors.code"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                    </div>
                    <hr
                      style="
                        margin: 10px 0 !important;
                        border-top: 1px solid rgb(141 163 159 / 42%);
                      "
                    />
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">
                            {{ getCompanyKey("realEstate_unit_unit_ty") }}
                            <span class="text-danger">*</span>
                          </label>
                          <input
                            type="number"
                            class="form-control"
                            v-model="$v.create.unit_ty.$model"
                            :class="{
                              'is-invalid':
                                $v.create.unit_ty.$error || errors.unit_ty,
                              'is-valid':
                                !$v.create.unit_ty.$invalid && !errors.unit_ty,
                            }"
                          />
                          <template v-if="errors.unit_ty">
                            <ErrorMessage
                              v-for="(errorMessage, index) in errors.unit_ty"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="field-1" class="control-label">
                            {{ getCompanyKey("realEstate_unit_name_ar") }}
                            <span class="text-danger">*</span>
                          </label>
                          <div dir="rtl">
                            <input
                              @keyup="arabicValue(create.name)"
                              type="text"
                              class="form-control"
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
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="field-2" class="control-label">
                            {{ getCompanyKey("realEstate_unit_name_en") }}
                            <span class="text-danger">*</span>
                          </label>
                          <div dir="ltr">
                            <input
                              @keyup="englishValue(create.name_e)"
                              type="text"
                              class="form-control"
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
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">
                            {{ getCompanyKey("realEstate_unit_floor") }}
                            <span class="text-danger">*</span>
                          </label>
                          <input
                            type="number"
                            class="form-control"
                            v-model="$v.create.floor.$model"
                            :class="{
                              'is-invalid':
                                $v.create.floor.$error || errors.floor,
                              'is-valid':
                                !$v.create.floor.$invalid && !errors.floor,
                            }"
                          />
                          <template v-if="errors.floor">
                            <ErrorMessage
                              v-for="(errorMessage, index) in errors.floor"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                    </div>
                    <hr
                      style="
                        margin: 10px 0 !important;
                        border-top: 1px solid rgb(141 163 159 / 42%);
                      "
                    />
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group position-relative">
                          <label class="control-label">
                            {{ getCompanyKey("realEstate_unit_status") }}
                            <span class="text-danger">*</span>
                          </label>
                          <multiselect
                            :disabled="true"
                            @input="showUnitStatusModal"
                            v-model="$v.create.unit_status_id.$model"
                            :options="unit_status.map((type) => type.id)"
                            :custom-label="
                              (opt) =>
                                $i18n.locale == 'ar'
                                  ? unit_status.find((x) => x.id == opt).name
                                  : unit_status.find((x) => x.id == opt).name_e
                            "
                          >
                          </multiselect>
                          <div
                            v-if="
                              $v.create.unit_status_id.$error ||
                              errors.unit_status_id
                            "
                            class="text-danger"
                          >
                            {{ $t("general.fieldIsRequired") }}
                          </div>
                          <template v-if="errors.unit_status_id">
                            <ErrorMessage
                              v-for="(
                                errorMessage, index
                              ) in errors.unit_status_id"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">
                            {{ getCompanyKey("realEstate_unit_unit_area") }}
                            <span class="text-danger">*</span>
                          </label>
                          <input
                            type="number"
                            class="form-control"
                            step="0.1"
                            v-model="$v.create.unit_area.$model"
                            :class="{
                              'is-invalid':
                                $v.create.unit_area.$error || errors.unit_area,
                              'is-valid':
                                !$v.create.unit_area.$invalid &&
                                !errors.unit_area,
                            }"
                          />
                          <template v-if="errors.unit_area">
                            <ErrorMessage
                              v-for="(errorMessage, index) in errors.land_area"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">
                            {{ getCompanyKey("realEstate_unit_unit_net_area") }}
                            <span class="text-danger">*</span>
                          </label>
                          <input
                            type="number"
                            class="form-control"
                            step="0.1"
                            v-model="$v.create.unit_net_area.$model"
                            :class="{
                              'is-invalid':
                                $v.create.unit_net_area.$error ||
                                errors.unit_net_area,
                              'is-valid':
                                !$v.create.unit_net_area.$invalid &&
                                !errors.unit_net_area,
                            }"
                          />
                          <template v-if="errors.unit_net_area">
                            <ErrorMessage
                              v-for="(
                                errorMessage, index
                              ) in errors.unit_net_area"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">
                            {{ getCompanyKey("realEstate_unit_rooms") }}
                            <span class="text-danger">*</span>
                          </label>
                          <input
                            type="number"
                            class="form-control"
                            v-model="$v.create.rooms.$model"
                            :class="{
                              'is-invalid':
                                $v.create.rooms.$error || errors.rooms,
                              'is-valid':
                                !$v.create.rooms.$invalid && !errors.rooms,
                            }"
                          />
                          <template v-if="errors.rooms">
                            <ErrorMessage
                              v-for="(errorMessage, index) in errors.rooms"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">
                            {{ getCompanyKey("realEstate_unit_path") }}
                            <span class="text-danger">*</span>
                          </label>
                          <input
                            type="number"
                            class="form-control"
                            v-model="$v.create.path.$model"
                            :class="{
                              'is-invalid':
                                $v.create.path.$error || errors.path,
                              'is-valid':
                                !$v.create.path.$invalid && !errors.path,
                            }"
                          />
                          <template v-if="errors.path">
                            <ErrorMessage
                              v-for="(errorMessage, index) in errors.path"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                    </div>
                    <hr
                      style="
                        margin: 10px 0 !important;
                        border-top: 1px solid rgb(141 163 159 / 42%);
                      "
                    />
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">
                            {{ getCompanyKey("realEstate_unit_finishing") }}
                            <span class="text-danger">*</span>
                          </label>
                          <input
                            type="number"
                            class="form-control"
                            v-model="$v.create.finishing.$model"
                            :class="{
                              'is-invalid':
                                $v.create.finishing.$error || errors.finishing,
                              'is-valid':
                                !$v.create.finishing.$invalid &&
                                !errors.finishing,
                            }"
                          />
                          <template v-if="errors.finishing">
                            <ErrorMessage
                              v-for="(errorMessage, index) in errors.finishing"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">
                            {{ getCompanyKey("realEstate_unit_view") }}
                            <span class="text-danger">*</span>
                          </label>
                          <input
                            type="number"
                            class="form-control"
                            v-model="$v.create.view.$model"
                            :class="{
                              'is-invalid':
                                $v.create.view.$error || errors.view,
                              'is-valid':
                                !$v.create.view.$invalid && !errors.view,
                            }"
                          />
                          <template v-if="errors.view">
                            <ErrorMessage
                              v-for="(errorMessage, index) in errors.view"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">
                            {{ getCompanyKey("realEstate_unit_properties") }}
                            <span class="text-danger">*</span>
                          </label>
                          <multiselect
                            :multiple="true"
                            @input="showPropertyModal"
                            v-model="$v.create.properties.$model"
                            :options="properties.map((type) => type.id)"
                            :custom-label="
                              (opt) =>
                                $i18n.locale == 'ar'
                                  ? properties.find((x) => x.id == opt).name
                                  : properties.find((x) => x.id == opt).name_e
                            "
                          >
                          </multiselect>
                          <template v-if="errors.properties">
                            <ErrorMessage
                              v-for="(errorMessage, index) in errors.properties"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="mr-2">
                            {{
                              getCompanyKey("realEstate_unit_description_ar")
                            }}
                            <span class="text-danger">*</span>
                          </label>
                          <textarea
                            @input="arabicValueDescription(create.description)"
                            v-model="$v.create.description.$model"
                            class="form-control"
                            :maxlength="1000"
                            rows="5"
                          ></textarea>
                          <template v-if="errors.description">
                            <ErrorMessage
                              v-for="(
                                errorMessage, index
                              ) in errors.description"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="mr-2">
                            {{
                              getCompanyKey("realEstate_unit_description_en")
                            }}
                            <span class="text-danger">*</span>
                          </label>
                          <textarea
                            @input="
                              englishValueDescription(create.description_e)
                            "
                            v-model="$v.create.description_e.$model"
                            class="form-control"
                            :maxlength="1000"
                            rows="5"
                          ></textarea>
                          <template v-if="errors.description_e">
                            <ErrorMessage
                              v-for="(
                                errorMessage, index
                              ) in errors.description_e"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                    </div>
                  </b-tab>
                  <b-tab :disabled="!unit_id" :title="$t('general.video')">
                    <div class="d-flex justify-content-end">
                      <b-button
                        variant="success"
                        type="button"
                        class="mx-1"
                        v-if="!isLoader"
                        @click.prevent="AddVideo('create')"
                      >
                        {{ $t("general.Add") }}
                      </b-button>
                      <b-button variant="success" class="mx-1" disabled v-else>
                        <b-spinner small></b-spinner>
                        <span class="sr-only"
                          >{{ $t("login.Loading") }}...</span
                        >
                      </b-button>
                    </div>
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label">
                            {{ $t("general.video") }}
                            <span class="text-danger">*</span>
                          </label>
                          <input
                            type="text"
                            class="form-control"
                            v-model="$v.createVideo.$model"
                            :class="{
                              'is-invalid': $v.createVideo.$error,
                              'is-valid': !$v.createVideo.$invalid,
                            }"
                          />
                        </div>
                      </div>
                      <div
                        v-html="frameUrl"
                        v-if="frameUrl"
                        class="col-md-12"
                      ></div>
                    </div>
                  </b-tab>
                  <b-tab
                    :disabled="!unit_id"
                    :title="$t('general.ImageUploads')"
                  >
                    <div class="row">
                      <input
                        accept="image/png, image/gif, image/jpeg, image/jpg"
                        type="file"
                        id="uploadImageCreate"
                        @change.prevent="onImageChanged"
                        class="input-file-upload position-absolute"
                        :class="['d-none']"
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
                                    images.length - 1 == index
                                      ? 'bg-primary'
                                      : '',
                                  ]"
                                >
                                  <div class="p-2">
                                    <div class="row align-items-center">
                                      <div
                                        class="col-auto"
                                        @click="
                                          showPhoto = photo.url;
                                          mime_type = photo.mime_type;
                                        "
                                      >
                                        <template
                                          v-if="
                                            !photo.mime_type.includes('video')
                                          "
                                        >
                                          <img
                                            data-dz-thumbnail
                                            :src="photo.url"
                                            class="avatar-sm rounded bg-light"
                                            @error="
                                              src =
                                                '../../../assets/images/video.jpg'
                                            "
                                          />
                                        </template>
                                        <template v-else>
                                          <img
                                            data-dz-thumbnail
                                            src="../../../assets/images/video.jpg"
                                            class="avatar-sm rounded bg-light"
                                            @error="
                                              src =
                                                '../../../assets/images/video.jpg'
                                            "
                                          />
                                        </template>
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
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div
                          class="show-dropzone"
                          v-if="!mime_type.includes('video')"
                        >
                          <img
                            :src="showPhoto"
                            class="img-thumbnail"
                            @error="src = './images/img-1.png'"
                          />
                        </div>
                        <div class="show-dropzone" v-else>
                          <video width="320" height="240" controls autoplay>
                            <source :src="showPhoto" :type="mime_type" />
                            <source :src="showPhoto" type="video/mp4" />
                            <source :src="showPhoto" type="video/ogg" />
                          </video>
                        </div>
                      </div>
                    </div>
                  </b-tab>
                </b-tabs>
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
                id="printUnit"
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
                    <th v-if="setting.name">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("realEstate_unit_name_ar")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="units.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="units.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.name_e">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("realEstate_unit_name_en")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="units.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="units.sort(sortString('-name_e'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.description">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("realEstate_unit_description_ar")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="units.sort(sortString('description'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="units.sort(sortString('-description'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.description_e">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("realEstate_unit_description_en")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="units.sort(sortString('description_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="units.sort(sortString('-description_e'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.code">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("realEstate_unit_code") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="units.sort(sortString('code'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="units.sort(sortString('-code'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.module_id">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("realEstate_unit_module")
                        }}</span>
                      </div>
                    </th>
                    <th v-if="setting.building_id">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("realEstate_unit_building")
                        }}</span>
                      </div>
                    </th>
                    <th v-if="setting.unit_status_id">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("realEstate_unit_status")
                        }}</span>
                      </div>
                    </th>
                    <th v-if="setting.unit_ty">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("realEstate_unit_unit_ty")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="units.sort(SortNumber('unit_ty'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="units.sort(SortNumber('-unit_ty'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.unit_area">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("realEstate_unit_unit_area")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="units.sort(SortNumber('unit_area'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="units.sort(SortNumber('-unit_area'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.rooms">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("realEstate_unit_rooms")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="units.sort(SortNumber('rooms'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="units.sort(SortNumber('-rooms'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.floor">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("realEstate_unit_floor")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="units.sort(SortNumber('floor'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="units.sort(SortNumber('-floor'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.path">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("realEstate_unit_path") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="units.sort(SortNumber('path'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="units.sort(SortNumber('-path'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.view">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("realEstate_unit_view") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="units.sort(SortNumber('view'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="units.sort(SortNumber('-view'))"
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
                <tbody v-if="units.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in units"
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
                    <td v-if="setting.name">
                      <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                    </td>
                    <td v-if="setting.name_e">
                      <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
                    </td>
                    <td v-if="setting.description">{{ data.description }}</td>
                    <td v-if="setting.description_e">
                      {{ data.description_e }}
                    </td>
                    <td v-if="setting.code">{{ data.code }}</td>
                    <td v-if="setting.module_id">{{ data.module }}</td>
                    <td v-if="setting.building_id">
                      {{
                        $i18n.locale == "ar"
                          ? data.building.name
                          : data.building.name_e
                      }}
                    </td>
                    <td v-if="setting.unit_status_id">
                      {{
                        $i18n.locale == "ar"
                          ? data.unit_status.name
                          : data.unit_status.name_e
                      }}
                    </td>
                    <td v-if="setting.unit_ty">{{ data.unit_ty }}</td>
                    <td v-if="setting.unit_area">{{ data.unit_area }}</td>
                    <td v-if="setting.rooms">{{ data.rooms }}</td>
                    <td v-if="setting.floor">{{ data.floor }}</td>
                    <td v-if="setting.path">{{ data.path }}</td>
                    <td v-if="setting.view">{{ data.view }}</td>
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
                        :title="getCompanyKey('realEstate_unit_edit_form')"
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
                          <b-tabs nav-class="nav-tabs nav-bordered">
                            <b-tab :title="$t('general.DataEntry')" active>
                              <div class="row">
                                <div class="col-md-3">
                                  <div class="form-group position-relative">
                                    <label class="control-label">
                                      {{
                                        getCompanyKey(
                                          "realEstate_unit_building"
                                        )
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                      @input="showBuildingModal"
                                      v-model="$v.edit.building_id.$model"
                                      :options="
                                        buildings.map((type) => type.id)
                                      "
                                      :custom-label="
                                        (opt) =>
                                          $i18n.locale == 'ar'
                                            ? buildings.find((x) => x.id == opt)
                                                .name
                                            : buildings.find((x) => x.id == opt)
                                                .name_e
                                      "
                                    >
                                    </multiselect>
                                    <div
                                      v-if="
                                        $v.edit.building_id.$error ||
                                        errors.building_id
                                      "
                                      class="text-danger"
                                    >
                                      {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.building_id">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.building_id"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="form-group">
                                    <label
                                      for="edit-4353"
                                      class="control-label"
                                    >
                                      {{
                                        getCompanyKey("realEstate_unit_code")
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <div dir="ltr">
                                      <input
                                        type="text"
                                        class="form-control"
                                        v-model="$v.edit.code.$model"
                                        :class="{
                                          'is-invalid':
                                            $v.edit.code.$error || errors.code,
                                          'is-valid':
                                            !$v.edit.code.$invalid &&
                                            !errors.code,
                                        }"
                                        id="edit-4353"
                                      />
                                    </div>
                                    <div
                                      v-if="!$v.edit.code.maxLength"
                                      class="invalid-feedback"
                                    >
                                      {{ $t("general.Itmustbeatmost") }}
                                      {{ $v.create.code.$params.maxLength.max }}
                                      {{ $t("general.letters") }}
                                    </div>
                                    <template v-if="errors.code">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.code"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                              </div>
                              <hr
                                style="
                                  margin: 10px 0 !important;
                                  border-top: 1px solid rgb(141 163 159 / 42%);
                                "
                              />
                              <div class="row">
                                <div class="col-md-3">
                                  <div class="form-group">
                                    <label class="control-label">
                                      {{
                                        getCompanyKey("realEstate_unit_unit_ty")
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input
                                      type="number"
                                      class="form-control"
                                      v-model="$v.edit.unit_ty.$model"
                                      :class="{
                                        'is-invalid':
                                          $v.edit.unit_ty.$error ||
                                          errors.unit_ty,
                                        'is-valid':
                                          !$v.edit.unit_ty.$invalid &&
                                          !errors.unit_ty,
                                      }"
                                    />
                                    <template v-if="errors.unit_ty">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.unit_ty"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="form-group">
                                    <label for="edit-1" class="control-label">
                                      {{
                                        getCompanyKey("realEstate_unit_name_ar")
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <div dir="rtl">
                                      <input
                                        @keyup="arabicValue(edit.name)"
                                        type="text"
                                        class="form-control"
                                        v-model="$v.edit.name.$model"
                                        :class="{
                                          'is-invalid':
                                            $v.edit.name.$error || errors.name,
                                          'is-valid':
                                            !$v.edit.name.$invalid &&
                                            !errors.name,
                                        }"
                                        id="edit-1"
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
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="form-group">
                                    <label for="edit-2" class="control-label">
                                      {{
                                        getCompanyKey("realEstate_unit_name_en")
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <div dir="ltr">
                                      <input
                                        @keyup="englishValue(edit.name_e)"
                                        type="text"
                                        class="form-control"
                                        v-model="$v.edit.name_e.$model"
                                        :class="{
                                          'is-invalid':
                                            $v.edit.name_e.$error ||
                                            errors.name_e,
                                          'is-valid':
                                            !$v.edit.name_e.$invalid &&
                                            !errors.name_e,
                                        }"
                                        id="edit-2"
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
                                <div class="col-md-3">
                                  <div class="form-group">
                                    <label class="control-label">
                                      {{
                                        getCompanyKey("realEstate_unit_floor")
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input
                                      type="number"
                                      class="form-control"
                                      v-model="$v.edit.floor.$model"
                                      :class="{
                                        'is-invalid':
                                          $v.edit.floor.$error || errors.floor,
                                        'is-valid':
                                          !$v.edit.floor.$invalid &&
                                          !errors.floor,
                                      }"
                                    />
                                    <template v-if="errors.floor">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.floor"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                              </div>
                              <hr
                                style="
                                  margin: 10px 0 !important;
                                  border-top: 1px solid rgb(141 163 159 / 42%);
                                "
                              />
                              <div class="row">
                                <div class="col-md-3">
                                  <div class="form-group position-relative">
                                    <label class="control-label">
                                      {{
                                        getCompanyKey("realEstate_unit_status")
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                      @select="showUnitStatusEditModal"
                                      v-model="$v.edit.unit_status_id.$model"
                                      :options="
                                        unit_status.map((type) => type.id)
                                      "
                                      :custom-label="
                                        (opt) =>
                                          $i18n.locale == 'ar'
                                            ? unit_status.find(
                                                (x) => x.id == opt
                                              ).name
                                            : unit_status.find(
                                                (x) => x.id == opt
                                              ).name_e
                                      "
                                    >
                                    </multiselect>
                                    <div
                                      v-if="
                                        $v.edit.unit_status_id.$error ||
                                        errors.unit_status_id
                                      "
                                      class="text-danger"
                                    >
                                      {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.unit_status_id">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.unit_status_id"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="form-group">
                                    <label class="control-label">
                                      {{
                                        getCompanyKey(
                                          "realEstate_unit_unit_area"
                                        )
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input
                                      type="number"
                                      class="form-control"
                                      step="0.1"
                                      v-model="$v.edit.unit_area.$model"
                                      :class="{
                                        'is-invalid':
                                          $v.edit.unit_area.$error ||
                                          errors.unit_area,
                                        'is-valid':
                                          !$v.edit.unit_area.$invalid &&
                                          !errors.unit_area,
                                      }"
                                    />
                                    <template v-if="errors.unit_area">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.land_area"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="form-group">
                                    <label class="control-label">
                                      {{
                                        getCompanyKey(
                                          "realEstate_unit_unit_net_area"
                                        )
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input
                                      type="number"
                                      class="form-control"
                                      step="0.1"
                                      v-model="$v.edit.unit_net_area.$model"
                                      :class="{
                                        'is-invalid':
                                          $v.edit.unit_net_area.$error ||
                                          errors.unit_net_area,
                                        'is-valid':
                                          !$v.edit.unit_net_area.$invalid &&
                                          !errors.unit_net_area,
                                      }"
                                    />
                                    <template v-if="errors.unit_net_area">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.unit_net_area"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="form-group">
                                    <label class="control-label">
                                      {{
                                        getCompanyKey("realEstate_unit_rooms")
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input
                                      type="number"
                                      class="form-control"
                                      v-model="$v.edit.rooms.$model"
                                      :class="{
                                        'is-invalid':
                                          $v.edit.rooms.$error || errors.rooms,
                                        'is-valid':
                                          !$v.edit.rooms.$invalid &&
                                          !errors.rooms,
                                      }"
                                    />
                                    <template v-if="errors.rooms">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.rooms"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="form-group">
                                    <label class="control-label">
                                      {{
                                        getCompanyKey("realEstate_unit_path")
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input
                                      type="number"
                                      class="form-control"
                                      v-model="$v.edit.path.$model"
                                      :class="{
                                        'is-invalid':
                                          $v.edit.path.$error || errors.path,
                                        'is-valid':
                                          !$v.edit.path.$invalid &&
                                          !errors.path,
                                      }"
                                    />
                                    <template v-if="errors.path">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.path"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                              </div>
                              <hr
                                style="
                                  margin: 10px 0 !important;
                                  border-top: 1px solid rgb(141 163 159 / 42%);
                                "
                              />
                              <div class="row">
                                <div class="col-md-3">
                                  <div class="form-group">
                                    <label class="control-label">
                                      {{
                                        getCompanyKey(
                                          "realEstate_unit_finishing"
                                        )
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input
                                      type="number"
                                      class="form-control"
                                      v-model="$v.edit.finishing.$model"
                                      :class="{
                                        'is-invalid':
                                          $v.edit.finishing.$error ||
                                          errors.finishing,
                                        'is-valid':
                                          !$v.edit.finishing.$invalid &&
                                          !errors.finishing,
                                      }"
                                    />
                                    <template v-if="errors.finishing">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.finishing"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="form-group">
                                    <label class="control-label">
                                      {{
                                        getCompanyKey("realEstate_unit_view")
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input
                                      type="number"
                                      class="form-control"
                                      v-model="$v.edit.view.$model"
                                      :class="{
                                        'is-invalid':
                                          $v.edit.view.$error || errors.view,
                                        'is-valid':
                                          !$v.edit.view.$invalid &&
                                          !errors.view,
                                      }"
                                    />
                                    <template v-if="errors.view">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.view"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="form-group">
                                    <label class="control-label">
                                      {{
                                        getCompanyKey(
                                          "realEstate_unit_properties"
                                        )
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                      :multiple="true"
                                      @input="showPropertyModal"
                                      v-model="$v.edit.properties.$model"
                                      :options="
                                        properties.map((type) => type.id)
                                      "
                                      :custom-label="
                                        (opt) =>
                                          $i18n.locale == 'ar'
                                            ? properties.find(
                                                (x) => x.id == opt
                                              ).name
                                            : properties.find(
                                                (x) => x.id == opt
                                              ).name_e
                                      "
                                    >
                                    </multiselect>
                                    <template v-if="errors.properties">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.properties"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label class="mr-2">
                                      {{
                                        getCompanyKey(
                                          "realEstate_unit_description_ar"
                                        )
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <textarea
                                      @input="
                                        arabicValueDescription(
                                          create.description
                                        )
                                      "
                                      v-model="$v.edit.description.$model"
                                      class="form-control"
                                      :maxlength="1000"
                                      rows="5"
                                    ></textarea>
                                    <template v-if="errors.description">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.description"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label class="mr-2">
                                      {{
                                        getCompanyKey(
                                          "realEstate_unit_description_en"
                                        )
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <textarea
                                      @input="
                                        englishValueDescription(
                                          create.description_e
                                        )
                                      "
                                      v-model="$v.edit.description_e.$model"
                                      class="form-control"
                                      :maxlength="1000"
                                      rows="5"
                                    ></textarea>
                                    <template v-if="errors.description_e">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.description_e"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                              </div>
                            </b-tab>
                            <b-tab :title="$t('general.video')">
                              <div class="d-flex justify-content-end">
                                <b-button
                                  variant="success"
                                  type="button"
                                  class="mx-1"
                                  v-if="!isLoader"
                                  @click.prevent="AddVideo('edit')"
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
                              </div>
                              <div class="row">
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label class="control-label">
                                      {{ $t("general.video") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input
                                      type="text"
                                      class="form-control"
                                      v-model="$v.createVideo.$model"
                                      :class="{
                                        'is-invalid': $v.createVideo.$error,
                                        'is-valid': !$v.createVideo.$invalid,
                                      }"
                                    />
                                  </div>
                                </div>
                                <div
                                  v-html="frameUrl"
                                  v-if="frameUrl"
                                  class="col-md-12"
                                ></div>
                              </div>
                            </b-tab>
                            <b-tab :title="$t('general.ImageUploads')">
                              <div class="row">
                                <input
                                  accept="image/png, image/gif, image/jpeg, image/jpg"
                                  type="file"
                                  id="uploadImageEdit"
                                  @change.prevent="onImageChanged"
                                  class="input-file-upload position-absolute"
                                  :class="['d-none']"
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
                                                    showPhoto = photo.url;
                                                    mime_type = photo.mime_type;
                                                  "
                                                >
                                                  <template
                                                    v-if="
                                                      !photo.mime_type.includes(
                                                        'video'
                                                      )
                                                    "
                                                  >
                                                    <img
                                                      data-dz-thumbnail
                                                      :src="photo.url"
                                                      class="avatar-sm rounded bg-light"
                                                      @error="
                                                        src =
                                                          '../../../assets/images/video.jpg'
                                                      "
                                                    />
                                                  </template>
                                                  <template v-else>
                                                    <img
                                                      data-dz-thumbnail
                                                      src="../../../assets/images/video.jpg"
                                                      class="avatar-sm rounded bg-light"
                                                      @error="
                                                        src =
                                                          '../../../assets/images/video.jpg'
                                                      "
                                                    />
                                                  </template>
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
                                      </div>
                                    </div>
                                    <div class="footer-image col-12">
                                      <b-button
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
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div
                                    class="show-dropzone"
                                    v-if="!mime_type.includes('video')"
                                  >
                                    <img
                                      :src="showPhoto"
                                      class="img-thumbnail"
                                      @error="src = './images/img-1.png'"
                                    />
                                  </div>
                                  <div class="show-dropzone" v-else>
                                    <video
                                      width="320"
                                      height="240"
                                      controls
                                      autoplay
                                    >
                                      <source
                                        :src="showPhoto"
                                        :type="mime_type"
                                      />
                                      <source
                                        :src="showPhoto"
                                        type="video/mp4"
                                      />
                                      <source
                                        :src="showPhoto"
                                        type="video/ogg"
                                      />
                                    </video>
                                  </div>
                                </div>
                              </div>
                            </b-tab>
                          </b-tabs>
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
                    <th class="text-center" colspan="25">
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
.dropdown-menu-custom-company.dropdown .dropdown-menu {
  padding: 5px 10px !important;
  overflow-y: scroll;
  height: 300px;
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




