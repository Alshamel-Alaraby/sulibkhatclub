<script>
/**
 * Projections component
 */
export default {
    data() {
        return {
            series1: [],
            chartOptions1: {
                legend: {
                    show: true,
                    position: "bottom",
                    horizontalAlign: "center",
                    verticalAlign: "middle",
                    floating: false,
                    fontSize: "14px",
                    offsetX: 0,
                    offsetY: 7
                },
                labels: [this.$t('general.InTheListOfMembers'), this.$t('general.stopped'), this.$t('general.HeHasTheRightToRun'), this.$t('general.HeHasTheRightToVote'), this.$t('general.HeHasNoRightToVote'), this.$t('general.PaidPersonHasNoRightToAttend')],
                colors: ["#3bafda", "#1abc9c", "#f7b84b","#675aa9","#f1556c","#fd7e14"],
                responsive: [{
                    breakpoint: 600,
                    options: {
                        chart: {
                            height: 240
                        },
                        legend: {
                            show: false
                        }
                    }
                }]
            },
            series3: [],
            chartOptions3: {
                legend: {
                    show: true,
                    position: "bottom",
                    horizontalAlign: "center",
                    verticalAlign: "middle",
                    floating: false,
                    fontSize: "14px",
                    offsetX: 0,
                    offsetY: 7
                },
                labels: [this.$t('general.DeletedAtHisRequest'), this.$t('general.CancellationDueToDeath'), this.$t('general.CancellationPursuantToMinisterialDecision'),this.$t('general.CancellationAccordingToTheAuthoritySLetter'), this.$t('general.WriteOffForNonPayment')],
                colors: ["#3bafda", "#1abc9c", "#f7b84b","#675aa9","#f1556c"],
                responsive: [{
                    breakpoint: 600,
                    options: {
                        chart: {
                            height: 240
                        },
                        legend: {
                            show: false
                        }
                    }
                }]
            },
            series2: [],
            chartOptions2: {
                legend: {
                    show: true,
                    position: "bottom",
                    horizontalAlign: "center",
                    verticalAlign: "middle",
                    floating: false,
                    fontSize: "14px",
                    offsetX: 0,
                    offsetY: 7
                },
                labels: [this.$t('general.FoundingMemberDetail'), this.$t('general.HeHasTheRightToRunAndVoteDetail')],
                colors: ["#3bafda", "#1abc9c"],
                responsive: [{
                    breakpoint: 600,
                    options: {
                        chart: {
                            height: 240
                        },
                        legend: {
                            show: false
                        }
                    }
                }]
            },
            series4: [],
            chartOptions4: {
                legend: {
                    show: true,
                    position: "bottom",
                    horizontalAlign: "center",
                    verticalAlign: "middle",
                    floating: false,
                    fontSize: "14px",
                    offsetX: 0,
                    offsetY: 7
                },
                labels: ["Direct", "Affilliate", "Sponsored"],
                colors: ["#3bafda", "#1abc9c", "#f7b84b"],
                responsive: [{
                    breakpoint: 600,
                    options: {
                        chart: {
                            height: 240
                        },
                        legend: {
                            show: false
                        }
                    }
                }]
            }
        };
    },
    props: ['statices'],
    watch:{
        statices(newDa,old){
            this.series1 = [newDa.InTheListOfMembersPercentage,newDa.stoppedPercentage,newDa.HeHasTheRightToRunPercentage,newDa.HeHasTheRightToVotePercentage,newDa.HeHasNoRightToVotePercentage,newDa.PaidPersonHasNoRightToAttendPercentage];
            this.series3 = [newDa.DeletedAtHisRequestPercentage,newDa.CancellationDueToDeathPercentage,newDa.CancellationPursuantToMinisterialDecisionPercentage,newDa.CancellationAccordingToTheAuthoritySLetterPercentage,newDa.WriteOffForNonPaymentPercentage];
            this.series2 = [newDa.FoundingMemberDetailPercentage,newDa.HeHasTheRightToRunAndVoteDetailPercentage];

        }
    }
};
</script>

