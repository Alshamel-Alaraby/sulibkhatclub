<template>
  <div>
    <div class="col-md-12 position-relative">
      <div class="form-group">
        <label class="my-1 mr-2">{{ label }}</label>

        <input
          type="number"
          v-model="localValue.from"
          :placeholder="'From'"
          class="form-control"
          @input="updateSelectedValues"
        />
        <input
          type="number"
          v-model="localValue.to"
          :placeholder="'To'"
          class="form-control"
          @input="updateSelectedValues"
        />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    label: String,
    rangeId: String,
    from: Number,
    to: Number,
  },
  data() {
    return {
      localValue: {
        from: this.from !== undefined ? this.from : null,
        to: this.to !== undefined ? this.to : null,
      },
    };
  },

  methods: {
    updateSelectedValues() {
      // Still i did not do anything by them in the parent page
      console.log(this.rangeId, this.localValue);
      this.$emit("updateIds", this.rangeId, this.localValue);
    },
  },
};
</script>
