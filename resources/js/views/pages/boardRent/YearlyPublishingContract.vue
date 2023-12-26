<template>
    <Layout>
        <PageHeader />
        <DocumentWithItem :document_id="43" />
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
        title: "Yearly Publishing Contract",
        meta: [{ name: "description", content: "Yearly Publishing Contract" }],
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Yearly Publishing Contract", "all Yearly Publishing Contract");
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
