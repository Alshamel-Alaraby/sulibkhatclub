<template>
    <Layout>
        <PageHeader />
        <DocumentWithItem :document_id="9" />
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
        title: "Quotation",
        meta: [{ name: "description", content: "Quotation" }],
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Quotation BoardRent Basic", "all Quotation BoardRent Basic");
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
