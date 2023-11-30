<template>
    <Layout>
        <PageHeader />
        <DocumentWithItem :document_id="44" />
    </Layout>
</template>

<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import DocumentWithItem from "../../../components/document/withItem/documentWithItem";
import Swal from "sweetalert2";
import permissionGuard from "../../../helper/permission";

export default {
    page: {
        title: "YearlyMaintenanceContract",
        meta: [{ name: "YearlyMaintenanceContract", content: "YearlyMaintenanceContract" }],
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Yearly Maintenance Contract", "all YearlyMaintenanceContract");
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
