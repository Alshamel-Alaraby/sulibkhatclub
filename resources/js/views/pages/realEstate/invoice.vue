<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import InvoiceCom from "../../../components/create/invoiceCom";
/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Invoice",
        meta: [{ name: "description", content: "Invoice" }],
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

            if (vm.$store.state.auth.work_flow_trees.includes('invoice') || vm.$store.state.auth.work_flow_trees.includes('real estate') || vm.$store.state.auth.user.type == 'super_admin') {
                return true;
            } else {
                return vm.$router.push({ name: "home" });
            }
        });
    },
    components: {
        Layout,
        PageHeader,
        InvoiceCom
    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <InvoiceCom />
    </Layout>
</template>
