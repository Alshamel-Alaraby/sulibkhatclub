<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import adminApi from "../../../../api/adminAxios";
import Switches from "vue-switches";
import Multiselect from "vue-multiselect";
import permissionGuard from "../../../../helper/permission";

import Swal from "sweetalert2";
import ErrorMessage from "../../../../components/widgets/errorMessage";
import loader from "../../../../components/general/loader";
import DatePicker from "vue2-datepicker";
import {
    dynamicSortString,
    dynamicSortNumber,
} from "../../../../helper/tableSort";
import translation from "../../../../helper/mixin/translation-mixin";
import { formatDateOnly } from "../../../../helper/startDate";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Daily Sales Income Report",
        meta: [{ name: "Daily Sales Income Report", content: "Daily Sales Income Report" }],
    },
    mixins: [translation],
    components: {
        Multiselect,
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        DatePicker
    },
    data() {
        return {
            member_type_id: null,
            date_from: this.formatDate(new Date()),
            date_to: this.formatDate(new Date()),
            member_types: [],
            per_page: 50,
            search: "",
            debounce: {},
            enabled3: true,
            itemsPagination: {},
            progress: 0,
            items: [],
            isLoader: false,
            Tooltip: "",
            mouseEnter: "",
            fields: [],
            company_id: null,
            errors: {},
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            setting: {
                name: true,
                name_e: true,
            },
            is_disabled: false,
            filterSetting: ["name"],
            printLoading: true,
            printObj: {
                id: "printData",
            },
        };
    },
    validations: {},
    watch: {
        /**
         * watch per_page
         */
        per_page(after, befour) {
            this.getData();
        },
        /**
         * watch search
         */
        search(after, befour) {
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.getData();
            }, 400);
        },
        /**
         * watch check All table
         */
        isCheckAll(after, befour) {
            if (after) {
                this.items.forEach((el) => {
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
        this.getMemberTypes();
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Daily Sales Income Report", "all Daily Sales Income Report");
        });
    },
    methods: {
        formatDate(value) {
            return formatDateOnly(value);
        },
        getData(page = 1) {
            this.isLoader = true;
            let filter = "";
            for (let i = 0; i < this.filterSetting.length; ++i) {
                filter += `columns[${i}]=${this.filterSetting[i]}&`;
            }
            adminApi
                .get(
                    `/booking/report/sales-income?page=${page}&per_page=${this.per_page}&branch_id=${this.member_type_id}&date_from=${this.date_from}&date_to=${this.date_to}`
                )
                .then((res) => {
                    let l = res.data;
                    this.items = l.data;
                    // this.itemsPagination = l.pagination;
                    // this.current_page = l.pagination.current_page;
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
                this.current_page <= this.itemsPagination.last_page &&
                this.current_page != this.itemsPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let filter = "";
                for (let i = 0; i < this.filterSetting.length; ++i) {
                    filter += `columns[${i}]=${this.filterSetting[i]}&`;
                }

                adminApi
                    .get(
                        `/booking/report/sales-income?page=${this.current_page}&per_page=${this.per_page}&branch_id=${this.member_type_id}&date_from=${this.date_from}&date_to=${this.date_to}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.items = l.data;
                        this.itemsPagination = l.pagination;
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
            }
        },
        sortString(value) {
            return dynamicSortString(value);
        },
        SortNumber(value) {
            return dynamicSortNumber(value);
        },
        checkRow(id) {
            if (!this.checkAll.includes(id)) {
                this.checkAll.push(id);
            } else {
                let index = this.checkAll.indexOf(id);
                this.checkAll.splice(index, 1);
            }
        },
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
                if (dl) {
                    XLSX.write(wb, { bookType: type, bookSST: true, type: "base64" });
                } else {
                    XLSX.writeFile(
                        wb,
                        fn || ("Branch" + "." || "SheetJSTableExport.") + (type || "xlsx")
                    );
                }
                this.enabled3 = true;
            }, 100);
        },
        getMemberTypes() {
            adminApi
                .get(`/branches?notParent=1`)
                .then((res) => {
                    this.member_types = res.data.data;
                    if(res.data.data.length == 1){
                        this.member_type_id = res.data.data[0].id;
                        this.getData();
                    }
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })

        },

        sumTotal(data) {
            let total = 0 ;

            data.forEach((e) => {
                total += parseFloat(e.amount)

            });

            return total ;

        },
        roomsReceiptsTotal() {
            let total = 0 ;
            this.items.forEach((e) => {
                total += parseFloat(this.sumTotal(e.voucher_header));
            });
            return total ;
        }
    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">
                                {{ $t("general.DailySalesIncomeReport") }}
                            </h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <!-- <b-dropdown
                                      variant="primary"
                                      :text="$t('general.searchSetting')"
                                      ref="dropdown"
                                      class="btn-block setting-search"
                                    >
                                      <b-form-checkbox
                                        v-model="filterSetting"
                                        value="id"
                                        class="mb-1"
                                      >
                                        {{ $t("general.id") }}
                                      </b-form-checkbox>
                                      <b-form-checkbox
                                        v-model="filterSetting"
                                        value="path"
                                        class="mb-1"
                                      >
                                        {{ $t("general.path") }}
                                      </b-form-checkbox>
                                      <b-form-checkbox
                                        v-model="filterSetting"
                                        value="created_at"
                                        class="mb-1"
                                      >
                                        {{ $t("general.created_at") }}
                                      </b-form-checkbox>
                                    </b-dropdown> -->
                                    <!-- Basic dropdown -->
                                </div>

                                <div
                                    class="d-inline-block position-relative"
                                    style="width: 77%"
                                >
                                    <div class="form-group position-relative"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end search -->

                        <div
                            class="row justify-content-between align-items-center mb-2 px-1"
                        >
                            <div class="col-md-3 d-flex align-items-center mb-1 mt-2 mb-xl-0">
                                <div style="width: 100%">
                                    <multiselect
                                        @input="getData(1)"
                                        v-model="member_type_id"
                                        :options="member_types.map((type) => type.id)"
                                        :custom-label="
                              (opt) => member_types.find((x) => x.id == opt)?
                                $i18n.locale == 'ar'
                                  ? member_types.find((x) => x.id == opt).name
                                  : member_types.find((x) => x.id == opt).name_e: null
                            "
                                    >
                                    </multiselect>
                                </div>
                                <!-- start create and printer -->
                                <!-- <b-button
                                  v-b-modal.progress
                                  variant="primary"
                                  class="btn-sm mx-1 font-weight-bold"
                                >
                                  {{ $t("general.Create") }}
                                  <i class="fas fa-plus"></i>
                                </b-button> -->
                                <div class="d-inline-flex">
                                    <button
                                        style="margin: 0 15px"
                                        @click="ExportExcel('xlsx')"
                                        class="custom-btn-dowonload"
                                    >
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printData'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <button
                                        class="custom-btn-dowonload"
                                        @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                                        v-if="checkAll.length == 1"
                                    >
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                </div>
                                <!-- end create and printer -->
                            </div>

                            <div class="col-md-9  d-flex align-items-center justify-content-center">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.fromDate') }}
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="date_from"
                                                format="YYYY-MM-DD"
                                                valueType="format"
                                                :confirm="false"
                                            ></date-picker>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.toDate') }}
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="date_to"
                                                format="YYYY-MM-DD"
                                                valueType="format"
                                                :confirm="false"
                                            ></date-picker>
                                        </div>
                                    </div>
                                    <div class="col-mb-2 mt-3">
                                        <b-button
                                            variant="success"
                                            type="button" class="mx-1"
                                            v-if="!isLoader"
                                            @click.prevent="getData"
                                        >
                                            {{ $t('general.Search') }}
                                        </b-button>
                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                        </b-button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- start .table-responsive-->
                        <div ref="exportable_table" id="printData">
                            <div class="head-branch text-center">
                                <h2>
                                    DIVONA HOTAL
                                    <span v-if="member_types.find(el => el.id == member_type_id)">
                                        <template v-if="member_types.find(el => el.id == member_type_id).media">
                                            <img :src="member_types.find(el => el.id == member_type_id).media[0].url" />
                                        </template>
                                    </span>
                                    فندق ديفونا
                                </h2>
                                <p class="font-weight-bold">-Daily Sales Income Report-</p>
                            </div>

                            <div class="table-responsive mb-3 custom-table-theme position-relative"
                                v-if="items.length > 0"
                            >
                                <!--       start loader       -->
                                <loader size="large" v-if="isLoader" />
                                <!--       end loader       -->
                                <table
                                    v-for="(data, indexP) in items"
                                    :key="data.id"
                                    class="table table-borderless table-hover table-centered m-0"
                                >
                                    <thead>
                                    <tr>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{ $t("general.paymentMethod") }}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{$t('general.Date')}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{$t('general.serial_number')}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{$t("general.room")}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{$t("general.guest")}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{$t("general.Notes")}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{$t("general.Receipt")}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{$t("general.amount")}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{$t("general.insertBy")}}</span>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="data.voucher_header.length > 0">
                                    <tr
                                        v-for="(item, index) in data.voucher_header"
                                        :key="item.id"
                                        class="body-tr-custom"
                                    >
                                        <td v-if="index == 0" >{{ $i18n.locale == "ar" ? data.name : data.name_e }}</td>
                                        <td v-else ></td>
                                        <td>{{item.date}}</td>
                                        <td>{{item.prefix}}</td>
                                        <td>{{$i18n.locale == "ar" ? item.room.name : item.room.name_e}}</td>
                                        <td>{{$i18n.locale == "ar" ? item.customer.name : item.customer.name_e}}</td>
                                        <td>{{item.notes}}</td>
                                        <td>{{item.manual_document_number}}</td>
                                        <td>{{item.amount}} KD</td>
                                        <td>{{$i18n.locale == "ar" ? item.salesmen.name : item.salesmen.name_e}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6"></td>
                                        <td>{{$t('general.total')}} - {{$i18n.locale == "ar" ? data.name : data.name_e}}</td>
                                        <td>{{parseFloat(sumTotal(data.voucher_header)).toFixed(3)}} KD</td>
                                        <td ></td>
                                    </tr>
                                    <tr v-if="items.length == (parseInt(indexP) + 1)">
                                        <td colspan="6"></td>
                                        <td>{{$t('general.RoomsReceiptsTotal')}}</td>
                                        <td>{{parseFloat(roomsReceiptsTotal()).toFixed(3)}} KD</td>
                                        <td></td>
                                    </tr>
                                    </tbody>
                                    <tbody v-else>
                                    <tr>
                                        <th class="text-center" colspan="11">
                                            {{ $t("general.notDataFound") }}
                                        </th>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- end .table-responsive-->

                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
.signature {
    display: none;
}
.head-branch {
    display: none;
}
@media print {
    .head-branch {
        margin-top: 50px;
        display: block;
    }
    .head-branch h2{
        text-decoration: underline;
    }
    .head-branch img{
        width: 100px;
        height: 100px;
    }
    .head-branch span {
        display: inline-block;
        position: relative;
        top: -49px;
    }
    .do-not-print {
        display: none;
    }

    .arrow-sort {
        display: none;
    }
    table thead tr th {
        color: #000;
        border: 1px solid #000;
    }
    table tbody tr td {
        color: #000;
        border: 1px solid #000;
    }
    hr {
        transform: rotate(90deg);
        background-color: #0000008a;
        border: 1px solid #4444449c;
    }
    .custom-table-theme thead {
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
    }
    .custom-table-theme tbody {
        border: 1px solid #000;
    }
    .signature {
        display: block;
    }
    .signature h4 {
        text-decoration: underline;
        margin: 3px;
    }
}
</style>
