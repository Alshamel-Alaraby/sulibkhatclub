<template>
  <div>
    <router-view />
  </div>
</template>
<script>
import Swal from "sweetalert2";
export default {
  methods: {
    async companyId(id) {
      this.$store.commit("auth/editCompanyId", id);
      await axios
        .get(`${process.env.MIX_APP_URL_OUTSIDE}api/everything_about_the_company/${id}`)
        .then((res) => {
          let l = res.data.data;
          let name = [];
          l.work_flow_trees.forEach((parent) => {
            if (parent.module_id && parent.is_module_expired) {
              name.push(parent.name_e + "-e");
            }
            name.push(parent.name_e);
            if (parent.child) {
              parent.child.forEach((child1) => {
                name.push(child1.name_e);
                if (child1.child) {
                  child1.child.forEach((child2) => {
                    name.push(child2.name_e);
                    if (child2.child) {
                      child2.child.forEach((child3) => {
                        name.push(child3.name_e);
                      });
                    }
                  });
                }
              });
            }
          });
          this.$store.commit("auth/editWorkFlowTrees", [
            "dictionary",
            "company",
            ...name,
          ]);
          // this.$store.state.auth.work_flow_trees = ["dictionary", "company", ...name];
          // this.$store.state.auth.allWorkFlow = l.work_flow_trees;
          this.$store.commit("auth/allWorkFlow", l.work_flow_trees);
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
  },
  mounted() {
    if (this.$store.getters["auth/company_id"]) {
      // this.companyId(this.$store.getters["auth/company_id"]);
    }
  },
};
</script>
