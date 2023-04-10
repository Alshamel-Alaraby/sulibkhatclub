import adminApi from "../api/adminAxios";
import Swal from "sweetalert2";
import translationCompMixin from "./translation-comp-mixin";
export default {
  data() {
    return {
      defaultsKeys: {},
      companyKeys: {},
      filterResult: {}
    }
  },
  mixins: [translationCompMixin],
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
    this.getDefaultKeys();
    this.getCompanyKeys();
  },
  methods: {
    async getDefaultKeys() {
      this.isLoader = true;
      await adminApi
        .post(`/translation-update`, {
          company_id: 0,
          translations: {},
          get_translation: true,
        })
        .then((res) => {
          let workflows = this.$store.state.auth.work_flow_trees;
          let keys = {};
          for (let key in res.data.translations) {
            if (this.$store.state.auth.user.type == 'super_admin' || workflows.includes(res.data.translations[key].screen)) {
              keys[key] = res.data.translations[key];
            }
          }
          this.defaultsKeys = keys;
          this.filterResult = { ...this.defaultsKeys };
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    async getCompanyKeys() {
      this.isLoader = true;
      await adminApi
        .post(`/translation-update`, {
          company_id: this.company_id,
          translations: {},
          get_translation: true,
        })
        .then((res) => {
          this.companyKeys = res.data.translations;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
  },
}
