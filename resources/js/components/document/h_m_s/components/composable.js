import { BIconExclamationSquareFill } from "bootstrap-vue";
import adminApi from "../../../../api/adminAxios";
import Swal from "sweetalert2";

export default {
    methods: {
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

        getCustomers(search = "") {
            adminApi
                .get(
                    `h_m_s/patients?per_page=10&search=${search}&columns[0]=name&columns[1]=name_e&columns[2]=phone&columns[2]=whatsapp&selected_patient_id=${this.create.patient_id??0}`
                )
                .then((res) => {
                    let l = res.data.data;
                    this.patients = l;
                    if (this.customer_data_edit) {
                        if (
                            !this.patients.find(
                                (e) => e.id == this.customer_data_edit.id
                            )
                        ) {
                            this.patients.push(this.customer_data_edit);
                        }
                    }
                    if (this.customer_data_create) {
                        if (
                            !this.patients.find(
                                (e) => e.id == this.customer_data_create.id
                            )
                        ) {
                            this.patients.push(this.customer_data_create);
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
        getRelatedDocument(related_document_id, branch_id, document_id) {
            this.isLoader = true;
            adminApi
                .get(
                    `/document-headers/check-related-document?related_document_id=${related_document_id}&document_id=${document_id}&branch_id=${branch_id}`
                )
                .then((res) => {
                    let l = res.data.data;
                    this.relatedDocumentNumbers = l;
                    if (this.dataOfRow) {
                        this.relatedDocumentNumbers.push(
                            this.dataOfRow.document_number
                        );
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
                        let branch = this.branches.find(
                            (row) => branch_id == row.id
                        );
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
        async getDataRow() {
            this.isLoader = true;
            await adminApi
                .get(`/document-headers/${this.dataRow.id}`)
                .then((res) => {
                    let l = res.data.data;
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
        editSubmit(id) {
            // if (
            //     parseInt(this.document.required) == 1 &&
            //     !this.create.related_document_number
            // ) {
            //     Swal.fire({
            //         icon: "error",
            //         title: `${this.$t("general.Error")}`,
            //         text: `${this.$t(
            //             "general.PleaseChooseRelatedSerialNumber"
            //         )}`,
            //     });
            //     return 0;
            // }
            this.$v.create.$touch();

            if (this.$v.create.$invalid || !this.financial_years_validate) {
                return;
            } else {
                this.create.company_id = this.company_id;
                this.create_break_downs()

                this.isLoader = true;
                this.errors = {};
                adminApi
                    .put(`document-headers/${id}`, {
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

                    })
                    .catch((err) => {
                        if(err.response.status == 402){
                            console.log(err.response.status)
                            this.resetModalEdit(this.dataRow.id);
                            Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.You cant edit, because there is a settlement for this invoice")}`,
                        });
                        }else{
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: `${this.$t("general.Error")}`,
                                text: `${this.$t(
                                    "general.Thereisanerrorinthesystem"
                                )}`,
                            });
                        }
                        }
                    })
                    .finally(() => {
                        this.isLoader = false;
                    });
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
            //         text: `${this.$t(
            //             "general.PleaseChooseRelatedSerialNumber"
            //         )}`,
            //     });
            //     return 0;
            // }
            this.$v.create.$touch();
            if (this.$v.create.$invalid || !this.financial_years_validate) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                // if (
                //     this.document.attributes &&
                //     parseInt(this.document.attributes.customer) != 0
                // ) {
                //     if (!is_break) {
                //         this.create.is_break = 1;
                //     } else {
                //         this.create.is_break = 0;
                //     }
                // } else {
                //     this.create.is_break = 0;
                // }
                this.create.is_break = 0;

                this.create.company_id = this.company_id;

                this.create_break_downs()

                adminApi
                    .post(`document-headers`, { ...this.create})
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
                                text: `${this.$t(
                                    "general.Thereisanerrorinthesystem"
                                )}`,
                            });
                        }
                    })
                    .finally(() => {
                        this.isLoader = false;
                    });
            }
        },
        getBranch() {
            adminApi
                .get(`/branches?company_id=${this.company_id}&notParent=1&document_id=45`)
                .then((res) => {
                    this.branches = res.data.data
                })
                .catch((err) => {
                    console.log(err)
                })
        },
        Submit(is_break = false)
        {
            if (this.check_data_row > 0)
            {
                this.editSubmit(this.dataRow.id);
            }else{
                this.AddSubmit(is_break);
            }
        },

        create_break_downs(){
            let invoice = []
            if(this.client_type == 'patient'){
                if(this.create.total_patient_amount > 0)
                    invoice.push({
                        net_invoice:this.create.total_patient_amount,
                        customer_id:this.create.patient_id,
                        client_type_id:4,
                    })

                if(this.create.total_company_insurance_amount > 0)
                    invoice.push({
                        net_invoice:this.create.total_company_insurance_amount,
                        customer_id:this.create.company_insurance_id,
                        client_type_id:6,
                    })
            }else{
                this.create.company_insurance_id = ''
                if(this.create.net_invoice > 0){
                    this.create.total_company_insurance_amount = 0
                    this.create.total_patient_amount = 0
                    invoice.push({
                        net_invoice:this.create.net_invoice,
                        customer_id:this.create.doctor_id,
                        client_type_id:5,
                    })
                }
            }

            if(Object.keys(invoice ?? []).length)
                this.create.header_break_downs = invoice
        }


    },
};
