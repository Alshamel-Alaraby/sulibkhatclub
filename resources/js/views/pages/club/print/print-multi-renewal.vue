<script>
import {formatDateOnly} from "../../../../helper/startDate";
import convertToArabicWords from "../../../../helper/mixin/convertCurrencyToWords";

/**
 * Advanced Table component
 */
export default {
    name: "print-multi-renewal",
    props: {
        data_row: {
            default: [],
        },
    },
    mixins: [convertToArabicWords],
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
    <div v-if="data_row.length > 0" id="printInv"
         :style="{ 'direction': $i18n.locale == 'ar' ? 'rtl!important' : '', 'text-align': $i18n.locale == 'ar' ? 'start' : '' }">
        <div class="invoice" v-for="(data, index) in data_row" :key="data.id">
            <div class="box pl-0 ml-0">
                <img src="/images/sulib.png" alt="Watermark Image" class="watermark">
                <div class="image-header px-0">
                    <img src="/images/sulib.png"/>
                    <h1 class="text-center mt-4">
                        <b>{{ $t('general.SulaibikhatClub') }}</b> <br>
                        <span style="margin-top: 7px;font-size: 27px;font-weight: bold;color:#000">
                            <b v-if="data.serial" style="border-bottom:1px solid #717171;padding-bottom:1.5px;">
                                {{ $t('general.MembershipReceiptRenewal') }}
                            </b>
                            <b v-else style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{
                                    $t('general.Membership renewal receipt voucher')
                                }}</b>
                        </span>

                    </h1>
                    <div class="doc_box text-center pl-0 ml-0"><b
                        style="font-size: 15px;">{{ $t('general.DocumentNumber') }}</b> <br>
                        {{ data.serial.perfix}}-{{ data.serial_number }}
                    </div>

                </div>

                <h3><b>{{ $t('general.Date') }}: {{ formatDate(data.date) }}</b></h3>
                <div style="display: flex; margin: 0;padding: 0;">
                    <h3 class="col-8"><b>{{ $t('general.We got from Mr') }} : {{ data.member.full_name }}</b></h3>
                    <h3 class="col-4"><b>{{ $t('general.NoMembership') }} : {{ data.member.membership_number }}</b></h3>
                </div>
                <h3><b>{{ $t('general.According to the cash amount and its amount') }} ( {{
                        data.amount + " " +
                        $t('general.APPKDW')
                    }} ) {{ convertToArabicWords(data.amount) }} {{
                        $t('general.KDW') + " " +
                        $t('general.Just nothing else')
                    }}</b></h3>

                <h3><b>{{
                        $t('general.This is the cost of renewal/annual subscription to the club for up to one year')
                    }} {{
                        data.year
                    }}</b></h3>

                <div style="display: flex;justify-content: space-around;margin: 30px 10px;">
                <span style="display: inline-block; text-align: center;">
                    <span style="margin-top: 7px; font-size: 27px; font-weight: bold; color:#000; display: block;">
                        <b style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{
                                $t('general.Accountant')
                            }}</b>
                    </span>
                    <img src="/images/khatm1.png" style="width: 200px; height: auto; margin: 5px auto 0;">
                </span>

                    <span style="display: flex; align-items: center;">
                    <img src="/images/sulibkhatKhat.png" style="width: 200px; height: auto; margin: 0 10px;">
                </span>
                    <span style="display: inline-block; text-align: center;">
                    <span style="margin-top: 7px; font-size: 27px; font-weight: bold; color:#000; display: block;">
                        <b style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{
                                $t('general.Treasurer')
                            }}</b>
                    </span>
                    <img src="/images/khatm2.png" style="width: 200px; height: auto; margin: 5px auto 0;">
                </span>
                </div>

            </div>
            <div class="box mt-5 pl-0 ml-0">
                <img src="/images/sulib.png" alt="Watermark Image" class="watermark">
                <div class="image-header ">
                    <img src="/images/sulib.png"/>
                    <h1 class="text-center mt-4">
                        <b>{{ $t('general.SulaibikhatClub') }}</b> <br>
                        <span style="margin-top: 7px;font-size: 27px;font-weight: bold;color:#000">
                            <b v-if="data.serial" style="border-bottom:1px solid #717171;padding-bottom:1.5px;">
                                {{ $t('general.MembershipReceiptRenewal') }}
                                </b>
                            <b v-else style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{
                                    $t('general.Membership renewal receipt voucher')
                                }}</b>
                        </span>

                    </h1>
                    <div class="doc_box text-center pl-0 ml-0"><b
                        style="font-size: 15px;">{{ $t('general.DocumentNumber') }}</b>
                        <br>
                        {{ data.serial.perfix}}-{{ data.serial_number }}
                    </div>

                </div>

                <h3><b>{{ $t('general.Date') }}: {{ formatDate(data.date) }}</b></h3>
                <div style="display: flex; margin: 0;padding: 0;">
                    <h3 class="col-8"><b>{{ $t('general.We got from Mr') }} : {{ data.member.full_name }}</b></h3>
                    <h3 class="col-4"><b>{{ $t('general.NoMembership') }} : {{ data.member.membership_number }}</b></h3>
                </div>
                <h3><b>{{ $t('general.According to the cash amount and its amount') }} ( {{
                        data.amount + " " +
                        $t('general.APPKDW')
                    }} ) {{ convertToArabicWords(data.amount) }} {{
                        $t('general.KDW') + " " +
                        $t('general.Just nothing else')
                    }}</b></h3>

                <h3><b>{{
                        $t('general.This is the cost of renewal/annual subscription to the club for up to one year')
                    }} {{
                        data.year
                    }}</b></h3>

                <div style="display: flex;justify-content: space-around;margin: 30px 10px;">
                <span style="display: inline-block; text-align: center;">
                    <span style="margin-top: 7px; font-size: 27px; font-weight: bold; color:#000; display: block;">
                        <b style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{
                                $t('general.Accountant')
                            }}</b>
                    </span>
                    <img src="/images/khatm1.png" style="width: 200px; height: auto; margin: 5px auto 0;">
                </span>

                    <span style="display: flex; align-items: center;">
                    <img src="/images/sulibkhatKhat.png" style="width: 200px; height: auto; margin: 0 10px;">
                </span>
                    <span style="display: inline-block; text-align: center;">
                    <span style="margin-top: 7px; font-size: 27px; font-weight: bold; color:#000; display: block;">
                        <b style="border-bottom:1px solid #717171;padding-bottom:1.5px;">{{
                                $t('general.Treasurer')
                            }}</b>
                    </span>
                    <img src="/images/khatm2.png" style="width: 200px; height: auto; margin: 5px auto 0;">
                </span>
                </div>


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
        font-width: bold;
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
        padding: 0px 40px 0px 30px;
        margin: 0 0 25px 0;
        height: 670px;
        border: #6e6b6b solid 1px;
        position: relative;
    }

    .doc_box {
        border: #6e6b6b solid 1px;
        background: #d7d7d7 !important;
        color: #000;
        padding: 10px;
        font-weight: bold;
        width: 180px;
        height: 80px;
        print-color-adjust: exact;
    }

    .watermark {
        position: absolute;
        opacity: 0.07;
        pointer-events: none;
        height: 100%;
        width: 100%;

    }
}</style>
