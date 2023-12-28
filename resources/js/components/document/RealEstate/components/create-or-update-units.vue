<script>
import ErrorMessage from "../../../widgets/errorMessage";
import loader from "../../../general/loader";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";
import translation from "../../../../helper/mixin/translation-mixin";
import {required} from "vuelidate/lib/validators";
import {formatDateOnly} from "../../../../helper/startDate";
import adminApi from "../../../../api/adminAxios";
import Swal from "sweetalert2";
// import PrintInvoice from "../print/print-general-item-invoice";
// import transactionBreak from "../../../create/receivablePayment/transactionBreak/transactionBreak";


/**
 * Advanced Table component
 */
export default {
    name: "createOrUpdateUnits",
    props: {
        document: {
            default: null,
        },
        document_id: {
            default: null,
        },
        dataRow: {
            default: null,
        },
        idEdit: {
          default: null,
        },
        id: {
            default: "create",
        },
        type: {
            default: 'create'
        }
    },
    mixins: [translation],
    components: {
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
        // PrintInvoice,
        // transactionBreak,

    },
    data() {
        return {
            financial_years_validate: true,
            tenant_data_edit: "",
            tenant_data_create: "",
            openingBreak: "",
            debounce: {},
            isPermission: {},
            tenants: [],
            units: [],
            services: [],
            unit_ids: [],
            payment_types: [],
            salesmen: [],
            statuses: [],
            externalSalesman: [],
            serial_number: "",
            showValue: true,
            relatedDocumentNumbers: [],
            relatedDocuments: [],
            paymentMethods: [],
            enabled3: true,
            isLoader: false,
            showServiceModal: false,
            create: {
                document_id: this.document_id,
                document_module_type_id: this.document
                    ? this.document.document_module_type_id
                    : null,
                // document_status_id: null,
                // reason: "",
                branch_id: null,
                date: this.formatDate(new Date()),
                serial_id: null,
                related_document_id: null,
                related_document_number: null,
                related_document_prefix: "",
                employee_id: null,
                tenant_id: null,
                notice_period: 0, // it should be how many months before the tenant leave
                insurance_amount: 0,
                automatic_renews: "",
                external_salesmen_id: null,
                commission: 0,
                posted: "",
                receipt_print_detail: "", // it is 0 or 1
                company_id: null, // ask alaa to add
                // tenant_type: 1,

                units: [
                    {
                        unit_id: null,
                        unit_services: [],
                    }
                ],
                header_details: [
                    {
                        from_date: this.formatDate(new Date()),
                        to_date: "",
                        rent_amount: 0,
                        payment_type_id: null,
                        print_day: null,
                        due_day: null,
                    },
                ],
            },
            errors: {},
            branches: [],
            is_disabled: false,
            company_id: null,
            printObj: {
                id: "printReservation",
            },
            dataOfRow: "",
        };
    },
    validations: {
        create: {
            document_id: {required},
            // document_status_id: {required},
            reason: {},
            branch_id: {required},
            date: {required},
            serial_id: {required},
            related_document_id: {},
            related_document_number: {},
            related_document_prefix: {},
            employee_id: {required},
            tenant_id: {required},
            notice_period: {required},
            insurance_amount: {required},
            // tenant_type: {},
            attendants: {},
            automatic_renews: {},
            external_salesmen_id: {required},
            commission: {},
            posted: {},
            receipt_print_detail: {},
            // payment_method_id: { required },
            // total_invoice: { required },
            // invoice_discount: { required },
            // attendans_num: { required },
            // net_invoice: { required },
            // sell_method_discount: {},
            // unrelaized_revenue: {},
            units: {
                required,
                $each: {
                    unit_id: {required},
                    unit_services: {required}
                },
            },
            header_details: {
                required,
                $each: {
                    contract_header_id: {},
                    from_date: {required},
                    to_date: {required},
                    rent_amount: {required},
                    payment_type_id: {required},
                    print_day: {required},
                    due_day: {required},
                },
            },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.$store.dispatch("locationIp/getIp");
    },
    //watch for unit
    watch: {
        unit_ids: function (newUnitIds) {
            this.create.units = [];
            newUnitIds.forEach(unitId => {
                this.create.units.push({
                    unit_id: unitId,
                    unit_services: [],
                });
            });
        }
    },
    methods: {
        test() {
            console.log(this.create.units)
        },
         resetModalCreateOrUpdate() {
            this.relatedDocuments = this.document.document_relateds;
             setTimeout( async () => {
            if (this.dataRow) {
                await this.getDataRow();
                await this.resetModalEdit(this.dataRow.id);
            } else {
                await this.resetModal();
            }
             },50);
        },
        resetModalHiddenCreateOrUpdate() {
            if (this.dataRow) {
                this.resetModalHiddenEdit(this.dataRow.id);
            } else {
                this.resetModalHidden();
            }
        },
        // changeValue(index = null) {
        //   let sum = 0;
        //   this.create.header_details.forEach((e) => {
        //     sum += e.price_per_uint * e.quantity;
        //   });
        //   this.create.total_invoice = sum;
        //   this.create.net_invoice = sum - this.create.invoice_discount;
        //   // this.calculateDiscountLine();
        // },
        // calculateDiscountLine() {
        //   this.create.header_details.forEach((e, index) => {
        //     this.create.header_details[index].discount =
        //       (this.create.invoice_discount * e.total) / this.create.total_invoice;
        //   });
        // },
        addNewField() {
            this.create.header_details.push({
                contract_header_id: null,
                from_date: this.formatDate(new Date()),
                to_date: this.formatDate(new Date()),
                rent_amount: 0,
                payment_type_id: null,
                print_day: this.formatDate(new Date()),
                due_day: "",
            });
            //   this.changeValue();
        },
        removeNewField(index) {
            if (this.create.header_details.length > 1) {
                this.create.header_details.splice(index, 1);
            }
            //   this.changeValue();
        },
        /**
         *  data create (create)
         */
        dataCreate() {
            this.tenant_data_edit = "";
            this.tenant_data_create = "";
            this.serial_number = "";
            this.financial_years_validate = true;
            this.create = {
                document_id: this.document_id,
                document_module_type_id: this.document
                    ? this.document.document_module_type_id
                    : null,
                // document_status_id: null,
                // reason: "",
                branch_id: null,
                date: this.formatDate(new Date()),
                serial_id: null,
                related_document_id: null,
                related_document_number: null,
                related_document_prefix: "",
                employee_id: null,
                tenant_id: null,
                notice_period: 0, // it should be how many months before the tenant leave
                insurance_amount: 0,
                automatic_renews: "",
                external_salesmen_id: null,
                commission: 0,
                posted: "",
                receipt_print_detail: "", // it is 0 or 1
                units: [
                    {
                        unit_id: null,
                        unit_services: [],
                    }
                ],
                header_details: [
                    {
                        contract_header_id: null, //for rooms
                        from_date: this.formatDate(new Date()),
                        to_date: this.formatDate(new Date()),
                        rent_amount: 0,
                        payment_type_id: null,
                        print_day: this.formatDate(new Date()),
                        due_day: "",
                    },
                ],
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
            if (this.checkDocumentNeedApprove()) {
                this.getStatus();
            }
            this.dataCreate();
            this.getBranches();
            this.getExternalSalesmen();
            this.getTenants();
            this.getUnits();
            this.getInstallPaymentTypes();
            if (
                parseInt(this.document.required) == 1 &&
                this.relatedDocuments.length == 1
            ) {
                this.create.related_document_id = this.relatedDocuments[0].id;
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
        Submit(is_break = false) {
            if (this.dataRow) {
                this.editSubmit(this.dataRow.id);
            } else {
                this.AddSubmit(is_break);
            }
        },
        AddSubmit(is_break = false) {
            // if (
            //     parseInt(this.document.required) == 1 &&
            //     !this.create.related_document_number
            // ) {
            //     Swal.fire({
            //         icon: "error",
            //         title: `${this.$t("general.Error")}`,
            //         text: `${this.$t("general.PleaseChooseRelatedSerialNumber")}`,
            //     });
            //     return 0;
            // }
            this.$v.create.$touch();
            // if (this.$v.create.$invalid || !this.financial_years_validate) {
            //     return;
            // } else {
            //     this.isLoader = true;
            //     this.errors = {};
            //     this.is_disabled = false;
            // }
            this.create.company_id = this.company_id;
            adminApi
                .post(`real-estate/contract-headers`, {...this.create})
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
                    // here for break if user do
                    if (
                        this.document.attributes &&
                        parseInt(this.document.attributes.customer) != 0 &&
                        is_break == true
                    ) {
                        this.create.id = res.data.data.id;
                        this.showBreakCreate();
                    }
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
        },

        /**
         *  edit screen
         */
         editSubmit(id) {
        //   if (
        //     parseInt(this.document.required) == 1 &&
        //     !this.create.related_document_number
        //   ) {
        //     Swal.fire({
        //       icon: "error",
        //       title: `${this.$t("general.Error")}`,
        //       text: `${this.$t("general.PleaseChooseRelatedSerialNumber")}`,
        //     });
        //     return 0;
        //   }
           this.$v.create.$touch();

          if (this.$v.create.$invalid || !this.financial_years_validate) {
            return;
          } else {
             this.create.company_id = this.company_id;
             this.isLoader = true;
             this.errors = {};
             adminApi
               .put(`contract-headers/${id}`, {
                 ...this.create,
               })
               .then((res) => {
                 this.$emit("created");
                 setTimeout(() => {
                   Swal.fire({
                     icon: "success",
                     text: `${this.$t("general.Editsuccessfully")}`,
                     showConfirmButton: false,
                     timer: 1500,
                   });
                 }, 500);
                 // if (
                 //   this.document.attributes &&
                 //   parseInt(this.document.attributes.customer) != 0
                 // ) {
                 //   this.showBreakCreate();
                 // }
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
        getStatus() {
            this.isLoader = true;

            adminApi
                .get(`/document-statuses`)
                .then((res) => {
                    let l = res.data.data;
                    this.statuses = l;
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
                    if (!this.dataRow) {
                        if (this.branches.length == 1) {
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
        getSerialNumber(e) {
            let date = this.create.date;
            let shortYear = new Date(date).getFullYear();
            let twoDigitYear = shortYear.toString().substr(-2);
            let branch = this.branches.find((row) => e == row.id);
            let serial = branch.serials.find(
                (row) => this.document_id == row.document_id
            );
            this.serial_number = `${twoDigitYear}-${branch.id}-${this.document_id}-${serial.perfix}`;

            let document_id = this.create.related_document_id;
            let branch_id = this.create.branch_id;
            if (document_id && branch_id) {
                this.handelRelatedDocument();
            }
        },
        getExternalSalesmen() {
            this.isLoader = true;
            adminApi
                .get(`/external-salesmen?company_id=${this.company_id}`)
                .then((res) => {
                    let l = res.data.data;
                    this.externalSalesman = l;
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

        getTenants() {
            this.isLoader = true;
            adminApi
                .get(`/real-estate/tenants`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.unshift({id: 0, name: "اضف مسآجر", name_e: "Add Tenant"});
                    this.tenants = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },

        getUnits() {
            this.isLoader = true;
            adminApi
                .get(`/real-estate/units`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.unshift({id: 0, name: "اضافة وحده", name_e: "Add unit"});
                    this.units = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },

        getServices() {
            // make method that takes the unit_id that in creat.unit_service.unit_id
            let unit_ids = this.unit_ids;
            let params = unit_ids.map(unit_id => `unit_id[]=${unit_id}`).join('&');
            // use this as params in the api of the unit service table
            adminApi
                .get(`/real-estate/unit-service?${params}`)
                .then((res) => {
                    this.isLoader = false;
                    // take unique values from the data
                    let data = [];

                    unit_ids.forEach((item, index) => {

                        data.push({
                            services: [],
                            totalAmount: 0
                        });
                        let totalAmount = 0
                        res.data.data.forEach((ele) => {

                            if (ele.unit.id == item) {
                                this.create.units[index].unit_services.push(ele.service.id)
                                totalAmount += ele.price
                                data[index]['services'].push({
                                    id: ele.service.id,
                                    name: ele.service.name,
                                    name_e: ele.service.name_e,
                                    price: ele.price,

                                })
                            }
                        });
                        data[index]['totalAmount'] = totalAmount
                    })


                    // from the data I get fromm the api I make the service array to be that and make user choose form them,
                    this.services = data;
                    // make condition it be false at first and then when this method gets triggered show the modal for the user

                    this.showServiceModal = true;
                    console.log('Unit Services:', this.create.units);
                })
                .catch((err) => {
                    console.log('error', err)
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },

        // When the user selects services for a specific unit_id inside the modal
        // Making sure it saved right
        updateServicesForUnit(unit, selectedServices, index) {
            let totalAmount = 0;
            this.services[index]['services'].forEach((ele) => {
                if (this.create.units[index].unit_services.includes(ele.id))
                    totalAmount += ele.price
            });
            this.$set(this.services[index], 'totalAmount', totalAmount)
            console.log('totaAmount', totalAmount)
            const unitId = this.unit_ids.find(id => id === unit);
            if (unitId) {
                this.selectServicesForUnit(selectedServices, unitId);
            }
            console.log("unitService after", this.create.units)
        },

        selectServicesForUnit(services, unit_id) {
            const unitService = this.create.units.find(us => us.unit_id === unit_id);
            if (unitService) {
                unitService.units = services
            }
        },

        getInstallPaymentTypes() {
            this.isLoader = true;
            adminApi
                .get(`/recievable-payable/rp_installment_payment_types`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({
                        id: 0,
                        name: "اضف نوع دفع",
                        name_e: "Add installment payment type",
                    });
                    this.payment_types = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })

        },
        // why its unused,,, component called breakdown
        getPaymentType() {
            this.isLoader = true;
            adminApi
                .get(`/payment-methods`)
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

        },


        /**
         *   show Modal (edit)
         */
        async resetModalEdit(id) {
            this.showServiceModal = false;
          this.tenant_data_edit = "";
          this.tenant = "";
          this.isLoader = true;
          this.financial_years_validate = true;
          // if (this.checkDocumentNeedApprove()) {
          //   this.getStatus();
          // }
          this.getBranches();

          let reservation = this.dataOfRow;
          if (reservation.breakSettlement === 1) {
              this.showValue = false;
          } else {
              this.showValue = true;
          }
            console.log('breakSettlement', reservation.breakSettlement)
          this.tenant_data_edit = reservation.tenant;
          this.getTenants();
          this.getUnits();
          this.getExternalSalesmen();
          this.getInstallPaymentTypes();

          this.serial_number = reservation.prefix;
          this.create.branch_id = reservation.branch_id;
          this.create.date = reservation.date;
          this.create.related_document_id = reservation.related_document_id;
          this.create.document_module_type_id = this.document
            ? this.document.document_module_type_id
            : null;
          if (reservation.related_document_id) {
            this.handelRelatedDocument();
          }
          if (reservation.document_number) {
            this.relatedDocumentNumbers.push(reservation.document_number);
          }
          this.create.id = reservation.id;
          this.create.tenant_id = reservation.tenant_id;
          // this.create.company_id = reservation.company_id;
          this.create.employee_id = reservation.employee.id;
          this.create.notice_period = reservation.notice_period;
          this.create.insurance_amount = reservation.insurance_amount;
          this.create.automatic_renews = reservation.automatic_renews;
          this.create.external_salesmen_id = reservation.externalSalesmen.id;
          this.create.commission = reservation.commission;
          this.create.posted = reservation.posted;
          this.create.receipt_print_detail = reservation.receipt_print_detail;
            this.unit_ids = [];
            this.create.units = []
            reservation.units.forEach((unit) => {
                this.unit_ids.push(unit.unit_id);
                this.create.units.push({
                   unit_id : unit.id,
                    unit_services: unit.pivot.unit_services
                }) ;
            });

          this.create.related_document_number = reservation.related_document_number;
          this.create.related_document_prefix = reservation.related_document_prefix;
          this.create.header_details = [];
          reservation.contractHeaderDetail.forEach((e, index) => {
            this.create.header_details.push({
              from_date: this.formatDate(e.from_date),
              rent_amount: e.rent_amount,
              to_date: this.formatDate(e.to_date),
                payment_type_id: e.payment_type_id,
                print_day: e.print_day,
                due_day: e.due_day,
            });
          });
          this.isLoader = false;
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
          this.tenant_data_edit = "";
            this.showServiceModal = false;
            this.showValue = true
            this.unit_ids = [],
          this.errors = {};
          this.create = {
            document_id: this.document_id,
            // document_status_id: null,
            id: null,
              branch_id: null,
              date: this.formatDate(new Date()),
              serial_id: null,
              related_document_id: null,
              related_document_number: null,
              related_document_prefix: "",
              employee_id: null,
              tenant_id: null,
              notice_period: 0, // it should be how many months before the tenant leave
              insurance_amount: 0,
              automatic_renews: "",
              external_salesmen_id: null,
              commission: 0,
              posted: "",
              receipt_print_detail: "", // it is 0 or 1
              company_id: null,
              units: [],
            header_details: [],
          };
        },
        async getDataRow() {
            this.isLoader = true;
            await adminApi
                .get(`/real-estate/contract-headers/${this.idEdit}`)
                .then((res) => {
                    let l = res.data.data;
                    console.log('dataEddit', l)
                    this.dataOfRow = l;
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
         *  start  dynamicSortString
         */

        formatDate(value) {
            return formatDateOnly(value);
        },

        // searchUnits(e) {
        //     let search = e ?? "";
        //     clearTimeout(this.debounce);
        //     this.debounce = setTimeout(() => {
        //         let units_handle = this.salesmen.find(
        //             (el) => el.id == this.create.units.unit_id
        //         )
        //             ? this.salesmen.find((el) => el.id == this.create.units.unit_id)
        //                 .units_handle
        //             : "";
        //         this.getUnits(units_handle, this.create.units.unit_id, search);
        //     }, 500);
        // },
        //
        // searchTenant(e) {
        //     let search = e ?? "";
        //     clearTimeout(this.debounce);
        //     this.debounce = setTimeout(() => {
        //         let tenant_handle = this.salesmen.find(
        //             (el) => el.id == this.create.tenant_id
        //         )
        //             ? this.salesmen.find((el) => el.id == this.create.tenant_id)
        //                 .tenant_handle
        //             : "";
        //         this.getTenants(tenant_handle, this.create.tenant_id, search);
        //     }, 500);
        // },
        checkDocumentNeedApprove() {
            if (this.document) {
                if (parseInt(this.document.need_approve) == 1) {
                    return true;
                }
            }
            return false;
        },

        checkDocumentLinked() {
            if (this.document) {
                if (this.document.document_Relateds.length > 0) {
                    return true;
                }
            }
            return false;
        },

        titleModelName() {
            if (this.document) {
                if (!this.dataRow) {
                    return `${this.$t("general.addNew")} ${
                        this.$i18n.locale == "ar"
                            ? this.document.name
                            : this.document.name_e
                    } `;
                } else {
                    return `${this.$t("general.Edit")} ${
                        this.$i18n.locale == "ar"
                            ? this.document.name
                            : this.document.name_e
                    } `;
                }
            }
        },

        async changeDateDocument() {
            let date = this.create.date;
            let branch_id = this.create.branch_id;
            await this.checkFinancialYears(date, branch_id);
        },

        async checkFinancialYears(date, branch_id) {
            this.isLoader = true;
            await adminApi
                .get(`/document-headers/check-date?date=${date}`)
                .then((res) => {
                    let l = res.data;
                    if (!l) {
                        this.financial_years_validate = false;
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t(
                                "general.The date is outside the permitted fiscal year"
                            )}`,
                        });
                        this.serial_number = "";
                    } else {
                        this.financial_years_validate = true;
                        let shortYear = new Date(date).getFullYear();
                        let twoDigitYear = shortYear.toString().substr(-2);
                        let branch = this.branches.find((row) => branch_id == row.id);
                        let serial = branch.serials.find(
                            (row) => this.document_id == row.document_id
                        );
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
        async handelRelatedDocument() {
            let related_document_id = this.create.related_document_id;
            let document_id = this.document_id;
            let branch_id = this.create.branch_id;
            await this.getRelatedDocument(
                related_document_id,
                branch_id,
                document_id
            );
        },
        // here the route does not work
        getRelatedDocument(related_document_id, branch_id, document_id) {
            this.isLoader = true;
            adminApi
                .get(
                    `/contract-headers/check-related-document?related_document_id=${related_document_id}&document_id=${document_id}&branch_id=${branch_id}`
                )
                .then((res) => {
                    let l = res.data.data;
                    this.relatedDocumentNumbers = l;
                    if (this.dataOfRow) {
                        this.relatedDocumentNumbers.push(this.dataOfRow.document_number);
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

        RelatedDocumentData() {
            let related_document_number = this.create.related_document_number;
            if (related_document_number) {
                this.displayDataCreate(related_document_number);
            }
        },
        //  here I will adjust it and then comment for future
        displayDataCreate(related_document_number) {
            this.isLoader = true;
            let relatedDocument = this.relatedDocumentNumbers.find(
                (el) => el.id == related_document_number
            );
            this.create.header_details = [];
            // this.tenant_data_edit = relatedDocument.customer;
            // this.tenant_data_create = relatedDocument.customer;
            this.getTenants();
            this.getUnits();
            this.getInstallPaymentTypes()
            this.create.related_document_prefix = relatedDocument.serial_number;
            this.create.customer_type = relatedDocument.customer_type;
            this.create.attendants = relatedDocument.attendants;
            this.create.customer_id = relatedDocument.customer_id;
            this.create.company_id = relatedDocument.company_id;
            this.create.employee_id = relatedDocument.employee_id;
            if (relatedDocument.customer_type == 0) {
                this.getCompanies(
                    relatedDocument.employee.customer_handel,
                    relatedDocument.employee_id
                );
            }
            this.create.payment_method_id = relatedDocument.payment_method_id;
            this.create.sell_method_id = relatedDocument.sell_method_id;
            this.create.total_invoice = relatedDocument.total_invoice;
            this.create.invoice_discount = relatedDocument.invoice_discount;
            this.create.attendans_num = relatedDocument.attendans_num;
            this.create.net_invoice = relatedDocument.net_invoice;
            this.create.sell_method_discount = relatedDocument.sell_method_discount;
            this.create.unrelaized_revenue = relatedDocument.unrelaized_revenue;

            relatedDocument.header_details.forEach((e, index) => {
                if (e.unit_id) {
                    this.rooms.push({rooms: [e.unit]});
                } else {
                    this.rooms.push({rooms: []});
                }
                this.create.header_details.push({
                    unit_id: e.unit_id, //for rooms
                    item_id: e.item_id, //for service
                    date_from: this.formatDate(e.date_from),
                    rent_days: e.rent_days,
                    date_to: this.formatDate(e.date_to),
                    check_in_time: this.currentTime(),
                    unit_type: e.unit_type,
                    quantity: e.quantity,
                    price_per_uint: e.price_per_uint,
                    total: e.total,
                    discount: e.discount,
                    discount_per_day: e.discount / e.rent_days,
                    is_stripe: e.is_stripe,
                    sell_method_discount: e.sell_method_discount,
                    note: e.note,
                    category_booking: e.category_booking,
                });
            });
            this.isLoader = false;
        },

        // calcDateTo(index) {
        //   let days = parseInt(this.create.header_details[index].rent_days);
        //   let date_from = new Date(this.create.header_details[index].date_from);
        //   date_from.setDate(date_from.getDate() + days);
        //   this.create.header_details[index].date_to = new Date(date_from)
        //     .toISOString()
        //     .slice(0, 10);
        //   this.create.header_details[index].quantity = parseInt(
        //     this.create.header_details[index].rent_days
        //   );
        //   this.totalCreate(index);
        //   if (this.create.header_details[index].rent_days) {
        //     this.getRooms(
        //       index,
        //       this.create.header_details[index].date_from,
        //       this.create.header_details[index].date_to
        //     );
        //   }
        // },

        showBreakCreate() {
            if (this.create.id) {
                this.openingBreak = {
                    customer_id:
                        this.create.customer_type == 0
                            ? this.create.company_id
                            : this.create.customer_id,
                    currency_id: 1,
                    document_id: this.document_id,
                    debit:
                        this.document.attributes &&
                        parseInt(this.document.attributes.customer) == 1
                            ? this.create.net_invoice
                            : 0,
                    credit:
                        this.document.attributes &&
                        parseInt(this.document.attributes.customer) == -1
                            ? this.create.net_invoice
                            : 0,
                    date: this.create.date,
                    rate: 1,
                    id: this.create.id,
                    break_type: "documentHeader",
                    is_update: this.dataRow ? true : false,
                };
                this.$bvModal.show("opening-balance-break-create");
            }
        },
        addLineEnter(e) {
            const keyCode = e.keyCode;
            if (keyCode === 13) {
                this.addNewField();
            }
        },

        // currentTime() {
        //   const currentTime = new Date();
        //   const hours = currentTime.getHours();
        //   const minutes = currentTime.getMinutes();
        //
        //   const formattedTime = `${hours.toString().padStart(2, "0")}:${minutes
        //     .toString()
        //     .padStart(2, "0")}`;
        //   return formattedTime;
        // },

    },
};
</script>

<template>
    <div>


        <!--    <div v-if="dataOfRow" style="display: none">-->
        <!--      <PrintInvoice :document_data="dataOfRow" />-->
        <!--    </div>-->
        <!--    <transactionBreak-->
        <!--      :companyKeys="companyKeys"-->
        <!--      :defaultsKeys="defaultsKeys"-->
        <!--      :opening="openingBreak"-->
        <!--    />-->
        <!--  create   -->
        <b-modal
            :id="id"
            :hide-footer="true"
            :title="titleModelName()"
            body-class="p-4 "
            dialog-class="modal-full-width"
            title-class="font-18"
            @hidden="resetModalHiddenCreateOrUpdate"
            @show="resetModalCreateOrUpdate"
        >
            <form>
                <!--                <div class="row">-->
                <!--                    <div v-if="checkDocumentNeedApprove()" class="col-md-6">-->
                <!--                        <div class="row">-->
                <!--                            <div class="col-md-6">-->
                <!--                                <div class="form-group position-relative">-->
                <!--                                    <label class="control-label">-->
                <!--                                        {{ $t("general.Status") }}-->
                <!--                                    </label>-->
                <!--                                    <multiselect-->
                <!--                                        v-model="create.document_status_id"-->
                <!--                                        :custom-label="-->
                <!--                                                          (opt) =>-->
                <!--                                                            statuses.find((x) => x.id == opt)-->
                <!--                                                              ? $i18n.locale == 'ar'-->
                <!--                                                                ? statuses.find((x) => x.id == opt).name-->
                <!--                                                                : statuses.find((x) => x.id == opt).name_e-->
                <!--                                                              : ''-->
                <!--                                                        "-->
                <!--                                        :options="statuses.map((type) => type.id)"-->
                <!--                                        :show-labels="false"-->
                <!--                                    >-->
                <!--                                    </multiselect>-->
                <!--                                    <template v-if="errors.document_status_id">-->
                <!--                                        <ErrorMessage-->
                <!--                                            v-for="(errorMessage, index) in errors.document_status_id"-->
                <!--                                            :key="index"-->
                <!--                                        >{{ errorMessage }}-->
                <!--                                        </ErrorMessage>-->
                <!--                                    </template>-->
                <!--                                </div>-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                        <div class="col-md-6">-->
                <!--                            <div class="form-group position-relative">-->
                <!--                                <label class="control-label">-->
                <!--                                    {{ $t("general.reason") }}-->
                <!--                                </label>-->
                <!--                                <input-->
                <!--                                    v-model="$v.create.reason.$model"-->
                <!--                                    :class="{-->
                <!--                                                        'is-invalid': $v.create.reason.$error || errors.reason,-->
                <!--                                                        'is-valid': !$v.create.reason.$invalid && !errors.reason,-->
                <!--                                                      }"-->
                <!--                                    class="form-control englishInput"-->
                <!--                                    type="text"-->
                <!--                                />-->
                <!--                                <template v-if="errors.reason">-->
                <!--                                    <ErrorMessage-->
                <!--                                        v-for="(errorMessage, index) in errors.reason"-->
                <!--                                        :key="index"-->
                <!--                                    >{{ errorMessage }}-->
                <!--                                    </ErrorMessage>-->
                <!--                                </template>-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->
                <div
                    :class="[ checkDocumentNeedApprove() ? 'col-md-6' : 'col-md-12','mb-3 d-flex justify-content-end',]">
                    <b-button
                        v-if="!dataRow"
                        class="font-weight-bold px-2 mx-1"
                        type="button"
                        variant="primary"
                    >
                        {{ $t("general.print") }}
                        <i class="fe-printer"></i>
                    </b-button>
                    <b-button
                        v-else
                        v-print="'#printReservation'"
                        class="font-weight-bold px-2 mx-1"
                        type="button"
                        variant="primary"
                    >
                        {{ $t("general.print") }}
                        <i class="fe-printer"></i>
                    </b-button>
                    <b-button
                        v-if="!dataRow"
                        :class="['font-weight-bold px-2 mx-1', is_disabled ? 'mx-2' : '']"
                        :disabled="!is_disabled"
                        type="button"
                        variant="success"
                        @click.prevent="resetForm"
                    >
                        {{ $t("general.AddNewRecord") }}
                    </b-button>
                    <template v-if="!is_disabled">
                        <a
                            v-if="!isLoader"
                            class="btn btn-success mx-1"
                            @click.prevent="Submit(false)"
                        >
                            {{ dataRow ? $t("general.Edit") : $t("general.Add") }}
                        </a>
                        <b-button v-else class="mx-1" disabled variant="success">
                            <b-spinner small></b-spinner>
                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                        </b-button>
                    </template>
                    <b-button
                        type="button"
                        variant="danger"
                        @click.prevent="$bvModal.hide(`${id}`)"
                    >
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"
                            >{{ $t("general.Branch") }}
                                <span class="text-danger">*</span></label
                            >
                            <multiselect
                                v-model="create.branch_id"
                                :class="{
                                  'is-invalid': $v.create.branch_id.$error || errors.branch_id,
                                }"
                                :custom-label="
                                  (opt) =>
                                    branches.find((x) => x.id == opt)
                                      ? $i18n.locale == 'ar'
                                        ? branches.find((x) => x.id == opt).name
                                        : branches.find((x) => x.id == opt).name_e
                                      : ''
                                "
                                :options="branches.map((type) => type.id)"
                                :show-labels="false"
                                @input="getSerialNumber($event)"
                                :disabled="!showValue"
                            >
                            </multiselect>
                            <div
                                v-if="!$v.create.branch_id.required"
                                class="invalid-feedback"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>

                            <template v-if="errors.branch_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.branch_id"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">
                                {{ $t("general.Date") }}
                                <span class="text-danger">*</span>
                            </label>
                            <date-picker
                                v-model="create.date"
                                :class="{ 'is-invalid': !financial_years_validate }"
                                :confirm="false"
                                :disabled="!create.branch_id"
                                format="YYYY-MM-DD"
                                type="date"
                                valueType="format"
                                @input="changeDateDocument()"
                            ></date-picker>
                            <div v-if="!financial_years_validate" class="invalid-feedback">
                                {{
                                    $t("general.The date is outside the permitted fiscal year")
                                }}
                            </div>
                            <template v-if="errors.date">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.date"
                                    :key="index"
                                >
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">
                                {{ $t("general.serial_number") }}
                            </label>
                            <input
                                v-model="serial_number"
                                :disabled="true"
                                class="form-control"
                                type="text"
                            />
                        </div>
                    </div>
                    <div v-if="checkDocumentLinked()" class="col-md-3">
                        <div class="form-group">
                            <label>{{ $t("general.RelatedDocument") }}</label>
                            <multiselect
                                v-model="create.related_document_id"
                                :class="{
                                  'is-invalid':
                                    $v.create.related_document_id.$error ||
                                    errors.related_document_id,
                                }"
                                :custom-label="
                                  (opt) =>
                                    relatedDocuments.find((x) => x.id == opt)
                                      ? $i18n.locale == 'ar'
                                        ? relatedDocuments.find((x) => x.id == opt).name
                                        : relatedDocuments.find((x) => x.id == opt).name_e
                                      : ''
                                "
                                :disabled="
                                  !create.branch_id ||
                                  (relatedDocuments.length == 1 &&
                                    parseInt(document.required) == 1)
                                "
                                :options="relatedDocuments.map((type) => type.id)"
                                :show-labels="false"
                                @input="handelRelatedDocument()"
                            >
                            </multiselect>

                            <template v-if="errors.related_document_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.related_document_id"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="checkDocumentLinked()" class="col-md-3">
                        <div class="form-group">
                            <label>{{ $t("general.related_document_prefix") }}</label>
                            <multiselect
                                v-model="create.related_document_number"
                                :class="{
                                  'is-invalid':
                                    $v.create.related_document_number.$error ||
                                    errors.related_document_number,
                                }"
                                :custom-label="
                                  (opt) =>
                                    relatedDocumentNumbers.find((x) => x.id == opt)
                                      ? relatedDocumentNumbers.find((x) => x.id == opt).prefix
                                      : ''
                                "
                                :disabled="!create.branch_id"
                                :options="relatedDocumentNumbers.map((type) => type.id)"
                                :show-labels="false"
                                @input="RelatedDocumentData()"
                            >
                            </multiselect>

                            <template v-if="errors.related_document_number">
                                <ErrorMessage
                                    v-for="(
                                        errorMessage, index
                                      ) in errors.related_document_number"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="checkDocumentLinked()" class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">
                                {{ $t("general.RelatedDocumentNumber") }}
                            </label>
                            <input
                                v-model="create.related_document_prefix"
                                :disabled="true"
                                class="form-control"
                                type="text"
                            />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"
                            >{{ $t("general.tenant") }}
                                <span class="text-danger">*</span></label
                            >

                            <multiselect
                                v-model="create.tenant_id"
                                :class="{
                                  'is-invalid': $v.create.tenant_id.$error || errors.tenant_id,
                                }"
                                :custom-label="
                                  (opt) =>
                                    tenants.find((x) => x.id == opt)
                                      ? $i18n.locale == 'ar'
                                        ? tenants.find((x) => x.id == opt).name
                                        : tenants.find((x) => x.id == opt).name_e
                                      : ''
                                "
                                :disabled="!showValue"
                                :internalSearch="false"
                                :options="tenants.map((type) => type.id)"
                                :show-labels="false"

                            >
                            </multiselect>
                            <div
                                v-if="!$v.create.tenant_id.required"
                                class="invalid-feedback"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.tenant_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.button_id"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"
                            >{{ $t("general.employee") }}
                                <span class="text-danger">*</span></label
                            >

                            <multiselect
                                v-model="create.employee_id"
                                :class="{
                                  'is-invalid':
                                    $v.create.employee_id.$error || errors.employee_id,
                                }"
                                :custom-label="
                                  (opt) =>
                                    externalSalesman.find((x) => x.id == opt)
                                      ? $i18n.locale == 'ar'
                                        ? externalSalesman.find((x) => x.id == opt).name
                                        : externalSalesman.find((x) => x.id == opt).name_e
                                      : ''
                                "

                                :options="externalSalesman.map((type) => type.id)"
                                :show-labels="false"
                            >
                            </multiselect>
                            <div
                                v-if="!$v.create.employee_id.required"
                                class="invalid-feedback"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.employee_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.button_id"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ $t("general.noticePeriod") }}
                            </label>
                            <input
                                v-model="$v.create.notice_period.$model"
                                :class="{
                                  'is-invalid':
                                    $v.create.notice_period.$error || errors.notice_period,
                                  'is-valid':
                                    !$v.create.notice_period.$invalid && !errors.notice_period,
                                }"
                                :disabled="!create.branch_id"
                                class="form-control englishInput"
                                step="any"
                                type="number"
                            />
                            <template v-if="errors.notice_period">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.notice_period"
                                    :key="index"
                                >
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">
                                {{ $t("general.automatic_renews") }}
                                <span class="text-danger">*</span>
                            </label>
                            <b-form-group
                                :class="{
                                  'is-invalid':
                                    $v.create.automatic_renews.$error || errors.automatic_renews,
                                  'is-valid':
                                    !$v.create.automatic_renews.$invalid &&
                                    !errors.automatic_renews,
                                }"
                            >
                                <b-form-radio
                                    v-model="$v.create.automatic_renews.$model"
                                    class="d-inline-block"
                                    name="automatic_renews"
                                    value="1"
                                >{{ $t("general.Yes") }}
                                </b-form-radio
                                >
                                <b-form-radio
                                    v-model="$v.create.automatic_renews.$model"
                                    class="d-inline-block m-1"
                                    name="automatic_renews"
                                    value="0"
                                >{{ $t("general.No") }}
                                </b-form-radio
                                >
                            </b-form-group>
                            <template v-if="errors.automatic_renews">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.automatic_renews"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage
                                >
                            </template>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ $t("general.insurance_amount") }}
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                v-model="$v.create.insurance_amount.$model"
                                :class="{
                                  'is-invalid':
                                    $v.create.insurance_amount.$error || errors.insurance_amount,
                                  'is-valid':
                                    !$v.create.insurance_amount.$invalid && !errors.insurance_amount,
                                }"
                                :disabled="!showValue"
                                class="form-control englishInput"
                                step="any"
                                type="number"
                            />
                            <template v-if="errors.insurance_amount">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.insurance_amount"
                                    :key="index"
                                >
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>{{ $t('general.externalSalesmen') }}</label>
                            <multiselect
                                v-model="create.external_salesmen_id"
                                :class="{'is-invalid': $v.create.external_salesmen_id.$error || errors.external_salesmen_id, }"
                                :custom-label="(opt) => externalSalesman.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? externalSalesman.find((x) => x.id == opt).name : externalSalesman.find((x) => x.id == opt).name_e :''"
                                :options="externalSalesman.map((type) => type.id)"
                                :show-labels="false">
                            </multiselect>

                            <template v-if="errors.external_salesmen_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.external_salesmen_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ $t("general.commission") }}
                            </label>
                            <input
                                v-model="$v.create.commission.$model"
                                :class="{
                                  'is-invalid':
                                    $v.create.commission.$error || errors.commission,
                                  'is-valid':
                                    !$v.create.commission.$invalid && !errors.commission,
                                }"
                                :disabled="!create.branch_id"
                                class="form-control englishInput"
                                step="any"
                                type="number"
                            />
                            <template v-if="errors.commission">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.commission"
                                    :key="index"
                                >
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>

                    <div class="col-md-3" v-if="showValue">
                        <div class="form-group">
                            <label class="control-label">
                                {{ $t("general.posted") }}
                                <span class="text-danger">*</span>
                            </label>
                            <b-form-group
                                :class="{
                                  'is-invalid':
                                    $v.create.posted.$error || errors.posted,
                                  'is-valid':
                                    !$v.create.posted.$invalid &&
                                    !errors.posted,
                                }"
                            >
                                <b-form-radio
                                    v-model="$v.create.posted.$model"
                                    class="d-inline-block"
                                    name="posted"
                                    value="1"
                                >{{ $t("general.Yes") }}
                                </b-form-radio
                                >
                                <b-form-radio
                                    v-model="$v.create.posted.$model"
                                    class="d-inline-block m-1"
                                    name="posted"
                                    value="0"
                                >{{ $t("general.No") }}
                                </b-form-radio
                                >
                            </b-form-group>
                            <template v-if="errors.posted">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.posted"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage
                                >
                            </template>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">
                                {{ $t("general.receipt_print_detail") }}
                                <span class="text-danger">*</span>
                            </label>
                            <b-form-group
                                :class="{
                                  'is-invalid':
                                    $v.create.receipt_print_detail.$error || errors.receipt_print_detail,
                                  'is-valid':
                                    !$v.create.receipt_print_detail.$invalid &&
                                    !errors.receipt_print_detail,
                                }"
                            >
                                <b-form-radio
                                    v-model="$v.create.receipt_print_detail.$model"
                                    class="d-inline-block"
                                    name="receipt_print_detail"
                                    value="1"
                                >{{ $t("general.Yes") }}
                                </b-form-radio
                                >
                                <b-form-radio
                                    v-model="$v.create.receipt_print_detail.$model"
                                    class="d-inline-block m-1"
                                    name="receipt_print_detail"
                                    value="0"
                                >{{ $t("general.No") }}
                                </b-form-radio
                                >
                            </b-form-group>
                            <template v-if="errors.receipt_print_detail">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.receipt_print_detail"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage
                                >
                            </template>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"
                            >{{ $t("general.units") }}
                                <span class="text-danger">*</span></label
                            >

                            <multiselect
                                v-model="unit_ids"
                                :class="{
                                  'is-invalid': $v.create.units.$error || errors.units,
                                }"
                                :custom-label="
                                  (opt) =>
                                    units.find((x) => x.id == opt)
                                      ? $i18n.locale == 'ar'
                                        ? units.find((x) => x.id == opt).name
                                        : units.find((x) => x.id == opt).name_e
                                      : ''
                                "
                                :internalSearch="false"
                                :multiple="true"
                                :disabled="!showValue"
                                :options="units.map((type) => type.id)"
                                :show-labels="false"
                                @input="test"

                            >
                            </multiselect>
                            <div
                                v-if="!$v.create.units.required"
                                class="invalid-feedback"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.units">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.button_id"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>

                    <div class="col-md-3" v-if="showValue">
                        <div class="form-group">
                            <label class="control-label"
                            >{{ $t("general.chooseServiceForUnits") }}
                                <span class="text-danger">*</span></label>
                            <b-button variant="primary" @click="getServices">Add Services</b-button>
                        </div>
                    </div>

                    <b-modal v-model="showServiceModal" title="Select Service">
                        <div v-for="(unit, index) in unit_ids" :key="index" class="row mb-3">
                            <div class="col-md-3">
                                <label class="control-label">Unit Name</label>
                                <div class="pt-2">
                                    {{ units.find(u => u.id === unit)?.name_e || '' }}
                                </div>
                            </div>
                            :
                            <div v-if="Object.keys(services[index] ?? []).length" class="col-md-5">
                                <div class="form-group">
                                    <label class="control-label">{{ $t("general.services") }} <span class="text-danger">*</span></label>
                                    <multiselect
                                        v-model="create.units[index].unit_services"
                                        :class="{
                                                'is-invalid': $v.create.units?.[index]?.unit_services?.$error || errors.units,
                                                 }"
                                        :custom-label="
                                            (opt) => services[index]['services'].find((x) => x.id === opt) ?
                                              ($i18n.locale === 'ar' ? services[index]['services'].find((x) => x.id === opt).name : services[index]['services'].find((x) => x.id === opt).name_e) : ''
                                          "
                                        :internalSearch="false"
                                        :multiple="true"
                                        :options="services[index]['services'].map((type) => type.id)"
                                        :show-labels="false"
                                        @input="updateServicesForUnit(unit, $event, index)"
                                    ></multiselect>
                                </div>
                            </div>
                            <div v-if="Object.keys(services[index] ?? []).length" class="col-md-3">
                                <label class="control-label">Price</label>
                                <div class="pt-2">
                                    {{ services[index]['totalAmount'] }}
                                </div>
                            </div>

                        </div>
                    </b-modal>
                    <div class="col-md-12 p-0 m-0">
                        <div class="page-content">
                            <div class="px-0">
                                <div class="row">
                                    <div class="col-12 col-lg-12">
                                        <!-- .row -->
                                        <hr class="row"/>
                                        <div>
                                            <div
                                                class="row text-600 text-white bgc-default-tp1 py-25"
                                            >

                                                <div class="col">
                                                    {{ $t("general.from_date") }}
                                                </div>
                                                <div class="col">
                                                    {{ $t("general.to_date") }}
                                                </div>
                                                <div class="col">
                                                    {{ $t("general.rent_amount") }}
                                                </div>
                                                <div class="col">
                                                    {{ $t("general.payment_type") }}
                                                </div>
                                                <div class="col">
                                                    {{ $t("general.print_day") }}
                                                </div>
                                                <div class="col">
                                                    {{ $t("general.due_day") }}
                                                </div>
                                                <!--<div class="col-1">{{ $t("general.Total") }}</div>-->
                                                <div class="col">{{ $t("general.Action") }}</div>
                                            </div>
                                        </div>
                                        <div
                                            v-for="(item, gIndex) in create.header_details"
                                            class="text-95 text-secondary-d3"
                                        >
                                            <div
                                                :class="[gIndex % 2 == 0 ? 'bgc-default-l4' : '']"
                                                class="row mb-2 mb-sm-0 py-25"
                                            >
                                                <div class="col p-0">
                                                    <div class="form-group">
                                                        <date-picker
                                                            v-model="
                                                                $v.create.header_details.$each[gIndex].from_date
                                                                  .$model
                                                              "
                                                            :class="{
                                                                'is-invalid':
                                                                  $v.create.header_details.$each[gIndex]
                                                                    .from_date.$error || errors.from_date,
                                                              }"
                                                            :confirm="false"
                                                            :disabled="true"
                                                            format="YYYY-MM-DD"
                                                            type="date"
                                                            valueType="format"
                                                        >
                                                        </date-picker>
                                                    </div>
                                                </div>

                                                <div class="col p-0">
                                                    <div class="form-group">
                                                        <date-picker
                                                            v-model="
                                                                $v.create.header_details.$each[gIndex].to_date
                                                                  .$model
                                                              "
                                                            :class="{
                                                                'is-invalid':
                                                                  $v.create.header_details.$each[gIndex]
                                                                    .to_date.$error || errors.to_date,
                                                              }"
                                                            :confirm="false"
                                                            :disabled="!showValue"
                                                            format="YYYY-MM-DD"
                                                            type="date"
                                                            valueType="format"
                                                        >
                                                        </date-picker>
                                                    </div>
                                                </div>

                                                <div class="col p-0">
                                                    <div class="form-group">
                                                        <input
                                                            v-model="
                                                                $v.create.header_details.$each[gIndex]
                                                                  .rent_amount.$model
                                                              "
                                                            :class="{
                                                                'is-invalid':
                                                                  $v.create.header_details.$each[gIndex]
                                                                    .rent_amount.$error || errors.rent_amount,
                                                              }"
                                                            class="form-control englishInput"
                                                            required
                                                            type="number"
                                                            :disabled="!showValue"
                                                        />
                                                    </div>
                                                </div>

                                                <div class="col p-0">
                                                    <div class="form-group">
                                                        <multiselect
                                                            v-model="$v.create.header_details.$each[gIndex]
                                                                  .payment_type_id.$model"
                                                            :class="{
                                                                'is-invalid':
                                                                  $v.create.header_details.$each[gIndex]
                                                                    .payment_type_id.$error || errors.payment_type_id,
                                                              }"
                                                            :custom-label="
                                                                (opt) => payment_types.find((x) => x.id === opt) ?
                                                                  ($i18n.locale === 'ar' ? payment_types.find((x) => x.id === opt).name : payment_types.find((x) => x.id === opt).name_e) : ''
                                                              "
                                                            :options="payment_types.map((type) => type.id)"
                                                            :show-labels="false"
                                                            :disabled="!showValue"
                                                        ></multiselect>
                                                    </div>
                                                </div>


                                                <div class="col p-0">
                                                    <div class="form-group">
                                                        <input
                                                            v-model="
                                                                $v.create.header_details.$each[gIndex]
                                                                  .print_day.$model
                                                              "
                                                            :class="{
                                                                'is-invalid':
                                                                  $v.create.header_details.$each[gIndex]
                                                                    .print_day.$error || errors.print_day,
                                                              }"
                                                            class="form-control englishInput"
                                                            required
                                                            type="number"
                                                            :disabled="!showValue"
                                                        />
                                                    </div>
                                                </div>

                                                <div class="col p-0">
                                                    <div class="form-group">
                                                        <input
                                                            v-model="
                                                                $v.create.header_details.$each[gIndex]
                                                                  .due_day.$model
                                                              "
                                                            :class="{
                                                                'is-invalid':
                                                                  $v.create.header_details.$each[gIndex]
                                                                    .due_day.$error || errors.due_day,
                                                              }"
                                                            class="form-control englishInput"
                                                            required
                                                            type="number"
                                                            :disabled="!showValue"
                                                        />
                                                    </div>
                                                </div>


                                                <!--                                                <div class="col-1 p-0">-->
                                                <!--                                                    <input-->
                                                <!--                                                        v-model.number="-->
                                                <!--                                $v.create.header_details.$each[gIndex].total-->
                                                <!--                                  .$model-->
                                                <!--                              "-->
                                                <!--                                                        :class="{-->
                                                <!--                                'is-invalid':-->
                                                <!--                                  $v.create.header_details.$each[gIndex].total-->
                                                <!--                                    .$error || errors.total,-->
                                                <!--                                'is-valid':-->
                                                <!--                                  !$v.create.header_details.$each[gIndex].total-->
                                                <!--                                    .$invalid && !errors.total,-->
                                                <!--                              }"-->
                                                <!--                                                        class="form-control"-->
                                                <!--                                                        step="any"-->
                                                <!--                                                        type="number"-->
                                                <!--                                                        @keyup="addLineEnter($event)"-->
                                                <!--                                                    />-->
                                                <!--                                                </div>-->


                                                <div class="col">
                                                    <button
                                                        v-if="create.header_details.length > 1"
                                                        class="custom-btn-dowonload p-0"
                                                        type="button"
                                                        @click.prevent="removeNewField(gIndex)"
                                                    >
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row border-b-2 brc-default-l2"></div>
                                        <!--                                        <div class="row mt-3">-->
                                        <!--                                            <div-->
                                        <!--                                                class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0"-->
                                        <!--                                            >-->
                                        <!--                                                {{ $t("general.Extra_note") }}-->
                                        <!--                                            </div>-->

                                        <!--                                            <div-->
                                        <!--                                                class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last"-->
                                        <!--                                            >-->
                                        <!--                                                <div class="row align-items-center bgc-primary-l3">-->
                                        <!--                                                    <div class="col-7 text-right">-->
                                        <!--                                                        {{ $t("general.TotalInvoice") }}-->
                                        <!--                                                    </div>-->
                                        <!--                                                    <div class="col-5">-->
                                        <!--                                                      <span class="text-150 text-success-d3 opacity-2">-->
                                        <!--                                                        {{-->
                                        <!--                                                              !create.total_invoice-->
                                        <!--                                                                  ? "0.00"-->
                                        <!--                                                                  : parseFloat(create.total_invoice).toFixed(-->
                                        <!--                                                                      3-->
                                        <!--                                                                  )-->
                                        <!--                                                          }}-->
                                        <!--                                                      </span>-->
                                        <!--                                                    </div>-->
                                        <!--                                                </div>-->

                                        <!--                                            </div>-->
                                        <!--                                        </div>-->
                                        <hr/>
                                        <div>
                                        <span class="text-secondary-d1 text-105">{{
                                                $t("general.Thank_you")
                                            }}</span>
                                            <a
                                                class="btn btn-info btn-bold px-4 float-right mt-3 mx-2 mt-lg-0"
                                                @click.prevent="addNewField"
                                                v-if="showValue"
                                            >
                                                {{ $t("general.AddNewLine") }}
                                            </a>
<!--                                            <div-->
<!--                                                v-if="-->
<!--                                                document &&-->
<!--                                                document.attributes &&-->
<!--                                                parseInt(document.attributes.customer) != 0-->
<!--                                              "-->
<!--                                                class="px-4 float-right mt-3 mt-lg-0"-->
<!--                                            >-->
<!--                                                <b-button-->
<!--                                                    v-if="!create.id && create.net_invoice > 0"-->
<!--                                                    class="btn btn-primary btn-bold px-4 float-right mt-3 mx-2 mt-lg-0"-->
<!--                                                    variant="primary"-->
<!--                                                    @click="Submit(true)"-->
<!--                                                >-->
<!--                                                    {{ $t("general.Break") }}-->
<!--                                                </b-button>-->

<!--                                                <b-button-->
<!--                                                    v-else-->
<!--                                                    class="btn btn-secondary btn-bold px-4 float-right mt-3 mx-2 mt-lg-0"-->
<!--                                                    variant="secondary"-->
<!--                                                >-->
<!--                                                    {{ $t("general.Break") }}-->
<!--                                                </b-button>-->
<!--                                            </div>-->
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
.custom-panel-quotation {
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
    padding-top: 0.5rem;
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
    margin-left: -0.25rem !important;
}

.mr-n1,
.mx-n1 {
    margin-right: -0.25rem !important;
}

.mb-4,
.my-4 {
    margin-bottom: 1.5rem !important;
}

hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 0;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
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
    padding-bottom: 0.75rem !important;
}

.pt-25,
.py-25 {
    padding-top: 0.75rem !important;
}

.bgc-default-tp1 {
    background-color: rgba(121, 169, 197, 0.92) !important;
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
    background-color: #f6a9a9 !important;
}

</style>
