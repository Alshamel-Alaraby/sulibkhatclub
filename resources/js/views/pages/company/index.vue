<template>
  <div class="container">
    <div class="company d-flex align-items-center">
      <div class="col-12">
        <div class="mt-3 row justify-content-center align-items-center">
          <div class="col-md-4" v-for="company in companies">
            <div class="text-center company-item" @click="companyId(company.id)">
              <img
                v-if="company.media"
                class="img-thumbnail"
                :src="company.media[0].webp"
                @error="src = '/images/img-1.png'"
              />
              <img
                v-else
                class="img-thumbnail"
                src="/images/img-1.png"
                @error="src = '/images/img-1.png'"
              />
              <h4 class="mt-3">
                {{ $i18n.locale == "ar" ? company.name : company.name_e }}
              </h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Swal from "sweetalert2";
import adminApi from "../../../api/adminAxios";
export default {
  name: "index",
  data() {
    return {
      company_id: null,
    };
  },
  computed: {
    companies() {
      return this.$store.getters["auth/companies"];
    },
  },
  async mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
  },
  methods: {
    async companyId(id) {
      this.$store.commit("auth/editCompanyId", id);
      await axios
        .get(
          `${process.env.MIX_APP_URL_OUTSIDE}api/everything_about_the_company/${id}`
        )
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
            if(l.document_company_module.length > 0){
                let documents = [];
                l.document_company_module.forEach(e => {
                    if(e.document_types.length > 0){
                        e.document_types.forEach(w => {
                            documents.push({
                                name: w.name,
                                name_e: w.name_e,
                                is_admin: w.is_admin,
                                is_default: 0,
                                company_id: id
                            });
                        });
                    }
                });
                if(documents.length > 0){
                    documents.forEach(e => e.is_admin = 1);
                    adminApi
                        .post(`/document/from_admin`,{documents})
                        .then((res) => {})
                        .catch((err) => {
                            Swal.fire({
                                icon: "error",
                                title: `${this.$t("general.Error")}`,
                                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                            });
                        })
                }
            }
          this.$store.commit("auth/allWorkFlow", l.work_flow_trees);
          return this.$router.push({ name: "home" });
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
};
</script>

<style scoped>
.company {
  background-color: #dff0fe;
  min-height: 100vh;
  width: 100%;
  padding: 40px 0;
}

.company-content {
  height: 100%;
}
img {
  max-height: 150px;
  max-width: 250px;
}
.company-item {
  cursor: pointer;
  background: #fff;
  padding: 40px 20px;
  border-radius: 14px;
    height: 260px;
    margin: 7px 0;
}
.container {
  max-width: 1100px !important;
}
</style>
