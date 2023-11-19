<script>
import Layout from "../../views/layouts/main";
import Swal from "sweetalert2";
import WidgetChart from "../widgets/Widget-chart";
import PageHeader from "../general/Page-header";
import RevenueReport from "../widgets/Revenue-report";
import ProductsSales from "../widgets/Products-sales";
import MarketingReports from "../widgets/Marketing-reports";
import Portlet from "../widgets/Portlet";
import RevenueHistory from "../widgets/Revenue-history";
import Projections from "../widgets/Projections";
import adminApi from "../../api/adminAxios";
import {formatDateOnly} from "../../helper/startDate";
import {required} from "vuelidate/lib/validators";
import DatePicker from "vue2-datepicker";


/**
 * Sales-Dashboard component
 */
export default {
    name: "Statistics Booking",
  components: {
    Layout,
    WidgetChart,
    PageHeader,
    RevenueReport,
    ProductsSales,
    MarketingReports,
    Portlet,
    RevenueHistory,
    Projections,
      DatePicker,
  },
  data() {
    return {
      title: "Welcome !",
      items: [
        {
          text: "Minton",
        },
        {
          text: "Dashboards",
        },
        {
          text: "Sales",
          active: true,
        },
      ],
      tableTitle: "Top Selling Products",
      productData: [
        {
          name: "ASOS Ridley High Waist",
          price: "$79.49",
          quantity: 82,
          amount: "$6,518.18",
          date: "Sep 1, 2018",
          sales: 54,
          productid: 200125,
        },
        {
          name: "Marco Lightweight Shirt",
          price: "$128.50",
          quantity: 37,
          amount: "$4,754.50",
          date: "Sep 15, 2018",
          sales: 28,
          productid: 200130,
        },
        {
          name: "Half Sleeve Shirt",
          price: "$39.99",
          quantity: 64,
          amount: "$2,559.36",
          date: "Nov 1, 2018",
          sales: 55,
          productid: 200140,
        },
        {
          name: "Lightweight Jacket",
          price: "$20.00",
          quantity: 184,
          amount: "$3,680.00",
          date: "Nov 15, 2018",
          sales: 85,
          productid: 200250,
        },
        {
          name: "Marco Shoes",
          price: "$28.49",
          quantity: 69,
          amount: "$1,965.81",
          date: "Jan 1, 2019",
          sales: 49,
          productid: 200256,
        },
        {
          name: "ASOS Ridley High Waist",
          price: "$79.49",
          quantity: 82,
          amount: "$6,518.18",
          date: "Sep 1, 2018",
          sales: 54,
          productid: 200125,
        },
        {
          name: "Half Sleeve Shirt",
          price: "$39.99",
          quantity: 64,
          amount: "$2,559.36",
          date: "Nov 1, 2018",
          sales: 55,
          productid: 200140,
        },
        {
          name: "Lightweight Jacket",
          price: "$20.00",
          quantity: 184,
          amount: "$3,680.00",
          date: "Nov 15, 2018",
          sales: 85,
          productid: 200250,
        },
      ],
      isLoader : false,
      statices: {},
        documents:[],
        create: {
            date: this.formatDate(new Date()),
            header_id:null,
            detail_ids:[]
        },
        is_disabled: false,
        errors: {},
    };
  },
    validations: {
        create: {
            date: {required},
        },
    },
  mounted() {
      this.getStatices();
      this.getCheckInCustomer();
  },
  methods: {
      getStatices() {
          this.isLoader = true;

          adminApi.get(`/booking/statistics`)
              .then((res) => {
                  let l = res.data.data;
                  this.statices = l;
              })
              .catch((err) => {
                  Swal.fire({
                      icon: 'error',
                      title: `${this.$t('general.Error')}`,
                      text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                  });
              })
              .finally(() => {
                  this.isLoader = false;
              });
      },
      getCheckInCustomer(){
          this.isLoader = true;
          adminApi
              .get(`/document-headers/check-in-customer`)
              .then((res) => {
                  let l = res.data;
                  this.documents = l;
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
      formatDate(value) {
          return formatDateOnly(value);
      },
      resetModalHidden(id) {
          this.create = {
              date: this.formatDate(new Date()),
              header_id:null,
              detail_ids:[]
          };
          this.$nextTick(() => {
              this.$v.$reset();
          });
          this.is_disabled = false;
          this.errors = {};
          this.$bvModal.hide(id);
      },
      resetModal(id) {
          let data = this.documents.find((e) => id == e.id);
          this.create = {
              date: this.formatDate(new Date()),
              header_id:data.id,
              detail_ids:[data.document_header_details[0].id]
          };
          this.$nextTick(() => {
              this.$v.$reset();
          });
          this.is_disabled = false;
          this.errors = {};
      },
      AddSubmit() {
          this.$v.create.$touch();
          if (this.$v.create.$invalid) {
              return;
          } else {
              this.isLoader = true;
              this.errors = {};
              this.is_disabled = false;
              adminApi.post(`document-headers/update-check-in-customer`, {...this.create})
                  .then((res) => {
                      this.getCheckInCustomer();
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
  }
};
</script>

<template>
  <Layout>
    <PageHeader :title="title" :items="items" />
      <div class="dashboard-fluid">
          <div class="row">
              <div class="col-xl-6">
                  <div class="row mt-3">
                      <div class="col-xl-6 col-md-12">
                          <WidgetChart
                              :number="statices.statusCount"
                              :text="'departments'"
                              :chart-color="'#1abc9c'"
                          />
                      </div>
                      <div class="col-xl-6 col-md-12">
                          <WidgetChart
                              :number="statices.UnitsCount"
                              :text="'rooms'"
                              :chart-color="'#f1556c'"
                          />
                      </div>
                      <!-- end col -->
                      <div class="col-xl-12 col-md-12">
                          <Projections :statices="statices"  />
                      </div>
                  </div>
              </div>
              <div class="col-xl-6">
                  <div class="card">
                      <div class="card-body">
                          <h4 class="header-title mb-3">{{$t('general.extendReservation')}}</h4>
                          <div class="table-responsive mb-0">
                              <table class="table table-borderless table-hover table-centered m-0">
                                  <thead class="thead-light">
                                  <tr>
                                      <th>{{$t('general.serial_number')}}</th>
                                      <th>{{$t('general.room')}}</th>
                                      <th>{{$t('general.Arival')}}</th>
                                      <th>{{$t('general.DepartureDate')}}</th>
                                      <th>{{$t('general.guest')}}</th>
                                      <th>{{$t('general.Action')}}</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <tr v-for="data in documents" :key="data.id">
                                      <td>
                                          <h5 class="m-0 font-weight-normal">{{ data.prefix }}</h5>
                                      </td>
                                      <td>{{ $i18n.locale == "ar" ? data.document_header_details[0].unit.name : data.document_header_details[0].unit.name_e }}</td>
                                      <td>{{ data.document_header_details[0].date_from }}</td>
                                      <td>{{ data.document_header_details[0].date_to }}</td>
                                      <td>{{$i18n.locale == "ar" ? data.customer.name : data.customer.name_e}} </td>
                                      <td>
                                          <a @click="$bvModal.show(`${data.id+''}`)" href="javascript: void(0);" class="btn btn-xs btn-primary">
                                              <i class="mdi mdi-square-edit-outline"></i>
                                          </a>
                                      </td>
                                      <!--  create   -->
                                      <b-modal
                                          :id="data.id+''"
                                          :title="$t('general.ExtendingTheReservationPeriod')"
                                          title-class="font-18"
                                          body-class="p-4 "
                                          :hide-footer="true"
                                          @show="resetModal(data.id)"
                                          @hidden="resetModalHidden((data.id))"
                                      >
                                          <form>
                                              <div class="mb-3 d-flex justify-content-end">
                                                  <template v-if="!is_disabled">
                                                      <b-button
                                                          variant="success"
                                                          type="button"
                                                          class="mx-1"
                                                          v-if="!isLoader"
                                                          @click.prevent="AddSubmit"
                                                      >
                                                          {{$t("general.edit")}}
                                                      </b-button>

                                                      <b-button variant="success" class="mx-1" disabled v-else>
                                                          <b-spinner small></b-spinner>
                                                          <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                      </b-button>
                                                  </template>
                                                  <!-- Emulate built in modal footer ok and cancel button actions -->

                                                  <b-button
                                                      variant="danger"
                                                      type="button"
                                                      @click.prevent="resetModalHidden(data.id+'')"
                                                  >
                                                      {{ $t("general.Cancel") }}
                                                  </b-button>
                                              </div>
                                              <div class="row">
                                                  <div class="col-md-12">
                                                      <div class="form-group">
                                                          <label class="control-label">
                                                              {{ $t('general.Date') }}
                                                              <span class="text-danger">*</span>
                                                          </label>
                                                          <date-picker
                                                              type="date"
                                                              v-model="create.date"
                                                              format="YYYY-MM-DD"
                                                              valueType="format"
                                                              :confirm="false"
                                                              :class="{'is-invalid': $v.create.date.$error || errors.date,}"
                                                          ></date-picker>
                                                          <div v-if="!$v.create.date.required" class="invalid-feedback">
                                                              {{ $t("general.fieldIsRequired") }}
                                                          </div>
                                                          <template v-if="errors.date">
                                                              <ErrorMessage v-for="(errorMessage, index) in errors.date" :key="index">
                                                                  {{ errorMessage }}
                                                              </ErrorMessage>
                                                          </template>
                                                      </div>
                                                  </div>
                                              </div>
                                          </form>
                                      </b-modal>
                                      <!--  /create   -->
                                  </tr>
                                  </tbody>
                              </table>
                          </div>
                          <!-- end .table-responsive-->
                      </div>
                  </div>
              </div>
          </div>


      </div>
  </Layout>
</template>

<style scoped>
.content-page {
    padding: 70px 15px 5px !important;
}
.dashboard-fluid {
    padding: 0 10px !important;
}
</style>
