<script>
import {formatDateOnly} from "../../../../helper/startDate";
import convertCurrencyToWords from "../../../../helper/mixin/convertCurrencyToWords";
/**
 * Advanced Table component
 */
export default {
    name: "print-subscription",
    props: {
        data_row: {
            default: '',
        },
    },
    mixins:[convertCurrencyToWords],
    data(){
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
      <div id="printInv" class="invoice pl-0 ml-0"
        :style="{ 'direction': $i18n.locale == 'ar' ? 'rtl!important' : '', 'text-align': $i18n.locale == 'ar' ? 'start' : '' }">
        <div class="box pl-0 ml-0">
            <div class="image-header px-0">
                <img src="/images/sulib.png" />
                <h1 class="text-center">
                    <b>{{ $t('general.SulaibikhatClub') }}</b> <br>
                    <span style="margin-top: 7px;font-size: 27px;font-weight: bold;color:#000">
                        <b v-if="data_row.serial"
                            style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{ $t('general.docu') }}
                            {{ data_row.serial ? ($i18n.locale == 'ar' ? data_row.serial.name : data_row.serial.name_e) :
                                "" }}</b>
                        <b v-else style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{ $t('general.Membership subscription receipt voucher')}}</b>
                    </span>

                </h1>
                <div class="doc_box text-center pl-0 ml-0"><b style="font-size: 15px;">{{ $t('general.DocumentNumber') }}</b> <br>
                    {{ data_row.document_no }}</div>

            </div>

            <h3><b>{{ $t('general.Date') }}: {{ formatDate(data_row.date) }}</b>  </h3>
            <div style="display: flex; margin: 0;padding: 0;">
                <h3 class="col-8"><b>{{ $t('general.We got from Mr') }}  : {{ data_row.member.full_name }}</b></h3>
                <h3 class="col-4"><b>{{ $t('general.NoMembership') }} : {{ data_row.member.membership_number }}</b> </h3>
            </div>
            <h3><b>{{ $t('general.According to the cash amount and its amount') }} ( {{ data_row.amount + " " + $t('general.APPKDW')}} ) {{ convert_currency_to_words(data_row.amount) }} {{ $t('general.KDW') + " " + $t('general.Just nothing else') }}</b></h3>

            <h3><b>{{ $t('general.This is the cost of subscribe/annual subscription to the club for up to one year') }} {{ data_row.year }}</b></h3>

            <div style="display: flex;justify-content: space-around;margin: 30px 10px;">
                <span style="margin-top: 7px;font-size: 27px;font-weight: bold;color:#000">
                    <b style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{ $t('general.Accountant') }}</b>
                </span>
                <span style="margin-top: 7px;font-size: 27px;font-weight: bold;color:#000">
                    <b style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{ $t('general.Treasurer') }}</b>
                </span>
            </div>

            <h4 style="margin-top: 120px;color: #000;font-weight: bold">* {{ $t('general.This is an electronic document approved without a signature') }}</h4>
        </div>
        <div class="box mt-5 pl-0 ml-0">
            <div class="image-header ">
                <img src="/images/sulib.png" />
                <h1 class="text-center">
                    <b>{{ $t('general.SulaibikhatClub') }}</b> <br>
                    <span style="margin-top: 7px;font-size: 27px;font-weight: bold;color:#000">
                        <b v-if="data_row.serial"
                            style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{ $t('general.docu') }}
                            {{ data_row.serial ? ($i18n.locale == 'ar' ? data_row.serial.name : data_row.serial.name_e) :
                                "" }}</b>
                        <b v-else style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{ $t('general.Membership subscription receipt voucher')}}</b>
                    </span>

                </h1>
                <div class="doc_box text-center pl-0 ml-0"><b style="font-size: 15px;">{{ $t('general.DocumentNumber') }}</b> <br>
                    {{ data_row.document_no }}</div>

            </div>

            <h3><b>{{ $t('general.Date') }}: {{ formatDate(data_row.date) }}</b>  </h3>
            <div style="display: flex; margin: 0;padding: 0;">
                <h3 class="col-8"><b>{{ $t('general.We got from Mr') }}  : {{ data_row.member.full_name }}</b></h3>
                <h3 class="col-4"><b>{{ $t('general.NoMembership') }} : {{ data_row.member.membership_number }}</b> </h3>
            </div>
            <h3><b>{{ $t('general.According to the cash amount and its amount') }} ( {{ data_row.amount + " " + $t('general.APPKDW')}} ) {{ convert_currency_to_words(data_row.amount) }} {{ $t('general.KDW') + " " + $t('general.Just nothing else') }}</b></h3>

            <h3><b>{{ $t('general.This is the cost of renewal/annual subscription to the club for up to one year') }} {{ data_row.year }}</b></h3>

            <div style="display: flex;justify-content: space-around;margin: 30px 10px;">
                <span style="margin-top: 7px;font-size: 27px;font-weight: bold;color:#000">
                    <b style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{ $t('general.Accountant') }}</b>
                </span>
                <span style="margin-top: 7px;font-size: 27px;font-weight: bold;color:#000">
                    <b style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{ $t('general.Treasurer') }}</b>
                </span>
            </div>

            <h4 style="margin-top: 120px;color: #000;font-weight: bold">* {{ $t('general.This is an electronic document approved without a signature') }}</h4>
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
        border: #6e6b6b solid 1px;
        max-height: 720px;
    }

    .doc_box {
        border: #6e6b6b solid 1px;
        background: #d7d7d7 !important;
        color: #000;
        padding: 10px;
        width: 150px;
        height: 70px;
        print-color-adjust: exact;
    }
}</style>
