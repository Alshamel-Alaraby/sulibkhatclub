<template>
    <div>
        <b-modal
            :id="id"
            :title="$t('general.print')"
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
                        {{$t('general.InvoiceNO')}} : {{document_data.prefix}}
                    </h1>

                    <div class="page-tools">
                        <div class="action-buttons">
                            <a v-print="'#printGeneralInvoice'" class="btn bg-white btn-light mx-1px text-95" href="#" data-title="Print">
                                <i class="mr-1 fa fa-print text-primary-m1 text-120 w-2"></i>
                                {{$t('general.print')}}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="container px-0" id="printGeneralInvoice">
                    <div class="row mt-4" v-if="document_data">
                        <div class="col-12 col-lg-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center text-150">
                                        <span class="text-default-d3 fw-bold text-uppercase">{{document_data.branch.name_e}}</span>
                                        <template v-if="document_data.branch.media.length">
                                            <img :src="document_data.branch.media[0].url" alt="Company logo" :style="`width: ${widthLogoForCompany()}%;`" />
                                        </template>

                                        <span class="text-default-d3 fw-bold">{{document_data.branch.name}}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="text-center">
                                        <h3>{{$i18n.locale == "ar" ? document_data.document.name : document_data.document.name_e}}</h3>
                                    </div>
                                </div>
                            </div>
                            <!-- .row -->

                            <!--                        <hr class="row brc-default-l1 mx-n1 mb-4" />-->

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="my-2 font-weight-bold"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90">{{$t('general.InvoiceNO')}}:</span> {{document_data.prefix}}</div>
                                    <div class="my-2 font-weight-bold" v-if="document_data.document.document_detail_type == 'board_rent'"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90">{{$t('general.Agency')}}:</span> {{$i18n.locale == "ar" ? document_data.customer.customer_sub_category.name : document_data.customer.customer_sub_category.name_e}}</div>
                                    <div class="my-2 font-weight-bold" v-if="document_data.document.document_detail_type == 'booking'">
                                        <i class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                        <span class="text-600 text-90">{{$t('general.CompanyName')}}:</span>
                                        {{document_data.customer_type == 0? document_data.company ? $i18n.locale == "ar" ? document_data.company.name : document_data.company.name_e : '' :''}}
                                    </div>

                                    <div class="my-2 font-weight-bold">
                                        <i class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                        <span class="text-600 text-90">{{document_data.document.document_detail_type == 'booking' ? $t('general.GuestName') : $t('general.client')}}:</span>
                                        {{ document_data.customer ? $i18n.locale == "ar" ? document_data.customer.name : document_data.customer.name_e : ''}}
                                    </div>
                                    <div class="my-2 font-weight-bold" v-if="document_data.customer && document_data.customer.address">
                                        <i class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                        <span class="text-600 text-90">{{$t('general.Address')}}:</span>
                                        {{document_data.customer ? document_data.customer.address : ''}}
                                    </div>
                                </div>
                                <!-- /.col -->

                                <div class="text-95 col-sm-6 align-self-start d-sm-flex justify-content-end">
                                    <hr class="d-sm-none" />
                                    <div class="text-grey-m2">
                                        <div class="my-2 font-weight-bold" v-if="document_data.document.document_detail_type == 'booking' || document_data.document_id == 36">
                                            <i class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                            <span class="text-600 text-90">{{$t('general.LevelBooking')}}:</span>
                                            {{document_data.header_details[0] ? $i18n.locale == "ar" ? document_data.header_details[0].unit.name : document_data.header_details[0].unit.name_e : ''}}
                                        </div>

                                        <div class="my-2 font-weight-bold" v-if="document_data.document.document_detail_type == 'booking'">
                                            <i class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                            <span class="text-600 text-90">{{$t('general.NoOfPersons')}}:</span>
                                            {{document_data.attendans_num}}
                                        </div>

                                        <div class="my-2 font-weight-bold">
                                            <i class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                            <span class="text-600 text-90">{{$t('general.InvoiceIssueDate')}}:</span>
                                            {{document_data.date}}
                                        </div>
                                    </div>
                                </div>
                                <!-- /.col -->
                            </div>

                            <div class="mt-4" v-if="document_data.document.document_detail_type == 'booking'">
                                <div class="row text-600 text-white bgc-default-tp1 py-25" >
                                    <div class="d-none d-sm-block col-2">{{$t('general.DescriptionBooking')}}</div>
                                    <div class="d-none d-sm-block col-2">{{$t('general.fromDate')}}</div>
                                    <div class="col-2">{{$t('general.toDate')}}</div>
                                    <div class="d-none d-sm-block col-2">{{$t('general.bookingDays')}}</div>
                                    <div class="d-none d-sm-block col-2">{{$t('general.time')}}</div>
                                    <div class="d-none d-sm-block col-2">{{$t('general.PricePerDay')}}</div>
                                </div>

                                <div class="text-95 text-secondary-d3" >
                                    <div class="row mb-2 mb-sm-0 py-25" v-for="(data, index) in document_data.header_details">
                                        <div class="d-none d-sm-block col-2">
                                            <h5>
                                            {{document_data.header_details.length > 1 ? $i18n.locale == "ar" ? data.unit.name : data.unit.name_e : $t('general.accommodation') }}
                                            </h5>
                                        </div>
                                        <div class="d-none d-sm-block col-2"><h5>{{data.date_from}}</h5></div>
                                        <div class="col-2"><h5>{{data.date_to}}</h5></div>
                                        <div class="d-none d-sm-block col-2 text-95"><h5>{{data.rent_days}}</h5></div>
                                        <div class="d-none d-sm-block col-2 text-95"><h5>{{data.check_in_time}}</h5></div>
                                        <div class="d-none d-sm-block col-2 text-95"><h5>{{data.price_per_uint}} KD</h5></div>
                                    </div>
                                </div>

                                <div class="row border-b-2 brc-default-l2"></div>

                                <div class="row mt-3">
                                    <div class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                        <h5>
                                        {{$t('general.Extra_note')}}
                                        </h5>
                                    </div>

                                    <div class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                        <div class="row my-2">
                                            <div class="col-7 text-right">
                                                <h5>
                                                {{$t('general.TotalInvoice')}}
                                                </h5>
                                            </div>
                                            <div class="col-5">
                                                <h5>
                                                <span class="text-150 text-success-d3 opacity-2">{{!document_data.total_invoice ? '0.00' : parseFloat(document_data.total_invoice).toFixed(3)}} KD</span>
                                                </h5>
                                            </div>
                                        </div>

                                        <div class="row my-2">
                                            <div class="col-7 text-right">
                                                <h5>
                                                {{$t('general.InvoiceDiscount')}}
                                                </h5>
                                            </div>
                                            <div class="col-5">
                                                <h5>
                                                <span class="text-150 text-success-d3 opacity-2">{{!document_data.invoice_discount ? '0.00' : parseFloat(document_data.invoice_discount).toFixed(3)}} KD</span>
                                                </h5>
                                            </div>
                                        </div>

                                        <div class="row my-2">
                                            <div class="col-7 text-right">
                                                <h5>
                                                {{$t('general.NetInvoice')}}
                                                </h5>
                                            </div>
                                            <div class="col-5">
                                                <h5>
                                                <span class="text-150 text-success-d3 opacity-2">{{!document_data.net_invoice ? '0.00' : parseFloat(document_data.net_invoice).toFixed(3)}} KD</span>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr />

                                <div class="row">
                                    <div class="col-4 mb-5 text-center">
                                        <h4>
                                        <span class="text-secondary-d1 text-105">{{$t('general.GuestSignature')}}</span>
                                        </h4>
                                    </div>
                                    <div class="col-4 mb-5 text-center">
                                        <h4>
                                        <span class="text-secondary-d1 text-105">{{$t('general.ReceptionistSignature')}}</span>
                                        </h4>
                                    </div>
                                    <div class="col-4 mb-5 text-center">
                                        <h4>
                                        <span class="text-secondary-d1 text-105">{{$t('general.ManagerSignature')}}</span>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4" v-if="document_data.document.document_detail_type == 'board_rent'">
                                <div class="row text-600 text-white bgc-default-tp1 py-25">
                                    <div class="d-none d-sm-block col-3">{{$t('general.Media')}}</div>
                                    <div class="d-none d-sm-block col-2">{{$t('general.fromDate')}}</div>
                                    <div class="col-2">{{$t('general.toDate')}}</div>
                                    <div class="d-none d-sm-block col-1">{{$t('general.rent_days')}}</div>
                                    <div class="d-none d-sm-block col-sm-2">{{$t('general.Notes')}}</div>
                                    <div class="col-2">{{$t('general.totalAmount')}}</div>
                                </div>

                                <div class="text-95 text-secondary-d3">
                                    <div class="row mb-2 mb-sm-0 py-25" v-for="(data, index) in document_data.header_details">
                                        <div class="col-12" v-if="parseInt(data.is_stripe) == 0">
                                            <div class="d-none d-sm-block col-12 category-header">{{data.category ? $i18n.locale == "ar" ? data.category.name : data.category.name_e :'-'}}</div>
                                            <div class="row mb-2 mb-sm-0 py-25 category-details" v-for="(break_down, index) in data.break_downs">
                                                <div class="d-none d-sm-block col-3">{{break_down.panel.code + ' - ' + break_down.panel.face + ' - ' + ($i18n.locale == "ar" ? break_down.panel.category.name : break_down.panel.category.name_e) + ' - ' + ($i18n.locale == "ar" ? break_down.panel.governorate.name : break_down.panel.governorate.name_e)}}</div>
                                                <div class="d-none d-sm-block col-2">{{break_down.date_from}}</div>
                                                <div class="col-2">{{break_down.date_to}}</div>
                                                <div class="d-none d-sm-block col-1 text-95">{{data.rent_days}}</div>
                                                <div class="d-none d-sm-block col-2 text-95">{{data.note}}</div>
                                                <div class="col-2 text-secondary-d2">{{data.total}} KD</div>
                                            </div>
                                        </div>
                                        <div class="col-12" v-if="parseInt(data.is_stripe) == 1">
                                            <div class="row mb-2 mb-sm-0 py-25 category-details">
                                                <div class="d-none d-sm-block col-3">{{data.package ? ($i18n.locale == "ar" ? data.package.name : data.package.name_e) + ' - ' + $t('general.Package'):$t('general.Package')}}</div>
                                                <div class="d-none d-sm-block col-2">{{data.date_from}}</div>
                                                <div class="col-2">{{data.date_to}}</div>
                                                <div class="d-none d-sm-block col-1 text-95">{{data.rent_days}}</div>
                                                <div class="d-none d-sm-block col-2 text-95">{{data.note}}</div>
                                                <div class="col-2 text-secondary-d2">{{data.total}} KD</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row border-b-2 brc-default-l2"></div>

                                <div class="row mt-3">
                                    <div class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                        <h5>
                                        {{$t('general.Extra_note')}}
                                        </h5>
                                    </div>

                                    <div class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                        <div class="row my-2" v-if="document_data.invoice_discount">
                                            <div class="col-7 text-right">
                                                <h5>
                                                {{$t('general.TotalInvoice')}}
                                                </h5>
                                            </div>
                                            <div class="col-5">
                                                <h5>
                                                <span class="text-150 text-success-d3 opacity-2">{{ !document_data.total_invoice ? '0.00' : parseFloat(document_data.total_invoice).toFixed(3) }} KD</span>
                                                </h5>
                                            </div>
                                        </div>

                                        <div class="row my-2" v-if="document_data.invoice_discount">
                                            <div class="col-7 text-right">
                                                <h5>
                                                {{$t('general.InvoiceDiscount')}}
                                                </h5>
                                            </div>
                                            <div class="col-5">
                                                <h5>
                                                <span class="text-150 text-success-d3 opacity-2">{{ !document_data.invoice_discount ? '0.00' : parseFloat(document_data.invoice_discount).toFixed(3) }} KD</span>
                                                </h5>
                                            </div>
                                        </div>

                                        <div class="row my-2">
                                            <div class="col-7 text-right">
                                                <h5>
                                                {{$t('general.NetInvoice')}}
                                                </h5>
                                            </div>
                                            <div class="col-5">
                                                <h5>
                                                <span class="text-150 text-success-d3 opacity-2">{{ !document_data.net_invoice ? '0.00' : parseFloat(document_data.net_invoice).toFixed(3) }} KD</span>
                                                </h5>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <hr />

                                <div class="row">
                                    <div class="col-6 mb-5 text-center">
                                        <h4>
                                        <span class="text-secondary-d1 text-105">{{$t('general.ClientSignature')}}</span>
                                        </h4>
                                    </div>
                                    <div class="col-6 mb-5 text-center">
                                        <h4>
                                        <span class="text-secondary-d1 text-105">{{$t('general.ManagerSignature')}}</span>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4" v-if="document_data.document.document_detail_type == 'normal'">
                                <div class="row text-600 text-white bgc-default-tp1 py-25" >
                                    <div class="d-none d-sm-block col-3">{{$t('general.Service')}}</div>
                                    <div class="d-none d-sm-block col-2">{{$t('general.quantity')}}</div>
                                    <div class="col-2">{{$t('general.Price')}}</div>
                                    <div class="d-none d-sm-block col-2">{{$t('general.total')}}</div>
                                    <div class="d-none d-sm-block col-3">{{$t('general.note')}}</div>
                                </div>

                                <div class="text-95 text-secondary-d3" >
                                    <div class="row mb-2 mb-sm-0 py-25" v-for="(data, index) in document_data.header_details">
                                        <div class="d-none d-sm-block col-3"><h5>{{ $i18n.locale == "ar" ? data.item.name : data.item.name_e }}</h5></div>
                                        <div class="d-none d-sm-block col-2"><h5>{{data.quantity}}</h5></div>
                                        <div class="d-none d-sm-block col-2 text-95"><h5>{{data.price_per_uint}} KD</h5></div>
                                        <div class="d-none d-sm-block col-2 text-95"><h5>{{data.total}} KD</h5></div>
                                        <div class="d-none d-sm-block col-3 text-95"><h5>{{data.note}} </h5></div>
                                    </div>
                                </div>

                                <div class="row border-b-2 brc-default-l2"></div>

                                <div class="row mt-3">
                                    <div class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                        <h5>
                                        {{$t('general.Extra_note')}}
                                        </h5>
                                    </div>

                                    <div class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                        <div class="row my-2" v-if="document_data.invoice_discount">
                                            <div class="col-7 text-right">
                                                <h5>
                                                {{$t('general.TotalInvoice')}}
                                                </h5>
                                            </div>
                                            <div class="col-5">
                                                <h5>
                                                <span class="text-150 text-success-d3 opacity-2">{{!document_data.total_invoice ? '0.00' : parseFloat(document_data.total_invoice).toFixed(3)}} KD</span>
                                                </h5>
                                            </div>
                                        </div>

                                        <div class="row my-2" v-if="document_data.invoice_discount">
                                            <div class="col-7 text-right">
                                                <h5>
                                                {{$t('general.InvoiceDiscount')}}
                                                </h5>
                                            </div>
                                            <div class="col-5">
                                                <h5>
                                                <span class="text-150 text-success-d3 opacity-2">{{!document_data.invoice_discount ? '0.00' : parseFloat(document_data.invoice_discount).toFixed(3)}} KD</span>
                                                </h5>
                                            </div>
                                        </div>

                                        <div class="row my-2">
                                            <div class="col-7 text-right">
                                                <h5>
                                                {{$t('general.NetInvoice')}}
                                                </h5>
                                            </div>
                                            <div class="col-5">
                                                <h5>
                                                <span class="text-150 text-success-d3 opacity-2">{{!document_data.net_invoice ? '0.00' : parseFloat(document_data.net_invoice).toFixed(3)}} KD</span>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr />

                                <div class="row">
                                    <div class="col-4 mb-5 text-center">
                                        <h4>
                                        <span class="text-secondary-d1 text-105">{{$t('general.GuestSignature')}}</span>
                                        </h4>
                                    </div>
                                    <div class="col-4 mb-5 text-center">
                                        <h4>
                                        <span class="text-secondary-d1 text-105">{{$t('general.ReceptionistSignature')}}</span>
                                        </h4>
                                    </div>
                                    <div class="col-4 mb-5 text-center">
                                        <h4>
                                        <span class="text-secondary-d1 text-105">{{$t('general.ManagerSignature')}}</span>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import ErrorMessage from "../../../widgets/errorMessage";
