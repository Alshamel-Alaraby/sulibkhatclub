<template>
    <Layout>
        <PageHeader />
        <DocumentWithItem :document_id="4" />
    </Layout>
</template>

<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import DocumentWithItem from "../../../components/document/RealEstate/documentWithUnitsGeneral";
import Swal from "sweetalert2";
import permissionGuard from "../../../helper/permission";

export default {
    page: {
        title: "Unit Credit Invoice",
        meta: [{ name: "unitCreditInvoice", content: "unitCreditInvoice" }],
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Unit Credit Invoice", "all Unit Credit Invoice");
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
