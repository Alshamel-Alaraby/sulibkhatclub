<script>
import ErrorMessage from "../../../widgets/errorMessage";
import loader from "../../../general/loader";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";
import translation from "../../../../helper/mixin/translation-mixin";
import {minValue, required, requiredIf} from "vuelidate/lib/validators";
import {formatDateOnly} from "../../../../helper/startDate";
import adminApi from "../../../../api/adminAxios";
import Swal from "sweetalert2";
import {dynamicSortString} from "../../../../helper/tableSort";

/**
 * Advanced Table component
 */
export default {
    name: "createOrUpdateMoney",
    props: {
        document: {
            default: null,
        },
        document_id: {
            default: null,
        },
        dataRow:{
            default:null,
        },
        id: {
            default:'create'
        },
        other_data:{
            default:null,
        },
    },
    mixins: [translation],
    components: {
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect
    },
    data() {
        return {
            financial_years_validate:true,
            customer_data_edit: "",
            customer_data_create: "",
            debounce: {},
            customers: [],
            salesmen: [],
            paymentMethods: [],
            customerDebits:[],
            customerCredit:[],
            moduleTypes:[],
            totalDebitSettlement: 0,
            totalCreditSettlement: 0,
            serial_number:"",
            relatedDocumentNumbers: [],
            relatedDocuments: [],
            openTransfer:false,
            amountPaid:0,
            transaction:[],
            clientTypes:[],
            with_paid:false,
            open_invoice_details:false,
            enabled3: true,
            isLoader: false,
            invoice_id: null,
            create: {
                document_id: this.document_id,
                document_module_type_id: this.document?this.document.document_module_type_id:null,
                module_type_id: null,
                branch_id: null,
                date: this.formatDate(new Date()),
                salesmen_id: null,
                payment_method_id: null,
                customer_id: null,
                amount:0,
                client_type_id:1,
                notes:''
            },
            errors: {},
            branches: [],
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
            date: {required},
            customer_id: {required},
            branch_id: {required},
            salesmen_id: {required},
            payment_method_id: {required},
            amount: {required},
            document_id: {required},
            client_type_id: {required},
            notes: {},
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.$store.dispatch('locationIp/getIp');
    },
    methods: {
        async resetModalCreateOrUpdate()
        {
            this.relatedDocuments = this.document.document_relateds;
            if (this.dataRow)
            {
                await this.getDataRow();
                await this.resetModalEdit(this.dataRow.id);
            }else{
                await this.resetModal();
            }
        },
        resetModalHiddenCreateOrUpdate ()
        {
            if (this.dataRow)
            {
                this.resetModalHiddenEdit(this.dataRow.id);
            }else{
                this.resetModalHidden();
            }
        },
        /**
         *  data create (create)
         */
        dataCreate()
        {
            this.serial_number = "";
            this.customer_data_create='';
            this.customer_data_edit='';
            this.customerCredit=[];
            this.customerDebits=[];
            this.totalDebitSettlement= 0;
            this.totalCreditSettlement= 0;
            this.openTransfer=false;
            this.amountPaid=0;
            this.amount=0;
            this.open_invoice_details=false;
            this.financial_years_validate = true;
            this.with_paid = false;
            this.create = {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesmen_id: null,
                payment_method_id: null,
                customer_id: null,
                amount:0,
                document_id:this.document_id,
                client_type_id:1,
                module_type_id: null,
                notes:''
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
            this.getBranches();
            this.getPaymentMethod();
            this.getSalesmen();
            this.getClientType();
            if (this.document && this.document.document_module_type_id)
            {
                this.getModuleType(this.document.document_module_type.db_table);
            }
            // هنرجعلها
            if (this.other_data)
            {
                this.create.module_type_id = this.other_data.unit_id;
                this.getCustomerInRoom();
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
        resetForm() {
            this.dataCreate();
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
        },
        Submit(is_break = false)
        {
            if (this.dataRow)
            {
                this.editSubmit(this.dataRow.id);
            }else{
                this.AddSubmit(is_break);
            }
        },
        AddSubmit() {
            if (this.open_invoice_details)
            {
                if (!this.validationBeforeSubmit()){return;}
            }
            if (this.$v.create.$invalid || !this.financial_years_validate) {
                this.$v.create.$touch();
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                let data_create = this.create;
                let break_settlement = [];
                this.customerDebits.forEach((el,index) => {
                    if (el.settlement_amount > 0) {
                        break_settlement.push({
                            'break_id': el.id,
                            'amount': el.settlement_amount,
                        })
                    }
                });
                this.customerCredit.forEach((el,index) => {
                    if (el.settlement_amount > 0) {
                        break_settlement.push({
                            'break_id': el.id,
                            'amount': el.settlement_amount,
                        })
                    }
                });
                data_create.break_settlement = break_settlement;
                adminApi
                    .post(`voucher-headers`, {...data_create, company_id: this.company_id})
                    .then((res) => {
                        this.$emit("created");
                        this.is_disabled = true;
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Addedsuccessfully")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: `${this.$t("general.Error")}`,
                                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                            });
                        }
                    })
                    .finally(() => {
                        this.isLoader = false;
                    });
            }
        },
        /**
         *  edit screen
         */
        editSubmit(id) {
            this.$v.edit.$touch();
            if (this.$v.edit.$invalid  || !this.financial_years_validate) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .put(`voucher-headers/${id}`, {...this.edit})
                    .then((res) => {
                        this.$bvModal.hide(`modal-edit-${id}`);
                        this.getData();
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Editsuccessfully")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: `${this.$t("general.Error")}`,
                                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                            });
                        }
                    })
                    .finally(() => {
                        this.isLoader = false;
                    });
            }
        },
        /**
         *  get workflows
         */
        getCustomerInRoom() {
            this.isLoader = true;
            adminApi
                .get(`/document-headers/customer-room?unit_id=${this.other_data.unit_id}&date_from=${this.other_data.date_from}&date_to=${this.other_data.date_to}`)
                .then((res) => {
                    let l = res.data;
                    if (l)
                    {
                        this.create.salesmen_id = l.employee_id;
                        if (l.employee_id)
                        {
                            this.customer_data_create = l.customer;
                            this.getCustomers(l.employee_id);
                            this.create.customer_id = l.customer_id;
                            this.customers.push(this.customer_data_create);
                            // لو فى بريك افتحها لان مع ديفونا لا يوجد بريك
                            // this.getBreakCustomer(l.customer_id);
                        }
                    }
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
        },
        getClientType() {
            this.isLoader = true;
             adminApi
                .get(`/client-types/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    this.clientTypes = l;
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
        },
        getBranches() {
            this.isLoader = true;
            adminApi
                .get(`/branches?document_id=${this.document_id}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.branches = l;
                    if (!this.dataRow){
                        if (this.branches.length == 1)
                        {
                            this.create.branch_id = this.branches[0].id;
                            this.getSerialNumber(this.create.branch_id);
                        }
                    }
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        getSerialNumber(e)
        {
            let date = this.create.date;
            let shortYear =new Date(date).getFullYear();
            let twoDigitYear = shortYear.toString().substr(-2);
            let branch = this.branches.find((row) => e == row.id);
            let serial = branch.serials.find((row) => this.document_id == row.document_id);
            this.serial_number = `${twoDigitYear}-${branch.id}-${this.document_id}-${serial.perfix}`;

            let document_id = this.create.related_document_id;
            let branch_id = this.create.branch_id;
            if (document_id && branch_id)
            {
                this.handelRelatedDocument()
            }
        },
        getSalesmen() {
            this.isLoader = true;
            adminApi
                .get(`/employees?is_salesman=1&customer_handel=1`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.salesmen = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        getCustomerSalesman(e)
        {
            let employee_id = e;
            if (employee_id)
            {
                this.getCustomers(employee_id);
            }
        },
        getCustomers(employee_id=null,search='') {
            this.isLoader = true;
             adminApi
                .get(`/general-customer?limet=10&company_id=${this.company_id}&employee_id=${employee_id}&search=${search}&columns[0]=name&columns[1]=name_e&columns[2]=id`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.customers = l;
                    if (this.customer_data_edit)
                    {
                        if (!this.customers.find((e) => e.id == this.customer_data_edit.id))
                        {
                            this.customers.push(this.customer_data_edit);
                        }
                    }
                    if (this.customer_data_create)
                    {
                        if (!this.customers.find((e) => e.id == this.customer_data_create.id))
                        {
                            this.customers.push(this.customer_data_create);
                        }
                    }
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        getPaymentMethod() {
            this.isLoader = true;
            adminApi.get(`/payment-methods`)
                .then((res) => {
                    let l = res.data.data;
                    this.paymentMethods = l;
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
        },
        getBreakCustomer(id) {
            this.isLoader = true;
            this.customerDebits = [];
            this.customerCredit = [];
            this.customer_data_create = this.customers.find((e) => e.id == this.create.customer_id);
            this.customer_data_edit = this.customers.find((e) => e.id == this.create.customer_id);
             adminApi
                .get(`/recievable-payable/document-with-money-details?customer_id=${id}&with_paid=${this.with_paid}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.forEach((e) => {
                        if (parseFloat(e.debit) > 0)
                        {
                            this.customerDebits.push({
                                id:e.id,
                                break_type:e.break_type,
                                document:e.document,
                                amount:parseFloat(e.debit),
                                instalment_date:e.instalment_date,
                                // salesman: this.handelSalesman(e),
                                serial_number: this.handelSerial(e),
                                amount_status:e.amount_status,
                                settlement_amount:0,
                                prev_settlement:e.prev_settlement??0,
                            });
                        }else {
                            this.customerCredit.push({
                                id:e.id,
                                break_type:e.break_type,
                                document:e.document,
                                amount:parseFloat(e.credit),
                                instalment_date:e.instalment_date,
                                // salesman: this.handelSalesman(e),
                                serial_number: this.handelSerial(e),
                                amount_status:e.amount_status,
                                settlement_amount:0,
                                prev_settlement:e.prev_settlement??0,
                            });
                        }

                    });
                    this.accountTotalAmount();
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        handelSalesman(e)
        {
            if (e.break_type == 'invoice')
            {
                if (e.invoice)
                {
                    if (e.invoice.salesman)
                    {
                        return this.$i18n.locale == "ar" ? e.invoice.salesman.name : e.invoice.salesman.name_e;
                    }else {
                        return '---';
                    }
                }else {
                    return '---';
                }
            }
            if (e.break_type == 'contract')
            {
                if (e.contract)
                {
                    if (e.contract.salesman)
                    {
                        return this.$i18n.locale == "ar" ? e.contract.salesman.name : e.contract.salesman.name_e;
                    }else {
                        return '---';
                    }
                }else {
                    return '---';
                }
            }
            if (e.break_type == 'documentHeader')
            {
                if (e.documentHeader)
                {
                    if (e.documentHeader.employee)
                    {
                        return this.$i18n.locale == "ar" ? e.documentHeader.employee.name : e.documentHeader.employee.name_e;
                    }else {
                        return '---';
                    }
                }else {
                    return '---';
                }
            }

            return '---';
        },
        handelSerial(e)
        {
            if (e.break_type == 'invoice')
            {
                if (e.invoice)
                {
                    return e.invoice.prefix;
                }else {
                    return '---';
                }
            }
            if (e.break_type == 'contract')
            {
                if (e.contract)
                {
                    return e.contract.prefix;
                }else {
                    return '---';
                }
            }
            if (e.break_type == 'documentHeader')
            {
                if (e.documentHeader)
                {
                    return e.documentHeader.prefix;
                }else {
                    return '---';
                }
            }

            return '---';
        },
        getModuleType(db_table='') {
            this.isLoader = true;
             adminApi
                .get(`/document-module-type/get-drop-down-model?db_table=${db_table}`)
                .then((res) => {
                    let l = res.data.data;
                    this.moduleTypes = l;
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
        },
        /**
         *   show Modal (edit)
         */
        async resetModalEdit(id) {
            this.customerBreak=[];
            this.breakTransactions=[];
            this.totalOrderAmount= 0;
            this.totalTransferAmount= 0;
            this.openTransfer=false;
            this.amountPaid=0;
            this.remainingAmount=0;
            this.amount=0;
            let invoice = this.invoices.find((e) => id == e.id);
            this.serial_number = invoice.prefix;
            this.invoice_id = invoice.id;
            await this.getCustomers(invoice.salesmen_id);
            await this.getSalesmen();
            await this.getPaymentMethod();
            await this.getBranches();
            this.edit.date = invoice.date;
            this.edit.customer_id = invoice.customer_id;
            this.edit.salesmen_id = invoice.salesmen_id;
            this.edit.payment_method_id = invoice.payment_method_id;
            this.edit.notes = invoice.notes;
            this.edit.branch_id = invoice.branch_id;
            this.edit.document_id = invoice.document_id;
            this.edit.amount = invoice.amount;
            this.customer_data_edit = invoice.customer;
            if(this.document && this.document.attributes && this.parseInt(document.attributes.customer) == -1){
                await this.getBreakCustomer(this.edit.customer_id);
                await this.getTransactions(id);
                this.amount= this.totalTransferAmount ;
            }
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.customerBreak=[];
            this.breakTransactions=[];
            this.totalOrderAmount= 0;
            this.customer_data_edit = '';
            this.totalTransferAmount= 0;
            this.openTransfer=false;
            this.amountPaid=0;
            this.remainingAmount=0;
            this.amount=0;
            this.errors = {};
            this.serial_number = "";
            this.financial_years_validate = true;
            this.edit = {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesmen_id: null,
                payment_method_id: null,
                document_id: this.document_id,
                customer_id: null,
                amount:0,
                notes:''
            };
            this.invoice_id = null;
        },
        /**
         *  start  dynamicSortString
         */

        formatDate(value) {
            return formatDateOnly(value);
        },

        searchCustomer(e) {
            let search = e??'';
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.getCustomers(this.create.salesmen_id,search);
            }, 500);

        },
        titleModelName()
        {
            if(this.document) {
                if (!this.dataRow)
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
        async checkFinancialYears(date,branch_id) {
            this.isLoader = true;
            await adminApi
                .get(`/document-headers/check-date?date=${date}`)
                .then((res) => {
                    let l = res.data;
                    if(!l)
                    {
                        this.financial_years_validate= false;
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.The date is outside the permitted fiscal year")}`,
                        });
                        this.serial_number = '';
                    }else{
                        this.financial_years_validate= true;
                        let shortYear =new Date(date).getFullYear();
                        let twoDigitYear = shortYear.toString().substr(-2);
                        let branch = this.branches.find((row) => branch_id == row.id);
                        let serial = branch.serials.find((row) => this.document_id == row.document_id);
                        this.serial_number = `${twoDigitYear}-${branch.id}-${this.document_id}-${serial.perfix}`;
                    }
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
        },
        checkDebitSettlement(index)
        {
            let settlement_amount = this.customerDebits[index].settlement_amount;
            let prev_settlement = this.customerDebits[index].prev_settlement;
            let amount = this.customerDebits[index].amount;
            if(parseFloat(settlement_amount) < 0){
                this.customerDebits[index].settlement_amount = 0 ;
            }else if ((amount - prev_settlement) >= settlement_amount ){
                this.accountTotalAmount();
            }else if((amount - prev_settlement) < settlement_amount) {
                this.customerDebits[index].settlement_amount = amount - prev_settlement;
                this.accountTotalAmount();
            }

        },
        checkCreditSettlement(index)
        {
            let settlement_amount = this.customerCredit[index].settlement_amount;
            let prev_settlement = this.customerCredit[index].prev_settlement;
            let amount = this.customerCredit[index].amount;
            if(parseFloat(settlement_amount) < 0){
                this.customerCredit[index].settlement_amount = 0 ;
            }else if ((amount - prev_settlement) >= settlement_amount ){
                this.accountTotalAmount();
            }else if((amount - prev_settlement) < settlement_amount) {
                this.customerCredit[index].settlement_amount = amount - prev_settlement;
                this.accountTotalAmount();
            }

        },
        //Transfer break To Transactions
        orderTransferToTransaction(index){
            if (this.remainingAmount > 0){
                let orderTransfer = "";
                let endAmount = 0;
                endAmount = parseInt(this.remainingAmount) - parseInt(this.customerBreak[index].amount);
                if (endAmount >= 0){
                    orderTransfer = this.customerBreak.splice(index,1);
                    orderTransfer[0].amount_status = "Paid";
                    this.breakTransactions.push(orderTransfer[0]);
                }else {
                    this.breakTransactions.push({
                        id:this.customerBreak[index].id,
                        break_type:this.customerBreak[index].break_type,
                        document:this.customerBreak[index].document,
                        instalment_date:this.customerBreak[index].instalment_date,
                        serial_number:this.customerBreak[index].serial_number,
                        salesman:this.customerBreak[index].salesman,
                        amount:parseInt(this.remainingAmount),
                        amount_status:"Paid_Partially",
                    });
                    this.customerBreak[index].amount = parseInt(this.customerBreak[index].amount) - parseInt(this.remainingAmount);
                }

                this.accountTotalAmount();

                this.remainingAmount = parseInt(this.amountPaid) - parseInt(this.totalTransferAmount);
            }
        },
        //return Transaction To customer Break
        ReturnTransactionToOrder(index){
            let orderTransfer = "";
            let orderReturn = "";
            this.remainingAmount += parseInt(this.breakTransactions[index].amount);
            orderReturn = this.customerBreak.find(e => e.id == this.breakTransactions[index].id);
            if (orderReturn){
                orderReturn.amount += this.breakTransactions[index].amount;
                this.breakTransactions.splice(index,1);
            }else {
                orderTransfer = this.breakTransactions.splice(index,1);
                this.customerBreak.push(orderTransfer[0]);
            }
            this.accountTotalAmount();
        },
        // calculate total amount in customer Break and Transactions
        accountTotalAmount(){
            //account total amount order
            this.totalDebitSettlement = 0;
            this.customerDebits.forEach((el,index)=>{
                this.totalDebitSettlement += parseFloat(el.settlement_amount);
            });

            //account total amount transfer order
            this.totalCreditSettlement = 0;
            this.customerCredit.forEach((el,index)=>{
                this.totalCreditSettlement += parseFloat(el.settlement_amount);
            });
        },
        validationBeforeSubmit()
        {
            let amount = this.create.amount;
            if (parseInt(this.document.attributes.customer) == -1){
                if ( amount !=(this.totalDebitSettlement - this.totalCreditSettlement)){
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.There_is_an_error_in_the_calculations")}`,
                    });
                    return false;
                }
            }else if(parseInt(this.document.attributes.customer) == 1){
                if ( amount !=(this.totalCreditSettlement - this.totalDebitSettlement)){
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.There_is_an_error_in_the_calculations")}`,
                    });
                    return false;
                }
            }
            return true;
        },
        resetDebitsAndCredit()
        {
            this.customerDebits.forEach((e) => {
                e.settlement_amount=0
            });
            this.customerCredit.forEach((e) => {
                e.settlement_amount=0
            });
            this.totalDebitSettlement = 0;
            this.totalCreditSettlement = 0;
        },
        autoSettlement()
        {
            this.resetDebitsAndCredit();
            let amount = this.create.amount;
            if (parseInt(this.document.attributes.customer) == -1){
                this.customerDebits.forEach((e,index) => {
                    if ((e.amount - e.prev_settlement) != 0 && amount > 0)
                    {
                        if (amount >=  e.amount - e.prev_settlement)
                        {
                            e.settlement_amount= e.amount - e.prev_settlement;
                            amount -= e.amount - e.prev_settlement;
                        }else{
                            e.settlement_amount= amount;
                            amount -= amount
                        }
                    }
                });
            }else if(parseInt(this.document.attributes.customer) == 1){
                this.customerCredit.forEach((e,index) => {
                    if ((e.amount - e.prev_settlement) != 0 && amount > 0)
                    {
                        if (amount >=  e.amount - e.prev_settlement)
                        {
                            e.settlement_amount= e.amount - e.prev_settlement;
                            amount -= e.amount - e.prev_settlement;
                        }else{
                            e.settlement_amount= amount;
                            amount -= amount
                        }

                    }
                });
            }
            this.accountTotalAmount();
            if (amount)
            {
                Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.TheSettlementAmountIsGreaterThanTheInstallments")}`,
                });
            }

        }
    }
};
</script>

