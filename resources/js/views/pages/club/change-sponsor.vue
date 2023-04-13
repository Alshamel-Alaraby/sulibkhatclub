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
import translation from "../../../helper/translation-mixin";
import Sponsor from "../../../components/create/club/sponsor.vue";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Change sponsor",
        meta: [{ name: "description", content: "Change sponsor" }],
    },
    mixins: [translation],
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
        Sponsor
    },
    mounted() {

    },
    data() {
        return {
            sponsors: [],
            isLoader: false,
            create: {
                old_sponsor_id: null,
                sponsor_id: null,
            }
        };
    },
    validations: {
        create: {
            old_sponsor_id: { required },
            sponsor_id: { required },
        }
    },
    mounted() {
        this.getSponsors();
    },
    methods: {
        AddSubmit() {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                if (this.create.old_sponsor_id == this.create.sponsor_id) {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.BothSponsorsAreSame")}`,
                    });
                    return;
                }
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .put(`/club-members/members/sponsor/${this.create.old_sponsor_id}`, {
                        sponsor_id: this.create.sponsor_id
                    })
                    .then((res) => {
                        this.create.sponsor_id = null;
                        this.create.old_sponsor_id = null;
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Addedsuccessfully")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: `${this.$t("general.Error")}`,
                                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                            });
                        }
                    })
                    .finally(() => {
                        this.isLoader = false;
                    });
            }
        },
        showSponsorModalOld() {
            if (this.create.old_sponsor_id == 0) {
                this.$bvModal.show("create-sponsor");
                this.create.old_sponsor_id = null;
            }
        },
        showSponsorModal() {
            if (this.create.sponsor_id == 0) {
                this.$bvModal.show("create-sponsor");
                this.create.sponsor_id = null;
            }
        },
        async getSponsors() {
            this.isLoader = true;
            await adminApi
                .get(`/club-members/sponsers`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف راعي", name_e: "Add sponsor" });
                    this.sponsors = l;
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
        <Sponsor :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getSponsors" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <h4 class="header-title">{{ $t("general.changeSpenser") }}</h4>
                        </div>
                        <div class="row d-flex justify-content-center">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="my-1 mr-2">
                                                {{ getCompanyKey("sponsor") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect @input="showSponsorModalOld" v-model="create.old_sponsor_id"
                                                :options="sponsors.map((type) => type.id)" :custom-label="
                                                    (opt) =>
                                                        $i18n.locale == 'ar'
                                                            ? sponsors.find((x) => x.id == opt).name
                                                            : sponsors.find((x) => x.id == opt).name_e
                                                ">
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="my-1 mr-2">
                                                {{ getCompanyKey("sponsor") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect @input="showSponsorModal" v-model="create.sponsor_id"
                                                :options="sponsors.map((type) => type.id)" :custom-label="
                                                    (opt) =>
                                                        $i18n.locale == 'ar'
                                                            ? sponsors.find((x) => x.id == opt).name
                                                            : sponsors.find((x) => x.id == opt).name_e
                                                ">
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <b-button style="margin-top:33px" variant="success" type="button" class="mx-1"
                                            v-if="!isLoader" @click.prevent="AddSubmit">
                                            {{ $t("general.Add") }}
                                        </b-button>

                                        <b-button style="margin-top:33px" variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                        </b-button>
                                    </div>
                                </div>
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
