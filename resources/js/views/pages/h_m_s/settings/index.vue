<script>
import Layout from "../../../layouts/main.vue";
import PageHeader from "../../../../components/general/Page-header.vue";
import loader from "../../../../components/general/loader.vue";
import translation from "../../../../helper/mixin/translation-mixin";
import permissionGuard from "../../../../helper/permission";
import customTable from "../../../../helper/mixin/customTable";
import crudHelper from "../../../../helper/mixin/crudHelper";
import searchPage from "../../../../components/general/searchPage";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";
import Modal from "./modal.vue";
import page_title from "../../../../helper/PageTitle"

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "HMSSettings",
        meta: [{ name: "description", content: "HMS Settings" }],
    },
    watch:{
        pageTitle: {
        handler(newV, old) {
            this.page_title = page_title.value;
        },
        },
    },
    computed: {
        pageTitle: function () {
        return page_title.value;
        },
    },
    mixins: [translation,customTable,crudHelper],
    components: {
        Layout, PageHeader, loader, searchPage,
        actionSetting, tableCustom, Modal
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
          return permissionGuard(vm, "HMSSettings", "show HMS Settings");
        });
   },
    data() {
        return {
            url: '/h_m_s/settings',
            page_title: {},
            searchMain: '',
            tableSetting: [
                {
                    isFilter: false,isSet: true,trans:"hms_settings_taxes",isV: 'taxes',
                    type: 'string',sort: false,setting: {"taxes":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"hms_settings_is_there_insurance",isV: 'is_there_insurance',
                    type: 'boolean1',sort: false,setting: {"is_there_insurance":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"hms_settings_interval",isV: 'interval',
                    type: 'string',sort: false,setting: {"interval":true},isSetting: true
                }
            ],
            sendSetting: {},
            searchField: [],
        };
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
        this.settingFun();
        this.page_title = page_title.value
        this.getData(1,this.url,this.filterSearch(this.searchField));
        this.getCustomTableFields('h_m_s_settings');

    },
    methods: {
        filterSearch(fields){
            let filter = '';
            for (let i = 0; i < fields.length; ++i) {
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
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ page_title && page_title.url == $route.fullPath ? ($i18n.locale ==
                                'ar' ? page_title.title : page_title.title_e) : $t('general.settings') }}</h4>

                        </div>

                        <!--  create   -->
                        <Modal
                            :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :url="url"
                            :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired"
                            :type="type" :idObjEdit="idEdit? {idEdit,dataObj: this.tables.find(el => el.id == idEdit)}:null"
                            @getDataTable="getData(1,url,filterSearch(searchField))" :isPermission="isPermission"
                        />

                        <div class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table"
                             id="printCustom">
                            <loader size="large" v-if="isLoader" />

                            <tableCustom
                                :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                                :tables="tables" :isEdit="true" :isDelete="false"
                                :permissionUpdate="isPermission('update HMS Settings')"
                                :permissionDelete="false"
                                :isVisible="isVisible" :tableSetting="tableSetting"
                                :enabled3="enabled3" :Tooltip="Tooltip" @logFire="(id) => log(id,url)"
                                @delete="ids => deleteRow(ids,url)" @editRow="id => dbClickRow(id)"
                                @checkRows="(cR) => checkAll = cR" @checkRowTable="id => checkRow(id)"
                                :isInputCheck="false" :isLog="false" :isAction="true"
                            />

                        </div>
                        <!-- end .table-responsive-->
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
