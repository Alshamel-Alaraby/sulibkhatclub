<script>

export default {
    props: ['departments_tasks'],
    data() {
        return {
            series: [],
            options: {
                chart: {
                    width: 380,
                    type: 'pie',
                },
                colors: ['#F44336', '#aeda3b', '#9C27B0','#065c08', '#1421b9', '#14b990','#E91E63','#3bafda'],
                fill:{
                    colors:['#F44336', '#aeda3b', '#9C27B0','#065c08', '#1421b9', '#14b990','#E91E63','#3bafda']
                },
                labels: [],
                dataLabels: {
                    enabled: true,
                    offsetX: -6,
                    style: {
                        fontSize: '20px',
                        fontWeight: 'bold',
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]

            }
        };
    },
    watch: {
        departments_tasks: {
            handler(newV, old) {
                this.departments_tasks.forEach(element => {
                    this.series.push(parseInt(element.count))
                    this.options.labels.push(this.$i18n.locale == 'ar' ?  element.name :element.name_e)
                });
            }
        }
    }
};
</script>

<template>
    <div class="card">
        <div class="card-body ordinaryMember">

            <h4 class="header-title">{{ $t('general.Number of tasks for each department') }}</h4>

            <div class="mt-3 text-center" :dir="$i18n.locale == 'ar' ? 'rtl' : 'ltr'">
                <apexchart class="apex-charts" :options="options" height="320" :series="series"></apexchart>
            </div>

        </div>
    </div>
</template>

<style scoped>
.apexcharts-legend-text{
    font-size: 15px;
    font-weight: bold;
}
</style>
