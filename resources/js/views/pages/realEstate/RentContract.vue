<template>
  <Layout>
    <PageHeader />
    <DocumentWithUnits :document_id="28" />
  </Layout>
</template>

<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import DocumentWithUnits from "./documentWithUnits.vue";
import Swal from "sweetalert2";
import permissionGuard from "../../../helper/permission";

export default {
  page: {
    title: "Renting Units",
    meta: [{ name: "Renting Units", content: "Renting Units" }],
  },
  // beforeRouteEnter(to, from, next) {
  //   next((vm) => {
  //     return permissionGuard(vm, "Renting Units", "all Renting Units");
  //   });
  // },
  components: {
    Layout,
    PageHeader,
    DocumentWithUnits,
  },
};
</script>

<style scoped></style>