<template>
    <div>

        <!--  create   -->
        <b-modal dialog-class="modal-full-width" :id="id"
                 :title="titleModelName()"
                 title-class="font-18" body-class="p-4 "
                 :hide-footer="true"
                 @show="resetModalCreateOrUpdate"
                 @hidden="resetModalHiddenCreateOrUpdate">
            <form>
                <div class="row" >
                    <div :class="['col-md-12','mb-3 d-flex justify-content-end',]">
<!--                        <b-button v-if="!dataRow" variant="primary" type="button" class="font-weight-bold px-2 mx-1">-->
<!--                            {{ $t("general.print") }}-->
<!--                            <i class="fe-printer"></i>-->
<!--                        </b-button>-->
<!--                        <b-button v-else v-print="'#printReservation'" variant="primary" type="button" class="font-weight-bold px-2 mx-1">-->
<!--                            {{ $t("general.print") }}-->
<!--                            <i class="fe-printer"></i>-->
<!--                        </b-button>-->
                        <b-button v-if="!dataRow" variant="success" :disabled="!is_disabled" @click.prevent="resetForm" type="button"
                                  :class="['font-weight-bold px-2 mx-1', is_disabled ? 'mx-2' : '']">
                            {{ $t("general.AddNewRecord") }}
                        </b-button>
                        <template v-if="!is_disabled">
                            <a class="btn btn-success mx-1" v-if="!isLoader" @click.prevent="Submit(false)">
                                {{ dataRow ? $t('general.Edit') : $t("general.Add") }}
                            </a>
                            <b-button variant="success" class="mx-1" disabled v-else>
                                <b-spinner small></b-spinner>
                                <span class="sr-only">{{ $t("login.Loading") }}...</span>
                            </b-button>

                        </template>
                        <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(`${id}`)">
                            {{ $t("general.Cancel") }}
                        </b-button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.Branch') }} <span class="text-danger">*</span></label>

                            <multiselect
                                @input="getSerialNumber('create',$event)"
                                v-model="create.branch_id"
                                :options="branches.map((type) => type.id)"
                                :custom-label="(opt) => $i18n.locale == 'ar'? branches.find((x) => x.id == opt).name: branches.find((x) => x.id == opt).name_e"
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">
                                {{ $t('general.Date') }}
                                <span class="text-danger">*</span>
                            </label>
                            <date-picker
                                :disabled="!create.branch_id"
                                @input="changeDateDocument('create')"
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
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.documentSalesmen') }} <span class="text-danger">*</span></label>

                            <multiselect
                                :disabled="!create.branch_id"
                                @input="getCustomerSalesman"
                                v-model="create.salesmen_id"
                                :options="salesmen.map((type) => type.id)"
                                :custom-label=" (opt) => $i18n.locale == 'ar' ? salesmen.find((x) => x.id == opt).name: salesmen.find((x) => x.id == opt).name_e"
                                :class="{'is-invalid':$v.create.salesmen_id.$error || errors.salesmen_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.salesmen_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.salesmen_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.salesmen_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">
                                {{getCompanyKey('money_voucher_client_type')}}
                                <span class="text-danger">*</span>
                            </label>

                            <multiselect
                                :disabled="true"
                                v-model="create.client_type_id"
                                :options="clientTypes.map((type) => type.id)"
                                :custom-label=" (opt) => clientTypes.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? clientTypes.find((x) => x.id == opt).name: clientTypes.find((x) => x.id == opt).name_e:''"
                                :class="{'is-invalid':$v.create.client_type_id.$error || errors.client_type_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.client_type_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.client_type_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.client_type_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.client') }} <span class="text-danger">*</span></label>
                            <multiselect
                                :disabled="!create.branch_id"
                                :internalSearch="false"
                                @search-change="searchCustomer"
                                @input="getBreakCustomer(create.customer_id)"
                                v-model="create.customer_id"
                                :options="customers.map((type) => type.id)"
                                :custom-label="(opt) =>customers.find((x) => x.id == opt)?$i18n.locale == 'ar' ? customers.find((x) => x.id == opt).name: customers.find((x) => x.id == opt).name_e:''"
                                :class="{'is-invalid':$v.create.customer_id.$error || errors.customer_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.customer_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>

                            <template v-if="errors.customer_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.customer_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="document && document.document_module_type_id">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ $i18n.locale == 'ar' ? document.document_module_type.title : document.document_module_type.title_e }}
                            </label>
                            <multiselect
                                :show-labels="false"
                                v-model="create.module_type_id"
                                :options="moduleTypes.map((type) => type.id)"
                                :custom-label="(opt) =>moduleTypes.find((x) => x.id == opt) ? $i18n.locale == 'ar' ?moduleTypes.find((x) => x.id == opt).name:moduleTypes.find((x) => x.id == opt).name_e:''"
                            >
                            </multiselect>
                            <template v-if="errors.module_type_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.module_type_id" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">{{ $t("general.paymentMethod") }}
                                <span class="text-danger">*</span>
                            </label>
                            <multiselect
                                :disabled="!create.branch_id"
                                v-model="create.payment_method_id"
                                :options="paymentMethods.map((type) => type.id)" :custom-label="
                                                (opt) => $i18n.locale == 'ar' ? paymentMethods.find((x) => x.id == opt).name : paymentMethods.find((x) => x.id == opt).name_e"
                                :class="{ 'is-invalid':$v.create.payment_method_id.$error || errors.payment_method_id,}"
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ $t("general.amount") }}</label>
                            <input
                                :disabled="!create.branch_id"
                                type="number"
                                class="form-control"
                                step="any"
                                v-model="create.amount"
                                :class="{'is-invalid':$v.create.amount.$error || errors.amount,}"
                            />
                            <div v-if="!$v.create.amount.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.amount">
                                <ErrorMessage v-for="(errorMessage, index) in errors.amount"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ $t("general.note") }}</label>
                            <input
                                type="text"
                                class="form-control"
                                step="any"
                                v-model="create.notes"
                                :class="{'is-invalid':$v.create.notes.$error || errors.notes,}"
                            />
                            <template v-if="errors.notes">
                                <ErrorMessage v-for="(errorMessage, index) in errors.notes"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="document && parseInt(document.is_break) == 2">
                        <div class="form-check mt-3">
                            <input style="transform: scale(1.5);" type="checkbox" v-model="open_invoice_details" value="true" id="flexCheckDefault1">
                            <label style="padding:9px" class="form-check-label">
                                {{$t('general.showInvoiceDetails')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="create.customer_id && open_invoice_details">
                        <div class="form-check mt-3">
                            <input style="transform: scale(1.5);" @change="getBreakCustomer(create.customer_id)" type="checkbox" v-model="with_paid" value="true" id="flexCheckDefault">
                            <label style="padding:9px" class="form-check-label">
                                {{$t('general.showPayment')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="create.amount && create.customer_id && open_invoice_details">
                        <b-button
                            variant="primary"
                            class="mx-1 mt-3"
                            @click.prevent="autoSettlement"
                        >
                            {{ $t("general.AutoSettlement") }}
                        </b-button>
                    </div>
                    <div class="col-md-12" v-if="document && document.attributes && parseInt(document.attributes.customer) != 0 && parseInt(document.is_break) > 0 && open_invoice_details">
                        <div class="page-content">
                            <div class="px-0">
                                <div class="row mt-4">
                                    <div class="col-12 col-lg-12">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="text-center text-150">
                                                    <i style="font-size:20px" class="fa fa-book fa-2x text-success-m2 mr-1"></i>
                                                    <span class="text-default-d3">{{$t("general.invoice_details")}}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- .row -->
                                        <div class="mt-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card-header p-0">
                                                        <h3 class="card-title float-left">
                                                            {{$t('general.CustomerDebts')}}
                                                        </h3>
                                                    </div>
                                                    <div class="table-responsive mb-3 custom-table-theme position-relative">
                                                        <!--       start loader       -->
                                                        <loader size="large" v-if="isLoader" />
                                                        <!--       end loader       -->

                                                        <table class="table table-borderless table-hover table-centered m-0" >
                                                            <thead>
                                                            <tr>
                                                                <th>{{ $t("general.documentSerial") }}</th>
                                                                <!--                                                                <th>{{ $t("general.salesmen") }}</th>-->
                                                                <th>{{ $t("general.type") }}</th>
                                                                <th>{{ $t("general.Date") }}</th>
                                                                <th>{{ $t("general.amount") }}</th>
                                                                <th>{{ $t("general.prev_settlement") }}</th>
                                                                <th>{{ $t("general.settlement_amount") }}</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody v-if="customerDebits.length > 0">
                                                            <tr v-for="(data, index) in customerDebits"
                                                                :key="data.id"
                                                                class="body-tr-custom"
                                                            >
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">{{ data.serial_number }}</h5>
                                                                </td>
                                                                <!--                                                                <td>-->
                                                                <!--                                                                    <h5 class="m-0 font-weight-normal">{{ data.salesman }}</h5>-->
                                                                <!--                                                                </td>-->
                                                                <td>
                                                                    <h5 v-if="data.document" class="m-0 font-weight-normal">{{$i18n.locale == "ar" ? data.document.name : data.document.name_e}}</h5>
                                                                    <h5 v-else class="m-0 font-weight-normal">---</h5>
                                                                </td>
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">
                                                                        {{ data.instalment_date }}
                                                                    </h5>
                                                                </td>
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">{{ data.amount }}</h5>
                                                                </td>
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">{{ data.prev_settlement }}</h5>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input
                                                                            @input="checkDebitSettlement(index)"
                                                                            type="number"
                                                                            step="any"
                                                                            class="form-control englishInput"
                                                                            v-model="customerDebits[index].settlement_amount"
                                                                        />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr v-if="customerDebits.length > 0" class="total-amount">
                                                                <td></td>
                                                                <td></td>
                                                                <td colspan="3"><h5 class="m-0 font-weight-normal">{{$t('general.totalAmountSettlement')}}</h5></td>
                                                                <td v-html="totalDebitSettlement" class="amount-red"></td>
                                                            </tr>
                                                            </tbody>
                                                            <tbody v-else>
                                                            <tr>
                                                                <th class="text-center" colspan="6">
                                                                    {{ $t("general.notDataFound") }}
                                                                </th>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card-header p-0">
                                                        <h3 class="card-title float-left">
                                                            {{$t('general.CustomerCredit')}}
                                                        </h3>
                                                    </div>
                                                    <div class="table-responsive mb-3 custom-table-theme position-relative">
                                                        <!--       start loader       -->
                                                        <loader size="large" v-if="isLoader" />
                                                        <!--       end loader       -->

                                                        <table class="table table-borderless table-hover table-centered m-0" >
                                                            <thead>
                                                            <tr>
                                                                <th>{{ $t("general.documentSerial") }}</th>
                                                                <!--                                                                <th>{{ $t("general.salesmen") }}</th>-->
                                                                <th>{{ $t("general.type") }}</th>
                                                                <th>{{ $t("general.Date") }}</th>
                                                                <th>{{ $t("general.amount") }}</th>
                                                                <th>{{ $t("general.prev_settlement") }}</th>
                                                                <th>{{ $t("general.settlement_amount") }}</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody v-if="customerCredit.length > 0">
                                                            <tr v-for="(data, index) in customerCredit"
                                                                :key="data.id"
                                                                class="body-tr-custom"
                                                            >
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">{{ data.serial_number }}</h5>
                                                                </td>
                                                                <!--                                                                <td>-->
                                                                <!--                                                                    <h5 class="m-0 font-weight-normal">{{ data.salesman }}</h5>-->
                                                                <!--                                                                </td>-->
                                                                <td>
                                                                    <h5 v-if="data.document" class="m-0 font-weight-normal">{{$i18n.locale == "ar" ? data.document.name : data.document.name_e}}</h5>
                                                                    <h5 v-else class="m-0 font-weight-normal">---</h5>
                                                                </td>
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">
                                                                        {{ data.instalment_date }}
                                                                    </h5>
                                                                </td>
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">{{ data.amount }}</h5>
                                                                </td>
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">{{ data.prev_settlement }}</h5>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input
                                                                            @input="checkCreditSettlement(index)"
                                                                            type="number"
                                                                            step="any"
                                                                            class="form-control englishInput"
                                                                            v-model="customerCredit[index].settlement_amount"
                                                                        />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr v-if="customerCredit.length > 0" class="total-amount">
                                                                <td></td>
                                                                <td></td>
                                                                <td colspan="3"><h5 class="m-0 font-weight-normal">{{$t('general.totalAmountSettlement')}}</h5></td>
                                                                <td v-html="totalCreditSettlement" class="amount-red"></td>
                                                            </tr>

                                                            </tbody>
                                                            <tbody v-else>
                                                            <tr>
                                                                <th class="text-center" colspan="6">
                                                                    {{ $t("general.notDataFound") }}
                                                                </th>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
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
.amount-red{
    color: red;
}
.total-amount{
    background-color: rgba(0,0,0,.075);
}
</style>
