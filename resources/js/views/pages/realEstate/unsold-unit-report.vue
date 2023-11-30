<template>
  <Layout>
    <PageHeader />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <b-button @click="openModal">Open Modal</b-button>
            <b-modal
              ref="myModal"
              title="Filter Options"
              dialog-class="modal-lg"
              title-class="font-18"
              body-class="p-4"
              :hide-footer="true"
            >
              <!-- Display dropdowns -->
              <div
                class="row"
                v-for="(row, rowIndex) in dropdownRows"
                :key="rowIndex"
              >
                <div
                  class="col-md-4 mb-3"
                  v-for="(dropdown, colIndex) in row"
                  :key="colIndex"
                >
                  <filter-dropdown
                    :label="dropdown.label"
                    :dropdownId="dropdown.id"
                    :selectedValues="dropdown.selectedValues"
                    :options="dropdown.options"
                    @updateIds="handleDropdownUpdate"
                  ></filter-dropdown>
                </div>
              </div>

              <!-- Display range inputs -->
              <div class="row mt-3">
                <div
                  class="col-md-4 mb-3"
                  v-for="rangeInput in rangeInputs"
                  :key="rangeInput.id"
                >
                  <filterRangeInputs
                    :label="rangeInput.label"
                    :rangeId="rangeInput.id"
                    :value="rangeInput.value"
                  ></filterRangeInputs>
                </div>
              </div>

              <!-- Apply Button -->
              <div class="mt-3">
                <b-button @click="fetchTableData">Apply</b-button>
              </div>
            </b-modal>
            <FilterTable
              @filters-applied="fetchFilteredData"
              v-if="showTable"
              :fetchedResults="results"
            />
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import FilterDropdown from "../../../components/general/filterDropDown";
import FilterRangeInputs from "../../../components/general/filterRangeInputs";
import FilterTable from "../../../components/general/filterTable";
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import permissionGuard from "../../../helper/permission";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import Multiselect from "vue-multiselect";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import {
  dynamicSortString,
  dynamicSortNumber,
} from "../../../helper/tableSort";
import translation from "../../../helper/mixin/translation-mixin";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Unsold unit report",
    meta: [{ name: "description", content: "Unsold unit report" }],
  },
  mixins: [translation],
  components: {
    Multiselect,
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    FilterDropdown,
    FilterRangeInputs,
    FilterTable,
  },

  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(
        vm,
        "Unsold Unit Report",
        "all unSold_Unit RealState"
      );
    });
  },

  data() {
    return {
      dropdowns: [
        {
          id: "building_ids",
          label: "Buildings",
          selectedValues: [],
          options: [],
        },
        {
          id: "wallet_ids",
          label: "Wallets",
          selectedValues: [],
          options: [],
        },
        {
          id: "owner_ids",
          label: "Owners",
          selectedValues: [],
          options: [],
        },
        {
          id: "policy_ids",
          label: "Policies",
          selectedValues: [],
          options: [],
        },
        {
          id: "country_ids",
          label: "Countries",
          selectedValues: [],
          options: [],
        },
        {
          id: "governorate_ids",
          label: "Governorates",
          selectedValues: [],
          options: [],
        },
        {
          id: "city_ids",
          label: "Cities",
          selectedValues: [],
          options: [],
        },
        {
          id: "avenue_ids",
          label: "Avenues",
          selectedValues: [],
          options: [],
        },
        {
          id: "street_ids",
          label: "Streets",
          selectedValues: [],
          options: [],
        },
        {
          id: "building_type_ids",
          label: "Building Types",
          selectedValues: [],
          options: [],
        },
        {
          id: "unit_type_ids",
          label: "Unit Types",
          selectedValues: [],
          options: [],
        },
        {
          id: "unit_status_ids",
          label: "Unit Statuses",
          selectedValues: [],
          options: [],
        },
        {
          id: "view_ids",
          label: "Views",
          selectedValues: [],
          options: [],
        },
        {
          id: "finishing_ids",
          label: "Finishings",
          selectedValues: [],
          options: [],
        },
      ],
      rangeInputs: [
        { id: "unitArea", label: "Unit Area", from: 0, to: 2000 },
        { id: "unitRooms", label: "Unit Rooms", from: 0, to: 22 },
        { id: "unitBathrooms", label: "Unit Bathrooms", from: 0, to: 12 },
      ],
      results: [],
      showTable: false,
      dropdownIdTable: null,
      selectedTableValues: [],
    };
  },
  computed: {
    // Organize dropdowns into rows
    dropdownRows() {
      const rows = [];
      const itemsPerRow = 3;

      for (let i = 0; i < this.dropdowns.length; i += itemsPerRow) {
        rows.push(this.dropdowns.slice(i, i + itemsPerRow));
      }

      return rows;
    },
  },
  mounted() {
    this.fetchInitialData();
  },

  methods: {
    openModal() {
      this.$refs.myModal.show();
    },
    fetchInitialData() {
      adminApi
        .get("real-estate/unit-reports/get-lists")
        .then((response) => {
          const dropdownKeys = this.dropdowns.map((dropdown) => dropdown.id);

          dropdownKeys.forEach((key, index) => {
            this.dropdowns[index].options = response.data.data[key].map(
              (item) => ({
                text: item.name || item.name_e || item.code,
                value: item.id,
              })
            );
          });
          const rangeInputKeys = [
            "unitAreaFrom",
            "unitAreaTo",
            "unitRoomsFrom",
            "unitRoomsTo",
            "unitBathroomsFrom",
            "unitBathroomsTo",
          ];
          rangeInputKeys.forEach((key) => {
            const rangeInput = this.rangeInputs.find(
              (range) => range.id === key.slice(0, -4)
            );

            if (rangeInput) {
              rangeInput.from = response.data.data[key + "From"];
              rangeInput.to = response.data.data[key + "To"];
            }
          });
        })
        .catch((error) => {
          console.error("Error fetching data:", error);
        });
    },
    handleDropdownUpdate(dropdownId, selectedValues) {
      // so i can use them to fetch the table data
      this.dropdownIdTable = dropdownId;
      this.selectedTableValues = selectedValues;

      // Generate dropdownKeys dynamically based on the updated dropdowns
      const dropdownKeys = this.dropdowns.map((dropdown) => dropdown.id);
      const rangeInputKeys = [
        "unitAreaFrom",
        "unitAreaTo",
        "unitRoomsFrom",
        "unitRoomsTo",
        "unitBathroomsFrom",
        "unitBathroomsTo",
      ];

      // making the params dynamic based on the dropdownId:
      const params = {};
      const dropdown = this.dropdowns.find(
        (dropdown) => dropdown.id === dropdownId
      );
      if (dropdown) {
        params[dropdownId] = selectedValues;
      }
      console.log("DropdownId and SelectedValues:", dropdownId, selectedValues);
      console.log("params", params);
      adminApi
        .get("real-estate/unit-reports/get-lists", {
          params: params,
        })
        .then((response) => {
          const updatedData = response.data.data;
          console.log("updatedData", updatedData);
          dropdownKeys.forEach((key, index) => {
            if (updatedData[key]) {
              this.dropdowns[index].options = updatedData[key].map((item) => ({
                text: item.name || item.name_e || item.code,
                value: item.id,
              }));
            }
          });

          rangeInputKeys.forEach((key) => {
            if (updatedData[key]) {
              const rangeInput = this.rangeInputs.find(
                (range) => range.id === key.slice(0, -4)
              );

              if (rangeInput) {
                rangeInput[key.slice(-2)] = updatedData[key];
              }
            }
          });

          console.log("Dropdowns after update:", this.dropdowns);
          console.log("Range Inputs after update:", this.rangeInputs);
        })
        .catch((error) => {
          console.error("Error fetching or updating dropdowns:", error);
        });
    },
    fetchTableData() {
      const params = {};
      const dropdown = this.dropdowns.find(
        (dropdown) => dropdown.id === this.dropdownIdTable
      );
      if (dropdown) {
        params[this.dropdownIdTable] = this.selectedTableValues;
      }

      adminApi
        .get("real-estate/unit-reports/get-results", {
          params: params,
        })
        .then((response) => {
          this.results = response.data.data.results;
          this.showTable = true;
          this.$refs.myModal.hide();
          this.fetchInitialData();
        });
    },

    fetchFilteredData(selectedFilters) {
      console.log("selectedFiltersFather", selectedFilters);
      const columnMap = {
        code: "x_code",
        name: "x_name",
        name_e: "x_name_e",
        description: "x_description",
        description_e: "x_description_e",
        unit_ty: "x_unit_ty",
        unit_status: "x_unit_status",
        view: "x_view",
        finishing: "x_finishing",
        building: "x_building_ids",
        unit_types: "x_unit_types",
        unit_area: "x_unit_area",
        rooms: "x_unit_rooms",
        path: "x_unit_bathrooms",
        floor: "x_floor",
      };
      const params = {};

      for (const column in selectedFilters) {
        if (selectedFilters.hasOwnProperty(column)) {
          const apiParam = columnMap[column];
          if (apiParam) {
            params[apiParam] = selectedFilters[column].map((item) =>
              typeof item === "object" ? item.id : item
            );
          }
        }
      }
      console.log("params", params);

      adminApi
        .get("real-estate/unit-reports/get-excel-results", {
          params: params,
        })
        .then((response) => {
          // Update the 'results' data in this component with the fetched data
          this.results = response.data.data.excel_results;
        })
        .catch((error) => {
          console.error("Error fetching filtered data:", error);
          // Handle the error appropriately, such as displaying an error message
        });
    },
  },
};
</script>
