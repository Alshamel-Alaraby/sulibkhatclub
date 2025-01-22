<script>
import DatePicker from "vue2-datepicker";
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {
    required,
    minLength,
    maxLength,
    integer,
    requiredIf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import alphaArabic from "../../../helper/alphaArabic";
import alphaEnglish from "../../../helper/alphaEnglish";
import {
    dynamicSortString,
    dynamicSortNumber,
} from "../../../helper/tableSort";
import translation from "../../../helper/mixin/translation-mixin";
import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import permissionGuard from "../../../helper/permission";
import Subscription from "../../../components/create/club/subscription";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Members",
        meta: [{ name: "description", content: "Members" }],
    },
    mixins: [translation],
    components: {
        DatePicker,
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
        Subscription,
    },
    data() {
        return {
            printSponsor: [],
            selectedNameMember: [],
            membersNames: [],
            sponsorsNames: [],
            selectedNameSponser: [],
            inputPerPage: null,
            debounceTimer: null,
            serachInput: "",
            fields: [],
            per_page: 35,
            search: "",
            member_request_id: "",
            checkMembers: "",
            debounce: {},
            enabled3: true,
            membersPagination: {},
            members: [],
            sponsors: [],
            isLoader: false,
            Tooltip: "",
            mouseEnter: "",
            applying_date: new Date(),
            birth_date: new Date(),
            transaction: "",
            create: {
                applying_date: this.formatDate(new Date()),
                // applying_number: "",
                first_name: "",
                second_name: "",
                third_name: "",
                phone_code: "",
                last_name: "",
                family_name: "",
                birth_date: "",
                national_id: "",
                nationality_number: "",
                home_phone: "",
                work_phone: "",
                home_address: "",
                work_address: "",
                job: "",
                degree: "",
                acceptance: "0",
                gender: 1,
                member_type_id: 1,
                sponsor_id: null,
                application_number: "",
            },
            edit: {
                applying_date: this.formatDate(new Date()),
                // applying_number: "",
                first_name: "",
                second_name: "",
                phone_code: "",
                third_name: "",
                last_name: "",
                family_name: "",
                birth_date: "",
                national_id: "",
                nationality_number: "",
                home_phone: "",
                work_phone: "",
                home_address: "",
                work_address: "",
                job: "",
                degree: "",
                acceptance: "0",
                gender: 1,
                member_type_id: 1,
                sponsor_id: null,
                application_number: "",
            },
            company_id: null,
            errors: {},
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            showSponsor: 0,
            setting: {
                gender: true,
                member_type_id: true,
                Subscription_receipt_number: true,
                full_name: true,
                status_id: false,
                birth_date: true,
                national_id: true,
                nationality_number: true,
                home_phone: false,
                work_phone: false,
                home_address: true,
                work_address: false,
                job: false,
                degree: false,
                sponsor_id: false,
                application_number: true,
            },
            is_disabled: false,
            filterSetting: [
                "full_name",
                "document_no",
                "birth_date",
                "national_id",
                "home_phone",
                "work_phone",
                "home_address",
                "work_address",
                "job",
                "degree",
                "sponsor_id",
                "application_number",
            ],
            codeCountry: "",
            printLoading: true,
            printObj: {
                id: "printData",
            },
        };
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(
                vm,
                "Members Request",
                "all Members Request"
            );
        });
    },
    validations: {
        create: {
            first_name: {
                required: requiredIf(function (model) {
                    return this.isRequired("first_name");
                }),
            },
            second_name: {
                required: requiredIf(function (model) {
                    return this.isRequired("second_name");
                }),
            },
            gender: {
                required: requiredIf(function (model) {
                    return this.isRequired("gender");
                }),
            },
            member_type_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("member_type_id");
                }),
            },
            sponsor_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("sponsor_id");
                }),
            },
            application_number: {
                required: requiredIf(function (model) {
                    return this.isRequired("application_number");
                }),
            },
            third_name: {
                required: requiredIf(function (model) {
                    return this.isRequired("third_name");
                }),
            },
            last_name: {
                required: requiredIf(function (model) {
                    return this.isRequired("last_name");
                }),
            },
            family_name: {
                required: requiredIf(function (model) {
                    return this.isRequired("family_name");
                }),
            },
            birth_date: {
                required: requiredIf(function (model) {
                    return this.isRequired("birth_date");
                }),
            },
            national_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("national_id");
                }),
            },
            nationality_number: {
                required: requiredIf(function (model) {
                    return this.isRequired("nationality_number");
                }),
            },
            home_phone: {
                required: requiredIf(function (model) {
                    return this.isRequired("home_phone");
                }),
            },
            work_phone: {
                required: requiredIf(function (model) {
                    return this.isRequired("work_phone");
                }),
            },
            home_address: {
                required: requiredIf(function (model) {
                    return this.isRequired("home_address");
                }),
            },
            work_address: {
                required: requiredIf(function (model) {
                    return this.isRequired("work_address");
                }),
            },
            job: {
                required: requiredIf(function (model) {
                    return this.isRequired("job");
                }),
            },
            degree: {
                required: requiredIf(function (model) {
                    return this.isRequired("degree");
                }),
            },
            applying_date: {
                required: requiredIf(function (model) {
                    return this.isRequired("applying_date");
                }),
            },
            // applying_number: {
            //   required: requiredIf(function (model) {
            //     return this.isRequired("applying_number");
            //   }),
            // },
        },
        edit: {
            first_name: {
                required: requiredIf(function (model) {
                    return this.isRequired("first_name");
                }),
            },
            second_name: {
                required: requiredIf(function (model) {
                    return this.isRequired("second_name");
                }),
            },
            gender: {
                required: requiredIf(function (model) {
                    return this.isRequired("gender");
                }),
            },
            member_type_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("member_type_id");
                }),
            },
            sponsor_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("sponsor_id");
                }),
            },
            application_number: {
                required: requiredIf(function (model) {
                    return this.isRequired("application_number");
                }),
            },
            third_name: {
                required: requiredIf(function (model) {
                    return this.isRequired("third_name");
                }),
            },
            last_name: {
                required: requiredIf(function (model) {
                    return this.isRequired("last_name");
                }),
            },
            family_name: {
                required: requiredIf(function (model) {
                    return this.isRequired("family_name");
                }),
            },
            birth_date: {
                required: requiredIf(function (model) {
                    return this.isRequired("birth_date");
                }),
            },
            national_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("national_id");
                }),
            },
            nationality_number: {
                required: requiredIf(function (model) {
                    return this.isRequired("nationality_number");
                }),
            },
            home_phone: {
                required: requiredIf(function (model) {
                    return this.isRequired("home_phone");
                }),
            },
            work_phone: {
                required: requiredIf(function (model) {
                    return this.isRequired("work_phone");
                }),
            },
            home_address: {
                required: requiredIf(function (model) {
                    return this.isRequired("home_address");
                }),
            },
            work_address: {
                required: requiredIf(function (model) {
                    return this.isRequired("work_address");
                }),
            },
            job: {
                required: requiredIf(function (model) {
                    return this.isRequired("job");
                }),
            },
            degree: {
                required: requiredIf(function (model) {
                    return this.isRequired("degree");
                }),
            },
            applying_date: {
                required: requiredIf(function (model) {
                    return this.isRequired("applying_date");
                }),
            },
            // applying_number: {
            //   required: requiredIf(function (model) {
            //     return this.isRequired("applying_number");
            //   }),
            // },
        },
    },
    watch: {
        // search(after, befour) {
        //     clearTimeout(this.debounce);
        //     this.debounce = setTimeout(() => {
        //         this.getData();
        //     }, 400);
        // },
        /**
         * watch check All table
         */
        isCheckAll(after, befour) {
            if (after) {
                this.members.forEach((el) => {
                    if (!this.checkAll.includes(el.id)) {
                        this.checkAll.push(el.id);
                    }
                });
            } else {
                this.checkAll = [];
            }
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.getCustomTableFields();
        this.getData();
        this.$store.dispatch("locationIp/getIp");
        this.getMemberNames();
         this.getSponsersNames();
    },
    // directives: {
    //     print: {
    //         inserted(el, binding, vnode) {
    //             el.addEventListener("click", () => {
    //                 vnode.context.printElement(binding.value);
    //             });
    //         },
    //     },
    // },
    methods: {
        applyFilters() {
            if (
                this.selectedNameMember &&
                this.selectedNameSponser.length === 0
            ) {
                this.updateMembers();
            } else if (
                this.selectedNameSponser &&
                this.selectedNameMember.length === 0
            ) {
                this.updateSponser();
            } else {
                this.updateMembers();
            }
        },
        updateMembers() {
         this.getData();
        },
       updateSponser() {
            this.getData();

            const selectedSponsors = this.sponsorsNames.filter((sponsor) =>
                this.selectedNameSponser.includes(sponsor.id)
            );

            this.printSponsor = selectedSponsors.map((sponsor) => sponsor.name);

        },

        getOriginalMembers() {
            this.getData();
        },
        getMemberLabel(opt) {
            const member = this.membersNames.find((x) => x.id === opt);
            if (!member) return null;
            return this.$i18n.locale === "ar"
                ? member.full_name
                : member.full_name;
        },
        getMemberNames() {
            this.isLoader = true;
            adminApi
                .get(`/club-members/member-requests/member-names`)
                .then((res) => {
                    const response = res.data;
                    this.membersNames = response.data;
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
        getSponsersNames() {
            this.isLoader = true;
            adminApi
                .get(`/club-members/sponsers`)
                .then((res) => {
                    const response = res.data;
                    this.sponsorsNames = response.data;
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
        getSponsorsLabel(opt) {
            const member = this.sponsorsNames.find((x) => x.id === opt);
            if (!member) return null;
            return this.$i18n.locale === "ar" ? member.name : member.name_e;
        },
        handelSerach() {
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.search = this.serachInput;
                this.getData();
            }, 1000);
        },
        handelPerPageInput() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.per_page = this.inputPerPage;
                this.getData();
            }, 1000);
        },
        printElement(selector) {
            const elementTest = document.querySelector(selector);

            if (elementTest) {
                const rowsPerPage = 19;
                const members = this.members;
                const totalPages = Math.ceil(members.length / rowsPerPage);


                const headerElement =
                    document.querySelector(".data-header-print");
                headerElement.style.display = "flex";
                const header = headerElement.outerHTML;
                headerElement.style.display = "none";


                const originalTbody = document
                    .querySelector(selector)
                    .querySelector("tbody");
                const allRows = Array.from(
                    originalTbody.querySelectorAll("tr")
                );

                let printableContent = "";

                for (let page = 0; page < totalPages; page++) {
                    const startIndex = page * rowsPerPage;
                    const pageRows = allRows.slice(
                        startIndex,
                        startIndex + rowsPerPage
                    );


                    const tbodyClone = document.createElement("tbody");
                    pageRows.forEach((row) =>
                        tbodyClone.appendChild(row.cloneNode(true))
                    );
                                        const tableClone = document
                        .querySelector(selector)
                        .querySelector("table")
                        .cloneNode(true);

                                        tableClone.querySelector("tbody").replaceWith(tbodyClone);


                    printableContent += `
                <div style="page-break-after: always;">
                    ${header}
                    ${tableClone.outerHTML}
                </div>
            `;
                }

                const container = document.createElement("div");
                container.innerHTML = printableContent;
                document.body.appendChild(container);

                $(container).printThis({
                    header: null,
                    pageTitle: "Members Apply",
                    importCSS: true,
                    afterPrint: () => {
                        console.log("Print completed");
                        container.remove();
                    },
                });
            } else {
                console.log("Element to print not found", selector);
            }
        },
        getCustomTableFields() {
            adminApi
                .get(`/customTable/table-columns/cm_member_requests`)
                .then((res) => {
                    this.fields = res.data;
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
        isVisible(fieldName) {
            let res = this.fields.filter((field) => {
                return field.column_name == fieldName;
            });
            return res.length > 0 && res[0].is_visible == 1 ? true : false;
        },
        isRequired(fieldName) {
            let res = this.fields.filter((field) => {
                return field.column_name == fieldName;
            });
            return res.length > 0 && res[0].is_required == 1 ? true : false;
        },
        isPermission(item) {
            if (this.$store.state.auth.type == "user") {
                return this.$store.state.auth.permissions.includes(item);
            }
            return true;
        },
        v_dateCreate(e, name) {
            if (e) {
                this.create[name] = formatDateOnly(e);
            } else {
                this.create[name] = null;
            }
        },
        v_dateEdit(e, name) {
            if (e) {
                this.edit[name] = formatDateOnly(e);
            } else {
                this.edit[name] = null;
            }
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if (this.mouseEnter != id) {
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`/club-members/member-requests/logs/${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.forEach((e) => {
                            this.Tooltip += `Created By: ${
                                e.causer_type
                            }; Event: ${e.event}; Description: ${
                                e.description
                            } ;Created At: ${this.formatDate(e.created_at)} \n`;
                        });
                    })
                    .catch((err) => {
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t(
                                "general.Thereisanerrorinthesystem"
                            )}`,
                        });
                    });
            } else {
            }
        },
        /**
         *  start get Data countrie && pagination
         */
        getData(page = 1) {

            this.isLoader = true;

            let _filterSetting = [...this.filterSetting];
            let index = this.filterSetting.indexOf("document_no");
            if (index > -1) {
                _filterSetting[index] = "cmTransaction.document_no";
            }
            index = this.filterSetting.indexOf("sponsor_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar"
                        ? "sponsor.name"
                        : "sponsor.name_e";
            }
            let filter = "";
            for (let i = 0; i < _filterSetting.length; ++i) {
                filter += `columns[${i}]=${_filterSetting[i]}&`;
            }
            adminApi
                .get(
                    `/club-members/member-requests?member_types=1&page=${page}&per_page=${this.per_page}&company_id=${this.company_id}&search=${this.search}&${filter}&sponsor_id=${this.selectedNameSponser}&member_id=${this.selectedNameMember}`
                )
                .then((res) => {
                    let l = res.data;
                    this.members = l.data;
                    this.membersPagination = l.pagination;
                    this.current_page = l.pagination.current_page;
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
        getDataCurrentPage(page = 1) {
            if (
                this.current_page <= this.membersPagination.last_page &&
                this.current_page != this.membersPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let _filterSetting = [...this.filterSetting];
                let index = this.filterSetting.indexOf("document_no");
                if (index > -1) {
                    _filterSetting[index] = "cmTransaction.document_no";
                }
                index = this.filterSetting.indexOf("sponsor_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar"
                            ? "sponsor.name"
                            : "sponsor.name_e";
                }
                let filter = "";
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }
                adminApi
                    .get(
                        `/club-members/member-requests?member_types=1&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}&company_id=${this.company_id}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.members = l.data;
                        this.membersPagination = l.pagination;
                        this.current_page = l.pagination.current_page;
                    })
                    .catch((err) => {
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t(
                                "general.Thereisanerrorinthesystem"
                            )}`,
                        });
                    })
                    .finally(() => {
                        this.isLoader = false;
                    });
            }
        },
        /**
         *  end get Data countrie && pagination
         */
        /**
         *  start delete countrie
         */
        deleteBranch(id, index) {
            if (Array.isArray(id)) {
                Swal.fire({
                    title: `${this.$t("general.Areyousure")}`,
                    text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
                    cancelButtonText: `${this.$t("general.Nocancel")}`,
                    confirmButtonClass: "btn btn-success mt-2",
                    cancelButtonClass: "btn btn-danger ml-2 mt-2",
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.value) {
                        this.isLoader = true;
                        adminApi
                            .post(`/club-members/member-requests/bulk-delete`, {
                                ids: id,
                            })
                            .then((res) => {
                                this.checkAll = [];
                                this.getData();
                                Swal.fire({
                                    icon: "success",
                                    title: `${this.$t("general.Deleted")}`,
                                    text: `${this.$t(
                                        "general.Yourrowhasbeendeleted"
                                    )}`,
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                            })
                            .catch((err) => {
                                if (err.response.status == 400) {
                                    Swal.fire({
                                        icon: "error",
                                        title: `${this.$t("general.Error")}`,
                                        text: `${this.$t(
                                            "general.CantDeleteRelation"
                                        )}`,
                                    });
                                    this.getData();
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
                });
            } else {
                Swal.fire({
                    title: `${this.$t("general.Areyousure")}`,
                    text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
                    cancelButtonText: `${this.$t("general.Nocancel")}`,
                    confirmButtonClass: "btn btn-success mt-2",
                    cancelButtonClass: "btn btn-danger ml-2 mt-2",
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.value) {
                        this.isLoader = true;

                        adminApi
                            .delete(`/club-members/member-requests/${id}`)
                            .then((res) => {
                                this.checkAll = [];
                                this.getData();
                                Swal.fire({
                                    icon: "success",
                                    title: `${this.$t("general.Deleted")}`,
                                    text: `${this.$t(
                                        "general.Yourrowhasbeendeleted"
                                    )}`,
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                            })

                            .catch((err) => {
                                if (err.response.status == 400) {
                                    Swal.fire({
                                        icon: "error",
                                        title: `${this.$t("general.Error")}`,
                                        text: `${this.$t(
                                            "general.CantDeleteRelation"
                                        )}`,
                                    });
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
                });
            }
        },
        /**
         *  end delete countrie
         */
        /**
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.birth_date = new Date();
            this.applying_date = new Date();
            this.create = {
                applying_date: this.formatDate(new Date()),
                // applying_number: "",
                first_name: "",
                phone_code: "",
                second_name: "",
                third_name: "",
                last_name: "",
                family_name: "",
                birth_date: "",
                national_id: "",
                nationality_number: "",
                home_phone: "",
                work_phone: "",
                home_address: "",
                work_address: "",
                job: "",
                degree: "",
                acceptance: "0",
                gender: 1,
                member_type_id: 1,
                sponsor_id: null,
                application_number: "",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.$bvModal.hide(`create`);
        },
        async getSponsors() {
            this.isLoader = true;
            await adminApi
                .get(`/club-members/sponsers`)
                .then((res) => {
                    let l = res.data.data;
                    this.sponsors = l;
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
         *  hidden Modal (create)
         */
        async resetModal() {
            this.birth_date = new Date();
            this.applying_date = new Date();
            this.codeCountry = this.$store.getters["locationIp/countryCode"];
            if (this.isVisible("sponsor_id")) await this.getSponsors();
            this.create = {
                applying_date: this.formatDate(new Date()),
                // applying_number: "",
                first_name: "",
                phone_code: "",
                second_name: "",
                third_name: "",
                last_name: "",
                family_name: "",
                birth_date: "",
                national_id: "",
                nationality_number: "",
                home_phone: "",
                work_phone: "",
                home_address: "",
                work_address: "",
                job: "",
                degree: "",
                acceptance: "0",
                gender: 1,
                member_type_id: 1,
                sponsor_id: null,
                application_number: "",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  create countrie
         */
        resetForm() {
            this.birth_date = new Date();
            this.applying_date = new Date();
            this.codeCountry = this.$store.getters["locationIp/countryCode"];
            this.create = {
                applying_date: this.formatDate(new Date()),
                // applying_number: "",
                phone_code: "",
                first_name: "",
                second_name: "",
                third_name: "",
                last_name: "",
                family_name: "",
                birth_date: "",
                national_id: "",
                nationality_number: "",
                home_phone: "",
                work_phone: "",
                home_address: "",
                work_address: "",
                job: "",
                degree: "",
                acceptance: "0",
                gender: 1,
                member_type_id: 1,
                sponsor_id: null,
                application_number: "",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
        },

        AddSubmit() {
            this.$v.create.$touch();
            this.create.phone_code = this.codeCountry;
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .post(`/club-members/member-requests`, {
                        ...this.create,
                        company_id: this.$store.getters["auth/company_id"],
                    })
                    .then((res) => {
                        this.is_disabled = true;
                        this.getData();
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Addedsuccessfully")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        }, 500);
                        this.$bvModal.hide(`check_national_id`);
                        this.member_request_id = res.data.data.id;
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
        /**
         *  edit countrie
         */
        editSubmit(id) {
            this.$v.edit.$touch();
            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .put(`/club-members/member-requests/${id}`, this.edit)
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
        /**
         *   show Modal (edit)
         */
        async resetModalEdit(id) {
            if (this.isVisible("sponsor_id")) await this.getSponsors();
            let member = this.members.find((e) => id == e.id);
            this.birth_date = new Date(member.birth_date);
            this.applying_date = new Date(member.applying_date);
            this.edit.applying_number = member.applying_number;
            this.edit.first_name = member.first_name;
            this.edit.second_name = member.second_name;
            this.edit.phone_code = member.phone_code;
            this.edit.third_name = member.third_name;
            this.edit.last_name = member.last_name;
            this.edit.family_name = member.family_name;
            this.edit.birth_date = member.birth_date;
            this.edit.applying_date = member.applying_date;
            this.edit.national_id = member.national_id;
            this.edit.nationality_number = member.nationality_number;
            this.edit.home_phone = member.home_phone;
            this.edit.work_phone = member.work_phone;
            this.edit.home_address = member.home_address;
            this.edit.work_address = member.work_address;
            this.edit.job = member.job;
            this.edit.degree = member.degree;
            this.edit.gender = member.gender;
            this.edit.member_type_id = member.member_type_id;
            this.edit.sponsor_id = member.sponsor_id;
            this.edit.application_number = member.application_number;
            this.transaction = member.transaction;
            this.member_request_id = member.id;
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.birth_date = new Date();
            this.applying_date = new Date();
            this.errors = {};
            this.transaction = "";
            this.edit = {
                applying_date: this.formatDate(new Date()),
                // applying_number: "",
                phone_code: "",
                first_name: "",
                second_name: "",
                third_name: "",
                last_name: "",
                family_name: "",
                birth_date: "",
                national_id: "",
                nationality_number: "",
                home_phone: "",
                work_phone: "",
                home_address: "",
                work_address: "",
                job: "",
                degree: "",
                acceptance: "0",
                gender: 1,
                member_type_id: 1,
                sponsor_id: null,
                application_number: "",
            };
        },
        /*
         *  start  dynamicSortString
         */
        sortString(value) {
            return dynamicSortString(value);
        },
        SortNumber(value) {
            return dynamicSortNumber(value);
        },
        /**
         *  start  ckeckRow
         */
        checkRow(id) {
            if (!this.checkAll.includes(id)) {
                this.checkAll.push(id);
            } else {
                let index = this.checkAll.indexOf(id);
                this.checkAll.splice(index, 1);
            }
        },
        /**
         *  end  ckeckRow
         */

        /**
         *   Export Excel
         */
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
                if (dl) {
                    XLSX.write(wb, {
                        bookType: type,
                        bookSST: true,
                        type: "base64",
                    });
                } else {
                    XLSX.writeFile(
                        wb,
                        fn ||
                            ("Stores" + "." || "SheetJSTableExport.") +
                                (type || "xlsx")
                    );
                }
                this.enabled3 = true;
            }, 100);
        },
        arabicValue(txt) {
            this.create.name = arabicValue(txt);
            this.edit.name = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.name_e = englishValue(txt);
            this.edit.name_e = englishValue(txt);
        },
        updatePhoneEdit(e) {
            this.codeCountry = e.countryCode;
            this.edit.phone_code = e.countryCode;
            this.create.phone_code = e.countryCode;
        },
        showSubscriptionModal() {
            this.$bvModal.show("add_subscription");
        },
        async checkNationalId() {
            this.isLoader = true;
            await adminApi
                .get(
                    `/club-members/member-requests/checkNationalId?national_id=${this.create.national_id}`
                )
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data;
                    if (l) {
                        this.$bvModal.show("check_national_id");
                        this.checkMembers = l;
                    } else {
                        this.AddSubmit();
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
    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <Subscription
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            :member_request_id="member_request_id"
            :id="'add_subscription'"
            @created="getData"
        />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- start search -->
                        <div
                            class="row justify-content-between align-items-center mb-2"
                        >
                            <h4 class="header-title">
                                {{ $t("general.membersRequestTable") }}
                            </h4>
                            <div
                                class="col-xs-10 col-md-9 col-lg-7"
                                style="font-weight: 500"
                            >
                                <div
                                    class="d-inline-block"
                                    style="width: 22.2%"
                                >
                                    <!-- Basic dropdown -->
                                    <b-dropdown
                                        variant="primary"
                                        :text="$t('general.searchSetting')"
                                        ref="dropdown"
                                        class="btn-block setting-search"
                                    >
                                        <b-form-checkbox
                                            v-model="filterSetting"
                                            value="full_name"
                                            class="mb-1"
                                        >
                                            {{
                                                $t(
                                                    "general.NameMembershipApplicant"
                                                )
                                            }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-model="filterSetting"
                                            value="document_no"
                                            class="mb-1"
                                        >
                                            {{
                                                $t("general.SubscriptionNumber")
                                            }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-if="isVisible('national_id')"
                                            v-model="filterSetting"
                                            value="national_id"
                                            class="mb-1"
                                            >{{
                                                getCompanyKey(
                                                    "member_national_id"
                                                )
                                            }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-if="
                                                isVisible('nationality_number')
                                            "
                                            v-model="filterSetting"
                                            value="nationality_number"
                                            class="mb-1"
                                            >{{
                                                getCompanyKey(
                                                    "member_nationality_number"
                                                )
                                            }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-if="isVisible('sponsor_id')"
                                            v-model="filterSetting"
                                            value="sponsor_id"
                                            class="mb-1"
                                            >{{ getCompanyKey("sponsor") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-if="
                                                isVisible('application_number')
                                            "
                                            v-model="filterSetting"
                                            value="application_number"
                                            class="mb-1"
                                            >{{
                                                $t("general.application_number")
                                            }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-if="isVisible('home_phone')"
                                            v-model="filterSetting"
                                            value="home_phone"
                                            class="mb-1"
                                            >{{
                                                getCompanyKey(
                                                    "member_home_phone"
                                                )
                                            }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-if="isVisible('work_phone')"
                                            v-model="filterSetting"
                                            value="work_phone"
                                            class="mb-1"
                                            >{{
                                                getCompanyKey(
                                                    "member_work_phone"
                                                )
                                            }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-if="isVisible('home_address')"
                                            v-model="filterSetting"
                                            value="home_address"
                                            class="mb-1"
                                            >{{
                                                getCompanyKey(
                                                    "member_home_address"
                                                )
                                            }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-if="isVisible('work_address')"
                                            v-model="filterSetting"
                                            value="work_address"
                                            class="mb-1"
                                            >{{
                                                getCompanyKey(
                                                    "member_work_address"
                                                )
                                            }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-if="isVisible('job')"
                                            v-model="filterSetting"
                                            value="job"
                                            class="mb-1"
                                            >{{
                                                getCompanyKey("member_job")
                                            }}</b-form-checkbox
                                        >
                                        <b-form-checkbox
                                            v-if="isVisible('degree')"
                                            v-model="filterSetting"
                                            value="degree"
                                            class="mb-1"
                                            >{{
                                                getCompanyKey("member_degree")
                                            }}</b-form-checkbox
                                        >
                                    </b-dropdown>
                                    <!-- Basic dropdown -->
                                </div>

                                <div
                                    class="d-inline-block position-relative"
                                    style="width: 77%"
                                >
                                    <span
                                        :class="[
                                            'search-custom position-absolute',
                                            $i18n.locale == 'ar'
                                                ? 'search-custom-ar'
                                                : '',
                                        ]"
                                    >
                                        <i class="fe-search"></i>
                                    </span>
                                    <input
                                        class="form-control"
                                        style="
                                            display: block;
                                            width: 93%;
                                            padding-top: 3px;
                                        "
                                        type="text"
                                        v-model.trim="serachInput"
                                        @input="handelSerach"
                                        :placeholder="`${$t(
                                            'general.Search'
                                        )}...`"
                                    />
                                </div>
                            </div>
                        </div>
                        <!-- end search -->

                        <div
                            class="row justify-content-between align-items-center mb-2 px-1"
                        >
                            <div
                                class="col-md-5 d-flex align-items-center mb-1 mb-xl-0"
                            >
                                <!-- start create and printer -->
                                <b-button
                                    v-b-modal.create
                                    v-if="
                                        isPermission(
                                            'create memberRequest club'
                                        )
                                    "
                                    variant="primary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button
                                        @click="ExportExcel('xlsx')"
                                        class="custom-btn-dowonload"
                                    >
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button
                                        @click="printElement('#printData')"
                                        class="custom-btn-dowonload"
                                    >
                                        <i class="fe-printer"></i>
                                    </button>
                                    <button
                                        class="custom-btn-dowonload"
                                        @click="
                                            $bvModal.show(
                                                `modal-edit-${checkAll[0]}`
                                            )
                                        "
                                        v-if="
                                            checkAll.length == 1 &&
                                            isPermission(
                                                'update memberRequest club'
                                            )
                                        "
                                    >
                                        <i
                                            class="mdi mdi-square-edit-outline"
                                        ></i>
                                    </button>
                                    <!-- start mult delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="
                                            checkAll.length > 1 &&
                                            isPermission(
                                                'delete memberRequest club'
                                            )
                                        "
                                        @click.prevent="deleteBranch(checkAll)"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="
                                            checkAll.length == 1 &&
                                            isPermission(
                                                'delete memberRequest club'
                                            )
                                        "
                                        @click.prevent="
                                            deleteBranch(checkAll[0])
                                        "
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!--  end one delete  -->
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ $t("general.showSponsorPrint") }}
                                        </label>
                                        <select
                                            v-model="showSponsor"
                                            class="form-control"
                                        >
                                            <option value="1">
                                                {{ $t("general.Yes") }}
                                            </option>
                                            <option value="0">
                                                {{ $t("general.No") }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <!-- end create and printer -->
                            </div>
                            <div
                                class="col-xs-10 col-md-7 col-lg-7 d-flex align-items-center justify-content-end"
                            >
                                <div class="d-fex">
                                    <!-- start filter and setting -->
                                    <div class="d-inline-block">
                                        <b-button
                                            v-b-modal.memberFilter
                                            class="mx-1 custom-btn-background"
                                        >
                                            {{ $t("general.filter") }}
                                            <i class="fas fa-filter"></i>
                                        </b-button>
                                        <b-button
                                            class="mx-1 custom-btn-background"
                                        >
                                            {{ $t("general.group") }}
                                            <i class="fe-menu"></i>
                                        </b-button>
                                        <!-- Basic dropdown -->
                                        <b-dropdown
                                            variant="primary"
                                            :html="`${$t(
                                                'general.setting'
                                            )} <i class='fe-settings'></i>`"
                                            ref="dropdown"
                                            class="dropdown-custom-ali"
                                        >
                                            <b-form-checkbox
                                                v-model="setting.full_name"
                                                class="mb-1"
                                                >{{
                                                    $t(
                                                        "general.NameMembershipApplicant"
                                                    )
                                                }}</b-form-checkbox
                                            >
                                            <b-form-checkbox
                                                v-if="isVisible('birth_date')"
                                                v-model="setting.birth_date"
                                                class="mb-1"
                                                >{{
                                                    getCompanyKey(
                                                        "member_birth_date"
                                                    )
                                                }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('gender')"
                                                v-model="setting.gender"
                                                class="mb-1"
                                                >{{
                                                    getCompanyKey("member_type")
                                                }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="
                                                    isVisible('member_type_id')
                                                "
                                                v-model="setting.member_type_id"
                                                class="mb-1"
                                                >{{ $t("general.status") }}
                                            </b-form-checkbox>

                                            <b-form-checkbox
                                                v-model="
                                                    setting.Subscription_receipt_number
                                                "
                                                class="mb-1"
                                                >{{
                                                    getCompanyKey(
                                                        "Subscription_receipt_number"
                                                    )
                                                }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('national_id')"
                                                v-model="setting.national_id"
                                                class="mb-1"
                                                >{{
                                                    getCompanyKey(
                                                        "member_national_id"
                                                    )
                                                }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="
                                                    isVisible(
                                                        'nationality_number'
                                                    )
                                                "
                                                v-model="
                                                    setting.nationality_number
                                                "
                                                class="mb-1"
                                                >{{
                                                    getCompanyKey(
                                                        "member_nationality_number"
                                                    )
                                                }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('sponsor_id')"
                                                v-model="setting.sponsor_id"
                                                class="mb-1"
                                                >{{ getCompanyKey("sponsor") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="
                                                    isVisible(
                                                        'application_number'
                                                    )
                                                "
                                                v-model="
                                                    setting.application_number
                                                "
                                                class="mb-1"
                                                >{{
                                                    $t(
                                                        "general.application_number"
                                                    )
                                                }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('home_phone')"
                                                v-model="setting.home_phone"
                                                class="mb-1"
                                                >{{
                                                    getCompanyKey(
                                                        "member_home_phone"
                                                    )
                                                }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('work_phone')"
                                                v-model="setting.work_phone"
                                                class="mb-1"
                                                >{{
                                                    getCompanyKey(
                                                        "member_work_phone"
                                                    )
                                                }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('home_address')"
                                                v-model="setting.home_address"
                                                class="mb-1"
                                                >{{
                                                    getCompanyKey(
                                                        "member_home_address"
                                                    )
                                                }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('work_address')"
                                                v-model="setting.work_address"
                                                class="mb-1"
                                                >{{
                                                    getCompanyKey(
                                                        "member_work_address"
                                                    )
                                                }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('job')"
                                                v-model="setting.job"
                                                class="mb-1"
                                                >{{
                                                    getCompanyKey("member_job")
                                                }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('degree')"
                                                v-model="setting.degree"
                                                class="mb-1"
                                                >{{
                                                    getCompanyKey(
                                                        "member_degree"
                                                    )
                                                }}
                                            </b-form-checkbox>
                                            <div
                                                class="d-flex justify-content-end"
                                            >
                                                <a
                                                    href="javascript:void(0)"
                                                    class="btn btn-primary btn-sm"
                                                    >Apply</a
                                                >
                                            </div>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </div>
                                    <!-- end filter and setting -->
                                    <div
                                        class="d-inline-flex align-items-center"
                                    >
                                        <label
                                            for="rows"
                                            class="control-label mb-0"
                                        >
                                            {{ $t("general.chooseRows") }}
                                        </label>
                                        <span class="mx-1">:</span>
                                        <input
                                            type="number"
                                            id="rows"
                                            v-model.number="inputPerPage"
                                            @input="handelPerPageInput"
                                            class="form-control-sm mb-0"
                                            style="width: 70px"
                                        />
                                    </div>

                                    <!-- start Pagination -->
                                    <div
                                        class="d-inline-flex align-items-center pagination-custom"
                                    >
                                        <div
                                            class="d-inline-block"
                                            style="font-size: 13px"
                                        >
                                            {{ membersPagination.from }}-{{
                                                membersPagination.to
                                            }}
                                            /
                                            {{ membersPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{
                                                    'pointer-events':
                                                        membersPagination.current_page ==
                                                        1
                                                            ? 'none'
                                                            : '',
                                                }"
                                                @click.prevent="
                                                    getData(
                                                        membersPagination.current_page -
                                                            1
                                                    )
                                                "
                                            >
                                                <span>&lt;</span>
                                            </a>
                                            <input
                                                type="text"
                                                @keyup.enter="
                                                    getDataCurrentPage()
                                                "
                                                v-model="current_page"
                                                class="pagination-current-page"
                                            />
                                            <a
                                                href="javascript:void(0)"
                                                :style="{
                                                    'pointer-events':
                                                        membersPagination.last_page ==
                                                        membersPagination.current_page
                                                            ? 'none'
                                                            : '',
                                                }"
                                                @click.prevent="
                                                    getData(
                                                        membersPagination.current_page +
                                                            1
                                                    )
                                                "
                                            >
                                                <span>&gt;</span>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>
                        <b-modal
                            id="memberFilter"
                            title="Filter Members"
                            @ok="applyFilters"
                            @cancel="getOriginalMembers"
                            ok-title="Apply"
                            cancel-title="Cancel"
                        >
                            <div class="form-group md-6">
                                <label class="control-label">
                                    {{ $t("general.filterMembers") }}
                                </label>
                                <multiselect
                                    v-model="selectedNameMember"
                                    :options="
                                        membersNames.map((type) => type.id)
                                    "
                                    :custom-label="getMemberLabel"
                                    :multiple="true"
                                >
                                </multiselect>
                            </div>
                            <div class="form-group md-6">
                                <label class="control-label">
                                    {{ $t("general.filterSponser") }}
                                </label>
                                <multiselect
                                    v-model="selectedNameSponser"
                                    :options="
                                        sponsorsNames.map((type) => type.id)
                                    "
                                    :custom-label="getSponsorsLabel"
                                    :multiple="true"
                                >
                                </multiselect>
                            </div>
                        </b-modal>

                        <!--  create   -->
                        <b-modal
                            id="create"
                            :title="getCompanyKey('member_request_create_form')"
                            title-class="font-18"
                            dialog-class="modal-full-width"
                            body-class="p-4 "
                            :hide-footer="true"
                            @show="resetModal"
                            @hidden="resetModalHidden"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <b-button
                                        @click="showSubscriptionModal"
                                        variant="primary"
                                        :disabled="!is_disabled"
                                        type="button"
                                        v-print="'#printInv'"
                                        :class="[
                                            'font-weight-bold px-2',
                                            is_disabled ? 'mx-2' : 'mx-2',
                                        ]"
                                    >
                                        {{ $t("general.addSubscription") }}
                                    </b-button>
                                    <b-button
                                        variant="success"
                                        :disabled="!is_disabled"
                                        @click.prevent="resetForm"
                                        type="button"
                                        :class="[
                                            'font-weight-bold px-2',
                                            is_disabled ? 'mx-2' : '',
                                        ]"
                                    >
                                        {{ $t("general.AddNewRecord") }}
                                    </b-button>
                                    <template v-if="!is_disabled">
                                        <b-button
                                            variant="success"
                                            type="submit"
                                            class="mx-1"
                                            v-if="!isLoader"
                                            @click.prevent="checkNationalId"
                                        >
                                            {{ $t("general.Add") }}
                                        </b-button>
                                        <b-button
                                            variant="success"
                                            class="mx-1"
                                            disabled
                                            v-else
                                        >
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only"
                                                >{{
                                                    $t("login.Loading")
                                                }}...</span
                                            >
                                        </b-button>
                                    </template>
                                    <b-button
                                        @click.prevent="$bvModal.hide(`create`)"
                                        variant="danger"
                                        type="button"
                                    >
                                        {{ $t("general.Cancel") }}
                                    </b-button>
                                </div>
                                <div class="row">
                                    <div
                                        class="col-md-3 position-relative"
                                        v-if="isVisible('applying_date')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "apply_membership_date"
                                                    )
                                                }}
                                                <span
                                                    v-if="
                                                        isRequired(
                                                            'applying_date'
                                                        )
                                                    "
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="
                                                    $v.create.applying_date
                                                        .$model
                                                "
                                                format="YYYY-MM-DD"
                                                valueType="format"
                                                :confirm="false"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.applying_date
                                                            .$error ||
                                                        errors.applying_date,
                                                    'is-valid':
                                                        !$v.create.applying_date
                                                            .$invalid &&
                                                        !errors.applying_date,
                                                }"
                                            ></date-picker>

                                            <template
                                                v-if="errors.applying_date"
                                            >
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.applying_date"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-3"
                                        v-if="isVisible('sponsor_id')"
                                    >
                                        <div
                                            class="form-group position-relative"
                                        >
                                            <label class="control-label">
                                                {{ getCompanyKey("sponsor") }}
                                                <span
                                                    v-if="
                                                        isRequired('sponsor_id')
                                                    "
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <multiselect
                                                v-model="create.sponsor_id"
                                                :options="
                                                    sponsors.map(
                                                        (type) => type.id
                                                    )
                                                "
                                                :custom-label="
                                                    (opt) =>
                                                        sponsors.find(
                                                            (x) => x.id == opt
                                                        )
                                                            ? $i18n.locale ==
                                                              'ar'
                                                                ? sponsors.find(
                                                                      (x) =>
                                                                          x.id ==
                                                                          opt
                                                                  ).name
                                                                : sponsors.find(
                                                                      (x) =>
                                                                          x.id ==
                                                                          opt
                                                                  ).name_e
                                                            : null
                                                "
                                            >
                                            </multiselect>
                                            <div
                                                v-if="
                                                    $v.create.sponsor_id
                                                        .$error ||
                                                    errors.sponsor_id
                                                "
                                                class="text-danger"
                                            >
                                                {{
                                                    $t(
                                                        "general.fieldIsRequired"
                                                    )
                                                }}
                                            </div>
                                            <template v-if="errors.sponsor_id">
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.sponsor_id"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-3"
                                        v-if="isVisible('application_number')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    $t(
                                                        "general.application_number"
                                                    )
                                                }}
                                                <span
                                                    v-if="
                                                        isRequired(
                                                            'application_number'
                                                        )
                                                    "
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <input
                                                v-model="
                                                    $v.create.application_number
                                                        .$model
                                                "
                                                class="form-control"
                                                type="number"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create
                                                            .application_number
                                                            .$error ||
                                                        errors.application_number,
                                                    'is-valid':
                                                        !$v.create
                                                            .application_number
                                                            .$invalid &&
                                                        !errors.application_number,
                                                }"
                                            />
                                            <template
                                                v-if="errors.application_number"
                                            >
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.application_number"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <!--                  <div class="col-md-3" v-if="isVisible('applying_number')">-->
                                    <!--                    <div class="form-group">-->
                                    <!--                      <label class="control-label">-->
                                    <!--                          {{getCompanyKey("apply_membership_number")}}-->
                                    <!--                          <span v-if="isRequired('applying_number')" class="text-danger">*</span>-->
                                    <!--                      </label>-->
                                    <!--                      <input-->
                                    <!--                        v-model="$v.create.applying_number.$model"-->
                                    <!--                        class="form-control"-->
                                    <!--                        type="number"-->
                                    <!--                        :class="{-->
                                    <!--                          'is-invalid':-->
                                    <!--                            $v.create.applying_number.$error ||-->
                                    <!--                            errors.applying_number,-->
                                    <!--                          'is-valid':-->
                                    <!--                            !$v.create.applying_number.$invalid &&-->
                                    <!--                            !errors.applying_number,-->
                                    <!--                        }"-->
                                    <!--                      />-->
                                    <!--                      <template v-if="errors.applying_number">-->
                                    <!--                        <ErrorMessage-->
                                    <!--                          v-for="(-->
                                    <!--                            errorMessage, index-->
                                    <!--                          ) in errors.applying_number"-->
                                    <!--                          :key="index"-->
                                    <!--                          >{{ errorMessage }}-->
                                    <!--                        </ErrorMessage>-->
                                    <!--                      </template>-->
                                    <!--                    </div>-->
                                    <!--                  </div>-->
                                </div>
                                <hr
                                    style="
                                        margin: 10px 0 !important;
                                        border-top: 1px solid
                                            rgb(141 163 159 / 42%);
                                    "
                                    v-if="
                                        isVisible('first_name') ||
                                        isVisible('second_name') ||
                                        isVisible('third_name') ||
                                        isVisible('last_name') ||
                                        isVisible('family_name')
                                    "
                                />
                                <div class="row">
                                    <div
                                        class="col-md-2"
                                        v-if="isVisible('first_name')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_first_name"
                                                    )
                                                }}
                                                <span
                                                    v-if="
                                                        isRequired('first_name')
                                                    "
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>

                                            <input
                                                v-model="
                                                    $v.create.first_name.$model
                                                "
                                                class="form-control"
                                                type="text"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.first_name
                                                            .$error ||
                                                        errors.first_name,
                                                    'is-valid':
                                                        !$v.create.first_name
                                                            .$invalid &&
                                                        !errors.first_name,
                                                }"
                                            />
                                            <template v-if="errors.first_name">
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.first_name"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-2"
                                        v-if="isVisible('second_name')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_second_name"
                                                    )
                                                }}
                                                <span
                                                    v-if="
                                                        isRequired(
                                                            'second_name'
                                                        )
                                                    "
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <input
                                                v-model="
                                                    $v.create.second_name.$model
                                                "
                                                class="form-control"
                                                type="text"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.second_name
                                                            .$error ||
                                                        errors.second_name,
                                                    'is-valid':
                                                        !$v.create.second_name
                                                            .$invalid &&
                                                        !errors.second_name,
                                                }"
                                            />
                                            <template v-if="errors.second_name">
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.second_name"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-2"
                                        v-if="isVisible('third_name')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_third_name"
                                                    )
                                                }}
                                                <span
                                                    v-if="
                                                        isRequired('third_name')
                                                    "
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <input
                                                v-model="
                                                    $v.create.third_name.$model
                                                "
                                                class="form-control"
                                                type="text"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.third_name
                                                            .$error ||
                                                        errors.third_name,
                                                    'is-valid':
                                                        !$v.create.third_name
                                                            .$invalid &&
                                                        !errors.third_name,
                                                }"
                                            />
                                            <template v-if="errors.third_name">
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.third_name"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-2"
                                        v-if="isVisible('last_name')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_last_name"
                                                    )
                                                }}
                                                <span
                                                    v-if="
                                                        isRequired('last_name')
                                                    "
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <input
                                                v-model="
                                                    $v.create.last_name.$model
                                                "
                                                class="form-control"
                                                type="text"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.last_name
                                                            .$error ||
                                                        errors.last_name,
                                                    'is-valid':
                                                        !$v.create.last_name
                                                            .$invalid &&
                                                        !errors.last_name,
                                                }"
                                            />
                                            <template v-if="errors.last_name">
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.third_name"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-2"
                                        v-if="isVisible('family_name')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_family_name"
                                                    )
                                                }}
                                                <span
                                                    v-if="
                                                        isRequired(
                                                            'family_name'
                                                        )
                                                    "
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <input
                                                v-model="
                                                    $v.create.family_name.$model
                                                "
                                                class="form-control"
                                                type="text"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.family_name
                                                            .$error ||
                                                        errors.family_name,
                                                    'is-valid':
                                                        !$v.create.family_name
                                                            .$invalid &&
                                                        !errors.family_name,
                                                }"
                                            />
                                            <template v-if="errors.family_name">
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.family_name"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <hr
                                    style="
                                        margin: 10px 0 !important;
                                        border-top: 1px solid
                                            rgb(141 163 159 / 42%);
                                    "
                                    v-if="
                                        isVisible('birth_date') ||
                                        isVisible('gender') ||
                                        isVisible('national_id') ||
                                        isVisible('nationality_number') ||
                                        isVisible('birth_date')
                                    "
                                />
                                <div class="row">
                                    <div
                                        class="col-md-3 position-relative"
                                        v-if="isVisible('birth_date')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_birth_date"
                                                    )
                                                }}
                                                <span
                                                    v-if="
                                                        isRequired('birth_date')
                                                    "
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="
                                                    $v.create.birth_date.$model
                                                "
                                                format="YYYY-MM-DD"
                                                valueType="format"
                                                :confirm="false"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.birth_date
                                                            .$error ||
                                                        errors.birth_date,
                                                    'is-valid':
                                                        !$v.create.birth_date
                                                            .$invalid &&
                                                        !errors.birth_date,
                                                }"
                                            ></date-picker>

                                            <template v-if="errors.birth_date">
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.birth_date"
                                                    :key="index"
                                                >
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-3"
                                        v-if="isVisible('gender')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label mr-2">
                                                {{
                                                    getCompanyKey(
                                                        "member_gender"
                                                    )
                                                }}
                                                <span
                                                    v-if="isRequired('gender')"
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <b-form-group
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.gender
                                                            .$error ||
                                                        errors.gender,
                                                    'is-valid':
                                                        !$v.create.gender
                                                            .$invalid &&
                                                        !errors.gender,
                                                }"
                                            >
                                                <b-form-radio
                                                    class="d-inline-block"
                                                    v-model="
                                                        $v.create.gender.$model
                                                    "
                                                    name="create_gender"
                                                    value="1"
                                                    >{{ $t("general.male") }}
                                                </b-form-radio>
                                                <b-form-radio
                                                    class="d-inline-block m-1"
                                                    v-model="
                                                        $v.create.gender.$model
                                                    "
                                                    name="create_gender"
                                                    value="0"
                                                    >{{ $t("general.female") }}
                                                </b-form-radio>
                                            </b-form-group>
                                            <template v-if="errors.gender">
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.gender"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-3"
                                        v-if="isVisible('national_id')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_national_id"
                                                    )
                                                }}
                                                <span
                                                    v-if="
                                                        isRequired(
                                                            'national_id'
                                                        )
                                                    "
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <input
                                                v-model="
                                                    $v.create.national_id.$model
                                                "
                                                class="form-control"
                                                type="number"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.national_id
                                                            .$error ||
                                                        errors.national_id,
                                                    'is-valid':
                                                        !$v.create.national_id
                                                            .$invalid &&
                                                        !errors.national_id,
                                                }"
                                            />
                                            <template v-if="errors.national_id">
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.national_id"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-3"
                                        v-if="isVisible('nationality_number')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_nationality_number"
                                                    )
                                                }}
                                                <span
                                                    v-if="
                                                        isRequired(
                                                            'nationality_number'
                                                        )
                                                    "
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <input
                                                v-model="
                                                    $v.create.nationality_number
                                                        .$model
                                                "
                                                class="form-control"
                                                type="number"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create
                                                            .nationality_number
                                                            .$error ||
                                                        errors.nationality_number,
                                                    'is-valid':
                                                        !$v.create
                                                            .nationality_number
                                                            .$invalid &&
                                                        !errors.nationality_number,
                                                }"
                                            />
                                            <template
                                                v-if="errors.nationality_number"
                                            >
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.nationality_number"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <hr
                                    style="
                                        margin: 10px 0 !important;
                                        border-top: 1px solid
                                            rgb(141 163 159 / 42%);
                                    "
                                    v-if="
                                        isVisible('home_phone') ||
                                        isVisible('home_address') ||
                                        isVisible('work_phone') ||
                                        isVisible('work_address')
                                    "
                                />
                                <div class="row">
                                    <div
                                        class="col-md-3"
                                        v-if="isVisible('home_phone')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_home_phone"
                                                    )
                                                }}
                                                <span
                                                    v-if="
                                                        isRequired('home_phone')
                                                    "
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <VuePhoneNumberInput
                                                v-model="
                                                    $v.create.home_phone.$model
                                                "
                                                :default-country-code="
                                                    codeCountry
                                                "
                                                valid-color="#28a745"
                                                error-color="#dc3545"
                                                :preferred-countries="[
                                                    'FR',
                                                    'EG',
                                                    'DE',
                                                ]"
                                                @update="updatePhoneEdit"
                                            />
                                            <template v-if="errors.home_phone">
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.home_phone"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-3"
                                        v-if="isVisible('home_address')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_home_address"
                                                    )
                                                }}
                                                <span
                                                    v-if="
                                                        isRequired(
                                                            'home_address'
                                                        )
                                                    "
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <input
                                                v-model="
                                                    $v.create.home_address
                                                        .$model
                                                "
                                                class="form-control"
                                                type="text"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.home_address
                                                            .$error ||
                                                        errors.home_address,
                                                    'is-valid':
                                                        !$v.create.home_address
                                                            .$invalid &&
                                                        !errors.home_address,
                                                }"
                                            />
                                            <template
                                                v-if="errors.home_address"
                                            >
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.home_address"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-3"
                                        v-if="isVisible('work_phone')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_work_phone"
                                                    )
                                                }}
                                                <span
                                                    v-if="
                                                        isRequired('work_phone')
                                                    "
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <VuePhoneNumberInput
                                                v-model="
                                                    $v.create.work_phone.$model
                                                "
                                                :default-country-code="
                                                    codeCountry
                                                "
                                                valid-color="#28a745"
                                                error-color="#dc3545"
                                                :preferred-countries="[
                                                    'FR',
                                                    'EG',
                                                    'DE',
                                                ]"
                                                @update="updatePhoneEdit"
                                            />
                                            <template v-if="errors.work_phone">
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.work_phone"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-3"
                                        v-if="isVisible('work_address')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_work_address"
                                                    )
                                                }}
                                                <span
                                                    v-if="
                                                        isRequired(
                                                            'work_address'
                                                        )
                                                    "
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <input
                                                v-model="
                                                    $v.create.work_address
                                                        .$model
                                                "
                                                class="form-control"
                                                type="text"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.work_address
                                                            .$error ||
                                                        errors.work_address,
                                                    'is-valid':
                                                        !$v.create.work_address
                                                            .$invalid &&
                                                        !errors.work_address,
                                                }"
                                            />
                                            <template
                                                v-if="errors.work_address"
                                            >
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.work_address"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <hr
                                    style="
                                        margin: 10px 0 !important;
                                        border-top: 1px solid
                                            rgb(141 163 159 / 42%);
                                    "
                                    v-if="
                                        isVisible('job') ||
                                        isVisible('degree') ||
                                        isVisible('member_type_id')
                                    "
                                />
                                <div class="row">
                                    <div
                                        class="col-md-3"
                                        v-if="isVisible('job')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey("member_job")
                                                }}
                                                <span
                                                    v-if="isRequired('job')"
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <input
                                                v-model="$v.create.job.$model"
                                                class="form-control"
                                                type="text"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.job.$error ||
                                                        errors.job,
                                                    'is-valid':
                                                        !$v.create.job
                                                            .$invalid &&
                                                        !errors.job,
                                                }"
                                            />
                                            <template v-if="errors.job">
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.job"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-3"
                                        v-if="isVisible('degree')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_degree"
                                                    )
                                                }}
                                                <span
                                                    v-if="isRequired('degree')"
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <input
                                                v-model="
                                                    $v.create.degree.$model
                                                "
                                                class="form-control"
                                                type="text"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.degree
                                                            .$error ||
                                                        errors.degree,
                                                    'is-valid':
                                                        !$v.create.degree
                                                            .$invalid &&
                                                        !errors.degree,
                                                }"
                                            />
                                            <template v-if="errors.degree">
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.degree"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-3"
                                        v-if="isVisible('member_type_id')"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t("general.status") }}
                                                <span
                                                    v-if="isRequired('type')"
                                                    class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <select
                                                :disabled="true"
                                                class="form-control"
                                                v-model="create.member_type_id"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.member_type_id
                                                            .$error ||
                                                        errors.member_type_id,
                                                    'is-valid':
                                                        !$v.create
                                                            .member_type_id
                                                            .$invalid &&
                                                        !errors.member_type_id,
                                                }"
                                            >
                                                <option value="1">
                                                    {{
                                                        $t(
                                                            "general.pendingMember"
                                                        )
                                                    }}
                                                </option>
                                                <option value="2">
                                                    {{
                                                        $t(
                                                            "general.unacceptable"
                                                        )
                                                    }}
                                                </option>
                                            </select>

                                            <template
                                                v-if="errors.member_type_id"
                                            >
                                                <ErrorMessage
                                                    v-for="(
                                                        errorMessage, index
                                                    ) in errors.member_type_id"
                                                    :key="index"
                                                    >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /create   -->
                        <!--  check_national_id   -->
                        <b-modal
                            id="check_national_id"
                            :title="
                                $t('general.WarningThisMemberAlreadyExists')
                            "
                            title-class="font-18"
                            body-class="p-4 "
                            :hide-footer="true"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <template
                                        v-if="
                                            !is_disabled &&
                                            checkMembers &&
                                            checkMembers.status &&
                                            checkMembers.status.id != 1
                                        "
                                    >
                                        <b-button
                                            variant="success"
                                            type="submit"
                                            class="mx-1"
                                            v-if="!isLoader"
                                            @click.prevent="AddSubmit"
                                        >
                                            {{ $t("general.Add") }}
                                        </b-button>
                                        <b-button
                                            variant="success"
                                            class="mx-1"

                                            v-else
                                        >
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only"
                                                >{{
                                                    $t("login.Loading")
                                                }}...</span
                                            >
                                        </b-button>
                                    </template>
                                    <b-button
                                        @click.prevent="
                                            $bvModal.hide(`check_national_id`)
                                        "
                                        variant="danger"
                                        type="button"
                                    >
                                        {{ $t("general.Cancel") }}
                                    </b-button>
                                </div>
                                <div class="row" v-if="checkMembers">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_membership_number"
                                                    )
                                                }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                v-model="
                                                    checkMembers.membership_number
                                                "
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t("general.name") }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                v-model="checkMembers.full_name"
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6"
                                        v-if="checkMembers.birth_date"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_birth_date"
                                                    )
                                                }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                v-model="
                                                    checkMembers.birth_date
                                                "
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6"
                                        v-if="checkMembers.gender"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_gender"
                                                    )
                                                }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                :value="
                                                    checkMembers.gender
                                                        ? parseInt(
                                                              checkMembers.gender
                                                          ) == 1
                                                            ? $t('general.male')
                                                            : $t(
                                                                  'general.female'
                                                              )
                                                        : '---'
                                                "
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6"
                                        v-if="checkMembers.membership_date"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_membership_date"
                                                    )
                                                }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                :value="
                                                    checkMembers.membership_date
                                                        ? formatDate(
                                                              checkMembers.membership_date
                                                          )
                                                        : '---'
                                                "
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6"
                                        v-if="checkMembers.financial_status_id"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "financial_status"
                                                    )
                                                }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                :value="
                                                    checkMembers.financial_status
                                                        ? $i18n.locale == 'ar'
                                                            ? checkMembers
                                                                  .financial_status
                                                                  .name
                                                            : checkMembers
                                                                  .financial_status
                                                                  .name_e
                                                        : '---'
                                                "
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6"
                                        v-if="checkMembers.member_status_id"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t("general.status") }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                :value="
                                                    checkMembers.status
                                                        ? $i18n.locale == 'ar'
                                                            ? checkMembers
                                                                  .status.name
                                                            : checkMembers
                                                                  .status.name_e
                                                        : '---'
                                                "
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6"
                                        v-if="checkMembers.last_cm_transaction"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t("general.LstPayDate") }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                :value="
                                                    checkMembers.last_cm_transaction
                                                        ? formatDate(
                                                              checkMembers
                                                                  .last_cm_transaction
                                                                  .date
                                                          )
                                                        : '---'
                                                "
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6"
                                        v-if="checkMembers.last_cm_transaction"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    $t("general.ReceiptNumber")
                                                }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                :value="
                                                    checkMembers.last_cm_transaction
                                                        ? checkMembers
                                                              .last_cm_transaction
                                                              .document_no
                                                        : '---'
                                                "
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6"
                                        v-if="checkMembers.last_cm_transaction"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t("general.ForAYear") }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                :value="
                                                    checkMembers.last_cm_transaction
                                                        ? checkMembers
                                                              .last_cm_transaction
                                                              .year
                                                        : '---'
                                                "
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6"
                                        v-if="checkMembers.national_id"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_national_id"
                                                    )
                                                }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                :value="
                                                    checkMembers.national_id
                                                "
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6"
                                        v-if="checkMembers.home_phone"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_home_phone"
                                                    )
                                                }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                :value="checkMembers.home_phone"
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6"
                                        v-if="checkMembers.home_address"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_home_address"
                                                    )
                                                }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                :value="
                                                    checkMembers.home_address
                                                "
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6"
                                        v-if="checkMembers.work_phone"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_work_phone"
                                                    )
                                                }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                :value="checkMembers.work_phone"
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6"
                                        v-if="checkMembers.job"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey("member_job")
                                                }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                :value="checkMembers.job"
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6"
                                        v-if="checkMembers.degree"
                                    >
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{
                                                    getCompanyKey(
                                                        "member_degree"
                                                    )
                                                }}
                                            </label>
                                            <input
                                                :disabled="true"
                                                :value="checkMembers.degree"
                                                class="form-control"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /check_national_id   -->
                        <!-- start .table-responsive-->
                        <div
                            class="table-responsive mb-3 custom-table-theme position-relative"
                            ref="exportable_table"
                            id="printData"
                        >
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->
                            <div
                                style="display: none;"
                                class="data-header-print"
                                :class="[
                                    $i18n.locale == 'ar' ? 'dir-print-rtl' : 'dir-print-ltr',
                                ]"
                            >
                                <!-- Left section with logo -->
                                <div style="width: 15%; padding-left: 20px">
                                    <img style="width: 100%" :src="'/images/sulib.png'" />
                                </div>

                                <!-- Center section with headings -->
                                <div
                                    class="text-center"
                                    style="
                                        width: 65%;
                                        display: flex;
                            flex-direction: column;
                            align-items: center;
                        "
                            >
                                <h2 style="font-weight: bold">
                                    {{ $t("general.SulaibikhatClub") }}
                                </h2>
                                <h2 style="font-weight: bold">
                                    {{ $t("general.SubscriptionRequests") }}
                                </h2>
                            </div>

                            <!-- Right section with total count and sponsors -->
                            <div
                                class="row"
                                style="
                                    display: flex;
                                    flex-direction: column;
                                    align-items: flex-start;
                                    width: 20%;
                                "
                            >
                                <!-- Total Count -->
                                <div
                                    class="text-right"
                                    style="
                                        width: 100%;
                                        text-align: left;
                                        margin-bottom: 10px;
                                    "
                                >
                                    <h5
                                        style="
                                            font-size: 18px;
                                            font-weight: bold;
                                        "
                                    >
                                        {{ $t("general.totalCount") }} : {{ members.length }}
                                    </h5>
                                </div>

                                <!-- Sponsor Labels -->
                                <div
                                    class="text-left p-0 m-0"
                                    v-if="members.length > 0 && parseInt(showSponsor) == 1"
                                    style="width: 100%;"
                                >
                                    <h3
                                        style="
                                            font-weight: bold;
                                            margin: 0 0 5px 0;
                                            font-size: 14px;
                                        "
                                    >
                                        {{ getCompanyKey("sponsor") }}:
                                    </h3>
                                    <ul style="padding-left: 20px; margin: 0;">
                                        <li
                                            v-for="sponsor in printSponsor"
                                            :key="sponsor"
                                            style="list-style: none;"
                                        >
                                            {{ sponsor }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                            <table
                                class="table table-borderless table-hover table-centered m-0"
                                :class="[
                                    $i18n.locale == 'ar'
                                        ? 'dir-print-rtl'
                                        : 'dir-print-ltr',
                                ]"
                            >
                                <thead>
                                    <tr>
                                        <th
                                            v-if="enabled3"
                                            class="do-not-print"
                                            scope="col"
                                            style="width: 0"
                                        >
                                            <div
                                                class="form-check custom-control"
                                            >
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    v-model="isCheckAll"
                                                    style="
                                                        width: 17px;
                                                        height: 17px;
                                                    "
                                                />
                                            </div>
                                        </th>
                                        <th>
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    $t("general.M")
                                                }}</span>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.full_name &&
                                                isVisible('full_name')
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    $t(
                                                        "general.NameMembershipApplicant"
                                                    )
                                                }}</span>
                                                <div class="arrow-sort">
                                                    <i
                                                        class="fas fa-arrow-up"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    'full_name'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                    <i
                                                        class="fas fa-arrow-down"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    '-full_name'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.birth_date &&
                                                isVisible('birth_date')
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    getCompanyKey(
                                                        "member_birth_date"
                                                    )
                                                }}</span>
                                                <div class="arrow-sort">
                                                    <i
                                                        class="fas fa-arrow-up"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    'birth_date'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                    <i
                                                        class="fas fa-arrow-down"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    '-birth_date'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.gender &&
                                                isVisible('gender')
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    getCompanyKey(
                                                        "member_gender"
                                                    )
                                                }}</span>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.member_type_id &&
                                                isVisible('member_type_id')
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    $t("general.status")
                                                }}</span>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.Subscription_receipt_number
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    getCompanyKey(
                                                        "Subscription_receipt_number"
                                                    )
                                                }}</span>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.national_id &&
                                                isVisible('national_id')
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    getCompanyKey(
                                                        "member_national_id"
                                                    )
                                                }}</span>
                                                <div class="arrow-sort">
                                                    <i
                                                        class="fas fa-arrow-up"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    'national_id'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                    <i
                                                        class="fas fa-arrow-down"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    '-national_id'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.nationality_number &&
                                                isVisible('nationality_number')
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    getCompanyKey(
                                                        "member_nationality_number"
                                                    )
                                                }}</span>
                                                <div class="arrow-sort">
                                                    <i
                                                        class="fas fa-arrow-up"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    'nationality_number'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                    <i
                                                        class="fas fa-arrow-down"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    '-nationality_number'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.sponsor_id &&
                                                isVisible('sponsor_id')
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    getCompanyKey("sponsor")
                                                }}</span>
                                                <div class="arrow-sort">
                                                    <i
                                                        class="fas fa-arrow-up"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    'name'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                    <i
                                                        class="fas fa-arrow-down"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    '-name'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.application_number &&
                                                isVisible('application_number')
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    $t(
                                                        "general.application_number"
                                                    )
                                                }}</span>
                                                <div class="arrow-sort">
                                                    <i
                                                        class="fas fa-arrow-up"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    'application_number'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                    <i
                                                        class="fas fa-arrow-down"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    '-application_number'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.home_phone &&
                                                isVisible('home_phone')
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    getCompanyKey(
                                                        "member_home_phone"
                                                    )
                                                }}</span>
                                                <div class="arrow-sort">
                                                    <i
                                                        class="fas fa-arrow-up"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    'home_phone'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                    <i
                                                        class="fas fa-arrow-down"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    '-home_phone'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.work_phone &&
                                                isVisible('work_phone')
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    getCompanyKey(
                                                        "member_work_phone"
                                                    )
                                                }}</span>
                                                <div class="arrow-sort">
                                                    <i
                                                        class="fas fa-arrow-up"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    'work_phone'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                    <i
                                                        class="fas fa-arrow-down"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    '-work_phone'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.home_address &&
                                                isVisible('home_address')
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    getCompanyKey(
                                                        "member_home_address"
                                                    )
                                                }}</span>
                                                <div class="arrow-sort">
                                                    <i
                                                        class="fas fa-arrow-up"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    'home_address'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                    <i
                                                        class="fas fa-arrow-down"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    '-home_address'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.work_address &&
                                                isVisible('work_address')
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    getCompanyKey(
                                                        "member_work_address"
                                                    )
                                                }}</span>
                                                <div class="arrow-sort">
                                                    <i
                                                        class="fas fa-arrow-up"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    'work_address'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                    <i
                                                        class="fas fa-arrow-down"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    '-work_address'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.job && isVisible('job')
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    getCompanyKey("member_job")
                                                }}</span>
                                                <div class="arrow-sort">
                                                    <i
                                                        class="fas fa-arrow-up"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    'job'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                    <i
                                                        class="fas fa-arrow-down"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    '-job'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.degree &&
                                                isVisible('degree')
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    getCompanyKey(
                                                        "member_degree"
                                                    )
                                                }}</span>
                                                <div class="arrow-sort">
                                                    <i
                                                        class="fas fa-arrow-up"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    'degree'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                    <i
                                                        class="fas fa-arrow-down"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    '-degree'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th
                                            v-if="
                                                setting.acceptance &&
                                                isVisible('acceptance')
                                            "
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <span>{{
                                                    getCompanyKey(
                                                        "member_acceptance"
                                                    )
                                                }}</span>
                                                <div class="arrow-sort">
                                                    <i
                                                        class="fas fa-arrow-up"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    'acceptance'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                    <i
                                                        class="fas fa-arrow-down"
                                                        @click="
                                                            members.sort(
                                                                sortString(
                                                                    '-acceptance'
                                                                )
                                                            )
                                                        "
                                                    ></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th
                                            v-if="enabled3"
                                            class="do-not-print"
                                        >
                                            {{ $t("general.Action") }}
                                        </th>
                                        <th
                                            v-if="enabled3"
                                            class="do-not-print"
                                        >
                                            <i class="fas fa-ellipsis-v"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody v-if="members.length > 0">
                                    <tr
                                        @click.capture="checkRow(data.id)"
                                        @dblclick.prevent="
                                            isPermission(
                                                'update memberRequest club'
                                            )
                                                ? $bvModal.show(
                                                      `modal-edit-${data.id}`
                                                  )
                                                : false
                                        "
                                        v-for="(data, index) in members"
                                        :key="data.id"
                                        class="body-tr-custom"
                                    >
                                        <td
                                            v-if="enabled3"
                                            class="do-not-print"
                                        >
                                            <div
                                                class="form-check custom-control"
                                                style="min-height: 1.9em"
                                            >
                                                <input
                                                    style="
                                                        width: 17px;
                                                        height: 17px;
                                                    "
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    :value="data.id"
                                                    v-model="checkAll"
                                                />
                                            </div>
                                        </td>
                                        <td>{{ index + 1 }}</td>
                                        <td
                                            v-if="
                                                setting.full_name &&
                                                isVisible('full_name')
                                            "
                                        >
                                            {{ data.full_name }}
                                        </td>
                                        <td
                                            v-if="
                                                setting.birth_date &&
                                                isVisible('birth_date')
                                            "
                                        >
                                            {{ data.birth_date }}
                                        </td>
                                        <td
                                            v-if="
                                                setting.gender &&
                                                isVisible('gender')
                                            "
                                        >
                                            {{
                                                data.gender == 1
                                                    ? $t("general.male")
                                                    : $t("general.female")
                                            }}
                                        </td>
                                        <td
                                            v-if="
                                                setting.member_type_id &&
                                                isVisible('member_type_id')
                                            "
                                        >
                                            {{
                                                parseInt(data.member_type_id) ==
                                                1
                                                    ? $t(
                                                          "general.pendingMember"
                                                      )
                                                    : $t("general.unacceptable")
                                            }}
                                        </td>
                                        <td
                                            v-if="
                                                setting.Subscription_receipt_number
                                            "
                                        >
                                            {{
                                                data.transaction
                                                    ? data.transaction.serial
                                                          .perfix +
                                                      "-" +
                                                      data.transaction
                                                          .document_no
                                                    : "---"
                                            }}
                                        </td>
                                        <td
                                            v-if="
                                                setting.national_id &&
                                                isVisible('national_id')
                                            "
                                        >
                                            {{ data.national_id }}
                                        </td>
                                        <td
                                            v-if="
                                                setting.nationality_number &&
                                                isVisible('nationality_number')
                                            "
                                        >
                                            {{ data.nationality_number }}
                                        </td>
                                        <td
                                            v-if="
                                                setting.sponsor_id &&
                                                isVisible('sponsor_id')
                                            "
                                        >
                                            {{
                                                data.sponsor
                                                    ? $i18n.locale == "ar"
                                                        ? data.sponsor.name
                                                        : data.sponsor.name_e
                                                    : "-"
                                            }}
                                        </td>
                                        <td
                                            v-if="
                                                setting.application_number &&
                                                isVisible('application_number')
                                            "
                                        >
                                            {{ data.application_number }}
                                        </td>
                                        <td
                                            v-if="
                                                setting.home_phone &&
                                                isVisible('home_phone')
                                            "
                                        >
                                            {{ data.home_phone }}
                                        </td>
                                        <td
                                            v-if="
                                                setting.work_phone &&
                                                isVisible('work_phone')
                                            "
                                        >
                                            {{ data.work_phone }}
                                        </td>
                                        <td
                                            v-if="
                                                setting.home_address &&
                                                isVisible('home_address')
                                            "
                                        >
                                            {{ data.home_address }}
                                        </td>
                                        <td
                                            v-if="
                                                setting.work_address &&
                                                isVisible('work_address')
                                            "
                                        >
                                            {{ data.work_address }}
                                        </td>
                                        <td
                                            v-if="
                                                setting.job && isVisible('job')
                                            "
                                        >
                                            {{ data.job }}
                                        </td>
                                        <td
                                            v-if="
                                                setting.degree &&
                                                isVisible('degree')
                                            "
                                        >
                                            {{ data.degree }}
                                        </td>
                                        <td
                                            v-if="
                                                setting.acceptance &&
                                                isVisible('acceptance')
                                            "
                                        >
                                            <span class="text-success">
                                                <template
                                                    v-if="
                                                        data.acceptance == '0'
                                                    "
                                                >
                                                    {{ $t("general.pending") }}
                                                </template>
                                                <template
                                                    v-else-if="
                                                        data.acceptance == '1'
                                                    "
                                                >
                                                    {{ $t("general.accepted") }}
                                                </template>
                                                <template
                                                    v-else="
                                                        data.acceptance == '2'
                                                    "
                                                >
                                                    {{ $t("general.declined") }}
                                                </template>
                                            </span>
                                        </td>
                                        <td
                                            v-if="enabled3"
                                            class="do-not-print"
                                        >
                                            <div class="btn-group">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                    data-toggle="dropdown"
                                                    aria-expanded="false"
                                                >
                                                    {{ $t("general.commands") }}
                                                    <i
                                                        class="fas fa-angle-down"
                                                    ></i>
                                                </button>
                                                <div
                                                    class="dropdown-menu dropdown-menu-custom"
                                                >
                                                    <a
                                                        class="dropdown-item"
                                                        href="#"
                                                        v-if="
                                                            isPermission(
                                                                'update memberRequest club'
                                                            )
                                                        "
                                                        @click="
                                                            $bvModal.show(
                                                                `modal-edit-${data.id}`
                                                            )
                                                        "
                                                    >
                                                        <div
                                                            class="d-flex justify-content-between align-items-center text-black"
                                                        >
                                                            <span>{{
                                                                $t(
                                                                    "general.edit"
                                                                )
                                                            }}</span>
                                                            <i
                                                                class="mdi mdi-square-edit-outline text-info"
                                                            ></i>
                                                        </div>
                                                    </a>
                                                    <a
                                                        class="dropdown-item text-black"
                                                        href="#"
                                                        v-if="
                                                            isPermission(
                                                                'delete memberRequest club'
                                                            )
                                                        "
                                                        @click.prevent="
                                                            deleteBranch(
                                                                data.id
                                                            )
                                                        "
                                                    >
                                                        <div
                                                            class="d-flex justify-content-between align-items-center text-black"
                                                        >
                                                            <span>{{
                                                                $t(
                                                                    "general.delete"
                                                                )
                                                            }}</span>
                                                            <i
                                                                class="fas fa-times text-danger"
                                                            ></i>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>

                                            <!--  edit   -->
                                            <b-modal
                                                dialog-class="modal-full-width"
                                                :id="`modal-edit-${data.id}`"
                                                :title="
                                                    getCompanyKey(
                                                        'member_request_edit_form'
                                                    )
                                                "
                                                title-class="font-18"
                                                body-class="p-4"
                                                :ref="`edit-${data.id}`"
                                                :hide-footer="true"
                                                @show="resetModalEdit(data.id)"
                                                @hidden="
                                                    resetModalHiddenEdit(
                                                        data.id
                                                    )
                                                "
                                            >
                                                <form>
                                                    <div
                                                        class="mb-3 d-flex justify-content-end"
                                                    >
                                                        <!-- Emulate built in modal footer ok and cancel button actions -->
                                                        <b-button
                                                            @click="
                                                                showSubscriptionModal
                                                            "
                                                            variant="primary"
                                                            :disabled="
                                                                data.transaction
                                                            "
                                                            type="button"
                                                            v-print="
                                                                '#printInv'
                                                            "
                                                            :class="[
                                                                'font-weight-bold px-2 mx-2',
                                                            ]"
                                                        >
                                                            {{
                                                                $t(
                                                                    "general.addSubscription"
                                                                )
                                                            }}
                                                        </b-button>
                                                        <b-button
                                                            variant="success"
                                                            type="submit"
                                                            class="mx-1"
                                                            v-if="!isLoader"
                                                            @click.prevent="
                                                                editSubmit(
                                                                    data.id
                                                                )
                                                            "
                                                        >
                                                            {{
                                                                $t(
                                                                    "general.Edit"
                                                                )
                                                            }}
                                                        </b-button>

                                                        <b-button
                                                            variant="success"
                                                            class="mx-1"

                                                            v-else
                                                        >
                                                            <b-spinner
                                                                small
                                                            ></b-spinner>
                                                            <span
                                                                class="sr-only"
                                                                >{{
                                                                    $t(
                                                                        "login.Loading"
                                                                    )
                                                                }}...</span
                                                            >
                                                        </b-button>

                                                        <b-button
                                                            variant="danger"
                                                            type="button"
                                                            @click.prevent="
                                                                $bvModal.hide(
                                                                    `modal-edit-${data.id}`
                                                                )
                                                            "
                                                        >
                                                            {{
                                                                $t(
                                                                    "general.Cancel"
                                                                )
                                                            }}
                                                        </b-button>
                                                    </div>
                                                    <div class="row">
                                                        <div
                                                            class="col-md-3 position-relative"
                                                            v-if="
                                                                isVisible(
                                                                    'applying_date'
                                                                )
                                                            "
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "apply_membership_date"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'applying_date'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <date-picker
                                                                    type="date"
                                                                    v-model="
                                                                        $v.edit
                                                                            .applying_date
                                                                            .$model
                                                                    "
                                                                    format="YYYY-MM-DD"
                                                                    valueType="format"
                                                                    :confirm="
                                                                        false
                                                                    "
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v
                                                                                .edit
                                                                                .applying_date
                                                                                .$error ||
                                                                            errors.applying_date,
                                                                        'is-valid':
                                                                            !$v
                                                                                .edit
                                                                                .applying_date
                                                                                .$invalid &&
                                                                            !errors.applying_date,
                                                                    }"
                                                                ></date-picker>
                                                                <template
                                                                    v-if="
                                                                        errors.applying_date
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.applying_date"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-md-3"
                                                            v-if="
                                                                isVisible(
                                                                    'applying_number'
                                                                )
                                                            "
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "apply_membership_number"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'applying_number'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <input
                                                                    :disabled="
                                                                        true
                                                                    "
                                                                    v-model="
                                                                        edit.applying_number
                                                                    "
                                                                    class="form-control"
                                                                    type="number"
                                                                    :class="{
                                                                        'is-invalid':
                                                                            errors.applying_number,
                                                                        'is-valid':
                                                                            !errors.applying_number,
                                                                    }"
                                                                />
                                                                <template
                                                                    v-if="
                                                                        errors.applying_number
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.applying_number"
                                                                        :key="
                                                                            index
                                                                        "
                                                                    >
                                                                        {{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-md-3"
                                                            v-if="
                                                                isVisible(
                                                                    'application_number'
                                                                )
                                                            "
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        $t(
                                                                            "general.application_number"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'application_number'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <input
                                                                    v-model="
                                                                        edit.application_number
                                                                    "
                                                                    class="form-control"
                                                                    type="number"
                                                                    :class="{
                                                                        'is-invalid':
                                                                            errors.application_number,
                                                                        'is-valid':
                                                                            !errors.application_number,
                                                                    }"
                                                                />
                                                                <template
                                                                    v-if="
                                                                        errors.application_number
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.application_number"
                                                                        :key="
                                                                            index
                                                                        "
                                                                    >
                                                                        {{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-md-3"
                                                            v-if="
                                                                isVisible(
                                                                    'sponsor_id'
                                                                )
                                                            "
                                                        >
                                                            <div
                                                                class="form-group position-relative"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "sponsor"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'sponsor_id'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <multiselect
                                                                    v-model="
                                                                        edit.sponsor_id
                                                                    "
                                                                    :options="
                                                                        sponsors.map(
                                                                            (
                                                                                type
                                                                            ) =>
                                                                                type.id
                                                                        )
                                                                    "
                                                                    :custom-label="
                                                                        (opt) =>
                                                                            sponsors.find(
                                                                                (
                                                                                    x
                                                                                ) =>
                                                                                    x.id ==
                                                                                    opt
                                                                            )
                                                                                ? $i18n.locale ==
                                                                                  'ar'
                                                                                    ? sponsors.find(
                                                                                          (
                                                                                              x
                                                                                          ) =>
                                                                                              x.id ==
                                                                                              opt
                                                                                      )
                                                                                          .name
                                                                                    : sponsors.find(
                                                                                          (
                                                                                              x
                                                                                          ) =>
                                                                                              x.id ==
                                                                                              opt
                                                                                      )
                                                                                          .name_e
                                                                                : null
                                                                    "
                                                                >
                                                                </multiselect>
                                                                <div
                                                                    v-if="
                                                                        $v.edit
                                                                            .sponsor_id
                                                                            .$error ||
                                                                        errors.sponsor_id
                                                                    "
                                                                    class="text-danger"
                                                                >
                                                                    {{
                                                                        $t(
                                                                            "general.fieldIsRequired"
                                                                        )
                                                                    }}
                                                                </div>
                                                                <template
                                                                    v-if="
                                                                        errors.sponsor_id
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.sponsor_id"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr
                                                        style="
                                                            margin: 10px 0 !important;
                                                            border-top: 1px
                                                                solid
                                                                rgb(
                                                                    141 163 159 /
                                                                        42%
                                                                );
                                                        "
                                                        v-if="
                                                            isVisible(
                                                                'first_name'
                                                            ) ||
                                                            isVisible(
                                                                'second_name'
                                                            ) ||
                                                            isVisible(
                                                                'third_name'
                                                            ) ||
                                                            isVisible(
                                                                'last_name'
                                                            ) ||
                                                            isVisible(
                                                                'family_name'
                                                            )
                                                        "
                                                    />
                                                    <div class="row">
                                                        <div
                                                            v-if="
                                                                isVisible(
                                                                    'first_name'
                                                                )
                                                            "
                                                            class="col-md-2"
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "member_first_name"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'first_name'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <input
                                                                    v-model="
                                                                        $v.edit
                                                                            .first_name
                                                                            .$model
                                                                    "
                                                                    class="form-control"
                                                                    type="text"
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v
                                                                                .edit
                                                                                .first_name
                                                                                .$error ||
                                                                            errors.first_name,
                                                                        'is-valid':
                                                                            !$v
                                                                                .edit
                                                                                .first_name
                                                                                .$invalid &&
                                                                            !errors.first_name,
                                                                    }"
                                                                />
                                                                <template
                                                                    v-if="
                                                                        errors.first_name
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.first_name"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div
                                                            v-if="
                                                                isVisible(
                                                                    'second_name'
                                                                )
                                                            "
                                                            class="col-md-2"
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "member_second_name"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'second_name'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <input
                                                                    v-model="
                                                                        $v.edit
                                                                            .second_name
                                                                            .$model
                                                                    "
                                                                    class="form-control"
                                                                    type="text"
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v
                                                                                .edit
                                                                                .second_name
                                                                                .$error ||
                                                                            errors.second_name,
                                                                        'is-valid':
                                                                            !$v
                                                                                .edit
                                                                                .second_name
                                                                                .$invalid &&
                                                                            !errors.second_name,
                                                                    }"
                                                                />
                                                                <template
                                                                    v-if="
                                                                        errors.second_name
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.second_name"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div
                                                            v-if="
                                                                isVisible(
                                                                    'third_name'
                                                                )
                                                            "
                                                            class="col-md-2"
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "member_third_name"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'third_name'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <input
                                                                    v-model="
                                                                        $v.edit
                                                                            .third_name
                                                                            .$model
                                                                    "
                                                                    class="form-control"
                                                                    type="text"
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v
                                                                                .edit
                                                                                .third_name
                                                                                .$error ||
                                                                            errors.third_name,
                                                                        'is-valid':
                                                                            !$v
                                                                                .edit
                                                                                .third_name
                                                                                .$invalid &&
                                                                            !errors.third_name,
                                                                    }"
                                                                />
                                                                <template
                                                                    v-if="
                                                                        errors.third_name
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.third_name"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div
                                                            v-if="
                                                                isVisible(
                                                                    'last_name'
                                                                )
                                                            "
                                                            class="col-md-2"
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "member_last_name"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'last_name'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <input
                                                                    v-model="
                                                                        $v.edit
                                                                            .last_name
                                                                            .$model
                                                                    "
                                                                    class="form-control"
                                                                    type="text"
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v
                                                                                .edit
                                                                                .last_name
                                                                                .$error ||
                                                                            errors.last_name,
                                                                        'is-valid':
                                                                            !$v
                                                                                .edit
                                                                                .last_name
                                                                                .$invalid &&
                                                                            !errors.last_name,
                                                                    }"
                                                                />
                                                                <template
                                                                    v-if="
                                                                        errors.last_name
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.third_name"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div
                                                            v-if="
                                                                isVisible(
                                                                    'family_name'
                                                                )
                                                            "
                                                            class="col-md-2"
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "member_family_name"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'family_name'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <input
                                                                    v-model="
                                                                        $v.edit
                                                                            .family_name
                                                                            .$model
                                                                    "
                                                                    class="form-control"
                                                                    type="text"
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v
                                                                                .edit
                                                                                .family_name
                                                                                .$error ||
                                                                            errors.family_name,
                                                                        'is-valid':
                                                                            !$v
                                                                                .edit
                                                                                .family_name
                                                                                .$invalid &&
                                                                            !errors.family_name,
                                                                    }"
                                                                />
                                                                <template
                                                                    v-if="
                                                                        errors.family_name
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.family_name"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr
                                                        style="
                                                            margin: 10px 0 !important;
                                                            border-top: 1px
                                                                solid
                                                                rgb(
                                                                    141 163 159 /
                                                                        42%
                                                                );
                                                        "
                                                        v-if="
                                                            isVisible(
                                                                'gender'
                                                            ) ||
                                                            isVisible(
                                                                'national_id'
                                                            ) ||
                                                            isVisible(
                                                                'nationality_number'
                                                            ) ||
                                                            isVisible(
                                                                'birth_date'
                                                            )
                                                        "
                                                    />
                                                    <div class="row">
                                                        <div
                                                            v-if="
                                                                isVisible(
                                                                    'birth_date'
                                                                )
                                                            "
                                                            class="col-md-3 position-relative"
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "member_birth_date"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'birth_date'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <date-picker
                                                                    type="date"
                                                                    v-model="
                                                                        $v.edit
                                                                            .birth_date
                                                                            .$model
                                                                    "
                                                                    format="YYYY-MM-DD"
                                                                    valueType="format"
                                                                    :confirm="
                                                                        false
                                                                    "
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v
                                                                                .edit
                                                                                .birth_date
                                                                                .$error ||
                                                                            errors.birth_date,
                                                                        'is-valid':
                                                                            !$v
                                                                                .edit
                                                                                .birth_date
                                                                                .$invalid &&
                                                                            !errors.birth_date,
                                                                    }"
                                                                ></date-picker>

                                                                <template
                                                                    v-if="
                                                                        errors.date
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.date"
                                                                        :key="
                                                                            index
                                                                        "
                                                                    >
                                                                        {{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div
                                                            v-if="
                                                                isVisible(
                                                                    'gender'
                                                                )
                                                            "
                                                            class="col-md-3"
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label mr-2"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "member_gender"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'gender'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <b-form-group
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v
                                                                                .edit
                                                                                .gender
                                                                                .$error ||
                                                                            errors.gender,
                                                                        'is-valid':
                                                                            !$v
                                                                                .edit
                                                                                .gender
                                                                                .$invalid &&
                                                                            !errors.gender,
                                                                    }"
                                                                >
                                                                    <b-form-radio
                                                                        class="d-inline-block"
                                                                        v-model="
                                                                            $v
                                                                                .edit
                                                                                .gender
                                                                                .$model
                                                                        "
                                                                        name="edit_gender"
                                                                        value="1"
                                                                        >{{
                                                                            $t(
                                                                                "general.male"
                                                                            )
                                                                        }}
                                                                    </b-form-radio>
                                                                    <b-form-radio
                                                                        class="d-inline-block m-1"
                                                                        v-model="
                                                                            $v
                                                                                .edit
                                                                                .gender
                                                                                .$model
                                                                        "
                                                                        name="edit_gender"
                                                                        value="0"
                                                                        >{{
                                                                            $t(
                                                                                "general.female"
                                                                            )
                                                                        }}
                                                                    </b-form-radio>
                                                                </b-form-group>
                                                                <template
                                                                    v-if="
                                                                        errors.gender
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.gender"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div
                                                            v-if="
                                                                isVisible(
                                                                    'national_id'
                                                                )
                                                            "
                                                            class="col-md-3"
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "member_national_id"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'national_id'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <input
                                                                    v-model="
                                                                        $v.edit
                                                                            .national_id
                                                                            .$model
                                                                    "
                                                                    class="form-control"
                                                                    type="number"
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v
                                                                                .edit
                                                                                .national_id
                                                                                .$error ||
                                                                            errors.national_id,
                                                                        'is-valid':
                                                                            !$v
                                                                                .edit
                                                                                .national_id
                                                                                .$invalid &&
                                                                            !errors.national_id,
                                                                    }"
                                                                />
                                                                <template
                                                                    v-if="
                                                                        errors.national_id
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.national_id"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div
                                                            v-if="
                                                                isVisible(
                                                                    'nationality_number'
                                                                )
                                                            "
                                                            class="col-md-3"
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "member_nationality_number"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'nationality_number'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <input
                                                                    v-model="
                                                                        $v.edit
                                                                            .nationality_number
                                                                            .$model
                                                                    "
                                                                    class="form-control"
                                                                    type="number"
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v
                                                                                .edit
                                                                                .nationality_number
                                                                                .$error ||
                                                                            errors.nationality_number,
                                                                        'is-valid':
                                                                            !$v
                                                                                .edit
                                                                                .nationality_number
                                                                                .$invalid &&
                                                                            !errors.nationality_number,
                                                                    }"
                                                                />
                                                                <template
                                                                    v-if="
                                                                        errors.nationality_number
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.nationality_number"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr
                                                        style="
                                                            margin: 10px 0 !important;
                                                            border-top: 1px
                                                                solid
                                                                rgb(
                                                                    141 163 159 /
                                                                        42%
                                                                );
                                                        "
                                                        v-if="
                                                            isVisible(
                                                                'home_phone'
                                                            ) ||
                                                            isVisible(
                                                                'home_address'
                                                            ) ||
                                                            isVisible(
                                                                'work_phone'
                                                            ) ||
                                                            isVisible(
                                                                'work_address'
                                                            )
                                                        "
                                                    />
                                                    <div class="row">
                                                        <div
                                                            v-if="
                                                                isVisible(
                                                                    'home_phone'
                                                                )
                                                            "
                                                            class="col-md-3"
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "member_home_phone"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'home_phone'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <VuePhoneNumberInput
                                                                    v-model="
                                                                        $v.edit
                                                                            .home_phone
                                                                            .$model
                                                                    "
                                                                    :default-country-code="
                                                                        edit.phone_code
                                                                    "
                                                                    valid-color="#28a745"
                                                                    error-color="#dc3545"
                                                                    @update="
                                                                        updatePhoneEdit
                                                                    "
                                                                    :preferred-countries="[
                                                                        'FR',
                                                                        'EG',
                                                                        'DE',
                                                                    ]"
                                                                />
                                                                <template
                                                                    v-if="
                                                                        errors.home_phone
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.home_phone"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <di
                                                            v-if="
                                                                isVisible(
                                                                    'home_address'
                                                                )
                                                            "
                                                            class="col-md-3"
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "member_home_address"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'home_address'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <input
                                                                    v-model="
                                                                        $v.edit
                                                                            .home_address
                                                                            .$model
                                                                    "
                                                                    class="form-control"
                                                                    type="text"
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v
                                                                                .edit
                                                                                .home_address
                                                                                .$error ||
                                                                            errors.home_address,
                                                                        'is-valid':
                                                                            !$v
                                                                                .edit
                                                                                .home_address
                                                                                .$invalid &&
                                                                            !errors.home_address,
                                                                    }"
                                                                />
                                                                <template
                                                                    v-if="
                                                                        errors.home_address
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.home_address"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </di>
                                                        <div
                                                            v-if="
                                                                isVisible(
                                                                    'work_phone'
                                                                )
                                                            "
                                                            class="col-md-3"
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "member_work_phone"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'work_phone'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <VuePhoneNumberInput
                                                                    v-model="
                                                                        $v.edit
                                                                            .work_phone
                                                                            .$model
                                                                    "
                                                                    :default-country-code="
                                                                        edit.phone_code
                                                                    "
                                                                    valid-color="#28a745"
                                                                    error-color="#dc3545"
                                                                    @update="
                                                                        updatePhoneEdit
                                                                    "
                                                                    :preferred-countries="[
                                                                        'FR',
                                                                        'EG',
                                                                        'DE',
                                                                    ]"
                                                                />
                                                                <template
                                                                    v-if="
                                                                        errors.work_phone
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.work_phone"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div
                                                            v-if="
                                                                isVisible(
                                                                    'work_address'
                                                                )
                                                            "
                                                            class="col-md-3"
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "member_work_address"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'work_address'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <input
                                                                    v-model="
                                                                        $v.edit
                                                                            .work_address
                                                                            .$model
                                                                    "
                                                                    class="form-control"
                                                                    type="text"
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v
                                                                                .edit
                                                                                .work_address
                                                                                .$error ||
                                                                            errors.work_address,
                                                                        'is-valid':
                                                                            !$v
                                                                                .edit
                                                                                .work_address
                                                                                .$invalid &&
                                                                            !errors.work_address,
                                                                    }"
                                                                />
                                                                <template
                                                                    v-if="
                                                                        errors.work_address
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.work_address"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr
                                                        style="
                                                            margin: 10px 0 !important;
                                                            border-top: 1px
                                                                solid
                                                                rgb(
                                                                    141 163 159 /
                                                                        42%
                                                                );
                                                        "
                                                        v-if="
                                                            isVisible('job') ||
                                                            isVisible(
                                                                'degree'
                                                            ) ||
                                                            isVisible(
                                                                'member_type_id'
                                                            )
                                                        "
                                                    />
                                                    <div class="row">
                                                        <div
                                                            v-if="
                                                                isVisible('job')
                                                            "
                                                            class="col-md-3"
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "member_job"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'job'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <input
                                                                    v-model="
                                                                        $v.edit
                                                                            .job
                                                                            .$model
                                                                    "
                                                                    class="form-control"
                                                                    type="text"
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v
                                                                                .edit
                                                                                .job
                                                                                .$error ||
                                                                            errors.job,
                                                                        'is-valid':
                                                                            !$v
                                                                                .edit
                                                                                .job
                                                                                .$invalid &&
                                                                            !errors.job,
                                                                    }"
                                                                />
                                                                <template
                                                                    v-if="
                                                                        errors.job
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.job"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div
                                                            v-if="
                                                                isVisible(
                                                                    'degree'
                                                                )
                                                            "
                                                            class="col-md-3"
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        getCompanyKey(
                                                                            "member_degree"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'degree'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <input
                                                                    v-model="
                                                                        $v.edit
                                                                            .degree
                                                                            .$model
                                                                    "
                                                                    class="form-control"
                                                                    type="text"
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v
                                                                                .edit
                                                                                .degree
                                                                                .$error ||
                                                                            errors.degree,
                                                                        'is-valid':
                                                                            !$v
                                                                                .edit
                                                                                .degree
                                                                                .$invalid &&
                                                                            !errors.degree,
                                                                    }"
                                                                />
                                                                <template
                                                                    v-if="
                                                                        errors.degree
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.degree"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-md-3"
                                                            v-if="
                                                                isVisible(
                                                                    'member_type_id'
                                                                )
                                                            "
                                                        >
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label
                                                                    class="control-label"
                                                                >
                                                                    {{
                                                                        $t(
                                                                            "general.status"
                                                                        )
                                                                    }}
                                                                    <span
                                                                        v-if="
                                                                            isRequired(
                                                                                'member_type_id'
                                                                            )
                                                                        "
                                                                        class="text-danger"
                                                                        >*</span
                                                                    >
                                                                </label>
                                                                <select
                                                                    :disabled="
                                                                        true
                                                                    "
                                                                    class="form-control"
                                                                    v-model="
                                                                        edit.member_type_id
                                                                    "
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v
                                                                                .edit
                                                                                .member_type_id
                                                                                .$error ||
                                                                            errors.member_type_id,
                                                                        'is-valid':
                                                                            !$v
                                                                                .edit
                                                                                .member_type_id
                                                                                .$invalid &&
                                                                            !errors.member_type_id,
                                                                    }"
                                                                >
                                                                    <option
                                                                        value="1"
                                                                    >
                                                                        {{
                                                                            $t(
                                                                                "general.pendingMember"
                                                                            )
                                                                        }}
                                                                    </option>
                                                                    <option
                                                                        value="2"
                                                                    >
                                                                        {{
                                                                            $t(
                                                                                "general.unacceptable"
                                                                            )
                                                                        }}
                                                                    </option>
                                                                </select>

                                                                <template
                                                                    v-if="
                                                                        errors.member_type_id
                                                                    "
                                                                >
                                                                    <ErrorMessage
                                                                        v-for="(
                                                                            errorMessage,
                                                                            index
                                                                        ) in errors.member_type_id"
                                                                        :key="
                                                                            index
                                                                        "
                                                                        >{{
                                                                            errorMessage
                                                                        }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr
                                                        style="
                                                            margin: 10px 0 !important;
                                                            border-top: 1px
                                                                solid
                                                                rgb(
                                                                    141 163 159 /
                                                                        42%
                                                                );
                                                        "
                                                        v-if="transaction"
                                                    />
                                                    <div
                                                        v-if="transaction"
                                                        class="row"
                                                    >
                                                        <div class="col-md-3">
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label>{{
                                                                    getCompanyKey(
                                                                        "Subscription_receipt_number"
                                                                    )
                                                                }}</label>
                                                                <input

                                                                    v-model="
                                                                        transaction.document_no
                                                                    "
                                                                    class="form-control"
                                                                    type="text"
                                                                />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label>{{
                                                                    $t(
                                                                        "general.serial_number"
                                                                    )
                                                                }}</label>
                                                                <input

                                                                    class="form-control"
                                                                    type="text"
                                                                    :value="
                                                                        transaction
                                                                            .serial
                                                                            .perfix +
                                                                        '-' +
                                                                        transaction.document_no
                                                                    "
                                                                />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label>{{
                                                                    $t(
                                                                        "general.forYear"
                                                                    )
                                                                }}</label>
                                                                <input

                                                                    v-model="
                                                                        transaction.year
                                                                    "
                                                                    class="form-control"
                                                                    type="text"
                                                                />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div
                                                                class="form-group"
                                                            >
                                                                <label>{{
                                                                    $t(
                                                                        "general.amount"
                                                                    )
                                                                }}</label>
                                                                <input

                                                                    v-model="
                                                                        transaction.amount
                                                                    "
                                                                    class="form-control"
                                                                    type="text"
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </b-modal>
                                            <!--  /edit   -->
                                        </td>
                                        <td
                                            v-if="enabled3"
                                            class="do-not-print"
                                        >
                                            <button
                                                @mouseover="log(data.id)"
                                                @mousemove="log(data.id)"
                                                type="button"
                                                class="btn"
                                                data-toggle="tooltip"
                                                :data-placement="
                                                    $i18n.locale == 'en'
                                                        ? 'left'
                                                        : 'right'
                                                "
                                                :title="Tooltip"
                                            >
                                                <i
                                                    class="fe-info"
                                                    style="font-size: 22px"
                                                ></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <th class="text-center" colspan="30">
                                            {{ $t("general.notDataFound") }}
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- end .table-responsive-->
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style>

@media print {

    table.table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid black;
        font-size: 20px;
    }


    table.table thead {
        display: table-header-group;
        background-color: #f5f5f5;
        color: black;
        text-align: center;
        font-weight: bold;
        border-bottom: 2px solid black;
    }


    table.table tbody tr {
        page-break-inside: avoid;
    }

    table.table th,
    table.table td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
        font-weight: bold;
    }


    table.table-hover tbody tr:hover {
        background-color: transparent;
    }

    table.table-centered td,
    table.table-centered th {
        text-align: center;
        vertical-align: middle;
    }


    table.table-borderless td,
    table.table-borderless th {
        border: 1px solid black;
    }

    body {
        margin: 0;
        padding: 0;
    }

    .data-header-print {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    margin-bottom: 10px;
    margin-left: 30px;
    background-color: white;
    padding: 10px 0;
    border-bottom: 2px solid black; /* Adds a clear visual separation */
}

.data-header-print h2 {
    margin: 5px 0; /* Adjust spacing between headings */
}

.data-header-print .row {
    margin-top: 10px; /* Add some space above the sponsor section */
}

.data-header-print ul {
    padding-left: 20px;
    margin: 5px 0;
}

.data-header-print li {
    font-size: 12px; /* Adjust font size for better readability */
}

    /* Additional Styling for RTL */
    .dir-print-rtl {
        direction: rtl;
        text-align: right;
    }

    .dir-print-ltr {
        direction: ltr;
        text-align: left;
    }
}
</style>
