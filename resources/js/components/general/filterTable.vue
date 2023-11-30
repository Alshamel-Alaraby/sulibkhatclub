<template>
  <div>
    <table class="table table-borderless table-hover table-centered m-0">
      <thead>
        <tr>
          <!-- Render dropdown icon for each column header -->
          <th v-for="column in columns" :key="column.key" scope="col">
            <div class="d-flex align-items-center">
              {{ column.label }}
              <i
                class="fas fa-caret-down ml-2 dropdown-icon"
                style="font-size: 10px; color: white"
                @click="openModal(column.key)"
              ></i>
            </div>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(result, index) in fetchedResults"
          :key="index"
          class="body-tr-custom"
        >
          <td
            v-for="column in columns"
            :key="column.key"
            class="body-td-custom"
            style="vertical-align: middle"
          >
            {{ getColumnValue(result, column.key) }}
          </td>
        </tr>
      </tbody>
    </table>
    <!-- Bootstrap Vue Modal -->
    <b-modal
      ref="tableModal"
      title="Filter Table"
      title-class="font-18"
      body-class="p-4"
      :hide-footer="true"
    >
      <template #modal-title>
        {{
          modalColumnKey
            ? columns.find((col) => col.key === modalColumnKey).label +
              " Filters"
            : ""
        }}
      </template>
      <div class="d-flex justify-content-between mt-3">
        <div>
          <b-dropdown
            text="Filter by Value"
            variant="secondary"
            class="custom-dropdown"
          >
            <template #button-content>
              <div class="d-flex align-items-center">
                <span class="filter-label">Filter by Value</span>
                <i class="fas fa-caret-down ml-2 dropdown-icon"></i>
              </div>
            </template>
            <b-dropdown-form class="custom-dropdown-form">
              <b-form @submit.prevent="applyFilters">
                <b-form-group>
                  <b-form-input
                    v-model="searchValue"
                    placeholder="Search value"
                    class="search-input"
                  ></b-form-input>
                </b-form-group>
                <b-form-group>
                  <b-form-checkbox-group
                    v-model="selectedFilters[modalColumnKey]"
                    :options="
                      filteredValues.map((value) => ({ text: value, value }))
                    "
                    stacked
                  ></b-form-checkbox-group>
                </b-form-group>
                <b-button type="submit" @click="applyFilters" variant="primary"
                  >Apply</b-button
                >
              </b-form>
            </b-dropdown-form>
          </b-dropdown>
        </div>

        <div>
          <b-button @click="openNumberFilterModal" class="ml-2"
            >Number Filter</b-button
          >
          <b-modal
            ref="numberFilterModal"
            title="Number Filter"
            title-class="font-18"
            body-class="p-4"
            :hide-footer="true"
          >
            <b-form @submit.prevent="applyNumberFilters">
              <!-- First row -->
              <b-row class="mb-3">
                <b-col>
                  <b-form-select
                    v-model="firstFunction"
                    :options="functionOptions"
                  ></b-form-select>
                </b-col>
                <b-col>
                  <b-form-input
                    v-model="firstNumber"
                    type="number"
                    placeholder="Enter number"
                  ></b-form-input>
                </b-col>
              </b-row>

              <!-- Radio boxes for AND/OR -->
              <b-form-group class="mb-3">
                <b-form-radio-group v-model="logicalOperator">
                  <b-form-radio value="AND">AND</b-form-radio>
                  <b-form-radio value="OR">OR</b-form-radio>
                </b-form-radio-group>
              </b-form-group>

              <!-- Second row -->
              <b-row class="mb-3">
                <b-col>
                  <b-form-select
                    v-model="secondFunction"
                    :options="functionOptions"
                  ></b-form-select>
                </b-col>
                <b-col>
                  <b-form-input
                    v-model="secondNumber"
                    type="number"
                    placeholder="Enter number"
                  ></b-form-input>
                </b-col>
              </b-row>

              <!-- Buttons -->
              <b-row>
                <b-col>
                  <b-button variant="primary" @click="applyNumberFilters"
                    >Okay</b-button
                  >
                </b-col>
                <b-col>
                  <b-button variant="danger" @click="cancelNumberFilters"
                    >Cancel</b-button
                  >
                </b-col>
              </b-row>
            </b-form>
          </b-modal>
        </div>
      </div>

      <div>
        <b-button @click="applyFilters" variant="primary">Apply</b-button>
      </div>
    </b-modal>
  </div>
</template>

