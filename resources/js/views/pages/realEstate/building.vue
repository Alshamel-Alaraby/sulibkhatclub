<script>
import translation from "../../../helper/translation-mixin";

import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {
  required,
  minLength,
  maxLength,
  integer,
  decimal,
  numeric,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import { dynamicSortNumber, dynamicSortString } from "../../../helper/tableSort";
import { formatDateOnly } from "../../../helper/startDate";
import Country from "../../../components/country";
import Avenue from "../../../components/create/avenue";
import City from "../../../components/city";
import Multiselect from "vue-multiselect";
/**
 * Advanced Table component
 */

import { arabicValue, englishValue } from "../../../helper/langTransform";

export default {
  page: {
    title: "Buildings",
    meta: [{ name: "description", content: "Buildings" }],
  },
  mixins: [translation],
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
        vm.$store.state.auth.work_flow_trees.includes("building") ||
        vm.$store.state.auth.work_flow_trees.includes("real estate") ||
        vm.$store.state.auth.user.type == "super_admin"
      ) {
        return true;
      } else {
        return vm.$router.push({ name: "home" });
      }
    });
  },
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    Country,
    City,
    Avenue,
    Multiselect,
  },
  data() {
    return {
      per_page: 50,
      search: "",
      debounce: {},
      buildsPagination: {},
      builds: [],
      cities: [],
      countries: [],
      avenues: [],
      modules: ["sales", "rent"],
      isLoader: false,
      building_id: null,
      createVideo: "",
      frameUrl: "",
      idDelete: null,
      create: {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        land_area: 0,
        building_area: 0,
        construction_year: "",
        module: "sales",
        country_id: null,
        city_id: null,
        avenue_id: null,
        lng: 0,
        lat: 0,
        media: [],

      },
      edit: {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        old_media: [],

        land_area: 0,
        building_area: 0,
        construction_year: "",
        module: "sales",
        country_id: null,
        city_id: null,
        avenue_id: null,
        lng: 0,
        lat: 0,
      },
      b_images: [],
      media: {},
      errors: {},
      isCheckAll: false,
      checkAll: [],
      current_page: 1,
      b_showPhoto: "../../../../../images/img-1.png",
      saveImageName: [],

      setting: {
        name: true,
        name_e: true,
        description: true,
        description_e: true,
        land_area: true,
        building_area: true,
        construction_year: true,
        module_id: true,
        country_id: true,
        city_id: true,
        avenue_id: true,
        lng: true,
        lat: true,
      },
      is_disabled: false,
      filterSetting: [
        "name",
        "name_e",
        "description",
        "description_e",
        "land_area",
        "building_area",
        "construction_year",
        "module",
        this.$i18n.locale == "ar" ? "country.name" : "country.name_e",
        this.$i18n.locale == "ar" ? "city.name" : "city.name_e",
        this.$i18n.locale == "ar" ? "avenue.name" : "avenue.name_e",
        "lng",
        "lat",
      ],
      Tooltip: "",
      mouseEnter: null,
      enabled3: true,
      printLoading: true,
      printObj: {
        id: "printBuilding",
      },
    };
  },
  validations: {
    createVideo: { required },
    create: {
      media: {},
      name: { required, minLength: minLength(2), maxLength: maxLength(255) },
      name_e: { required, minLength: minLength(2), maxLength: maxLength(255) },
      description: { maxLength: maxLength(1000) },
      description_e: { maxLength: maxLength(1000) },
      land_area: { numeric, maxLength: maxLength(9) },
      building_area: { numeric, maxLength: maxLength(9) },
      construction_year: { integer, minLength: minLength(4), maxLength: maxLength(4) },
      module: { required },
      country_id: {},
      city_id: {},
      avenue_id: {},
      lng: { decimal },
      lat: { decimal },
    },
    edit: {
      name: { required, minLength: minLength(2), maxLength: maxLength(255) },
      name_e: { required, minLength: minLength(2), maxLength: maxLength(255) },
      description: { maxLength: maxLength(1000) },
      description_e: { maxLength: maxLength(1000) },
      land_area: { decimal, maxLength: maxLength(9) },
      building_area: { decimal, maxLength: maxLength(9) },
      construction_year: { integer, minLength: minLength(4), maxLength: maxLength(4) },
      module: { required },
      country_id: {},
      city_id: {},
      avenue_id: {},
      lng: { decimal },
      lat: { decimal },
      media: {},

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
        this.builds.forEach((el) => {
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
  updated() {
    $(function () {
      // $(".englishInput").keypress(function (event) {
      //   var ew = event.which;
      //   if (ew == 32) return true;
      //   if (48 <= ew && ew <= 57) return true;
      //   if (65 <= ew && ew <= 90) return true;
      //   if (97 <= ew && ew <= 122) return true;
      //   return false;
      // });
      // $(".arabicInput").keypress(function (event) {
      //   var ew = event.which;
      //   if (ew == 32) return true;
      //   if (48 <= ew && ew <= 57) return true;
      //   if (65 <= ew && ew <= 90) return false;
      //   if (97 <= ew && ew <= 122) return false;
      //   return true;
      // });
    });
  },
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
          .put(`/real-estate/buildings/${this.building_id}`, { ...data, video_link: this.createVideo })
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
     *  start get Data builds && pagination
     */
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      for (let i = 0; i < this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/real-estate/buildings?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.builds = l.data;
          this.buildsPagination = l.pagination;
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
        this.current_page <= this.buildsPagination.last_page &&
        this.current_page != this.buildsPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/real-estate/buildings?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.builds = l.data;
            this.buildsPagination = l.pagination;
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
              .post(`/real-estate/buildings/bulk-delete`, { ids: id })
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
              .delete(`/real-estate/buildings/${id}`)
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
      this.frameUrl = "";
      this.createVideo = "";
      this.building_id = null;
      this.create = {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        land_area: 0,
        building_area: 0,
        construction_year: "",
        module: "sales",
        country_id: null,
        city_id: null,
        avenue_id: null,
        lng: 0,
        lat: 0,
        media: null,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.b_images = [];

      this.errors = {};
      this.$bvModal.hide(`create`);
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      await this.getCategory();
      this.frameUrl = "";
      this.building_id = null;
      this.createVideo = "";
      this.create = {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        land_area: 0,
        building_area: 0,
        construction_year: "",
        module: "sales",
        country_id: null,
        city_id: null,
        avenue_id: null,
        lng: 0,
        lat: 0,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.media = {};
      this.b_images = [];

      this.errors = {};
    },
    /**
     *  create countrie
     */
    async resetForm() {
      this.building_id = null;
      await this.getCategory();
      this.createVideo = "";
      this.frameUrl = "";
      this.create = {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        land_area: 0,
        building_area: 0,
        construction_year: "",
        module: "sales",
        country_id: null,
        city_id: null,
        avenue_id: null,
        lng: 0,
        lat: 0,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.media = {};
      this.b_images = [];

      this.is_disabled = false;
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
          .post(`/real-estate/buildings`, this.create)
          .then((res) => {
            this.building_id = res.data.data.id;
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
          .put(`/real-estate/buildings/${id}`, this.edit)
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
      await this.getCategory();
      let build = this.builds.find((e) => id == e.id);
      await this.getCityTwe(build.country.id);
      await this.getAvenueTwe(build.country.id, build.avenue.id);
      this.building_id = build.id;
      this.createVideo = "";
      this.frameUrl = build.video_link;
      this.edit.name = build.name;
      this.edit.name_e = build.name_e;
      this.edit.description = build.description;
      this.edit.description_e = build.description_e;
      this.edit.building_area = build.building_area ?? 0;
      this.edit.land_area = build.land_area ?? 0;
      this.edit.construction_year = build.construction_year ?? "";
      this.edit.module = build.module;
      this.edit.country_id = build.country.id;
      this.edit.city_id = build.city.id;
      this.edit.avenue_id = build.avenue.id;
      this.edit.lng = build.lng;
      this.edit.lat = build.lat;
      this.b_images = build.media ?? [];
      if (this.b_images && this.b_images.length > 0) {
        this.b_showPhoto = this.b_images[this.b_images.length - 1].webp;
      } else {
        this.b_showPhoto = "../../../../../images/img-1.png";
      }
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.building_id = null;
      this.createVideo = "";
      this.errors = {};
      this.frameUrl = "";
      this.edit = {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        land_area: 0,
        building_area: 0,
        old_media: [],

        construction_year: "",
        module: "sales",
        country_id: null,
        city_id: null,
        avenue_id: null,
        lng: 0,
        lat: 0,
      };
      this.b_images = [];

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
          .get(`/real-estate/buildings/logs/${id}`)
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
    getCurrentYear() {
      return new Date().getFullYear();
    },
    async getCategory() {
      this.create.city_id = null;
      this.edit.city_id = null;
      this.create.avenue_id = null;
      this.edit.avenue_id = null;
      this.cities = [];
      this.avenues = [];
      await adminApi
        .get(`/countries?is_active=active`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضافة دولة", name_e: "Add Country" });
          this.countries = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    async getCity(id = null) {
      if (this.edit.city_id == 0 || this.create.city_id == 0) {
        this.$bvModal.show("city-create");
        this.create.city_id = null;
        this.edit.city_id = null;
      }
      else {
        this.create.avenue_id = null;
        this.edit.avenue_id = null;
        let country = this.create.country_id ? this.create.country_id : this.edit.country_id;
        let city = this.create.city_id ? this.create.city_id : this.edit.city_id;
        await adminApi
          .get(`/avenues?country_id=${country}&city_id=${city}`)
          .then((res) => {
            let l = res.data.data;
            l.unshift({ id: 0, name: "اضافة منطقه", name_e: "Add Avenue" });
            this.avenues = l;
          })
          .catch((err) => {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.Thereisanerrorinthesystem")}`,
            });
          });
      }
      if (id) {
        this.create.city_id = null;
        this.edit.city_id = null;
        this.create.avenue_id = null;
        this.edit.avenue_id = null;
        this.avenues = [];
        await adminApi
          .get(`/cities?country_id=${id}`)
          .then((res) => {
            let l = res.data.data;
            l.unshift({ id: 0, name: "اضافة مدينة", name_e: "Add City" });
            this.cities = l;
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
    async getCityTwe(id = null) {
      if (id) {
        await adminApi
          .get(`/cities?country_id=${id}`)
          .then((res) => {
            let l = res.data.data;
            l.unshift({ id: 0, name: "اضافة مدينة", name_e: "Add City" });
            this.cities = l;
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
    async showAvenueModal(id = null, id2 = null) {
      if (this.edit.avenue_id == 0 || this.create.avenue_id == 0) {
        this.$bvModal.show("avenue-create");
        this.create.avenue_id = null;
        this.edit.avenue_id = null;
      }
    },
    async getAvenue() {
      this.create.avenue_id = null;
      this.edit.avenue_id = null;
      let country = this.create.country_id ? this.create.country_id : this.edit.country_id;
      let city = this.create.city_id ? this.create.city_id : this.edit.city_id;
      await adminApi
        .get(`/avenues?country_id=${country}&city_id=${city}`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضافة منطقه", name_e: "Add Avenue" });
          this.avenues = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    async getAvenueTwe(id, id2) {
      await adminApi
        .get(`/avenues?country_id=${id}&city_id=${id2}`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضافة منطقه", name_e: "Add Avenue" });
          this.avenues = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    showCountryModal() {
      if (this.create.country_id == 0) {
        this.$bvModal.show("country-create");
        this.create.country_id = null;
      } else {
        this.getCity(this.create.country_id);
      }
    },
    showCountryModalEdit() {
      if (this.edit.country_id == 0) {
        this.$bvModal.show("country-create");
        this.edit.country_id = null;
      } else {
        this.getCity(this.edit.country_id);
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
            fn || ("Building" + "." || "SheetJSTableExport.") + (type || "xlsx")
          );
        }
        this.enabled3 = true;
      }, 100);
    },
    async getLocation() {
      if (navigator.geolocation) {
        return await navigator.geolocation.getCurrentPosition(this.showPosition);
      }
    },
    showPosition(position) {
      this.create.lat = position.coords.latitude;
      this.create.lng = position.coords.longitude;
      this.edit.lat = position.coords.latitude;
      this.edit.lng = position.coords.longitude;
    },
    b_changePhoto() {
      document.getElementById("b_uploadImageCreate").click();
    },
    b_changePhotoEdit() {
      document.getElementById("b_uploadImageEdit").click();
    },
    b_onImageChanged(e) {
      const file = e.target.files[0];
      this.addImage(file);
    },
    addImage(file) {
      this.media = file; //upload
      if (file) {
        this.idDelete = null;
        let is_media = this.b_images.find(
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
              this.b_images.forEach((e) => old_media.push(e.id));
              let new_media = [];
              res.data.data.forEach((e) => new_media.push(e.id));
              adminApi
                .put(`/real-estate/buildings/${this.building_id}`, { old_media, media: new_media })
                .then((res) => {
                  this.b_images = res.data.data.media ?? [];
                  if (this.b_images && this.b_images.length > 0) {
                    this.b_showPhoto = this.b_images[this.b_images.length - 1].webp;
                  } else {
                    this.b_showPhoto = "../../../../../images/img-1.png";
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
                  this.b_images.forEach((e) => old_media.push(e.id));
                  old_media.splice(old_media.indexOf(this.idDelete), 1);
                  let new_media = [];
                  res.data.data.forEach((e) => new_media.push(e.id));

                  adminApi
                    .put(`/real-estate/buildings/${this.building_id}`, { old_media, media: new_media })
                    .then((res) => {
                      this.b_images = res.data.data.media ?? [];
                      if (this.b_images && this.b_images.length > 0) {
                        this.b_showPhoto = this.b_images[this.b_images.length - 1].webp;
                      } else {
                        this.b_showPhoto = "../../../../../images/img-1.png";
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
      this.b_images.forEach((e) => {
        if (e.id != id) {
          old_media.push(e.id);
        }
      });
      adminApi
        .put(`/real-estate/buildings/${this.building_id}`, { old_media })
        .then((res) => {
          this.builds[index] = res.data.data;
          this.b_images = res.data.data.media ?? [];
          if (this.b_images && this.b_images.length > 0) {
            this.b_showPhoto = this.b_images[this.b_images.length - 1].webp;
          } else {
            this.b_showPhoto = "../../../../../images/img-1.png";
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

  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <Country :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCategory" />
    <City :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
      @created="getCity(create.country_id ? create.country_id : edit.country_id)" />
    <Avenue :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getAvenue" />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("general.buildsTable") }}</h4>
              <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                <div class="d-inline-block" style="width: 22.2%">
                  <!-- Basic dropdown -->
                  <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                    class="btn-block setting-search">
                    <b-form-checkbox v-model="filterSetting" value="name" class="mb-1">{{
                      getCompanyKey("building_name_ar")
                    }}</b-form-checkbox>
                    <b-form-checkbox v-model="filterSetting" value="name_e" class="mb-1">{{
                      getCompanyKey("building_name_en") }}</b-form-checkbox>
                    <b-form-checkbox v-model="filterSetting" value="description" class="mb-1">{{
                      getCompanyKey("building_description_ar") }}</b-form-checkbox>
                    <b-form-checkbox v-model="filterSetting" value="description_e" class="mb-1">{{
                      getCompanyKey("building_description_en") }}</b-form-checkbox>
                    <b-form-checkbox v-model="filterSetting" value="land_area" class="mb-1">{{
                      getCompanyKey("building_land_area") }}</b-form-checkbox>
                    <b-form-checkbox v-model="filterSetting" value="building_area" class="mb-1">{{
                      getCompanyKey("building_area") }}</b-form-checkbox>
                    <b-form-checkbox v-model="filterSetting" value="construction_year" class="mb-1">{{
                      getCompanyKey("building_construction_year") }}</b-form-checkbox>
                    <b-form-checkbox v-model="filterSetting"
                      :value="$i18n.locale == 'ar' ? 'country.name' : 'country.name_e'" class="mb-1">{{
                        getCompanyKey("country") }}</b-form-checkbox>
                    <b-form-checkbox v-model="filterSetting"
                      :value="$i18n.locale == 'ar' ? 'country.name' : 'country.name_e'" class="mb-1">{{
                        getCompanyKey("city") }}</b-form-checkbox>
                    <b-form-checkbox v-model="filterSetting"
                      :value="$i18n.locale == 'ar' ? 'country.name' : 'country.name_e'" class="mb-1">{{
                        getCompanyKey("avenue") }}</b-form-checkbox>
                    <b-form-checkbox v-model="filterSetting" value="lng" class="mb-1">{{
                      getCompanyKey("building_longitude")
                    }}</b-form-checkbox>
                    <b-form-checkbox v-model="filterSetting" value="lat" class="mb-1">{{
                      getCompanyKey("building_latitude")
                    }}</b-form-checkbox>
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
                  <button v-print="'#printBuilding'" class="custom-btn-dowonload">
                    <i class="fe-printer"></i>
                  </button>
                  <button class="custom-btn-dowonload" @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                    v-if="checkAll.length == 1">
                    <i class="mdi mdi-square-edit-outline"></i>
                  </button>
                  <!-- start mult delete  -->
                  <button class="custom-btn-dowonload" v-if="checkAll.length > 1"
                    @click.prevent="deleteCountry(checkAll)">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button class="custom-btn-dowonload" v-if="checkAll.length == 1"
                    @click.prevent="deleteCountry(checkAll[0])">
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
                      ref="dropdown" class="dropdown-custom-ali dropdown-menu-custom-company">
                      <b-form-checkbox v-model="setting.name" class="mb-1">{{
                        getCompanyKey("building_name_ar")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.name_e" class="mb-1">{{
                        getCompanyKey("building_name_en")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.description" class="mb-1">{{
                        getCompanyKey("building_description_ar")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.description_e" class="mb-1">{{
                        getCompanyKey("building_description_en")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.construction_year" class="mb-1">{{
                        getCompanyKey("building_construction_year")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.building_area" class="mb-1">{{
                        getCompanyKey("building_area")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.land_area" class="mb-1">{{
                        getCompanyKey("building_land_area")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.lat" class="mb-1">{{
                        getCompanyKey("building_latitude")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.lng" class="mb-1">{{
                        getCompanyKey("building_longitude")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.module_id" class="mb-1">{{
                        getCompanyKey("module")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.country_id" class="mb-1">{{
                        getCompanyKey("country")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.city_id" class="mb-1">{{
                        getCompanyKey("city")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.avenue_id" class="mb-1">{{
                        getCompanyKey("avenue")
                      }}</b-form-checkbox>
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
                      {{ buildsPagination.from }}-{{ buildsPagination.to }} /
                      {{ buildsPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a href="javascript:void(0)" :style="{
                        'pointer-events':
                          buildsPagination.current_page == 1 ? 'none' : '',
                      }" @click.prevent="getData(buildsPagination.current_page - 1)">
                        <span>&lt;</span>
                      </a>
                      <input type="text" @keyup.enter="getDataCurrentPage()" v-model="current_page"
                        class="pagination-current-page" />
                      <a href="javascript:void(0)" :style="{
                        'pointer-events':
                          buildsPagination.last_page == buildsPagination.current_page
                            ? 'none'
                            : '',
                      }" @click.prevent="getData(buildsPagination.current_page + 1)">
                        <span>&gt;</span>
                      </a>
                    </div>
                  </div>
                  <!-- end Pagination -->
                </div>
              </div>
            </div>

            <!--  create   -->
            <b-modal id="create" :title="getCompanyKey('building_create_form')" title-class="font-18"
              dialog-class="modal-full-width" body-class="p-4 " :hide-footer="true" @show="resetModal"
              @hidden="resetModalHidden">
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
                <b-tabs nav-class="nav-tabs nav-bordered">
                  <b-tab :title="$t('general.DataEntry')" active>
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label">
                            {{ getCompanyKey("building_name_ar") }}
                            <span class="text-danger">*</span>
                          </label>
                          <div dir="rtl">
                            <input @keyup="arabicValue(create.name)" type="text" class="form-control"
                              v-model="$v.create.name.$model" :class="{
                                'is-invalid': $v.create.name.$error || errors.name,
                                'is-valid': !$v.create.name.$invalid && !errors.name,
                              }" />
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
                            <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index">{{ errorMessage }}
                            </ErrorMessage>
                          </template>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label">
                            {{ getCompanyKey("building_name_en") }}
                            <span class="text-danger">*</span>
                          </label>
                          <div dir="ltr">
                            <input @keyup="englishValue(create.name_e)" type="text" class="form-control"
                              v-model="$v.create.name_e.$model" :class="{
                                'is-invalid':
                                  $v.create.name_e.$error || errors.name_e,
                                'is-valid':
                                  !$v.create.name_e.$invalid && !errors.name_e,
                              }" />
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
                    </div>
                    <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="mr-2">
                            {{ $t("general.construction_year") }}
                            <span class="text-danger">*</span>
                          </label>
                          <select class="custom-select mr-sm-2" v-model="$v.create.construction_year.$model" :class="{
                            'is-invalid':
                              $v.create.construction_year.$error ||
                              errors.construction_year,
                            'is-valid':
                              !$v.create.construction_year.$invalid &&
                              !errors.construction_year,
                          }">
                            <option value="" selected>
                              {{ $t("general.Choose") }}...
                            </option>
                            <option v-for="year in getCurrentYear()" v-if="year >= 2000" :value="year">
                              {{ year }}
                            </option>
                          </select>
                          <template v-if="errors.construction_year">
                            <ErrorMessage v-for="(
                                                                                        errorMessage, index
                                                                                      ) in errors.construction_year"
                              :key="index">
                              {{
                                errorMessage
                              }}
                            </ErrorMessage>
                          </template>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label">
                            {{ getCompanyKey("building_area") }}
                            <span class="text-danger">*</span>
                          </label>
                          <input type="number" class="form-control" step="0.1" v-model="$v.create.building_area.$model"
                            :class="{
                              'is-invalid':
                                $v.create.building_area.$error ||
                                errors.building_area,
                              'is-valid':
                                !$v.create.building_area.$invalid &&
                                !errors.building_area,
                            }" />
                          <template v-if="errors.building_area">
                            <ErrorMessage v-for="(errorMessage, index) in errors.building_area" :key="index">{{
                              errorMessage
                            }}</ErrorMessage>
                          </template>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label">
                            {{ getCompanyKey("building_land_area") }}
                            <span class="text-danger">*</span>
                          </label>
                          <input type="number" class="form-control" step="0.1" v-model="$v.create.land_area.$model"
                            :class="{
                              'is-invalid':
                                $v.create.land_area.$error || errors.land_area,
                              'is-valid':
                                !$v.create.land_area.$invalid && !errors.land_area,
                            }" />
                          <template v-if="errors.land_area">
                            <ErrorMessage v-for="(errorMessage, index) in errors.land_area" :key="index">{{ errorMessage
                            }}
                            </ErrorMessage>
                          </template>
                        </div>
                      </div>
                    </div>
                    <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group position-relative">
                          <label class="control-label">
                            {{ getCompanyKey("country") }}
                            <span class="text-danger">*</span>
                          </label>
                          <multiselect @input="showCountryModal" v-model="$v.create.country_id.$model"
                            :options="countries.map((type) => type.id)" :custom-label="
                              (opt) => countries.find((x) => x.id == opt).name
                            ">
                          </multiselect>
                          <div v-if="$v.create.country_id.$error || errors.country_id" class="text-danger">
                            {{ $t("general.fieldIsRequired") }}
                          </div>
                          <template v-if="errors.country_id">
                            <ErrorMessage v-for="(errorMessage, index) in errors.country_id" :key="index">{{ errorMessage
                            }}
                            </ErrorMessage>
                          </template>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group position-relative">
                          <label class="control-label">
                            {{ getCompanyKey("city") }}
                            <span class="text-danger">*</span>
                          </label>
                          <multiselect @input="getCity()" v-model="$v.create.city_id.$model"
                            :options="cities.map((type) => type.id)" :custom-label="
                              (opt) => cities.find((x) => x.id == opt).name
                            ">
                          </multiselect>
                          <div v-if="$v.create.city_id.$error || errors.city_id" class="text-danger">
                            {{ $t("general.fieldIsRequired") }}
                          </div>
                          <template v-if="errors.city_id">
                            <ErrorMessage v-for="(errorMessage, index) in errors.city_id" :key="index">{{ errorMessage }}
                            </ErrorMessage>
                          </template>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group position-relative">
                          <label class="control-label">
                            {{ getCompanyKey("avenue") }}
                            <span class="text-danger">*</span>
                          </label>
                          <multiselect @input="showAvenueModal" v-model="$v.create.avenue_id.$model"
                            :options="avenues.map((type) => type.id)" :custom-label="
                              (opt) => avenues.find((x) => x.id == opt).name
                            ">
                          </multiselect>
                          <div v-if="$v.create.avenue_id.$error || errors.avenue_id" class="text-danger">
                            {{ $t("general.fieldIsRequired") }}
                          </div>
                          <template v-if="errors.city_id">
                            <ErrorMessage v-for="(errorMessage, index) in errors.avenue_id" :key="index">{{ errorMessage
                            }}
                            </ErrorMessage>
                          </template>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label">
                            {{ getCompanyKey("building_longitude") }}
                            <span class="text-danger">*</span>
                          </label>
                          <input type="number" class="form-control" step="0.00000000000001" v-model="$v.create.lng.$model"
                            :class="{
                              'is-invalid': $v.create.lng.$error || errors.lng,
                              'is-valid': !$v.create.lng.$invalid && !errors.lng,
                            }" />
                          <template v-if="errors.lng">
                            <ErrorMessage v-for="(errorMessage, index) in errors.lng" :key="index">{{ errorMessage }}
                            </ErrorMessage>
                          </template>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label">
                            {{ getCompanyKey("building_latitude") }}
                            <span class="text-danger">*</span>
                          </label>
                          <input type="number" class="form-control" data-create="9" step="0.00000000000001"
                            v-model="$v.create.lat.$model" :class="{
                              'is-invalid': $v.create.lat.$error || errors.lat,
                              'is-valid': !$v.create.lat.$invalid && !errors.lat,
                            }" />
                          <template v-if="errors.lat">
                            <ErrorMessage v-for="(errorMessage, index) in errors.lat" :key="index">{{ errorMessage }}
                            </ErrorMessage>
                          </template>
                        </div>
                      </div>
                      <div class="col-md-4 d-flex align-items-center mt-3">
                        <button class="btn btn-primary" type="button" @click="getLocation">
                          {{ $t('general.location') }}
                        </button>
                      </div>
                    </div>
                    <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="mr-2" for="inlineFormCustomSelectPref">
                            {{ getCompanyKey("building_description_ar") }}
                            <span class="text-danger">*</span>
                          </label>
                          <textarea @input="arabicValueDescription(create.description)"
                            v-model="$v.create.description.$model" class="form-control" :maxlength="1000"
                            rows="5"></textarea>
                          <template v-if="errors.description">
                            <ErrorMessage v-for="(errorMessage, index) in errors.description" :key="index">{{ errorMessage
                            }}
                            </ErrorMessage>
                          </template>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="mr-2">
                            {{ getCompanyKey("building_description_en") }}
                            <span class="text-danger">*</span>
                          </label>
                          <textarea @input="englishValueDescription(create.description_e)"
                            v-model="$v.create.description_e.$model" class="form-control" :maxlength="1000"
                            rows="5"></textarea>
                          <template v-if="errors.description_e">
                            <ErrorMessage v-for="(errorMessage, index) in errors.description_e" :key="index">{{
                              errorMessage
                            }}</ErrorMessage>
                          </template>
                        </div>
                      </div>
                    </div>
                  </b-tab>
                  <b-tab :disabled="!building_id" :title="$t('general.ImageUploads')">
                    <div class="row">
                      <input accept="image/png, image/gif, image/jpeg, image/jpg" type="file" id="b_uploadImageCreate"
                        @change.prevent="b_onImageChanged" class="input-file-upload position-absolute" :class="[
                          'd-none',
                          {
                            'is-invalid': $v.create.media.$error || errors.media,
                            'is-valid': !$v.create.media.$invalid && !errors.media,
                          },
                        ]" />
                      <div class="col-md-8 my-1">
                        <!-- file upload -->
                        <div class="row align-content-between" style="width: 100%; height: 100%">
                          <div class="col-12">
                            <div class="d-flex flex-wrap">
                              <div :class="[
                                'dropzone-previews col-4 position-relative mb-2',
                              ]" v-for="(photo, index) in b_images" :key="photo.id">
                                <div :class="[
                                  'card mb-0 shadow-none border',
                                  b_images.length - 1 == index ? 'bg-primary' : '',
                                ]">
                                  <div class="p-2">
                                    <div class="row align-items-center">
                                      <div class="col-auto" @click="b_showPhoto = photo.webp">
                                        <img data-dz-thumbnail :src="photo.webp" class="avatar-sm rounded bg-light"
                                          @error="src = '../../../../../images/img-1.png'" />
                                      </div>
                                      <div class="col pl-0">
                                        <a href="javascript:void(0);" :class="[
                                          'font-weight-bold',
                                          b_images.length - 1 == index
                                            ? 'text-white'
                                            : 'text-muted',
                                        ]" data-dz-name>
                                          {{ photo.name }}
                                        </a>
                                      </div>
                                      <!-- Button -->
                                      <a href="javascript:void(0);" :class="[
                                        'btn-danger dropzone-close',
                                        $i18n.locale == 'ar'
                                          ? 'dropzone-close-rtl'
                                          : '',
                                      ]" data-dz-remove @click.prevent="
  deleteImageCreate(photo.id, index)
">
                                        <i class="fe-x"></i>
                                      </a>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="footer-image col-12">
                            <b-button @click="b_changePhoto" variant="success" type="button"
                              class="mx-1 font-weight-bold px-3" v-if="!isLoader">
                              {{ $t("general.Add") }}
                            </b-button>
                            <b-button variant="success" class="mx-1" disabled v-else>
                              <b-spinner small></b-spinner>
                              <span class="sr-only">{{ $t("login.Loading") }}...</span>
                            </b-button>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="show-dropzone">
                          <img :src="b_showPhoto" class="img-thumbnail" @error="src = '../../../../../images/img-1.png'" />
                        </div>
                      </div>
                    </div>
                  </b-tab>
                  <b-tab :disabled="!building_id" :title="$t('general.video')">
                    <div class="d-flex justify-content-end">
                      <b-button variant="success" type="button" class="mx-1" v-if="!isLoader"
                        @click.prevent="AddVideo('create')">
                        {{ $t("general.Add") }}
                      </b-button>
                      <b-button variant="success" class="mx-1" disabled v-else>
                        <b-spinner small></b-spinner>
                        <span class="sr-only">{{ $t("login.Loading") }}...</span>
                      </b-button>
                    </div>
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label">
                            {{ $t("general.video") }}
                            <span class="text-danger">*</span>
                          </label>
                          <input type="text" class="form-control" v-model="$v.createVideo.$model" :class="{
                            'is-invalid':
                              $v.createVideo.$error,
                            'is-valid':
                              !$v.createVideo.$invalid
                          }" />
                        </div>
                      </div>
                      <div v-html="frameUrl" v-if="frameUrl" class="col-md-12"></div>
                    </div>
                  </b-tab>
                </b-tabs>
              </form>
            </b-modal>
            <!--  /create   -->

            <!-- start .table-responsive-->
            <div class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table"
              id="printBuilding">
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->

              <table class="table table-borderless table-hover table-centered m-0">
                <thead>
                  <tr>
                    <th scope="col" style="width: 0" v-if="enabled3" class="do-not-print">
                      <div class="form-check custom-control">
                        <input class="form-check-input" type="checkbox" v-model="isCheckAll"
                          style="width: 17px; height: 17px" />
                      </div>
                    </th>
                    <th v-if="setting.name">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("building_name_ar") }}</span>
                        <div class="arrow-sort">
                          <i class="fas fa-arrow-up" @click="builds.sort(sortString('name'))"></i>
                          <i class="fas fa-arrow-down" @click="builds.sort(sortString('-name'))"></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.name_e">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("building_name_en") }}</span>
                        <div class="arrow-sort">
                          <i class="fas fa-arrow-up" @click="builds.sort(sortString('name_e'))"></i>
                          <i class="fas fa-arrow-down" @click="builds.sort(sortString('-name_e'))"></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.description">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("building_description_ar") }}</span>
                        <div class="arrow-sort">
                          <i class="fas fa-arrow-up" @click="builds.sort(sortString('description'))"></i>
                          <i class="fas fa-arrow-down" @click="builds.sort(sortString('-description'))"></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.description_e">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("building_description_en") }}</span>
                        <div class="arrow-sort">
                          <i class="fas fa-arrow-up" @click="builds.sort(sortString('description_e'))"></i>
                          <i class="fas fa-arrow-down" @click="builds.sort(sortString('-description_e'))"></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.building_area">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("building_area") }}</span>
                        <div class="arrow-sort">
                          <i class="fas fa-arrow-up" @click="builds.sort(SortNumber('building_area'))"></i>
                          <i class="fas fa-arrow-down" @click="builds.sort(SortNumber('-building_area'))"></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.land_area">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("building_land_area") }}</span>
                        <div class="arrow-sort">
                          <i class="fas fa-arrow-up" @click="builds.sort(SortNumber('land_area'))"></i>
                          <i class="fas fa-arrow-down" @click="builds.sort(SortNumber('-land_area'))"></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.construction_year">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("building_construction_year") }}</span>
                        <div class="arrow-sort">
                          <i class="fas fa-arrow-up" @click="builds.sort(SortNumber('construction_year'))"></i>
                          <i class="fas fa-arrow-down" @click="builds.sort(SortNumber('-construction_year'))"></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.module_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("module") }}</span>
                      </div>
                    </th>
                    <th v-if="setting.country_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("country") }}</span>
                      </div>
                    </th>
                    <th v-if="setting.city_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("city") }}</span>
                      </div>
                    </th>
                    <th v-if="setting.avenue_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("avenue") }}</span>
                      </div>
                    </th>
                    <th v-if="setting.lng">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("building_longitude") }}</span>
                      </div>
                    </th>
                    <th v-if="setting.lat">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("building_latitude") }}</span>
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
                <tbody v-if="builds.length > 0">
                  <tr @click.capture="checkRow(data.id)" @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in builds" :key="data.id" class="body-tr-custom">
                    <td v-if="enabled3" class="do-not-print">
                      <div class="form-check custom-control" style="min-height: 1.9em">
                        <input style="width: 17px; height: 17px" class="form-check-input" type="checkbox"
                          v-model="checkAll" :value="data.id" />
                      </div>
                    </td>
                    <td v-if="setting.name">
                      <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                    </td>
                    <td v-if="setting.name_e">
                      <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
                    </td>
                    <td v-if="setting.description">{{ data.description }}</td>
                    <td v-if="setting.description_e">{{ data.description_e }}</td>
                    <td v-if="setting.building_area">{{ data.building_area }}</td>
                    <td v-if="setting.land_area">{{ data.land_area }}</td>
                    <td v-if="setting.construction_year">{{ data.construction_year }}</td>
                    <td v-if="setting.module_id">{{ data.module }}</td>
                    <td v-if="setting.country_id">
                      {{
                        data.country
                        ? $i18n.locale == "ar"
                          ? data.country.name
                          : data.country.name_e
                        : "-"
                      }}
                    </td>
                    <td v-if="setting.city_id">
                      {{
                        data.country
                        ? $i18n.locale == "ar"
                          ? data.city.name
                          : data.city.name_e
                        : "-"
                      }}
                    </td>
                    <td v-if="setting.avenue_id">
                      {{
                        data.avenue
                        ? $i18n.locale == "ar"
                          ? data.avenue.name
                          : data.avenue.name_e
                        : "-"
                      }}
                    </td>
                    <td v-if="setting.lng">{{ data.lng }}</td>
                    <td v-if="setting.lat">{{ data.lat }}</td>
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
                          <a class="dropdown-item text-black" href="#" @click.prevent="deleteCountry(data.id)">
                            <div class="d-flex justify-content-between align-items-center text-black">
                              <span>{{ $t("general.delete") }}</span>
                              <i class="fas fa-times text-danger"></i>
                            </div>
                          </a>
                        </div>
                      </div>

                      <!--  edit   -->
                      <b-modal :id="`modal-edit-${data.id}`" :title="getCompanyKey('building_edit_form')"
                        title-class="font-18" body-class="p-4" dialog-class="modal-full-width" :ref="`edit-${data.id}`"
                        :hide-footer="true" @show="resetModalEdit(data.id)" @hidden="resetModalHiddenEdit(data.id)">
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
                          <b-tabs>
                            <b-tab :title="$t('general.DataEntry')" active>
                              <div class="row">
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label for="field-1" class="control-label">
                                      {{ getCompanyKey("building_name_ar") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <div dir="rtl">
                                      <input @keyup="arabicValue(edit.name)" type="text" class="form-control"
                                        data-edit="1" v-model="$v.edit.name.$model" :class="{
                                          'is-invalid': $v.edit.name.$error || errors.name,
                                          'is-valid': !$v.edit.name.$invalid && !errors.name,
                                        }" />
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
                                      <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index">{{
                                        errorMessage
                                      }}</ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label for="field-2" class="control-label">
                                      {{ getCompanyKey("building_name_en") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <div dir="ltr">
                                      <input @keyup="englishValue(edit.name_e)" type="text" class="form-control"
                                        data-edit="2" v-model="$v.edit.name_e.$model" :class="{
                                          'is-invalid':
                                            $v.edit.name_e.$error || errors.name_e,
                                          'is-valid':
                                            !$v.edit.name_e.$invalid && !errors.name_e,
                                        }" />
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
                                        errorMessage }}</ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                              </div>
                              <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                              <div class="row">
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label class="mr-2">
                                      {{ $t("general.construction_year") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <select class="custom-select mr-sm-2" data-edit="6"
                                      v-model="$v.edit.construction_year.$model" :class="{
                                        'is-invalid':
                                          $v.edit.construction_year.$error ||
                                          errors.construction_year,
                                        'is-valid':
                                          !$v.edit.construction_year.$invalid &&
                                          !errors.construction_year,
                                      }">
                                      <option value="" selected>
                                        {{ $t("general.Choose") }}...
                                      </option>
                                      <option v-for="year in getCurrentYear()" v-if="year >= 2000" :value="year">
                                        {{ year }}
                                      </option>
                                    </select>
                                    <template v-if="errors.construction_year">
                                      <ErrorMessage v-for="(
                                                                                        errorMessage, index
                                                                                      ) in errors.construction_year"
                                        :key="index">
                                        {{
                                          errorMessage
                                        }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label class="control-label">
                                      {{ getCompanyKey("building_area") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" data-edit="9" step="0.1"
                                      v-model="$v.edit.building_area.$model" :class="{
                                        'is-invalid':
                                          $v.edit.building_area.$error ||
                                          errors.building_area,
                                        'is-valid':
                                          !$v.edit.building_area.$invalid &&
                                          !errors.building_area,
                                      }" />
                                    <template v-if="errors.building_area">
                                      <ErrorMessage v-for="(errorMessage, index) in errors.building_area" :key="index">{{
                                        errorMessage }}</ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label class="control-label">
                                      {{ getCompanyKey("building_land_area") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" data-edit="9" step="0.1"
                                      v-model="$v.edit.land_area.$model" :class="{
                                        'is-invalid':
                                          $v.edit.land_area.$error || errors.land_area,
                                        'is-valid':
                                          !$v.edit.land_area.$invalid && !errors.land_area,
                                      }" />
                                    <template v-if="errors.land_area">
                                      <ErrorMessage v-for="(errorMessage, index) in errors.land_area" :key="index">{{
                                        errorMessage }}</ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                              </div>
                              <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                              <div class="row">
                                <div class="col-md-4">
                                  <div class="form-group position-relative">
                                    <label class="control-label">
                                      {{ getCompanyKey("country") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <multiselect @input="showCountryModalEdit" v-model="$v.edit.country_id.$model"
                                      :options="countries.map((type) => type.id)" :custom-label="
                                        (opt) => countries.find((x) => x.id == opt).name
                                      ">
                                    </multiselect>
                                    <div v-if="$v.edit.country_id.$error || errors.country_id" class="text-danger">
                                      {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.country_id">
                                      <ErrorMessage v-for="(errorMessage, index) in errors.country_id" :key="index">{{
                                        errorMessage }}</ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group position-relative">
                                    <label class="control-label">
                                      {{ getCompanyKey("city") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <multiselect @input="getCity()" v-model="$v.edit.city_id.$model"
                                      :options="cities.map((type) => type.id)" :custom-label="
                                        (opt) => cities.find((x) => x.id == opt).name
                                      ">
                                    </multiselect>
                                    <div v-if="$v.edit.city_id.$error || errors.city_id" class="text-danger">
                                      {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.city_id">
                                      <ErrorMessage v-for="(errorMessage, index) in errors.city_id" :key="index">{{
                                        errorMessage }}</ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group position-relative">
                                    <label class="control-label">
                                      {{ getCompanyKey("avenue") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <multiselect @input="showAvenueModal" v-model="$v.edit.avenue_id.$model"
                                      :options="avenues.map((type) => type.id)" :custom-label="
                                        (opt) => avenues.find((x) => x.id == opt).name
                                      ">
                                    </multiselect>
                                    <div v-if="$v.edit.avenue_id.$error || errors.avenue_id" class="text-danger">
                                      {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.city_id">
                                      <ErrorMessage v-for="(errorMessage, index) in errors.avenue_id" :key="index">{{
                                        errorMessage }}</ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label class="control-label">
                                      {{ getCompanyKey("building_longitude") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" data-edit="9" step="0.00000000000001"
                                      v-model="$v.edit.lng.$model" :class="{
                                        'is-invalid': $v.edit.lng.$error || errors.lng,
                                        'is-valid': !$v.edit.lng.$invalid && !errors.lng,
                                      }" />
                                    <template v-if="errors.lng">
                                      <ErrorMessage v-for="(errorMessage, index) in errors.lng" :key="index">{{
                                        errorMessage
                                      }}</ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label class="control-label">
                                      {{ getCompanyKey("building_latitude") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" data-edit="9" step="0.00000000000001"
                                      v-model="$v.edit.lat.$model" :class="{
                                        'is-invalid': $v.edit.lat.$error || errors.lat,
                                        'is-valid': !$v.edit.lat.$invalid && !errors.lat,
                                      }" />
                                    <template v-if="errors.lat">
                                      <ErrorMessage v-for="(errorMessage, index) in errors.lat" :key="index">{{
                                        errorMessage
                                      }}</ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-center mt-3">
                                  <button class="btn btn-primary" type="button" @click="getLocation">
                                    {{ $t('general.location') }}
                                  </button>
                                </div>
                              </div>
                              <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" />
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label class="mr-2" for="inlineFormCustomSelectPref">
                                      {{ getCompanyKey("building_description_ar") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <textarea @input="arabicValueDescription(edit.description)"
                                      v-model="$v.edit.description.$model" class="form-control" :maxlength="1000"
                                      rows="5"></textarea>
                                    <template v-if="errors.description">
                                      <ErrorMessage v-for="(errorMessage, index) in errors.description" :key="index">{{
                                        errorMessage }}</ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label class="mr-2" for="inlineFormCustomSelectPref">
                                      {{ getCompanyKey("building_description_en") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <textarea @input="englishValueDescription(edit.description_e)"
                                      v-model="$v.edit.description_e.$model" class="form-control" :maxlength="1000"
                                      rows="5"></textarea>
                                    <template v-if="errors.description_e">
                                      <ErrorMessage v-for="(errorMessage, index) in errors.description_e" :key="index">{{
                                        errorMessage }}</ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                              </div>
                            </b-tab>
                            <b-tab :title="$t('general.video')">
                              <div class="d-flex justify-content-end">
                                <b-button variant="success" type="button" class="mx-1" v-if="!isLoader"
                                  @click.prevent="AddVideo('edit')">
                                  {{ $t("general.Add") }}
                                </b-button>
                                <b-button variant="success" class="mx-1" disabled v-else>
                                  <b-spinner small></b-spinner>
                                  <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                </b-button>
                              </div>
                              <div class="row">
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label class="control-label">
                                      {{ $t("general.video") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" v-model="$v.createVideo.$model" :class="{
                                      'is-invalid':
                                        $v.createVideo.$error,
                                      'is-valid':
                                        !$v.createVideo.$invalid
                                    }" />
                                  </div>
                                </div>
                                <div v-html="frameUrl" v-if="frameUrl" class="col-md-12"></div>
                              </div>
                            </b-tab>
                            <b-tab :title="$t('general.ImageUploads')">
                              <div class="row">
                                <input accept="image/png, image/gif, image/jpeg, image/jpg" type="file"
                                  id="b_uploadImageEdit" @change.prevent="b_onImageChanged"
                                  class="input-file-upload position-absolute" :class="[
                                    'd-none',
                                    {
                                      'is-invalid': $v.edit.media.$error || errors.media,
                                      'is-valid':
                                        !$v.edit.media.$invalid && !errors.media,
                                    },
                                  ]" />
                                <div class="col-md-8 my-1">
                                  <!-- file upload -->
                                  <div class="row align-content-between" style="width: 100%; height: 100%">
                                    <div class="col-12">
                                      <div class="d-flex flex-wrap">
                                        <div class="dropzone-previews col-4 position-relative mb-2"
                                          v-for="(photo, index) in b_images">
                                          <div :class="[
                                            'card mb-0 shadow-none border',
                                            b_images.length - 1 == index
                                              ? 'bg-primary'
                                              : '',
                                          ]">
                                            <div class="p-2">
                                              <div class="row align-items-center">
                                                <div class="col-auto" @click="b_showPhoto = photo.webp">
                                                  <img data-dz-thumbnail :src="photo.webp"
                                                    class="avatar-sm rounded bg-light"
                                                    @error="src = '../../../../../images/img-1.png'" />
                                                </div>
                                                <div class="col pl-0">
                                                  <a href="javascript:void(0);" :class="[
                                                    'font-weight-bold',
                                                    b_images.length - 1 == index
                                                      ? 'text-white'
                                                      : 'text-muted',
                                                  ]" data-dz-name>
                                                    {{ photo.name }}
                                                  </a>
                                                </div>
                                                <!-- Button -->
                                                <a href="javascript:void(0);" :class="[
                                                  'btn-danger text-muted dropzone-close',
                                                  $i18n.locale == 'ar'
                                                    ? 'dropzone-close-rtl'
                                                    : '',
                                                ]" data-dz-remove @click.prevent="
  deleteImageCreate(photo.id, index)
">
                                                  <i class="fe-x"></i>
                                                </a>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="footer-image col-12">
                                      <b-button @click="b_changePhotoEdit" variant="success" type="button"
                                        class="mx-1 font-weight-bold px-3" v-if="!isLoader">
                                        {{ $t("general.Add") }}
                                      </b-button>
                                      <b-button variant="success" class="mx-1" disabled v-else>
                                        <b-spinner small></b-spinner>
                                        <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                      </b-button>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="show-dropzone">
                                    <img :src="b_showPhoto" class="img-thumbnail"
                                      @error="src = '../../../../../images/img-1.png'" />
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
                    <th class="text-center" colspan="16">
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
<style >
.dropdown-menu-custom-company.dropdown .dropdown-menu {
  padding: 5px 10px !important;
  overflow-y: scroll;
  height: 300px;
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
