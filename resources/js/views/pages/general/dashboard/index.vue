<script>
import { selectedParents } from "../../../../helper/global.js";
import Layout from "../../../layouts/main";
import bookingDashboard from "../../../../components/statistic/booking.vue";
import clubDashboard from "../../../../components/statistic/club.vue";
import defaultDashboard from "../../../../components/statistic/defaultDashboard.vue";
/**
 * Sales-Dashboard component
 */
export default {
    components: {
        Layout,
        clubDashboard,
        bookingDashboard,
        defaultDashboard,
    },
    data() {
        return{
            selectedParents: selectedParents.value,
        }

    },

    mounted(){
        // this.selectedParents = selectedParents.value
    },
    watch:{
    selectedParentsComputed: {
      handler(newV, old) {
        console.log(selectedParents.value)
        this.selectedParents = selectedParents.value;
      },
    },
  },
  computed: {
    selectedParentsComputed: function () {
      return selectedParents.value;
    },
  },
};
</script>

<template>
        <Layout>

            <clubDashboard v-if="selectedParents && selectedParents[2] == '3'" />

            <bookingDashboard v-if="selectedParents && selectedParents[2] == '2'" />

            <defaultDashboard v-if="selectedParents && selectedParents[2] == '1'" />


        </Layout>
</template>

<style scope>
.content-page {
    padding: 70px 15px 5px !important;
}
</style>
