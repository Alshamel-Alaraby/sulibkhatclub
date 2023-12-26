<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import loader from "../../../components/general/loader";
import translation from "../../../helper/mixin/translation-mixin";
import permissionGuard from "../../../helper/permission";
import customTable from "../../../helper/mixin/customTable";
import successError from "../../../helper/mixin/success&error";
import crudHelper from "../../../helper/mixin/crudHelper";
import Panel from "../../../components/create/boardRent/panel";
import searchPage from "../../../components/general/searchPage";
import actionSetting from "../../../components/general/actionSetting";
import tableCustom from "../../../components/general/tableCustom";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import Multiselect from "vue-multiselect";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Panels",
        meta: [{ name: "description", content: "Panels" }],
    },
    mixins: [translation,customTable,successError,crudHelper],
    components: {
        Layout, PageHeader, loader, Panel,
        searchPage,actionSetting, tableCustom,Multiselect
    },
    data() {
        return {
            url: '/boards-rent/panels',
            searchMain: '',
            tableSetting: [
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_name_ar",isV: 'name',
                    type: 'string',sort: true,setting: {"name":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_name_en",isV: 'name_e',
                    type: 'string',sort: true,setting: {"name_e":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"boardRent_panel_description",isV: 'description',
                    type: 'string',sort: true,setting: {"description":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"boardRent_panel_description_e",isV: 'description_e',
                    type: 'string',sort: true,setting: {"description_e":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_code",isV: 'code',
                    type: 'string',sort: true,setting: {"code":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_new_code",isV: 'new_code',
                    type: 'string',sort: true,setting: {"new_code":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_size",isV: 'size',
                    type: 'string',sort: true,setting: {"size":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"boardRent_panel_note",isV: 'note',
                    type: 'string',sort: true,setting: {"note":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_face",isV: 'face',
                    type: 'string',sort: true,setting: {"face":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_unit_status", isV: 'unit_status_id',
                    type: 'relation',name: 'unitStatus',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"unit_status_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_category", isV: 'category_id',
                    type: 'relation',name: 'category',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"category_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_governorate", isV: 'governorate_id',
                    type: 'relation',name: 'governorate',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"governorate_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_city", isV: 'city_id',
                    type: 'relation',name: 'city',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"city_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_avenue", isV: 'avenue_id',
                    type: 'relation',name: 'avenue',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"avenue_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_street", isV: 'street_id',
                    type: 'relation',name: 'street',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"street_id":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"branch_status",isV: 'is_active',
                    type: 'boolean',setting: {"is_active":true},isSetting: true
                }
            ],
            sendSetting: {},
            searchField: [],
            categories: [],
            governorates: [],
            cities: [],
            avenues: [],
            streets: [],
            faces: ['A','B','Multi','One-Face'],
            filterButton: '',
            location: {city_id: null,governorate_id: null,avenue_id: null,category_id: null,face: null,street_id: null,code: ''}
        };
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
        this.settingFun();
        this.getCustomTableFields('boards_rent_panels');
        this.getData(1,this.url,this.filterSearch(this.searchField));
        this.getCategory();
        this.getGovernorate();
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
          return permissionGuard(vm, "Board rent Panel", "all panel boardRent");
        });
    },
    methods: {
        filterSearch(fields){
            let _filterSetting = [...fields];
            let unit_status = _filterSetting.indexOf("unit_status_id");
            let category = _filterSetting.indexOf("category_id");
            let city = _filterSetting.indexOf("city_id");
            let governorate = _filterSetting.indexOf("governorate_id");
            let avenue = _filterSetting.indexOf("avenue_id");
            let street = _filterSetting.indexOf("street_id");
            if (unit_status > -1) {_filterSetting[unit_status] = this.$i18n.locale == "ar" ? "unitStatus.name" : "unitStatus.name_e";}
            if (category > -1) {_filterSetting[category] = this.$i18n.locale == "ar" ? "category.name" : "category.name_e";}
            if (city > -1) {_filterSetting[city] = this.$i18n.locale == "ar" ? "city.name" : "city.name_e";}
            if (avenue > -1) {_filterSetting[avenue] = this.$i18n.locale == "ar" ? "city.name" : "city.name_e";}
            if (street > -1) {_filterSetting[street] = this.$i18n.locale == "ar" ? "avenue.name" : "avenue.name_e";}
            if (governorate > -1) {_filterSetting[governorate] = this.$i18n.locale == "ar" ? "governorate.name" : "governorate.name_e";}
            let filter = '';

            for (let i = 0; i < _filterSetting.length; ++i) {
                filter += `columns[${i}]=${fields[i]}&`;
            }
            return filter;
        },
        settingFun(setting = null){
            if(setting) this.tableSetting = setting;
            let l = {},names = [];
            names = this.tableSetting.filter(e => e.isSet ).map(el => el.setting);
            this.tableSetting.forEach((e,i) => {
                l[e.isV] = names[i][e.isV];
                e['isSetting'] = l[e.isV];
            });
            this.sendSetting = l;
        },
        getCategory() {
            this.isLoader = true;

            adminApi
                .get(
                    `/categories`
                )
                .then((res) => {
                    let l = res.data.data;
                    this.categories = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');

                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        showGovernorateModal() {
            if (this.location.governorate_id > 0) {
                this.getCity();
            }
        },
        showCityModal() {
            if (this.location.city_id > 0) {
                this.getAvnue();
            }
        },
        showAvenueModal() {
            if (this.location.avenue_id > 0) {
                this.getStreet();
            }
        },
        getGovernorate() {

            this.governorates = [];this.cities = [];this.avenues = [];this.streets = [];

            adminApi
                .get(`/governorates?columns[0]=country.id&search=1`)
                .then((res) => {
                    let l = res.data.data;
                    this.governorates = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');

                });
        },
        getCity() {
            this.isLoader = true;
            let governorate = this.location.governorate_id;
            this.location.city_id = null;
            this.location.avenue_id = null;
            this.location.street_id = null;
            this.cities = [];this.avenues = [];this.streets = [];

            adminApi
                .get(`/cities?columns[0]=governorate.id&search=${governorate}`)
                .then((res) => {
                    let l = res.data.data;
                    this.cities = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');

                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getAvnue() {
            this.isLoader = true;
            let city = this.location.city_id;
            this.location.avenue_id = null;
            this.location.street_id = null;
            this.avenues = [];this.streets = [];

            adminApi
                .get(`/avenues?columns[0]=city.id&search=${city}`)
                .then((res) => {
                    let l = res.data.data;
                    this.avenues = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');

                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getStreet() {
            this.isLoader = true;
            let avenue = this.location.avenue_id;
            this.location.street_id = null;
            this.streets = [];

            adminApi
                .get(`/streets?columns[0]=avenue.id&search=${avenue}`)
                .then((res) => {
                    let l = res.data.data;
                    this.streets = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');

                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getPanel(page) {
            this.isLoader = true;
            this.current_page = 1;
            let filter = '';
            filter += this.location.category_id ? `&category_id=${this.location.category_id}` : '';
            filter += this.location.governorate_id ? `&governorate_id=${this.location.governorate_id}` : '';
            filter += this.location.city_id ? `&city_id=${this.location.city_id}` : '';
            filter += this.location.avenue_id ? `&avenue_id=${this.location.avenue_id}` : '';
            filter += this.location.face ? `&face=${this.location.face}` : '';
            filter += this.location.street_id ? `&street_id=${this.location.street_id}` : '';
            filter += this.location.code ? `&code=${this.location.code}` : '';
            this.filterButton = filter;
            this.getData(1,this.url,'',filter);

        },
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
                        <searchPage
                            page="general.panelsTable"
                            :isVisible="isVisible"
                            :filterSetting="tableSetting"
                            :companyKeys="companyKeys"
                            :defaultsKeys="defaultsKeys"
                            @dataSearch="() => getData(1,url,filterSearch(searchField))"
                            @searchFun="(fields) => searchField = fields"
                            @editSearch="(search) => searchMain = search"
                            :isSearch="true"
                        />
                        <!-- end search -->

                        <!-- start setting -->
                        <actionSetting
                            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :current_page="current_page"
                            :isCreate="true" :isEdit="true" :isDelete="true"
                            :permissionCreate="isPermission('create panel boardRent')"
                            :permissionUpdate="isPermission('update panel boardRent')"
                            :permissionDelete="isPermission('delete panel boardRent')" :isExcl="true"
                            :isPrint="true" :checkAll="checkAll" :sideAction="true" :sidePaginate="true"
                            :isFilter="true" :group="true" :isGroup="true" :isVisible="isVisible"
                            :isSetting="true" :isPaginate="true" :settings="tableSetting"
                            @delete="ids => deleteRow(ids,url)"
                            @gen_xsl="ExportExcel('xlsx')"
                            @settingFun="setting => settingFun(setting)"
                            :objPagination="objPagination"
                            @perviousOrNext="(current) => getData(current,url,filterSearch(searchField))"
                            @currentPage="(page) => current_page = page"
                            @DataCurrentPage="(page) => getDataCurrentPage(page,filterButton)"
                            @actionChange="({typeAction,id}) => changeType({typeAction,id})"
                        />
                        <!-- end setting -->

                        <!--  search   -->
                        <b-modal
                            id="filter"
                            :title="$t('general.Search')"
                            title-class="font-18"
                            body-class="p-4"
                            size="lg"
                            :hide-footer="true"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <b-button
                                        variant="success"
                                        type="button" class="mx-1"
                                        v-if="!isLoader"
                                        @click.prevent="getPanel"
                                    >
                                        {{ $t('general.Search') }}
                                    </b-button>

                                    <b-button variant="success" class="mx-1" disabled v-else>
                                        <b-spinner small></b-spinner>
                                        <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                    </b-button>
                                    <!-- Emulate built in modal footer ok and cancel button actions -->

                                    <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(`search`)">
                                        {{ $t('general.Cancel') }}
                                    </b-button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_code") }}
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="location.code"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_category") }}
                                            </label>
                                            <multiselect
                                                v-model="location.category_id"
                                                :options="categories.map((type) => type.id)"
                                                :custom-label="(opt) => categories.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar' ? categories.find((x) => x.id == opt).name : categories.find((x) => x.id == opt).name_e
                                    : null
                                "
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_governorate") }}
                                            </label>
                                            <multiselect
                                                @input="showGovernorateModal"
                                                v-model="location.governorate_id"
                                                :options="governorates.map((type) => type.id)"
                                                :custom-label="(opt) => governorates.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar' ? governorates.find((x) => x.id == opt).name : governorates.find((x) => x.id == opt).name_e
                                    : null
                                "
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_city") }}
                                            </label>
                                            <multiselect
                                                @input="showCityModal"
                                                v-model="location.city_id"
                                                :options="cities.map((type) => type.id)"
                                                :custom-label="(opt) => cities.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar' ? cities.find((x) => x.id == opt).name : cities.find((x) => x.id == opt).name_e
                                    : null
                                "
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_avenue") }}
                                            </label>
                                            <multiselect
                                                @input="showAvenueModal"
                                                v-model="location.avenue_id"
                                                :options="avenues.map((type) => type.id)"
                                                :custom-label="(opt) => avenues.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar' ? avenues.find((x) => x.id == opt).name : avenues.find((x) => x.id == opt).name_e
                                    : null
                                "
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_street") }}
                                            </label>
                                            <multiselect
                                                v-model="location.street_id"
                                                :options="streets.map((type) => type.id)"
                                                :custom-label="(opt) => streets.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar' ? streets.find((x) => x.id == opt).name : streets.find((x) => x.id == opt).name_e
                                    : null
                                "
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_panel_face") }}
                                            </label>
                                            <multiselect
                                                v-model="location.face"
                                                :options="faces"
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /search   -->

                        <!--  create or edit  -->
                        <Panel
                            :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                            :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired" :url="url"
                            :type="type" :idObjEdit="idEdit? {idEdit,dataObj: this.tables.find(el => el.id == idEdit)}:null"
                            @getDataTable="getData(1,url,filterSearch(searchField))" :isPermission="isPermission"
                        />
                        <!--  /create   -->

                        <!-- start .table-responsive-->
                        <div
                            class="table-responsive mb-3 custom-table-theme position-relative"
                            ref="exportable_table" id="printCustom"
                        >
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->

                            <tableCustom
                                :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                                :tables="tables" :isEdit="true" :isDelete="true"
                                :permissionUpdate="isPermission('update panel boardRent')"
                                :permissionDelete="isPermission('delete panel boardRent')"
                                :isVisible="isVisible" :tableSetting="tableSetting"
                                :enabled3="enabled3" :Tooltip="Tooltip" @logFire="(id) => log(id,url)"
                                @delete="ids => deleteRow(ids,url)" @editRow="id => dbClickRow(id)"
                                @checkRows="(cR) => checkAll = cR" @checkRowTable="id => checkRow(id)"
                                :isInputCheck="true" :isLog="true" :isAction="true"
                            />

                        </div>
                        <!-- end .table-responsive-->
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style>
.dropdown-menu-custom-company.dropdown .dropdown-menu {
    padding: 5px 10px !important;
    overflow-y: scroll;
    height: 300px;
}
</style>
