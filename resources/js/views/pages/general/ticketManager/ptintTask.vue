<script>
import {formatDateOnly} from "../../../../helper/startDate";
import convertCurrencyToWords from "../../../../helper/mixin/convertCurrencyToWords";
import translation from "../../../../helper/mixin/translation-mixin";

/**
 * Advanced Table component
 */
export default {
    name: "print-tasks",
    props: {
        data_row: {
            default: '',
        },
    },
    mixins: [convertCurrencyToWords,translation],
    data() {
        return {
            printObj: {
                id: "printInv",
            }
        }
    },
    methods: {
        formatDate(value) {
            return formatDateOnly(value);
        },
    }
};
</script>

<template>
    <div id="printInv" class="invoice pl-0 ml-0 d-none"
         :style="{ 'direction': $i18n.locale == 'ar' ? 'rtl!important' : '', 'text-align': $i18n.locale == 'ar' ? 'start' : '' }">
        <div class="box pl-0 ml-0" v-if="Object.keys(data_row ?? []).length">
            <div class="image-header px-0 float-left">
                <img src="/images/trustLogo.jpg"/>
                <h1 class="text-center">
                    <b>{{ $t('general.TrustCompany') }}</b> <br>
                    <span style="margin-top: 7px;font-size: 27px;font-weight: bold;color:#000">
                        <b
                            style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{
                                $t('general.WorkOrder')
                            }}</b>
                    </span>

                </h1>
                <div class="doc_box text-center pl-0 ml-0"><b style="font-size: 15px;">{{
                        $t('general.WorkOrderNumber')
                    }}</b> <br>
                    {{
                        data_row.id
                    }}
                </div>

            </div>

            <h3><b>{{ $t('general.Date') }}: {{ new Date(data_row.created_at).toISOString().slice(0,10) }}</b></h3>
            <div style="display: flex; margin: 0;padding: 0;">
                <h3 class="col-8" v-if="data_row.location"><b>{{ getCompanyKey("boardRent_task_location") }} : {{ $i18n.locale == 'ar' ? data_row.location.name : data_row.location.name_e }}</b></h3>
                <h3 class="col-4" v-if="data_row.department"><b>{{ getCompanyKey("boardRent_task_department")}} : {{ $i18n.locale == 'ar' ? data_row.department.name : data_row.department.name_e }}</b></h3>
            </div>
            <h3><b>{{ getCompanyKey('task_title') }}: {{ data_row.task_title }}</b></h3>


            <h3><b>{{ $t("general.MinistryEngineersNotes") }}: {{ data_row.note }}</b></h3>
            <h3><b>{{ getCompanyKey('actual_execution_end_date') }}: {{ data_row.actual_execution_end_date }}</b></h3>
            <h3><b>{{ getCompanyKey('actual_task_end_time') }}: {{ data_row.actual_end_time }}</b></h3>


            <h3><b>{{ getCompanyKey('boardRent_panel_admin_note') }}</b></h3>
            <div v-html="data_row.admin_note" style="font-size:17px"></div>

            <div style="display: flex;justify-content: space-around;margin: 30px 10px;">
                <span style="margin-top: 7px;font-size: 27px;font-weight: bold;color:#000">
                    <b style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{ $t('general.Technician') }}</b>
                </span>
                <span style="margin-top: 7px;font-size: 27px;font-weight: bold;color:#000">
                    <b style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{ $t('general.recipient') }}</b>
                </span>
                <span style="margin-top: 7px;font-size: 27px;font-weight: bold;color:#000">
                    <b style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{ $t('general.CompanyEngineer') }}</b>
                </span>
                <span style="margin-top: 7px;font-size: 27px;font-weight: bold;color:#000">
                    <b style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{ $t('general.MinistryEngineer') }}</b>
                </span>
            </div>


        </div>
    </div>

</template>

<style scoped>
@media print {
    body {
        -webkit-print-color-adjust: exact;
    }

    h1,
    h2,
    h3,
    p {
        margin: 5px 0;
        color: #333;
    }

    hr {
        border: none;
        border-top: 1px solid #ccc;
        margin: 10px 0;
    }

    h3 {
        font-size: 25px;
        color: #000;
        padding: 4px 2px;
        margin: 15px 0px;
    }

    h3 b {
        background: #d7d7d7 !important;
        print-color-adjust: exact;
        padding: 4px 2px;

    }

    .invoice {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        margin: 0 auto;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    button {
        margin-top: 20px;
    }

    .invoice .center {
        text-align: center;
    }

    .invoice .right {
        text-align: right;
    }

    .image-header {
        width: 100%;
        margin-bottom: 30px;
        padding-left: 0px;
        display: flex;
        justify-content: space-between;
    }

    .image-header img {
        display: inline-block;
        margin-top: 7px;
        height: 100px;
    }

    .image-header h1 {
        display: inline-block;
        margin-top: 10px;
    }

    .box {
        padding: 0px 30px 0px 20px;
        margin: 50px 0;
        height: 49%;
    }

    .doc_box {
        border: #6e6b6b solid 1px;
        text-align: center;
        background: #d7d7d7 !important;
        color: #000;
        padding: 10px;
        width: 150px;
        height: 70px;
        print-color-adjust: exact;
    }
}</style>
