<script>
import Layout from "../../views/layouts/main";
import Swal from "sweetalert2";
import WidgetChart from "../widgets/Widget-chart";
import PageHeader from "../general/Page-header";
import RevenueReport from "../widgets/Revenue-report";
import ProductsSales from "../widgets/Products-sales";
import MarketingReports from "../widgets/Marketing-reports";
import Portlet from "../widgets/Portlet";
import RevenueHistory from "../widgets/Revenue-history";
import Projections from "../widgets/Projections-club"
import adminApi from "../../api/adminAxios";
import CrmWidget from "../widgets/Crm-widget";

/**
 * Sales-Dashboard component
 */
export default {
    name: "Statistics Club",
    components: {
        Layout,
        WidgetChart,
        PageHeader,
        RevenueReport,
        ProductsSales,
        MarketingReports,
        Portlet,
        RevenueHistory,
        Projections,
        CrmWidget
    },
    data() {
        return {
            title: "Welcome !",
            items: [
                {
                    text: "Minton",
                },
                {
                    text: "Dashboards",
                },
                {
                    text: "Sales",
                    active: true,
                },
            ],
            tableTitle: "Top Selling Products",
            productData: [
                {
                    name: "ASOS Ridley High Waist",
                    price: "$79.49",
                    quantity: 82,
                    amount: "$6,518.18",
                    date: "Sep 1, 2018",
                    sales: 54,
                    productid: 200125,
                },
                {
                    name: "Marco Lightweight Shirt",
                    price: "$128.50",
                    quantity: 37,
                    amount: "$4,754.50",
                    date: "Sep 15, 2018",
                    sales: 28,
                    productid: 200130,
                },
                {
                    name: "Half Sleeve Shirt",
                    price: "$39.99",
                    quantity: 64,
                    amount: "$2,559.36",
                    date: "Nov 1, 2018",
                    sales: 55,
                    productid: 200140,
                },
                {
                    name: "Lightweight Jacket",
                    price: "$20.00",
                    quantity: 184,
                    amount: "$3,680.00",
                    date: "Nov 15, 2018",
                    sales: 85,
                    productid: 200250,
                },
                {
                    name: "Marco Shoes",
                    price: "$28.49",
                    quantity: 69,
                    amount: "$1,965.81",
                    date: "Jan 1, 2019",
                    sales: 49,
                    productid: 200256,
                },
                {
                    name: "ASOS Ridley High Waist",
                    price: "$79.49",
                    quantity: 82,
                    amount: "$6,518.18",
                    date: "Sep 1, 2018",
                    sales: 54,
                    productid: 200125,
                },
                {
                    name: "Half Sleeve Shirt",
                    price: "$39.99",
                    quantity: 64,
                    amount: "$2,559.36",
                    date: "Nov 1, 2018",
                    sales: 55,
                    productid: 200140,
                },
                {
                    name: "Lightweight Jacket",
                    price: "$20.00",
                    quantity: 184,
                    amount: "$3,680.00",
                    date: "Nov 15, 2018",
                    sales: 85,
                    productid: 200250,
                },
            ],
            isLoader : false,
            statices: {},
            normalDetails:[],
            dismissedDetails:[],
            foundingDetails:[],
        };
    },
    mounted() {
        this.getStatices();

    },
    methods: {
        getStatices() {
            this.isLoader = true;

            adminApi.get(`/club-members/statics/get-members-valid`)
                .then((res) => {
                    let l = res.data.data;
                    this.statices = l;
                    this.staticesHandel();
                })
                .catch((err) => {
                    Swal.fire({
                        icon: 'error',
                        title: `${this.$t('general.Error')}`,
                        text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                    });
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        staticesHandel() {
            this.normalDetails = [
                {
                    "name":'InTheListOfMembers',
                    "value":this.statices.InTheListOfMembers
                },
                {
                    "name":'stopped',
                    "value":this.statices.stopped
                },
                {
                    "name":'HeHasTheRightToRun',
                    "value":this.statices.HeHasTheRightToRun
                },
                {
                    "name":'HeHasTheRightToVote',
                    "value":this.statices.HeHasTheRightToVote
                },
                {
                    "name":'HeHasNoRightToVote',
                    "value":this.statices.HeHasNoRightToVote
                },
                {
                    "name":'PaidPersonHasNoRightToAttend',
                    "value":this.statices.PaidPersonHasNoRightToAttend
                },
            ];

            this.dismissedDetails = [
                {
                    "name":'DeletedAtHisRequest',
                    "value":this.statices.DeletedAtHisRequest
                },
                {
                    "name":'CancellationDueToDeath',
                    "value":this.statices.CancellationDueToDeath
                },
                {
                    "name":'CancellationPursuantToMinisterialDecision',
                    "value":this.statices.CancellationPursuantToMinisterialDecision
                },
                {
                    "name":'CancellationAccordingToTheAuthoritySLetter',
                    "value":this.statices.CancellationAccordingToTheAuthoritySLetter
                },
                {
                    "name":'WriteOffForNonPayment',
                    "value":this.statices.WriteOffForNonPayment
                },
            ];

            this.foundingDetails = [
                {
                    "name":'FoundingMemberDetail',
                    "value":this.statices.FoundingMemberDetail
                },
                {
                    "name":'HeHasTheRightToRunAndVoteDetail',
                    "value":this.statices.HeHasTheRightToRunAndVoteDetail
                },
            ];
        }
    }
};
</script>

<template>
    <Layout>
        <PageHeader :title="title" :items="items" />

        <div class="dashboard-fluid mb-3">
<!--            <div class="row mt-2">-->
<!--                <div class="col-xl-3 col-md-6">-->
<!--                    <CrmWidget-->
<!--                        :title="'normalMemberCount'"-->
<!--                        :icon="'ri-stack-line'"-->
<!--                        :value="statices.normalMemberCount"-->
<!--                    />-->
<!--                </div>-->
<!--                <div class="col-xl-3 col-md-6">-->
<!--                    <CrmWidget-->
<!--                        :title="'dismissedMemberCount'"-->
<!--                        :icon="'ri-slideshow-2-line'"-->
<!--                        :value="statices.dismissedMemberCount"-->
<!--                    />-->
<!--                </div>-->
<!--                <div class="col-xl-3 col-md-6">-->
<!--                    <CrmWidget-->
<!--                        :title="'foundingMemberCount'"-->
<!--                        :icon="'ri-hand-heart-line'"-->
<!--                        :value="statices.foundingMemberCount"-->
<!--                    />-->
<!--                </div>-->
<!--                <div class="col-xl-3 col-md-6">-->
<!--                    <CrmWidget-->
<!--                        :title="'pendingMember'"-->
<!--                        :icon="'ri-hand-heart-line'"-->
<!--                        :value="statices.RequestMembersPendingCount"-->
<!--                    />-->
<!--                </div>-->
<!--                <div class="col-xl-3 col-md-6">-->
<!--                    <CrmWidget-->
<!--                        :title="'sponsor'"-->
<!--                        :icon="'ri-money-dollar-box-line'"-->
<!--                        :value="statices.sponsorsCount"-->
<!--                    />-->
<!--                </div>-->
<!--                <div class="col-xl-3 col-md-6">-->
<!--                    <CrmWidget-->
<!--                        :title="'AttendElections'"-->
<!--                        :icon="'ri-money-dollar-box-line'"-->
<!--                        :value="statices.presenceMemberCount"-->
<!--                    />-->
<!--                </div>-->
<!--                <div class="col-xl-3 col-md-6">-->
<!--                    <CrmWidget-->
<!--                        :title="'voteMemberCount'"-->
<!--                        :icon="'ri-hand-heart-line'"-->
<!--                        :value="statices.voteMemberCount"-->
<!--                    />-->
<!--                </div>-->
<!--                <div class="col-xl-3 col-md-6">-->
<!--                    <CrmWidget-->
<!--                        :title="'nominateMemberCount'"-->
<!--                        :icon="'ri-stack-line'"-->
<!--                        :value="statices.nominateMemberCount"-->
<!--                    />-->
<!--                </div>-->
<!--                &lt;!&ndash; end col &ndash;&gt;-->
<!--            </div>-->
            <!-- end row -->

            <Projections :statices="statices"  />
        </div>

    </Layout>
</template>

<style scoped>
.content-page {
    padding: 70px 15px 5px !important;
}
.dashboard-fluid {
    padding: 0 10px !important;
}
</style>
