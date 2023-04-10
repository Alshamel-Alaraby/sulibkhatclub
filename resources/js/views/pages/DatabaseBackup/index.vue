<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import { required, minLength, maxLength, integer, requiredIf } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/loader";
import { dynamicSortString, dynamicSortNumber } from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Database backup",
    meta: [{ name: "description", content: "Database backup" }],
  },
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
  },
  data() {
    return {
      progress: 0
    };
  },
  validations: {
  },
  mounted() {
  },
  methods: {

    uploadFile(e) {
      const file = e.target.files[0];
      let formData = new FormData();
      formData.append("file", file);
      let bar = document.getElementById("progress-bar");
      let self=this;
      const config = {
        onUploadProgress: function (progressEvent) {
          const percentCompleted = Math.round((progressEvent.loaded / progressEvent.total) * 100);
          self.progress = percentCompleted;
          bar.innerHTML = `${percentCompleted}%`;
          bar.style.width = `${percentCompleted}%`;
        }
      };
      adminApi
        .post(`/general_upload`, formData, config)
        .then((res) => {
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
  }
};
</script>

<template>
  <Layout>
    <PageHeader />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row mb-2">
              <h4 class="header-title">{{ $t("general.DatabaseBackup") }}</h4>
            </div>
            <div class="row">
              <div class="buttons d-flex">
                <div class="file-upload">
                  <label class="btn btn-primary icon" for="file">
                    {{ $t("general.Upload") }} <i class="fas fa-plus" aria-hidden="true"></i>
                  </label>
                  <input @change="uploadFile" type="file" id="file" />
                </div>
                <div class="file-upload">
                  <label class="btn btn-primary icon">
                    {{ $t("general.Download") }} <i class="fa fa-download" aria-hidden="true"></i>
                  </label>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-center align-items-center border"
              style="height: 100px;margin-top:100px;flex-direction: column;">
              <h4 v-if="progress == 100" class="header-title text-center mb-2">{{ $t("general.completed") }}</h4>
              <h4 v-else class="header-title text-center mb-2">{{ $t("general.loading") }}...</h4>
              <div style="width:85%;background-color: #fff;" class="progress">
                <div id="progress-bar" class="progress-bar" role="progressbar"
                  style="width:0%;background-color: #3bafda;transition:width .5s;" aria-valuenow="25" aria-valuemin="0"
                  aria-valuemax="100">0%</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>
<style lang="scss">
.buttons {
  margin-top: 20px !important;
  gap: 20px;
  display: flex;
  align-items: center;
  margin: 0 25px;
}

.file-upload {
  i {
    margin-top: 7px;
  }

  .icon {
    &:hover {
      cursor: pointer !important;
    }

    padding: 4px 12px !important;

    i {
      font-size: 14px;
      position: relative;
    }

    color: white !important;
  }

  text-align: center;

  input[type="file"] {
    display: none;
  }
}
</style>