import loader from "../../../general/loader";
import {formatDateOnly} from "../../../../helper/startDate";
import adminApi from "../../../../api/adminAxios";
import Swal from "sweetalert2";

export default {
    name: "print-general-item-invoice",
    props: {
        id: {default: "create",},
        document_row_id: { default: '',},
    },
    components: {
        ErrorMessage,
        loader,
    },
    data(){
        return {
            isLoader:false,
            document_data:'',
            printObj: {
                id: "printGeneralInvoice",
            }
        }
    },
    methods: {
        widthLogoForCompany()
        {
            let company_id = this.$store.getters["auth/company_id"];
            if(company_id== 2)
            {
                return 30;
            }else if(company_id== 3)
            {
                return 7;
            }else{
                return 25;
            }
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        async resetModal() {
            this.document_data='';
            await this.getDataPrint();
        },
        resetModalHidden() {
            this.document_data='';
            this.$bvModal.hide(this.id);
        },
        async getDataPrint()
        {
            this.isLoader = true;
            await adminApi
                .get(`/document-headers/${this.document_row_id}`)
                .then((res) => {
                    let l = res.data.data;
                    this.document_data = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })
                .finally(() => {
                    this.isLoader = false;
                });
        }

    }
}
</script>

<style scoped>
@media print {
    .bgc-default-tp1{
        background-color: unset !important;
        color: #000 !important;
    }
}
.category-header {
    background-color: #b0cac7;
    color: #111;
    font-weight: bold;
    font-size: 20px;
}
.category-details{
    font-size: 14px;
    font-weight: bold;
}
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


</style>
