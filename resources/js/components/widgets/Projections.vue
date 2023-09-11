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
                labels: [
                    this.$t('general.Deletedathisrequest'),this.$t('general.Cancellationduetodeath'),
                    this.$t('general.Cancellationaccordingtotheministerialdecision'),this.$t('general.WrittenoffaccordingtotheAuthorityletter'),
                    this.$t('general.Writtenofffornonpayment')
                ],
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
            series6: [],
            chartOptions6: {
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
                labels: [
                    this.$t('general.Deletedathisrequest'),this.$t('general.Cancellationduetodeath'),
                    this.$t('general.Cancellationaccordingtotheministerialdecision'),this.$t('general.WrittenoffaccordingtotheAuthorityletter'),
                    this.$t('general.Writtenofffornonpayment')
                ],
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
            }
            series7: [],
            chartOptions7: {
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
                labels: [
                    this.$t('general.Deletedathisrequest'),this.$t('general.Cancellationduetodeath'),
                    this.$t('general.Cancellationaccordingtotheministerialdecision'),this.$t('general.WrittenoffaccordingtotheAuthorityletter'),
                    this.$t('general.Writtenofffornonpayment')
                ],
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
            }
            series8: [],
            chartOptions8: {
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
                labels: [
                    this.$t('general.Deletedathisrequest'),this.$t('general.Cancellationduetodeath'),
                    this.$t('general.Cancellationaccordingtotheministerialdecision'),this.$t('general.WrittenoffaccordingtotheAuthorityletter'),
                    this.$t('general.Writtenofffornonpayment')
                ],
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
            }
            series9: [],
            chartOptions9: {
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
                labels: [
                    this.$t('general.Deletedathisrequest'),this.$t('general.Cancellationduetodeath'),
                    this.$t('general.Cancellationaccordingtotheministerialdecision'),this.$t('general.WrittenoffaccordingtotheAuthorityletter'),
                    this.$t('general.Writtenofffornonpayment')
                ],
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
            }
        };
    },
    props: ['statices'],
    watch:{
        statices(newDa,old){
            newDa.member_types_data.forEach((el,index) => {
                this.series1.push(el.percentage);
                this.chartOptions1.labels.push(this.$i18n.locale == 'ar'?el.name: el.name_e);
                this.chartOptions1.colors.push(this.colors[index]);
            });
            newDa.financial_statuses_data.forEach((el,index) => {
                this.series3.push(el.percentage);
                this.chartOptions3.labels.push(this.$i18n.locale == 'ar'?el.name: el.name_e);
                this.chartOptions3.colors.push(this.colors[index]);
            });
            newDa.member_permissions_data.forEach((el,index) => {
                this.series2.push(el.percentage);
                this.chartOptions2.labels.push(this.$i18n.locale == 'ar'?el.name: el.name_e);
                this.chartOptions2.colors.push(this.colors[index]);
            });
            newDa.member_types.forEach((el,index) => {
                this.series4.push(el.percentage);
                this.chartOptions4.labels.push(this.$i18n.locale == 'ar'?el.name: el.name_e);
                this.chartOptions4.colors.push(this.colors[index]);
            });
            this.series5 = [(newDa.Percentage_6 + .01),newDa.Percentage_7,newDa.Percentage_8,newDa.Percentage_9,newDa.Percentage_18];
        }
    }
};
</script>

