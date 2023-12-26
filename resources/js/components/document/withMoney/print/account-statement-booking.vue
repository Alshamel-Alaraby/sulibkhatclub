
<template>
    <div>

        <b-modal
            :id="id"
            :title="$t('general.accountStatement')"
            title-class="font-18"
            dialog-class="modal-full-width"
            body-class="p-4 "
            :hide-footer="true"
            @show="resetModal"
            @hidden="resetModalHidden"
        >
            <div class="page-content container">
                <loader size="large" v-if="isLoader" />
                <div class="page-header text-blue-d2">
                    <h1 class="page-title text-secondary-d1">
                        {{$t('general.InvoiceNO')}} : {{data.prefix}}
                    </h1>

                    <div class="page-tools">
                        <div class="action-buttons">
                            <a v-print="'#printSettlement'" class="btn bg-white btn-light mx-1px text-95" href="#" data-title="Print">
                                <i class="mr-1 fa fa-print text-primary-m1 text-120 w-2"></i>
                                {{$t('general.print')}}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="container px-0" id="printSettlement">
                    <div class="row mt-4">
                        <div class="col-12 col-lg-12">
                            <div class="row">
                                <div class="col-4" v-if="data.branch">
                                    <div class="text-center text-150">
                                        <span class="text-default-d3 fw-bold text-uppercase">{{data.branch.name_e}}</span>
                                    </div>
                                </div>
                                <div class="col-4" v-if="data.branch">
                                    <div class="text-center" v-if="data.branch.media && data.branch.media.length > 0">
                                        <img :src="data.branch.media[0].url" alt="Company logo" style="width: 19%;" />
                                    </div>
                                </div>
                                <div class="col-4" v-if="data.branch">
                                    <div class="text-center text-150">
                                        <span class="text-default-d3 fw-bold">{{data.branch.name}}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="text-center">
                                        <h3>{{$i18n.locale == 'ar' ? data.document.name : data.document.name_e}}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center align-items-center mt-4 detail-print">
                        <div class="col-7" :style="{direction: $i18n.locale == 'ar' ? 'ltr': 'rtl'}">
                              <h5>{{ $t('general.InvoiceIssueDate') }} : {{ data.date }}</h5>
                              <h5>{{ $t('general.InvoiceNO') }}: {{ data.prefix }}</h5>
                              <h5>{{ $t('general.DocumentNumber') }}: {{ data.serial_number }}</h5>
                              <h3 class="text-center mb-2">{{ $t('general.client') }}</h3>
                              <h5 v-if="data.customer">{{ $t('general.client') }} : {{ $i18n.locale == 'ar' ? data.customer.name : data.customer.name_e }}</h5>
                              <h3 class="text-center mb-2">{{$t('general.theDetails')}}</h3>
                              <h5>{{$t('general.paymentMethod')}} : {{$i18n.locale == 'ar' ? data.paymentMethod.name : data.paymentMethod.name_e}}</h5>
                              <h5 v-if="data.manual_document_number">{{$t('general.manual_document_number')}} : {{data.manual_document_number}}</h5>
                              <h5 v-if="data.notes">{{$t('general.note')}} : {{data.notes}}</h5>
                              <h5>{{$t('general.amount')}} : {{data.amount}} KD</h5>
                        </div>
                    </div>
                    <div class="row detail-print-details">
                        <div class="col-6" style="height: 50px">
                            <div v-if="data.branch">
                                tel: ({{ data.branch.code_country }}) {{ data.branch.phone }} - ({{ data.branch.code_country }}) {{ data.branch.second_phone }}
                            </div>
                            <div v-if="data.branch">
                                P.O.Box: {{ data.branch.p_o_pox }}
                            </div>
                        </div>
                        <div class="col-6" style="height: 50px">
                            <div class="text-right" v-if="data.branch">
                                هاتف: ({{ data.branch.code_country }}) {{ data.branch.phone }} - ({{ data.branch.code_country }}) {{ data.branch.second_phone }}
                            </div>
                            <div class="text-right" v-if="data.branch">
                                ص.ب: {{ data.branch.p_o_pox }}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="text-center" v-if="data.branch">
                                {{$t('general.email')}}: {{ data.branch.email }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import {formatDateOnly} from "../../../../helper/startDate";
import Swal from "sweetalert2";
import loader from "../../../general/loader";
import ErrorMessage from "../../../widgets/errorMessage";
import adminApi from "../../../../api/adminAxios";

export default {
    name: "account-statement-booking",
    props: {
        id: {default: "create",},
        document_row_id: { default: '',},
        data: {default: Object},
    },
    components: {
        ErrorMessage,
        loader,
    },
    data(){
        return {
            isLoader:false,
            dataPrint:'',
            level:'',
            total_nights:0,
            total:0,
            paid:0,
            details:[],
            printObj: {
                id: "printSettlement",
            }
        }
    },
    methods: {
        formatDate(value) {
            return formatDateOnly(value);
        },
        resetModal(){

        },
        resetModalHidden(){

        }
    }
}
</script>

<style scoped>
.text-secondary-d1 {
    color: #728299!important;
}
.page-header {
    margin: 0 0 1rem;
    padding-bottom: 1rem;
    padding-top: .5rem;
    border-bottom: 3px dotted #000000;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -ms-flex-align: center;
    align-items: center;
}
.page-title {
    padding: 0;
    margin: 0;
    font-size: 1.75rem;
    font-weight: 300;
}
.brc-default-l1 {
    border-color: #dce9f0!important;
}

.ml-n1, .mx-n1 {
    margin-left: -.25rem!important;
}
.mr-n1, .mx-n1 {
    margin-right: -.25rem!important;
}
.mb-4, .my-4 {
    margin-bottom: 1.5rem!important;
}

hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 0;
    border-top: 1px solid rgba(0,0,0,.1);
}

.text-grey-m2 {
    color: #888a8d!important;
}

.text-success-m2 {
    color: #86bd68!important;
}

.font-bolder, .text-600 {
    font-weight: 600!important;
}

.text-110 {
    font-size: 110%!important;
}
.text-blue {
    color: #478fcc!important;
}
.pb-25, .py-25 {
    padding-bottom: .75rem!important;
}

.pt-25, .py-25 {
    padding-top: .75rem!important;
}
.bgc-default-tp1 {
    background-color: rgba(121,169,197,.92)!important;
}
.bgc-default-l4, .bgc-h-default-l4:hover {
    background-color: #f3f8fa!important;
}
.page-header .page-tools {
    -ms-flex-item-align: end;
    align-self: flex-end;
}

.btn-light {
    color: #757984;
    background-color: #f5f6f9;
    border-color: #dddfe4;
}
.w-2 {
    width: 1rem;
}

.text-120 {
    font-size: 120%!important;
}
.text-primary-m1 {
    color: #4087d4!important;
}

.text-danger-m1 {
    color: #dd4949!important;
}
.text-blue-m2 {
    color: #68a3d5!important;
}
.text-150 {
    font-size: 150%!important;
}
.text-60 {
    font-size: 60%!important;
}
.text-grey-m1 {
    color: #7b7d81!important;
}
.align-bottom {
    vertical-align: bottom!important;
}
.fw-bold {
    font-weight: 700!important;
}
small{
    font-size: 80% !important;
}

.detail-print {
    border: 3px solid;
    padding: 52px 0;
    border-radius: 20px 20px 0 0;
}

.detail-print-details {
    border: 3px solid;
    border-top: 0;
    padding: 20px 0;
    border-radius: 0 0 20px 20px;
}
</style>
