<script>
import { selectedParents } from "../../../../helper/global.js";
import Layout from "../../../layouts/main";
import bookingDashboard from "../../../../components/statistic/booking.vue";
import clubDashboard from "../../../../components/statistic/club.vue";
import hmsDashboard from "../../../../components/statistic/h_m_s.vue";
import defaultDashboard from "../../../../components/statistic/defaultDashboard.vue";
import permissionGuard from "../../../../helper/permission";

/**
 * Sales-Dashboard component
 */
export default {
    components: {
        Layout,
        clubDashboard,
        hmsDashboard,
        bookingDashboard,
        defaultDashboard,
    },
    data() {
        return{
            selectedParents: selectedParents.value,
        }

    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Dashboard", "Dashboard");
        });
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

            <hmsDashboard v-if="selectedParents && selectedParents[2] == '4'" />

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