<template>
    <div>
        <h1 class="header-title text-center my-3">{{ $t('general.Ordinarymembers') }}</h1>
        <div class="row">
            <!-- end col -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">

                        <h4 class="header-title">{{ $t('general.memberType') }}</h4>

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
                            <div class="col-4" v-for="item in statices.member_types_data">
                                <p class="font-15 mb-1 text-truncate">{{ $i18n.locale == 'ar' ?item.name:item.name_e }}</p>
                                <h4>{{ item.count }}</h4>
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
                            <div class="col-4" v-for="item in statices.financial_statuses_data">
                                <p class="font-15 mb-1 text-truncate">{{ $i18n.locale == 'ar' ?item.name:item.name_e }}</p>
                                <h4>{{ item.count }}</h4>
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
                            <div class="col-4" v-for="item in statices.member_permissions_data">
                                <p class="font-15 mb-1 text-truncate">{{ $i18n.locale == 'ar' ?item.name:item.name_e }}</p>
                                <h4>{{ item.count }}</h4>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end col -->

        </div>

        <h1 class="header-title text-center my-3">{{ $t('general.dismissedMemberCount') }}</h1>
        <div class="row">
            <!-- end col -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">

                        <h4 class="header-title">{{ $t('general.memberType') }}</h4>

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
                            <div class="col-4" v-for="item in statices.member_types">
                                <p class="font-15 mb-1 text-truncate">{{ $i18n.locale == 'ar' ?item.name:item.name_e }}</p>
                                <h4>{{ item.count }}</h4>
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
                                :options="chartOptions5"
                                height="312"
                                :series="series5"
                            >
                            </apexchart>
                        </div>

                        <div class="row mt-3">
                            <div class="col-4">
                                <p class="text-muted font-15 mb-1 text-truncate">{{$t('general.Deletedathisrequest')}}</p>
                                <h4> {{ statices.count_6 }} </h4>
                            </div>
                            <div class="col-4">
                                <p class="text-muted font-15 mb-1 text-truncate">{{$t('general.Cancellationduetodeath')}}</p>
                                <h4>{{ statices.count_7 }}</h4>
                            </div>
                            <div class="col-4">
                                <p class="text-muted font-15 mb-1 text-truncate">{{$t('general.Cancellationaccordingtotheministerialdecision')}}</p>
                                <h4>{{ statices.count_8 }}</h4>
                            </div>
                            <div class="col-4">
                                <p class="text-muted font-15 mb-1 text-truncate">{{$t('general.WrittenoffaccordingtotheAuthorityletter')}}</p>
                                <h4>{{ statices.count_9 }}</h4>
                            </div>
                            <div class="col-4">
                                <p class="text-muted font-15 mb-1 text-truncate">{{$t('general.Writtenofffornonpayment')}}</p>
                                <h4>{{ statices.count_18 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>

        <h1 class="header-title text-center my-3">{{ $t('general.sponsor') }}</h1>
        <div class="row">
            <!-- end col -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">

                        <h4 class="header-title">{{ $t('general.group') }}</h4>

                        <div class="mt-3 text-center" dir="ltr">
                            <apexchart
                                class="apex-charts"
                                type="donut"
                                :options="chartOptions6"
                                height="312"
                                :series="series6"
                            >
                            </apexchart>
                        </div>

                        <div class="row mt-3">
                            <div class="col-4" v-for="item in statices.member_types">
                                <p class="font-15 mb-1 text-truncate">{{ $i18n.locale == 'ar' ?item.name:item.name_e }}</p>
                                <h4>{{ item.count }}</h4>
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

                        <h4 class="header-title">{{ $t('general.group') }}</h4>

                        <div class="mt-3 text-center" dir="ltr">
                            <apexchart
                                class="apex-charts"
                                type="donut"
                                :options="chartOptions7"
                                height="312"
                                :series="series7"
                            >
                            </apexchart>
                        </div>

                        <div class="row mt-3">
                            <div class="col-4" v-for="item in statices.member_types">
                                <p class="font-15 mb-1 text-truncate">{{ $i18n.locale == 'ar' ?item.name:item.name_e }}</p>
                                <h4>{{ item.count }}</h4>
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

                        <h4 class="header-title">{{ $t('general.group') }}</h4>

                        <div class="mt-3 text-center" dir="ltr">
                            <apexchart
                                class="apex-charts"
                                type="donut"
                                :options="chartOptions8"
                                height="312"
                                :series="series8"
                            >
                            </apexchart>
                        </div>

                        <div class="row mt-3">
                            <div class="col-4" v-for="item in statices.member_types">
                                <p class="font-15 mb-1 text-truncate">{{ $i18n.locale == 'ar' ?item.name:item.name_e }}</p>
                                <h4>{{ item.count }}</h4>
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

                        <h4 class="header-title">{{ $t('general.group') }}</h4>

                        <div class="mt-3 text-center" dir="ltr">
                            <apexchart
                                class="apex-charts"
                                type="donut"
                                :options="chartOptions9"
                                height="312"
                                :series="series9"
                            >
                            </apexchart>
                        </div>

                        <div class="row mt-3">
                            <div class="col-4" v-for="item in statices.member_types">
                                <p class="font-15 mb-1 text-truncate">{{ $i18n.locale == 'ar' ?item.name:item.name_e }}</p>
                                <h4>{{ item.count }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
    </div>
    <!-- end row -->
    <!-- end card-box -->
</template>

<style scoped>
.card-body {
    background-color: #bee3fe !important;
}

h1 {
    font-size: 30px;
}

.card-body {
    color: #000 !important;
}
</style>
