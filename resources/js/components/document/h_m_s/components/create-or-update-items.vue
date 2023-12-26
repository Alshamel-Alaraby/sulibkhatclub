<script>
import ErrorMessage from "../../../widgets/errorMessage";
import loader from "../../../general/loader";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";
import translation from "../../../../helper/mixin/translation-mixin";
import composable from "./composable";
import {minValue, required, requiredIf} from "vuelidate/lib/validators";
import {formatDateOnly} from "../../../../helper/startDate";
import {dynamicSortString} from "../../../../helper/tableSort";
import transactionBreak from "../../../create/receivablePayment/transactionBreak/transactionBreak";


/**
 * Advanced Table component
 */
export default {
    name: "createOrUpdateItems",
    props: {
        modalId:{
            default:'createInvoice'
        },
        bill_holder: {
            default: null,
        },
        document: {
            default: null,
        },
        document_id: {
            default: null,
        },
        appointment_data:{
            Object,
            default:{},
        },
        companies_insurances:{
            Array,
            default:[],
        },
        services:{
            Array,
            default:[],
        },
        paymentMethods:{
            Array,
            default:[],
        },
        doctors:{
            Array,
            default:[],
        },
        dataRow:{
            Object,
            default:{},
        },
        id: {
            default:'create'
        },
        other_data:{
            default:null,
        },
    },
    mixins: [translation,composable],
    components: {
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
        transactionBreak
    },
    data() {
        return {
            financial_years_validate:true,
            customer_data_edit: "",
            customer_data_create: "",
            client_type:'patient',
            openingBreak:'',
            check_data_row:0,
            check_appointment_data:0,
            unit_id:null,
            debounce: {},
            branches: [],
            customers: [],
            companies: [],
            patients: [],
            statuses: [],
            serial_number:"",
            relatedDocumentNumbers: [],
            relatedDocuments: [],
            enabled3: true,
            isLoader: false,
            create: {
                document_id: this.document_id,
                document_module_type_id: this.document&& this.document.document_module_type_id?this.document.document_module_type_id:'',
                document_status_id: null,
                reason:'',
                branch_id: null,
                date: this.formatDate(new Date()),
                related_document_id: null,
                related_document_number: null,
                related_document_prefix: '',
                doctor_id: null,
                from_doctor_id: null,
                patient_id: null,
                company_id: null,
                customer_type:1,
                payment_method_id: 1,
                total_invoice: 0,
                total_patient_amount: 0,
                total_company_insurance_amount: 0,
                invoice_discount: 0,
                patient_insurance_number: '',
                company_insurance_id: null,
                net_invoice: 0,
                sell_method_discount: 0,
                unrelaized_revenue: 0,
                header_details: [{
                    service_id:null, //for service
                    quantity: 1,
                    price_per_uint: 0,
                    company_insurance_amount: 0,
                    patient_amount: 0,
                    total: 0,
                    unit_type: "Appointment",
                    date_from: this.formatDate(new Date()),
                    rent_days:0,
                    date_to: this.formatDate(new Date()),
                    check_in_time:'',
                    discount: 0,
                    is_stripe: 1,
                    sell_method_discount: 0,
                    note:'',
                }]
            },
            errors: {},
            is_disabled: false,
            company_id: null,
            printObj: {
                id: "printReservation",
            },
            dataOfRow:''
        };
    },
    validations: {
        create: {
            document_id: { required },
            document_status_id: { required },
            reason:{ },
            branch_id: { required },
            date: { required },
            related_document_id: {  },
            related_document_number: {  },
            related_document_prefix: {  },
            doctor_id: { required },
            from_doctor_id: {  },
            patient_id: { required },
            company_id: {  required: requiredIf(function (model) {
                    return this.create.customer_type == 0;
                }),
            },
            customer_type: {},
            payment_method_id: { required },
            total_invoice: { required },
            invoice_discount: { required },
            patient_insurance_number: { required(value){
                return this.create.company_insurance_id? value :true
            } },
            company_insurance_id: {  },
            net_invoice: { required },
            sell_method_discount: {  },
            unrelaized_revenue: {  },
            header_details: {
                required,
                $each: {
                    service_id: {},
                    quantity: { required, minValue: minValue(0) },
                    date_from: {required},
                    rent_days:{},
                    date_to: {},
                    check_in_time:{},
                    unit_type: {required},
                    price_per_uint: {required, minValue: minValue(0)},
                    company_insurance_amount: {required, minValue: minValue(0)},
                    patient_amount: {required, minValue: minValue(0)},
                    total: {required},
                    discount: {},
                    is_stripe: {required},
                    sell_method_discount: {},
                    note: {},
                }
            }
        },
        unit_id:{ required },
    },

    watch:{
        bill_holder:{
            handler(newV, old) {
                if(newV == 'patient')
                this.client_type = 'patient'
            },
        },
        dataRow:{
            handler(newV, old) {
                this.check_data_row = Object.keys(newV).length
                if(this.check_data_row != Object.keys(old).length)
                this.resetModalCreateOrUpdate()
            },
        },
        appointment_data:{
            handler(newV, old) {
                this.check_appointment_data = Object.keys(newV).length
                if(this.check_appointment_data){
                    this.create.branch_id = this.appointment_data.branch_id
                    this.create.doctor_id = this.appointment_data.doctor_id
                    this.create.from_doctor_id = this.appointment_data.from_doctor_id
                    this.create.patient_id = this.appointment_data.patient_id
                    this.create.appointment_id = this.appointment_data.appointment_id
                    this.getSerialNumber(this.appointment_data.branch_id)
                }
            },
        },

    },
    mounted(){
        this.resetModalCreateOrUpdate()

        this.company_id = this.$store.getters["auth/company_id"];
        this.$store.dispatch('locationIp/getIp');
        this.getBranch()
    },
    methods: {
        async resetModalCreateOrUpdate()
        {
            this.relatedDocuments = this.document.document_relateds;
            if (this.check_data_row)
            {
                await this.getDataRow();
                await this.resetModalEdit(this.dataRow.id);
            }else{
                await this.resetModal();
            }
        },
        resetModalHiddenCreateOrUpdate ()
        {
            if (this.check_data_row)
            {
                // this.resetModalHiddenEdit(this.dataRow.id);
            }else{
                this.resetModalHidden();
            }
        },
        changeValue(came_from = null){
            let sum = 0;
            let total_patient_amount = 0;
            let total_company_insurance_amount = 0;

            this.create.header_details.forEach(e => {
                sum += e.price_per_uint * e.quantity;
                total_patient_amount += e.patient_amount;
                total_company_insurance_amount += e.company_insurance_amount;
            });
            this.create.total_patient_amount = total_patient_amount;
            this.create.total_company_insurance_amount = total_company_insurance_amount;
            this.create.total_invoice = sum;
            this.create.net_invoice = sum - this.create.invoice_discount;
            if(came_from != 'change_sub_discount')
                this.calculateDiscountLine(came_from);

        },
        calculateDiscountLine(came_from = null)
        {
            this.create.header_details.forEach((e,index) => {
                this.create.header_details[index].discount = Math.round(((this.create.invoice_discount * e.total) / this.create.total_invoice ) * 100) /100 ;
                if(came_from == 'input')
                    this.change_in_any_field(index)
            });
        },
        addNewField(){
            this.create.header_details.push({
                service_id:null, //for service
                quantity: 1,
                price_per_uint: 0,
                patient_amount: 0,
                company_insurance_amount: 0,
                date_from: this.formatDate(new Date()),
                rent_days:0,
                date_to: this.formatDate(new Date()),
                check_in_time:'',
                total: 0,
                discount: 0,
                unit_type: "Appointment",
                is_stripe: 1,
                sell_method_discount: 0,
                note: '',
            });
            this.changeValue();
        },
        removeNewField(index){
            if(this.create.header_details.length > 1){
                this.create.header_details.splice(index,1);
            }
            this.changeValue();
        },
        /**
         *  data create (create)
         */
        dataCreate()
        {
            this.customer_data_edit = '';
            this.customer_data_create = '';
            this.client_type = "patient";
            this.serial_number = "";
            this.unit_id = null;
            this.financial_years_validate = true;
            this.create = {
                document_id: this.document_id,
                document_module_type_id: this.document && this.document.document_module_type_id?this.document.document_module_type_id:'',
                document_status_id: parseInt(this.document.need_approve) == 0 ? 5 : 1,
                reason:'',
                branch_id: null,
                date: this.formatDate(new Date()),
                related_document_id: null,
                related_document_number: null,
                related_document_prefix: '',
                doctor_id: null,
                from_doctor_id: null,
                patient_id: null,
                company_id: null,
                sell_method_id: null,
                payment_method_id: 1,
                customer_type:1,
                total_invoice: 0,
                invoice_discount: 0,
                patient_insurance_number: '',
                company_insurance_id: null,
                net_invoice: 0,
                sell_method_discount: 0,
                unrelaized_revenue: 0,
                header_details: [{
                    service_id:null, //for service
                    quantity: 1,
                    price_per_uint: 0,
                    patient_amount: 0,
                    company_insurance_amount: 0,
                    total: 0,
                    unit_type: "Appointment",
                    date_from: this.formatDate(new Date()),
                    rent_days:0,
                    date_to: this.formatDate(new Date()),
                    check_in_time:'',
                    discount: 0,
                    is_stripe: 1,
                    sell_method_discount: 0,
                    note: '',
                }]
            };
        },
        /**
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.dataCreate();
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {

            this.dataCreate();
            // this.getSalesmen();
            this.getCustomers();
            if(parseInt(this.document.required) == 1 && this.relatedDocuments.length == 1){
                this.create.related_document_id = this.relatedDocuments[0].id;
            }
            if (this.other_data)
            {
                this.unit_id = this.other_data.unit_id;
            }
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  create screen
         */
         showCompanyInsurancesModal() {
            if (this.create.company_insurance_id == 0) {
                this.$bvModal.show("addCompanyInsurance");
                this.create.company_insurance_id = null;
            }
        },
        resetForm() {
            this.dataCreate();
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
        },


        async resetModalEdit(id) {
            this.client_type = 'patient';
            this.customer_data_edit = '';
            this.customer_data_create = '';
            this.isLoader = true;
            this.financial_years_validate = true;

            // this.getSalesmen();
            this.getCustomers();

            let invoice = this.dataOfRow;
            this.customer_data_edit = invoice.patient;

            this.serial_number = invoice.prefix;
            this.create.document_status_id = invoice.document_status_id;
            this.create.reason = invoice.reason??'';
            this.create.branch_id = invoice.branch_id;
            this.create.date = invoice.date;
            this.create.related_document_id = invoice.related_document_id;
            this.create.document_module_type_id= this.document && this.document.document_module_type_id?this.document.document_module_type_id:'';
            if(invoice.related_document_id)
            {
                this.handelRelatedDocument();
            }
            if(invoice.document_number)
            {
                this.relatedDocumentNumbers.push(invoice.document_number);
            }
            this.create.id = invoice.id;
            this.create.patient_id = invoice.patient_id;
            this.create.company_id = invoice.company_id;
            this.create.customer_type = invoice.customer_type;
            this.create.doctor_id = invoice.doctor_id;
            this.create.from_doctor_id = invoice.from_doctor_id;
            this.create.payment_method_id = invoice.payment_method_id;
            this.create.sell_method_id = invoice.sell_method_id;
            this.create.total_invoice = invoice.total_invoice;
            this.create.patient_insurance_number = invoice.patient_insurance_number;
            this.create.company_insurance_id = invoice.company_insurance_id;
            this.create.invoice_discount = invoice.invoice_discount;
            this.create.net_invoice = invoice.net_invoice;
            this.create.sell_method_discount = invoice.sell_method_discount;
            this.create.unrelaized_revenue = invoice.unrelaized_revenue;
            this.create.related_document_number = invoice.related_document_number;
            this.create.related_document_prefix = invoice.related_document_prefix;
            this.create.total_company_insurance_amount = invoice.total_company_insurance_amount;
            this.create.total_patient_amount = invoice.total_patient_amount;
            this.create.header_details = [];
            invoice.header_details.forEach((e,index) => {
                this.create.header_details.push({
                    service_id:e.service_id, //for service
                    date_from: this.formatDate(e.date_from),
                    rent_days:e.rent_days,
                    date_to: this.formatDate(e.date_to),
                    check_in_time:e.check_in_time,
                    unit_type: e.unit_type,
                    quantity: e.quantity,
                    price_per_uint: e.price_per_uint,
                    company_insurance_amount: e.company_insurance_amount,
                    patient_amount: e.patient_amount,
                    discount: e.discount,
                    total: e.total,
                    is_stripe: e.is_stripe,
                    sell_method_discount: e.sell_method_discount,
                    note: e.note,
                });
            });
            this.isLoader = false;
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.client_type = 'patient';
            this.customer_data_edit = '';
            this.errors = {};
            this.create = {
                document_id: this.document_id,
                document_status_id: null,
                id: null,
                reason:'',
                branch_id: null,
                date: this.formatDate(new Date()),
                related_document_id: null,
                related_document_number: null,
                related_document_prefix: '',
                doctor_id: null,
                from_doctor_id: null,
                patient_id: null,
                company_id: null,
                sell_method_id: null,
                payment_method_id: 1,
                customer_type:1,
                total_invoice: 0,
                patient_insurance_number: '',
                company_insurance_id: null,
                invoice_discount: 0,
                net_invoice: 0,
                sell_method_discount: 0,
                unrelaized_revenue: 0,
                header_details: [],
            };
        },

        /**
         *  start  dynamicSortString
         */

        formatDate(value) {
            return formatDateOnly(value);
        },

        showPackageModal(index) {
            if (this.create.header_details[index].package_id) {
                this.create.header_details[index].price_per_uint = this.packages.find(el => el.id == this.create.header_details[index].package_id).price;
                this.totalCreate(index);
            }
        },
        totalCreate(index,came_from = null)
        {
            this.create.header_details[index].total = this.create.header_details[index].quantity * this.create.header_details[index].price_per_uint;

            this.changeValue(came_from);

        },
        patient_and_company_amount(index,changed_type)
        {
            let diff_in_ammount = this.create.header_details[index].total - this.create.header_details[index].discount;
            if(changed_type == 'company')
                this.create.header_details[index].patient_amount = diff_in_ammount - this.create.header_details[index].company_insurance_amount
            else
                this.create.header_details[index].company_insurance_amount = diff_in_ammount - this.create.header_details[index].patient_amount

                this.changeValue()

        },
        change_in_any_field(index){
            let diff_in_ammount = Math.round((this.create.header_details[index].total - this.create.header_details[index].discount) * 100) /100;
            if((this.create.header_details[index].patient_amount && !this.create.header_details[index].company_insurance_amount) || !this.create.company_insurance_id)
                this.create.header_details[index].patient_amount = Math.round(diff_in_ammount * 100 ) / 100
            else{
                let old_amount = this.create.header_details[index].company_insurance_amount + this.create.header_details[index].patient_amount
                this.create.header_details[index].company_insurance_amount += Math.round(((diff_in_ammount - old_amount) / 2) * 100) /100
                this.create.header_details[index].patient_amount += Math.round(((diff_in_ammount - old_amount) / 2) *100) /100
            }
                this.changeValue()
        },
        changeStrip(index)
        {
            this.create.header_details[index].service_id = null;
            this.create.header_details[index].quantity = 1;
            this.create.header_details[index].price_per_uint = 0;
            this.create.header_details[index].patient_amount = 0;
            this.create.header_details[index].company_insurance_amount = 0;
            this.create.header_details[index].total = 0;
            this.create.header_details[index].discount = 0;
            this.create.header_details[index].unit_type = "Appointment";
            this.create.header_details[index].date_from = this.formatDate(new Date());
            this.create.header_details[index].rent_days = 0;
            this.create.header_details[index].date_to = this.formatDate(new Date());
            this.create.header_details[index].check_in_time=''
        },
        searchCustomer(e) {
            let search = e??'';
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                console.log('search ===== patient')
                this.getCustomers(search);
            }, 900);

        },


        checkDocumentLinked()
        {
            if(this.document)
            {
                if(this.document.document_Relateds && this.document.document_Relateds.length > 0)
                {
                    return true;
                }
            }
            return false;
        },
        titleModelName()
        {
            if(this.document) {
                if (this.check_data_row == 0)
                {
                    return `${this.$t('general.addNew')} ${this.$i18n.locale == 'ar'?this.document.name:this.document.name_e} `;
                }else {
                    return `${this.$t('general.Edit')} ${this.$i18n.locale == 'ar'?this.document.name:this.document.name_e} `;
                }
            }
        },
        async changeDateDocument()
        {
            let date = this.create.date;
            let branch_id = this.create.branch_id;
            await this.checkFinancialYears(date,branch_id);
        },

        async handelRelatedDocument()
        {
            let related_document_id = this.create.related_document_id;
            let document_id = this.document_id;
            let branch_id = this.create.branch_id;
            await this.getRelatedDocument(related_document_id,branch_id,document_id);
        },


        RelatedDocumentData()
        {
            let related_document_number = this.create.related_document_number;
            if(related_document_number)
            {
                this.displayDataCreate(related_document_number)
            }
        },
        displayDataCreate(related_document_number)
        {
            this.isLoader = true;
            let relatedDocument = this.relatedDocumentNumbers.find(el => el.id == related_document_number);
            this.create.header_details = [];
            this.customer_data_edit = relatedDocument.customer;
            this.customer_data_create = relatedDocument.customer;
            console.log('nasser=====')

            this.getCustomers();
            this.create.related_document_prefix = relatedDocument.serial_number;
            this.create.customer_type = relatedDocument.customer_type;
            this.create.patient_id = relatedDocument.patient_id;
            this.create.company_id = relatedDocument.company_id;
            this.create.doctor_id = relatedDocument.doctor_id;
            this.create.from_doctor_id = relatedDocument.from_doctor_id;

            this.create.payment_method_id = relatedDocument.payment_method_id;
            this.create.sell_method_id = relatedDocument.sell_method_id;
            this.create.total_invoice = relatedDocument.total_invoice;
            this.create.invoice_discount = relatedDocument.invoice_discount;
            this.create.patient_insurance_number = relatedDocument.patient_insurance_number;
            this.create.company_insurance_id = relatedDocument.company_insurance_id;
            this.create.net_invoice = relatedDocument.net_invoice;
            this.create.sell_method_discount = relatedDocument.sell_method_discount;
            this.create.unrelaized_revenue = relatedDocument.unrelaized_revenue;

            relatedDocument.header_details.forEach((e,index) => {
                this.create.header_details.push({
                    service_id:e.service_id, //for service
                    date_from: this.formatDate(e.date_from),
                    rent_days: e.rent_days,
                    date_to: this.formatDate(e.date_to),
                    check_in_time:'',
                    unit_type: e.unit_type,
                    quantity: e.quantity,
                    price_per_uint: e.price_per_uint,
                    patient_amount: e.patient_amount,
                    company_insurance_amount: e.company_insurance_amount,
                    total: e.total,
                    discount: e.discount,
                    is_stripe: e.is_stripe,
                    sell_method_discount: e.sell_method_discount,
                    note: e.note,
                });
            });
            this.isLoader = false;
        },
        showBreakCreate(){
            if (this.create.id) {

                this.openingBreak = {
                    patient_id: this.create.customer_type == 0 ? this.create.company_id : this.create.patient_id,
                    currency_id: 1,
                    document_id: this.document_id,
                    debit: (this.document.attributes && parseInt(this.document.attributes.customer) == 1)?this.create.net_invoice:0,
                    credit: (this.document.attributes && parseInt(this.document.attributes.customer) == -1)?this.create.net_invoice:0,
                    date: this.create.date,
                    rate: 1,
                    id: this.create.id,
                    break_type: 'documentHeader',
                    is_update: this.check_data_row ? true : false,
                };
                this.$bvModal.show("opening-balance-break-create");
            }
        },
        addLineEnter(e)
        {
            const keyCode = e.keyCode;
            if (keyCode === 13)
            {
                this.addNewField();
            }
        },
        addServicePrice(index)
        {
            if(this.create.header_details[index].service_id)
            {
                let price = this.services.find(el => el.id == this.create.header_details[index].service_id) ? this.services.find(el => el.id == this.create.header_details[index].service_id).price : 0;
                this.create.header_details[index].price_per_uint = price;
                this.create.header_details[index].patient_amount = price;
            }else {
                this.create.header_details[index].price_per_uint = 0 ;
                this.create.header_details[index].patient_amount = 0 ;
            }
            this.totalCreate(index,'input');
        },
        showServiceModal(index) {
            if (this.create.header_details[index].service_id == 0) {
                this.$bvModal.show("addService");
                this.create.header_details[index].service_id = null;
            }
        },
        discountLine(index)
        {
            let sum = 0;
            this.create.header_details.forEach(e => {
                sum += e.discount;
            });
            this.create.invoice_discount = sum;
            this.changeValue('change_sub_discount');
            this.change_in_any_field(index)
        },

        addCustomerDataCreate()
        {
            if (this.create.patient_id)
            {
                this.customer_data_create = this.customers.find((e) => e.id == this.create.patient_id);
            }
        }
    }
};
</script>

