<script>

export default {
    props: ['locations_tasks'],
    data() {
        return {
            series: [],
            options: {

                chart: {
                    height: 350,
                    type: 'rangeArea',
                    animations: {
                        speed: 500
                    }
                },
                colors: ['#000'],
                dataLabels: {
                    enabled: false,
                    style: {
                        fontSize: '20px',
                        fontWeight: 'bold',
                    }
                },
                fill: {
                    opacity: [1]
                },
                forecastDataPoints: {
                    count: 2
                },
                stroke: {
                    curve: 'straight',
                    width: [2]
                },
                legend: {
                    show: true,
                    customLegendItems: [''],
                    inverseOrder: true
                },
                title: {
                    text: ''
                },
                yaxis: {
                    title: {
                        text: this.$t('general.Number of Tasks'),
                    },
                },
                markers: {
                    hover: {
                        sizeOffset: 5
                    }
                }

            }
        };
    },
    watch: {
        locations_tasks: {
            handler(newV, old) {
                let data = []
                this.locations_tasks.forEach(element => {
                    data.push({x:this.$i18n.locale == 'ar' ?  element.name :element.name_e,y:element.count})
                });
                this.series = [
                    {
                        type: 'line',
                        name: '',
                        data: data
                    },

                ]
            }
        }
    }
};
</script>

<template>
    <div class="card">
        <div class="card-body ordinaryMember">

            <h4 class="header-title">{{ $t('general.Number of Tasks for each location') }}</h4>

            <div class="mt-3 text-center" :dir="$i18n.locale == 'ar' ? 'rtl' : 'ltr'">
                <apexchart class="apex-charts" :options="options" height="320" :series="series"></apexchart>
            </div>

        </div>
    </div>
</template>

