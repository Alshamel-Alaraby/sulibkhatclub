<script>
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import successError from "../../../helper/mixin/success&error";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import Country from "../general/country";
import Avenue from "../general/avenue";
import City from "../general/city";
import currency from "../general/currency";
import Accounts from "../general/Accounts.vue";
import buildingType from "./buildingType.vue";
import {
  required,
  minLength,
  maxLength,
  integer,
  numeric,
  decimal,
  oneOf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
import Governorate from "../general/governate.vue";
import Street from "../general/street";
/**
 * Advanced Table component
 */

export default {
  components: {
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
    Country,
    Avenue,
    City,
    currency,
    Accounts,
    Governorate,
    Street,
    buildingType,
  },
  mixins: [transMixinComp, successError],
  props: {
    id: { default: "create" },
    companyKeys: { default: [] },
    defaultsKeys: { default: [] },
    isPage: { default: true },
    isVisiblePage: { default: null },
    isRequiredPage: { default: null },
    type: { default: "create" },
    idObjEdit: { default: null },
    isPermission: {},
    url: { default: "/real-estate/buildings" },
  },
  data() {
    return {
      urlBuildingPolicy: "/real-estate/building-policy",
      managementPolicyTabDisable: true,
      componentsTabDisabled: true,
      financeTabDisabled: true,
      accountingTabDisabled: true,
      builds: [],
      governorates: [],
      streets: [],
      cities: [],
      countries: [],
      avenues: [],
      currencies: [],
      buildingTypes: [],
      accounts: [],
      streets: [],
      modules: ["sales", "rent"],
      isLoader: false,
      isCustom: false,
      building_id: null,
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
        governorrate_id: null,
        street_id: null,
        lng: 0,
        lat: 0,
        media: [],
        floors_number: 0,
        mediums_number: 0,
        ground_floors_number: 0,
        vaults_number: 0,
        central_air_conditioning: 0,
        elevators_number: 0,
        electricity_meters_number: 0,
        water_meters_number: 0,
        gas_meters_number: 0,
        buying_price: 0,
        buying_date: "",
        middleman_cost: 0,
        registration_cost: 0,
        building_currency_id: null,
        building_type_id: null,
        policy_id: null,
        accrued_revenues_account_id: null,
        advance_revenues_account_id: null,
        revenues_account_id: null,
        discounts_account_id: null,
        cash_account_id: null,
        knet_account_id: null,
        insurance_account_id: null,
        company_ownership: "",
      },
      building_policy_id: null,
      createPolicy: {
        building_policy: [
          {
            policy_id: null,
            year: null,
            month: null,
            amount: null,
            percent: null,
            percent_amount: "",
            after_expenses: "",
            plus_extra_revenues: "",
            collected_rent_type: "",
            company_pays_rest: "",
            owner_pays_rest: "",
          },
        ],
      },
      policies: [],
      showCompanyPaysInput: false,
      showOwnerPaysInput: false,
      showAmountInput: false,
      showPercentInput: false,
      showPercentAmountInput: false,
      b_images: [],
      media: {},
      b_showPhoto: "../../../../../images/img-1.png",
      saveImageName: [],
      createVideo: "",
      frameUrl: "",
      idDelete: null,
      errors: {},
      is_disabled: false,
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
      construction_year: {
        integer,
        minLength: minLength(4),
        maxLength: maxLength(4),
      },
      module: { required },
      country_id: {},
      city_id: {},
      avenue_id: {},
      governorrate_id: {},
      street_id: {},
      lng: { decimal },
      lat: { decimal },
      floors_number: { integer, maxLength: maxLength(2) },
      mediums_number: { integer, maxLength: maxLength(2) },
      ground_floors_number: { integer, maxLength: maxLength(2) },
      vaults_number: { integer, maxLength: maxLength(2) },
      central_air_conditioning: { integer, maxLength: maxLength(2) },
      elevators_number: { integer, maxLength: maxLength(2) },
      electricity_meters_number: { integer, maxLength: maxLength(2) },
      water_meters_number: { integer, maxLength: maxLength(2) },
      gas_meters_number: { integer, maxLength: maxLength(2) },
      buying_price: { numeric, maxLength: maxLength(9) },
      buying_date: {},
      middleman_cost: { numeric, maxLength: maxLength(9) },
      registration_cost: { numeric, maxLength: maxLength(9) },
      building_currency_id: {},
      building_type_id: {},
      accrued_revenues_account_id: {},
      advance_revenues_account_id: {},
      revenues_account_id: {},
      discounts_account_id: {},
      cash_account_id: {},
      knet_account_id: {},
      policy_id: {},
      insurance_account_id: {},
      company_ownership: { required },
    },
    createPolicy: {
      building_id: { required },
      building_policy: {
        // required,
        policy_id: { required: true },
        year: { required: true },
        month: { required: true },
        amount: { numeric: true },
        percent: { numeric: true },
        percent_amount: { numeric: true },
        after_expenses: { required: true },
        plus_extra_revenues: { required: true },
        collected_rent_type: { required: true },
        company_pays_rest: { string: true },
        owner_pays_rest: { string: true },
      },
    },
  },
  computed: {
    selectedPolicyId() {
      return this.createPolicy.building_policy.policy_id;
    },
  },
  watch: {
    selectedPolicyId: function (newPolicyId) {
      this.showAmountInput = [1, 2, 4, 5].includes(newPolicyId);
      this.showPercentInput = [2, 3, 5].includes(newPolicyId);
      this.showPercentAmountInput = [5].includes(newPolicyId);

      if (newPolicyId === 1 || newPolicyId === 4) {
        this.createPolicy.building_policy.after_expenses = "no";
        this.createPolicy.building_policy.plus_extra_revenues = "no";
        this.createPolicy.building_policy.collected_rent_type = "accrued";
      } else {
        this.createPolicy.building_policy.after_expenses = "yes";
        this.createPolicy.building_policy.plus_extra_revenues = "yes";
        this.createPolicy.building_policy.collected_rent_type = "actual";
      }
      this.showCompanyPaysInput = [1].includes(newPolicyId);
      this.showOwnerPaysInput = [2, 4, 5].includes(newPolicyId);
    },
  },

  methods: {
    async getPolicies() {
      this.isLoader = true;
      await adminApi
        .get(`/real-estate/policy`)
        .then((res) => {
          this.isLoader = false;
          let l = res.data.data;
          if (this.isPermission("create policy RealState")) {
            l.unshift({ id: 0, name: "اضافة سياسة", name_e: "Add Policy" });
          }
          this.policies = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    async getCustomTableFields() {
      this.isCustom = true;
      await adminApi
        .get(`/customTable/table-columns/general_avenues`)
        .then((res) => {
          this.fields = res.data;
        })
        .catch((err) => {
          this.errorFun("Error", "Thereisanerrorinthesystem");
        })
        .finally(() => {
          this.isCustom = false;
        });
    },
    isVisible(fieldName) {
      if (!this.isPage) {
        let res = this.fields.filter((field) => {
          return field.column_name == fieldName;
        });
        return res.length > 0 && res[0].is_visible == 1 ? true : false;
      } else {
        return this.isVisiblePage(fieldName);
      }
    },
    isRequired(fieldName) {
      if (!this.isPage) {
        let res = this.fields.filter((field) => {
          return field.column_name == fieldName;
        });
        return res.length > 0 && res[0].is_required == 1 ? true : false;
      } else {
        return this.isRequiredPage(fieldName);
      }
    },
    getCurrentYear() {
      return new Date().getFullYear();
    },
    defaultData() {
      this.building_id = null;
      (this.building_policy_id = null), (this.createVideo = "");
      this.frameUrl = "";
      this.createPolicy = {
        building_policy: [
          {
            policy_id: null,
            year: null,
            month: null,
            amount: null,
            percent: null,
            percent_amount: null,
            after_expenses: "",
            plus_extra_revenues: "",
            collected_rent_type: "",
            company_pays_rest: "",
            owner_pays_rest: "",
          },
        ],
      };
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
        governorrate_id: null,
        street_id: null,
        lng: 0,
        lat: 0,
        floors_number: 0,
        mediums_number: 0,
        ground_floors_number: 0,
        vaults_number: 0,
        central_air_conditioning: 0,
        elevators_number: 0,
        electricity_meters_number: 0,
        water_meters_number: 0,
        gas_meters_number: 0,
        buying_price: 0,
        buying_date: "",
        middleman_cost: 0,
        registration_cost: 0,
        building_currency_id: null,
        building_type_id: null,
        accrued_revenues_account_id: null,
        advance_revenues_account_id: null,
        revenues_account_id: null,
        discounts_account_id: null,
        cash_account_id: null,
        knet_account_id: null,
        insurance_account_id: null,
        company_ownership: "",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.is_disabled = false;
    },
    resetModalHidden() {
      this.defaultData();
      this.managementPolicyTabDisable = true;
      this.$bvModal.hide(this.id);
    },
    showPolicyModal(index) {
      if (this.createPolicy.building_policy[index].policy_id == 0) {
        this.$bvModal.show("policy-create-building");
        this.createPolicy.building_policy[index].policy_id = null;
      }
    },
    resetModal() {
      this.defaultData();
      setTimeout(async () => {
        if (this.type != "edit") {
          if (!this.isPage) await this.getCustomTableFields();
          this.$nextTick(() => {
            this.$v.$reset();
          });
          this.getCategory();
          this.getCurrencies();
          this.getBuildingType();
          this.getAccounts();
          this.getPolicies();
          //   this.getStreets();
        } else {
          if (this.idObjEdit.dataObj) {
            let build = this.idObjEdit.dataObj;
            this.getPolicies();
            this.getAccounts();
            this.errors = {};
            if (this.isVisible("country_id")) this.getCategory();
            if (this.isVisible("city_id")) this.getCity(build.country?build.country.id:null);
            if (this.isVisible("street_id")) this.getStreets(build.avenue?build.avenue.id:null);
            if (this.isVisible("building_currency_id")) this.getCurrencies();
            // if (this.isVisible("accounts_number_id")) this.getAccounts();
            if (this.isVisible("building_type_id")) this.getBuildingType();
            this.building_id = build.id;
            this.createVideo = "";
            this.frameUrl = build.video_link;
            this.create.name = build.name;
            this.create.name_e = build.name_e;
            this.create.description = build.description;
            this.create.description_e = build.description_e;
            this.create.building_area = build.building_area ?? 0;
            this.create.land_area = build.land_area ?? 0;
            this.create.construction_year = build.construction_year ?? "";
            this.create.module = build.module;
            this.create.country_id = build.country?build.country.id:null;
            this.create.governorrate_id = build.governorate;
            if (this.isVisible("governorrate_id"))
              this.getGovernorates(build.country?build.country.id:null);
            this.create.city_id = build.city.id;
            this.create.avenue_id = build.avenue.id;
            if (this.isVisible("avenue_id")) this.getAvenue();
            if (build.street) {
              this.create.street_id = build.street.id;
            } else {
              this.create.street_id = null;
            }
            if (this.isVisible("street_id")) this.getStreets();
            this.create.lng = build.lng;
            this.create.lat = build.lat;
            this.create.floors_number = build.floors_number;
            this.create.company_ownership =
              build.company_ownership == 1 ? "Yes" : "No";
            if (build.company_ownership == 0) {
              this.managementPolicyTabDisable = false;
            }

            // this.create.company_ownership = build.company_ownership;
            this.create.mediums_number = build.mediums_number;
            this.create.ground_floors_number = build.ground_floors_number;
            this.create.vaults_number = build.vaults_number;
            this.create.central_air_conditioning =
              build.central_air_conditioning;
            this.create.elevators_number = build.elevators_number;
            this.create.electricity_meters_number =
              build.electricity_meters_number;
            this.create.water_meters_number = build.water_meters_number;
            this.create.gas_meters_number = build.gas_meters_number;
            this.create.buying_price = build.buying_price;
            this.create.buying_date = build.buying_date;
            this.create.middleman_cost = build.middleman_cost;
            this.create.registration_cost = build.registration_cost;
            this.create.building_currency_id = build.building_currency?build.building_currency.id:null;

            // building_type_id was giving error when it was null so i made this if statement
            if (build.building_type) {
              this.create.building_type_id = build.building_type.id;
            } else {
              this.create.building_type_id = null;
            }

            this.create.accrued_revenues_account_id =
                build.accrued_revenues_account?build.accrued_revenues_account.id:null;

            this.create.advance_revenues_account_id =
                build.advance_revenues_account?build.advance_revenues_account.id:null;

            this.create.revenues_account_id = build.revenues_account?build.revenues_account.id:null;

            this.create.discounts_account_id = build.discounts_account?build.discounts_account.id:null;

            this.create.cash_account_id = build.cash_account?build.cash_account.id:null;

            this.create.knet_account_id = build.knet_account_id?build.knet_account_id.id:null;

            this.create.insurance_account_id = build.insurance_account_id?build.insurance_account_id.id:null;

            // Make a GET request to fetch the building policy data
            adminApi
              .get(
                `/real-estate/building-policy/get-building-policies/${this.building_id}`
              )
              .then((response) => {
                // Check if data array is not empty
                if (response.data.data.length > 0) {
                  // Assign the id to building_policy_id
                  this.building_policy_id = response.data.data[0].id;
                  // Prepare the building_policy object
                  const building_policy = {
                    policy_id: response.data.data[0].policy.id,
                    year: response.data.data[0].year,
                    month: response.data.data[0].month,
                    amount: response.data.data[0].amount,
                    percent: response.data.data[0].percent,
                    percent_amount: response.data.data[0].percent_amount,
                    after_expenses: response.data.data[0].after_expenses,
                    plus_extra_revenues:
                      response.data.data[0].plus_extra_revenues,
                    collected_rent_type:
                      response.data.data[0].collected_rent_type,
                    company_pays_rest: response.data.data[0].company_pays_rest,
                    owner_pays_rest: response.data.data[0].owner_pays_rest,
                  };

                  // Assign the building_policy object to createPolicy.building_policy
                  this.createPolicy.building_policy = building_policy;
                  console.log(
                    "this.createPolicy.building_policy",
                    this.createPolicy.building_policy
                  );
                }
              })
              .catch((error) => {
                console.error("There was an error!", error);
              });

            this.b_images = build.media ?? [];
            if (this.b_images && this.b_images.length > 0) {
              this.b_showPhoto = this.b_images[this.b_images.length - 1].webp;
            } else {
              this.b_showPhoto = "../../../../../images/img-1.png";
            }
            this.errors = {};
          }
        }
      }, 50);
    },

    showCountryModal() {
      if (this.create.country_id == 0) {
        this.$bvModal.show("country-create");
        this.create.country_id = null;
      } else {
        this.getCity(this.create.country_id);
        this.getGovernorates(this.create.country_id);
      }
    },
    /**
     *  create countrie
     */
    async resetForm() {
      this.defaultData();
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
        this.is_disabled = false;

        if (this.type != "edit") {
          adminApi
            .post(this.url, {
              ...this.create,
              company_ownership: this.create.company_ownership == "Yes" ? 1 : 0,
              company_id: this.$store.getters["auth/company_id"],
            })
            .then((res) => {
              this.building_id = res.data.data.id;
              if (!this.isPage) this.$emit("created");
              else this.$emit("getDataTable");
              setTimeout(() => {
                this.successFun("Addedsuccessfully");
              }, 500);
              if (this.create.company_ownership === "No") {
                this.managementPolicyTabDisable = false;
                setTimeout(() => {
                  this.$refs.ManagementPolicy.activate();
                }, 1300);
              }
            })
            .catch((err) => {
              if (err.response.data) {
                this.errors = err.response.data.errors;
              } else {
                this.errorFun("Error", "Thereisanerrorinthesystem");
              }
            })
            .finally(() => {
              this.isLoader = false;
            });
        } else {
          adminApi
            .put(`${this.url}/${this.idObjEdit.idEdit}`, {
              ...this.create,
              company_ownership: this.create.company_ownership == "Yes" ? 1 : 0,

              company_id: this.$store.getters["auth/company_id"],
            })
            .then((res) => {
              // here i removed the hide to the modal cause we sill make another request for the policy
              // so zizo remember to add it in the AddSubmitPolicy
              //   this.$bvModal.hide(this.id);
              this.$emit("getDataTable");
              setTimeout(() => {
                this.successFun("Editsuccessfully");
              }, 500);
              if (this.create.company_ownership === "No") {
                this.managementPolicyTabDisable = false;
                setTimeout(() => {
                  this.$refs.ManagementPolicy.activate();
                }, 1300);
              }
            })
            .catch((err) => {
              if (err.response.data) {
                this.errors = err.response.data.errors;
              } else {
                this.errorFun("Error", "Thereisanerrorinthesystem");
              }
            })
            .finally(() => {
              this.isLoader = false;
            });
        }
      }
    },
    AddSubmitPolicy() {
      if (this.createPolicy.building_policy.amount === null) {
        delete this.create.building_policy.amount;
      }
      if (this.createPolicy.building_policy.percent === null) {
        delete this.createPolicy.building_policy.percent;
      }
      if (this.createPolicy.building_policy.percent_amount === null) {
        delete this.createPolicy.building_policy.percent_amount;
      }
      if (
        this.createPolicy.building_policy.company_pays_rest === null ||
        this.createPolicy.building_policy.company_pays_rest === ""
      ) {
        delete this.createPolicy.building_policy.company_pays_rest;
      }
      if (
        this.createPolicy.building_policy.owner_pays_rest === null ||
        this.createPolicy.building_policy.owner_pays_rest === ""
      ) {
        delete this.createPolicy.building_policy.owner_pays_rest;
      }

      if (this.$v.create.$invalid) {
        this.$v.create.$touch();
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;

        const buildingPolicyData = [
          {
            building_id: this.building_id,
            policy_id: this.createPolicy.building_policy.policy_id,
            year: this.createPolicy.building_policy.year,
            month: this.createPolicy.building_policy.month,
            amount: this.createPolicy.building_policy.amount,
            percent: this.createPolicy.building_policy.percent,
            percent_amount: this.createPolicy.building_policy.percent_amount,
            after_expenses: this.createPolicy.building_policy.after_expenses,
            plus_extra_revenues:
              this.createPolicy.building_policy.plus_extra_revenues,
            collected_rent_type:
              this.createPolicy.building_policy.collected_rent_type,
            company_pays_rest:
              this.createPolicy.building_policy.company_pays_rest,
            owner_pays_rest: this.createPolicy.building_policy.owner_pays_rest,
          },
        ];

        if (this.type != "edit") {
          adminApi
            .post(`/real-estate/building-policy`, {
              "building-policy": buildingPolicyData,
              company_id: this.$store.getters["auth/company_id"],
            })
            .then((res) => {
              this.is_disabled = true;
              if (!this.isPage) this.$emit("created");
              else this.$emit("getDataTable");

              setTimeout(() => {
                this.successFun("Addedsuccessfully");
              }, 500);
            })
            .catch((err) => {
              if (err.response.data) {
                this.errors = err.response.data.errors;
              } else {
                this.errorFun("Error", "Thereisanerrorinthesystem");
              }
            })
            .finally(() => {
              this.isLoader = false;
            });
        } else {
          console.log("this for edit", this.building_policy_id);
          adminApi
            .put(`${this.urlBuildingPolicy}/${this.building_policy_id}`, {
              "building-policy": buildingPolicyData,
              company_id: this.$store.getters["auth/company_id"],
            })
            .then((res) => {
              this.$bvModal.hide(this.id);
              this.$emit("getDataTable");
              setTimeout(() => {
                this.successFun("Editsuccessfully");
              }, 500);
            })
            .catch((err) => {
              if (err.response.data) {
                this.errors = err.response.data.errors;
              } else {
                this.errorFun("Error", "Thereisanerrorinthesystem");
              }
            })
            .finally(() => {
              this.isLoader = false;
            });
        }
      }
    },

    async getCategory() {
      this.create.city_id = null;
      this.create.avenue_id = null;
      this.create.governorrate_id = null;
      this.create.street_id = null;
      this.cities = [];
      this.avenues = [];
      this.governorates = [];
      this.streets = [];
      await adminApi
        .get(`/countries/get-drop-down?is_active=active`)
        .then((res) => {
          res.data.data.unshift({
            id: 0,
            name: "اضافة دولة",
            name_e: "Add Country",
          });
          this.countries = res.data.data;
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
      if (this.create.city_id == 0) {
        this.$bvModal.show("city-create");
        this.create.city_id = null;
      } else if (this.create.city_id) {
        this.create.avenue_id = null;
        this.create.street_id = null;
        let country = this.create.country_id;
        let city = this.create.city_id;
        await adminApi
          .get(`/avenues/get-drop-down?country_id=${country}&city_id=${city}`)
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
        this.create.avenue_id = null;
        this.avenues = [];
        await adminApi
          .get(`/cities/get-drop-down?country_id=${id}`)
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
      if (this.create.avenue_id == 0) {
        this.$bvModal.show("avenue-create");
        this.create.avenue_id = null;
      } else {
        this.getStreets();
      }
    },
    async getGovernorates() {
      if (this.create.governorrate_id == 0) {
        this.$bvModal.show("governorate-create");
      }
      setTimeout(() => {
        let country = this.create.country_id;
        adminApi
          .get(`/governorates/get-drop-down?country_id=${country}`)
          .then((res) => {
            let l = res.data.data;
            l.unshift({
              id: 0,
              name: "اضافة محافظه",
              name_e: "Add Governorate",
            });
            this.governorates = l;
          })
          .catch((err) => {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.Thereisanerrorinthesystem")}`,
            });
          });
      }, 500);
    },
    async showGovernorateModal(id = null, id2 = null) {
      if (this.create.governorrate_id == 0) {
        this.$bvModal.show("governorate-create");
        this.create.governorrate_id = null;
      }
    },
    async getStreets() {
      setTimeout(() => {
        let avenue = this.create.avenue_id;
        adminApi
          .get(`/streets/get-drop-down?avenue_id=${avenue}`)
          .then((res) => {
            let l = res.data.data;
            l.unshift({
              id: 0,
              name: "اضافة شارع",
              name_e: "Add Street",
            });
            this.streets = l;
          })
          .catch((err) => {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.Thereisanerrorinthesystem")}`,
            });
          });
      }, 500);
    },
    async showStreetModal(id = null, id2 = null) {
      if (this.create.street_id == 0) {
        this.$bvModal.show("street-create");
        this.create.street_id = null;
      }
    },
    getAvenue() {
      if (this.create.avenue_id == 0) {
        this.$bvModal.show("avenue-create");
        this.create.avenue_id = null;
      } else if (this.create.avenue_id) {
        this.create.street_id = null;
        let country = this.create.country_id;
        let city = this.create.city_id;
        adminApi
          .get(`/avenues/get-drop-down?country_id=${country}&city_id=${city}`)
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
        500;
      }
    },
    async getLocation() {
      if (navigator.geolocation) {
        return await navigator.geolocation.getCurrentPosition(
          this.showPosition
        );
      }
    },
    showPosition(position) {
      this.create.lat = position.coords.latitude;
      this.create.lng = position.coords.longitude;
    },
    arabicValue(txt) {
      this.create.name = arabicValue(txt);
    },
    arabicValueDescription(txt) {
      this.create.description = arabicValue(txt);
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
    },
    englishValueDescription(txt) {
      this.create.description_e = englishValue(txt);
    },
    /**
     *  image functions
     */
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
                .put(`/real-estate/buildings/${this.building_id}`, {
                  old_media,
                  media: new_media,
                })
                .then((res) => {
                  this.b_images = res.data.data.media ?? [];
                  if (this.b_images && this.b_images.length > 0) {
                    this.b_showPhoto =
                      this.b_images[this.b_images.length - 1].webp;
                  } else {
                    this.b_showPhoto = "../../../../../images/img-1.png";
                  }
                  this.$emit("getDataTable");
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
                    .put(`/real-estate/buildings/${this.building_id}`, {
                      old_media,
                      media: new_media,
                    })
                    .then((res) => {
                      this.b_images = res.data.data.media ?? [];
                      if (this.b_images && this.b_images.length > 0) {
                        this.b_showPhoto =
                          this.b_images[this.b_images.length - 1].webp;
                      } else {
                        this.b_showPhoto = "../../../../../images/img-1.png";
                      }
                      this.$emit("getDataTable");
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
    b_changePhoto() {
      document.getElementById("b_uploadImageCreate").click();
    },
    /**
     *  video functions
     */
    AddVideo(action) {
      let data = action == "create" ? this.create : this.edit;
      this.$v.createVideo.$touch();
      if (this.$v.createVideo.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        adminApi
          .put(`/real-estate/buildings/${this.building_id}`, {
            ...data,
            video_link: this.createVideo,
          })
          .then((res) => {
            this.$emit("getDataTable");
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
    async getCurrencies() {
      this.isLoader = true;
      await adminApi
        .get(`/currencies`)
        .then((res) => {
          let l = res.data.data;
          if (this.isPermission("create Currency")) {
            l.unshift({
              id: 0,
              name: "اضف عمله جديده",
              name_e: "Add New Currency",
            });
          }
          this.currencies = l;
          this.isLoader = false;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    async getBuildingType() {
      this.isLoader = true;
      await adminApi
        .get(`/real-estate/building-type`)
        .then((res) => {
          let l = res.data.data;
          if (this.isPermission("create Building Type")) {
            l.unshift({
              id: 0,
              name: "اضف نوع عقار جديد",
              name_e: "Add Building Type Currency",
            });
          }
          this.buildingTypes = l;
          this.isLoader = false;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    showCurrencyModal() {
      if (this.create.building_currency_id == 0) {
        this.$bvModal.show("currency-create");
        this.create.building_currency_id = null;
      }
    },
    showBuildingTypeModal() {
      if (this.create.building_type_id == 0) {
        this.$bvModal.show("buildingType-create");
        this.create.building_type_id = null;
      }
    },
    async getAccounts() {
      this.isLoader = true;
      await adminApi
        .get(`/general-accounts`)
        .then((res) => {
          let l = res.data.data;
          if (this.isPermission("create Accounts")) {
            l.unshift({
              id: 0,
              name: "اضف حساب جديده  ",
              name_e: "Add New Account",
            });
          }
          this.accounts = l;
          this.isLoader = false;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    showAccountModal() {
      if (this.create.accrued_revenues_account_id == 0) {
        this.$bvModal.show("Account-create");
        this.create.accrued_revenues_account_id = null;
      }
    },
    ShowAccountModal_advance() {
      if (this.create.advance_revenues_account_id == 0) {
        this.$bvModal.show("Account-create");
        this.create.advance_revenues_account_id = null;
      }
    },
    ShowAccountModal_revenues() {
      if (this.create.revenues_account_id == 0) {
        this.$bvModal.show("Account-create");
        this.create.revenues_account_id = null;
      }
    },

    ShowAccountModal_discounts() {
      if (this.create.discounts_account_id == 0) {
        this.$bvModal.show("Account-create");
        this.create.discounts_account_id = null;
      }
    },
    ShowAccountModal_cash() {
      if (this.create.cash_account_id == 0) {
        this.$bvModal.show("Account-create");
        this.create.cash_account_id = null;
      }
    },

    ShowAccountModal_knet() {
      if (this.create.knet_account_id == 0) {
        this.$bvModal.show("Account-create");
        this.create.knet_account_id = null;
      }
    },
    ShowAccountModal_insurance() {
      if (this.create.insurance_account_id == 0) {
        this.$bvModal.show("Account-create");
        this.create.insurance_account_id = null;
      }
    },

    goToPropertyComponentTab() {
      //   this.componentsTabDisabled = false;
      setTimeout(() => {
        this.$refs.propertyComponentsTab.activate();
      }, 1);
      //   this.financeTabDisabled = true;
      //   this.accountingTabDisabled = true;
    },

    goToFinancialDetailsTab() {
      //   this.financeTabDisabled = false;
      setTimeout(() => {
        this.$refs.FinancialDetailsTab.activate();
      }, 1);
      //   this.componentsTabDisabled = false;
      //   this.accountingTabDisabled = true;
    },

    goToAccountingDetailsTab() {
      //   this.accountingTabDisabled = false;
      setTimeout(() => {
        this.$refs.AccountingDetails.activate();
      }, 1);
      //   this.componentsTabDisabled = false;
      //   this.financeTabDisabled = false;
    },
    goToManagementPolicyTab() {
      //   if (this.create.company_ownership === "no") {
      //     this.managementPolicyTabDisable = false;
      //     setTimeout(() => {
      //     this.$refs.ManagementPolicy.activate();
      //   }, 900);
      //   }
      //   this.componentsTabDisabled = true;
      //   this.financeTabDisabled = true;
      //   this.accountingTabDisabled = true;
    },
  },
};
</script>
<template>
  <!--  create   -->
  <div>
    <Country
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getCategory"
      :isPage="false"
      :isPermission="isPermission"
      :id="'country-create'"
    />
    <City
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getCity(create.country_id)"
      :isPage="false"
      :isPermission="isPermission"
      :id="'city-create'"
    />
    <Avenue
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getAvenue"
      :isPage="false"
      :isPermission="isPermission"
      :id="'avenue-create'"
    />
    <Governorate
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getGovernorates(create.country_id)"
      :isPage="false"
      :isPermission="isPermission"
      :id="'governorate-create'"
    />
    <Street
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getStreets"
      :isPage="false"
      :isPermission="isPermission"
      :id="'street-create'"
    />
    <currency
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getCurrencies"
      :isPage="false"
      :isPermission="isPermission"
      :id="'currency-create'"
    />
    <buildingType
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getBuildingType"
      :isPage="false"
      :isPermission="isPermission"
      :id="'buildingType-create'"
    />
    <Accounts
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getAccounts"
      :isPage="false"
      :isPermission="isPermission"
      :id="'Account-create'"
    />

    <b-modal
      :id="id"
      :title="
        type != 'edit'
          ? getCompanyKey('building_create_form')
          : getCompanyKey('building_edit_form')
      "
      title-class="font-18"
      dialog-class="modal-full-width"
      body-class="p-4 "
      :hide-footer="true"
      @show="resetModal"
      @hidden="resetModalHidden"
    >
      <form>
        <loader size="large" v-if="isCustom && !isPage" />
        <div class="mb-3 d-flex justify-content-end">
          <b-button
            v-if="type != 'edit'"
            variant="success"
            :disabled="!is_disabled"
            @click.prevent="resetForm"
            type="button"
            :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
          >
            {{ $t("general.AddNewRecord") }}
          </b-button>

          <b-button
            @click.prevent="resetModalHidden"
            variant="danger"
            type="button"
          >
            {{ $t("general.Cancel") }}
          </b-button>
        </div>
        <b-tabs nav-class="nav-tabs nav-bordered">
          <b-tab :title="$t('general.DataEntry')" active>
            <div class="d-flex justify-content-end mt-4">
              <b-button
                variant="success"
                @click.prevent="goToPropertyComponentTab"
              >
                {{ $t("general.Add") }}
              </b-button>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ getCompanyKey("building_name_ar") }}
                    <span class="text-danger">*</span>
                  </label>
                  <div dir="rtl">
                    <input
                      @keyup="arabicValue(create.name)"
                      type="text"
                      class="form-control"
                      v-model="$v.create.name.$model"
                      :class="{
                        'is-invalid': $v.create.name.$error || errors.name,
                        'is-valid': !$v.create.name.$invalid && !errors.name,
                      }"
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
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ getCompanyKey("building_name_en") }}
                    <span class="text-danger">*</span>
                  </label>
                  <div dir="ltr">
                    <input
                      @keyup="englishValue(create.name_e)"
                      type="text"
                      class="form-control"
                      v-model="$v.create.name_e.$model"
                      :class="{
                        'is-invalid': $v.create.name_e.$error || errors.name_e,
                        'is-valid':
                          !$v.create.name_e.$invalid && !errors.name_e,
                      }"
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

              <div class="col-md-4">
                <div class="form-group position-relative">
                  <label class="control-label">
                    {{ $t("general.buildingType") }}
                    <span class="text-danger">*</span>
                  </label>
                  <multiselect
                    @input="showBuildingTypeModal"
                    v-model="$v.create.building_type_id.$model"
                    :options="buildingTypes.map((type) => type.id)"
                    :custom-label="
                      (opt) =>
                        buildingTypes.find((x) => x.id == opt)
                          ? buildingTypes.find((x) => x.id == opt).name
                          : ''
                    "
                  >
                  </multiselect>
                  <div
                    v-if="
                      $v.create.building_type_id.$error ||
                      errors.building_type_id
                    "
                    class="text-danger"
                  >
                    {{ $t("general.fieldIsRequired") }}
                  </div>
                  <template v-if="errors.building_type_id">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.building_type_id"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label class="mr-2">
                    {{ $t("general.company_ownership") }}
                    <span class="text-danger">*</span>
                  </label>
                  <b-form-group
                    :class="{
                      'is-invalid':
                        $v.create.company_ownership.$error ||
                        errors.company_ownership,
                      'is-valid':
                        !$v.create.company_ownership.$invalid &&
                        !errors.company_ownership,
                    }"
                  >
                    <b-form-radio
                      class="d-inline-block"
                      v-model="$v.create.company_ownership.$model"
                      name="some-radios"
                      value="Yes"
                      >{{ $t("general.Yes") }}</b-form-radio
                    >
                    <b-form-radio
                      class="d-inline-block m-1"
                      v-model="$v.create.company_ownership.$model"
                      name="some-radios"
                      value="No"
                      >{{ $t("general.No") }}</b-form-radio
                    >
                  </b-form-group>
                  <template v-if="errors.company_ownership">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.company_ownership"
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
              <div class="col-md-4">
                <div class="form-group">
                  <label class="mr-2">
                    {{ $t("general.construction_year") }}
                    <span class="text-danger">*</span>
                  </label>
                  <select
                    class="custom-select mr-sm-2"
                    v-model="$v.create.construction_year.$model"
                    :class="{
                      'is-invalid':
                        $v.create.construction_year.$error ||
                        errors.construction_year,
                      'is-valid':
                        !$v.create.construction_year.$invalid &&
                        !errors.construction_year,
                    }"
                  >
                    <option value="" selected>
                      {{ $t("general.Choose") }}...
                    </option>
                    <option
                      v-for="year in getCurrentYear()"
                      v-if="year >= 2000"
                      :value="year"
                    >
                      {{ year }}
                    </option>
                  </select>
                  <template v-if="errors.construction_year">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.construction_year"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ getCompanyKey("building_area") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    step="0.1"
                    v-model="$v.create.building_area.$model"
                    :class="{
                      'is-invalid':
                        $v.create.building_area.$error || errors.building_area,
                      'is-valid':
                        !$v.create.building_area.$invalid &&
                        !errors.building_area,
                    }"
                  />
                  <template v-if="errors.building_area">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.building_area"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ getCompanyKey("building_land_area") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    step="0.1"
                    v-model="$v.create.land_area.$model"
                    :class="{
                      'is-invalid':
                        $v.create.land_area.$error || errors.land_area,
                      'is-valid':
                        !$v.create.land_area.$invalid && !errors.land_area,
                    }"
                  />
                  <template v-if="errors.land_area">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.land_area"
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
              <div class="col-md-4">
                <div class="form-group position-relative">
                  <label class="control-label">
                    {{ getCompanyKey("country") }}
                    <span class="text-danger">*</span>
                  </label>
                  <multiselect
                    @input="showCountryModal"
                    v-model="$v.create.country_id.$model"
                    :options="countries.map((type) => type.id)"
                    :custom-label="
                      (opt) =>
                        countries.find((x) => x.id == opt)
                          ? countries.find((x) => x.id == opt).name
                          : ''
                    "
                  >
                  </multiselect>
                  <div
                    v-if="$v.create.country_id.$error || errors.country_id"
                    class="text-danger"
                  >
                    {{ $t("general.fieldIsRequired") }}
                  </div>
                  <template v-if="errors.country_id">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.country_id"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group position-relative">
                  <label class="control-label">
                    {{ getCompanyKey("governorate") }}
                    <span class="text-danger">*</span>
                  </label>
                  <multiselect
                    @input="showGovernorateModal"
                    v-model="$v.create.governorrate_id.$model"
                    :options="governorates.map((type) => type.id)"
                    :custom-label="
                      (opt) =>
                        governorates.find((x) => x.id == opt)
                          ? governorates.find((x) => x.id == opt).name
                          : ''
                    "
                  >
                  </multiselect>
                  <div
                    v-if="
                      $v.create.governorrate_id.$error || errors.governorrate_id
                    "
                    class="text-danger"
                  >
                    {{ $t("general.fieldIsRequired") }}
                  </div>
                  <template v-if="errors.governorrate_id">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.governorrate_id"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group position-relative">
                  <label class="control-label">
                    {{ getCompanyKey("city") }}
                    <span class="text-danger">*</span>
                  </label>
                  <multiselect
                    @input="getCity()"
                    @change="getAvenue"
                    v-model="$v.create.city_id.$model"
                    :options="cities.map((type) => type.id)"
                    :custom-label="
                      (opt) =>
                        cities.find((x) => x.id == opt)
                          ? cities.find((x) => x.id == opt).name
                          : ''
                    "
                  >
                  </multiselect>
                  <div
                    v-if="$v.create.city_id.$error || errors.city_id"
                    class="text-danger"
                  >
                    {{ $t("general.fieldIsRequired") }}
                  </div>
                  <template v-if="errors.city_id">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.city_id"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group position-relative">
                  <label class="control-label">
                    {{ getCompanyKey("avenue") }}
                    <span class="text-danger">*</span>
                  </label>
                  <multiselect
                    @input="showAvenueModal"
                    v-model="$v.create.avenue_id.$model"
                    :options="avenues.map((type) => type.id)"
                    :custom-label="
                      (opt) =>
                        avenues.find((x) => x.id == opt)
                          ? avenues.find((x) => x.id == opt).name
                          : ''
                    "
                  >
                  </multiselect>
                  <div
                    v-if="$v.create.avenue_id.$error || errors.avenue_id"
                    class="text-danger"
                  >
                    {{ $t("general.fieldIsRequired") }}
                  </div>
                  <template v-if="errors.city_id">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.avenue_id"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group position-relative">
                  <label class="control-label">
                    {{ getCompanyKey("street_name_en") }}
                    <span class="text-danger">*</span>
                  </label>
                  <multiselect
                    @input="showStreetModal"
                    v-model="$v.create.street_id.$model"
                    :options="streets.map((type) => type.id)"
                    :custom-label="
                      (opt) =>
                        streets.find((x) => x.id == opt)
                          ? streets.find((x) => x.id == opt).name
                          : ''
                    "
                  >
                  </multiselect>
                  <div
                    v-if="$v.create.street_id.$error || errors.street_id"
                    class="text-danger"
                  >
                    {{ $t("general.fieldIsRequired") }}
                  </div>
                  <template v-if="errors.street_id">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.street_id"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ getCompanyKey("building_longitude") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    step="0.00000000000001"
                    v-model="$v.create.lng.$model"
                    :class="{
                      'is-invalid': $v.create.lng.$error || errors.lng,
                      'is-valid': !$v.create.lng.$invalid && !errors.lng,
                    }"
                  />
                  <template v-if="errors.lng">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.lng"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ getCompanyKey("building_latitude") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    data-create="9"
                    step="0.00000000000001"
                    v-model="$v.create.lat.$model"
                    :class="{
                      'is-invalid': $v.create.lat.$error || errors.lat,
                      'is-valid': !$v.create.lat.$invalid && !errors.lat,
                    }"
                  />
                  <template v-if="errors.lat">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.lat"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>
              <div class="col-md-4 d-flex align-items-center mt-3">
                <button
                  class="btn btn-primary"
                  type="button"
                  @click="getLocation"
                >
                  {{ $t("general.location") }}
                </button>
              </div>
            </div>
            <hr
              style="
                margin: 10px 0 !important;
                border-top: 1px solid rgb(141 163 159 / 42%);
              "
            />
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="mr-2">
                    {{ getCompanyKey("building_description_ar") }}
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
                      v-for="(errorMessage, index) in errors.description"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="mr-2">
                    {{ getCompanyKey("building_description_en") }}
                    <span class="text-danger">*</span>
                  </label>
                  <textarea
                    @input="englishValueDescription(create.description_e)"
                    v-model="$v.create.description_e.$model"
                    class="form-control"
                    :maxlength="1000"
                    rows="5"
                  ></textarea>
                  <template v-if="errors.description_e">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.description_e"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>
            </div>
          </b-tab>
          <b-tab
            :title="$t('general.Property Components')"
            ref="propertyComponentsTab"
          >
            <div class="d-flex justify-content-end mt-4">
              <b-button
                variant="success"
                @click.prevent="goToFinancialDetailsTab"
              >
                {{ $t("general.Add") }}
              </b-button>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ $t("general.floors_number") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    v-model="$v.create.floors_number.$model"
                    :class="{
                      'is-invalid':
                        $v.create.floors_number.$error || errors.floors_number,
                      'is-valid':
                        !$v.create.floors_number.$invalid &&
                        !errors.floors_number,
                    }"
                  />
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ $t("general.mediums_number") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    v-model="$v.create.mediums_number.$model"
                    :class="{
                      'is-invalid':
                        $v.create.mediums_number.$error ||
                        errors.mediums_number,
                      'is-valid':
                        !$v.create.mediums_number.$invalid &&
                        !errors.mediums_number,
                    }"
                  />
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ $t("general.ground_floors_number") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    v-model="$v.create.ground_floors_number.$model"
                    :class="{
                      'is-invalid':
                        $v.create.ground_floors_number.$error ||
                        errors.ground_floors_number,
                      'is-valid':
                        !$v.create.ground_floors_number.$invalid &&
                        !errors.ground_floors_number,
                    }"
                  />
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
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ $t("general.vaults_number") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    v-model="$v.create.vaults_number.$model"
                    :class="{
                      'is-invalid':
                        $v.create.vaults_number.$error || errors.vaults_number,
                      'is-valid':
                        !$v.create.vaults_number.$invalid &&
                        !errors.vaults_number,
                    }"
                  />
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ $t("general.central_air_conditioning") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    v-model="$v.create.central_air_conditioning.$model"
                    :class="{
                      'is-invalid':
                        $v.create.central_air_conditioning.$error ||
                        errors.central_air_conditioning,
                      'is-valid':
                        !$v.create.central_air_conditioning.$invalid &&
                        !errors.central_air_conditioning,
                    }"
                  />
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ $t("general.elevators_number") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    v-model="$v.create.elevators_number.$model"
                    :class="{
                      'is-invalid':
                        $v.create.elevators_number.$error ||
                        errors.elevators_number,
                      'is-valid':
                        !$v.create.elevators_number.$invalid &&
                        !errors.elevators_number,
                    }"
                  />
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
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ $t("general.electricity_meters_number") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    v-model="$v.create.electricity_meters_number.$model"
                    :class="{
                      'is-invalid':
                        $v.create.electricity_meters_number.$error ||
                        errors.electricity_meters_number,
                      'is-valid':
                        !$v.create.electricity_meters_number.$invalid &&
                        !errors.electricity_meters_number,
                    }"
                  />
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ $t("general.water_meters_number") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    v-model="$v.create.water_meters_number.$model"
                    :class="{
                      'is-invalid':
                        $v.create.water_meters_number.$error ||
                        errors.water_meters_number,
                      'is-valid':
                        !$v.create.water_meters_number.$invalid &&
                        !errors.water_meters_number,
                    }"
                  />
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ $t("general.gas_meters_number") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    v-model="$v.create.gas_meters_number.$model"
                    :class="{
                      'is-invalid':
                        $v.create.gas_meters_number.$error ||
                        errors.gas_meters_number,
                      'is-valid':
                        !$v.create.gas_meters_number.$invalid &&
                        !errors.gas_meters_number,
                    }"
                  />
                </div>
              </div>
            </div>
          </b-tab>
          <b-tab :title="$t('Financials Details')" ref="FinancialDetailsTab">
            <div class="d-flex justify-content-end mt-4">
              <b-button
                variant="success"
                @click.prevent="goToAccountingDetailsTab"
              >
                {{ $t("general.Add") }}
              </b-button>
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ $t("general.buying_price") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    v-model="$v.create.buying_price.$model"
                    :class="{
                      'is-invalid':
                        $v.create.buying_price.$error || errors.buying_price,
                      'is-valid':
                        !$v.create.buying_price.$invalid &&
                        !errors.buying_price,
                    }"
                  />
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ $t("general.buying_date") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="date"
                    class="form-control"
                    v-model="$v.create.buying_date.$model"
                    :class="{
                      'is-invalid':
                        $v.create.buying_date.$error || errors.buying_date,
                      'is-valid':
                        !$v.create.buying_date.$invalid && !errors.buying_date,
                    }"
                  />
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ $t("general.middleman_cost") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    v-model="$v.create.middleman_cost.$model"
                    :class="{
                      'is-invalid':
                        $v.create.middleman_cost.$error ||
                        errors.middleman_cost,
                      'is-valid':
                        !$v.create.middleman_cost.$invalid &&
                        !errors.middleman_cost,
                    }"
                  />
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">
                    {{ $t("general.registration_cost") }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    v-model="$v.create.registration_cost.$model"
                    :class="{
                      'is-invalid':
                        $v.create.registration_cost.$error ||
                        errors.registration_cost,
                      'is-valid':
                        !$v.create.registration_cost.$invalid &&
                        !errors.registration_cost,
                    }"
                  />
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group position-relative">
                  <label class="control-label">
                    {{ $t("general.building_currency") }}
                    <span class="text-danger">*</span>
                  </label>
                  <multiselect
                    @input="showCurrencyModal"
                    v-model="$v.create.building_currency_id.$model"
                    :options="currencies.map((type) => type.id)"
                    :custom-label="
                      (opt) =>
                        currencies.find((x) => x.id == opt)
                          ? currencies.find((x) => x.id == opt).name
                          : ''
                    "
                  >
                  </multiselect>
                  <div
                    v-if="
                      $v.create.building_currency_id.$error ||
                      errors.building_currency_id
                    "
                    class="text-danger"
                  >
                    {{ $t("general.fieldIsRequired") }}
                  </div>
                  <template v-if="errors.building_currency_id">
                    <ErrorMessage
                      v-for="(
                        errorMessage, index
                      ) in errors.building_currency_id"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>
            </div>
          </b-tab>
          <b-tab
            :title="$t('general.Accounting Details')"
            ref="AccountingDetails"
          >
            <div class="d-flex justify-content-end mt-4">
              <template v-if="!is_disabled">
                <b-button
                  variant="success"
                  type="submit"
                  class="mx-1"
                  v-if="!isLoader"
                  @click.prevent="AddSubmit"
                >
                  {{ type != "edit" ? $t("general.Add") : $t("general.edit") }}
                </b-button>
                <b-button variant="success" class="mx-1" disabled v-else>
                  <b-spinner small></b-spinner>
                  <span class="sr-only">{{ $t("login.Loading") }}...</span>
                </b-button>
              </template>
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="form-group position-relative">
                  <label class="control-label">
                    {{ $t("general.accrued_revenues_account") }}
                    <span class="text-danger">*</span>
                  </label>
                  <multiselect
                    @input="showAccountModal()"
                    v-model="$v.create.accrued_revenues_account_id.$model"
                    :options="accounts.map((type) => type.id)"
                    :custom-label="
                      (opt) =>
                        accounts.find((x) => x.id == opt)
                          ? accounts.find((x) => x.id == opt).name
                          : ''
                    "
                  >
                  </multiselect>
                  <div
                    v-if="
                      $v.create.accrued_revenues_account_id.$error ||
                      errors.accrued_revenues_account_id
                    "
                    class="text-danger"
                  >
                    {{ $t("general.fieldIsRequired") }}
                  </div>
                  <template v-if="errors.accrued_revenues_account_id">
                    <ErrorMessage
                      v-for="(
                        errorMessage, index
                      ) in errors.accrued_revenues_account_id"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group position-relative">
                  <label class="control-label">
                    {{ $t("general.advance_revenues_account") }}
                    <span class="text-danger">*</span>
                  </label>
                  <multiselect
                    @input="ShowAccountModal_advance()"
                    v-model="$v.create.advance_revenues_account_id.$model"
                    :options="accounts.map((type) => type.id)"
                    :custom-label="
                      (opt) =>
                        accounts.find((x) => x.id == opt)
                          ? accounts.find((x) => x.id == opt).name
                          : ''
                    "
                  >
                  </multiselect>
                  <div
                    v-if="
                      $v.create.advance_revenues_account_id.$error ||
                      errors.advance_revenues_account_id
                    "
                    class="text-danger"
                  >
                    {{ $t("general.fieldIsRequired") }}
                  </div>
                  <template v-if="errors.advance_revenues_account_id">
                    <ErrorMessage
                      v-for="(
                        errorMessage, index
                      ) in errors.advance_revenues_account_id"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group position-relative">
                  <label class="control-label">
                    {{ $t("general.revenues_account") }}
                    <span class="text-danger">*</span>
                  </label>
                  <multiselect
                    @input="ShowAccountModal_revenues"
                    v-model="$v.create.revenues_account_id.$model"
                    :options="accounts.map((type) => type.id)"
                    :custom-label="
                      (opt) =>
                        accounts.find((x) => x.id == opt)
                          ? accounts.find((x) => x.id == opt).name
                          : ''
                    "
                  >
                  </multiselect>
                  <div
                    v-if="
                      $v.create.revenues_account_id.$error ||
                      errors.revenues_account_id
                    "
                    class="text-danger"
                  >
                    {{ $t("general.fieldIsRequired") }}
                  </div>
                  <template v-if="errors.revenues_account_id">
                    <ErrorMessage
                      v-for="(
                        errorMessage, index
                      ) in errors.revenues_account_id"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group position-relative">
                  <label class="control-label">
                    {{ $t("general.discounts_account") }}
                    <span class="text-danger">*</span>
                  </label>
                  <multiselect
                    @input="ShowAccountModal_discounts"
                    v-model="$v.create.discounts_account_id.$model"
                    :options="accounts.map((type) => type.id)"
                    :custom-label="
                      (opt) =>
                        accounts.find((x) => x.id == opt)
                          ? accounts.find((x) => x.id == opt).name
                          : ''
                    "
                  >
                  </multiselect>
                  <div
                    v-if="
                      $v.create.discounts_account_id.$error ||
                      errors.discounts_account_id
                    "
                    class="text-danger"
                  >
                    {{ $t("general.fieldIsRequired") }}
                  </div>
                  <template v-if="errors.discounts_account_id">
                    <ErrorMessage
                      v-for="(
                        errorMessage, index
                      ) in errors.discounts_account_id"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group position-relative">
                  <label class="control-label">
                    {{ $t("general.cash_account_number") }}
                    <span class="text-danger">*</span>
                  </label>
                  <multiselect
                    @input="ShowAccountModal_cash"
                    v-model="$v.create.cash_account_id.$model"
                    :options="accounts.map((type) => type.id)"
                    :custom-label="
                      (opt) =>
                        accounts.find((x) => x.id == opt)
                          ? accounts.find((x) => x.id == opt).name
                          : ''
                    "
                  >
                  </multiselect>
                  <div
                    v-if="
                      $v.create.cash_account_id.$error || errors.cash_account_id
                    "
                    class="text-danger"
                  >
                    {{ $t("general.fieldIsRequired") }}
                  </div>
                  <template v-if="errors.cash_account_id">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.cash_account_id"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group position-relative">
                  <label class="control-label">
                    {{ $t("general.knet_account_number") }}
                    <span class="text-danger">*</span>
                  </label>
                  <multiselect
                    @input="ShowAccountModal_knet"
                    v-model="$v.create.knet_account_id.$model"
                    :options="accounts.map((type) => type.id)"
                    :custom-label="
                      (opt) =>
                        accounts.find((x) => x.id == opt)
                          ? accounts.find((x) => x.id == opt).name
                          : ''
                    "
                  >
                  </multiselect>
                  <div
                    v-if="
                      $v.create.knet_account_id.$error || errors.knet_account_id
                    "
                    class="text-danger"
                  >
                    {{ $t("general.fieldIsRequired") }}
                  </div>
                  <template v-if="errors.knet_account_id">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.knet_account_id"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group position-relative">
                  <label class="control-label">
                    {{ $t("general.insurance_account_number") }}
                    <span class="text-danger">*</span>
                  </label>
                  <multiselect
                    @input="ShowAccountModal_insurance"
                    v-model="$v.create.insurance_account_id.$model"
                    :options="accounts.map((type) => type.id)"
                    :custom-label="
                      (opt) =>
                        accounts.find((x) => x.id == opt)
                          ? accounts.find((x) => x.id == opt).name
                          : ''
                    "
                  >
                  </multiselect>
                  <div
                    v-if="
                      $v.create.insurance_account_id.$error ||
                      errors.insurance_account_id
                    "
                    class="text-danger"
                  >
                    {{ $t("general.fieldIsRequired") }}
                  </div>
                  <template v-if="errors.insurance_account_id">
                    <ErrorMessage
                      v-for="(
                        errorMessage, index
                      ) in errors.insurance_account_id"
                      :key="index"
                      >{{ errorMessage }}</ErrorMessage
                    >
                  </template>
                </div>
              </div>
            </div>
          </b-tab>

          <b-tab
            :disabled="managementPolicyTabDisable"
            :title="$t('general.Management Policy')"
            ref="ManagementPolicy"
          >
            <form>
              <loader size="large" v-if="isCustom && !isPage" />

              <div class="d-flex justify-content-end mt-4">
                <template v-if="!is_disabled">
                  <b-button
                    variant="success"
                    type="submit"
                    class="mx-1"
                    v-if="!isLoader"
                    @click.prevent="AddSubmitPolicy"
                  >
                    {{
                      type != "edit" ? $t("general.Add") : $t("general.edit")
                    }}
                  </b-button>
                  <b-button variant="success" class="mx-1" disabled v-else>
                    <b-spinner small></b-spinner>
                    <span class="sr-only">{{ $t("login.Loading") }}...</span>
                  </b-button>
                </template>
              </div>

              <div class="row">
                <div class="col-md-12 position-relative">
                  <div class="form-group">
                    <label class="my-1 mr-2">{{ $t("general.policy") }}</label>
                    <multiselect
                      v-model="createPolicy.building_policy.policy_id"
                      :options="policies.map((type) => type.id)"
                      :custom-label="
                        (opt) =>
                          policies.find((x) => x.id == opt)
                            ? $i18n.locale == 'ar'
                              ? policies.find((x) => x.id == opt).name
                              : policies.find((x) => x.id == opt).name_e
                            : ''
                      "
                      :class="{
                        'is-invalid':
                          $v.create.policy_id.$error || errors.policy_id,
                      }"
                    >
                    </multiselect>
                    <div
                      v-if="!$v.create.policy_id.required"
                      class="invalid-feedback"
                    >
                      {{ $t("general.fieldIsRequired") }}
                    </div>
                    <template v-if="errors.policy_id">
                      <ErrorMessage
                        v-for="(errorMessage, index) in errors.policy_id"
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
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">
                      {{ $t("general.Year") }}
                      <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <input
                        type="number"
                        class="form-control"
                        v-model="$v.createPolicy.building_policy.year.$model"
                        :class="{
                          'is-invalid':
                            $v.createPolicy.building_policy.year.$error ||
                            errors.year,
                          'is-valid':
                            !$v.createPolicy.building_policy.year.$invalid &&
                            !errors.year,
                        }"
                        placeholder="YYYY"
                        data-provide="datepicker"
                        data-date-format="yyyy"
                        data-date-start-view="years"
                        data-date-min-view-mode="years"
                        data-date-autoclose="true"
                        data-date-min-view="years"
                      />
                      <div class="input-group-append">
                        <span class="input-group-text">
                          <i class="far fa-calendar"></i>
                        </span>
                      </div>
                    </div>
                    <template v-if="errors.year">
                      <ErrorMessage
                        v-for="(errorMessage, index) in errors.year"
                        :key="index"
                        >{{ errorMessage }}</ErrorMessage
                      >
                    </template>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">
                      {{ $t("general.month") }}
                      <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <input
                        type="number"
                        class="form-control"
                        v-model="$v.createPolicy.building_policy.month.$model"
                        :class="{
                          'is-invalid':
                            $v.createPolicy.building_policy.month.$error ||
                            errors.month,
                          'is-valid':
                            !$v.createPolicy.building_policy.month.$invalid &&
                            !errors.month,
                        }"
                        placeholder="MM"
                        data-provide="datepicker"
                        data-date-format="mm"
                        data-date-start-view="months"
                        data-date-min-view-mode="months"
                        data-date-autoclose="true"
                        data-date-min-view="months"
                      />
                      <div class="input-group-append">
                        <span class="input-group-text">
                          <i class="far fa-calendar"></i>
                        </span>
                      </div>
                    </div>
                    <template v-if="errors.month">
                      <ErrorMessage
                        v-for="(errorMessage, index) in errors.month"
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
                <div class="col-md-4" v-if="showAmountInput">
                  <div class="form-group">
                    <label class="control-label">
                      {{ $t("general.amount") }}
                      <span class="text-danger">*</span>
                    </label>
                    <input
                      type="number"
                      class="form-control"
                      step="0.1"
                      v-model="$v.createPolicy.building_policy.amount.$model"
                      :class="{
                        'is-invalid':
                          $v.createPolicy.building_policy.amount.$error ||
                          errors.amount,
                        'is-valid':
                          !$v.createPolicy.building_policy.amount.$invalid &&
                          !errors.amount,
                      }"
                    />
                    <template v-if="errors.amount">
                      <ErrorMessage
                        v-for="(errorMessage, index) in errors.amount"
                        :key="index"
                        >{{ errorMessage }}</ErrorMessage
                      >
                    </template>
                  </div>
                </div>

                <div class="col-md-4" v-if="showPercentInput">
                  <div class="form-group">
                    <label class="control-label">
                      {{ $t("general.percent") }}
                      <span class="text-danger">*</span>
                    </label>
                    <input
                      type="number"
                      class="form-control"
                      step="0.1"
                      v-model="$v.createPolicy.building_policy.percent.$model"
                      :class="{
                        'is-invalid':
                          $v.createPolicy.building_policy.percent.$error ||
                          errors.percent,
                        'is-valid':
                          !$v.createPolicy.building_policy.percent.$invalid &&
                          !errors.percent,
                      }"
                    />
                    <template v-if="errors.percent">
                      <ErrorMessage
                        v-for="(errorMessage, index) in errors.percent"
                        :key="index"
                        >{{ errorMessage }}</ErrorMessage
                      >
                    </template>
                  </div>
                </div>

                <div class="col-md-4" v-if="showPercentAmountInput">
                  <div class="form-group">
                    <label class="control-label">
                      {{ $t("general.percent_amount") }}
                      <span class="text-danger">*</span>
                    </label>
                    <input
                      type="number"
                      class="form-control"
                      step="0.1"
                      v-model="
                        $v.createPolicy.building_policy.percent_amount.$model
                      "
                      :class="{
                        'is-invalid':
                          $v.createPolicy.building_policy.percent_amount
                            .$error || errors.percent_amount,
                        'is-valid':
                          !$v.createPolicy.building_policy.percent_amount
                            .$invalid && !errors.percent_amount,
                      }"
                    />
                    <template v-if="errors.percent_amount">
                      <ErrorMessage
                        v-for="(errorMessage, index) in errors.percent_amount"
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
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">
                      {{ $t("general.after_expenses") }}
                      <span class="text-danger">*</span>
                    </label>
                    <b-form-group
                      :class="{
                        'is-invalid':
                          $v.createPolicy.building_policy.after_expenses
                            .$error || errors.after_expenses,
                        'is-valid':
                          !$v.createPolicy.building_policy.after_expenses
                            .$invalid && !errors.after_expenses,
                      }"
                    >
                      <b-form-radio
                        class="d-inline-block"
                        v-model="
                          $v.createPolicy.building_policy.after_expenses.$model
                        "
                        name="after_expenses"
                        value="yes"
                        >{{ $t("general.Yes") }}</b-form-radio
                      >
                      <b-form-radio
                        class="d-inline-block m-1"
                        v-model="
                          $v.createPolicy.building_policy.after_expenses.$model
                        "
                        name="after_expenses"
                        value="no"
                        >{{ $t("general.No") }}</b-form-radio
                      >
                    </b-form-group>
                    <template v-if="errors.after_expenses">
                      <ErrorMessage
                        v-for="(errorMessage, index) in errors.after_expenses"
                        :key="index"
                        >{{ errorMessage }}</ErrorMessage
                      >
                    </template>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">
                      {{ $t("general.plus_extra_revenues") }}
                      <span class="text-danger">*</span>
                    </label>
                    <b-form-group
                      :class="{
                        'is-invalid':
                          $v.createPolicy.building_policy.plus_extra_revenues
                            .$error || errors.plus_extra_revenues,
                        'is-valid':
                          !$v.createPolicy.building_policy.plus_extra_revenues
                            .$invalid && !errors.plus_extra_revenues,
                      }"
                    >
                      <b-form-radio
                        class="d-inline-block"
                        v-model="
                          $v.createPolicy.building_policy.plus_extra_revenues
                            .$model
                        "
                        name="plus_extra_revenues"
                        value="yes"
                        >{{ $t("general.Yes") }}</b-form-radio
                      >
                      <b-form-radio
                        class="d-inline-block m-1"
                        v-model="
                          $v.createPolicy.building_policy.plus_extra_revenues
                            .$model
                        "
                        name="plus_extra_revenues"
                        value="no"
                        >{{ $t("general.No") }}</b-form-radio
                      >
                    </b-form-group>
                    <template v-if="errors.plus_extra_revenues">
                      <ErrorMessage
                        v-for="(
                          errorMessage, index
                        ) in errors.plus_extra_revenues"
                        :key="index"
                        >{{ errorMessage }}</ErrorMessage
                      >
                    </template>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">
                      {{ $t("general.collected_rent_type") }}
                      <span class="text-danger">*</span>
                    </label>
                    <b-form-group
                      :class="{
                        'is-invalid':
                          $v.createPolicy.building_policy.collected_rent_type
                            .$error || errors.collected_rent_type,
                        'is-valid':
                          !$v.createPolicy.building_policy.collected_rent_type
                            .$invalid && !errors.collected_rent_type,
                      }"
                    >
                      <b-form-radio
                        class="d-inline-block"
                        v-model="
                          $v.createPolicy.building_policy.collected_rent_type
                            .$model
                        "
                        name="collected_rent_type"
                        value="actual"
                        >{{ $t("general.Actual") }}</b-form-radio
                      >
                      <b-form-radio
                        class="d-inline-block m-1"
                        v-model="
                          $v.createPolicy.building_policy.collected_rent_type
                            .$model
                        "
                        name="collected_rent_type"
                        value="accrued"
                        >{{ $t("general.Accrued") }}</b-form-radio
                      >
                    </b-form-group>
                    <template v-if="errors.collected_rent_type">
                      <ErrorMessage
                        v-for="(
                          errorMessage, index
                        ) in errors.collected_rent_type"
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
                <div class="col-md-5" v-if="showCompanyPaysInput">
                  <div class="form-group">
                    <label class="control-label">
                      {{ $t("general.company_pays_rest") }}
                      <span class="text-danger">*</span>
                    </label>
                    <b-form-group
                      :class="{
                        'is-invalid':
                          $v.createPolicy.building_policy.company_pays_rest
                            .$error || errors.company_pays_rest,
                        'is-valid':
                          !$v.createPolicy.building_policy.company_pays_rest
                            .$invalid && !errors.company_pays_rest,
                      }"
                    >
                      <b-form-radio
                        class="d-inline-block"
                        v-model="
                          $v.createPolicy.building_policy.company_pays_rest
                            .$model
                        "
                        name="company_pays_rest"
                        value="yes"
                        >{{ $t("general.Yes") }}</b-form-radio
                      >
                      <b-form-radio
                        class="d-inline-block m-1"
                        v-model="
                          $v.createPolicy.building_policy.company_pays_rest
                            .$model
                        "
                        name="company_pays_rest"
                        value="no"
                        >{{ $t("general.No") }}</b-form-radio
                      >
                    </b-form-group>
                    <template v-if="errors.company_pays_rest">
                      <ErrorMessage
                        v-for="(
                          errorMessage, index
                        ) in errors.company_pays_rest"
                        :key="index"
                        >{{ errorMessage }}</ErrorMessage
                      >
                    </template>
                  </div>
                </div>

                <div class="col-md-5" v-if="showOwnerPaysInput">
                  <div class="form-group">
                    <label class="control-label">
                      {{ $t("general.owner_pays_rest") }}
                      <span class="text-danger">*</span>
                    </label>
                    <b-form-group
                      :class="{
                        'is-invalid':
                          $v.createPolicy.building_policy.owner_pays_rest
                            .$error || errors.owner_pays_rest,
                        'is-valid':
                          !$v.createPolicy.building_policy.owner_pays_rest
                            .$invalid && !errors.owner_pays_rest,
                      }"
                    >
                      <b-form-radio
                        class="d-inline-block"
                        v-model="
                          $v.createPolicy.building_policy.owner_pays_rest.$model
                        "
                        name="owner_pays_rest"
                        value="yes"
                        >{{ $t("general.Yes") }}</b-form-radio
                      >
                      <b-form-radio
                        class="d-inline-block m-1"
                        v-model="
                          $v.createPolicy.building_policy.owner_pays_rest.$model
                        "
                        name="owner_pays_rest"
                        value="no"
                        >{{ $t("general.No") }}</b-form-radio
                      >
                    </b-form-group>
                    <template v-if="errors.owner_pays_rest">
                      <ErrorMessage
                        v-for="(errorMessage, index) in errors.owner_pays_rest"
                        :key="index"
                        >{{ errorMessage }}</ErrorMessage
                      >
                    </template>
                  </div>
                </div>
              </div>
            </form>
          </b-tab>

          <b-tab :disabled="!building_id" :title="$t('general.ImageUploads')">
            <div class="row">
              <input
                accept="image/png, image/gif, image/jpeg, image/jpg"
                type="file"
                id="b_uploadImageCreate"
                @change.prevent="b_onImageChanged"
                class="input-file-upload position-absolute"
                :class="[
                  'd-none',
                  {
                    'is-invalid': $v.create.media.$error || errors.media,
                    'is-valid': !$v.create.media.$invalid && !errors.media,
                  },
                ]"
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
                        v-for="(photo, index) in b_images"
                        :key="photo.id"
                      >
                        <div
                          :class="[
                            'card mb-0 shadow-none border',
                            b_images.length - 1 == index ? 'bg-primary' : '',
                          ]"
                        >
                          <div class="p-2">
                            <div class="row align-items-center">
                              <div
                                class="col-auto"
                                @click="b_showPhoto = photo.webp"
                              >
                                <img
                                  data-dz-thumbnail
                                  :src="photo.webp"
                                  class="avatar-sm rounded bg-light"
                                  @error="
                                    src = '../../../../../images/img-1.png'
                                  "
                                />
                              </div>
                              <div class="col pl-0">
                                <a
                                  href="javascript:void(0);"
                                  :class="[
                                    'font-weight-bold',
                                    b_images.length - 1 == index
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
                      @click="b_changePhoto"
                      variant="success"
                      type="button"
                      class="mx-1 font-weight-bold px-3"
                      v-if="!isLoader"
                    >
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
                  <img
                    :src="b_showPhoto"
                    class="img-thumbnail"
                    @error="src = '../../../../../images/img-1.png'"
                  />
                </div>
              </div>
            </div>
          </b-tab>
          <b-tab :disabled="!building_id" :title="$t('general.video')">
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
              <div v-html="frameUrl" v-if="frameUrl" class="col-md-12"></div>
            </div>
          </b-tab>
        </b-tabs>
      </form>
    </b-modal>
  </div>
  <!--  /create   -->
</template>
<style scoped>
.dropdown-menu-custom-company.dropdown .dropdown-menu {
  padding: 5px 10px !important;
  overflow-y: scroll;
  height: 300px;
}
</style>