<script>
export default {
  props: {
    fetchedResults: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      columns: [],
      selectedFilters: {},
      openDropdown: null,
      modalColumnKey: null,
      isModalOpen: false,
      searchValue: "",
      firstFunction: "",
      firstNumber: null,
      logicalOperator: "AND",
      secondFunction: "",
      secondNumber: null,
      functionOptions: [
        "Equals",
        "Not Equal to",
        "Greater Than",
        "Greater Than or Equal to",
        "Less Than",
        "Less Than or Equal to",
        "Between",
      ],
    };
  },
  mounted() {
    if (this.fetchedResults.length > 0) {
      const excludedColumns = [
        "id",
        "properties",
        "module",
        "media",
        "video_link",
      ]; // Columns to exclude
      const keys = Object.keys(this.fetchedResults[0]).filter(
        (key) => !excludedColumns.includes(key)
      );

      this.columns = keys.map((key) => ({
        key,
        label: key.charAt(0).toUpperCase() + key.slice(1),
        filterable: true,
      }));
    }
  },
  computed: {
    // Method to get unique values for a specific column
    uniqueValues() {
      return (columnKey) => {
        const values = this.fetchedResults.map((result) => result[columnKey]);
        return [...new Set(values)];
      };
    },
    filteredValues() {
      const searchValue = this.searchValue.toLowerCase();
      const columnValues = this.uniqueValues(this.modalColumnKey);
      return columnValues
        .map((value) => {
          if (typeof value === "object" && value !== null) {
            if (value.name || value.name_e) {
              return value.name || value.name_e;
            } else {
              // If neither 'name' nor 'name_e' is present, return an empty string
              return "";
            }
          } else if (typeof value === "number") {
            // Convert numeric values to strings for comparison
            return String(value).toLowerCase();
          } else {
            return String(value).toLowerCase(); // Convert non-objects to lowercase strings
          }
        })
        .filter((value) => value.toLowerCase().includes(searchValue));
    },
  },
  methods: {
    openModal(columnKey) {
      this.modalColumnKey = columnKey;
      this.$refs.tableModal.show();
    },

    isSelected(columnKey, value) {
      return (
        this.selectedFilters[columnKey] &&
        this.selectedFilters[columnKey].includes(value)
      );
    },

    toggleDropdown(key) {
      if (this.openDropdown === key) {
        this.openDropdown = null;
      } else {
        this.openDropdown = key;
      }
    },

    toggleCheckbox(columnKey, value) {
      if (!this.selectedFilters[columnKey]) {
        this.selectedFilters[columnKey] = [];
      }
      const index = this.selectedFilters[columnKey].indexOf(value);
      if (index > -1) {
        this.selectedFilters[columnKey].splice(index, 1);
      } else {
        this.selectedFilters[columnKey].push(value);
      }
      console.log("selectedFilters", this.selectedFilters);
    },

    applyFilters() {
      // Modify selectedFilters before sending the request
      const updatedFilters = {};
      for (const columnKey in this.selectedFilters) {
        const selectedValues = this.selectedFilters[columnKey];
        if (Array.isArray(selectedValues) && selectedValues.length > 0) {
          const columnValues = this.uniqueValues(columnKey);
          if (
            columnValues &&
            columnValues[0] &&
            typeof columnValues[0] === "object"
          ) {
            // For nested objects, get the IDs based on the selected names
            updatedFilters[columnKey] = selectedValues.map((name) => {
              const matchedObject = columnValues.find(
                (obj) => obj.name === name || obj.name_e === name
              );
              return matchedObject ? matchedObject.id : null;
            });
          } else {
            updatedFilters[columnKey] = selectedValues;
          }
        }
      }

      // Send the updatedFilters object to the backend
      this.$emit("filters-applied", updatedFilters);
      this.openDropdown = null;
      this.$refs.tableModal.hide();
    },
    openNumberFilterModal() {
      this.$refs.numberFilterModal.show();
    },
    applyNumberFilters() {
      console.log("applyNumberFilters");
      console.log("First Function:", this.firstFunction);
      console.log("First Number:", this.firstNumber);
      console.log("Logical Operator:", this.logicalOperator);
      console.log("Second Function:", this.secondFunction);
      console.log("Second Number:", this.secondNumber);

      // Implement your logic to apply number filters here
      // For example, you can make API calls to fetch data based on these filters
      // Or directly filter the existing fetchedResults array

      // Close the modal after applying filters
      this.$refs.numberFilterModal.hide();
    },

    // Method to cancel number filters
    cancelNumberFilters() {
      // Reset the values and close the modal
      this.firstFunction = "";
      this.firstNumber = null;
      this.logicalOperator = "AND";
      this.secondFunction = "";
      this.secondNumber = null;
      this.$refs.numberFilterModal.hide();
    },
    getColumnValue(result, key) {
      const nestedColumn = result[key];

      if (nestedColumn && typeof nestedColumn === "object") {
        // Check for 'name' or 'name_e' within the nested object
        const name = nestedColumn.name || nestedColumn.name_e;
        return name || ""; // Return 'name' if present, otherwise 'name_e' or an empty string
      } else if (Array.isArray(nestedColumn)) {
        // Assuming the array contains objects with 'name' and 'name_e' properties
        return nestedColumn.map((item) => item.name || item.name_e).join(", ");
      } else {
        return result[key]; // Return the value if it's not a nested object or array
      }
    },
  },
};
</script>

<style scoped>
.dropdown-icon {
  cursor: pointer;
}
.custom-dropdown {
  /* Styles for the dropdown container */
  margin-right: 10px; /* Example: add some margin to the right */
}

.dropdown-icon {
  /* Styles for the dropdown icon */
  color: white; /* Example: change the color to blue */
}

.custom-dropdown-form {
  /* Styles for the dropdown form */
  padding: 20px; /* Example: add padding */
}

.search-input {
  /* Styles for the search input */
  border: 1px solid #ccc; /* Example: add a border */
  border-radius: 8px; /* Example: add some border radius */
  padding: 8px; /* Example: adjust padding */
  width: 100%; /* Example: make the input full-width */
}

.filter-checkbox {
  /* Styles for the filter checkboxes */
  margin-top: 5px; /* Example: add some margin at the top */
}
</style>