<template>
    <div>

        <transactionBreak :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :opening="openingBreak"/>
        <!--  create   -->
        <b-modal dialog-class="modal-full-width" :id="modalId"
                 :title="titleModelName()"
                 title-class="font-18" body-class="p-4 "
                 :hide-footer="true"
                 @hidden="resetModalHiddenCreateOrUpdate()"
                 >
            <form>
                <loader size="large" v-if="isLoader" />

                <div class="row" >

                    <div class="col-md-12 mb-3 d-flex justify-content-end">

                        <div class="col-6">
                            <b-button v-if="check_data_row == 0" variant="primary" type="button" class="font-weight-bold px-2 mx-1">
                                {{ $t("general.print") }}
                                <i class="fe-printer"></i>
                            </b-button>
                            <b-button v-else v-print="'#printReservation'" variant="primary" type="button" class="font-weight-bold px-2 mx-1">
                                {{ $t("general.print") }}
                                <i class="fe-printer"></i>
                            </b-button>
                            <b-button v-if="check_data_row == 0" variant="success" :disabled="!is_disabled" @click.prevent="resetForm" type="button"
                                    :class="['font-weight-bold px-2 mx-1', is_disabled ? 'mx-2' : '']">
                                {{ $t("general.AddNewRecord") }}
                            </b-button>
                            <template v-if="!is_disabled">
                                <a class="btn btn-success mx-1" v-if="!isLoader" @click.prevent="Submit(false)">
                                    {{ check_data_row > 0 ? $t('general.Edit') : $t("general.Add") }}
                                </a>
                                <b-button variant="success" class="mx-1" disabled v-else>
                                    <b-spinner small></b-spinner>
                                    <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                </b-button>
                            </template>
                            <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(modalId)">
                                {{ $t("general.Cancel") }}
                            </b-button>
                        </div>
                        <div class="col-6" v-if="!bill_holder">
                            <div class="form-group">
                                <label class="mr-2">
                                    {{ $t('general.Bill holder') }}
                                </label>
                                <b-form-group >
                                    <b-form-radio class="d-inline-block" v-model="client_type"
                                        name="some-radios" :value="'patient'">{{ $t("general.Patient") }}
                                    </b-form-radio>
                                    <b-form-radio class="d-inline-block m-1" v-model="client_type"
                                        name="some-radios" :value="'doctor'">{{ $t("general.Doctor") }}
                                    </b-form-radio>
                                </b-form-group>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.Branch') }} <span class="text-danger">*</span></label>

                            <multiselect
                                :show-labels="false"
                                @input="getSerialNumber($event)"
                                :disabled="check_appointment_data > 0 "
                                v-model="create.branch_id"
                                :options="branches.map((type) => type.id)"
                                :custom-label="(opt) => branches.find((x) => x.id == opt)? $i18n.locale == 'ar'? branches.find((x) => x.id == opt).name: branches.find((x) => x.id == opt).name_e:''"
                                :class="{'is-invalid': $v.create.branch_id.$error || errors.branch_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.branch_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>

                            <template v-if="errors.branch_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.branch_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">
                                {{ $t('general.Date') }}
                                <span class="text-danger">*</span>
                            </label>
                            <date-picker
                                :disabled="!create.branch_id"
                                @input="changeDateDocument()"
                                type="date"
                                v-model="create.date"
                                format="YYYY-MM-DD"
                                valueType="format"
                                :confirm="false"
                                :class="{'is-invalid': !financial_years_validate}"

                            ></date-picker>
                            <div v-if="!financial_years_validate" class="invalid-feedback">
                                {{ $t("general.The date is outside the permitted fiscal year") }}
                            </div>
                            <template v-if="errors.date">
                                <ErrorMessage v-for="(errorMessage, index) in errors.date" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">
                                {{$t('general.serial_number')}}
                            </label>
                            <input
                                :disabled="true"
                                type="text"
                                class="form-control"
                                v-model="serial_number"
                            />
                        </div>
                    </div>
                    <div v-if="checkDocumentLinked()" class="col-md-2">
                        <div class="form-group">
                            <label>{{$t('general.RelatedDocument')}}</label>
                            <multiselect
                                :show-labels="false"
                                :disabled="!create.branch_id || (relatedDocuments.length == 1 && parseInt(document.required) == 1)"
                                @input="handelRelatedDocument()"
                                v-model="create.related_document_id"
                                :options="relatedDocuments.map((type) => type.id)"
                                :custom-label="(opt) => relatedDocuments.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? relatedDocuments.find((x) => x.id == opt).name : relatedDocuments.find((x) => x.id == opt).name_e:''"
                                :class="{'is-invalid': $v.create.related_document_id.$error || errors.related_document_id, }">
                            </multiselect>

                            <template v-if="errors.related_document_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.related_document_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="checkDocumentLinked()" class="col-md-2">
                        <div class="form-group">
                            <label>{{$t('general.related_document_prefix')}}</label>
                            <multiselect
                                :show-labels="false"
                                :disabled="!create.branch_id"
                                @input="RelatedDocumentData()"
                                v-model="create.related_document_number"
                                :options="relatedDocumentNumbers.map((type) => type.id)"
                                :custom-label="(opt) => relatedDocumentNumbers.find((x) => x.id == opt) ? relatedDocumentNumbers.find((x) => x.id == opt).prefix:''"
                                :class="{'is-invalid': $v.create.related_document_number.$error || errors.related_document_number, }">
                            </multiselect>

                            <template v-if="errors.related_document_number">
                                <ErrorMessage v-for="(errorMessage, index) in errors.related_document_number"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="checkDocumentLinked()" class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">
                                {{$t('general.RelatedDocumentNumber')}}
                            </label>
                            <input
                                :disabled="true"
                                type="text"
                                class="form-control"
                                v-model="create.related_document_prefix"
                            />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.paymentMethod') }} <span class="text-danger">*</span></label>

                            <multiselect
                                :show-labels="false"
                                :disabled="true"
                                v-model="create.payment_method_id"
                                :options="paymentMethods.map((type) => type.id)"
                                :custom-label=" (opt) => paymentMethods.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? paymentMethods.find((x) => x.id == opt).name: paymentMethods.find((x) => x.id == opt).name_e :''"
                                :class="{'is-invalid':$v.create.payment_method_id.$error || errors.payment_method_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.payment_method_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.payment_method_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.payment_method_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.Doctor') }} <span class="text-danger">*</span></label>

                            <multiselect
                                :show-labels="false"
                                :disabled="!create.branch_id || check_appointment_data > 0"
                                v-model="create.doctor_id"
                                :options="doctors.map((type) => type.id)"
                                :custom-label=" (opt) => doctors.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? doctors.find((x) => x.id == opt).name: doctors.find((x) => x.id == opt).name_e :''"
                                :class="{'is-invalid':$v.create.doctor_id.$error || errors.doctor_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.doctor_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.doctor_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.button_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.From Doctor') }} </label>

                            <multiselect
                                :show-labels="false"
                                :disabled="!create.branch_id || check_appointment_data > 0"
                                v-model="create.from_doctor_id"
                                :options="doctors.map((type) => type.id)"
                                :custom-label=" (opt) => doctors.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? doctors.find((x) => x.id == opt).name: doctors.find((x) => x.id == opt).name_e :''"
                                :class="{'is-invalid':$v.create.from_doctor_id.$error || errors.from_doctor_id,}"
                            >
                            </multiselect>

                            <template v-if="errors.from_doctor_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.button_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>


                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.Patient') }} <span class="text-danger">*</span></label>
                            <multiselect v-if="check_data_row == 0 && check_appointment_data == 0"
                                :show-labels="false"
                                :disabled="!create.branch_id"
                                :internalSearch="false"
                                @search-change="searchCustomer"
                                @input="addCustomerDataCreate"
                                v-model="create.patient_id"
                                :options="patients.map((type) => type.id)"
                                :custom-label="(opt) => patients.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? patients.find((x) => x.id == opt).name: patients.find((x) => x.id == opt).name_e:''"
                                :class="{'is-invalid':$v.create.patient_id.$error || errors.patient_id,}"
                            >
                            </multiselect>
                            <input type="text" disabled :value="check_appointment_data == 0 ? ($i18n.locale =='ar' ? dataRow.patient.name :dataRow.patient.name_e) :appointment_data.patient_name "  v-else class="form-control">
                            <div v-if="!$v.create.patient_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>

                            <template v-if="errors.patient_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.patient_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{$t('general.InvoiceDiscount')}}
                            </label>
                            <input
                                :disabled="!create.branch_id"
                                @input="changeValue('input')"
                                type="number"
                                step="any"
                                class="form-control englishInput"
                                v-model="$v.create.invoice_discount.$model"
                                :class="{
                                    'is-invalid': $v.create.invoice_discount.$error || errors.invoice_discount,
                                    'is-valid': !$v.create.invoice_discount.$invalid && !errors.invoice_discount,
                                  }"
                            />
                            <template v-if="errors.invoice_discount">
                                <ErrorMessage v-for="(errorMessage, index) in errors.invoice_discount" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-2" v-if="client_type =='patient'">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{$t('general.Patient insurance number')}}
                            </label>
                            <input
                                :disabled="!create.branch_id"
                                type="number"
                                v-model="$v.create.patient_insurance_number.$model"
                                class="form-control"
                                :class="{
                                    'is-invalid': $v.create.patient_insurance_number.$error || errors.patient_insurance_number,
                                    'is-valid': !$v.create.patient_insurance_number.$invalid && !errors.patient_insurance_number,
                                  }"
                            />
                            <template v-if="errors.patient_insurance_number">
                                <ErrorMessage v-for="(errorMessage, index) in errors.patient_insurance_number" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>

                    <div class="col-md-2" v-if="client_type =='patient'">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.Company Insurance') }}</label>
                            <multiselect
                            @input="showCompanyInsurancesModal"
                                :show-labels="false"
                                :disabled="!create.branch_id"
                                :internalSearch="true"
                                v-model="create.company_insurance_id"
                                :options="companies_insurances.map((type) => type.id)"
                                :custom-label="(opt) => companies_insurances.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? companies_insurances.find((x) => x.id == opt).name: companies_insurances.find((x) => x.id == opt).name_e:''"
                                :class="{'is-invalid':$v.create.company_insurance_id.$error || errors.company_insurance_id,}"
                            >
                            </multiselect>


                            <template v-if="errors.company_insurance_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.company_insurance_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>

                    <div class="col-md-12 p-0 m-0">
                        <div class="page-content">
                            <div class="px-0">
                                <div class="row">
                                    <div class="col-12 col-lg-12">
                                        <!-- .row -->
                                        <hr class="row" />
                                        <div>
                                            <div
                                                class="row text-600 text-white bgc-default-tp1 py-25">
                                                <div class="col-2">{{ $t('general.service') }}</div>
                                                <div class="col-2">{{ $t('general.Price') }}</div>
                                                <div class="col-1">{{ $t('general.discount') }}</div>
                                                <template v-if="create.company_insurance_id && create.patient_insurance_number && client_type =='patient'">
                                                    <div class="col-1">{{ $t('general.Patient Amount') }}</div>
                                                    <div class="col-1">{{ $t('general.Company Amount') }}</div>
                                                </template>
                                                <div class="col-2">{{ $t('general.note') }}</div>
                                                <div class="col-2">{{ $t('general.Action') }}</div>
                                            </div>
                                            <div v-for="(item, gIndex) in create.header_details" class="text-95  text-secondary-d3" :key="gIndex">
                                                <div class="row mb-2 mb-sm-0 py-25" :class="[gIndex % 2 == 0 ? 'bgc-default-l4' : '' ]" >
                                                    <div class="col-2">
                                                        <multiselect
                                                            :show-labels="false"
                                                            @input="addServicePrice(gIndex);showServiceModal(gIndex)"
                                                            v-model="$v.create.header_details.$each[gIndex].service_id.$model"
                                                            :options="services.map((type) => type.id)"
                                                            :custom-label="(opt) => services.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? services.find((x) => x.id == opt).name: services.find((x) => x.id == opt).name_e : ''"
                                                            :class="{'is-invalid':$v.create.header_details.$each[gIndex].service_id.$error || errors.service_id,}"
                                                        >
                                                        </multiselect>
                                                    </div>

                                                    <div class="col-2">
                                                        <input
                                                            @input="totalCreate(gIndex,'input')"
                                                            @keyup="addLineEnter($event)"
                                                            v-model.number="$v.create.header_details.$each[gIndex].price_per_uint.$model"
                                                            class="form-control" step="any"
                                                            type="number"
                                                            :class="{
                                                                'is-invalid': $v.create.header_details.$each[gIndex].price_per_uint.$error || errors.price_per_uint,
                                                                'is-valid': !$v.create.header_details.$each[gIndex].price_per_uint.$invalid && !errors.price_per_uint,
                                                            }"
                                                        />
                                                    </div>

                                                    <div class="col-1">
                                                        <input
                                                            @input="discountLine(gIndex)"
                                                            @keyup="addLineEnter($event)"
                                                            v-model.number="$v.create.header_details.$each[gIndex].discount.$model"
                                                            class="form-control" step="0.00"
                                                            type="number"
                                                            :class="{
                                                                'is-invalid': $v.create.header_details.$each[gIndex].discount.$error || errors.discount,
                                                                'is-valid': !$v.create.header_details.$each[gIndex].discount.$invalid && !errors.discount,
                                                            }"
                                                        />
                                                    </div>

                                                   <template v-if="create.company_insurance_id && create.patient_insurance_number && client_type =='patient'">
                                                    <div class="col-1">
                                                        <input
                                                            @input="patient_and_company_amount(gIndex,'patient')"
                                                            @keyup="addLineEnter($event)"
                                                            v-model.number="$v.create.header_details.$each[gIndex].patient_amount.$model"
                                                            class="form-control" step="0.00"
                                                            type="number"
                                                            :class="{
                                                                'is-invalid': $v.create.header_details.$each[gIndex].patient_amount.$error || errors.patient_amount,
                                                                'is-valid': !$v.create.header_details.$each[gIndex].patient_amount.$invalid && !errors.patient_amount,
                                                            }"
                                                        />
                                                    </div>
                                                    <div class="col-1">
                                                        <input
                                                            @input="patient_and_company_amount(gIndex,'company')"
                                                            @keyup="addLineEnter($event)"
                                                            v-model.number="$v.create.header_details.$each[gIndex].company_insurance_amount.$model"
                                                            class="form-control" step="0.00"
                                                            type="number"
                                                            :class="{
                                                                'is-invalid': $v.create.header_details.$each[gIndex].company_insurance_amount.$error || errors.company_insurance_amount,
                                                                'is-valid': !$v.create.header_details.$each[gIndex].company_insurance_amount.$invalid && !errors.company_insurance_amount,
                                                            }"
                                                        />
                                                    </div>
                                                    </template>
                                                    <div class="col-2">
                                                        <input
                                                            v-model="$v.create.header_details.$each[gIndex].note.$model"
                                                            class="form-control"
                                                            type="text"
                                                            :class="{
                                                                'is-invalid': $v.create.header_details.$each[gIndex].note.$error || errors.note,
                                                                'is-valid': !$v.create.header_details.$each[gIndex].note.$invalid && !errors.note,
                                                            }"
                                                        />
                                                    </div>
                                                    <div class="col-2">
                                                        <button
                                                            v-if="create.header_details.length > 1"
                                                            type="button"
                                                            @click.prevent="removeNewField(gIndex)"
                                                            class="custom-btn-dowonload p-0"
                                                        >
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row border-b-2 brc-default-l2"></div>
                                            <div class="row mt-3">
                                                <div class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                                    {{ $t("general.Extra_note") }}
                                                </div>

                                                <div class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                                    <div class="row align-items-center bgc-primary-l3">
                                                        <div class="col-7 text-right">
                                                            {{ $t("general.TotalInvoice") }}
                                                        </div>
                                                        <div class="col-5">
                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                {{ !create.total_invoice ? '0.00' : parseFloat(create.total_invoice).toFixed(2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row align-items-center bgc-primary-l3">
                                                        <div class="col-7 text-right">
                                                            {{ $t("general.InvoiceDiscount") }}
                                                        </div>
                                                        <div class="col-5">
                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                {{ !create.invoice_discount ? '0.00' : parseFloat(create.invoice_discount).toFixed(2) }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center bgc-primary-l3">
                                                        <div class="col-7 text-right">
                                                            {{ $t("general.NetInvoice") }}
                                                        </div>
                                                        <div class="col-5">
                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                {{ !create.net_invoice ? '0.00' : parseFloat(create.net_invoice).toFixed(2) }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center bgc-primary-l3" v-if="client_type == 'patient'">
                                                        <div class="col-7 text-right">
                                                            {{ $t("general.Patient Amount") }}
                                                        </div>
                                                        <div class="col-5">
                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                {{ !create.total_patient_amount ? '0.00' : parseFloat(create.total_patient_amount).toFixed(2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row align-items-center bgc-primary-l3" v-if="client_type == 'patient'">
                                                        <div class="col-7 text-right">
                                                            {{ $t("general.Company Amount") }}
                                                        </div>
                                                        <div class="col-5">
                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                {{ !create.total_company_insurance_amount ? '0.00' : parseFloat(create.total_company_insurance_amount).toFixed(2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <hr />
                                            <div>
                                                <span class="text-secondary-d1 text-105">{{$t("general.Thank_you") }}</span>
                                                <a @click.prevent="addNewField" class="btn btn-info btn-bold px-4 float-right mt-3 mx-2 mt-lg-0">
                                                    {{$t("general.AddNewLine") }}
                                                </a>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </b-modal>
        <!--  /create   -->
    </div>
</template>

<style scoped>
.custom-panel-quotation{
    background-color: #81afca !important;
    color: lemonchiffon;
    font-size: 16px;
    border: none;
}
.page-content {
    width: 100%;
}

.total {
    color: #343a40 !important;
    font-weight: bold;
}

.text-secondary-d1 {
    color: #728299 !important;
}

.page-header {
    margin: 0 0 1rem;
    padding-bottom: 1rem;
    padding-top: .5rem;
    border-bottom: 1px dotted #e2e2e2;
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
    border-color: #dce9f0 !important;
}

.ml-n1,
.mx-n1 {
    margin-left: -.25rem !important;
}

.mr-n1,
.mx-n1 {
    margin-right: -.25rem !important;
}

.mb-4,
.my-4 {
    margin-bottom: 1.5rem !important;
}

hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 0;
    border-top: 1px solid rgba(0, 0, 0, .1);
}

.text-grey-m2 {
    color: #888a8d !important;
}

.text-success-m2 {
    color: #86bd68 !important;
}

.font-bolder,
.text-600 {
    font-weight: 600 !important;
}

.text-110 {
    font-size: 110% !important;
}

.text-blue {
    color: #478fcc !important;
}

.pb-25,
.py-25 {
    padding-bottom: .75rem !important;
}

.pt-25,
.py-25 {
    padding-top: .75rem !important;
}

.bgc-default-tp1 {
    background-color: rgba(121, 169, 197, .92) !important;
}

.bgc-default-l4,
.bgc-h-default-l4:hover {
    background-color: #f3f8fa !important;
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
    font-size: 120% !important;
}

.text-primary-m1 {
    color: #4087d4 !important;
}

.text-danger-m1 {
    color: #dd4949 !important;
}

.text-blue-m2 {
    color: #68a3d5 !important;
}

.text-150 {
    font-size: 150% !important;
}

.text-60 {
    font-size: 60% !important;
}

.text-grey-m1 {
    color: #7b7d81 !important;
}

.align-bottom {
    vertical-align: bottom !important;
}

.face {
    display: inline-block;
    text-align: center;
    margin: 0 5px;
}

.face .face-name {
    background-color: #6dc6f5;
    padding: 0px 8px;
    font-size: 16px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 7px;
    display: block;
}
.row-danger {
    background-color:#f6a9a9 !important;
}
</style>
