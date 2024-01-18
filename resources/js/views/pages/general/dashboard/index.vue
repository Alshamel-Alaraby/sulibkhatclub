<script>
import { selectedParents } from "../../../../helper/global.js";
import Layout from "../../../layouts/main";
import bookingDashboard from "../../../../components/statistic/booking.vue";
import clubDashboard from "../../../../components/statistic/club.vue";
import hmsDashboard from "../../../../components/statistic/h_m_s.vue";
import tasksDashboard from "../../../../components/statistic/task_management.vue";
import defaultDashboard from "../../../../components/statistic/defaultDashboard.vue";
import permissionGuard from "../../../../helper/permission";
import customTable from "../../../../helper/mixin/customTable";

/**
 * Sales-Dashboard component
 */
export default {
    components: {
        Layout,
        clubDashboard,
        hmsDashboard,
        tasksDashboard,
        bookingDashboard,
        defaultDashboard,
    },
    data() {
        return{
            selectedParents: selectedParents.value,
        }

    },
    mixins:[customTable],
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
            <div v-if="isPermission('Dashboard')">
                <tasksDashboard v-if="selectedParents && selectedParents[2] == '5'" />

                <hmsDashboard v-if="selectedParents && selectedParents[2] == '4'" />

                <clubDashboard v-if="selectedParents && selectedParents[2] == '3'" />

                <bookingDashboard v-if="selectedParents && selectedParents[2] == '2'" />

                <defaultDashboard v-if="selectedParents && selectedParents[2] == '1'" />

            </div>

        </Layout>
</template>

<style scope>
.content-page {
    padding: 70px 15px 5px !important;
}
</style>
