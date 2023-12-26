<script>

export default {
    props: ['patient_group_by_blood_type'],
    data() {
        return {
            series: [],
            options: {
                chart: {
                    width: 380,
                    type: 'pie',
                },
                colors: ['#F44336', '#E91E63', '#9C27B0','#065c08', '#1421b9', '#14b990','#aeda3b','#3bafda'],
                fill:{
                    colors:['#F44336', '#E91E63', '#9C27B0','#065c08', '#1421b9', '#14b990','#aeda3b','#3bafda']
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
        patient_group_by_blood_type: {
            handler(newV, old) {
                this.patient_group_by_blood_type.forEach(element => {
                    this.series.push(parseInt(element.count))
                    this.options.labels.push(element.blood)
                });
            }
        }
    }
};
</script>

<template>
    <div class="card">
        <div class="card-body ordinaryMember">

            <h4 class="header-title">{{ $t('general.Classification of patients according to blood types') }}</h4>

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
