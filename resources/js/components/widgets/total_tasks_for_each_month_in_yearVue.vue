<script>

export default {
    props: ['total_tasks_for_each_month_in_year'],
    data() {
        return {
            series: [],
            options: {

                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false,
                    style:{
                        fontSize: '20px',
                        fontWeight: 'bold',
                        colors:['#1421b9']
                    }
                },
                colors:['#1421b9'],

                stroke: {
                    curve: 'straight'
                },
                title: {
                    align: 'left'
                },
                yaxis: {
                    title: {
                        text: this.$t('general.Number of Tasks'),
                    },
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: [this.$t('month.1'), this.$t('month.2'), this.$t('month.3'), this.$t('month.4'), this.$t('month.5'), this.$t('month.6'), this.$t('month.7'), this.$t('month.8'), this.$t('month.9'), this.$t('month.10'), this.$t('month.11'), this.$t('month.12')],
                }
            }
        };
    },
    watch: {
        total_tasks_for_each_month_in_year: {
            handler(newV, old) {
                console.log(newV)
                setTimeout(() => {
                    let monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',];
                    let current = []
                    monthNames.forEach((month) => {
                        let check_exists = 0
                        this.total_tasks_for_each_month_in_year.forEach(element => {
                            if (element.month == month) {
                                check_exists = 1
                                current.push(element.total)
                            }
                        });
                        if (check_exists == 0)
                            current.push(0)
                        check_exists = 0

                    })
                    this.series = [
                        { data: current }
                    ]
                }, 500)
            }
        }
    }
};
</script>

<template>
    <div class="card">
        <div class="card-body ordinaryMember">

            <h4 class="header-title">{{ $t('general.Total Tasks for each month in the year') }}</h4>

            <div class="mt-3 text-center" :dir="$i18n.locale == 'ar' ? 'rtl' : 'ltr'">
                <apexchart class="apex-charts" :options="options" height="320" :series="series"></apexchart>
            </div>

        </div>
    </div>
</template>