<template>
    <div class="row">
        <!-- end col -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">

                    <h4 class="header-title">{{ $t('general.Ordinarymembers') }}</h4>

                    <div class="mt-3 text-center" dir="ltr">
                        <apexchart
                            class="apex-charts"
                            type="donut"
                            :options="chartOptions1"
                            height="312"
                            :series="series1"
                        >
                        </apexchart>
                    </div>

                    <div class="row mt-3">
                        <div class="col-4">
                            <p class="font-15 mb-1 text-truncate">{{ $t('general.InTheListOfMembers') }}</p>
                            <h4>{{ statices.InTheListOfMembers }}</h4>
                        </div>
                        <div class="col-4">
                            <p class="font-15 mb-1 text-truncate">{{ $t('general.stopped') }}</p>
                            <h4>{{ statices.stopped }}</h4>
                        </div>
                        <div class="col-4">
                            <p class=" font-15 mb-1 text-truncate">{{ $t('general.HeHasTheRightToRun') }}</p>
                            <h4>{{ statices.HeHasTheRightToRun }}</h4>
                        </div>
                        <div class="col-4">
                            <p class="font-15 mb-1 text-truncate">{{ $t('general.HeHasTheRightToVote') }}</p>
                            <h4>{{ statices.HeHasTheRightToVote }}</h4>
                        </div>
                        <div class="col-4">
                            <p class="font-15 mb-1 text-truncate">{{ $t('general.HeHasNoRightToVote') }}</p>
                            <h4>{{ statices.HeHasNoRightToVote }}</h4>
                        </div>
                        <div class="col-4">
                            <p class="font-15 mb-1 text-truncate">{{ $t('general.PaidPersonHasNoRightToAttend') }}</p>
                            <h4>{{ statices.PaidPersonHasNoRightToAttend }}</h4>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- end col -->

        <!-- end col -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">

                    <h4 class="header-title">{{ $t('general.dismissedMemberCount') }}</h4>

                    <div class="mt-3 text-center" dir="ltr">
                        <apexchart
                            class="apex-charts"
                            type="donut"
                            :options="chartOptions3"
                            height="312"
                            :series="series3"
                        >
                        </apexchart>
                    </div>

                    <div class="row mt-3">
                        <div class="col-4">
                            <p class="font-15 mb-1 text-truncate">{{ $t('general.DeletedAtHisRequest') }}</p>
                            <h4>{{ statices.DeletedAtHisRequest }}</h4>
                        </div>
                        <div class="col-4">
                            <p class="ont-15 mb-1 text-truncate">{{ $t('general.CancellationDueToDeath') }}</p>
                            <h4> {{ statices.CancellationDueToDeath }}</h4>
                        </div>
                        <div class="col-4">
                            <p class="font-15 mb-1 text-truncate">{{ $t('general.CancellationPursuantToMinisterialDecision') }}</p>
                            <h4>{{ statices.CancellationPursuantToMinisterialDecision }}</h4>
                        </div>
                        <div class="col-4">
                            <p class="font-15 mb-1 text-truncate">{{ $t('general.CancellationAccordingToTheAuthoritySLetter') }}</p>
                            <h4>{{ statices.CancellationAccordingToTheAuthoritySLetter }}</h4>
                        </div>
                        <div class="col-4">
                            <p class="font-15 mb-1 text-truncate">{{ $t('general.WriteOffForNonPayment') }}</p>
                            <h4>{{ statices.WriteOffForNonPayment }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->

        <!-- end col -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">

                    <h4 class="header-title">{{ $t('general.Foundingmembers') }}</h4>

                    <div class="mt-3 text-center" dir="ltr">
                        <apexchart
                            class="apex-charts"
                            type="donut"
                            :options="chartOptions2"
                            height="312"
                            :series="series2"
                        >
                        </apexchart>
                    </div>

                    <div class="row mt-3">
                        <div class="col-4">
                            <p class="font-15 mb-1 text-truncate">{{ $t('general.FoundingMemberDetail') }}</p>
                            <h4>{{ statices.FoundingMemberDetail }}</h4>
                        </div>
                        <div class="col-4">
                            <p class="font-15 mb-1 text-truncate">{{ $t('general.HeHasTheRightToRunAndVoteDetail') }}</p>
                            <h4> {{ statices.HeHasTheRightToRunAndVoteDetail }}</h4>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- end col -->

        <!-- end col -->
<!--                <div class="col-xl-4">-->
<!--                    <div class="card">-->
<!--                        <div class="card-body">-->

<!--                            <h4 class="header-title">{{ $t('general.allSponer') }}</h4>-->

<!--                            <div class="mt-3 text-center" dir="ltr">-->
<!--                                <apexchart-->
<!--                                    class="apex-charts"-->
<!--                                    type="donut"-->
<!--                                    :options="chartOptions4"-->
<!--                                    height="312"-->
<!--                                    :series="series4"-->
<!--                                >-->
<!--                                </apexchart>-->
<!--                            </div>-->

<!--                            <div class="row mt-3">-->
<!--                                <div class="col-4">-->
<!--                                    <p class="text-muted font-15 mb-1 text-truncate">Target</p>-->
<!--                                    <h4>$8712</h4>-->
<!--                                </div>-->
<!--                                <div class="col-4">-->
<!--                                    <p class="text-muted font-15 mb-1 text-truncate">Last week</p>-->
<!--                                    <h4><i class="fe-arrow-up text-success"></i> $523</h4>-->
<!--                                </div>-->
<!--                                <div class="col-4">-->
<!--                                    <p class="text-muted font-15 mb-1 text-truncate">Last Month</p>-->
<!--                                    <h4><i class="fe-arrow-down text-danger"></i> $965</h4>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
        <!-- end col -->

    </div>
    <!-- end row -->
    <!-- end card-box -->
</template>

<style scoped>
.card-body {
    background-color: #bee3fe !important;
}

.card-body {
    color: #000 !important;
}
</style>
