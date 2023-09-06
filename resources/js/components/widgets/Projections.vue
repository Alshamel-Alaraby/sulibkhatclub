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
                labels: [this.$t('general.voteMemberCount'), this.$t('general.AttendElections'), this.$t('general.nominateMemberCount')],
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
                labels: [this.$t('general.voteMemberCount'), this.$t('general.AttendElections'), this.$t('general.nominateMemberCount')],
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
                labels: [this.$t('general.voteMemberCount'), this.$t('general.AttendElections'), this.$t('general.nominateMemberCount')],
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
            this.series1 = [newDa.voteNormalMemberPercentage,newDa.presenceNormalMemberCount,newDa.nominateNormalMemberPercentage];
            this.series2 = [newDa.voteFoundingMemberPercentage,newDa.presenceFoundingMemberPercentage,newDa.nominateFoundingMemberPercentage]
            this.series3 = [newDa.voteDismissedMemberPercentage,newDa.presenceDismissedMemberPercentage,newDa.nominateDismissedMemberPercentage]
        }
    }
};
</script>

<template>
    <div class="row">
        <!-- end col -->
        <div class="col-xl-4">
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
                            <p class="text-muted font-15 mb-1 text-truncate">{{ $t('general.voteMemberCount') }}</p>
                            <h4>{{ statices.voteNormalMemberCount }}</h4>
                        </div>
                        <div class="col-4">
                            <p class="text-muted font-15 mb-1 text-truncate">{{ $t('general.AttendElections') }}</p>
                            <h4><i class="fe-arrow-up text-success"></i> {{ statices.AttendElections }}</h4>
                        </div>
                        <div class="col-4">
                            <p class="text-muted font-15 mb-1 text-truncate">{{ $t('general.nominateMemberCount') }}</p>
                            <h4><i class="fe-arrow-down text-danger"></i> {{ statices.nominateMemberCount }}</h4>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- end col -->

        <!-- end col -->
        <div class="col-xl-4">
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
                            <p class="text-muted font-15 mb-1 text-truncate">{{ $t('general.voteFoundingMemberCount') }}</p>
                            <h4>{{ statices.voteNormalMemberCount }}</h4>
                        </div>
                        <div class="col-4">
                            <p class="text-muted font-15 mb-1 text-truncate">{{ $t('general.AttendElections') }}</p>
                            <h4><i class="fe-arrow-up text-success"></i> {{ statices.presenceFoundingMemberCount }}</h4>
                        </div>
                        <div class="col-4">
                            <p class="text-muted font-15 mb-1 text-truncate">{{ $t('general.nominateMemberCount') }}</p>
                            <h4><i class="fe-arrow-down text-danger"></i> {{ statices.nominateFoundingMemberCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->

        <!-- end col -->
        <div class="col-xl-4">
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
                            <p class="text-muted font-15 mb-1 text-truncate">{{ $t('general.voteDismissedMemberCount') }}</p>
                            <h4>{{ statices.voteNormalMemberCount }}</h4>
                        </div>
                        <div class="col-4">
                            <p class="text-muted font-15 mb-1 text-truncate">{{ $t('general.AttendElections') }}</p>
                            <h4><i class="fe-arrow-up text-success"></i> {{ statices.presenceDismissedMemberCount }}</h4>
                        </div>
                        <div class="col-4">
                            <p class="text-muted font-15 mb-1 text-truncate">{{ $t('general.nominateMemberCount') }}</p>
                            <h4><i class="fe-arrow-down text-danger"></i> {{ statices.nominateDismissedMemberCount }}</h4>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- end col -->

        <!-- end col -->
        <!--        <div class="col-xl-4">-->
        <!--            <div class="card">-->
        <!--                <div class="card-body">-->

        <!--                    <h4 class="header-title">{{ $t('general.allSponer') }}</h4>-->

        <!--                    <div class="mt-3 text-center" dir="ltr">-->
        <!--                        <apexchart-->
        <!--                            class="apex-charts"-->
        <!--                            type="donut"-->
        <!--                            :options="chartOptions4"-->
        <!--                            height="312"-->
        <!--                            :series="series4"-->
        <!--                        >-->
        <!--                        </apexchart>-->
        <!--                    </div>-->

        <!--                    <div class="row mt-3">-->
        <!--                        <div class="col-4">-->
        <!--                            <p class="text-muted font-15 mb-1 text-truncate">Target</p>-->
        <!--                            <h4>$8712</h4>-->
        <!--                        </div>-->
        <!--                        <div class="col-4">-->
        <!--                            <p class="text-muted font-15 mb-1 text-truncate">Last week</p>-->
        <!--                            <h4><i class="fe-arrow-up text-success"></i> $523</h4>-->
        <!--                        </div>-->
        <!--                        <div class="col-4">-->
        <!--                            <p class="text-muted font-15 mb-1 text-truncate">Last Month</p>-->
        <!--                            <h4><i class="fe-arrow-down text-danger"></i> $965</h4>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!-- end col -->

    </div>
    <!-- end row -->
    <!-- end card-box -->
</template>
