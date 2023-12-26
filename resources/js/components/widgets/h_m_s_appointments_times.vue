<script>

export default {
    props: ['peak_appointment_periods'],
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
                        text: this.$t('general.Number of Appointments'),
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
        peak_appointment_periods: {
            handler(newV, old) {
                let data = []
                this.peak_appointment_periods.forEach(element => {
                    data.push({x:element.period_group,y:element.count})
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

            <h4 class="header-title">{{ $t('general.Clarifying peak periods for appointments and reservations') }}</h4>

            <div class="mt-3 text-center" :dir="$i18n.locale == 'ar' ? 'rtl' : 'ltr'">
                <apexchart class="apex-charts" :options="options" height="320" :series="series"></apexchart>
            </div>

        </div>
    </div>
</template>

