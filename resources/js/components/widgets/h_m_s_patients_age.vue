<script>

export default {
    props: ['patient_group_by_age'],
    data() {
        return {
            series: [],
            options: {

                chart: {
                    type: "bar",
                    height: 440,
                    stacked: true,
                },
                colors: ["#008FFB", "#FF4560"],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: "80%",
                    },
                },
                dataLabels: {
                    enabled: false,
                    style: {
                        fontSize: '20px',
                        fontWeight: 'bold',
                    }
                },
                stroke: {
                    width: 1,
                    colors: ["#fff"],
                },

                grid: {
                    xaxis: {
                        lines: {
                            show: false,
                        },
                    },
                },
                yaxis: {
                    title: {
                        text: this.$t('general.Age'),
                    },
                },
                tooltip: {
                    shared: false,
                    x: {
                        formatter: function (val) {
                            return val;
                        },
                    },
                    y: {
                        formatter: function (val) {
                            return Math.abs(val) ;
                        },
                    },
                },
                title: {
                    text: '',
                },
                xaxis: {
                    categories: [

                    ],
                    title: {
                        text: this.$t('general.The Number'),
                    },
                    labels: {
                        formatter: function (val) {
                            return Math.abs(Math.round(val)) ;
                        },
                    },
                },
            }
        };
    },
    watch:{
        patient_group_by_age:{
            handler(newV,old){
                let max = 0
                let males_data= []
                let females_data= []
                this.patient_group_by_age.forEach(element => {
                    if(!this.options.xaxis.categories.includes(element.age_group))
                        this.options.xaxis.categories.push(element.age_group)
                    if(element.gender =='Male')
                        males_data.push(element.count)
                    else
                        females_data.push(-element.count)

                    if(element.count > max)
                        max = element.count

                });

                this.options.yaxis.min = -1*max
                this.options.yaxis.max = max

                this.series=  [
                    {
                        name: this.$t("general.Males") ,
                        data: males_data,
                    },
                    {
                        name: this.$t("general.Females"),
                        data: females_data,
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

            <h4 class="header-title">{{ $t('general.Classification of patients according to age')}}</h4>

            <div class="mt-3 text-center" :dir="$i18n.locale == 'ar' ? 'rtl' : 'ltr'">
                <apexchart class="apex-charts" :options="options" height="320" :series="series"></apexchart>
            </div>

        </div>
    </div>
</template>

