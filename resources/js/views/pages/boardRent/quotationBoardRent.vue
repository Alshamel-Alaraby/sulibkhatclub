<template>
    <Layout>
        <PageHeader />
        <DocumentWithItem :document_id="9" />
    </Layout>
</template>

<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import DocumentWithItem from "../../../components/document/documentWithItem";
import Swal from "sweetalert2";

export default {
    page: {
        title: "Quotation",
        meta: [{ name: "description", content: "Quotation" }],
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {

            if (vm.$store.state.auth.work_flow_trees.includes("board rent-e")) {
                Swal.fire({
                    icon: "error",
                    title: `${vm.$t("general.Error")}`,
                    text: `${vm.$t("general.ModuleExpired")}`,
                });
                return vm.$router.push({ name: "home" });
            }

            if (vm.$store.state.auth.work_flow_trees.includes('reservation') || vm.$store.state.auth.work_flow_trees.includes('board rent') || vm.$store.state.auth.user.type == 'super_admin') {
                return true;
            } else {
                return vm.$router.push({ name: "home" });
            }
        });
    },
    components: {
        Layout,
        PageHeader,
        DocumentWithItem
    },
}
</script>

<style scoped>

</style>
