<template>
    <Layout>
        <PageHeader />
        <ContractCom />
    </Layout>
</template>

<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/Page-header";
import ContractCom from "../../../../components/create/contractCom";

export default {
    name: "contract",page: {
        title: "Contract",
        meta: [{ name: "description", content: "Contract" }],
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
            if (vm.$store.state.auth.work_flow_trees.includes('contract') || vm.$store.state.auth.work_flow_trees.includes('real estate') || vm.$store.state.auth.user.type == 'super_admin') {
                return true;
            } else {
                return vm.$router.push({ name: "home" });
            }
        });
    },
    components: {
        Layout,
        PageHeader,
        ContractCom
    },

}
</script>

<style scoped>

</style>
