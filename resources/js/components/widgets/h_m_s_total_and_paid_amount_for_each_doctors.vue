<script>

export default {
    props: ['total_and_paid_amount_for_each_doctors'],
    data() {
        return {
            series: [],
            options: {
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false,
                    style:{
                        fontSize: '20px',
                        fontWeight: 'bold',
                    }
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: [],
                },
                yaxis: {
                    title: {
                        text: this.$t('general.KD')
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return   val
                        }
                    }
                }

            }
        };
    },
    watch: {
        total_and_paid_amount_for_each_doctors: {
            handler(newV, old) {
                this.series = [
                    { data: [], name: this.$t("general.Total Amount"), },
                    { data: [], name: this.$t("general.paid") }
                ]
                let doctors_names = []
                this.total_and_paid_amount_for_each_doctors.forEach(element => {
                    this.series[0].data.push(element.total_amount)
                    this.series[1].data.push(element.total_paid)
                    doctors_names.push(this.$i18n.locale == 'ar' ? element.name : element.name_e)
                });

                this.options.xaxis.categories = doctors_names

            }
        }
    }
};
</script>

<template>
    <b-modal id="h_m_s_total_and_paid_amount_for_each_doctors" title-class="font-18" size="xlll" body-class="p-4 "
        :hide-footer="true" :title="$t('general.Total and paid amount for each doctor')">
        <div class="card">
            <div class="card-body ordinaryMember">
                <div class="mt-3 text-center" :dir="$i18n.locale == 'ar' ? 'rtl' : 'ltr'">
                    <apexchart class="apex-charts" :options="options" height="600" :series="series"></apexchart>
                </div>

            </div>
        </div>
    </b-modal>
</template>

