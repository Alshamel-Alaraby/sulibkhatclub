<script>
/**
 * Projections component
 */
export default {
    data() {
        return {
            colors: ["#3bafda", "#1abc9c", "#f7b84b","#675aa9","#f1556c","#fd7e14"],
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
                labels: [],
                colors: [],
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
                labels: [],
                colors: [],
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
                labels: [],
                colors: [],
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
                labels: [],
                colors: [],
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
            series5: [],
            chartOptions5: {
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
                labels: [],
                colors: [],
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
            this.series1 = [];
            this.series3 = [];
            this.series2 = [];

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
                    </div>

                </div>
            </div>
        </div>
        <!-- end col -->

        <!-- end col -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">

                    <h4 class="header-title">{{ $t('general.Typesofpayment') }}</h4>

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
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->

        <!-- end col -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">

                    <h4 class="header-title">{{ $t('general.Memberrights') }}</h4>

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
                                    :options="chartOptions4"
                                    height="312"
                                    :series="series4"
                                >
                                </apexchart>
                            </div>

                            <div class="row mt-3">
                                <div class="col-4">
                                    <p class="text-muted font-15 mb-1 text-truncate">Target</p>
                                    <h4>$8712</h4>
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

                    <h4 class="header-title">{{ $t('general.Reasonsfordeletion') }}</h4>

                    <div class="mt-3 text-center" dir="ltr">
                        <apexchart
                            class="apex-charts"
                            type="donut"
                            :options="chartOptions4"
                            height="312"
                            :series="series4"
                        >
                        </apexchart>
                    </div>

                    <div class="row mt-3">
                        <div class="col-4">
                            <p class="text-muted font-15 mb-1 text-truncate">Target</p>
                            <h4>$8712</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
