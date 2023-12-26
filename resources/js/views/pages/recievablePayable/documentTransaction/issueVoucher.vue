<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import DocumentWithMoney from "../../../../components/document/withMoney/document-with-money";
import permissionGuard from "../../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Issue Voucher",
        meta: [{name: "Issue Voucher", content: 'Issue Voucher'}],
    },
    components: {
        Layout,
        PageHeader,
        DocumentWithMoney
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Issue Voucher RP", "all IssueVoucher RP");
    });
    },
};
</script>

<template>
    <Layout>
        <PageHeader/>
        <DocumentWithMoney :document_id="46"/>
    </Layout>
</template>

<style>
</style>



