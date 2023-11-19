<template>
  <Layout>
    <PageHeader />

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="form-group col-md-6">
              <label for="selectId">Select an ID</label>
              <select v-model="selectedId" class="form-control" id="selectId">
                <option value="" disabled>Select an ID</option>
                <option v-for="id in ids" :value="id">
                  {{ id }}
                </option>
              </select>
            </div>

            <div v-if="selectedId">
              <!-- Display the collected_rent_type input field -->
              <div
                class="form-group col-md-6"
                v-if="collectedRentType === 'Accrued'"
              >
                <label for="accruedRevenues">Accrued Revenues</label>
                <input
                  v-model="accruedRevenues"
                  type="number"
                  class="form-control"
                  id="accruedRevenues"
                  placeholder="Accrued Revenues"
                />
              </div>
              <div class="form-group col-md-6" v-else>
                <label for="actualRevenues">Actual Revenues</label>
                <input
                  v-model="actualRevenues"
                  type="number"
                  class="form-control"
                  id="actualRevenues"
                  placeholder="Actual Revenues"
                />
              </div>

              <!-- Display the after_expenses input field -->
              <div class="form-group col-md-6" v-if="afterExpenses === 1">
                <label for="expensesAmount">Expenses Amount</label>
                <input
                  v-model="expensesAmount"
                  type="number"
                  class="form-control"
                  id="expensesAmount"
                  placeholder="Expenses Amount"
                />
              </div>

              <!-- Display the plus_extra_revenues input field -->
              <div
                class="form-group col-md-6"
                v-if="plusExtraRevenues === 'yes'"
              >
                <label for="extraRevenues">Extra Revenues</label>
                <input
                  v-model="extraRevenues"
                  type="number"
                  class="form-control"
                  id="extraRevenues"
                  placeholder="Extra Revenues"
                />
              </div>
            </div>

            <button @click="addData" class="btn btn-primary">Calculate</button>

            <div class="mt-4">
              <h4>Results</h4>
              <div class="row">
                <div class="col-4">
                  <p><strong>Company Takes:</strong></p>
                  <p class="display-value">{{ company_takes }}</p>
                </div>
                <div class="col-4">
                  <p><strong>Owner Takes:</strong></p>
                  <p class="display-value">{{ owner_takes }}</p>
                </div>
                <div class="col-4">
                  <p><strong>Company Pays:</strong></p>
                  <p class="display-value">{{ company_pays }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import permissionGuard from "../../../../helper/permission";
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import loader from "../../../../components/general/loader";
import translation from "../../../../helper/mixin/translation-mixin";
import customTable from "../../../../helper/mixin/customTable";
import successError from "../../../../helper/mixin/success&error";
import crudHelper from "../../../../helper/mixin/crudHelper";
import searchPage from "../../../../components/general/searchPage";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";
import adminApi from "../../../../api/adminAxios";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "test",
    meta: [{ name: "test", content: "test" }],
  },
  mixins: [translation, customTable, successError, crudHelper],
  components: {
    Layout,
    PageHeader,
    loader,
    searchPage,
    actionSetting,
    tableCustom,
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "test", "all test");
    });
  },
  data() {
    return {
      url: "/real-estate/building-policy",
      ids: [],
      selectedId: null,
      collectedRentType: "",
      accruedRevenues: null,
      actualRevenues: null,
      afterExpenses: "",
      expensesAmount: null,
      plusExtraRevenues: "",
      extraRevenues: null,
      company_takes: "",
      owner_takes: "",
      company_pays: "",
    };
  },
  mounted() {
    this.fetchDataAndPopulateDropdown();
  },
  watch: {
    selectedId: "fetchDataAndPopulateDropdown",
  },
  methods: {
    async fetchDataAndPopulateDropdown() {
      try {
        const response = await adminApi.get(this.url);
        console.log("response", response);
        const data = response.data.data;
        this.ids = data.map((item) => item.id);
        console.log("ids", this.ids);
        console.log(data);

        if (this.selectedId !== null) {
          // Find the data entry with the selected ID
          const selectedData = data.find((item) => item.id === this.selectedId);

          if (selectedData) {
            const policy = selectedData.policy;
            this.collectedRentType = policy.collected_rent_type;
            this.afterExpenses = policy.after_expenses;
            this.plusExtraRevenues = policy.plus_extra_revenues;
            console.log("collection", this.collectedRentType);
          }
        }
      } catch (error) {
        console.log(error);
      }
    },
    async addData() {
      console.log("this is addData");
      try {
        // Create an object to store your form data
        const formData = {
          building_policy_id: this.selectedId,
          accrued_revenues: this.accruedRevenues,
          actual_revenues: this.actualRevenues,
          expenses_amount: this.expensesAmount,
          extra_revenues: this.extraRevenues,
        };

        // Use the URLSearchParams API to encode the form data into a query string
        const queryString = new URLSearchParams(formData).toString();
        console.log("queryString", queryString);

        // Make the GET request with the query string
        const response = await adminApi.get(
          `/real-estate/building-policy/divide-owner-company-share?${queryString}`
        );
        console.log("Response Data:", response.data);
        this.company_takes = response.data.data.company_takes;
        this.owner_takes = response.data.data.owner_takes;
        this.company_pays = response.data.data.company_pays;
        // Handle the response data here
      } catch (error) {
        // Handle any errors
        console.error("Error:", error);
      }
    },
  },
};
</script>
