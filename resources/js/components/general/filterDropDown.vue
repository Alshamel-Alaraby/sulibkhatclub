<template>
  <div>
    <div class="col-md-12 position-relative">
      <div class="form-group">
        <label class="my-1 mr-2">{{ label }}</label>
        <multiselect
          v-model="localSelectedValues"
          :disabled="disabled"
          :multiple="true"
          :options="options.map((x) => x.value)"
          :custom-label="
            (option) =>
              (options.find((x) => x.value == option) || {}).text || ''
          "
          class="custom-multiselect"
          @open="onDropDownOpen"
          @close="onDropDownClose"
        >
        </multiselect>
      </div>
    </div>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import adminApi from "../../api/adminAxios";

export default {
  components: {
    Multiselect,
  },
  props: {
    label: String,
    dropdownId: String,
    selectedValues: Array,
    options: Array,
    disabled: Boolean,
    placeholder: String,
  },
  data() {
    return {
      localSelectedValues: this.selectedValues,
      selectedDropdownIds: [],
    };
  },

  methods: {
    onDropDownOpen() {
      this.$emit("dropdownOpened", {
        dropdownId: this.dropdownId,
        selectedValues: this.localSelectedValues,
      });
    },

    onDropDownClose() {
      this.$emit("dropdownClosed", {
        dropdownId: this.dropdownId,
        selectedValues: this.localSelectedValues,
      });
    },
  },
};
</script>
