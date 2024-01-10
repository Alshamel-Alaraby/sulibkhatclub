<script>

export default {
    props: ['tasks_for_current_month', 'tasks_for_last_month'],
    data() {
        return {
            series: [],
            options: {
                chart: {
                    type: 'bar',
                    height: 320
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        dataLabels: {
                            position: 'top',
                        },
                    }
                },
                dataLabels: {
                    enabled: false,
                    offsetX: -6,
                    style: {
                        fontSize: '20px',
                        fontWeight: 'bold',
                        colors: ['#fff']
                    }
                },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['#fff']
                },
                tooltip: {
                    shared: true,
                    intersect: false
                },
                yaxis: {
                    title: {
                        text: this.$t('general.Number of Tasks'),
                    },
                },
                xaxis: {
                    categories: [this.$t('general.First Week'), this.$t('general.Second Week'), this.$t('general.Third Week'), this.$t('general.Fourth Week'), this.$t('general.Fifth Week')],
                },

            }
        };
    },
    watch: {
        tasks_for_current_month: {
            handler(newV, old) {
                setTimeout(() => {
                    let weeks = ['First Week', 'Second Week', 'Third Week', 'Fourth Week', 'Fifth Week'];
                    let current = []
                    let last = []
                    weeks.forEach((week) => {
                        let check_exists = 0
                        this.tasks_for_current_month.forEach(element => {
                            if (element.week == week) {
                                check_exists = 1
                                current.push(element.count)
                            }
                        });
                        if (check_exists == 0)
                            current.push(0)
                        check_exists = 0

                        this.tasks_for_last_month.forEach(element => {
                            if (element.week == week) {
                                check_exists = 1
                                last.push(element.count)
                            }
                        });
                        if (check_exists == 0)
                            last.push(0)

                    })
                    this.series = [
                        { data: current, name: this.$t("general.Current Month"), },
                        { data: last, name: this.$t("general.Last Month") }
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

            <h4 class="header-title">{{ $t('general.Number of tasks for this month and last month for each week') }}</h4>

            <div class="mt-3 text-center" :dir="$i18n.locale == 'ar' ? 'rtl' : 'ltr'">
                <apexchart class="apex-charts" :options="options" height="320" :series="series"></apexchart>
            </div>

        </div>
    </div>
</template>

