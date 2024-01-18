<script>
import {formatDateOnly} from "../../../../helper/startDate";

/**
 * Advanced Table component
 */
export default {
    name: "print-member-reject",
    props: {
        data_row: {
            default: '',
        },
    },
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
    <div id="printInv" class="invoice">
        <img src="/images/sulib.png" alt="Watermark Image" class="watermark">
        <div class="image-header">
            <img src="/images/sulib.png" alt="Sulibikhat Club Logo"/>
            <h1>{{ $t('general.SulaibikhatClub') }}</h1>
        </div>
        <div>
            <div>
                <h2 v-if="data_row.serial">{{ $t('general.docu') }}
                    {{
                        data_row.serial ? ($i18n.locale == 'ar' ? data_row.serial.name : data_row.serial.name_e) : ""
                    }}</h2>
                <h2 v-else>{{ $t('general.ReceiptVoucher') }}</h2>
                <hr>
                <div>
                    <p>{{ $t('general.Date') }} : {{ formatDate(data_row.date) }}</p>
                    <p>{{ data_row.prefix }} : {{ $t('general.serial_number') }}</p>
                </div>
                <hr>
                <div class="info-right">
                    <h3>{{ $t('general.TheMember') }}</h3>
                    <p>{{ $t('general.memberName') }} : {{ data_row.member.full_name }}</p>
                    <p>{{ $t('general.MembershipNo') }} : {{ data_row.member.membership_number }}</p>
                </div>
                <hr>
                <div class="info-right">
                    <h3>{{ $t('general.theDetails') }}</h3>
                    <p>{{ $t('general.sessionNumber') }} : {{ data_row.session_number }}</p>
                    <p>{{ $t('general.reason') }} :
                        {{
                            data_row.discharge_reson ? ($i18n.locale == 'ar' ? data_row.discharge_reson.name : data_row.discharge_reson.name_e) : ""
                        }}</p>
                    <p>{{ $t('general.Authority') }} :
                        {{ data_row.entity ? $t('general.' + data_row.entity) : '---' }}</p>
                    <p>{{ $t('general.note') }} : {{ data_row.note }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style>

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.invoice {
    width: 100vw;
    height: 100vh;
    padding: 20px;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
    display: compact;
    justify-content: center;
    align-items: center;
}

.image-header {
    text-align: center;
    margin-bottom: 20px;
}

.image-header img {
    max-width: 200px;
    height: auto;
}


h1 {
    font-size: 53px;
    margin-bottom: 20px;
    font-weight: bold;
}

h2 {
    font-size: 42px;
    margin-bottom: 10px;
    text-align: center;
    font-weight: bold;
}

h3 {
    font-size: 28px;
    margin-bottom: 10px;
    font-weight: bold;
}

p {
    font-size: 35px;
    margin-bottom: 5px;
    text-align: right;
}

hr {
    border: 2px solid #ccc;
    margin: 40px 0;
}


.info-right {
    flex: 1;
    padding-left: 20px;
    text-align: center;
}

h3 {
    font-size: 46px;
    margin-bottom: 25px;
}

p {
    font-size: 46px;
    margin-bottom: 25px;
    text-align: right;
}

.watermark {
    position: absolute;
    opacity: 0.04;
    pointer-events: none;
    height: 100%;
    width: 100%;
}
</style>




