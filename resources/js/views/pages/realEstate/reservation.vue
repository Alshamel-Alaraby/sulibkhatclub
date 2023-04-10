<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import ReservationCom from "../../../components/create/reservationCom";
/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Reservation",
        meta: [{ name: "description", content: "Reservation" }],
    },
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

            if (vm.$store.state.auth.work_flow_trees.includes('reservation') || vm.$store.state.auth.work_flow_trees.includes('real estate') || vm.$store.state.auth.user.type == 'super_admin') {
                return true;
            } else {
                return vm.$router.push({ name: "home" });
            }
        });
    },
    components: {
        Layout,
        PageHeader,
        ReservationCom
    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <ReservationCom />
    </Layout>
</template>
